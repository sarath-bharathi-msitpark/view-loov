<?php

namespace App\Http\Controllers\Web\Company;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\Incident;
use App\Models\Team;
use Carbon\Carbon;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ScreenshotController extends Controller
{
    /**
     * @param Request $request
     * @return Factory|View|Application|RedirectResponse
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $hasAdminRole = $user->getRoleNames()->contains(ROLE_ADMINISTRATOR);

        if (!$user->can('screenshot')) {
            return redirect()->back()->with('error', 'Permission denied.');
        }

        if ($hasAdminRole || $user->can('share_all_reports')) {
            $teams = Team::where('created_by', $user->creatorId())
                ->where('is_tracking', true)
                ->where('is_capturescreenshot', true)
                ->get();
        } else {
            $teams = Team::where('id', $user->employee->team_id)
                ->where('is_tracking', true)
                ->where('is_capturescreenshot', true)
                ->get();
        }

        if ($request->has('clear')) {
            return redirect()->route('organization.screenshot.index');
        }

        $validTeamIds = $teams->pluck('id');

        $employees = Employee::where('created_by', $user->creatorId())
            ->where('is_active', true)
            ->whereIn('team_id', $validTeamIds)
            ->whereHas('user', function ($q) {
                $q->where('track_type', USER_APK_TYPE_SYSTEM_TRACK)
                    ->whereHas('roles', function ($roleQuery) {
                        $roleQuery->whereIn('name', ['standard user', 'stealth user']);
                    });
            })
            ->when(!($hasAdminRole || $user->can('share_all_reports')), function ($q) use ($user) {
                $q->where('team_id', $user->employee->team_id);
            })
            ->when($request->filled('team_id'), function ($q) use ($request) {
                $q->where('team_id', $request->input('team_id'));
            })
            ->orderBy('name', 'asc')
            ->get();

        $employeesCount = $employees->count();
        $selectedEmployeeId = $request->input('employee_id') ?? ($employees->first()?->id);
        $userIdByEmployeeId = Employee::find($selectedEmployeeId)?->user_id;

        $dateRange = $request->input('date_range');
        if ($dateRange) {
            try {
                if (str_contains($dateRange, ' to ')) {
                    [$startDate, $endDate] = explode(' to ', $dateRange);
                } else {
                    $startDate = $endDate = $dateRange;
                }

                $startOfDay = Carbon::createFromFormat('Y-m-d', trim($startDate), 'Asia/Kolkata')->startOfDay();
                $endOfDay = Carbon::createFromFormat('Y-m-d', trim($endDate), 'Asia/Kolkata')->endOfDay();
            } catch (\Exception $e) {
                Log::error('Invalid date_range input: ' . $e->getMessage());
                $startOfDay = now('Asia/Kolkata')->startOfDay();
                $endOfDay = now('Asia/Kolkata')->endOfDay();
            }
        } else {
            $startOfDay = now('Asia/Kolkata')->startOfDay();
            $endOfDay = now('Asia/Kolkata')->endOfDay();
        }

        $employeeUserIds = $employees->pluck('user_id');

        $incidents = Incident::with('applicationLog.icon', 'employee.team')
            ->whereIn('user_id', $employeeUserIds)
            ->when($userIdByEmployeeId, function ($q) use ($userIdByEmployeeId) {
                $q->where('user_id', $userIdByEmployeeId);
            })
            ->whereBetween('capture_date_and_time', [$startOfDay, $endOfDay])
            ->orderBy('capture_date_and_time', 'desc')
            ->get()
            ->each(function ($incident) {
                $incident->action_percentage = $this->calculateActionPercentage($incident);
            });

        return view('company.screenshot.index', compact(
            'user',
            'teams',
            'employees',
            'employeesCount',
            'incidents',
            'selectedEmployeeId'
        ));
    }

//    public function index(Request $request)
//    {
//        $user = Auth::user();
//        $hasAdminRole = $user->getRoleNames()->contains(ROLE_ADMINISTRATOR);
//
//        if (!$user->can('screenshot')) {
//            return redirect()->back()->with('error', 'Permission denied.');
//        }
//
//        $teams = $hasAdminRole
//            ? Team::where('created_by', $user->creatorId())->get()
//            : Team::where('id', $user->employee->team_id)->get();
//
//        if ($request->has('clear')) {
//            return redirect()->route('organization.screenshot.index');
//        }
//
//        $employees = Employee::where('created_by', $user->creatorId())
//            ->where('is_active', true)
//            ->whereHas('user.roles', function ($q) {
//                $q->whereIn('name', ['standard user', 'stealth user']);
//            })
//            ->when(!$hasAdminRole, function ($q) use ($user) {
//                $q->where('team_id', $user->employee->team_id);
//            })
//            ->when($request->filled('team_id'), function ($q) use ($request) {
//                $q->where('team_id', $request->input('team_id'));
//            })
//            ->orderBy('name', 'asc')
//            ->get();
//
//        $employeesCount = $employees->count();
//        $selectedEmployeeId = $request->input('employee_id') ?? ($employees->first()?->id);
//        $userIdByEmployeeId = Employee::find($selectedEmployeeId)?->user_id;
//
//        $dateRange = $request->input('date_range');
//        if ($dateRange) {
//            try {
//                if (str_contains($dateRange, ' to ')) {
//                    [$startDate, $endDate] = explode(' to ', $dateRange);
//                } else {
//                    $startDate = $endDate = $dateRange;
//                }
//
//                $startOfDay = Carbon::createFromFormat('Y-m-d', trim($startDate), 'Asia/Kolkata')->startOfDay();
//                $endOfDay = Carbon::createFromFormat('Y-m-d', trim($endDate), 'Asia/Kolkata')->endOfDay();
//            } catch (\Exception $e) {
//                Log::error('Invalid date_range input: ' . $e->getMessage());
//                $startOfDay = now('Asia/Kolkata')->startOfDay();
//                $endOfDay = now('Asia/Kolkata')->endOfDay();
//            }
//        } else {
//            $startOfDay = now('Asia/Kolkata')->startOfDay();
//            $endOfDay = now('Asia/Kolkata')->endOfDay();
//        }
//
//        $employeeUserIds = $employees->pluck('user_id');
//
//        $incidents = Incident::with('applicationLog.icon', 'employee.team')
//            ->whereIn('user_id', $employeeUserIds)
//            ->when($userIdByEmployeeId, function ($q) use ($userIdByEmployeeId) {
//                $q->where('user_id', $userIdByEmployeeId);
//            })
//            ->whereBetween('capture_date_and_time', [$startOfDay, $endOfDay])
//            ->orderBy('capture_date_and_time', 'desc')
//            ->get()
//            ->each(function ($incident) {
//                $incident->action_percentage = $this->calculateActionPercentage($incident);
//            });
//
//        return view('company.screenshot.index', compact(
//            'user',
//            'teams',
//            'employees',
//            'employeesCount',
//            'incidents',
//            'selectedEmployeeId'
//        ));
//    }

    /**
     * @param Request $request
     * @param $id
     * @return JsonResponse
     */
    public function toggleHighlight(Request $request, $id)
    {
        $activity = \App\Models\Incident::findOrFail($id);
        $activity->is_highlight = $request->input('is_highlight');
        $activity->save();

        return response()->json(['success' => true]);
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

//    public function fetchIncidents($employeeId)
//    {
//        Log::info($employeeId);
//        $incidents = Incident::where('user_id', $employeeId)
//            ->orderBy('capture_date_and_time', 'desc')
//            ->get();
//
//        if ($incidents->isEmpty()) {
//            return response()->json([
//                'html' => '<div class="col-12 text-center"><p>No screenshots found for this user.</p></div>'
//            ]);
//        }
//
//        $grouped = $incidents->groupBy(function ($item) {
//            return \Carbon\Carbon::parse($item->capture_date_and_time)->format('h A');
//        });
//
//        $html = view('company.screenshot.partials.incidents', [
//            'grouped' => $grouped
//        ])->render();
//
//        return response()->json(['html' => $html]);
//    }
}
