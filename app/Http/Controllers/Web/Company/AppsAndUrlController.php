<?php

namespace App\Http\Controllers\Web\Company;

use App\Exports\ApplicationLogExport;
use App\Http\Controllers\Controller;
use App\Models\ApplicationLog;
use App\Models\Employee;
use App\Models\Team;
use Carbon\Carbon;
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
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class AppsAndUrlController extends Controller
{
    /**
     * @param Request $request
     * @return Factory|View|Application|object
     */
//    public function index(Request $request)
//    {
//        $user = \Auth::user();
//        if (!$user->can('apps_and_urls')) {
//            return redirect()->back()->with('error', 'Permission denied.');
//        }
//
//        $teamId = $request->input('team_id');
//        $dateRange = $request->input('date_range');
//
//        $selectedTeam = $teamId ? Team::find($teamId) : null;
//        $teams = Team::where('created_by', $user->creatorId())->get();
//
//        $query = ApplicationLog::query();
//
//        if ($teamId === 'All Team') {
//            $teamId = null;
//        }
//
//        if ($teamId) {
//            $query->whereHas('user.employee', function ($q) use ($teamId) {
//                $q->where('team_id', $teamId);
//            });
//        }
//
//        try {
//            if ($dateRange && str_contains($dateRange, 'to')) {
//                [$startDateStr, $endDateStr] = array_map('trim', explode('to', $dateRange));
//                $startDate = \Carbon\Carbon::parse($startDateStr)->startOfDay();
//                $endDate = \Carbon\Carbon::parse($endDateStr)->endOfDay();
//            } elseif ($dateRange) {
//                $startDate = \Carbon\Carbon::parse(trim($dateRange))->startOfDay();
//                $endDate = \Carbon\Carbon::parse(trim($dateRange))->endOfDay();
//            } else {
//                $startDate = \Carbon\Carbon::today()->startOfDay();
//                $endDate = \Carbon\Carbon::today()->endOfDay();
//            }
//        } catch (\Exception $e) {
//            Log::error('Invalid date_range: ' . $dateRange);
//            $startDate = \Carbon\Carbon::today()->startOfDay();
//            $endDate = \Carbon\Carbon::today()->endOfDay();
//        }
//
//        $query->whereBetween('created_at', [$startDate, $endDate]);
//
//        $appCount = (clone $query)->whereNotNull('application_name')->count();
//        $urlCount = (clone $query)->whereNotNull('url')->count();
//
//        $screenTimeData = (clone $query)->whereNotNull('application_name')
//            ->select('application_name', DB::raw('SUM(TIME_TO_SEC(screen_time)) as total_seconds'))
//            ->groupBy('application_name')
//            ->get()
//            ->map(fn($item) => [
//                'application' => $item->application_name,
//                'hours' => round($item->total_seconds / 3600, 2)
//            ]);
//
//        $urlTimeData = (clone $query)->whereNotNull('url')
//            ->select('url', DB::raw('SUM(TIME_TO_SEC(screen_time)) as total_seconds'))
//            ->groupBy('url')
//            ->get()
//            ->map(fn($item) => [
//                'url' => parse_url($item->url, PHP_URL_HOST) ?? $item->url,
//                'hours' => round($item->total_seconds / 3600, 2)
//            ]);
//
//        $topApplication = (clone $query)->whereNotNull('application_name')
//            ->select('application_name', DB::raw('SUM(TIME_TO_SEC(screen_time)) as total_seconds'))
//            ->groupBy('application_name')
//            ->orderByDesc('total_seconds')
//            ->first();
//
//        $topUrl = (clone $query)->whereNotNull('url')
//            ->select('url', DB::raw('SUM(TIME_TO_SEC(screen_time)) as total_seconds'))
//            ->groupBy('url')
//            ->orderByDesc('total_seconds')
//            ->first();
//
//        $topCategory = (clone $query)->whereNotNull('url')
//            ->select(DB::raw('SUBSTRING_INDEX(url, "/", 3) as category'), DB::raw('SUM(TIME_TO_SEC(screen_time)) as total_seconds'))
//            ->groupBy(DB::raw('SUBSTRING_INDEX(url, "/", 3)'))
//            ->orderByDesc('total_seconds')
//            ->first();
//
//        return view('company.apps_and_urls.index', compact(
//            'appCount',
//            'urlCount',
//            'screenTimeData',
//            'urlTimeData',
//            'topApplication',
//            'topUrl',
//            'topCategory',
//            'teams',
//            'selectedTeam'
//        ));
//    }

    /**
     * @param Request $request
     * @return Factory|View|Application|RedirectResponse|object|BinaryFileResponse
     */
    public function index(Request $request)
    {
        $authUser = Auth::user();
        $hasAdminRole = $authUser->getRoleNames()->contains(ROLE_ADMINISTRATOR);
        $canShareAll = $authUser->can('share_all_reports');

        if (!$authUser->can('apps_and_urls_report')) {
            return redirect()->back()->with('error', 'Permission denied.');
        }

        $perPage = $request->get('per_page', 10);

        $query = ApplicationLog::select([
            'application_name',
            'url',
            'user_id',
            'created_at',
            DB::raw('SUM(screen_time) as total_screen_time')
        ])
            ->with('user.employee.team')
            ->whereHas('user.employee', function ($q) use ($authUser) {
                $q->where('created_by', $authUser->creatorId());
            });

        $query->when(!($hasAdminRole || $canShareAll) || $request->filled('team_id'), function ($q) use ($hasAdminRole, $canShareAll, $authUser, $request) {
            $q->whereHas('user.employee', function ($sub) use ($hasAdminRole, $canShareAll, $authUser, $request) {
                if (!($hasAdminRole || $canShareAll)) {
                    $sub->where('team_id', $authUser->employee->team_id);
                }
                if ($request->filled('team_id')) {
                    $sub->where('team_id', $request->team_id);
                }
            });
        });

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        // Handle date filter
        if ($request->filled('month') && $request->month === 'this') {
            $query->whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year);
        } elseif ($request->filled('date_range')) {
            $range = $request->date_range;

            if (Str::contains($range, [' to ', ' - '])) {
                $separator = Str::contains($range, ' to ') ? ' to ' : ' - ';
                [$start, $end] = explode($separator, $range);
                $query->whereBetween('created_at', [
                    Carbon::parse(trim($start))->startOfDay(),
                    Carbon::parse(trim($end))->endOfDay()
                ]);
            } else {
                $query->whereDate('created_at', Carbon::parse($range));
            }
        } else {
            $query->whereDate('created_at', Carbon::today());
        }

        // Type filter (web/app)
        if ($request->filled('type')) {
            $types = (array)$request->type;
            $query->where(function ($q) use ($types) {
                if (in_array('web', $types)) {
                    $q->orWhere('is_browser', true);
                }
                if (in_array('app', $types)) {
                    $q->orWhere('is_browser', false);
                }
            });
        }

        $query->groupBy('application_name', 'url', 'user_id')
            ->orderByDesc('total_screen_time');

        $applicationLogs = $query->paginate($perPage)
            ->appends($request->except('page'));

        if ($hasAdminRole || $canShareAll) {
            $teams = Team::where('created_by', $authUser->creatorId())
                ->where('is_tracking', true)
                ->get();
        } else {
            $teams = Team::where('id', $authUser->employee->team_id)
                ->where('is_tracking', true)
                ->get();
        }

        $validTeamIds = $teams->pluck('id');

        $employees = Employee::with('user')
            ->where('created_by', $authUser->creatorId())
            ->where('is_active', true)
            ->whereIn('team_id', $validTeamIds)
            ->whereHas('user', function ($q) {
                $q->where('track_type', USER_APK_TYPE_SYSTEM_TRACK)
                    ->whereHas('roles', function ($roleQuery) {
                        $roleQuery->whereIn('name', ['standard user', 'stealth user']);
                    });
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

        return view('company.apps_and_urls.new_index', compact('applicationLogs', 'users', 'teams'));
    }

    /**
     * @param $applicationLogs
     * @return BinaryFileResponse
     */
    public function exportApplicationsToExcel($applicationLogs)
    {
        return Excel::download(new ApplicationLogExport($applicationLogs), 'applicationLogs_report.xlsx');
    }
}
