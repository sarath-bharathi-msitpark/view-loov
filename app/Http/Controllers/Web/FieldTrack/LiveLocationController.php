<?php

namespace App\Http\Controllers\Web\FieldTrack;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Employee;
use App\Models\AttendanceEmployee;
use App\Models\User;
use Carbon\Carbon;

class LiveLocationController extends Controller
{
    public function index()
    {
        $user = \Auth::user();
        
        $filteredEmployeeIds = [];

        if ($user->type === 'company') {
            $filteredEmployeeIds = $user->employees->pluck('id')->toArray();
            $filteredUserIds = $user->pluck('id')->toArray();
        }
        
        $usersdata = User::where('type', 'Employee')
            ->where('is_active', 1)
            ->where('track_type', USER_APK_TYPE_FIELD_TRACK)
            ->where(function ($query) {
                $query->where('last_location_at', '<=', Carbon::now()->subMinutes(2))->orWhereNull('last_location_at');
            })
            ->when($user->type !== 'super admin', function ($query) use ($filteredUserIds) {
                $query->whereIn('id', $filteredUserIds);
            })->update(['is_location' => 0]);

        $data['employees'] = Employee::with('user')->where('is_active', 1)
            ->whereHas('user', function ($query) {
                $query->where('type', 'Employee')->where('track_type', USER_APK_TYPE_FIELD_TRACK);
            })
            ->when($user->type !== 'super admin', function ($query) use ($filteredEmployeeIds) {
                $query->whereIn('id', $filteredEmployeeIds);
            })
            ->latest()
            ->get();

        $clockInEmployees = AttendanceEmployee::whereDate('date', Carbon::today())->where('clock_out', '00:00:00')->pluck('employee_id');

        $data['clock_in_employees'] = Employee::with('user')->where('is_active',1)
            ->whereIn('id', $clockInEmployees)
            ->whereHas('user', function ($query) {
                $query->where('type', 'Employee')->where('track_type', USER_APK_TYPE_FIELD_TRACK);
            })
            ->when($user->type !== 'super admin', function ($query) use ($filteredEmployeeIds) {
                $query->whereIn('id', $filteredEmployeeIds);
            })
            ->get();

        $clockOutEmployees = AttendanceEmployee::whereDate('date', Carbon::today())->where('clock_out', '!=', '00:00:00')->pluck('employee_id');

        $data['clock_out_employees'] = Employee::with('user')->where('is_active',1)
            ->whereHas('user', function ($query) {
                $query->where('type', 'Employee')->where('track_type', USER_APK_TYPE_FIELD_TRACK);
            })
            ->whereIn('id', $clockOutEmployees)
            ->when($user->type !== 'super admin', function ($query) use ($filteredEmployeeIds) {
                $query->whereIn('id', $filteredEmployeeIds);
            })
            ->get();
            
        $data['offline_employees'] = Employee::with('user')->where('is_active', 1)
            ->whereIn('id', $clockInEmployees)
            ->whereHas('user', function ($query) {
                $query->where(function ($q) {
                    $q->where('type', 'Employee')
                      ->where('track_type', USER_APK_TYPE_FIELD_TRACK)
                      ->where(function ($inner) {
                          $inner->where('is_location', '!=', 1)
                                ->orWhere('last_location_at', '<', now()->subMinutes(2));
                      });
                });
            })
            ->when($user->type !== 'super admin', function ($query) use ($filteredEmployeeIds) {
                $query->whereIn('id', $filteredEmployeeIds);
            })
            ->get();

        return view('field_track.livelocation.index', $data);
    
    }
    
    public function getLiveLocation(Request $request)
    {
        $id = $request->input('id');

        $employee = User::find($id);
        
        $employee_data = Employee::where('user_id',$id)->first();
    
        if(!$employee_data) {
            return response()->json(['error' => 'Employee not found'], 200);
        }
        
        $today = Carbon::today();
    
        $latestAttendance = AttendanceEmployee::where('employee_id', $employee_data->id)
                            ->whereDate('date', $today->toDateString())
                            ->latest()->first();

        if (!$latestAttendance) {
            return response()->json(['error' => 'Employee has not punched in today.'], 200);
        }
    
        if ($latestAttendance->clock_out !== '00:00:00') {
            return response()->json(['error' => 'Employee has already punched out.'], 200);
        }

        if ($employee) {
            $response = [
                'latitude' => $employee->latitude,
                'longitude' => $employee->longitude,
            ];

            return response()->json($response);
        } else {
            return response()->json(['error' => 'Employee not found'], 200);
        }
    }
}
