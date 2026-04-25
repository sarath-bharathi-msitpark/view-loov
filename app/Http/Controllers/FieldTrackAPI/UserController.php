<?php

namespace App\Http\Controllers\FieldTrackAPI;

use App\Http\Controllers\Controller;
use App\Models\AttendanceEmployee;
use App\Models\Employee;
use App\Models\User;
use App\Models\Holiday;
use App\Models\Utility;
use App\Models\WorkPlace;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use App\Traits\ApiResponser;

class UserController extends Controller
{
    use ApiResponser;

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        if (!Auth::attempt(['email' => $credentials['email'], 'password' => $credentials['password']])) {
            return response()->json([
                'is_success' => false,
                'message' => 'Credentials do not match',
            ], 401);
        }

        $user = Auth::user();

        if (
            $user->track_type !== USER_APK_TYPE_FIELD_TRACK ||
            !$user->hasRole(ROLE_STANDARD_USER) ||
            $user->type !== 'Employee'
        ) {
            Auth::logout();
            return response()->json([
                'is_success' => false,
                'message' => 'Access denied. Only Standard Employees with Field Track are allowed to log in.',
            ], 403);
        }

        $user->last_login_at = now();
        $user->save();

        $employee = Employee::where('user_id', $user->id)->first();
        if ($employee) {
            $employee->update([
                'is_inBreak' => false,
                'is_loggedIn' => true
            ]);
        }

