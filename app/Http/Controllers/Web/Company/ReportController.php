<?php

namespace App\Http\Controllers\Web\Company;

use App\Exports\ActivityLogExport;
use App\Exports\ApplicationLogExport;
use App\Exports\AttendanceReportExport;
use App\Exports\BreakReportExport;
use App\Exports\IndividualAttendanceReportExport;
use App\Http\Controllers\Controller;
use App\Models\ApplicationLog;
use App\Models\AttendanceEmployee;
use App\Models\BreakTime;
use App\Models\Employee;
use App\Models\IdleTimeOut;
use App\Models\Incident;
use App\Models\Team;
use App\Models\User;
use App\Models\WorkLog;
use App\Models\WorkPlace;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use File;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ReportController extends Controller
{
    /**
     * @return Factory|View|Application|RedirectResponse|object
     */
    public function index()
    {
        $user = \Auth::user();
        if (!$user->can('break_report') && !$user->can('daily_attendance_report') && !$user->can('activity_report') && !$user->can('apps_and_urls_report') && !$user->can('highlights_report')) {
            return redirect()->back()->with('error', 'Permission denied.');
        }
        return view('company.reports.index');
    }

    /**
     * @param Request $request
     * @return Factory|View|Application|RedirectResponse|object|BinaryFileResponse
     */
    public function activityReport(Request $request)
    {
        $authUser = Auth::user();
        $hasAdminRole = $authUser->getRoleNames()->contains(ROLE_ADMINISTRATOR);

        if (!$authUser->can('activity_report')) {
            return redirect()->back()->with('error', 'Permission denied.');
        }

        $creatorId = $authUser->creatorId();

        if ($hasAdminRole || $authUser->can('share_all_reports')) {
            $teams = Team::where('created_by', $creatorId)->get();
        } else {
            $teams = Team::where('id', $authUser->employee->team_id)->get();
        }

        // Employees
        $employeeQuery = Employee::where('created_by', $creatorId)
            ->where('is_active', true)
            ->whereHas('user', function ($q) {
                $q->where('track_type', USER_APK_TYPE_SYSTEM_TRACK);
            });

        if (!($hasAdminRole || $authUser->can('share_all_reports'))) {
            $employeeQuery->where('team_id', $authUser->employee->team_id);
        }

        if ($request->filled('team_id')) {
            $employeeQuery->where('team_id', $request->team_id);
        }

        $employees = $employeeQuery->get();

        if ($request->filled('user_id')) {
            $employees = $employees->filter(fn($emp) => $emp->user_id == $request->user_id);
        }

        $employeeUserIds = $employees->pluck('user_id')->toArray();
        $users = User::whereIn('id', $employeeUserIds)->get();

        $dateInput = $request->input('date', Carbon::today()->toDateString());

        try {
            if (Str::contains($dateInput, [' to ', ' - '])) {
                $separator = Str::contains($dateInput, ' to ') ? ' to ' : ' - ';
                [$startDateRaw, $endDateRaw] = explode($separator, $dateInput);
            } else {
                $startDateRaw = $endDateRaw = $dateInput;
            }

            $startDate = Carbon::parse(trim($startDateRaw))->startOfDay();
            $endDate = Carbon::parse(trim($endDateRaw))->endOfDay();
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Invalid date format.');
        }

        $perPage = $request->get('per_page', 10);

        $incidentsQuery = Incident::with(['user', 'employee.team'])
            ->whereBetween('created_at', [$startDate, $endDate])
            ->whereIn('user_id', $employeeUserIds)
            ->select([
                'user_id',
                DB::raw('DATE(created_at) as activity_date'),
                DB::raw('SUM(keyboard_action_count) as total_keyboard_actions'),
                DB::raw('SUM(mouse_action_count) as total_mouse_actions')
            ])
            ->groupBy('user_id', DB::raw('DATE(created_at)'))
            ->havingRaw('SUM(keyboard_action_count) > 0 OR SUM(mouse_action_count) > 0')
            ->orderByDesc('activity_date');

        $incidents = $incidentsQuery->paginate($perPage)
            ->appends($request->except('page'));

        foreach ($incidents as $incident) {
            $incident->activity_percentage = $this->calculateActionPercentage($incident);
            $incident->idle_time_out_count = $this->calculateIdleTImeOutCount($incident);
        }

        if ($request->filled('download') && $request->download === 'excel') {
            return $this->exportActivityToExcel($incidents->items());
        }

        return view('company.reports.activity_report', [
            'teams' => $teams,
            'users' => $users,
            'incidents' => $incidents,
            'dateInput' => $dateInput,
        ]);
    }

    /**
     * @param $incidents
     * @return RedirectResponse|BinaryFileResponse
     */
    public function exportActivityToExcel($incidents)
    {
        $authUser = Auth::user();
        if (!$authUser->can('activity_report')) {
            return redirect()->back()->with('error', 'Permission denied.');
        }
        return Excel::download(new ActivityLogExport($incidents), 'activityLogs_report.xlsx');
    }

    /**
     * @param $incident
     * @return int
     */
    public function calculateIdleTImeOutCount($incident)
    {
        if (!$incident->employee || !$incident->employee->team) {
            return 0;
        }

        // The date from the incidents query
        $activityDate = Carbon::parse($incident->activity_date);

        return IdleTimeOut::where('user_id', $incident->employee->user_id)
            ->whereDate('start_time_and_date', $activityDate)
            ->count();
    }

    /**
     * @param $incident
     * @return float|int
     */
    public static function calculateActionPercentage($incident)
    {
        if (!$incident->employee || !$incident->employee->team) {
            return 0;
        }

        $team = $incident->employee->team;

        $totalWorkMinutes = 8 * 60;

        $screenshotFrequency = (int)$team->is_screenshot_frequency > 0 ? (int)$team->is_screenshot_frequency : 10;

        $expectedBlocks = floor($totalWorkMinutes / $screenshotFrequency);

        $keyboardAvg = $expectedBlocks * ($team->avg_keyboard_clicks_per_day ?? 0);
        $mouseAvg = $expectedBlocks * ($team->avg_mouse_clicks_per_day ?? 0);

        $keyboardAvg = $keyboardAvg > 0 ? $keyboardAvg : 1;
        $mouseAvg = $mouseAvg > 0 ? $mouseAvg : 1;

        $keyboardClicks = $incident->total_keyboard_actions ?? 0;
        $mouseClicks = $incident->total_mouse_actions ?? 0;

        $keyboardPercent = ($keyboardClicks / $keyboardAvg) * 100;
        $mousePercent = ($mouseClicks / $mouseAvg) * 100;

        $average = ($keyboardPercent + $mousePercent) / 2;

        return round(min($average, 100), 2);
    }

    /**
     * @param Request $request
     * @return Factory|View|Application|RedirectResponse|object|BinaryFileResponse
     */
    public function appsAndUrlsReport(Request $request)
    {
        $authUser = Auth::user();
        $hasAdminRole = $authUser->getRoleNames()->contains(ROLE_ADMINISTRATOR);
        $canShareAll = $authUser->can('share_all_reports');

        if (!$authUser->can('apps_and_urls_report')) {
            return redirect()->back()->with('error', 'Permission denied.');
        }

        $query = ApplicationLog::select([
            'application_name',
            'url',
            'user_id',
            DB::raw('SUM(screen_time) as total_screen_time')
        ])
            ->with('user.employee.team')
            ->whereHas('user.employee', function ($q) use ($authUser, $hasAdminRole, $canShareAll) {
                $q->where('created_by', $authUser->creatorId())
                    ->whereHas('user', function ($sub) {
                        $sub->where('track_type', USER_APK_TYPE_SYSTEM_TRACK);
                    });

                if (!($hasAdminRole || $canShareAll)) {
                    $q->where('team_id', $authUser->employee->team_id);
                }
            });

        if ($request->filled('team_id')) {
            $query->whereHas('user.employee', function ($q) use ($request) {
                $q->where('team_id', $request->team_id);
            });
        }

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->filled('month') && $request->month === 'this') {
            $query->whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year);
        } elseif ($request->filled('date')) {
            if (Str::contains($request->date, ' to ')) {
                [$start, $end] = explode(' to ', $request->date);
                $query->whereBetween('created_at', [
                    Carbon::parse(trim($start))->startOfDay(),
                    Carbon::parse(trim($end))->endOfDay()
                ]);
            } else {
                $query->whereDate('created_at', Carbon::parse($request->date));
            }
        } else {
            $query->whereDate('created_at', Carbon::today());
        }

        // Type filter (web/app)
        if ($request->filled('type')) {
            $types = (array)$request->type;
            $query->where(function ($q) use ($types) {
                if (in_array('web', $types)) {
                    $q->orWhere(function ($sub) {
                        $sub->where('application_name', 'REGEXP', '^(http|https)://')
                            ->orWhere('application_name', 'LIKE', '%.%');
                    });
                }
                if (in_array('app', $types)) {
                    $q->orWhere(function ($sub) {
                        $sub->where('application_name', 'NOT REGEXP', '^(http|https)://')
                            ->where('application_name', 'NOT LIKE', '%.%');
                    });
                }
            });
        }

        $query->groupBy('application_name', 'url', 'user_id')
            ->orderByDesc('total_screen_time');

        $applicationLogs = $query->paginate(10)->withQueryString();

        $teams = ($hasAdminRole || $canShareAll)
            ? Team::where('created_by', $authUser->creatorId())->get()
            : Team::where('id', $authUser->employee->team_id)->get();

        $employees = Employee::with('user')
            ->where('created_by', $authUser->creatorId())
            ->where('is_active', true)
            ->whereHas('user', function ($q) {
                $q->where('track_type', USER_APK_TYPE_SYSTEM_TRACK);
            })
            ->when(!($hasAdminRole || $canShareAll), function ($q) use ($authUser) {
                $q->where('team_id', $authUser->employee->team_id);
            })
            ->when($request->filled('team_id'), function ($q) use ($request) {
                $q->where('team_id', $request->team_id);
            })
            ->orderBy('name', 'asc')
            ->get();

        $users = $employees->pluck('user');

        if ($request->has('download') && $request->download === 'excel') {
            $exportLogs = $query->get();
            return $this->exportApplicationsToExcel($exportLogs);
        }

        return view('company.reports.app_and_url_report', compact('applicationLogs', 'users', 'teams'));
    }

    /**
     * @param $applicationLogs
     * @return BinaryFileResponse
     */
    public function exportApplicationsToExcel($applicationLogs)
    {
        return Excel::download(new ApplicationLogExport($applicationLogs), 'applicationLogs_report.xlsx');
    }

    /**
     * @param Request $request
     * @return Factory|View|Application|RedirectResponse|object
     */
    public function highlight(Request $request)
    {
        $user = Auth::user();
        $hasAdminRole = $user->getRoleNames()->contains(ROLE_ADMINISTRATOR);
        $canShareAll = $user->can('share_all_reports');

        if (!$user->can('highlights_report')) {
            return redirect()->back()->with('error', 'Permission denied.');
        }

        if ($hasAdminRole || $canShareAll) {
            $teams = Team::where('created_by', $user->creatorId())->get();
        } else {
            $teams = Team::where('id', $user->employee->team_id)->get();
        }

        if ($request->has('clear')) {
            return redirect()->route('organization.screenshot.index');
        }

        $employees = Employee::where('created_by', $user->creatorId())
            ->whereHas('user', function ($q) {
                $q->where('track_type', USER_APK_TYPE_SYSTEM_TRACK);
            })
            ->whereHas('user.incidents', function ($query) {
                $query->where('is_highlight', 1);
            })
            ->when(!($hasAdminRole || $canShareAll), function ($query) use ($user) {
                $query->where('team_id', $user->employee->team_id);
            })
            ->when($request->filled('team_id'), function ($query) use ($request) {
                $query->where('team_id', $request->input('team_id'));
            })
            ->orderBy('name', 'asc')
            ->get();

        $employeesCount = $employees->count();
        $selectedEmployeeId = $request->input('employee_id') ?? ($employees->first()?->id);

        $userIdByEmployeeId = Employee::find($selectedEmployeeId)?->user_id;

        $incidents = Incident::with('applicationLog', 'employee.team')
            ->where('user_id', $userIdByEmployeeId)
            ->where('is_highlight', 1)
            ->orderBy('capture_date_and_time', 'desc')
            ->get();

        foreach ($incidents as $incident) {
            $incident->action_percentage = ScreenshotController::calculateActionPercentage($incident);
        }

        return view('company.reports.highlight', compact(
            'user',
            'teams',
            'employees',
            'employeesCount',
            'incidents',
            'selectedEmployeeId'
        ));
    }

    /**
     * @param Request $request
     * @return Factory|View|Application|RedirectResponse|object|BinaryFileResponse
     */
    public function breakReport(Request $request)
    {
        $authUser = Auth::user();
        $hasAdminRole = $authUser->getRoleNames()->contains(ROLE_ADMINISTRATOR);
        $canShareAll = $authUser->can('share_all_reports');

        if (!$authUser->can('break_report')) {
            return redirect()->back()->with('error', 'Permission denied.');
        }

        $query = BreakTime::with(['employee.user', 'employee.team', 'breakType', 'createdBy'])
            ->whereHas('employee', function ($q) use ($authUser, $hasAdminRole, $canShareAll) {
                $q->where('created_by', $authUser->creatorId())
                    ->where('is_active', true)
                    ->whereHas('user', function ($sub) {
                        $sub->where('track_type', USER_APK_TYPE_SYSTEM_TRACK);
                    });

                if (!($hasAdminRole || $canShareAll)) {
                    $q->where('team_id', $authUser->employee->team_id);
                }
            })
            ->orderBy('created_at', 'desc');

        if ($request->filled('team_id')) {
            $query->whereHas('employee', function ($q) use ($request) {
                $q->where('team_id', $request->team_id);
            });
        }

        if ($request->filled('user_id')) {
            $query->whereHas('employee', function ($q) use ($request) {
                $q->where('user_id', $request->user_id);
            });
        }

        if ($request->filled('date_range')) {
            $dateRange = $request->date_range;
            try {
                if (Str::contains($dateRange, ' to ')) {
                    [$start, $end] = explode(' to ', $dateRange);
                } elseif (Str::contains($dateRange, ' - ')) {
                    [$start, $end] = explode(' - ', $dateRange);
                } else {
                    $start = $end = $dateRange;
                }

                $query->whereBetween('created_at', [
                    Carbon::parse(trim($start))->startOfDay(),
                    Carbon::parse(trim($end))->endOfDay()
                ]);
            } catch (\Exception $e) {
                Log::error('Invalid date_range parsing', [
                    'input' => $dateRange,
                    'error' => $e->getMessage(),
                ]);
                return redirect()->back()->with('error', 'Invalid date range provided.');
            }
        } else {
            $query->whereDate('created_at', Carbon::today());
        }

        $perPage = $request->get('per_page', 10);
        $breaks = $query->paginate($perPage)->withQueryString();

        $teams = ($hasAdminRole || $canShareAll)
            ? Team::where('created_by', $authUser->creatorId())->get()
            : Team::where('id', $authUser->employee->team_id)->get();

        $employeeQuery = Employee::where('created_by', $authUser->creatorId())
            ->where('is_active', true)
            ->whereHas('user', function ($q) {
                $q->where('track_type', USER_APK_TYPE_SYSTEM_TRACK);
            });

        if (!($hasAdminRole || $canShareAll)) {
            $employeeQuery->where('team_id', $authUser->employee->team_id);
        }

        if ($request->filled('team_id')) {
            $employeeQuery->where('team_id', $request->team_id);
        }

        $employeeUserIds = $employeeQuery->pluck('user_id');
        $users = User::whereIn('id', $employeeUserIds)->get();

        if ($request->has('download') && $request->download === 'excel') {
            $exportBreaks = $query->get();
            return $this->exportBreaksToExcel($exportBreaks);
        }

        return view('company.reports.break_report', compact('breaks', 'teams', 'users'));
    }

    /**
     * @param $breaks
     * @return BinaryFileResponse
     */
    public function exportBreaksToExcel($breaks)
    {
        return Excel::download(new BreakReportExport($breaks), 'break_report.xlsx');
    }

    /**
     * @param Request $request
     * @return Factory|View|Application|object
     */
    public function attendanceReport(Request $request)
    {
        $authUser = Auth::user();
        $hasAdminRole = $authUser->hasRole(ROLE_ADMINISTRATOR);
        $canShareAll = $authUser->can('share_all_reports');

        if (!$authUser->can('daily_attendance_report')) {
            return redirect()->back()->with('error', 'Permission denied.');
        }

        $month = $request->get('month', now()->format('Y-m'));
        $team_id = $request->get('team_id');
        $user_id = $request->get('user_id');

        [$year, $monthNumber] = explode('-', $month);
        $start = Carbon::createFromDate($year, $monthNumber, 1);
        $end = $start->copy()->endOfMonth();

        if ($start->isCurrentMonth()) {
            $end = now();
        }

        $period = CarbonPeriod::create($start, $end);
        $saturdayCount = 0;

        $workingDays = collect($period)->reject(function ($date) use (&$saturdayCount) {
            if ($date->isSunday()) return true;
            if ($date->isSaturday()) {
                $saturdayCount++;
                return $saturdayCount % 2 === 0;
            }
            return false;
        })->count();

        $employees = Employee::with(['team', 'shift'])
            ->where('created_by', $authUser->creatorId())
            ->where('is_active', true)
            ->whereHas('user', function ($q) {
                $q->where('track_type', USER_APK_TYPE_SYSTEM_TRACK);
            })
            ->orderBy('name')
            ->when(!($hasAdminRole || $canShareAll) && empty($team_id), function ($q) use ($authUser) {
                $teamId = optional($authUser->employee)->team_id;
                if ($teamId) {
                    $q->where('team_id', $teamId);
                }
            })
            ->when($team_id, function ($q) use ($team_id) {
                $q->where('team_id', $team_id);
            })
            ->when($user_id, function ($q) use ($user_id) {
                $q->where('id', $user_id);
            })
            ->get();

        $timezone = env('APP_TIMEZONE', 'Asia/Kolkata');

        $reports = $employees->map(function ($employee) use ($start, $end, $workingDays, $timezone) {
            $attendances = AttendanceEmployee::with('breakTimes')
                ->where('employee_id', $employee->id)
                ->whereBetween('date', [$start->toDateString(), $end->toDateString()])
                ->get();

            $grouped = $attendances->groupBy('date');
            $onlineSeconds = 0;
            $breakSeconds = 0;

            foreach ($grouped as $day => $records) {
                $onlineSeconds += $records->reduce(function ($carry, $record) use ($timezone) {
                    if ($record->clock_in && $record->clock_in !== '00:00:00' &&
                        $record->clock_out && $record->clock_out !== '00:00:00') {
                        try {
                            $clockIn = Carbon::parse($record->date . ' ' . $record->clock_in, $timezone);
                            $clockOutDate = $record->clock_out_date ?? $record->date;
                            $clockOut = Carbon::parse($clockOutDate . ' ' . $record->clock_out, $timezone);
                            if ($clockOut->lt($clockIn)) $clockOut->addDay();
                            return $carry + $clockIn->diffInSeconds($clockOut);
                        } catch (\Throwable $e) {
                            return $carry;
                        }
                    }
                    return $carry;
                }, 0);

                $breakSeconds += $records->sum(function ($record) {
                    return $record->breakTimes->sum(function ($b) {
                        try {
                            [$h, $m, $s] = explode(':', $b->duration);
                            return ((int)$h * 3600) + ((int)$m * 60) + (int)$s;
                        } catch (\Throwable $e) {
                            return 0;
                        }
                    });
                });
            }

            $overtimeSeconds = $grouped->sum(function ($records) {
                $last = $records->whereNotNull('clock_out')->where('clock_out', '!=', '00:00:00')->sortByDesc('clock_out')->first();
                if (!$last || !$last->overtime) return 0;
                try {
                    [$h, $m, $s] = explode(':', $last->overtime);
                    return ((int)$h * 3600) + ((int)$m * 60) + (int)$s;
                } catch (\Throwable $e) {
                    return 0;
                }
            });

            $presentDays = $grouped->keys()->count();

            return [
                'employee' => $employee,
                'working_days' => $workingDays,
                'present_days' => $presentDays,
                'absent_days' => max($workingDays - $presentDays, 0),
                'online_hours' => $this->formatDuration($onlineSeconds),
                'active_hours' => $this->formatDuration(max($onlineSeconds - $breakSeconds, 0)),
                'break_hours' => $this->formatDuration($breakSeconds),
                'overtime' => $this->formatDuration($overtimeSeconds),
            ];
        });

        // --- Updated Pagination ---
        $page = $request->get('page', 1);
        $perPage = $request->get('per_page', 10); // dynamic per page
        $paginatedReports = new \Illuminate\Pagination\LengthAwarePaginator(
            $reports->forPage($page, $perPage),
            $reports->count(),
            $perPage,
            $page,
            [
                'path' => url()->current(),
                'query' => $request->query(),
            ]
        );

        $teams = Team::where('created_by', $authUser->creatorId())
            ->when(!($hasAdminRole || $canShareAll), function ($q) use ($authUser) {
                $q->where('id', $authUser->employee->team_id);
            })
            ->get();

        $users = Employee::where('created_by', $authUser->creatorId())
            ->where('is_active', true)
            ->whereHas('user', function ($q) {
                $q->where('track_type', USER_APK_TYPE_SYSTEM_TRACK);
            })
            ->when(!($hasAdminRole || $canShareAll), function ($q) use ($authUser) {
                $q->where('team_id', $authUser->employee->team_id);
            })
            ->when($team_id, function ($q) use ($team_id) {
                $q->where('team_id', $team_id);
            })
            ->orderBy('name')
            ->get();

        if ($request->has('download') && $request->download === 'excel') {
            return $this->exportAttendancesToExcel($reports);
        }

        return view('company.reports.attendance.index', compact(
            'reports',
            'month',
            'team_id',
            'user_id',
            'teams',
            'users'
        ))->with('reports', $paginatedReports);
    }

    /**
     * @param Request $request
     * @return Factory|View|Application|RedirectResponse|object|BinaryFileResponse
     */
    public function todayAttendanceReport(Request $request)
    {
        $authUser = Auth::user();
        $hasAdminRole = $authUser->hasRole(ROLE_ADMINISTRATOR);
        $canShareAll = $authUser->can('share_all_reports');

        if (!$authUser->can('daily_attendance_report')) {
            return redirect()->back()->with('error', 'Permission denied.');
        }

        $today = now()->toDateString();
        $team_id = $request->get('team_id');
        $user_id = $request->get('user_id');

        $employees = Employee::with(['team', 'shift'])
            ->where('created_by', $authUser->creatorId())
            ->where('is_active', true)
            ->whereHas('user', function ($q) {
                $q->where('track_type', USER_APK_TYPE_SYSTEM_TRACK);
            })
            ->orderBy('name')
            ->when(!($hasAdminRole || $canShareAll) && empty($team_id), function ($q) use ($authUser) {
                $teamId = optional($authUser->employee)->team_id;
                if ($teamId) {
                    $q->where('team_id', $teamId);
                }
            })
            ->when($team_id, function ($q) use ($team_id) {
                $q->where('team_id', $team_id);
            })
            ->when($user_id, function ($q) use ($user_id) {
                $q->where('id', $user_id);
            })
            ->get();

        $timezone = env('APP_TIMEZONE', 'Asia/Kolkata');

        $reports = $employees->map(function ($employee) use ($today, $timezone) {
            $attendances = AttendanceEmployee::with('breakTimes')
                ->where('employee_id', $employee->id)
                ->whereDate('date', $today)
                ->get();

            $grouped = $attendances->groupBy('date');
            $onlineSeconds = 0;
            $breakSeconds = 0;

            foreach ($grouped as $records) {
                $onlineSeconds += $records->reduce(function ($carry, $record) use ($timezone) {
                    if ($record->clock_in && $record->clock_in !== '00:00:00' &&
                        $record->clock_out && $record->clock_out !== '00:00:00') {
                        try {
                            $clockIn = Carbon::parse($record->date . ' ' . $record->clock_in, $timezone);
                            $clockOutDate = $record->clock_out_date ?? $record->date;
                            $clockOut = Carbon::parse($clockOutDate . ' ' . $record->clock_out, $timezone);
                            if ($clockOut->lt($clockIn)) $clockOut->addDay();
                            return $carry + $clockIn->diffInSeconds($clockOut);
                        } catch (\Throwable $e) {
                            return $carry;
                        }
                    }
                    return $carry;
                }, 0);

                $breakSeconds += $records->sum(function ($record) {
                    return $record->breakTimes->sum(function ($b) {
                        try {
                            [$h, $m, $s] = explode(':', $b->duration);
                            return ((int)$h * 3600) + ((int)$m * 60) + (int)$s;
                        } catch (\Throwable $e) {
                            return 0;
                        }
                    });
                });
            }

            $overtimeSeconds = $grouped->sum(function ($records) {
                $last = $records->whereNotNull('clock_out')->where('clock_out', '!=', '00:00:00')->sortByDesc('clock_out')->first();
                if (!$last || !$last->overtime) return 0;
                try {
                    [$h, $m, $s] = explode(':', $last->overtime);
                    return ((int)$h * 3600) + ((int)$m * 60) + (int)$s;
                } catch (\Throwable $e) {
                    return 0;
                }
            });

            return [
                'employee' => $employee,
                'is_present' => $grouped && $grouped->isNotEmpty() ? 'true' : 'false',
                'online_hours' => $this->formatDuration($onlineSeconds),
                'active_hours' => $this->formatDuration(max($onlineSeconds - $breakSeconds, 0)),
                'break_hours' => $this->formatDuration($breakSeconds),
                'overtime' => $this->formatDuration($overtimeSeconds),
            ];
        });

        // --- Updated Pagination ---
        $page = $request->get('page', 1);
        $perPage = $request->get('per_page', 10); // dynamic per page
        $paginatedReports = new \Illuminate\Pagination\LengthAwarePaginator(
            $reports->forPage($page, $perPage),
            $reports->count(),
            $perPage,
            $page,
            [
                'path' => url()->current(),
                'query' => $request->query(),
            ]
        );

        $teams = Team::where('created_by', $authUser->creatorId())
            ->when(!($hasAdminRole || $canShareAll), function ($q) use ($authUser) {
                $q->where('id', $authUser->employee->team_id);
            })
            ->get();

        $users = Employee::where('created_by', $authUser->creatorId())
            ->where('is_active', true)
            ->whereHas('user', function ($q) {
                $q->where('track_type', USER_APK_TYPE_SYSTEM_TRACK);
            })
            ->when(!($hasAdminRole || $canShareAll), function ($q) use ($authUser) {
                $q->where('team_id', $authUser->employee->team_id);
            })
            ->when($team_id, function ($q) use ($team_id) {
                $q->where('team_id', $team_id);
            })
            ->orderBy('name')
            ->get();

        if ($request->has('download') && $request->download === 'excel') {
            return $this->exportAttendancesToExcel($reports);
        }

        return view('company.reports.attendance.today_overview', compact(
            'reports',
            'team_id',
            'user_id',
            'teams',
            'users'
        ))->with('reports', $paginatedReports);
    }

    /**
     * @param $reports
     * @return BinaryFileResponse
     */
    public function exportAttendancesToExcel($reports)
    {
        return Excel::download(new AttendanceReportExport($reports), 'attendance_report.xlsx');
    }

    /**
     * @param Request $request
     * @param $id
     * @return Factory|View|Application|RedirectResponse|object
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function attendanceReportIndividual(Request $request, $id)
    {
        $authUser = Auth::user();
        $workplace = WorkPlace::where('user_id', $authUser->creatorId())->first();

        if (!$authUser->can('daily_attendance_report')) {
            return redirect()->back()->with('error', 'Permission denied.');
        }

        $employee = Employee::with('user', 'team', 'shift', 'designation')->findOrFail($id);

        $month = $request->filled('month') ? Carbon::parse($request->month) : now();

        $attendanceQuery = AttendanceEmployee::with(['idleTimeOut', 'breakTimes'])
            ->where('created_by', $authUser->creatorId())
            ->where('employee_id', $employee->id)
            ->whereYear('created_at', $month->year)
            ->whereMonth('created_at', $month->month)
            ->orderBy('created_at', 'desc');

        $attendanceRecords = $attendanceQuery->get();

        $workLogs = WorkLog::where('employee_id', $employee->id)
            ->whereYear('date', $month->year)
            ->whereMonth('date', $month->month)
            ->get()
            ->groupBy('date');

        $grouped = $attendanceRecords->groupBy(function ($item) {
            return $item->employee_id . '-' . $item->created_at->toDateString();
        });

        $attendances = $grouped->map(function ($group) use ($workplace, $workLogs) {

            $onlineSeconds = $group->sum(function ($record) {
                if ($record->clock_in && $record->clock_in !== '00:00:00' &&
                    $record->clock_out && $record->clock_out !== '00:00:00') {
                    try {
                        $clockIn = Carbon::parse($record->created_at->toDateString() . ' ' . $record->clock_in);
                        $clockOutDate = $record->clock_out_date ?? $record->created_at->toDateString();
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

            $onlineHours = $onlineSeconds / 3600;
            $status = 'Absent';

            $maxAbsentHours = (float)$workplace->workplace_max_hours_for_absent;
            $minHalfDayHours = (float)$workplace->workplace_min_hours_for_half_day;
            $fullDayBase = (float)$workplace->workplace_min_hours_for_full_day;

            $fullDayHours = 24 - $fullDayBase;

            if ($onlineHours <= $maxAbsentHours) {
                $status = 'Absent';
            } elseif ($onlineHours > $minHalfDayHours && $onlineHours < $fullDayHours) {
                $status = 'Half Day';
            } elseif ($onlineHours >= $fullDayHours) {
                $status = 'Full Day';
            }

            $attendanceDate = $first?->created_at->toDateString();
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
                'work_logs' => $workLogForDate,
            ];
        })->values();

        $page = $request->get('page', 1);
        $perPage = $request->get('per_page', 10);
        $paginatedAttendances = new \Illuminate\Pagination\LengthAwarePaginator(
            $attendances->forPage($page, $perPage),
            $attendances->count(),
            $perPage,
            $page,
            [
                'path' => $request->url(),
                'query' => $request->query(),
            ]
        );

        if ($request->has('download') && $request->download === 'excel') {
            $fileName = Str::slug($employee->user->name . '_' . $month->format('m-Y')) . '_attendance.xlsx';
            return Excel::download(new IndividualAttendanceReportExport($attendances), $fileName);
        }

        return view('company.reports.attendance.daily_attendance_report', [
            'employee' => $employee,
            'attendances' => $paginatedAttendances,
            'selectedMonth' => $month->format('Y-m'),
        ]);
    }

    /**
     * @param $reports
     * @return BinaryFileResponse
     */
    public function exportIndividualAttendancesToExcel($reports)
    {
        return Excel::download(new IndividualAttendanceReportExport($reports), 'attendance_report.xlsx');
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
}
