<?php

namespace App\Http\Controllers\Web\Company;

use App\Exports\UsersExport;
use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class AdministratorSettingsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $roles = Role::where('created_by', Auth::user()->creatorId())->get();
        $roleIds = $roles->pluck('id');

        $employeesQuery = User::join('employees', 'users.id', '=', 'employees.user_id')
            ->where('users.created_by', Auth::user()->creatorId())
            ->select(
                'users.*',
                'employees.employee_id',
                'employees.gender',
                'employees.dob',
                'employees.phone',
                'employees.company_doj',
                'employees.role_id as emp_role_id',
                'employees.is_active as emp_is_active'
            )
            ->whereIn('employees.role_id', $roleIds);

        if ($request->filled('search')) {
            $employeesQuery->where(function ($q) use ($request) {
                $q->where('users.name', 'like', '%' . $request->search . '%')
                    ->orWhere('users.email', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->filled('status')) {
            $employeesQuery->where('employees.is_active', $request->status);
        }

        $employeesQuery->orderBy('users.created_at', 'desc');

        $perPage = $request->get('per_page', 10);

        $employees = $employeesQuery->paginate($perPage)->appends($request->except('page'));

        return view('company.settings.administrator', compact('employees', 'roles'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:191',
            'email' => 'required|email|unique:users,email',
            'mobile_no' => 'required|numeric|digits:10|unique:users,mobile_no',
            'dob' => 'nullable|date|before_or_equal:today',
            'date_of_join' => 'required|date|before_or_equal:today',
            'gender' => 'required|string',
            'role_id' => 'required|integer',
            'employee_id' => 'required|string|max:50',
            'password' => 'required|string|min:6',
            'is_active' => 'required|boolean',
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
            'role_id.required' => 'Role is required.',
            'employee_id.required' => 'Employee ID is required.',
            'employee_id.max' => 'Employee ID should not exceed 50 characters.',
            'password.required' => 'Password is required.',
            'password.min' => 'Password must be at least 6 characters.',
            'is_active.required' => 'Status is required.',
            'is_active.boolean' => 'Invalid status value.',
        ]);

        $userData = [
            'name' => $request->name,
            'email' => $request->email,
            'mobile_no' => $request->mobile_no,
            'type' => 'Employee_Admin', // User type is constant, Not to change.
            'created_by' => Auth::user()->creatorId(),
        ];

        if ($request->filled('password')) {
            $userData['password'] = Hash::make($request->password);
        }

        $user = User::create($userData);

        $selectedRole = Role::findOrFail($request->role_id);
        $user->roles()->sync([$selectedRole->id]);

        DB::table('employees')->insert([
            'user_id' => $user->id,
            'employee_id' => $request->employee_id ?? $user->id,
            'name' => $request->name,
            'email' => $request->email,
            'password' => $user->password,
            'gender' => $request->gender,
            'dob' => $request->dob,
            'phone' => $request->mobile_no,
            'company_doj' => $request->date_of_join,
            'role_id' => $request->role_id,
            'is_active' => $request->is_active,
            'created_by' => Auth::user()->creatorId(),
        ]);

        return redirect()->route('organization.setting.administrator.index')->with('success', 'User record created successfully.');
    }

    /**
     * @param Request $request
     * @return BinaryFileResponse
     */
    public function download(Request $request)
    {
        $roles = Role::where('created_by', Auth::user()->creatorId())->get();
        $roleIds = $roles->pluck('id');

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
            )->whereIn('employees.role_id', $roleIds);

        $employees = $employeesQuery->get();

        $filename = 'administrator_' . now()->format('Ymd_His') . '.xlsx';

        return Excel::download(new UsersExport($employees), $filename);
    }

    /**
     * @param Request $request
     * @param string $id
     * @return RedirectResponse
     */
    public function update(Request $request, string $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:191',
            'email' => 'required|email|unique:users,email,' . $id,
            'mobile_no' => 'required|numeric|digits:10|unique:users,mobile_no,' . $id,
            'dob' => 'nullable|date|before_or_equal:today',
            'date_of_join' => 'required|date|before_or_equal:today',
            'gender' => 'required|string',
            'role_id' => 'required|integer',
            'employee_id' => 'required|string|max:50',
            'password' => 'nullable|string|min:6',
            'is_active' => 'required',

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
            'role_id.required' => 'Role is required.',
            'employee_id.required' => 'Employee ID is required.',
            'employee_id.max' => 'Employee ID should not exceed 50 characters.',
            'password.min' => 'Password must be at least 6 characters.',
            'is_active.required' => 'Status is required.',
            'is_active.boolean' => 'Invalid status value.',
        ]);

        $userData = [
            'name' => $request->name,
            'email' => $request->email,
            'mobile_no' => $request->mobile_no,
        ];

        if ($request->filled('password')) {
            $userData['password'] = Hash::make($request->password);
        }

        $userData['is_active'] = $request->has('is_active') ? 1 : 0;

        $user->update($userData);

        $selectedRole = Role::findOrFail($request->role_id);
        $user->roles()->sync([$selectedRole->id]);

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
                'role_id' => $request->role_id,
                'is_active' => $request->input('is_active', 0),
            ]);

        return redirect()->route('organization.setting.administrator.index')->with('success', 'User record updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
