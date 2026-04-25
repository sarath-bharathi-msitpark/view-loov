<?php

namespace App\Http\Controllers\Web\Company;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Models\Team;
use App\Models\Shift;
use App\Models\Employee;
use App\Models\Role;
use App\Models\Designation;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class TeamController extends Controller
{
    /**
     * @param Request $request
     * @return Factory|View|Application|object
     */
    public function index(Request $request)
    {
        $creatorId = Auth::user()->creatorId();

        $shifts = Shift::where('created_by', $creatorId)->get();

        $roles = Role::whereNotIn('name', ['super admin', 'administrator', 'client', 'standard user', 'stealth user'])
            ->where('created_by', Auth::user()->creatorId())
            ->get();

        $designations = Designation::where('created_by', $creatorId)->get();

        $teamsQuery = Team::where('created_by', $creatorId);

        if ($request->has('search') && !empty($request->search)) {
            $teamsQuery->where('name', 'like', '%' . $request->search . '%');
        }

        $teams = $teamsQuery->orderBy('id', 'desc')->get();

        $employeesQuery = Employee::where('created_by', $creatorId)
            ->where('is_active', 1)
            ->with('designation');

        if ($request->has('team_id') && !empty($request->team_id)) {
            $employeesQuery->where('team_id', $request->team_id);
        }

        if ($request->has('user_search') && !empty($request->user_search)) {
            $search = $request->user_search;

            $employeesQuery->where(function ($query) use ($search) {
                $query->where('name', 'like', "%$search%")
                    ->orWhere('email', 'like', "%$search%")
                    ->orWhereHas('designation', function ($q) use ($search) {
                        $q->where('name', 'like', "%$search%");
                    });
            });
        }

        $users = $employeesQuery->orderBy('id', 'desc')->paginate(5)->withQueryString();

        $selectedTeam = null;

        if ($request->has('team_id')) {
            $selectedTeam = Team::find($request->input('team_id'));
        }

        $activeUserCount = User::whereHas('roles', function ($query) {
            $query->whereIn('name', ['stealth user', 'standard user']);
        })
            ->join('employees', 'users.id', '=', 'employees.user_id')
            ->where('users.created_by', Auth::user()->creatorId())
            ->where('employees.is_active', 1)
            ->count();

        return view('company.settings.team', compact('teams', 'shifts', 'selectedTeam', 'users', 'roles', 'designations', 'activeUserCount'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'average_keyboard_clicks' => 'required|integer',
            'average_mouse_clicks' => 'required|integer',
            'excessive_keyboard_typing' => 'required|integer',
            'excessive_mouse_clicking' => 'required|integer',
        ], [
            'name.required' => 'Name is required.',
            'name.string' => 'Name must be a valid string.',
            'name.max' => 'Name must not exceed 255 characters.',

            'description.required' => 'Description is required.',
            'description.string' => 'Description must be a valid string.',

            'average_keyboard_clicks.required' => 'Average keyboard clicks value is required.',
            'average_keyboard_clicks.integer' => 'Average keyboard clicks must be an integer.',

            'average_mouse_clicks.required' => 'Average mouse clicks value is required.',
            'average_mouse_clicks.integer' => 'Average mouse clicks must be an integer.',

            'excessive_keyboard_typing.required' => 'Excessive keyboard typing value is required.',
            'excessive_keyboard_typing.integer' => 'Excessive keyboard typing must be an integer.',

            'excessive_mouse_clicking.required' => 'Excessive mouse clicking value is required.',
            'excessive_mouse_clicking.integer' => 'Excessive mouse clicking must be an integer.',
        ]);

        Team::create([
            'name' => $request->name,
            'description' => $request->description,
            'avg_keyboard_clicks_per_day' => $request->average_keyboard_clicks,
            'avg_mouse_clicks_per_day' => $request->average_mouse_clicks,
            'excessive_keyboard_typing_per_day' => $request->excessive_keyboard_typing,
            'excessive_mouse_clicking_per_day' => $request->excessive_mouse_clicking,
            'created_by' => Auth::user()->creatorId(),
        ]);

        return redirect()->route('organization.settings.team')->with('success', 'Team created successfully.');
    }

    /**
     * @param $id
     * @return Factory|View|Application|object
     */
    public function showTeamSettings($id)
    {
        $team = Team::findOrFail($id);
        $shifts = Shift::all();

        return view('company.settings.team', compact('team', 'shifts'));
    }

    /**
     * @param Request $request
     * @param Team $team
     * @return JsonResponse
     */
    public function updateShift(Request $request, Team $team)
    {
        $request->validate([
            'shift_id' => 'required|exists:shifts,id',
        ]);

        $team->shift_id = $request->shift_id;
        $team->save();

        return response()->json([
            'success' => true,
            'message' => 'Shift updated successfully!',
        ]);
    }

    /**
     * @param Request $request
     * @param Team $team
     * @return JsonResponse
     */
    public function updatePolicy(Request $request, Team $team)
    {
        $request->validate([
            'application_policy' => 'required',
        ]);

        $team->application_policy = $request->application_policy;
        $team->save();

        return response()->json([
            'success' => true,
            'message' => 'Application policy updated successfully!',
        ]);
    }

    /**
     * @param Request $request
     * @param $id
     * @return RedirectResponse
     */
    public function teamtrackUpdate(Request $request, $id)
    {
        $team = Team::findOrFail($id);

        $validatedData = $request->validate([
            'name' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'is_tracking' => 'nullable',
            'is_livestream' => 'nullable',
            'is_capturescreenshot' => 'nullable',
            'is_screenshot_frequency' => 'nullable',
            'is_app_url' => 'nullable',
            'is_keyboard_mouse' => 'nullable|string',
            'idle_timeout_popup_reminder_in_minutes' => 'nullable|integer',
            'auto_punch_out_threshold' => 'nullable|string',
            'is_portal_access' => 'nullable',
        ]);

        $updateData = [
            'is_tracking' => $request->has('is_tracking') ? 1 : 0,
            'is_livestream' => $request->has('is_livestream') ? 1 : 0,
            'is_capturescreenshot' => $request->has('is_capturescreenshot') ? 1 : 0,
            'is_screenshot_frequency' => $request->input('is_screenshot_frequency'),
            'is_app_url' => $request->has('is_app_url') ? 1 : 0,
            'is_keyboard_mouse' => $request->input('is_keyboard_mouse'),
            'idle_timeout_popup_reminder_in_minutes' => $validatedData['idle_timeout_popup_reminder_in_minutes'] ?? null,
            'auto_punch_out_threshold' => $validatedData['auto_punch_out_threshold'] ?? null,
            'is_portal_access' => $request->has('is_portal_access') ? 1 : 0,
        ];

        $team->update($updateData);

        return redirect()->route('organization.settings.team', ['team_id' => $team->id])
            ->with('success', 'Team settings updated successfully!');
    }

    /**
     * @param Request $request
     * @param $id
     * @return RedirectResponse
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'average_keyboard_clicks' => 'required|integer',
            'average_mouse_clicks' => 'required|integer',
            'excessive_keyboard_typing' => 'required|integer',
            'excessive_mouse_clicking' => 'required|integer',
        ], [
            'name.required' => 'Name is required.',
            'name.string' => 'Name must be a valid string.',
            'name.max' => 'Name must not exceed 255 characters.',

            'description.required' => 'Description is required.',
            'description.string' => 'Description must be a valid string.',

            'average_keyboard_clicks.required' => 'Average keyboard clicks value is required.',
            'average_keyboard_clicks.integer' => 'Average keyboard clicks must be an integer.',

            'average_mouse_clicks.required' => 'Average mouse clicks value is required.',
            'average_mouse_clicks.integer' => 'Average mouse clicks must be an integer.',

            'excessive_keyboard_typing.required' => 'Excessive keyboard typing value is required.',
            'excessive_keyboard_typing.integer' => 'Excessive keyboard typing must be an integer.',

            'excessive_mouse_clicking.required' => 'Excessive mouse clicking value is required.',
            'excessive_mouse_clicking.integer' => 'Excessive mouse clicking must be an integer.',
        ]);

        $team = Team::findOrFail($id);

        $updated = $team->update([
            'name' => $request->name,
            'description' => $request->description,
            'avg_keyboard_clicks_per_day' => $request->average_keyboard_clicks,
            'avg_mouse_clicks_per_day' => $request->average_mouse_clicks,
            'excessive_keyboard_typing_per_day' => $request->excessive_keyboard_typing,
            'excessive_mouse_clicking_per_day' => $request->excessive_mouse_clicking,
        ]);

        if (!$updated) {
            return back()->with('error', 'Failed to update team.');
        }

        return back()->with('success', 'Team updated successfully.');
    }

    /**
     * @param $id
     * @return RedirectResponse
     */
    public function destroy($id)
    {
        $team = Team::findOrFail($id);

        $hasActiveEmployees = Employee::where('team_id', $id)
            ->where('is_active', 1)
            ->exists();

        if ($hasActiveEmployees) {
            return back()->with('error', 'Cannot delete team. Active employees are assigned to this team.');
        }

        $team->delete();
        return back()->with('success', 'Team deleted successfully.');
    }
}
