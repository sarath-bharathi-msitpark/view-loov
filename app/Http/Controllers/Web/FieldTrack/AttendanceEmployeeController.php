<?php

namespace App\Http\Controllers\Web\FieldTrack;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Employee;
use App\Models\AttendanceEmployee;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Str;
use App\Exports\AttendanceExport;
use Maatwebsite\Excel\Facades\Excel;

class AttendanceEmployeeController extends Controller
{
    public function index(Request $request)
    {
        $dateInput = $request->input('date', Carbon::today()->toDateString());

        try {
            if (Str::contains($dateInput, ' to ')) {
                [$startDateRaw, $endDateRaw] = explode(' to ', $dateInput);
            } elseif (Str::contains($dateInput, ' - ')) {
                [$startDateRaw, $endDateRaw] = explode(' - ', $dateInput);
            } else {
                $startDateRaw = $endDateRaw = $dateInput;
            }

            $startDate = Carbon::parse(trim($startDateRaw))->startOfDay();
            $endDate = Carbon::parse(trim($endDateRaw))->endOfDay();
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Invalid date format.');
        }
        
        if (\Auth::user()->type == 'company') {
            $employees = \Auth::user()
                ->employees()
                ->whereHas('user', function ($query) {
                    $query->where('type', 'Employee')
                          ->where('track_type', USER_APK_TYPE_FIELD_TRACK);
                })
                ->pluck('name', 'id');
        
        } elseif (\Auth::user()->type == 'Employee') {
            $filteredEmployeeIds = [\Auth::id()]; // current logged-in employee
        
            $employees = Employee::whereIn('id', $filteredEmployeeIds)
                ->where('is_active', 1)
                ->whereHas('user', function ($query) {
                    $query->where('type', 'Employee')
                          ->where('track_type', USER_APK_TYPE_FIELD_TRACK);
                })
                ->pluck('name', 'id');
        
        } else {
            $employees = Employee::where('created_by', \Auth::user()->creatorId())
                ->where('is_active', 1)
                ->whereHas('user', function ($query) {
                    $query->where('type', 'Employee')
                          ->where('track_type', USER_APK_TYPE_FIELD_TRACK);
                })
                ->pluck('name', 'id');
        }


        $employees->prepend('Select Employee', '');

        if (\Auth::user()->type == 'super admin') {
            $employee = Employee::select('id')->where('created_by', \Auth::user()->creatorId())->where('is_active',1);
        } elseif (\Auth::user()->type == 'company') {
            $employeeIds = \Auth::user()->employees->where('user.track_type', USER_APK_TYPE_FIELD_TRACK)->pluck('id');
            $employee = Employee::select('id')->whereIn('id', $employeeIds)->where('is_active',1);
        } else {
            $filteredEmployeeIds = Auth::user()->where('track_type', USER_APK_TYPE_FIELD_TRACK)->pluck('id')->toArray();

            $employee = Employee::whereIn('id', $filteredEmployeeIds)->where('is_active', 1);
        }

        if (!empty($request->employee)) {
            $employee->where('id', $request->employee);
        }

        $employee = $employee->get()->pluck('id');

        $attendanceEmployee = AttendanceEmployee::whereIn('employee_id', $employee)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->whereHas('employee.user', function ($query) {
                $query->where('type', 'Employee')->where('track_type', USER_APK_TYPE_FIELD_TRACK);
            })
            ->orderBy('created_at', 'desc');

        
        $perPage = $request->input('per_page', 10);

        $attendanceEmployee = $attendanceEmployee->latest()->paginate($perPage)->withQueryString();
        
        if ($request->filled('download') && $request->download === 'excel') {
            return $this->exportAttendanceToExcel($attendanceEmployee->items());
        }
        
        return view('field_track.attendance.index', compact('attendanceEmployee', 'employees'));
    }
    
    public function exportAttendanceToExcel($attendances)
    {
        return Excel::download(new AttendanceExport($attendances), 'attendance_report.xlsx');
    }
}