        return response()->json([
            'is_success' => true,
            'token' => $user->createToken('API Token')->plainTextToken,
            'user' => $user,
            'message' => 'Login successfully',
        ], 200);
    }

    /**
     * @return JsonResponse
     */
    public function logout()
    {
        auth()->user()->tokens()->delete();

        return response()->json([
            'is_success' => true,
            'message' => 'Logout successfully',
        ], 200);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function attendanceClockIn(Request $request)
    {
        $user = Auth::user();
        $employee = $user->employee;

        if (!$employee) {
            return response()->json([
                'is_success' => false,
                'message' => 'Employee not found.'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'photo_in' => 'image',
            'start_ride' => 'required',
        ]);

        $employeeId = $employee->id;

        $existingAttendance = AttendanceEmployee::where('employee_id', $employeeId)
            ->where('clock_out', '00:00:00')
            ->latest()
            ->first();

        if ($existingAttendance) {
            return response()->json([
                'is_success' => false,
                'message' => 'You have already clocked in and not yet clocked out.'
            ], 400);
        }

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

        $late = '00:00:00';
        if ($now->greaterThan($shiftStart)) {
            $late = $shiftStart->diff($now)->format('%H:%I:%S');
        }

        $imageUrls = [];
        if ($request->hasFile('photo_in')) {
            $image = $request->file('photo_in');
            $fileNameToStore = rand(1000, 9999) . date('Ymd') . '_' . time() . '.' . $image->getClientOriginalExtension();
            $dir = 'uploads/attendance_images';

            $tempRequest = new \Illuminate\Http\Request();
            $tempRequest->files->set('photo_in', $image);

            $upload = Utility::upload_file($tempRequest, 'photo_in', $fileNameToStore, $dir, []);
            if ($upload['flag'] == 1) {
                $imageUrls[] = $upload['url'];
            } else {
                return $this->error(__($upload['msg']));
            }
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

            'start_ride' => $request->input('start_ride'),
            'total_ride' => $request->input('total_ride'),
            'clock_in_images' => json_encode($imageUrls),
            // 'clock_in_latitude' => $request->input('latitude_in'),
            // 'clock_in_longitude' => $request->input('longtitude_in'),
            // 'clock_in_location' => (new AttendanceEmployee())->getAddress(
            //     $request->input('latitude_in'),
            //     $request->input('longtitude_in')
            // ),
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
                'message' => 'Employee not found.'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'photo_out' => 'image',
            'latitude_out' => 'required',
            'longitude_out' => 'required',
            'end_ride' => 'required'
        ]);

        $employeeId = $employee->id;

        $todayAttendance = AttendanceEmployee::where('employee_id', $employeeId)
            ->where('clock_out', '00:00:00')
            ->latest()
            ->first();

        if (!$todayAttendance) {
            return response()->json([
                'is_success' => false,
                'message' => 'No active attendance found or already clocked out.'
            ], 400);
        }

        $shift = $employee->shift;
        $now = now();
        $today = $now->format('Y-m-d');

        $earlyLeaving = '00:00:00';
        $overtime = '00:00:00';

        if ($shift) {
            $shiftEnd = Carbon::parse($today . ' ' . $shift->end_time);

            if ($now->lessThan($shiftEnd)) {
                $earlyLeaving = $now->diff($shiftEnd)->format('%H:%I:%S');
            }

            if ($now->greaterThan($shiftEnd)) {
                $overtime = $shiftEnd->diff($now)->format('%H:%I:%S');
            }
        }

        $imageUrls = [];
        if ($request->hasFile('photo_out')) {
            $image = $request->file('photo_out');
            $fileNameToStore = rand(1000, 9999) . date('Ymd') . '_' . time() . '.' . $image->getClientOriginalExtension();
            $dir = 'uploads/attendance_images';

            $tempRequest = new \Illuminate\Http\Request();
            $tempRequest->files->set('photo_out', $image);

            $upload = Utility::upload_file($tempRequest, 'photo_out', $fileNameToStore, $dir, []);
            if ($upload['flag'] == 1) {
                $imageUrls[] = $upload['url'];
            } else {
                return $this->error(__($upload['msg']));
            }
        }

        $endRide = $request->input('end_ride');
        $totalDistance = ($endRide && $todayAttendance->start_ride)
            ? max(0, $endRide - $todayAttendance->start_ride)
            : 0;


        $attendanceModel = new AttendanceEmployee();
        $clockOutAddress = $attendanceModel->getAddress(
            $request->input('latitude_out') ?? $user->latitude,
            $request->input('longitude_out') ?? $user->longitude
        );

        $todayAttendance->update([
            'clock_out' => $now->format('H:i:s'),
            'early_leaving' => $earlyLeaving,
            'overtime' => $overtime,
            'end_ride' => $endRide,
            'total_ride' => $totalDistance,
            'clock_out_images' => json_encode($imageUrls),
            'clock_out_latitude' => $request->input('latitude_out'),
            'clock_out_longitude' => $request->input('longitude_out'),
            'clock_out_location' => $clockOutAddress,
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
        $user = Auth::user();
        $employee = $user->employee;

        if (!$employee) {
            return response()->json([
                'is_success' => false,
                'message' => 'Employee not found.',
            ], 404);
        }

        $today = now()->format('Y-m-d');

        // fetch today's records
        $records = AttendanceEmployee::with('breakTimes')
            ->where('employee_id', $employee->id)
            ->whereDate('date', $today)
            ->orderBy('id', 'desc')
            ->get();

        if ($records->isEmpty()) {
            // if no record for today, check if there's an open record from past days
            $openRecord = AttendanceEmployee::with('breakTimes')
                ->where('employee_id', $employee->id)
                ->where('clock_out', '00:00:00')
                ->whereNull('clock_out_date')
                ->latest()
                ->first();

            if ($openRecord) {
                $currentBreak = $openRecord->breakTimes()
                    ->whereNull('break_ended_at')
                    ->latest()
                    ->with('breakType')
                    ->first();

                return response()->json([
                    'is_success' => true,
                    'message' => 'You are still punched in from ' . $openRecord->date,
                    'date' => $today,
                    'online_hrs' => null, // we don’t calculate since it's ongoing
                    'is_punchedIn' => true,
                    'first_punch_in' => Carbon::parse($openRecord->clock_in)->format('h:i A'),
                    'last_punch_in' => Carbon::parse($openRecord->clock_in)->format('h:i A'),
                    'on_break' => $currentBreak ? true : false,
                    'break_details' => $currentBreak ? [
                        'break_type' => $currentBreak->breakType->name ?? 'Unknown',
                        'started_at' => $currentBreak->break_started_at,
                    ] : null,
                ]);
            }

            return response()->json([
                'is_success' => false,
                'message' => 'No attendance data for today. You can punch in now.',
            ], 200);
        }

        // open record = still punched in (today’s record)
        $openRecord = $records
            ->where('clock_out', '00:00:00')
            ->whereNull('clock_out_date')
            ->sortByDesc('clock_in')
            ->first();

        // detect active break
        $currentBreak = null;
        if ($openRecord) {
            $currentBreak = $openRecord->breakTimes()
                ->whereNull('break_ended_at')
                ->latest()
                ->with('breakType')
                ->first();
        }

        // calculate only completed sessions (both clock in/out done)
        $onlineSeconds = $records->sum(function ($record) {
            if ($record->clock_in && $record->clock_in !== '00:00:00' &&
                $record->clock_out && $record->clock_out !== '00:00:00') {

                $clockIn = Carbon::parse($record->date . ' ' . $record->clock_in);
                $clockOut = Carbon::parse(($record->clock_out_date ?? $record->date) . ' ' . $record->clock_out);

                $duration = $clockIn->diffInSeconds($clockOut);

                // subtract only closed breaks
                $breakSeconds = $record->breakTimes->sum(function ($break) {
                    if ($break->break_ended_at) {
                        return Carbon::parse($break->break_started_at)
                            ->diffInSeconds(Carbon::parse($break->break_ended_at));
                    }
                    return 0; // ignore unfinished break
                });

                return max($duration - $breakSeconds, 0);
            }
            return 0;
        });

        $totalWorked = gmdate('H:i:s', $onlineSeconds);

        $firstPunchIn = $records->where('clock_in', '!=', '00:00:00')->sortBy('clock_in')->first();
        $lastPunchIn = $records->where('clock_in', '!=', '00:00:00')->sortByDesc('clock_in')->first();

        return response()->json([
            'is_success' => true,
            'message' => 'Today attendance fetched successfully',
            'date' => $today,
            'online_hrs' => $totalWorked,
            'is_punchedIn' => $openRecord ? true : false,
            'first_punch_in' => $firstPunchIn ? Carbon::parse($firstPunchIn->clock_in)->format('h:i A') : null,
            'last_punch_in' => $lastPunchIn ? Carbon::parse($lastPunchIn->clock_in)->format('h:i A') : '--',
            'on_break' => $currentBreak ? true : false,
            'break_details' => $currentBreak ? [
                'break_type' => $currentBreak->breakType->name ?? 'Unknown',
                'started_at' => $currentBreak->break_started_at,
            ] : null,
        ]);
    }

    public function todayAttendance()
    {
        $employeeId = !empty(\Auth::user()->employee) ? \Auth::user()->employee->id : 0;

        $todayAttendance = AttendanceEmployee::where('employee_id', '=', $employeeId)->where('clock_out', "0000-00-00 00:00:00")->orderBy('id', 'desc')->first();

        if (!empty($todayAttendance)) {
            return $this->success($todayAttendance, 'Last Attendance Data.');
        } else {

            $todayAttendance = AttendanceEmployee::where('employee_id', '=', $employeeId)->whereDate('date', date('Y-m-d'))->orderBy('id', 'desc')->first();

            if (!empty($todayAttendance)) {
                $todayAttendance->clock_in = Carbon::parse($todayAttendance->clock_in)->format('g:i A');
                $todayAttendance->punch_time = ($todayAttendance->clock_out == "0000-00-00 00:00:00") ? Carbon::parse($todayAttendance->clock_in)->format('g:i a') : Carbon::parse($todayAttendance->clock_out)->format('g:i a');
                if ($todayAttendance->clock_out != "0000-00-00 00:00:00") {
                    $todayAttendance->status = "Punchout";
                } else {
                    $todayAttendance->clock_out = "0000-00-00 00:00:00";
                }
                return $this->success($todayAttendance, 'Today Attendance Data.');
            } else {
                return $this->error('No Data, You can punch in now');
            }

        }
    }

    public function yesterdayStatus()
    {
        $employeeId = !empty(\Auth::user()->employee) ? \Auth::user()->employee->id : 0;

        $attendance = AttendanceEmployee::where('employee_id', '=', $employeeId)->whereDate('date', Carbon::yesterday()->format('Y-m-d'))->first();

        // $yesterday_order = Proposal::where('created_by', '=', \Auth::user()->creatorId())->where('user_id', '=', \Auth::user()->id)->whereDate('created_at', Carbon::yesterday()->format('Y-m-d'))->count();
        // $quoted_order = Proposal::where('created_by', '=', \Auth::user()->creatorId())->where('user_id', '=', \Auth::user()->id)->whereDate('created_at', Carbon::yesterday()->format('Y-m-d'))->where('status',0)->count();
        $yesterday_order = 0;
        $quoted_order = 0;

        if ($attendance) {
            $data = [];
            $data['date'] = Carbon::parse($attendance->date)->format('d-m-Y');
            $data['attendance_status'] = $attendance->status;
            $data['working_day'] = 'Full Day';
            $data['distance'] = $attendance->total_ride;
            $data['yesterday_order'] = $yesterday_order;
            $data['quoted_order'] = $quoted_order;
            return $this->success($data, 'Yesterday Status');
        } else {
            // return $this->sendError('No Data');
            $staticData = [
                'date' => Carbon::yesterday()->format('d-m-Y'),
                'attendance_status' => 'No Record',
                'working_day' => 'No Record',
                'distance' => 0,
                'yesterday_order' => $yesterday_order,
                'quoted_order' => $quoted_order
            ];
            return $this->error('No Data, You can punch in now', $staticData);
        }
    }

    public function attendanceCountByMonth(Request $request)
    {
        $currentDate = Carbon::now();
        $month = $request->input('month');
        $year = $request->input('year');

        $employeeId = !empty(\Auth::user()->employee) ? \Auth::user()->employee->id : 0;
        $presense = AttendanceEmployee::where('employee_id', '=', $employeeId)->where('status', 'Present')->whereMonth('date', $request->input('month'))->whereYear('date', $request->input('year'))->count();
        // $absense = AttendanceEmployee::where('employee_id', '=', $employeeId)->where('status', 'Absent')->whereMonth('date', $request->input('month'))->whereYear('date', $request->input('year'))->count();

        $presentDates = AttendanceEmployee::where('employee_id', $employeeId)
            ->where('status', 'Present')
            ->whereMonth('date', $month)
            ->whereYear('date', $year)
            ->pluck('date')
            ->map(function ($date) {
                return Carbon::parse($date)->format('Y-m-d');
            })
            ->toArray();

        $absentDays = 0;
        for ($i = 1; $i <= $currentDate->day; $i++) {
            $date = Carbon::create($year, $month, $i)->format('Y-m-d');
            if (!in_array($date, $presentDates)) {
                $isHoliday = Holiday::where('date', '<=', $date)
                    ->where('end_date', '>=', $date)
                    ->exists();
                if (!$isHoliday) {
                    $absentDays++;
                }
            }
        }

        $absense = $absentDays;

        $total_travel = AttendanceEmployee::where('employee_id', '=', $employeeId)->whereMonth('date', $request->input('month'))->whereYear('date', $request->input('year'))->sum('total_ride');

        $data = [];
        $data['presense'] = $presense;
        $data['absense'] = $absense;
        $data['total_travel'] = round($total_travel);

        return $this->success($data, 'Data of Attendance');
    }

    public function updateUserInfoFromApp(Request $request)
    {
        $post = User::select('id')->find($request->user_id);

        if (!$post) {
            return $this->error('Not Received');
        }

        $updateData = [
            'is_location' => 1,
            'latitude' => $request->latitude,
            'longitude' => $request->longtitude,
            'last_location_at' => Carbon::now(),
        ];

        if (!empty($request->battery_level) && $request->battery_level != "0") {
            $updateData['battery_level'] = $request->battery_level;
        }

        User::where('id', $post->id)->update($updateData);

        $employeeId = optional($post->employee)->id ?? 0;

        if ($employeeId) {
            $todayAttendance = AttendanceEmployee::where('employee_id', $employeeId)
                // ->whereRaw('DATE(date) = CURDATE()')
                ->whereDate('date', date('Y-m-d'))
                ->whereNull('clock_in_latitude')
                ->latest('id')
                ->first();

            if ($todayAttendance) {

                $attendanceModel = new AttendanceEmployee();
                $address = $attendanceModel->getAddress($request->latitude, $request->longtitude);
                $todayAttendance->update([
                    'clock_in_latitude' => $request->latitude,
                    'clock_in_longitude' => $request->longtitude,
                    'clock_in_location' => $address
                ]);
            }
        }

//        \Log::info('User Update:', [
//            'user_id' => $request->user_id,
//            'latitude' => $request->latitude,
//            'longitude' => $request->longtitude,
//            'battery_level' => $request->battery_level ?? null,
//            'last_location_at' => now()->toDateTimeString(),
//        ]);

        return $this->success($request->all(), 'Received');
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function myProfile()
    {
        $androidFieldTrackVersion = Utility::getValByName('field_track_loov_version');
        $androidFieldTrackVersionUrl = Utility::getValByName('field_track_loov');

        $user = Auth::user();

        if (!$user) {
            return response()->json(
                [
                    'is_success' => false,
                    'message' => 'Unauthorized',
                ],
                401,
            );
        }

        $employee = Employee::with(['role', 'team', 'designation', 'shift'])
            ->where('user_id', $user->id)
            ->first();

        if (!$employee) {
            return response()->json(
                [
                    'is_success' => false,
                    'message' => 'Employee record not found',
                ],
                404,
            );
        }

        $workplace = WorkPlace::where('user_id', $employee->created_by)->first();

        $profile = [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'mobile_no' => $user->mobile_no,
            'type' => $user->type,
            'employee_id' => $employee->employee_id,
            'gender' => $employee->gender,
            'dob' => $employee->dob,
            'phone' => $employee->phone,
            'company_doj' => $employee->company_doj,
            'team' => $employee->team,
            'role' => $employee->role,
            'designation' => $employee->designation,
            'shift' => $employee->shift,
            'is_active' => $employee->is_active == 1 ? 'Active' : 'Inactive',
            'workplace_max_hours_for_absent' => $workplace ? $workplace->workplace_max_hours_for_absent : null,
            'workplace_min_hours_for_half_day' => $workplace ? $workplace->workplace_min_hours_for_half_day : null,
            'workplace_min_hours_for_full_day' => $workplace ? $workplace->workplace_min_hours_for_full_day : null,
        ];

        return response()->json(
            [
                'is_success' => true,
                'data' => $profile,
                'Dashboard_url' => url('/'),
                'version' => [
                    'field_track_loov_version' => $androidFieldTrackVersion,
                ],
                'url' => [
                    'field_track_loov' => $androidFieldTrackVersionUrl,
                ],
                'message' => 'Profile fetched successfully',
            ],
            200,
        );
    }
}
