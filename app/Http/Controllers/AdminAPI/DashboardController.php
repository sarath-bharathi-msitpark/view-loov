<?php

namespace App\Http\Controllers\AdminAPI;

use App\Http\Controllers\Controller;
use App\Models\AttendanceEmployee;
use App\Models\Employee;
use App\Models\IdleTimeOut;
use App\Models\Incident;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class DashboardController extends Controller
{
    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request)
    {
        $request->validate([
            'date' => 'nullable|date'
        ]);

        $authUser = Auth::user();
        $date = $request->filled('date')
            ? Carbon::parse($request->date)->toDateString()
            : Carbon::now()->toDateString();

        $employeeIds = Employee::where('created_by', $authUser->id)->pluck('id')->toArray();
        $totalEmployees = count($employeeIds);

        $presentCount = AttendanceEmployee::whereIn('employee_id', $employeeIds)
            ->whereDate('created_at', $date)
            ->count();

        $absentCount = $totalEmployees - $presentCount;
        $attendancePercentage = $totalEmployees > 0 ? round(($presentCount / $totalEmployees) * 100, 2) : 0;

        return response()->json([
            'is_success' => true,
            'message' => 'Dashboard data retrieved successfully',
            'data' => [
                'date' => $date,
                'total_employees' => $totalEmployees,
                'present_count' => $presentCount,
                'absent_count' => $absentCount,
                'attendance_percentage' => $attendancePercentage,
            ]
        ], 200);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function productiveBreakdown(Request $request)
    {
        $request->validate([
            'team_id' => 'nullable|exists:teams,id',
            'date' => 'nullable|date_format:Y-m-d',
        ]);

        $date = $request->input('date') ?? now()->format('Y-m-d');
        $timezone = env('APP_TIMEZONE', 'Asia/Kolkata');

        // Get all employees (optionally filtered by team)
        $employeeQuery = Employee::with('team')
            ->where('created_by', Auth::user()->creatorId())
            ->where('is_active', true);

        if ($request->filled('team_id')) {
            $employeeQuery->where('team_id', $request->input('team_id'));
        }

        $employees = $employeeQuery->get();

        // Calculate worked seconds per team
        $teamData = [];

        foreach ($employees as $employee) {
            if (!$employee->team) {
                continue;
            }

            $teamId = $employee->team->id;
            $teamName = $employee->team->name;
            $workedSeconds = $this->calculateWorkedSecondsForDate($employee->id, $date, $timezone);

            if (!isset($teamData[$teamId])) {
                $teamData[$teamId] = [
                    'team_id' => $teamId,
                    'team_name' => $teamName,
                    'worked_seconds' => 0,
                    'percentages' => [],
                ];
            }

            $teamData[$teamId]['worked_seconds'] += $workedSeconds;
        }

        // Fetch incidents and calculate productivity percentages
        $employeeIds = $employees->pluck('user_id');

        $startOfDay = Carbon::parse($date)->startOfDay();
        $endOfDay = Carbon::parse($date)->endOfDay();

        $incidents = Incident::with('employee.team')
            ->whereIn('user_id', $employeeIds)
            ->whereBetween('created_at', [$startOfDay, $endOfDay])
            ->get();

        foreach ($incidents as $incident) {
            if (!$incident->employee || !$incident->employee->team) {
                continue;
            }

            $teamId = $incident->employee->team->id;
            $percentage = $this->calculateActionPercentage($incident);

            if (!isset($teamData[$teamId])) {
                // Skip teams not already initialized from employees
                continue;
            }

            $teamData[$teamId]['percentages'][] = $percentage;
        }

        // Format the output
        $teamProductivity = collect($teamData)->map(function ($team) use ($employees, $date) {
            $teamIdleSeconds = 0;

            foreach ($employees as $employee) {
                if (!$employee->team || $employee->team->id !== $team['team_id']) {
                    continue;
                }

                $userId = $employee->user_id;

                $idleDurations = IdleTimeOut::where('user_id', $userId)
                    ->whereDate('start_time_and_date', $date)
                    ->whereNotNull('duration')
                    ->sum('duration');

                $teamIdleSeconds += $idleDurations;
            }

            $average = count($team['percentages']) > 0
                ? round(array_sum($team['percentages']) / count($team['percentages']), 2)
                : 0;

            return [
                'team_id' => $team['team_id'],
                'team_name' => $team['team_name'],
                'average_productivity' => $average,
                'worked_hours' => $this->formatDuration($team['worked_seconds']),
                'idle_time' => $this->formatDuration($teamIdleSeconds),
            ];
        })->sortBy('team_name')->values();

        return response()->json([
            'status' => true,
            'message' => 'Team-wise productivity breakdown retrieved successfully.',
            'data' => $teamProductivity,
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

    private function calculateWorkedSecondsForDate($employeeId, $date, $timezone = 'Asia/Kolkata')
    {
        $attendances = AttendanceEmployee::with('breakTimes')
            ->where('employee_id', $employeeId)
            ->whereDate('date', $date)
            ->get();

        $onlineSeconds = 0;
        $breakSeconds = 0;

        foreach ($attendances as $record) {
            if (
                $record->clock_in && $record->clock_in !== '00:00:00' &&
                $record->clock_out && $record->clock_out !== '00:00:00'
            ) {
                try {
                    $clockIn = Carbon::parse($record->date . ' ' . $record->clock_in, $timezone);
                    $clockOutDate = $record->clock_out_date ?? $record->date;
                    $clockOut = Carbon::parse($clockOutDate . ' ' . $record->clock_out, $timezone);

                    if ($clockOut->lt($clockIn)) {
                        $clockOut->addDay();
                    }

                    $onlineSeconds += $clockIn->diffInSeconds($clockOut);
                } catch (\Throwable $e) {
                    continue;
                }
            }

            $breakSeconds += $record->breakTimes->sum(function ($b) {
                try {
                    [$h, $m, $s] = explode(':', $b->duration);
                    return ((int)$h * 3600) + ((int)$m * 60) + (int)$s;
                } catch (\Throwable $e) {
                    return 0;
                }
            });
        }

        return max($onlineSeconds - $breakSeconds, 0);
    }

    private function formatDuration($seconds)
    {
        $hours = floor($seconds / 3600);
        $minutes = floor(($seconds % 3600) / 60);
        $seconds = $seconds % 60;

        return sprintf('%02d:%02d:%02d', $hours, $minutes, $seconds);
    }
}
