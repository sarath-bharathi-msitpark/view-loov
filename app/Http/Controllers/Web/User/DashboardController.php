<?php

namespace App\Http\Controllers\Web\User;

use App\Http\Controllers\Controller;
use App\Models\WorkLog;
use App\Models\WorkPlace;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Models\BreakTime;
use App\Models\Employee;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use File;
use Carbon\Carbon;
use App\Models\AttendanceEmployee;
use App\Models\ApplicationLog;
use App\Models\Incident;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Illuminate\Http\RedirectResponse;
use Illuminate\Validation\ValidationException;

class DashboardController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        return view('employee_web.dashboard');
    }

    /**
     * @param Request $request
     * @return Factory|View|Application|RedirectResponse|object
     */
    public function attendance(Request $request)
    {
        $authUser = Auth::user();
        $employee = Employee::where('user_id', $authUser->id)->first();

        if (!$employee) {
            return redirect()->back()->with('error', 'Employee profile not found.');
        }

        $workplace = WorkPlace::where('user_id', $authUser->creatorId())->first();

        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        if ($startDate && $endDate) {
            $start = Carbon::parse($startDate)->startOfDay();
            $end = Carbon::parse($endDate)->endOfDay();
        } else {
            $end = Carbon::today()->endOfDay();
            $start = $end->copy()->startOfMonth();
        }

        $attendanceDates = AttendanceEmployee::select(DB::raw('DATE(date) as day'))
            ->where('employee_id', $employee->id)
            ->whereBetween('date', [$start->toDateString(), $end->toDateString()])
            ->whereNotNull('clock_in')
            ->groupBy(DB::raw('DATE(date)'))
            ->pluck('day')
            ->toArray();

        $workingDays = [];
        for ($date = $start->copy(); $date->lte($end); $date->addDay()) {
            $workingDays[] = $date->toDateString();
        }

        $absentDates = array_diff($workingDays, $attendanceDates);
        $presentCount = count($attendanceDates);
        $absentCount = count($absentDates);

        // Pagination variables
        $perPage = $request->input('per_page', 10);
        $page = $request->input('page', 1);

        // Fetch all attendance records for the employee within date range
        $records = AttendanceEmployee::with(['employee.user', 'idleTimeOut', 'breakTimes'])
            ->where('employee_id', $employee->id)
            ->whereBetween('date', [$start->toDateString(), $end->toDateString()])
            ->orderBy('created_at', 'desc')
            ->get();

        // Fetch WorkLogs for employee within date range
        $workLogs = WorkLog::where('employee_id', $employee->id)
            ->whereBetween('date', [$start->toDateString(), $end->toDateString()])
            ->get()
            ->groupBy('date');

        // Group attendance by day
        $grouped = $records->groupBy(fn($item) => $item->employee_id . '-' . $item->date);

        $attendances = $grouped->map(function ($group) use ($workplace, $workLogs) {
            $onlineSeconds = $group->sum(function ($record) {
                if ($record->clock_in && $record->clock_in !== '00:00:00' &&
                    $record->clock_out && $record->clock_out !== '00:00:00') {
                    try {
                        $clockIn = Carbon::parse($record->date . ' ' . $record->clock_in);
                        $clockOutDate = $record->clock_out_date ?? $record->date;
                        $clockOut = Carbon::parse($clockOutDate . ' ' . $record->clock_out);
                        if ($clockOut->lt($clockIn)) $clockOut->addDay();
                        return $clockIn->diffInSeconds($clockOut);
                    } catch (\Exception $e) {
                        return 0;
                    }
                }
                return 0;
            });

            $totalBreakSeconds = $group->sum(function ($record) {
                return $record->breakTimes->sum(function ($break) {
                    try {
                        [$h, $m, $s] = explode(':', $break->duration);
                        return ((int)$h * 3600) + ((int)$m * 60) + (int)$s;
                    } catch (\Throwable $e) {
                        return 0;
                    }
                });
            });

            $logFormatted = $group->map(function ($log) {
                $clockIn = $log->clock_in ? Carbon::parse($log->clock_in)->format('h:i A') : '--';
                $clockOut = ($log->clock_out && $log->clock_out !== '00:00:00')
                    ? Carbon::parse($log->clock_out)->format('h:i A')
                    : '--';
                $log->formatted_clock_in = $clockIn;
                $log->formatted_clock_out = $clockOut;
                return $log;
            });

            $first = $group->filter(fn($rec) => $rec->clock_in && $rec->clock_in !== '00:00:00')
                ->sortBy('clock_in')
                ->first();

            $last = $group->filter(fn($rec) => $rec->clock_out && $rec->clock_out !== '00:00:00')
                ->sortByDesc('clock_out')
                ->first() ?? $group->last();

            $attendanceDate = $first?->date ?? $group->first()->date;

            $onlineHours = $onlineSeconds / 3600;
            $status = 'Absent';
            if ($workplace) {
                $maxAbsentHours = (float)($workplace->workplace_max_hours_for_absent ?? 0);
                $minHalfDayHours = (float)($workplace->workplace_min_hours_for_half_day ?? 0);
                $fullDayBase = (float)($workplace->workplace_min_hours_for_full_day ?? 0);
                $fullDayHours = 24 - $fullDayBase;

                if ($onlineHours <= $maxAbsentHours) {
                    $status = 'Absent';
                } elseif ($onlineHours > $minHalfDayHours && $onlineHours < $fullDayHours) {
                    $status = 'Half Day';
                } elseif ($onlineHours >= $fullDayHours) {
                    $status = 'Full Day';
                }
            }

            $workLogForDate = $workLogs[$attendanceDate] ?? collect();

            return (object)[
                'employee' => $first?->employee,
                'date' => $attendanceDate,
                'clock_in' => $first?->clock_in ? Carbon::parse($first->clock_in)->format('h:i A') : '--',
                'clock_out' => $last?->clock_out ? Carbon::parse($last->clock_out)->format('h:i A') : '--',
                'late' => $first?->late,
                'early_leaving' => $last?->early_leaving,
                'overtime' => $last?->overtime,
                'total_rest' => $this->formatDuration($totalBreakSeconds),
                'online_time' => $this->formatDuration($onlineSeconds),
                'workplace_status' => $status,
                'idleTimeOut' => $group->flatMap->idleTimeOut,
                'log' => $logFormatted,
                'breakTimes' => $group->flatMap->breakTimes,
                'work_logs' => $workLogForDate,
            ];
        })->values();

        $paginatedAttendances = new \Illuminate\Pagination\LengthAwarePaginator(
            $attendances->forPage($page, $perPage),
            $attendances->count(),
            $perPage,
            $page,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        return view('employee_web.attendance.index', [
            'attendanceRecords' => $paginatedAttendances,
            'presentCount' => $presentCount,
            'absentCount' => $absentCount,
            'attendanceDates' => $attendanceDates,
            'absentDates' => $absentDates,
        ]);
    }

    /**
     * @param $seconds
     * @return string
     */
    private function formatDuration($seconds)
    {
        $hours = floor($seconds / 3600);
        $minutes = floor(($seconds % 3600) / 60);
        $seconds = $seconds % 60;

        return sprintf('%02d:%02d:%02d', $hours, $minutes, $seconds);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function storeWorkLog(Request $request)
    {
        try {
            $request->validate([
                'employee_id' => 'required|exists:employees,id',
                'date_of_workLog' => 'required|date',
                'worklog_description' => 'required|string|max:1000',
            ]);

            WorkLog::updateOrCreate(
                [
                    'employee_id' => $request->employee_id,
                    'date' => $request->date_of_workLog,
                ],
                [
                    'description' => $request->worklog_description,
                ]
            );

            return response()->json([
                'success' => true,
                'message' => 'Work log saved successfully!',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function getMonthlyAttendance(Request $request)
    {
        $authUser = Auth::user();
        $employee = Employee::where('user_id', $authUser->id)->first();

        if (!$employee) {
            return response()->json(['error' => 'Employee not found'], 404);
        }

        $year = $request->input('year');
        $month = $request->input('month');

        if (!$year || !$month) {
            return response()->json(['error' => 'Invalid date parameters'], 400);
        }

        $start = Carbon::createFromDate($year, $month, 1)->startOfMonth();
        $end = $start->copy()->endOfMonth();

        $attendanceDates = AttendanceEmployee::select(DB::raw('DATE(date) as day'))
            ->where('employee_id', $employee->id)
            ->whereBetween('date', [$start->toDateString(), $end->toDateString()])
            ->whereNotNull('clock_in')
            ->groupBy(DB::raw('DATE(date)'))
            ->pluck('day')
            ->toArray();

        $workingDays = [];
        for ($date = $start->copy(); $date->lte($end); $date->addDay()) {
            $workingDays[] = $date->toDateString();
        }

        $absentDates = array_values(array_diff($workingDays, $attendanceDates));

        return response()->json([
            'presentDates' => $attendanceDates,
            'absentDates' => $absentDates,
        ]);
    }

    /**
     * @param Request $request
     * @return Factory|View|Application|RedirectResponse|object
     */
    public function breakInsight(Request $request)
    {
        $authUser = Auth::user();
        $employee = Employee::where('user_id', $authUser->id)->first();

        if (!$employee) {
            return redirect()->back()->with('error', 'Employee profile not found.');
        }

        $query = BreakTime::with(['employee.user', 'employee.team', 'breakType', 'createdBy'])
            ->where('employee_id', $employee->id);

        // Apply date filter if provided
        // if ($request->start_date && $request->end_date) {
        //     $query->whereBetween('created_at', [Carbon::parse($request->start_date)->startOfDay(), Carbon::parse($request->end_date)->endOfDay()]);
        // }

        if ($request->start_date && $request->end_date) {
            $query->whereBetween('created_at', [
                Carbon::parse($request->start_date)->startOfDay(),
                Carbon::parse($request->end_date)->endOfDay()
            ]);
        } else {
            // Default to current month
            $query->whereBetween('created_at', [
                now()->startOfMonth(),
                now()->endOfMonth()
            ]);
        }

        $perPage = $request->input('per_page', 10);
        $page = $request->input('page', 1);

        $breaks = $query->orderBy('created_at', 'desc')
            ->paginate($perPage, ['*'], 'page', $page)
            ->withQueryString();

        return view('employee_web.break_insight.index', compact('breaks'));
    }

    /**
     * @param Request $request
     * @return Factory|View|Application|RedirectResponse|object
     */
    public function activity(Request $request)
    {
        $authUser = Auth::user();
        $employee = Employee::where('user_id', $authUser->id)->first();

        if (!$employee) {
            return redirect()->back()->with('error', 'Employee profile not found.');
        }

        // Handle date filters
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        if ($startDate && $endDate) {
            $startDate = Carbon::parse($startDate)->startOfDay()->toDateTimeString();
            $endDate = Carbon::parse($endDate)->endOfDay()->toDateTimeString();
        } else {
            $startDate = Carbon::now()->subDays(6)->startOfDay()->toDateTimeString(); // Last 7 days
            $endDate = Carbon::now()->endOfDay()->toDateTimeString();
        }

        $query = ApplicationLog::where('user_id', $authUser->id)->whereBetween('created_at', [$startDate, $endDate]);

        // Top Application
        $topApplication = (clone $query)->whereNotNull('application_name')->select('application_name', DB::raw('SUM(TIME_TO_SEC(screen_time)) as total_seconds'))->groupBy('application_name')->orderByDesc('total_seconds')->first();

        // Top URL
        $topUrl = (clone $query)->whereNotNull('url')->select('url', DB::raw('SUM(TIME_TO_SEC(screen_time)) as total_seconds'))->groupBy('url')->orderByDesc('total_seconds')->first();

        // Top Category from URL
        $topCategory = (clone $query)->whereNotNull('url')->select(DB::raw('SUBSTRING_INDEX(url, "/", 3) as category'), DB::raw('SUM(TIME_TO_SEC(screen_time)) as total_seconds'))->groupBy('category')->orderByDesc('total_seconds')->first();

        // Incident data (mouse + keyboard actions)
        $incidents = Incident::where('user_id', $authUser->id)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->select(DB::raw('DATE(created_at) as activity_date'), DB::raw('SUM(keyboard_action_count) as total_keyboard_actions'), DB::raw('SUM(mouse_action_count) as total_mouse_actions'))
            ->groupBy(DB::raw('DATE(created_at)'))
            ->orderByDesc('activity_date')
            ->paginate(10)
            ->withQueryString();

        $totalActiveSeconds = 0;
        $totalBreakSeconds = 0;
        $validDays = 0;
        $totalOnlineSeconds = 0;
        $averageOnlineSeconds = 0;

        foreach ($incidents as $incident) {
            // Attendance with breaks
            $attendances = AttendanceEmployee::with(['employee.user', 'breakTimes'])
                ->where('employee_id', $employee->id)
                ->whereDate('date', $incident->activity_date)
                ->get();

            foreach ($attendances as $attendance) {
                $breaks = $attendance?->breakTimes ?? collect();

                $clockIn = $attendance?->date . ' ' . $attendance?->clock_in;
                $clockOut = $attendance?->clock_out_date . ' ' . $attendance?->clock_out;

                if ($clockIn) {
                    $in = Carbon::parse($clockIn);
                    //     if(!empty($attendance?->clock_out) &&  $attendance?->clock_out != "00:00:00"){
                    //           $out = $clockOut ? Carbon::parse($clockOut) : Carbon::now();
                    //                       $totalSeconds = $in->diffInSeconds($out);

                    //     }
                    //   else{
                    //         $totalSeconds = 0;
                    //   }

                    if (!empty($attendance?->clock_out) && $attendance?->clock_out != '00:00:00') {
                        $out = $clockOut ? Carbon::parse($clockOut) : Carbon::now();
                        $totalSeconds = $in->diffInSeconds($out);
                    } else {
                        if ($in->isToday()) {
                            $totalSeconds = $in->diffInSeconds(Carbon::now());
                        } else {
                            $totalSeconds = 0;
                        }
                    }

                    $breakSeconds = 0;
                    foreach ($breaks as $break) {
                        if ($break->break_started_at && $break->break_ended_at) {
                            $start = Carbon::parse($break->break_started_at);
                            $end = Carbon::parse($break->break_ended_at);
                            $breakSeconds += $start->diffInSeconds($end);
                        }
                    }

                    $activeSeconds = $totalSeconds - $breakSeconds;
                    $totalActiveSeconds += $activeSeconds;
                    $totalBreakSeconds += $breakSeconds;
                    $totalOnlineSeconds += $totalSeconds;
                }
            }
            $validDays++;
        }
        // dd($incidents);

        $dateInput = $request->input('date', Carbon::today()->toDateString());

        // Convert to hours for chart
        $activeHours = round($totalActiveSeconds / 3600, 2);
        $breakHours = round($totalBreakSeconds / 3600, 2);

        $averageOnlineSeconds = $validDays > 0 ? $totalOnlineSeconds / $validDays : 0;

        $totalOnlineFormatted = sprintf('%02dh %02dm', floor($totalOnlineSeconds / 3600), floor(($totalOnlineSeconds % 3600) / 60));
        $averageOnlineFormatted = sprintf('%02dh %02dm', floor($averageOnlineSeconds / 3600), floor(($averageOnlineSeconds % 3600) / 60));

        // Optional: format for display
        $formattedActive = sprintf('%02dh %02dm', floor($totalActiveSeconds / 3600), floor(($totalActiveSeconds % 3600) / 60));
        $formattedBreak = sprintf('%02dh %02dm', floor($totalBreakSeconds / 3600), floor(($totalBreakSeconds % 3600) / 60));

        return view('employee_web.activity.index', compact('topApplication', 'topUrl', 'topCategory', 'employee', 'incidents', 'dateInput', 'activeHours', 'breakHours', 'totalOnlineFormatted', 'averageOnlineFormatted'));
    }
}
