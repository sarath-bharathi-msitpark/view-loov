<?php

namespace App\Http\Controllers\AdminAPI;

use App\Http\Controllers\Controller;
use App\Models\ApplicationLog;
use App\Models\Employee;
use App\Models\Team;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AppsAndUrlController extends Controller
{
    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function team(Request $request)
    {
        $teams = Team::where('created_by', Auth::user()->creatorId())->get(['id', 'name']);

        return response()->json([
            'is_success' => true,
            'message' => 'Teams fetched successfully',
            'data' => $teams
        ], 200);
    }

    /**
     * @param string $id
     * @return JsonResponse
     */
    public function EmployeeByTeam(Request $request)
    {
        $employees = Employee::where('created_by', Auth::user()->creatorId())
            ->where('team_id', $request->team_id)
            ->get();

        return response()->json([
            'is_success' => true,
            'message' => 'Teams Members fetched successfully',
            'data' => $employees
        ], 200);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function appAndUrlUsage(Request $request)
    {
        $request->validate([
            'team_id' => 'nullable|integer|exists:teams,id',
        ]);

        $teamId = $request->input('team_id');

        $query = ApplicationLog::query();
        $query->whereDate('created_at', Carbon::today());

        if ($teamId) {
            $query->whereHas('user.employee', function ($q) use ($teamId) {
                $q->where('team_id', $teamId);
            });
        }

        $appCount = (clone $query)->whereNotNull('application_name')->count();
        $urlCount = (clone $query)->whereNotNull('url')->count();

        $screenTimeData = (clone $query)->whereNotNull('application_name')
            ->select('application_name', DB::raw('SUM(TIME_TO_SEC(screen_time)) as total_seconds'))
            ->groupBy('application_name')
            ->get()
            ->map(fn($item) => [
                'application' => $item->application_name,
                'hours' => round($item->total_seconds / 3600, 2)
            ]);

        $urlTimeData = (clone $query)->whereNotNull('url')
            ->select('url', DB::raw('SUM(TIME_TO_SEC(screen_time)) as total_seconds'))
            ->groupBy('url')
            ->get()
            ->map(fn($item) => [
                'url' => parse_url($item->url, PHP_URL_HOST) ?? $item->url,
                'hours' => round($item->total_seconds / 3600, 2)
            ]);

        $topApplicationRaw = (clone $query)->whereNotNull('application_name')
            ->select('application_name', DB::raw('SUM(TIME_TO_SEC(screen_time)) as total_seconds'))
            ->groupBy('application_name')
            ->orderByDesc('total_seconds')
            ->first();

        $topApplication = $topApplicationRaw ? [
            'application' => $topApplicationRaw->application_name,
            'formatted_time' => gmdate('H\h:i\m', $topApplicationRaw->total_seconds),
        ] : null;

        $topUrlRaw = (clone $query)->whereNotNull('url')
            ->select('url', DB::raw('SUM(TIME_TO_SEC(screen_time)) as total_seconds'))
            ->groupBy('url')
            ->orderByDesc('total_seconds')
            ->first();

        $topUrl = $topUrlRaw ? [
            'url' => parse_url($topUrlRaw->url, PHP_URL_HOST) ?? $topUrlRaw->url,
            'formatted_time' => gmdate('H\h:i\m', $topUrlRaw->total_seconds),
        ] : null;

        $topCategoryRaw = (clone $query)->whereNotNull('url')
            ->select(DB::raw('SUBSTRING_INDEX(url, "/", 3) as category'), DB::raw('SUM(TIME_TO_SEC(screen_time)) as total_seconds'))
            ->groupBy(DB::raw('SUBSTRING_INDEX(url, "/", 3)'))
            ->orderByDesc('total_seconds')
            ->first();

        $topCategory = $topCategoryRaw ? [
            'category' => $topCategoryRaw->category,
            'formatted_time' => gmdate('H\h:i\m', $topCategoryRaw->total_seconds),
        ] : null;

        $totalCount = $appCount + $urlCount;

        return response()->json([
            'is_success' => true,
            'message' => 'App and URL stats retrieved successfully',
            'data' => [
                'app_count' => $appCount,
                'url_count' => $urlCount,
                'total_count' => $totalCount,
                'screen_time_data' => $screenTimeData,
                'url_time_data' => $urlTimeData,
                'top_application' => $topApplication,
                'top_url' => $topUrl,
                'top_category' => $topCategory,
            ],
        ], 200);
    }
}
