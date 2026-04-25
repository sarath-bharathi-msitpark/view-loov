<?php

namespace App\Http\Controllers\Web\Company;

use App\Events\CaptureLiveScreenshot;
use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\Team;
use App\Models\Incident;
use App\Models\User;
use App\Models\Utility;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class LivestreamController extends Controller
{
    /**
     * @param Request $request
     * @return Factory|View|Application|object
     */
    public function screenshotIndex(Request $request)
    {
        $user = Auth::user();
        $hasAdminRole = $user->getRoleNames()->contains(ROLE_ADMINISTRATOR);

        if (!$user->can('live_shot')) {
            return redirect()->back()->with('error', 'Permission denied.');
        }

        if ($hasAdminRole || $user->can('share_all_reports')) {
            $teams = Team::where('created_by', $user->creatorId())
                ->where('is_livestream', true)
                ->get();
        } else {
            $teams = Team::where('id', $user->employee->team_id)
                ->where('is_livestream', true)
                ->get();
        }

        $validTeamIds = $teams->pluck('id');

        $perPage = $request->get('per_page', 10);

        $employeesQuery = Employee::where('is_loggedIn', true)
            ->where('is_inBreak', false)
            ->where('is_active', true)
            ->where('is_punchedIn', true)
            ->where('created_by', $user->creatorId())
            ->whereIn('team_id', $validTeamIds)
            ->whereHas('user', function ($q) {
                $q->where('track_type', USER_APK_TYPE_SYSTEM_TRACK);
            })
            ->when(!($hasAdminRole || $user->can('share_all_reports')), function ($q) use ($user) {
                $q->where('team_id', $user->employee->team_id);
            })
            ->when($request->filled('team_id'), function ($q) use ($request) {
                $q->where('team_id', $request->team_id);
            })
            ->when(
                $request->filled('employee_id') && $request->employee_id !== 'All Employee',
                function ($q) use ($request) {
                    $q->where('id', $request->employee_id);
                }
            );

        $employees = $employeesQuery->orderBy('id', 'desc')
            ->paginate($perPage)
            ->appends($request->except('page'));

        return view('company.live_screen_shot.index', compact('employees', 'teams'));
    }

    /**
     * @param Request $request
     * @return Factory|View|Application|object
     */
    public function webCamShotIndex(Request $request)
    {
        $user = Auth::user();
        $hasAdminRole = $user->getRoleNames()->contains(ROLE_ADMINISTRATOR);

        if (!$user->can('live_shot')) {
            return redirect()->back()->with('error', 'Permission denied.');
        }

        if ($hasAdminRole) {
            $teams = Team::where('created_by', $user->creatorId())
                ->where('is_livestream', true)
                ->get();
        } else {
            $teams = Team::where('id', $user->employee->team_id)
                ->where('is_livestream', true)
                ->get();
        }

        $validTeamIds = $teams->pluck('id');

        $perPage = $request->get('per_page', 10);

        $employeesQuery = Employee::where('is_loggedIn', true)
            ->where('is_inBreak', false)
            ->where('is_active', true)
            ->where('is_punchedIn', true)
            ->where('created_by', $user->creatorId())
            ->whereIn('team_id', $validTeamIds)
            ->whereHas('user', function ($q) {
                $q->where('track_type', USER_APK_TYPE_SYSTEM_TRACK);
            })
            ->when(!$hasAdminRole, function ($q) use ($user) {
                $q->where('team_id', $user->employee->team_id);
            })
            ->when($request->filled('team_id'), function ($q) use ($request) {
                $q->where('team_id', $request->team_id);
            })
            ->when(
                $request->filled('employee_id') && $request->employee_id !== 'All Employee',
                function ($q) use ($request) {
                    $q->where('id', $request->employee_id);
                }
            );

        $employees = $employeesQuery->orderBy('id', 'desc')
            ->paginate($perPage)
            ->appends($request->except('page'));

        return view('company.live_screen_shot.web_shot', compact('employees', 'teams'));
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function requestImage(Request $request)
    {
        $employeeId = $request->get('employee_id');
        $isWebCam = $request->get('is_web_cam', false);

        $employee = Employee::find($employeeId);

        if (!$employee) {
            return response()->json(['status' => 'error', 'message' => 'Employee not found']);
        }

        $incident = Incident::create([
            'user_id' => $employee->user_id,
            'requested_by' => Auth::id(),
            'requested_date_and_time' => now(),
            'is_web_cam' => $isWebCam
        ]);

        event(new CaptureLiveScreenshot($employee->user_id, $incident));

        return response()->json([
            'status' => 'success',
            'incident_id' => $incident->id,
            'message' => 'Screenshot request sent. Poll for screenshot with incident_id.',
        ]);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function checkScreenshotStatus(Request $request)
    {
        $incidentId = $request->get('incident_id');
        $incident = Incident::find($incidentId);

        if (!$incident) {
            return response()->json(['status' => 'error', 'message' => 'Incident not found']);
        }

//        Log::info($incident->screenshot);

        if (!empty($incident->screenshot)) {
            return response()->json([
                'status' => 'success',
                'image_url' => Utility::get_file($incident->screenshot),
                'employee' => [
                    'id' => $incident->employee->id ?? null,
                    'name' => $incident->employee->name ?? null,
                    'employee_id' => $incident->employee->employee_id ?? null,
                    'designation' => $incident->employee->designation->name ?? null,
                ],
            ]);
        }

        return response()->json(['status' => 'pending']);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request)
    {
        $incidentId = $request->input('incident_id');

        $incident = Incident::find($incidentId);

        if (!$incident) {
            return response()->json([
                'is_success' => false,
                'message' => 'Incident not found'
            ], 404);
        }

        $user = User::findOrFail($incident->user_id);

        if (!$user) {
            return response()->json([
                'is_success' => false,
                'message' => 'User not found'
            ], 404);
        }

        $createdBy = User::where('id', $user->created_by)->first();
        $companySlug = Str::slug($createdBy->company_name);
        $nameSlug = Str::slug($user->name);

        $screenshotUrl = null;
        if ($request->hasFile('screenshot')) {
            $filenameWithExt = $request->file('screenshot')->getClientOriginalName();
            $filename = pathinfo($filenameWithExt, PATHINFO_FILENAME);
            $extension = $request->file('screenshot')->getClientOriginalExtension();
            $fileNameToStore = $filename . '_' . time() . '.' . $extension;

            $dir = "uploads/companies/{$companySlug}/live-event/{$nameSlug}/screenshots";

            $image_path = public_path($dir . '/' . $filenameWithExt);
            if (\File::exists($image_path)) {
                \File::delete($image_path);
            }

            $path = Utility::upload_file($request, 'screenshot', $fileNameToStore, $dir, []);
            if ($path['flag'] == 1) {
                $screenshotUrl = $path['url'];
            } else {
                return response()->json([
                    'is_success' => false,
                    'message' => $path['msg']
                ], 400);
            }
        }

        if ($screenshotUrl) {
            $incident->screenshot = $screenshotUrl;
            $incident->save();
        }

        return response()->json([
            'is_success' => true,
            'message' => 'Screenshot uploaded and incident updated successfully',
        ], 200);
    }
}
