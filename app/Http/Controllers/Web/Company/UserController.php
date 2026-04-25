<?php

namespace App\Http\Controllers\Web\Company;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\User;
use App\Models\Role;
use App\Models\Shift;
use App\Models\Team;
use App\Models\Designation;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Lab404\Impersonate\Impersonate;
use Maatwebsite\Excel\Facades\Excel;
use File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use App\Exports\UsersExport;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class UserController extends Controller
{
    /**
     * @param Request $request
     * @return Factory|View|Application|object
     * @return Factory|View|Application|object
     */
    public function index(Request $request)
    {
        $shifts = Shift::where('created_by', Auth::user()->creatorId())->get();
        $teams = Team::where('created_by', Auth::user()->creatorId())->get();
        $designations = Designation::where('created_by', Auth::user()->creatorId())->get();

        $roles = Role::whereNotIn('name', ['super admin', 'administrator', 'client', 'standard user', 'stealth user'])
            ->where('created_by', Auth::user()->creatorId())
            ->get();

        $employeesQuery = User::with('roles')
            ->join('employees', 'users.id', '=', 'employees.user_id')
            ->where('users.created_by', Auth::user()->creatorId())
            ->select(
                'users.*',
                'employees.employee_id',
                'employees.gender',
                'employees.dob',
                'employees.phone',
                'employees.company_doj',
                'employees.team_id as emp_team_id',
                'employees.designation_id as emp_designation_id',
                'employees.shift_id',
                'employees.is_active as emp_is_active',
                'employees.role_id as emp_role_id',
                'users.track_type'
            )
            ->whereHas('roles', function ($query) {
                $query->whereIn('name', ['stealth user', 'standard user']);
            });

        if ($request->has('search') && !empty($request->search)) {
            $employeesQuery->where(function ($q) use ($request) {
                $q->where('users.name', 'like', '%' . $request->search . '%')
                    ->orWhere('users.email', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->has('team_id') && !empty($request->team_id)) {
            $teamIds = is_array($request->team_id) ? $request->team_id : [$request->team_id];
            $employeesQuery->whereIn('employees.team_id', $teamIds);
        }

        if ($request->filled('status')) {
            $employeesQuery->where('employees.is_active', $request->status);
        }

        $employeesQuery->orderBy('users.created_at', 'desc');

        $perPage = $request->input('per_page', 10);
        $employees = $employeesQuery->paginate($perPage)->appends($request->query());

        $activeUserCount = User::whereHas('roles', function ($query) {
            $query->whereIn('name', ['stealth user', 'standard user']);
        })
            ->join('employees', 'users.id', '=', 'employees.user_id')
            ->where('users.created_by', Auth::user()->creatorId())
            ->where('employees.is_active', 1)
            ->count();

        return view('company.settings.user_settings', compact(
            'employees',
            'roles',
            'shifts',
            'teams',
            'designations',
            'activeUserCount'
        ));
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:191',
            'email' => 'required|email|unique:users,email',
            'mobile_no' => 'required|numeric|digits:10|unique:users,mobile_no',
            'dob' => 'nullable|date|before_or_equal:today',
            'date_of_join' => 'required|date|before_or_equal:today',
            'gender' => 'required|string|in:male,female,other',
            'designation_id' => 'required|integer|exists:designations,id',
            'shift_id' => 'required|integer|exists:shifts,id',
            'team_id' => 'required|integer|exists:teams,id',
            'role_id' => 'nullable|array',
            'role_id.*' => 'integer|exists:roles,id',
            'employee_id' => 'required|string|max:50|unique:employees,employee_id',
            'password' => 'required|string|min:6',
            'is_active' => 'required|boolean',
            'track_type' => 'required'
        ], [
            'name.required' => 'Name is required.',
            'email.required' => 'Email is required.',
            'email.email' => 'Enter a valid email address.',
            'email.unique' => 'This email is already taken.',
            'mobile_no.required' => 'Mobile number is required.',
            'mobile_no.numeric' => 'Mobile number must be numeric.',
            'mobile_no.digits' => 'Mobile number must be 10 digits.',
            'mobile_no.unique' => 'This mobile number is already taken.',
            'dob.date' => 'Enter a valid date for date of birth.',
            'dob.before_or_equal' => 'Date of birth must not be a future date.',
            'date_of_join.required' => 'Date of joining is required.',
            'date_of_join.date' => 'Enter a valid joining date.',
            'date_of_join.before_or_equal' => 'Joining date must not be a future date.',
            'gender.required' => 'Gender is required.',
            'designation_id.required' => 'Designation is required.',
            'shift_id.required' => 'Shift is required.',
            'team_id.required' => 'Team is required.',
            'role_id.array' => 'Invalid roles format.',
            'role_id.*.exists' => 'One or more selected roles are invalid.',
            'employee_id.required' => 'Employee ID is required.',
            'employee_id.max' => 'Employee ID should not exceed 50 characters.',
            'employee_id.unique' => 'This Employee ID is already taken.',
            'password.required' => 'Password is required.',
            'password.min' => 'Password must be at least 6 characters.',
            'is_active.required' => 'Status is required.',
            'is_active.boolean' => 'Invalid status value.',
        ]);

        try {
            DB::beginTransaction();

            $activeUserCount = User::whereHas('roles', function ($query) {
                $query->whereIn('name', ['stealth user', 'standard user']);
            })
                ->join('employees', 'users.id', '=', 'employees.user_id')
                ->where('users.created_by', Auth::user()->creatorId())
                ->where('employees.is_active', 1)
                ->count();

            if (auth()->user()->max_users <= $activeUserCount) {
                return redirect()->route('organization.settings.user')
                    ->with('error', 'Subscribed User Exists');
            }

            $userData = [
                'name' => $request->name,
                'email' => $request->email,
                'mobile_no' => $request->mobile_no,
                'type' => 'Employee', // fixed type
                'track_type' => $request->track_type,
                'created_by' => Auth::user()->creatorId(),
                'password' => Hash::make($request->password),
                'email_verified_at' => now()
            ];

            $user = User::create($userData);

            $standardUserRole = Role::where('name', 'standard user')->firstOrFail();
            $roleIds = [$standardUserRole->id];

            if ($request->has('role_id') && is_array($request->role_id)) {
                $roleIds = array_merge($roleIds, $request->role_id);
            }

            $user->roles()->sync($roleIds);

            DB::table('employees')->insert([
                'user_id' => $user->id,
                'employee_id' => $request->employee_id,
                'name' => $request->name,
                'email' => $request->email,
                'password' => $user->password,
                'gender' => $request->gender,
                'dob' => $request->dob,
                'phone' => $request->mobile_no,
                'company_doj' => $request->date_of_join,
                'team_id' => $request->team_id,
                'role_id' => $request->role_id[0] ?? $standardUserRole->id,
                'designation_id' => $request->designation_id,
                'shift_id' => $request->shift_id,
                'is_active' => $request->is_active,
                'created_by' => Auth::user()->creatorId(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            DB::commit();

            return redirect()->route('organization.settings.user')
                ->with('success', 'User record created successfully.');

        } catch (\Throwable $e) {
            DB::rollBack();

            \Log::error('User creation failed: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);

            return redirect()->back()
                ->withInput()
                ->with('error', 'Something went wrong while creating the user: ' . $e->getMessage());
        }
    }

    /**
     * @param Request $request
     * @return BinaryFileResponse
     */
    public function download(Request $request)
    {
        $employeesQuery = User::join('employees', 'users.id', '=', 'employees.user_id')
            ->where('users.created_by', Auth::user()->creatorId())
            ->select(
                'users.*',
                'employees.employee_id',
                'employees.gender',
                'employees.dob',
                'employees.phone',
                'employees.company_doj',
                'employees.team_id',
                'employees.designation_id',
                'employees.shift_id',
                'employees.role_id',
                'employees.is_active as emp_is_active'
            );

        if ($request->has('search') && !empty($request->search)) {
            $employeesQuery->where(function ($q) use ($request) {
                $q->where('users.name', 'like', '%' . $request->search . '%')
                    ->orWhere('users.email', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->has('team_id') && !empty($request->team_id)) {
            $employeesQuery->where('employees.team_id', $request->team_id);
        }

        $employees = $employeesQuery->get();

        $filename = 'employees_' . now()->format('Ymd_His') . '.xlsx';

        return Excel::download(new UsersExport($employees), $filename);
    }

    /**
     * @param Request $request
     * @param $id
     * @return RedirectResponse
     */
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:191',
            'email' => 'required|email|unique:users,email,' . $id,
            'mobile_no' => 'required|numeric|digits:10|unique:users,mobile_no,' . $id,
            'dob' => 'nullable|date|before_or_equal:today',
            'date_of_join' => 'required|date|before_or_equal:today',
            'gender' => 'required|string',
            'designation_id' => 'required|integer',
            'shift_id' => 'required|integer',
            'team_id' => 'required|integer',
            'role_id' => 'nullable|array',
            'employee_id' => 'required|string|max:50',
            'password' => 'nullable|string|min:6',
            'is_active' => 'required|boolean',
            'track_type' => 'required',
        ], [
            'name.required' => 'Name is required.',
            'email.required' => 'Email is required.',
            'email.email' => 'Enter a valid email address.',
            'email.unique' => 'This email is already taken.',
            'mobile_no.required' => 'Mobile number is required.',
            'mobile_no.numeric' => 'Mobile number must be numeric.',
            'mobile_no.digits' => 'Mobile number must be 10 digits.',
            'mobile_no.unique' => 'This mobile number is already taken.',
            'dob.required' => 'Date of birth is required.',
            'dob.date' => 'Enter a valid date for date of birth.',
            'dob.before_or_equal' => 'Date of birth must not be a future date.',
            'date_of_join.required' => 'Date of joining is required.',
            'date_of_join.date' => 'Enter a valid joining date.',
            'date_of_join.before_or_equal' => 'Joining date must not be a future date.',
            'gender.required' => 'Gender is required.',
            'designation_id.required' => 'Designation is required.',
            'shift_id.required' => 'Shift is required.',
            'team_id.required' => 'Team is required.',
            'role_id.array' => 'Invalid roles format.',
            'role_id.*.exists' => 'One or more selected roles are invalid.',
            'employee_id.required' => 'Employee ID is required.',
            'employee_id.max' => 'Employee ID should not exceed 50 characters.',
            'password.min' => 'Password must be at least 6 characters.',
            'is_active.required' => 'Status is required.',
            'is_active.boolean' => 'Invalid status value.',
        ]);

        try {
            DB::beginTransaction();
            $userData = [
                'name' => $request->name,
                'email' => $request->email,
                'mobile_no' => $request->mobile_no,
                'track_type' => $request->track_type,
                'is_active' => $request->is_active
            ];

            if ($request->filled('password')) {
                $userData['password'] = Hash::make($request->password);
            }

            $user->update($userData);

            $standardUserRole = Role::where('name', 'standard user')->firstOrFail();

            $roleIds = [$standardUserRole->id];
            if ($request->filled('role_id') && is_array($request->role_id)) {
                $roleIds = array_merge($roleIds, $request->role_id);
                $roleIds = array_unique($roleIds);
            }

            $user->roles()->sync($roleIds);

            DB::table('employees')
                ->where('user_id', $user->id)
                ->update([
                    'employee_id' => $request->employee_id ?? $user->id,
                    'name' => $request->name,
                    'email' => $request->email,
                    'password' => $user->password,
                    'gender' => $request->gender,
                    'dob' => $request->dob,
                    'phone' => $request->mobile_no,
                    'company_doj' => $request->date_of_join,
                    'team_id' => $request->team_id,
                    'role_id' => $selectedRole->id ?? $standardUserRole->id,
                    'designation_id' => $request->designation_id,
                    'shift_id' => $request->shift_id,
                    'is_active' => $request->is_active
                ]);

            DB::commit();

            return redirect()->route('organization.settings.user')->with('success', 'User record updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('User update failed: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);

            return redirect()->route('organization.settings.user')
                ->with('error', 'Failed to update user. Please try again.');
        }
    }

    /**
     * @param Request $request
     * @param $id
     * @return JsonResponse
     */
    public function toggleActive(Request $request, $id)
    {
        $user = User::findOrFail($id);
        $employee = Employee::where('user_id', $user->id)->firstOrFail();

        $creatorId = Auth::user()->creatorId();
        $creatorUser = User::findOrFail($creatorId);

        if ($request->is_active == 1) {

            $activeUserCount = User::whereHas('roles', function ($query) {
                $query->whereIn('name', ['stealth user', 'standard user']);
            })
                ->join('employees', 'users.id', '=', 'employees.user_id')
                ->where('users.created_by', $creatorId)
                ->where('employees.is_active', 1)
                ->count();

            if ($activeUserCount >= $creatorUser->max_users) {
                return response()->json([
                    'success' => false,
                    'message' => 'User limit exceeded. Please upgrade your plan.'
                ], 403);
            }
        }

        $employee->update([
            'is_active' => $request->is_active,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Status updated successfully'
        ]);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function bulkStatusUpdate(Request $request)
    {
        $request->validate([
            'employee_ids' => 'required|array',
            'status' => 'required|in:0,1'
        ]);

        $creatorId = Auth::user()->creatorId();
        $creatorUser = User::findOrFail($creatorId);

        if ($request->status == 1) {

            $activeUserCount = User::whereHas('roles', function ($query) {
                $query->whereIn('name', ['stealth user', 'standard user']);
            })
                ->join('employees', 'users.id', '=', 'employees.user_id')
                ->where('users.created_by', $creatorId)
                ->where('employees.is_active', 1)
                ->count();

            $activatingCount = DB::table('employees')
                ->whereIn('user_id', $request->employee_ids)
                ->where('is_active', 0)
                ->count();

            if (($activeUserCount + $activatingCount) > $creatorUser->max_users) {
                return response()->json([
                    'success' => false,
                    'message' => 'User limit exceeded. Please upgrade your plan.'
                ], 403);
            }
        }

        DB::table('employees')
            ->whereIn('user_id', $request->employee_ids)
            ->update([
                'is_active' => $request->status == '1' ? 1 : 0,
                'updated_at' => now(),
            ]);

        return response()->json([
            'success' => true,
            'message' => 'Status updated successfully'
        ]);
    }

    /**
     * @param $id
     * @return RedirectResponse
     */
    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->delete();

        return redirect()->route('organization.settings.user');
    }

    /**
     * @param Request $request
     * @param User $user
     * @param $id
     * @return RedirectResponse|void
     */
    public function LoginWithStandardUser(Request $request, User $user, $id)
    {
        $user = User::find($id);
        if ($user && auth()->check()) {
            Impersonate::take($request->user(), $user);
            return redirect()->route('employee.self-report');
        }
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function exitEmployee(Request $request)
    {
        if (!auth()->user()->isImpersonated()) {
            return redirect()->route('organization.dashboard');
        }

        auth()->user()->leaveImpersonation();

        return redirect()->route('organization.dashboard');
    }
}
