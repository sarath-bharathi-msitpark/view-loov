<?php

namespace App\Http\Controllers\Web\Company;

use App\Http\Controllers\Controller;
use App\Models\Incident;
use App\Models\Team;
use App\Models\AttendanceEmployee;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Models\Employee;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class DashboardController extends Controller
{
    /**
     * @param Request $request
     * @return Factory|View|Application|object
     */
    public function index(Request $request)
    {
        $creatorId = Auth::user()->creatorId();

        $employeeQuery = Employee::where('created_by', $creatorId)
            ->where('is_active', true)
            ->whereHas('user', function ($query) {
                $query->where('track_type', USER_APK_TYPE_SYSTEM_TRACK)
                    ->whereHas('roles', function ($roleQuery) {
                        $roleQuery->whereIn('name', ['stealth user', 'standard user']);
                    });
            });

        if ($request->filled('team_id') && $request->team_id !== 'All Team') {
            $employeeQuery->where('team_id', $request->team_id);
        }

        if ($request->filled('user_id')) {
            $employeeQuery->where('user_id', $request->user_id);
        }

        $employeeIds = $employeeQuery->pluck('id');
        $totalEmployees = $employeeIds->count();

        $dateRange = $request->input('date_range');

        try {
            if ($dateRange && str_contains($dateRange, 'to')) {
                [$startDateStr, $endDateStr] = array_map('trim', explode('to', $dateRange));
                $startDate = Carbon::parse($startDateStr)->startOfDay();
                $endDate = Carbon::parse($endDateStr)->endOfDay();
            } elseif ($dateRange) {
                $startDate = Carbon::parse(trim($dateRange))->startOfDay();
                $endDate = Carbon::parse(trim($dateRange))->endOfDay();
            } else {
                $startDate = Carbon::today()->startOfDay();
                $endDate = Carbon::today()->endOfDay();
            }
        } catch (\Exception $e) {
            Log::error('Invalid date_range: ' . $dateRange);
            $startDate = Carbon::today()->startOfDay();
            $endDate = Carbon::today()->endOfDay();
        }

        $firstDay = $startDate->copy();
        $todayAttendances = AttendanceEmployee::whereIn('employee_id', $employeeIds)
            ->whereDate('date', $firstDay)
            ->orderBy('clock_in', 'asc')
            ->get();

        $todayFirstPunches = $todayAttendances->groupBy('employee_id')->map(fn($group) => $group->first());

        $presentCount = $todayFirstPunches->count();
        $absentCount = $totalEmployees - $presentCount;

        $presentPercent = $totalEmployees > 0 ? round(($presentCount / $totalEmployees) * 100) : 0;
        $absentPercent = $totalEmployees > 0 ? round(($absentCount / $totalEmployees) * 100) : 0;

        $onTimeCount = $todayFirstPunches->where('late', '00:00:00')->count();
        $lateCount = $todayFirstPunches->where('late', '!=', '00:00:00')->count();

        $onTimePercent = $presentCount > 0 ? round(($onTimeCount / $presentCount) * 100) : 0;
        $latePercent = $presentCount > 0 ? round(($lateCount / $presentCount) * 100) : 0;

        $onTimeEmployees = $todayFirstPunches->filter(function ($attendance) {
            return $attendance->late === '00:00:00';
        })->map(function ($attendance) {
            return $attendance->employee->user->name ?? 'Unknown';
        })->values();

        $lateEmployees = $todayFirstPunches->filter(function ($attendance) {
            return $attendance->late !== '00:00:00';
        })->map(function ($attendance) {
            return $attendance->employee->user->name ?? 'Unknown';
        })->values();

        $last7Days = collect(range(0, 6))->map(fn($i) => Carbon::today()->subDays($i)->format('Y-m-d'))->reverse();

        $chartData = $last7Days->map(function ($date) use ($employeeIds) {
            $attendances = AttendanceEmployee::whereIn('employee_id', $employeeIds)
                ->whereDate('date', $date)
                ->get()
                ->groupBy('employee_id')
                ->map(fn($group) => $group->first());

            $present = $attendances->count();
            $total = $employeeIds->count();
            $absent = $total - $present;

            return [
                'date' => Carbon::parse($date)->format('d/m'),
                'present' => $present,
                'absent' => $absent,
            ];
        });

        $incidentUserIds = Employee::whereIn('id', $employeeIds)->pluck('user_id');

        $incidents = Incident::with('employee.team')
            ->whereIn('user_id', $incidentUserIds)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->get();

        $teamData = [];

        foreach ($incidents as $incident) {
            if (!$incident->employee || !$incident->employee->team) {
                continue;
            }

            $teamId = $incident->employee->team->id;
            $teamName = $incident->employee->team->name;

            $percentage = $this->calculateActionPercentage($incident);

            if (!isset($teamData[$teamId])) {
                $teamData[$teamId] = [
                    'team_id' => $teamId,
                    'team_name' => $teamName,
                    'percentages' => [],
                ];
            }

            $teamData[$teamId]['percentages'][] = $percentage;
        }

        $teamProductivity = collect($teamData)->map(function ($team) {
            $average = count($team['percentages']) > 0
                ? round(array_sum($team['percentages']) / count($team['percentages']), 2)
                : 0;

            return [
                'team_id' => $team['team_id'],
                'team_name' => $team['team_name'],
                'average_productivity' => $average,
            ];
        });

        $proactiveTeams = $teamProductivity->sortByDesc('average_productivity')->values();
        $lethargicTeams = $teamProductivity->sortBy('average_productivity')->values();

        $teams = Team::where('created_by', $creatorId)->get();

        return view('company.dashboard', compact(
            'presentCount',
            'absentCount',
            'presentPercent',
            'absentPercent',
            'onTimeCount',
            'lateCount',
            'onTimePercent',
            'latePercent',
            'teams',
            'chartData',
            'proactiveTeams',
            'lethargicTeams',
            'onTimeEmployees',
            'lateEmployees'
        ));
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
     * @return JsonResponse
     */
    public function updateAPKUpdateNotify()
    {
        $user = \Auth::user();
        $user->is_apk_update_notified = false;
        $user->save();

        return response()->json(['success' => true]);
    }
}
