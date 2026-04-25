<?php

namespace App\Http\Controllers\AdminAPI;

use App\Events\CaptureLiveScreenshot;
use App\Http\Controllers\Controller;
use App\Models\AttendanceEmployee;
use App\Models\BreakTime;
use App\Models\Employee;
use App\Models\Incident;
use App\Models\Team;
use App\Models\User;
use App\Models\Utility;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class ScreenshotController extends Controller
{
    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function employees(Request $request)
    {
        $request->validate([
            'search' => 'nullable|string',
        ]);

        $authUser = Auth::user();

        $query = Employee::with(['user', 'team', 'role', 'designation', 'shift'])
            ->where('created_by', $authUser->id);

        if ($request->filled('search')) {
            $searchTerm = $request->search;
            $query->whereHas('user', function ($q) use ($searchTerm) {
                $q->where('name', 'like', "%$searchTerm%")
                    ->orWhere('email', 'like', "%$searchTerm%")
                    ->orWhere('mobile_no', 'like', "%$searchTerm%");
            });
        }

        $paginatedEmployees = $query->paginate(10)->withQueryString();

        $paginatedEmployees->getCollection()->transform(function ($employee) {
            return [
                'id' => $employee->id,
                'user_id' => $employee->user->id ?? null,
                'name' => $employee->user->name ?? '',
                'email' => $employee->user->email ?? '',
                'mobile_no' => $employee->user->mobile_no ?? '',
                'employee_id' => $employee->employee_id,
                'team' => $employee->team,
                'role' => $employee->role,
                'designation' => $employee->designation,
                'shift' => $employee->shift,
                'is_active' => $employee->is_active ? 'Active' : 'Inactive',
            ];
        });

        return response()->json([
            'is_success' => true,
            'message' => 'Employee details retrieved successfully',
            'data' => $paginatedEmployees->items(),
            'pagination' => [
                'total' => $paginatedEmployees->total(),
                'per_page' => $paginatedEmployees->perPage(),
                'current_page' => $paginatedEmployees->currentPage(),
                'last_page' => $paginatedEmployees->lastPage(),
                'next_page_url' => $paginatedEmployees->nextPageUrl(),
                'prev_page_url' => $paginatedEmployees->previousPageUrl(),
            ],
        ], 200);
    }

    /**
     * @param Request $request
     * @param $id
     * @return JsonResponse
     */
    public function viewScreenshot(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|string',
            'date' => 'required|date_format:Y-m-d',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'is_success' => false,
                'message' => $validator->errors()->first()
            ], 422);
        }

        $dateInput = $request->input('date');
        $UserId = $request->input('user_id');

        $user = User::findOrFail($UserId);

        $employee = Employee::where('user_id', $user->id)
            ->where('created_by', Auth::user()->creatorId())
            ->first();

        if (!$employee) {
            return response()->json([
                'is_success' => false,
                'error' => 'Employee not found for the given user.',
            ], 404);
        }

        try {
            $startOfDay = Carbon::createFromFormat('Y-m-d', $dateInput, 'Asia/Kolkata')->startOfDay();
            $endOfDay = Carbon::createFromFormat('Y-m-d', $dateInput, 'Asia/Kolkata')->endOfDay();
        } catch (\Exception $e) {
            return response()->json([
                'is_success' => false,
                'error' => 'Invalid date format.'
            ], 400);
        }

        $incidents = Incident::with('applicationLog')
            ->where('user_id', $user->id)
            ->whereBetween('capture_date_and_time', [$startOfDay, $endOfDay])
            ->orderBy('capture_date_and_time', 'desc')
            ->get();

        foreach ($incidents as $incident) {
            $incident->action_percentage = $this->calculateActionPercentage($incident);
        }

        $data = $incidents->map(function ($incident) {
            return [
                'id' => $incident->id,
                'user_id' => $incident->user_id,
                'name' => $incident->user->name,
                'email' => $incident->user->email,
                'capture_date_and_time' => $incident->capture_date_and_time,
                'time' => Carbon::parse($incident->capture_date_and_time)->format('h:i A'),
                'screenshot_url' => $incident->screenshot ? Utility::get_file($incident->screenshot) : null,
                'application_log' => $incident->applicationLog,
            ];
        });

        return response()->json([
            'is_success' => true,
            'data' => $data,
        ]);
    }

    /**
     * @param $incident
     * @return float
     */
    public static function calculateActionPercentage($incident)
    {
        $team = $incident->employee->team;

        $keyboardAvg = $team->avg_keyboard_clicks_per_day ?: 1;
        $mouseAvg = $team->avg_mouse_clicks_per_day ?: 1;

        $keyboardClicks = $incident->keyboard_action_count ?? 0;
        $mouseClicks = $incident->mouse_action_count ?? 0;

        $keyboardPercent = ($keyboardClicks / $keyboardAvg) * 100;
        $mousePercent = ($mouseClicks / $mouseAvg) * 100;

        $average = ($keyboardPercent + $mousePercent) / 2;

        return round(min($average, 100), 2);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function liveScreenshotAvailableEmployees(Request $request)
    {
        $today = Carbon::today()->toDateString();

        $query = AttendanceEmployee::where('date', $today)
            ->whereNotNull('clock_in')
            ->where('created_by', Auth::user()->creatorId())
            ->with('employee');

        if ($request->filled('team_id')) {
            $query->whereHas('employee', function ($q) use ($request) {
                $q->where('team_id', $request->team_id);
            });
        }

        if ($request->filled('employee_id')) {
            $query->where('employee_id', $request->employee_id);
        }

        $attendanceEmployees = $query->get();

        $filteredAttendance = $attendanceEmployees->filter(function ($attendance) {
            return !BreakTime::where('attendance_id', $attendance->id)
                ->whereNull('break_ended_at')
                ->exists();
        });

        $employees = $filteredAttendance->pluck('employee')->filter()->unique('id')->values();

        $perPage = $request->input('per_page', 10);
        $currentPage = $request->input('page', 1);
        $pagedEmployees = $employees->forPage($currentPage, $perPage)->values();

        $paginated = new \Illuminate\Pagination\LengthAwarePaginator(
            $pagedEmployees,
            $employees->count(),
            $perPage,
            $currentPage,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        $teams = Team::where('created_by', Auth::user()->creatorId())->get(['id', 'name']);

        return response()->json([
            'is_success' => true,
            'message' => 'Live screen eligible employees retrieved successfully.',
            'teams' => $teams,
            'employees' => $paginated,
        ]);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function requestScreenshot(Request $request)
    {
        $request->validate([
            'employee_id' => 'required|integer|exists:employees,id',
        ]);

        $isWebCam = $request->get('is_web_cam', false);
        $employee = Employee::find($request->employee_id);

        if (!$employee) {
            return response()->json([
                'is_success' => false,
                'message' => 'Employee not found.',
            ], 404);
        }

        $incident = Incident::create([
            'user_id' => $employee->user_id,
            'requested_by' => Auth::id(),
            'requested_date_and_time' => now(),
            'is_web_cam' => $isWebCam
        ]);

        $channelId = env('PUSHER_CHANNEL_ID');

        event(new CaptureLiveScreenshot($employee->user_id, $incident, $channelId));

        return response()->json([
            'is_success' => true,
            'message' => 'Screenshot request sent. Poll for screenshot with incident_id.',
            'data' => [
                'incident_id' => $incident->id,
                'channel_id' => $channelId,
            ],
        ], 200);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function checkScreenshotStatus(Request $request)
    {
        $request->validate([
            'incident_id' => 'required|integer|exists:user_activity,id',
        ]);

        $incident = Incident::find($request->incident_id);

        if (!$incident) {
            return response()->json([
                'is_success' => false,
                'message' => 'Incident not found.',
            ], 404);
        }

        if (!empty($incident->screenshot)) {
            return response()->json([
                'is_success' => true,
                'message' => 'Screenshot captured.',
                'data' => [
                    'image_url' => Utility::get_file($incident->screenshot),
                    'employee' => [
                        'id' => $incident->employee->id ?? null,
                        'name' => $incident->employee->name ?? null,
                        'employee_id' => $incident->employee->employee_id ?? null,
                        'designation' => $incident->employee->designation->name ?? null,
                    ],
                ],
            ], 200);
        }

        return response()->json([
            'is_success' => false,
            'message' => 'Screenshot not yet available. Still pending.',
        ], 202);
    }
}
