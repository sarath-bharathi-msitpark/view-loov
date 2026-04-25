<?php

namespace App\Http\Controllers\Web\Shared;

use App\Http\Controllers\Controller;

use App\Models\Plan;
use App\Models\User;
use App\Models\Utility;
use File;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;

use App\Http\Controllers\Payment\CashfreeController;
class PlanController extends Controller
{
    public function index(Request $request)
    {
        // $newPlan = Plan::find(27);
        // $cashfreeController = new CashfreeController();
        // $cashfreeController->updateCashfreePlanRecurringAmount("1223576", 1000);
        
        $query = Plan::with('company');
        
        if(\Auth::user()->can('manage plan'))
        {
            if(\Auth::user()->type == 'super admin') {
                $query = $query->orderByRaw("CASE WHEN plan_type = 'common' THEN 0 ELSE 1 END");
            } elseif (\Auth::user()->type == 'company') {
                $query->where('company_id', \Auth::user()->id);
            }  else {
                $query->where('is_disable', 1);
            }
            if (!empty($request->company_id2)) {
                $query->where('company_id', $request->company_id2);
            }
            
            if ($request->has('search') && !empty($request->search)) {
                $search = $request->search;
                $query->whereHas('company', function ($q) use ($search) {
                    $q->where('name', 'LIKE', "%{$search}%")
                        ->orWhere('email', 'LIKE', "%{$search}%")
                        ->orWhere('mobile_no', 'LIKE', "%{$search}%");
                });
            }

            $plans = $query->latest()->get();
            $admin_payment_setting = Utility::getAdminPaymentSetting();
        
            $companies = User::where('type', 'company')->pluck('company_name', 'id');
            $companies->prepend('Select Company', '');

            return view('shared.plan.index', compact('plans', 'admin_payment_setting', 'companies'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }


    public function create()
    {
        if(\Auth::user()->can('create plan'))
        {
            $arrDuration = [
                'lifetime' => __('Lifetime'),
                'month' => __('Per Month'),
                'year' => __('Per Year'),
            ];
            
            $companies = User::where('type', 'company')->pluck('company_name', 'id');
            $companies->prepend('Select Company', '');
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

            return view('shared.plan.create', compact('arrDuration', 'companies','permissions', 'modules'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }


    public function store(Request $request)
    {



        if(\Auth::user()->can('create plan'))
        {
            $admin_payment_setting = Utility::getAdminPaymentSetting();

            if(!empty($admin_payment_setting) && ($admin_payment_setting['is_manually_payment_enabled'] == 'on'
                    || $admin_payment_setting['is_bank_transfer_enabled'] == 'on' || $admin_payment_setting['is_stripe_enabled'] == 'on'
                    || $admin_payment_setting['is_paypal_enabled'] == 'on' || $admin_payment_setting['is_paystack_enabled'] == 'on'
                    || $admin_payment_setting['is_flutterwave_enabled'] == 'on' || $admin_payment_setting['is_razorpay_enabled'] == 'on'
                    || $admin_payment_setting['is_mercado_enabled'] == 'on' || $admin_payment_setting['is_paytm_enabled'] == 'on'
                    || $admin_payment_setting['is_mollie_enabled'] == 'on' || $admin_payment_setting['is_skrill_enabled'] == 'on'
                    || $admin_payment_setting['is_coingate_enabled'] == 'on'|| $admin_payment_setting['is_paymentwall_enabled'] == 'on'
                    || $admin_payment_setting['is_toyyibpay_enabled'] == 'on' || $admin_payment_setting['is_payfast_enabled'] == 'on'
                    || $admin_payment_setting['is_iyzipay_enabled'] == 'on' || $admin_payment_setting['is_sspay_enabled'] == 'on'
                    || $admin_payment_setting['is_paytab_enabled'] == 'on'  || $admin_payment_setting['is_benefit_enabled'] == 'on'
                    || $admin_payment_setting['is_cashfree_enabled'] == 'on'  || $admin_payment_setting['is_aamarpay_enabled'] == 'on'
                    || $admin_payment_setting['is_paytr_enabled'] == 'on' || $admin_payment_setting['is_yookassa_enabled'] ='on'
                    || $admin_payment_setting['is_midtrans_enabled'] == 'on' || $admin_payment_setting['is_xendit_enabled'] == 'on'
                    || $admin_payment_setting['is_nepalste_enabled'] == 'on'))
            {

                $validation = [
                    'price' => 'required|numeric|min:0',
                    'duration' => 'required|in:lifetime,month,year',
                    'max_users' => 'required|integer|min:1',
                    'max_clients' => 'nullable|integer|min:-1',
                    'max_customers' => 'nullable|integer|min:-1',
                    'max_venders' => 'nullable|integer|min:-1',
                    'tax' => 'nullable|numeric|min:0',
                    'company_id' => 'nullable|exists:users,id',
                    'image' => 'nullable|file|max:20480|mimes:jpg,jpeg,png,webp',
                ];
            
                $request->validate($validation);
            
                $post = $request->only([
                    'price', 'duration', 'max_users', 'max_clients', 'max_customers',
                    'max_venders', 'storage_limit', 'tax', 'company_id', 'description', 'name', 'plan_type', 'features', 'backup_duration'
                ]);
                $post['max_clients'] = $post['max_clients'] ?? -1;
                $post['max_customers'] = $post['max_customers'] ?? -1;
                $post['max_venders'] = $post['max_venders'] ?? -1;
                $post['storage_limit'] = $post['storage_limit'] ?? 300000;
            
                foreach (['enable_project', 'enable_crm', 'enable_hrm', 'enable_account', 'enable_pos', 'enable_chatgpt', 'trial'] as $key) {
                    $post[$key] = $request->has($key) ? 1 : 0;
                }
                
                if($request->hasFile('image'))
                {
                    $filenameWithExt = $request->file('image')->getClientOriginalName();
                    $filename        = pathinfo($filenameWithExt, PATHINFO_FILENAME);
                    $extension       = $request->file('image')->getClientOriginalExtension();
                    $fileNameToStore = 'plan_' . time() . '.' . $extension;

                    $dir = storage_path('uploads/plan/');
                    if(!file_exists($dir))
                    {
                        mkdir($dir, 0777, true);
                    }
                    $path          = $request->file('image')->storeAs('uploads/plan/', $fileNameToStore);
                    $post['image'] = $fileNameToStore;
                }
            
                if($request->has('plan_type') && $request->plan_type == "common") {
                    if ($post['price'] != 0) {
                        $amount = (float) $post['price'];
                        $userCount = (int) 1;//$post['max_users'];
                        $tax = (float) $post['tax'];
            
                        $taxPercentage = $tax / 100;
                        $totalAmount = ($amount * $userCount) + ($amount * $userCount * $taxPercentage);
                        $post['total_amount'] = $totalAmount;
                    } else {
                        $post['total_amount'] = 0;
                    }
                } else {
                    if ($post['price'] != 0) {
                        $amount = (float) $post['price'];
                        $userCount = (int) $post['max_users'];
                        $tax = (float) $post['tax'];
            
                        $taxPercentage = $tax / 100;
                        $totalAmount = ($amount * $userCount) + ($amount * $userCount * $taxPercentage);
                        $post['total_amount'] = $totalAmount;
                    } else {
                        $post['total_amount'] = 0;
                    }
                }
                
                $features = $request->input('features', []);
                $post['features'] = json_encode($features);
            
                $plan = Plan::create($post);
            
                if ($plan) {
                        $plan->permissions()->sync($request->permissions);
                    return redirect()->route('general.plans.index')->with('success', __('Plan Successfully created.'));
                } else {
                    return redirect()->back()->with('error', __('Something went wrong.'));
                }

            }
            else
            {
                return redirect()->back()->with('error', __('Please enable at least one payment method.'));
            }
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

    }


    public function edit($plan_id)
    {
        if(\Auth::user()->can('edit plan'))
        {
            $arrDuration = Plan::$arrDuration;
            $plan        = Plan::find($plan_id);
            
            $companies = User::where('type', 'company')->pluck('company_name', 'id');
            $companies->prepend('Select Company', '');
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
            
            if(isset($plan->company_id)) {
                $activeUserCount = User::whereHas('roles', function ($query) {
                    $query->whereIn('name', ['stealth user', 'standard user']);
                })
                    ->join('employees', 'users.id', '=', 'employees.user_id')
                    ->where('users.created_by', $plan->company_id)
                    ->where('employees.is_active', 1)
                    ->count();
            } else {
                $activeUserCount = null;
            }
            

            return view('shared.plan.edit', compact('plan', 'arrDuration', 'companies', 'modules', 'permissions', 'activeUserCount'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }


    public function update(Request $request, $plan_id)
    {


        if(\Auth::user()->can('edit plan'))
        {

            $admin_payment_setting = Utility::getAdminPaymentSetting();

            if(!empty($admin_payment_setting) && ($admin_payment_setting['is_manually_payment_enabled'] == 'on'
                    || $admin_payment_setting['is_bank_transfer_enabled'] == 'on' || $admin_payment_setting['is_stripe_enabled'] == 'on'
                    || $admin_payment_setting['is_paypal_enabled'] == 'on' || $admin_payment_setting['is_paystack_enabled'] == 'on'
                    || $admin_payment_setting['is_flutterwave_enabled'] == 'on' || $admin_payment_setting['is_razorpay_enabled'] == 'on'
                    || $admin_payment_setting['is_mercado_enabled'] == 'on' || $admin_payment_setting['is_paytm_enabled'] == 'on'
                    || $admin_payment_setting['is_mollie_enabled'] == 'on' || $admin_payment_setting['is_skrill_enabled'] == 'on'
                    || $admin_payment_setting['is_coingate_enabled'] == 'on'|| $admin_payment_setting['is_paymentwall_enabled'] == 'on'
                    || $admin_payment_setting['is_toyyibpay_enabled'] == 'on' || $admin_payment_setting['is_payfast_enabled'] == 'on'
                    || $admin_payment_setting['is_iyzipay_enabled'] == 'on' || $admin_payment_setting['is_sspay_enabled'] == 'on'
                    || $admin_payment_setting['is_paytab_enabled'] == 'on'  || $admin_payment_setting['is_benefit_enabled'] == 'on'
                    || $admin_payment_setting['is_cashfree_enabled'] == 'on'  || $admin_payment_setting['is_aamarpay_enabled'] == 'on'
                    || $admin_payment_setting['is_paytr_enabled'] == 'on' || $admin_payment_setting['is_yookassa_enabled'] ='on'
                    || $admin_payment_setting['is_midtrans_enabled'] == 'on' || $admin_payment_setting['is_xendit_enabled'] == 'on'
                    || $admin_payment_setting['is_nepalste_enabled'] == 'on'))
            {
                $plan = Plan::find($plan_id);
                if(!empty($plan))
                {
                    
                    $validator = \Validator::make($request->all(), [
                        'duration'      => function ($attribute, $value, $fail) use ($plan_id) {
                            if ($plan_id != 1 && empty($value)) {
                                $fail($attribute . ' is required.');
                            }
                        },
                        'price'         => 'required|numeric|min:0',
                        'max_users'     => 'required|numeric|min:1',
                        'max_clients'   => 'nullable|integer|min:-1',
                        'max_customers' => 'nullable|integer|min:-1',
                        'max_venders'   => 'nullable|integer|min:-1',
                        'tax'           => 'nullable|numeric|min:0',
                        'company_id'    => 'nullable|exists:users,id',
                        'image'         => 'nullable|file|max:20480|mimes:jpg,jpeg,png,webp',
                    ]);
            
                    if ($validator->fails()) {
                        return redirect()->back()->with('error', $validator->errors()->first());
                    }
            
                    $post = $request->only([
                        'price', 'duration', 'max_users', 'max_clients', 'max_customers',
                        'max_venders', 'storage_limit', 'tax', 'company_id', 'trial_days', 'description', 'name', 'plan_type', 'features','backup_duration'
                    ]);
            
                    $post['max_clients'] = $post['max_clients'] ?? -1;
                    $post['max_customers'] = $post['max_customers'] ?? -1;
                    $post['max_venders'] = $post['max_venders'] ?? -1;
                    $post['storage_limit'] = $post['storage_limit'] ?? 300000;
            
                    if($request->has('plan_type') && $request->plan_type == "common") {
                        if ($post['price'] != 0) {
                            $amount = (float) $post['price'];
                            $userCount = (int) 1;//$post['max_users'];
                            $tax = (float) $post['tax'];
                
                            $taxPercentage = $tax / 100;
                            $totalAmount = ($amount * $userCount) + ($amount * $userCount * $taxPercentage);
                            
                            $post['total_amount'] = $totalAmount;
                        } else {
                            $post['total_amount'] = 0;
                        }
                    } else {
                       if ($post['price'] != 0) {
                            $amount = (float) $post['price'];
                            $userCount = (int) $post['max_users'];
                            $tax = (float) $post['tax'];
                
                            $taxPercentage = $tax / 100;
                            $totalAmount = ($amount * $userCount) + ($amount * $userCount * $taxPercentage);
                            $post['total_amount'] = $totalAmount;
                        } else {
                            $post['total_amount'] = 0;
                        }
                    }
                    
                    foreach (['enable_project', 'enable_crm', 'enable_hrm', 'enable_account', 'enable_pos', 'enable_chatgpt', 'trial'] as $key) {
                        $post[$key] = $request->has($key) ? 1 : 0;
                    }
            
                    if ($request->has('trial')) {
                        $post['trial_days'] = $request->input('trial_days');
                    } else {
                        $post['trial_days'] = null;
                    }

                    if($request->hasFile('image'))
                    {
                        $filenameWithExt = $request->file('image')->getClientOriginalName();
                        $filename        = pathinfo($filenameWithExt, PATHINFO_FILENAME);
                        $extension       = $request->file('image')->getClientOriginalExtension();
                        $fileNameToStore = 'plan_' . time() . '.' . $extension;

                        $dir = storage_path('uploads/plan/');
                        if(!file_exists($dir))
                        {
                            mkdir($dir, 0777, true);
                        }
                        $image_path = $dir . '/' . $plan->image;  // Value is not URL but directory file path
                        if(File::exists($image_path))
                        {

                            chmod($image_path, 0755);
                            File::delete($image_path);
                        }
                        $path = $request->file('image')->storeAs('uploads/plan/', $fileNameToStore);

                        $post['image'] = $fileNameToStore;
                    }
                    
                        
                    $features = $request->input('features', []);
                    $post['features'] = json_encode($features);

                    if($plan->update($post))
                    {
                        $plan->permissions()->sync($request->permissions);
                        
                        return redirect()->back()->with('success', __('Plan successfully updated.'));
                    }
                    else
                    {
                        return redirect()->back()->with('error', __('Something is wrong.'));
                    }
                }
                else
                {
                    return redirect()->back()->with('error', __('Plan not found.'));
                }


            }
            else
            {
                return redirect()->back()->with('error', __('Please enable at least one payment method.'));
            }
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

    }

    public function destroy(Request $request, $id)
    {
        $userPlan = User::where('plan' , $id)->first();
        if($userPlan != null)
        {
            return redirect()->back()->with('error',__('The company has subscribed to this plan, so it cannot be deleted.'));
        }
        $plan = Plan::find($id);
        if($plan->id == $id)
        {
            $plan->delete();

            return redirect()->back()->with('success' , __('Plan deleted successfully'));
        }
        else
        {
            return redirect()->back()->with('error',__('Something went wrong'));
        }
    }

    public function userPlan(Request $request)
    {
        $objUser = \Auth::user();
        $planID  = \Illuminate\Support\Facades\Crypt::decrypt($request->code);
        $plan    = Plan::find($planID);
        if($plan)
        {
            if($plan->price <= 0)
            {
                $objUser->assignPlan($plan->id);

                return redirect()->route('plans.index')->with('success', __('Plan successfully activated.'));
            }
            else
            {
                return redirect()->back()->with('error', __('Something is wrong.'));
            }
        }
        else
        {
            return redirect()->back()->with('error', __('Plan not found.'));
        }
    }

    public function planTrial(Request $request , $plan)
    {

        $objUser = \Auth::user();
        $planID  = \Illuminate\Support\Facades\Crypt::decrypt($plan);
        $plan    = Plan::find($planID);

        if($plan)
        {
            if($plan->price > 0)
            {
                $user = User::find($objUser->id);
                $user->trial_plan = $planID;
                $currentDate = date('Y-m-d');
                $numberOfDaysToAdd = $plan->trial_days;

                $newDate = date('Y-m-d', strtotime($currentDate . ' + ' . $numberOfDaysToAdd . ' days'));
                $user->trial_expire_date = $newDate;
                $user->save();

                $objUser->assignPlan($planID, 0);

                return redirect()->route('plans.index')->with('success', __('Plan successfully activated.'));
            }
            else
            {
                return redirect()->back()->with('error', __('Something is wrong.'));
            }
        }
        else
        {
            return redirect()->back()->with('error', __('Plan not found.'));
        }
    }

    public function planDisable(Request $request)
    {
        $userPlan = User::where('plan' , $request->id)->first();
        if($userPlan != null)
        {
            return response()->json(['error' =>__('The company has subscribed to this plan, so it cannot be disabled.')]);
        }

        Plan::where('id', $request->id)->update(['is_disable' => $request->is_disable]);

        if ($request->is_disable == 1) {
            return response()->json(['success' => __('Plan successfully enable.')]);

        } else {
            return response()->json(['success' => __('Plan successfully disable.')]);
        }
    }

}
