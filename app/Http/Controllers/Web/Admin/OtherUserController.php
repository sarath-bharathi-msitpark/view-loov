<?php

namespace App\Http\Controllers\Web\Admin;
use App\Http\Controllers\Controller;

use App\Models\CustomField;
use App\Models\Employee;
use App\Models\User;
use App\Models\Utility;
use Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Illuminate\Validation\Rule;

use Spatie\Permission\Models\Permission;

class OtherUserController extends Controller
{

    public function index(Request $request)
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
    
            return view('admin.otheruser.index', compact('users'));
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
            return view('admin.otheruser.create', compact('roles', 'customFields'));
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
                $request['password'] = !empty($userpassword)?\Hash::make($userpassword) : null;
                $request['type'] = 'staff';
                $request['lang'] = !empty($default_language) ? $default_language->value : 'en';
                $request['created_by'] = \Auth::user()->creatorId();
                $request['email_verified_at'] = date('Y-m-d H:i:s');
                $request['is_enable_login'] = $enableLogin;

                $user = User::create($request->all());
                $user->assignRole($role_r);
                
                return redirect()->route('admin.otheruser.index')->with('success', __('User successfully created.'));
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
            $permissions = Permission::whereIn('id', [526, 527, 528, 529, 533, 534, 535, 537, 538, 539, 540])->pluck('name', 'id')->toArray();

            return view('admin.otheruser.edit', compact('user', 'roles', 'customFields', 'permissions'));
        } else {
            return redirect()->back();
        }

    }

    public function update(Request $request, $id)
    {

        if (\Auth::user()->can('edit user')) {
                $user = User::findOrFail($id);
                $validator = \Validator::make(
                    $request->all(), [
                        'name' => 'required|max:120',
                        'email' => 'required|email|unique:users,email,' . $id,
                        'role' => 'required',
                        
                    ]
                );
                if ($validator->fails()) {
                    $messages = $validator->getMessageBag();
                    return redirect()->back()->with('error', $messages->first());
                }

                $role = Role::findById($request->role);
                $input = $validator->validated();

                $user->fill($input)->save();

                $roles[] = $request->role;
                

                $user->roles()->sync($roles);

                return redirect()->route('admin.other-users.index')->with(
                    'success', 'User successfully updated.'
                );
        } else {
            return redirect()->back();
        }
    }

    public function destroy($id)
    {

        if (\Auth::user()->can('delete user')) {

            $user = User::find($id);
            if ($user) {
                $user->delete();
                return redirect()->route('admin.other-users.index')->with('success', __('User successfully deleted .'));
            } else {
                return redirect()->back()->with('error', __('Something is wrong.'));
            }
        } else {
            return redirect()->back();
        }
    }

    public function userPassword($id)
    {
        $eId = \Crypt::decrypt($id);
        $user = User::find($eId);

        return view('admin.otheruser.reset', compact('user'));

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

        return redirect()->route('admin.other-users.index')->with(
            'success', 'User Password successfully updated.'
        );

    }
    
    public function LoginManage($id)
    {
        $eId = \Crypt::decrypt($id);
        $user = User::find($eId);
        $authUser = \Auth::user();

        if ($user->is_enable_login == 1) {
            $user->is_enable_login = 0;
            $user->save();

            return redirect()->back()->with('success', __('User login disable successfully.'));
        } else {
            $user->is_enable_login = 1;
            $user->save();
            return redirect()->back()->with('success', __('User login enable successfully.'));
        }
    }
}
