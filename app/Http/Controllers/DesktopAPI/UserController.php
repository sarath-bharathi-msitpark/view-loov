<?php

namespace App\Http\Controllers\DesktopAPI;

use App\Http\Controllers\Controller;
use App\Models\Utility;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Employee;
use App\Models\WorkPlace;
use Illuminate\Support\Facades\Log;

class UserController extends Controller
{
    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function login(Request $request)
    {
        // Version
        $windowStealthUserVersion = Utility::getValByName('window_stel_user_version');
        $windowStandUserVersion = Utility::getValByName('window_stand_user_version');
        $macStealthUserVersion = Utility::getValByName('mac_stel_user_version');
        $macStandUserVersion = Utility::getValByName('mac_stand_user_version');

        // Latest Build URL
        $windowStealthUserVersionUrl = Utility::getValByName('window_stel_user');
        $windowStandUserVersionUrl = Utility::getValByName('window_stand_user');
        $macStealthUserVersionUrl = Utility::getValByName('mac_stel_user');
        $macStandUserVersionUrl = Utility::getValByName('mac_stand_user');

        $credentials = $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
            'time_zone' => 'required|string',
        ]);

        if (!Auth::attempt(['email' => $credentials['email'], 'password' => $credentials['password']])) {
            return response()->json([
                'message' => 'Credentials do not match',
            ], 401);
        }

        $user = Auth::user();

        if (
            $user->track_type !== USER_APK_TYPE_SYSTEM_TRACK ||
            $user->type !== 'Employee'
        ) {
            Auth::logout();
            return response()->json([
                'is_success' => false,
                'message' => 'Access denied. Only Standard Employees with System Track are allowed to log in.',
            ], 403);
        }

        $user->last_time_zone = $credentials['time_zone'];
        $user->last_login_at = now();
        $user->save();

        $employee = Employee::where('user_id', $user->id)->first();
        $employee->update([
            'is_inBreak' => false,
            'is_loggedIn' => true
        ]);

        return response()->json([
            'is_success' => true,
            'token' => $user->createToken('API Token')->plainTextToken,
            'user' => $user,
            'version' => [
                'windows_stealth_user_version' => $windowStealthUserVersion,
                'window_stand_user_version' => $windowStandUserVersion,
                'mac_stealth_user_version' => $macStealthUserVersion,
                'mac_stand_user_version' => $macStandUserVersion
            ],
            'url' => [
                'windows_stealth_user_version' => $windowStealthUserVersionUrl,
                'window_stand_user_version' => $windowStandUserVersionUrl,
                'mac_stealth_user_version' => $macStealthUserVersionUrl,
                'mac_stand_user_version' => $macStandUserVersionUrl
            ],
            'message' => 'Login successfully',
        ], 200);
    }

    /**
     * @return JsonResponse
     */
    public function logout()
    {
        $user = Auth::user();

        $employee = Employee::where('user_id', $user->id)->first();

        if ($employee) {
            $employee->update([
                'is_inBreak' => false,
                'is_loggedIn' => false,
            ]);
        }

        $user->tokens()->delete();

        return response()->json([
            'is_success' => true,
            'message' => 'Tokens revoked successfully.',
        ], 200);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function myProfile(Request $request)
    {
        // Version
        $windowStealthUserVersion = Utility::getValByName('window_stel_user_version');
        $windowStandUserVersion = Utility::getValByName('window_stand_user_version');
        $macStealthUserVersion = Utility::getValByName('mac_stel_user_version');
        $macStandUserVersion = Utility::getValByName('mac_stand_user_version');

        // Latest Build URL
        $windowStealthUserVersionUrl = Utility::getValByName('window_stel_user');
        $windowStandUserVersionUrl = Utility::getValByName('window_stand_user');
        $macStealthUserVersionUrl = Utility::getValByName('mac_stel_user');
        $macStandUserVersionUrl = Utility::getValByName('mac_stand_user');

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
                'pusher' => [
                    'pusher_channel_id' => config('broadcasting.broadcast_channel_prefix') . '-' . $user->id,
                    'pusher_app_id' => env('PUSHER_APP_ID'),
                    'pusher_app_key' => env('PUSHER_APP_KEY'),
                    'pusher_app_secret' => env('PUSHER_APP_SECRET'),
                ],
                'Dashboard_url' => url('/'),
                'version' => [
                    'windows_stealth_user_version' => $windowStealthUserVersion,
                    'window_stand_user_version' => $windowStandUserVersion,
                    'mac_stealth_user_version' => $macStealthUserVersion,
                    'mac_stand_user_version' => $macStandUserVersion
                ],
                'url' => [
                    'windows_stealth_user_version' => $windowStealthUserVersionUrl,
                    'window_stand_user_version' => $windowStandUserVersionUrl,
                    'mac_stealth_user_version' => $macStealthUserVersionUrl,
                    'mac_stand_user_version' => $macStandUserVersionUrl
                ],
                'message' => 'Profile fetched successfully',
            ],
            200,
        );
    }
}
