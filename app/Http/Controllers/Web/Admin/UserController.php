<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;

use App\Models\CustomField;
use App\Models\Employee;
use App\Models\LoginDetail;
use App\Models\Order;
use App\Models\Plan;
use App\Models\User;
use App\Models\UserToDo;
use App\Models\Utility;
use Auth;
use File;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Lab404\Impersonate\Impersonate;
use Spatie\Permission\Models\Role;
use App\Models\ReferralTransaction;
use Spatie\Permission\Models\Permission;

class UserController extends Controller
{

    public function index(Request $request)
    {
        User::defaultEmail();

        $user = \Auth::user();

        if ($user->can('manage user')) {
            $query = User::where('created_by', $user->creatorId())
                ->with(['currentPlan']);

            if ($user->type == 'super admin') {
                $query->where('type', '=', 'company');
            } else {
                $query->where('type', '!=', 'client');
            }

            // ── Status filter (default = active) ─────────────────────────────────
            $status = $request->get('status', 'active'); // default to 'active'

            if ($status === 'active') {
                $query->where('is_active', 1);
            } elseif ($status === 'inactive') {
                $query->where('is_active', 0);
            }
            // 'all' → no is_active filter applied

            // ── Search filter ─────────────────────────────────────────────────────
            if ($request->has('search') && !empty($request->search)) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'LIKE', "%{$search}%")
                        ->orWhere('email', 'LIKE', "%{$search}%")
                        ->orWhere('company_name', 'LIKE', "%{$search}%");
                });
            }

            $users = $query->paginate(12)->appends($request->all());

            return view('admin.user.index', compact('users'));
        } else {
            return redirect()->back();
        }
    }

    public function otherAdminUsers(Request $request)
    {

        $user = \Auth::user();

        if ($user->type == 'super admin') {
            $query = User::where('created_by', $user->creatorId());
            $query->where('type', '!=', 'company');

            if ($request->has('search') && !empty($request->search)) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'LIKE', "%{$search}%")
                        ->orWhere('email', 'LIKE', "%{$search}%")
                        ->orWhere('company_name', 'LIKE', "%{$search}%");
                });
            }

            $users = $query->paginate(12)->appends($request->all());

            return view('admin.user.other_admins', compact('users'));
        } else {
            return redirect()->back();
        }


    }

    public function create()
    {

        $customFields = CustomField::where('created_by', '=', \Auth::user()->creatorId())->where('module', '=', 'user')->get();
        $user = \Auth::user();
        $roles = Role::where('created_by', '=', $user->creatorId())->get()->pluck('name', 'id');
        if (\Auth::user()->can('create user')) {
            return view('admin.user.create', compact('roles', 'customFields'));
        } else {
            return redirect()->back();
        }
    }

    public function store(Request $request)
    {

        if (\Auth::user()->can('create user')) {
            $default_language = DB::table('settings')->select('value')->where('name', 'default_language')->where('created_by', '=', \Auth::user()->creatorId())->first();
            $objUser = \Auth::user()->creatorId();

            if (\Auth::user()->type == 'super admin') {
                $validator = \Validator::make(
                    $request->all(), [
                        'name' => 'required|max:120',
                        'email' => 'required|email|unique:users',
                        'role' => 'required',
                    ]
                );
                if ($validator->fails()) {
                    $messages = $validator->getMessageBag();
                    return redirect()->back()->with('error', $messages->first());
                }

                $enableLogin = 0;
                if (!empty($request->password_switch) && $request->password_switch == 'on') {
                    $enableLogin = 1;
                    $validator = \Validator::make(
                        $request->all(), ['password' => 'required|min:6']
                    );

                    if ($validator->fails()) {
                        return redirect()->back()->with('error', $validator->errors()->first());
                    }
                }

                $objUser = User::find($objUser);
                $user = User::find(\Auth::user()->created_by);
                $userpassword = $request->input('password');
                $role_r = Role::findById($request->role);
                $psw = $request->password;
                $request['password'] = !empty($userpassword) ? \Hash::make($userpassword) : null;
                $request['type'] = $role_r->name;
                $request['lang'] = !empty($default_language) ? $default_language->value : 'en';
                $request['created_by'] = \Auth::user()->creatorId();
                $request['email_verified_at'] = date('Y-m-d H:i:s');
                $request['is_enable_login'] = $enableLogin;

                $user = User::create($request->all());
                $user->assignRole($role_r);

                return redirect()->route('admin.otherAdminUsers')->with('success', __('User successfully created.'));
            }

        } else {
            return redirect()->back();
        }

    }

    public function show()
    {
        return redirect()->route('user.index');
    }

    public function edit($id)
    {
        $user = \Auth::user();
        $roles = Role::where('created_by', '=', $user->creatorId())->where('name', '!=', 'client')->get()->pluck('name', 'id');
        if (\Auth::user()->can('edit user')) {
            $user = User::findOrFail($id);
            $user->customField = CustomField::getData($user, 'user');
            $customFields = CustomField::where('created_by', '=', \Auth::user()->creatorId())->where('module', '=', 'user')->get();

            $modules = array(
                'settings',
                'screenshot',
                'live_shot',
                'live_cam_shot',
                'apps_and_urls',
                'break_report',
                'daily_attendance_report',
                'activity_report',
                'apps_and_urls_report',
                'highlights_report',
                'crm',
                'project_management',
                'task_management'
            );
            $permissions = Permission::whereIn('name', $modules)->pluck('name', 'id')->toArray();

            $admin_payment_setting = Utility::getAdminPaymentSetting();

            return view('admin.user.edit', compact('user', 'roles', 'customFields', 'permissions', 'modules', 'admin_payment_setting'));
        } else {
            return redirect()->back();
        }

    }



