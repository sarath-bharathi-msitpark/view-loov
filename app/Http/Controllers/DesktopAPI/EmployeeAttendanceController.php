<?php

namespace App\Http\Controllers\DesktopAPI;

use App\Http\Controllers\Controller;
use App\Models\AttendanceEmployee;
use App\Models\Incident;
use App\Models\Team;
use App\Models\Utility;
use App\Models\IdleTimeOut;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class EmployeeAttendanceController extends Controller
{
    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function attendanceClockIn(Request $request)
    {
        $user = Auth::user();

        if (
            $user->track_type !== USER_APK_TYPE_SYSTEM_TRACK ||
            $user->type !== 'Employee'
        ) {
            Auth::logout();
            return response()->json([
                'is_success' => false,
                'message' => 'Access denied. Only Standard Employees with System Track are allowed to log in.',
            ], 403);
        }

        $employee = $user->employee;

        if (!$employee) {
            return response()->json([
                'is_success' => false,
                'message' => 'Employee not found.'
            ], 404);
        }

        $employeeId = $employee->id;

        $existingAttendance = AttendanceEmployee::where('employee_id', $employeeId)
            ->where('clock_out', '00:00:00')
            ->whereNotNull('clock_out_date')
            ->latest()
            ->first();

        if ($existingAttendance) {
            return response()->json([
                'is_success' => false,
                'message' => 'You have already clocked in and not yet clocked out.'
            ], 400);
        }

        // Shift data
        $shift = $employee->shift;

        if (!$shift) {
            return response()->json([
                'is_success' => false,
                'message' => 'Shift not assigned to employee.'
            ], 400);
        }

        $now = now();
        $today = $now->format('Y-m-d');
        $shiftStart = Carbon::parse($today . ' ' . $shift->start_time);

        // Calculate late time
        $late = '00:00:00';
        if ($now->greaterThan($shiftStart)) {
            $late = $shiftStart->diff($now)->format('%H:%I:%S');
        }

        AttendanceEmployee::create([
            'employee_id' => $employeeId,
            'date' => $today,
            'status' => 'Present',
            'clock_in' => $now->format('H:i:s'),
            'clock_out' => '00:00:00',
            'late' => $late,
            'early_leaving' => '00:00:00',
            'overtime' => '00:00:00',
            'total_rest' => '00:00:00',
            'created_by' => $user->creatorId(),
        ]);

        $employee->update(['is_punchedIn' => true]);

        return response()->json([
            'is_success' => true,
            'message' => 'Clocked In Successfully'
        ], 200);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function attendanceClockOut(Request $request)
    {
        $user = Auth::user();
        $employee = $user->employee;

        if (!$employee) {
            return response()->json([
                'is_success' => false,
                'message' => 'Employee record not found for this user.'
            ], 404);
        }

        $employeeId = $employee->id;

        $openAttendance = AttendanceEmployee::where('employee_id', $employeeId)
            ->where('clock_out', '00:00:00')
            ->whereNull('clock_out_date')
            ->latest()
            ->first();

        if (!$openAttendance) {
            return response()->json([
                'is_success' => false,
                'message' => "You haven't clocked in or have already clocked out."
            ], 400);
        }

        $currentTime = now();
        $companyEndTime = Carbon::parse($openAttendance->date . ' ' . Utility::getValByName('company_end_time'));

        $earlyLeaving = '00:00:00';
        $overtime = '00:00:00';

        if ($currentTime->lt($companyEndTime)) {
            $earlyLeaving = $companyEndTime->diff($currentTime)->format('%H:%I:%S');
        } elseif ($currentTime->gt($companyEndTime)) {
            $overtime = $companyEndTime->diff($currentTime)->format('%H:%I:%S');
        }

        // Total worked hours
        // handle cross-midnight shifts
        $clockInTime = Carbon::parse($openAttendance->date . ' ' . $openAttendance->clock_in);
        if ($currentTime->lt($clockInTime)) {
            $currentTime->addDay();
        }
        $totalWorked = $clockInTime->diff($currentTime)->format('%H:%I:%S');

        $openAttendance->update([
            'clock_out' => $currentTime->format('H:i:s'),
            'clock_out_date' => $currentTime->format('Y-m-d'),
            'early_leaving' => $earlyLeaving,
            'overtime' => $overtime,
            'total_work' => $totalWorked,
        ]);

        $employee->update(['is_punchedIn' => false]);

        return response()->json([
            'is_success' => true,
            'message' => 'Clocked Out Successfully'
        ], 200);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function attendanceStatus(Request $request)
    {
        $request->validate([
            'date' => 'required|date',
        ]);

        $user = Auth::user();
        $isDebugMode = $user->is_debug_mode;

        // helper closure for conditional logging
        $debugLog = function ($message, $context = []) use ($isDebugMode) {
            if ($isDebugMode) {
                Log::info($message, $context);
            }
        };

        $debugLog('User', ['name' => $user->name]);

        $employee = $user->employee;

        if (!$employee) {
            return response()->json([
                'is_success' => false,
                'message' => 'Employee not found.',
            ], 404);
        }

        $team = Team::findOrFail($employee->team_id);
        $teamFrequency = (int)$team->is_screenshot_frequency;
        $date = $request->input('date');

        // fetch all attendance records for the date
        $records = AttendanceEmployee::with('breakTimes')
            ->where('employee_id', $employee->id)
            ->whereDate('date', $date)
            ->orderBy('id', 'desc')
            ->get();

        // find open record
        $openRecord = $records
            ->where('clock_out', '00:00:00')
            ->whereNull('clock_out_date')
            ->sortByDesc('clock_in')
            ->first();

        // find last activity (incident)
        $lastEvent = Incident::where('user_id', $user->id)
            ->whereDate('created_at', $date)
            ->latest()
            ->first();

        $currentBreak = null;
        if ($openRecord) {
            $currentBreak = $openRecord->breakTimes()
                ->whereNull('break_ended_at')
                ->latest()
                ->with('breakType')
                ->first();
        }

        $onlineSeconds = 0;
        $totalBreakSeconds = 0;

        // --- Handle auto clock-out if threshold passed ---
        if ($openRecord && $lastEvent) {
            $debugLog('Open Record and Last Event Found');

            $lastEventTime = Carbon::parse($lastEvent->created_at);
            $threshold = $lastEventTime->copy()->addMinutes($teamFrequency + 90);

            $debugLog('Last Event & Threshold', [
                'lastEventTime' => $lastEventTime->format('H:i:s'),
                'threshold' => $threshold->format('H:i:s')
            ]);

            if (now()->greaterThan($threshold)) {
                $clockOutTime = $lastEventTime;

                $companyEndTime = Carbon::parse($date . ' ' . Utility::getValByName('company_end_time'));
                $earlyLeaving = '00:00:00';
                $overtime = '00:00:00';

                if ($clockOutTime->lt($companyEndTime)) {
                    $earlyLeaving = $companyEndTime->diff($clockOutTime)->format('%H:%I:%S');
                } else {
                    $overtime = $clockOutTime->diff($companyEndTime)->format('%H:%I:%S');
                }

                $clockIn = Carbon::parse($openRecord->date . ' ' . $openRecord->clock_in);

                $totalWorked = $clockIn->diff($clockOutTime)->format('%H:%I:%S');

                $openRecord->update([
                    'clock_out' => $clockOutTime->format('H:i:s'),
                    'clock_out_date' => $clockOutTime->format('Y-m-d'),
                    'early_leaving' => $earlyLeaving,
                    'overtime' => $overtime,
                    'total_work' => $totalWorked,
                ]);

                $employee->update(['is_punchedIn' => false]);

                $debugLog('Auto Clock-Out Applied', [
                    'clockOutTime' => $clockOutTime->format('H:i:s'),
                    'total_work' => $totalWorked
                ]);
            }
        }

        // --- Calculate total online & break time for completed records ---
        foreach ($records as $record) {
            if ($record->clock_in && $record->clock_in !== '00:00:00' &&
                $record->clock_out && $record->clock_out !== '00:00:00') {

                $clockIn = Carbon::parse($record->date . ' ' . $record->clock_in);
                $clockOut = Carbon::parse(($record->clock_out_date ?? $record->date) . ' ' . $record->clock_out);

                $duration = $clockIn->diffInSeconds($clockOut);

                // sum all breaks safely
                $breakSeconds = $record->breakTimes->sum(function ($break) {
                    $start = $break->break_started_at ? Carbon::parse($break->break_started_at) : null;
                    $end = $break->break_ended_at ? Carbon::parse($break->break_ended_at) : null;

                    if (!$start) {
                        return 0;
                    }

                    return $end && $end->gt($start)
                        ? $start->diffInSeconds($end)
                        : 0; // ignore incomplete/invalid breaks
                });

                $breakSeconds = min($breakSeconds, $duration); // cap breaks
                $totalBreakSeconds += $breakSeconds;

                $netOnline = max($duration - $breakSeconds, 0);
                $onlineSeconds += $netOnline;

                $debugLog('Record Duration', [
                    'clock_in' => $clockIn->format('H:i:s'),
                    'clock_out' => $clockOut->format('H:i:s'),
                    'duration' => gmdate('H:i:s', $duration),
                    'break_seconds' => gmdate('H:i:s', $breakSeconds),
                    'net_online' => gmdate('H:i:s', $netOnline),
                ]);
            }
        }

        // --- Handle ongoing open record (fixed: always use now) ---
        if ($openRecord) {
            $clockIn = Carbon::parse($openRecord->date . ' ' . $openRecord->clock_in);
            $clockOut = now(); // FIX: don't stop at lastEvent, always count till now

            $duration = $clockIn->diffInSeconds($clockOut);

            // safer break calculation
            $breakSeconds = 0;
            foreach ($openRecord->breakTimes as $break) {
                $start = $break->break_started_at ? Carbon::parse($break->break_started_at) : null;
                $end = $break->break_ended_at ? Carbon::parse($break->break_ended_at) : null;

                if (!$start) {
                    continue;
                }

                if ($end && $end->gt($start)) {
                    $breakSeconds += $start->diffInSeconds($end);
                } elseif (!$end && $start->lt($clockOut)) {
                    $breakSeconds += $start->diffInSeconds($clockOut);
                }
            }

            $breakSeconds = min($breakSeconds, $duration); // cap
            $totalBreakSeconds += $breakSeconds;

            $extraOnline = max($duration - $breakSeconds, 0);
            $onlineSeconds += $extraOnline;

            $debugLog('Open Record Ongoing', [
                'clock_in' => $clockIn->format('H:i:s'),
                'clock_out_used' => $clockOut->format('H:i:s'),
                'duration' => gmdate('H:i:s', $duration),
                'break_seconds' => gmdate('H:i:s', $breakSeconds),
                'extra_online' => gmdate('H:i:s', $extraOnline)
            ]);
        }

        $totalWorked = gmdate('H:i:s', $onlineSeconds);
        $totalBreaks = gmdate('H:i:s', $totalBreakSeconds);

        $firstPunchIn = $records
            ->where('clock_in', '!=', '00:00:00')
            ->sortBy('clock_in')
            ->first();

        $lastPunchIn = $records
            ->where('clock_in', '!=', '00:00:00')
            ->sortByDesc('clock_in')
            ->first();

        $debugLog('Attendance Summary', [
            'total_online' => $totalWorked,
            'total_breaks' => $totalBreaks,
            'first_punch_in' => $firstPunchIn ? $firstPunchIn->clock_in : null,
            'last_punch_in' => $lastPunchIn ? $lastPunchIn->clock_in : null,
        ]);

        return response()->json([
            'is_success' => true,
            'message' => 'Attendance status fetched successfully',
            'date' => $date,
            'online_hrs' => $totalWorked,
            'break_hrs' => $totalBreaks,
            'is_punchedIn' => $openRecord ? true : false,
            'first_punch_in' => $firstPunchIn
                ? Carbon::parse($firstPunchIn->clock_in)->format('h:i A')
                : null,
            'last_punch_in' => $lastPunchIn
                ? Carbon::parse($lastPunchIn->clock_in)->format('h:i A')
                : '--',
            'last_event_at' => $lastEvent
                ? Carbon::parse($lastEvent->created_at)->format('H:i:s')
                : null,
            'on_break' => $currentBreak ? true : false,
            'break_details' => $currentBreak ? [
                'break_type' => $currentBreak->breakType ?? 'Unknown',
                'started_at' => $currentBreak->break_started_at,
            ] : null,
        ]);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function idleTimeStart(Request $request)
    {
        $request->validate([
            'start_time_and_date' => 'required',
        ]);

        $user = Auth::user();
        $employee = $user->employee;

        if (!$employee) {
            return response()->json([
                'is_success' => false,
                'message' => 'Employee not found.',
            ], 404);
        }

        $startDateTime = Carbon::parse($request->start_time_and_date);
        $date = $startDateTime->toDateString();

        // Get all attendance records for the date
        $records = AttendanceEmployee::where('employee_id', $employee->id)
            ->whereDate('date', $date)
            ->orderBy('id', 'desc')
            ->get();

        // Find the latest open clock-in (no clock_out)
        $openRecord = $records->firstWhere('clock_out', '00:00:00');

        if (!$openRecord) {
            return response()->json([
                'is_success' => false,
                'message' => 'No open attendance record found.',
            ], 404);
        }

        // Create idle time record
        $idletime = IdleTimeOut::create([
            'user_id' => $user->id,
            'attendance_id' => $openRecord->id,
            'start_time_and_date' => $startDateTime,
        ]);

        return response()->json([
            'is_success' => true,
            'idle_time' => $idletime,
            'message' => 'Idle time started successfully.',
        ], 200);
    }

    /**
     * @param Request $request
     * @param $id
     * @return JsonResponse
     */
    public function idleTimEnd(Request $request, $id)
    {
        $request->validate([
            'end_time_and_date' => 'nullable|date',
        ]);

        $user = Auth::user();

        $idleTime = IdleTimeOut::where('id', $id)
            ->where('user_id', $user->id)
            ->first();

        if (!$idleTime) {
            return response()->json([
                'is_success' => false,
                'message' => 'Idle time record not found.',
            ], 400);
        }

        $startDateTime = Carbon::parse($idleTime->start_time_and_date);
        $endDateTime = $request->end_time_and_date
            ? Carbon::parse($request->end_time_and_date)
            : now();

        if ($endDateTime->lessThan($startDateTime)) {
            return response()->json([
                'is_success' => false,
                'message' => 'End time cannot be before start time.',
            ], 400);
        }

        $duration = $startDateTime->diffInSeconds($endDateTime);

        $idleTime->end_time_and_date = $endDateTime->format('Y-m-d H:i:s');
        $idleTime->duration = $duration;
        $idleTime->save();

        return response()->json([
            'is_success' => true,
            'idle_time' => $idleTime,
            'message' => 'Idle time ended successfully.',
        ], 200);
    }
}

