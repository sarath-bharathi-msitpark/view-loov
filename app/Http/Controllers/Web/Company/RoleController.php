<?php

namespace App\Http\Controllers\Web\Company;

use App\Http\Controllers\Controller;
use App\Models\Role;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Permission;

class RoleController extends Controller
{
    /**
     * @param Request $request
     * @return Factory|View|Application|RedirectResponse|object
     */
    public function index(Request $request)
    {
        if (!Auth::user()->can('manage role')) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        $query = Role::where(function ($q) {
            $q->where('created_by', Auth::user()->creatorId())
                ->orWhere('created_by', 0);
        })
        
            ->whereNotIn('name', ['super admin', 'administrator', 'client', 'standard user', 'stealth user'])
            ->orderBy('id', 'asc');
        if ($request->filled('search')) {
            $term = '%' . $request->search . '%';
            $query->where(function ($q) use ($term) {
                $q->where('name', 'like', $term)
                  ->orWhere('description', 'like', $term);
            });
        }
        $perPage = $request->get('per_page', 10);

        $roles = $query->paginate($perPage);

        $roles->appends($request->except('page'));

        return view('company.settings.role.index', compact('roles'));
    }

    /**
     * @return Factory|View|Application|object
     */
    public function create()
    {
        $features = [
            'dashboard' => 'Dashboard',
            'screenshot' => 'Screenshot',
            'live_shot' => 'Live Shot',
            'live_cam_shot' => 'Live Cam Stream',
            'apps_and_urls' => 'Apps & URLs',
            'reports' => 'Reports',
            'settings' => 'Settings',
            'crm' => 'CRM',
            'project_management' => 'Project Management',
//            'task_management' => 'Task Management',
        ];

        $reports = [
            'break_report' => 'Break Report',
            'daily_attendance_report' => 'Daily Attendance',
            'activity_report' => 'Activity Report',
            'apps_and_urls_report' => 'Apps & Urls Report',
            'highlights_report' => 'Highlights Report',
        ];

        $allReports = [
            'share_all_reports' => 'Share All Reports',
        ];

        $settings = [
            'company_setting_admin' => 'Administrator',
            'company_setting_break' => 'Break',
            'company_setting_designation' => 'Designation',
            'company_setting_roles' => 'Roles',
            'company_setting_shifts' => 'Shift',
            'company_setting_teams' => 'Teams',
            'company_setting_user' => 'User',
            'company_setting_workspace' => 'Workspace',
        ];

        return view('company.settings.role.create', compact('features', 'reports', 'allReports', 'settings'));
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request)
    {
        $creatorId = Auth::user()->creatorId();

        $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('roles')->where(function ($query) use ($creatorId) {
                    return $query->where('created_by', $creatorId);
                }),
            ],
            'description' => 'required|string',
        ], [
            'name.required' => 'Name is required.',
            'name.string' => 'Name must be a valid string.',
            'name.max' => 'Name must not exceed 255 characters.',
            'name.unique' => 'This role name already exists for your organization.',
            'description.required' => 'Description is required.',
            'description.string' => 'Description must be a valid string.',
        ]);

        $role = Role::create([
            'name' => $request->name,
            'description' => $request->description,
            'created_by' => $creatorId,
            'guard_name' => 'web',
        ]);

        $permissions = array_merge(
            $request->input('features', []),
            $request->input('reports', []),
            $request->input('allReports', []),
            $request->input('settings', [])
        );

        $permissionIds = [];
        foreach ($permissions as $permName) {
            $permission = Permission::firstOrCreate(['name' => $permName, 'guard_name' => 'web']);
            $permissionIds[] = $permission->id;
        }

        $role->permissions()->sync($permissionIds);

        return response()->json([
            'message' => 'Role created successfully.',
            'redirect' => route('organization.settings.role'),
        ]);
    }

    /**
     * @param $id
     * @return Factory|View|Application|object
     */
    public function edit($id)
    {
        $role = Role::with('permissions')->findOrFail($id);

        $features = [
            'dashboard' => 'Dashboard',
            'screenshot' => 'Screenshot',
            'live_shot' => 'Live Shot',
            'live_cam_shot' => 'Live Cam Stream',
            'apps_and_urls' => 'Apps & URLs',
            'reports' => 'Reports',
            'settings' => 'Settings',
            'crm' => 'CRM',
            'project_management' => 'Project Management',
//            'task_management' => 'Task Management   ',
        ];

        $reports = [
            'break_report' => 'Break Report',
            'daily_attendance_report' => 'Daily Attendance',
            'activity_report' => 'Activity Report',
            'apps_and_urls_report' => 'Apps & Urls Report',
            'highlights_report' => 'Highlights Report',
        ];

        $allReports = [
            'share_all_reports' => 'Share All Reports',
        ];

        $settings = [
            'company_setting_admin' => 'Administrator',
            'company_setting_break' => 'Break',
            'company_setting_designation' => 'Designation',
            'company_setting_roles' => 'Roles',
            'company_setting_shifts' => 'Shift',
            'company_setting_teams' => 'Teams',
            'company_setting_user' => 'User',
            'company_setting_workspace' => 'Workspace',
        ];

        $assignedPermissions = $role->permissions->pluck('name')->toArray();

        return view('company.settings.role.edit', compact('role', 'features', 'reports', 'allReports', 'assignedPermissions', 'settings'));
    }

    /**
     * @param Request $request
     * @param $id
     * @return RedirectResponse
     */
    public function update(Request $request, $id)
    {
        $creatorId = Auth::user()->creatorId();

        $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('roles')->where(function ($query) use ($creatorId) {
                    return $query->where('created_by', $creatorId);
                })->ignore($id),
            ],
            'description' => 'required|string',
        ], [
            'name.required' => 'Name is required.',
            'name.string' => 'Name must be a valid string.',
            'name.max' => 'Name must not exceed 255 characters.',
            'name.unique' => 'This role name already exists for your organization.',
            'description.required' => 'Description is required.',
            'description.string' => 'Description must be a valid string.',
        ]);

        $role = Role::findOrFail($id);

        $role->update([
            'name' => $request->name,
            'description' => $request->description,
        ]);

        $permissions = array_merge(
            $request->input('features', []),
            $request->input('reports', []),
            $request->input('allReports', []),
            $request->input('settings', [])
        );

        $permissionIds = [];
        foreach ($permissions as $permName) {
            $permission = Permission::firstOrCreate(['name' => $permName, 'guard_name' => 'web']);
            $permissionIds[] = $permission->id;
        }

        $role->permissions()->sync($permissionIds);

        return redirect()->route('organization.settings.role')
            ->with('success', 'Role updated successfully.');
    }

    /**
     * @param $id
     * @return RedirectResponse
     */
    public function destroy($id)
    {
        if (!\Auth::user()->can('manage role')) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        $role = Role::where('id', $id)->where('created_by', \Auth::user()->creatorId())->firstOrFail();
        $role->delete();

        return redirect()->route('organization.settings.role')->with('success', __('Role deleted successfully.'));
    }
}