// ─────────────────────────────────────────────────────────────────────────────
// Add this to your UserController — replace the existing update() method
// ─────────────────────────────────────────────────────────────────────────────

    public function update(Request $request, $id)
    {
        if (\Auth::user()->can('edit user')) {

            $user = User::findOrFail($id);

            // ── Capture old status before saving ─────────────────────────────────
            $wasActive = (int)$user->is_active;
            $isNowActive = $request->has('is_active') ? 1 : 0;  // checkbox sends 1 or nothing

            if (\Auth::user()->type == 'super admin') {

                $validator = \Validator::make(
                    $request->all(), [
                        'name' => 'required|max:120',
                        'email' => 'required|email|unique:users,email,' . $id,
                        'company_name' => 'required|string|max:255',
                        'domain' => 'required|url',
                        'country' => 'required|string',
                        'country_code' => 'required|string',
                        'mobile_no' => 'required|string|max:20',
                        'permissions' => 'nullable|array',
                        'payment_mode' => 'required',
                    ]
                );

                if ($validator->fails()) {
                    $messages = $validator->getMessageBag();
                    return redirect()->back()->with('error', $messages->first());
                }

                $input = $validator->validated();

                // ── Append is_active (not in validator rules, handled separately) ─
                $input['is_active'] = $isNowActive;

                // ── Active → Inactive: disable company login + deactivate all employees ─
                if ($wasActive === 1 && $isNowActive === 0) {
                    $input['is_enable_login'] = 0;

                    // Deactivate all employees belonging to this company
                    User::where('created_by', $user->id)
                        ->update([
                            'is_active' => 0,
                            'is_enable_login' => 0,
                        ]);
                }

                // ── Inactive → Active: only activate the company account itself.
                //    Employees remain inactive — admin must activate them individually.
                //    (No bulk employee update here — per requirements)

                $user->fill($input)->save();
                CustomField::saveData($user, $request->customField);

                // ── Sync permissions ──────────────────────────────────────────────
                $modules = [
                    'settings',
                    'screenshot',
                    'live_shot',
                    'live_cam_shot',
                    'apps_and_urls',
                    'break_report',
                    'daily_attendance_report',
                    'activity_report',
                    'apps_and_urls_report',
                    'highlights_report',
                    'crm',
                    'project_management',
                    'task_management',
                ];

                $displayedPermissionIds = Permission::whereIn('name', $modules)->pluck('id')->toArray();
                $selectedPermissionIds = $request->input('permissions', []);
                $otherPermissionIds = $user->permissions()
                    ->whereNotIn('id', $displayedPermissionIds)
                    ->pluck('id')
                    ->toArray();

                $newPermissionIds = array_unique(array_merge($selectedPermissionIds, $otherPermissionIds));
                $user->permissions()->sync($newPermissionIds);

                return redirect()->route('admin.users.index')->with(
                    'success', 'Company successfully updated.'
                );

            } else {

                $validator = \Validator::make(
                    $request->all(), [
                        'name' => 'required|max:120',
                        'email' => 'required|email|unique:users,email,' . $id,
                        'role' => 'required',
                        'mobile_no' => 'required',
                        'country_code' => 'required',
                        'gender' => 'required',
                        'designation' => 'required',
                        'team' => 'required',
                        'shift' => 'required',
                        'employee_id' => 'nullable',
                    ]
                );

                if ($validator->fails()) {
                    $messages = $validator->getMessageBag();
                    return redirect()->back()->with('error', $messages->first());
                }

                $role = Role::findById($request->role);
                $input = $validator->validated();

                $input['type'] = $role->name;
                $input['is_active'] = $isNowActive;

                // Disable login when employee is deactivated
                if ($wasActive === 1 && $isNowActive === 0) {
                    $input['is_enable_login'] = 0;
                }

                $user->fill($input)->save();
                Utility::employeeDetailsUpdate($user->id, \Auth::user()->creatorId());
                CustomField::saveData($user, $request->customField);

                $roles[] = $request->role;
                $user->roles()->sync($roles);

                return redirect()->route('admin.users.index')->with(
                    'success', 'User successfully updated.'
                );
            }

        } else {
            return redirect()->back();
        }
    }

    public function destroy($id)
    {

        if (\Auth::user()->can('delete user')) {
            if ($id == 2) {
                return redirect()->back()->with('error', __('You can not delete By default Company'));
            }

            $user = User::find($id);
            if ($user) {
                if (\Auth::user()->type == 'super admin') {
                    // $referralSetting = ReferralSetting::where('created_by' , 1)->first();
                    // $users = ReferralTransaction::where('company_id' , $id)->first();
                    // $plan = Plan::find($users->plan_id);
                    // Utility::commissionAmount($plan , $referralSetting , $users->referral_code , 'minus');

                    $transaction = ReferralTransaction::where('company_id', $id)->delete();

                    $users = User::where('created_by', $id)->delete();
                    $employee = Employee::where('created_by', $id)->delete();

                    Plan::where('company_id', $id)->delete();
                    Order::where('user_id', $id)->delete();

                    $user->delete();

                    return redirect()->back()->with('success', __('Company Successfully deleted'));
                }

                if (\Auth::user()->type == 'company') {

                    $delete_user = User::where(['id' => $user->id])->first();
                    if ($delete_user) {
                        $employee = Employee::where(['user_id' => $user->id])->delete();
                        $delete_user->delete();

                        if ($delete_user || $employee) {
                            return redirect()->route('admin.users.index')->with('success', __('User successfully deleted .'));
                        } else {
                            return redirect()->back()->with('error', __('Something is wrong.'));
                        }
                    } else {
                        return redirect()->back()->with('error', __('Something is wrong.'));
                    }
                }
                return redirect()->route('admin.users.index')->with('success', __('User successfully deleted .'));
            } else {
                return redirect()->back()->with('error', __('Something is wrong.'));
            }
        } else {
            return redirect()->back();
        }
    }

    public function profile()
    {
        $userDetail = \Auth::user();
        $userDetail->customField = CustomField::getData($userDetail, 'user');
        $customFields = CustomField::where('created_by', '=', \Auth::user()->creatorId())->where('module', '=', 'user')->get();

        return view('user.profile', compact('userDetail', 'customFields'));
    }

    public function editprofile(Request $request)
    {
        $userDetail = \Auth::user();
        $user = User::findOrFail($userDetail['id']);

        $validator = \Validator::make(
            $request->all(), [
                'name' => 'required|max:120',
                'email' => 'required|email|unique:users,email,' . $userDetail['id'],
            ]
        );
        if ($validator->fails()) {
            $messages = $validator->getMessageBag();
            return redirect()->back()->with('error', $messages->first());
        }

        if ($request->hasFile('profile')) {
            $filenameWithExt = $request->file('profile')->getClientOriginalName();
            $filename = pathinfo($filenameWithExt, PATHINFO_FILENAME);
            $extension = $request->file('profile')->getClientOriginalExtension();
            $fileNameToStore = $filename . '_' . time() . '.' . $extension;

            $settings = Utility::getStorageSetting();
            if ($settings['storage_setting'] == 'local') {
                $dir = 'uploads/avatar/';
            } else {
                $dir = 'uploads/avatar';
            }

            $image_path = $dir . $userDetail['avatar'];

            if (File::exists($image_path)) {
                File::delete($image_path);
            }

            $url = '';
            $path = Utility::upload_file($request, 'profile', $fileNameToStore, $dir, []);
            if ($path['flag'] == 1) {
                $url = $path['url'];
            } else {
                return redirect()->route('profile', \Auth::user()->id)->with('error', __($path['msg']));
            }
        }

        if (!empty($request->profile)) {
            $user['avatar'] = $fileNameToStore;
        }
        $user['name'] = $request['name'];
        $user['email'] = $request['email'];
        $user->save();
        CustomField::saveData($user, $request->customField);

        return redirect()->route('profile', $user)->with(
            'success', 'Profile successfully updated.'
        );
    }

    public function changePassword()
    {
        return view('admin.user.change_password');
    }

    public function updatePassword(Request $request)
    {

        if (Auth::Check()) {

            $validator = \Validator::make(
                $request->all(), [
                    'old_password' => 'required',
                    'password' => 'required|min:6',
                    'password_confirmation' => 'required|same:password',
                ]
            );
            if ($validator->fails()) {
                $messages = $validator->getMessageBag();
                return redirect()->back()->with('error', $messages->first());
            }

            $objUser = Auth::user();
            $request_data = $request->All();
            $current_password = $objUser->password;
            if (Hash::check($request_data['old_password'], $current_password)) {
                $user_id = Auth::User()->id;
                $obj_user = User::find($user_id);
                $obj_user->password = Hash::make($request_data['password']);
                $obj_user->save();

                return redirect()->back()->with('success', 'Password successfully updated.');
            } else {
                return redirect()->back()->with('success', 'Please enter correct current password.');
            }
        } else {
            return redirect()->back()->with('error', __('Something is wrong.'));
        }
    }

    // User To do module
    public function todo_store(Request $request)
    {
        $request->validate(
            ['title' => 'required|max:120']
        );

        $post = $request->all();
        $post['user_id'] = Auth::user()->id;
        $todo = UserToDo::create($post);

        $todo->updateUrl = route(
            'todo.update', [
                $todo->id,
            ]
        );
        $todo->deleteUrl = route(
            'todo.destroy', [
                $todo->id,
            ]
        );

        return $todo->toJson();
    }

    public function todo_update($todo_id)
    {
        $user_todo = UserToDo::find($todo_id);
        if ($user_todo->is_complete == 0) {
            $user_todo->is_complete = 1;
        } else {
            $user_todo->is_complete = 0;
        }
        $user_todo->save();
        return $user_todo->toJson();
    }

    public function todo_destroy($id)
    {
        $todo = UserToDo::find($id);
        $todo->delete();

        return true;
    }

    // change mode 'dark or light'
    public function changeMode()
    {
        $usr = \Auth::user();
        if ($usr->mode == 'light') {
            $usr->mode = 'dark';
            $usr->dark_mode = 1;
        } else {
            $usr->mode = 'light';
            $usr->dark_mode = 0;
        }
        $usr->save();

        return redirect()->back();
    }

    public function upgradePlan($user_id)
    {
        $user = User::find($user_id);
        $plans = Plan::where('company_id', $user_id)->get();
        $admin_payment_setting = Utility::getAdminPaymentSetting();

        return view('admin.user.plan', compact('user', 'plans', 'admin_payment_setting'));
    }

    public function activePlan($user_id, $plan_id)
    {

        $plan = Plan::find($plan_id);
        if ($plan->is_disable == 0) {
            return redirect()->back()->with('error', __('You are unable to upgrade this plan because it is disabled.'));
        }

        $user = User::find($user_id);
        $assignPlan = $user->assignPlan($plan_id, $user_id);
        if ($assignPlan['is_success'] == true && !empty($plan)) {
            $orderID = strtoupper(str_replace('.', '', uniqid('', true)));
            Order::create(
                [
                    'order_id' => $orderID,
                    'name' => null,
                    'card_number' => null,
                    'card_exp_month' => null,
                    'card_exp_year' => null,
                    'plan_name' => $plan->name,
                    'plan_id' => $plan->id,
                    'price' => $plan->price,
                    'price_currency' => isset(\Auth::user()->planPrice()['currency']) ? \Auth::user()->planPrice()['currency'] : '',
                    'txn_id' => '',
                    'payment_status' => 'success',
                    'receipt' => null,
                    'user_id' => $user->id,
                ]
            );

            return redirect()->back()->with('success', 'Plan successfully upgraded.');
        } else {
            return redirect()->back()->with('error', 'Plan fail to upgrade.');
        }

    }

    public function userPassword($id)
    {
        $eId = \Crypt::decrypt($id);
        $user = User::find($eId);

        return view('admin.user.reset', compact('user'));

    }

    public function userPasswordReset(Request $request, $id)
    {
        $validator = \Validator::make(
            $request->all(), [
                'password' => 'required|confirmed|same:password_confirmation',
            ]
        );

        if ($validator->fails()) {
            $messages = $validator->getMessageBag();

            return redirect()->back()->with('error', $messages->first());
        }

        $user = User::where('id', $id)->first();
        $user->forceFill([
            'password' => Hash::make($request->password),
            'is_enable_login' => 1,
        ])->save();

        if (\Auth::user()->type == 'super admin') {
            return redirect()->route('admin.users.index')->with(
                'success', 'Company Password successfully updated.'
            );
        } else {
            return redirect()->route('admin.users.index')->with(
                'success', 'User Password successfully updated.'
            );
        }

    }

    //start for user login details
    public function userLog(Request $request)
    {
        $filteruser = User::where('created_by', \Auth::user()->creatorId())->get()->pluck('name', 'id');
        $filteruser->prepend('Select User', '');

        $query = DB::table('login_details')
            ->join('users', 'login_details.user_id', '=', 'users.id')
            ->select(DB::raw('login_details.*, users.id as user_id , users.name as user_name , users.email as user_email ,users.type as user_type'))
            ->where(['login_details.created_by' => \Auth::user()->id]);

        if (!empty($request->month)) {
            $query->whereMonth('date', date('m', strtotime($request->month)));
            $query->whereYear('date', date('Y', strtotime($request->month)));
        } else {
            $query->whereMonth('date', date('m'));
            $query->whereYear('date', date('Y'));
        }

        if (!empty($request->users)) {
            $query->where('user_id', '=', $request->users);
        }
        $userdetails = $query->get();
        $last_login_details = LoginDetail::where('created_by', \Auth::user()->creatorId())->get();

        return view('user.userlog', compact('userdetails', 'last_login_details', 'filteruser'));
    }

    public function userLogView($id)
    {
        $users = LoginDetail::find($id);

        return view('user.userlogview', compact('users'));
    }

    public function userLogDestroy($id)
    {
        $users = LoginDetail::where('user_id', $id)->delete();
        return redirect()->back()->with('success', 'User successfully deleted.');
    }

    public function LoginWithCompany(Request $request, User $user, $id)
    {
        $user = User::find($id);
        if ($user && auth()->check()) {
            Impersonate::take($request->user(), $user);
            return redirect()->route('organization.dashboard');
        }
    }

    public function ExitCompany(Request $request)
    {
        \Auth::user()->leaveImpersonation($request->user());
        return redirect()->route('admin.dashboard');
    }

    public function companyInfo(Request $request, $id)
    {
        $user = User::find($request->id);
        $status = $user->delete_status;
        $userData = User::where('created_by', $id)->where('type', '!=', 'client')->selectRaw('COUNT(*) as total_users, SUM(CASE WHEN is_disable = 0 THEN 1 ELSE 0 END) as disable_users, SUM(CASE WHEN is_disable = 1 THEN 1 ELSE 0 END) as active_users')->first();

        return view('admin.user.company_info', compact('userData', 'id', 'status'));
    }

    public function userUnable(Request $request)
    {
        User::where('id', $request->id)->update(['is_disable' => $request->is_disable]);
        $userData = User::where('created_by', $request->company_id)->where('type', '!=', 'client')->selectRaw('COUNT(*) as total_users, SUM(CASE WHEN is_disable = 0 THEN 1 ELSE 0 END) as disable_users, SUM(CASE WHEN is_disable = 1 THEN 1 ELSE 0 END) as active_users')->first();

        if ($request->is_disable == 1) {

            return response()->json(['success' => __('User successfully unable.'), 'userData' => $userData]);

        } else {
            return response()->json(['success' => __('User successfully disable.'), 'userData' => $userData]);
        }
    }

    public function LoginManage($id)
    {
        $eId = \Crypt::decrypt($id);
        $user = User::find($eId);
        $authUser = \Auth::user();

        if ($user->is_enable_login == 1) {
            $user->is_enable_login = 0;
            $user->save();

            if ($authUser->type == 'super admin') {
                return redirect()->back()->with('success', __('Company login disable successfully.'));
            } else {
                return redirect()->back()->with('success', __('User login disable successfully.'));
            }
        } else {
            $user->is_enable_login = 1;
            $user->save();
            if ($authUser->type == 'super admin') {
                return redirect()->back()->with('success', __('Company login enable successfully.'));
            } else {
                return redirect()->back()->with('success', __('User login enable successfully.'));
            }
        }
    }
}
