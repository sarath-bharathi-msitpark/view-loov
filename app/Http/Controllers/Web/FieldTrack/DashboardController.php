<?php

namespace App\Http\Controllers\Web\FieldTrack;

use App\Http\Controllers\Controller;
use App\Models\AttendanceEmployee;
use App\Models\Customer;
use App\Models\Employee;
use App\Models\Team;
use App\Models\Visit;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class DashboardController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $creatorId = Auth::user()->creatorId();

        $employeeQuery = Employee::where('created_by', $creatorId)
            ->where('is_active', true)
            ->whereHas('user', function ($query) {
                $query->where('track_type', USER_APK_TYPE_FIELD_TRACK)
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
            if ($dateRange && str_contains($dateRange, '-')) {
                [$startDateStr, $endDateStr] = array_map('trim', explode('-', $dateRange));
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

        $onTimeEmployees = $todayFirstPunches->filter(fn($a) => $a->late === '00:00:00')
            ->map(fn($a) => $a->employee->user->name ?? 'Unknown')
            ->values();

        $lateEmployees = $todayFirstPunches->filter(fn($a) => $a->late !== '00:00:00')
            ->map(fn($a) => $a->employee->user->name ?? 'Unknown')
            ->values();

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

        $teams = Team::where('created_by', $creatorId)->get();

        $todayCustomer = Customer::where('created_by', $creatorId)
            ->whereDate('created_at', now()->format('Y-m-d'))
            ->count();

        $totalCustomer = Customer::where('created_by', $creatorId)->count();

        $todayVisits = Visit::where('creator_id', $creatorId)
            ->whereDate('visit_date', Carbon::today())
            ->count();

        $currentMonthVisits = Visit::where('creator_id', $creatorId)
            ->whereBetween('visit_date', [Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth()])
            ->count();

        return view('field_track.dashboard', compact(
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
            'onTimeEmployees',
            'lateEmployees',
            'todayCustomer',
            'totalCustomer',
            'todayVisits',
            'currentMonthVisits',
        ));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
