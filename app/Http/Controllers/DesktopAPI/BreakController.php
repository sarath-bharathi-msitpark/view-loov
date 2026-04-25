<?php

namespace App\Http\Controllers\DesktopAPI;

use App\Http\Controllers\Controller;
use App\Models\AttendanceEmployee;
use App\Models\BreakTime;
use App\Models\BreakType;
use App\Models\Employee;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class BreakController extends Controller
{
    /**
     * @return JsonResponse
     */
    public function breaks()
    {
        $breakTypes = BreakType::all();

        return response()->json([
            'is_success' => true,
            'breakTypes' => $breakTypes,
            'message' => 'Break Types Retrieved Successfully.'
        ], 200);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function getAvailableBreaks(Request $request)
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json([
                'is_success' => false,
                'message' => 'Unauthorized',
            ], 401);
        }

        $employee = Employee::where('user_id', $user->id)->first();

        if (!$employee) {
            return response()->json([
                'is_success' => false,
                'message' => 'Employee record not found',
            ], 404);
        }

        // Fetch all active breaks for the company
        $breaks = BreakType::where('created_by', $employee->created_by)
            ->where('status', 1)
            ->get(['id', 'break_name', 'maximum_break_time', 'break_limit_apply']);

        $today = Carbon::today();

        // Fetch all break_type_ids that this employee has taken today
        $takenBreakIds = BreakTime::where('employee_id', $employee->id)
            ->whereDate('created_at', $today)
            ->pluck('break_type_id')
            ->toArray();

        // Filter available breaks
        $availableBreaks = $breaks->filter(function ($break) use ($takenBreakIds) {
            // If multi-use (break_limit_apply == 1), always show
            if ($break->break_limit_apply == 1) {
                return true;
            }

            // If single-use (break_limit_apply == 0), only show if not taken
            return !in_array($break->id, $takenBreakIds);
        })->values(); // Reset array keys

        return response()->json([
            'is_success' => true,
            'data' => $availableBreaks,
            'message' => 'Available breaks fetched successfully',
        ], 200);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function startBreak(Request $request)
    {
        $request->validate([
            'break_type_id' => 'required|numeric|exists:break_type,id',
        ]);

        $user = Auth::user();
        $employee = $user->employee;

        if (!$employee) {
            return response()->json([
                'is_success' => false,
                'message' => 'Employee record not found for this user.'
            ], 404);
        }

        $today = now()->toDateString();

        $attendance = AttendanceEmployee::where('employee_id', $employee->id)
            ->whereDate('date', $today)
            ->where('clock_out', '00:00:00')
            ->first();

        if (!$attendance) {
            return response()->json([
                'is_success' => false,
                'message' => 'You need to clock in first.'
            ], 400);
        }

        // Check for any ongoing break
        $ongoingBreak = BreakTime::where('attendance_id', $attendance->id)
            ->whereNull('break_ended_at')
            ->first();

        if ($ongoingBreak) {
            if ($ongoingBreak->break_type_id == $request->break_type_id) {
                return response()->json([
                    'is_success' => false,
                    'message' => 'You have already started this break type. Please end it before starting again.'
                ], 400);
            } else {
                return response()->json([
                    'is_success' => false,
                    'message' => 'You have an ongoing break. Please end it before starting a new one.'
                ], 400);
            }
        }

        $breakType = BreakType::find($request->break_type_id);

        $break = BreakTime::create([
            'attendance_id' => $attendance->id,
            'employee_id' => $employee->id,
            'break_type_id' => $request->break_type_id,
            'break_started_at' => now(),
        ]);

        $employee->update([
            'is_inBreak' => true
        ]);

        return response()->json([
            'is_success' => true,
            'message' => ucfirst($breakType->break_name) . ' break started.',
            'data' => ['break_id' => $break->id]
        ], 200);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function endBreak(Request $request)
    {
        $request->validate([
            'break_id' => 'nullable|exists:break_times,id',
            'break_type_id' => 'required_without:break_id|numeric|exists:break_type,id',
        ]);

        $employee = Auth::user()->employee;

        if (!$employee) {
            return response()->json([
                'is_success' => false,
                'message' => 'Employee record not found for this user.'
            ], 404);
        }

        $attendance = AttendanceEmployee::where('employee_id', $employee->id)
            ->whereDate('date', now()->toDateString())
            ->where('clock_out', '00:00:00')
            ->first();

        if (!$attendance) {
            return response()->json([
                'is_success' => false,
                'message' => 'You need to clock in first.'
            ], 400);
        }

        $break = $request->break_id
            ? BreakTime::where('id', $request->break_id)
                ->where('attendance_id', $attendance->id)
                ->whereNull('break_ended_at')
                ->first()
            : BreakTime::where('attendance_id', $attendance->id)
                ->where('break_type_id', $request->break_type_id)
                ->whereNull('break_ended_at')
                ->first();

        if (!$break) {
            return response()->json([
                'is_success' => false,
                'message' => $request->break_id
                    ? 'No active break found with this ID.'
                    : 'No active break of this type found.'
            ], 400);
        }

        $breakEndedAt = now();
        $breakStartedAt = Carbon::parse($break->break_started_at);

        $break->break_ended_at = $breakEndedAt;
        $break->duration = $breakStartedAt->diff($breakEndedAt)->format('%H:%I:%S');
        $break->save();

        $employee->update([
            'is_inBreak' => false
        ]);

        $breakType = BreakType::find($break->break_type_id);
        $maxSeconds = $breakType->maximum_break_time * 60;
        $actualSeconds = $breakStartedAt->diffInSeconds($breakEndedAt);

        if ($actualSeconds > $maxSeconds) {
            return response()->json([
                'is_success' => true,
                'message' => 'Your break time has exceeded the allowed limit of ' . $breakType->maximum_break_time . ' minutes.'
            ], 200);
        }

        $totalRest = BreakTime::where('attendance_id', $attendance->id)
            ->sum(\DB::raw('TIME_TO_SEC(duration)'));

        $attendance->total_rest = gmdate('H:i:s', $totalRest);
        $attendance->save();

        return response()->json([
            'is_success' => true,
            'message' => ucfirst($breakType->break_name) . ' break ended. Duration: ' . $break->duration
        ], 200);
    }
}
