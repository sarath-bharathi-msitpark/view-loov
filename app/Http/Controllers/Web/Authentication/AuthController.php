<?php

namespace App\Http\Controllers\Web\Authentication;

use App\Http\Controllers\Controller;
use App\Models\ExperienceCertificate;
use App\Models\GenerateOfferLetter;
use App\Models\JoiningLetter;
use App\Models\LoginDetail;
use App\Models\NOC;
use App\Models\Plan;
use App\Models\User;
use App\Models\Utility;
use App\Providers\RouteServiceProvider;
use Carbon\Carbon;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

use App\Http\Controllers\Payment\CashfreeController;

class AuthController extends Controller
{
    /**
     * @param Request $request
     * @param $ref
     * @param $lang
     * @return Factory|View|Application|RedirectResponse|object
     */
    public function showRegisterForm(Request $request, $ref = '', $lang = '')
    {
        $settings = Utility::settings();

        if ($settings['enable_signup'] == 'on') {
            $langList = Utility::languages()->toArray();
            $lang = array_key_exists($lang, $langList) ? $lang : 'en';

            if ($lang == '') {
                $lang = Utility::getValByName('default_language');
            }
            App::setLocale($lang);
            if ($ref == '') {
                $ref = 0;
            }

            $refCode = User::where('referral_code', '=', $ref)->first();
            if (isset($refCode) && $refCode->referral_code != $ref) {
                return redirect()->route('register');
            }

            $plan = null;
            if ($request->plan) {
                $plan = $request->plan;
            }
            $commonPlans = Plan::select('id', 'name', 'duration', 'price', 'tax')->where('plan_type', 'common')->get()->map(function ($plan) {
                $plan->duration_label = Plan::$arrDuration[$plan->duration] ?? $plan->duration;
                return $plan;
            });
            return view('auth.register', compact('lang', 'ref', 'plan', 'commonPlans'));
        } else {
            return Redirect::to('login');
        }
    }

    /**
     * @param Request $request
     * @return Application|RedirectResponse|Redirector|object
     */
    public function register(Request $request)
    {
        $settings = Utility::settings();

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'company_name' => 'required|string|max:255|unique:users',
            'password' => ['nullable', 'string', 'min:8'],
            'terms' => 'required',
        ]);

        DB::beginTransaction();

        try {

            do {
                $code = rand(100000, 999999);
            } while (User::where('referral_code', $code)->exists());

            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'domain' => $request->domain,
                'company_name' => $request->company_name,
                'country_code' => $request->country_code,
                'mobile_no' => $request->mobile_no,
                'country' => $request->country,
                'type' => 'company',
                'default_pipeline' => 1,
                'plan' => null,
                'lang' => Utility::getValByName('default_language'),
                'avatar' => '',
                'referral_code' => $code,
                'used_referral_code' => $request->ref_code,
                'created_by' => 1,
                'payment_mode' => "cashfree",
            ]);

            // $user->syncPermissions([526, 527, 528, 529, 533, 534, 535, 537, 538, 540]);


            if (isset($settings['email_verification']) && $settings['email_verification'] == 'on') {
                try {
                    Utility::smtpDetail(1);

                    $user->sendEmailVerificationNotification();

                    $role_r = Role::findByName('administrator');
                    $user->assignRole($role_r);
                    // $user->userDefaultDataRegister($user->id);
                    // $user->userWarehouseRegister($user->id);
                    // $user->userDefaultBankAccount($user->id);

                    // Utility::chartOfAccountTypeData($user->id);
                    // Utility::chartOfAccountData1($user->id);
                    // Utility::pipeline_lead_deal_Stage($user->id);
                    // Utility::project_task_stages($user->id);
                    // Utility::labels($user->id);
                    // Utility::sources($user->id);
                    // Utility::jobStage($user->id);
                    // GenerateOfferLetter::defaultOfferLetterRegister($user->id);
                    // ExperienceCertificate::defaultExpCertificatRegister($user->id);
                    // JoiningLetter::defaultJoiningLetterRegister($user->id);
                    // NOC::defaultNocCertificateRegister($user->id);

                } catch (\Exception $e) {
                    $user->delete();
                    return redirect()->back()->with('status', __('Email SMTP settings are not configured. Please contact your site admin.'));
                }

                if (isset($request->plan) && Crypt::decrypt($request->plan) && Crypt::decrypt($request->plan) != 1) {
                    return redirect()->route('stripe', ['code' => $request->plan]);
                } else {
                    return redirect(RouteServiceProvider::COMPANY_HOME);
                }
            } else {
                $user->email_verified_at = now();
                $user->save();

                $role_r = Role::findByName('administrator');
                $user->assignRole($role_r);
                // $user->userDefaultDataRegister($user->id);
                // $user->userWarehouseRegister($user->id);
                // $user->userDefaultBankAccount($user->id);

                // Utility::chartOfAccountTypeData($user->id);
                // Utility::chartOfAccountData1($user->id);

                // GenerateOfferLetter::defaultOfferLetterRegister($user->id);
                // ExperienceCertificate::defaultExpCertificatRegister($user->id);
                // JoiningLetter::defaultJoiningLetterRegister($user->id);
                // NOC::defaultNocCertificateRegister($user->id);

                $userArr = [
                    'email' => $user->email,
                    'password' => $user->password,
                ];

                // $default_price = Utility::getAdminPaymentSetting()['default_price_per_user'] ?? 0;

                // if ($default_price != 0) {
                //     $amount = (float)$default_price;
                //     $userCount = (int)$request['max_users'];
                //     $tax = (float)18;

                //     $taxPercentage = $tax / 100;
                //     $totalAmount = ($amount * $userCount) + ($amount * $userCount * $taxPercentage);
                //     $total_amount = $totalAmount;
                // } else {
                //     $total_amount = 0;
                // }

                // $planData = [
                //     'company_id' => $user->id,
                //     'price' => $default_price,
                //     'duration' => 'month',
                //     'max_users' => $request->max_users,
                //     'tax' => 18,
                //     'total_amount' => $total_amount
                // ];
                // $plan = Plan::create($planData);

                // $newDate = date('Y-m-d', strtotime(date('Y-m-d') . ' + ' . 7 . ' days'));
                // $user->plan_expire_date = $newDate;
                // $user->plan = $plan->id;
                // $user->save();

                $companyId = $user->id;
                $maxUsers = (int)$request->max_users;

                $createdPlans = $this->createCompanyPlansFromCommon($companyId, $maxUsers);

                DB::commit();

                // Utility::sendUserEmailTemplate('new_user', [$user->id => $user->email], $userArr);

                if (isset($request->plan) && Crypt::decrypt($request->plan) && Crypt::decrypt($request->plan) != 1) {
                    return redirect()->route('stripe', ['code' => $request->plan]);
                } else {

                    if ($request->has('password') && !empty($request->password)) {
                        \Auth::login($user);
                        return redirect()->intended(RouteServiceProvider::COMPANY_HOME);
                    }
                    // return redirect(RouteServiceProvider::COMPANY_HOME);

                    return redirect()->route('auth.google.login');
                }
            }
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('status', __('Please try again'));
        }
    }

    /**
     * @param $lang
     * @return Factory|View|Application
     */
    public function showLoginForm($lang = '')
    {
        if ($lang == '') {
            $lang = Utility::getValByName('default_language');
        }

        $langList = Utility::languages()->toArray();
        $lang = array_key_exists($lang, $langList) ? $lang : 'en';

        App::setLocale($lang);

        $settings = Utility::settings();

        return view('auth.login', compact('lang', 'settings'));
    }

    public function adminLoginForm($lang = '')
    {
        if ($lang == '') {
            $lang = Utility::getValByName('default_language');
        }

        $langList = Utility::languages()->toArray();
        $lang = array_key_exists($lang, $langList) ? $lang : 'en';

        App::setLocale($lang);

        $settings = Utility::settings();

        return view('auth.adminlogin', compact('lang', 'settings'));
    }

    public function login(Request $request)
    {
        $user = User::where('email', $request->email)->first();
//        Log::info($user);

        if ($user) {
            $companyUser = User::find($user->created_by);
        }

        if ($user && $user->is_disable == 0 && !in_array($user->type, ['company', 'super admin'])) {
            return redirect()->back()->with('status', __('Your Account is disabled, please contact your Administrator.'));
        }

        if (
            ($user && $user->is_enable_login == 0) ||
            (isset($companyUser) && $companyUser && $companyUser->is_enable_login == 0)
            && $user->type != 'super admin'
        ) {
            return redirect()->back()->with('status', __('Your Account is disabled from company.'));
        }

        User::defaultEmail();

        if ($user) {
            $user->userDefaultDataRegister($user->id);
        }

        // Authentication
        if (!Auth::attempt($request->only('email', 'password'))) {
            return redirect()->back()->with('status', __('The provided credentials do not match our records.'));
        }

        $request->session()->regenerate();
        $user = Auth::user();

        $companyUser = User::find($user->created_by);
        $companyStatus = $companyUser ? $companyUser->delete_status : 1;

        // if ($user->delete_status == 0 || $companyStatus == 0) {
        //     auth()->logout();
        //     return redirect()->back()->with('status', __('Your Account is deleted by admin, please contact your Administrator.'));
        // }

        // if ($user->is_active == 0) {
        //     auth()->logout();
        //     return redirect()->back()->with('status', __('Your Account is deactivated by admin, please contact your Administrator.'));
        // }

        if ($user->type == 'company') {
            $plan = Plan::find($user->plan);

            if ($plan) {
                if ($plan->duration != 'lifetime') {
                    $planExpiry = new \DateTime($user->plan_expire_date);
                    $today = new \DateTime(date('Y-m-d'));
                    $days = $today->diff($planExpiry)->format('%r%a');

                    if ($days <= 0) {
                        return redirect()->intended(RouteServiceProvider::COMPANY_HOME)->with('error', __('Your Plan is expired.'));
                    }
                }

                if ($user->trial_expire_date && $user->trial_expire_date < date('Y-m-d')) {
                    return redirect()->intended(RouteServiceProvider::COMPANY_HOME)->with('error', __('Your Trial plan expired.'));
                }
            }
        }

        $setting = Utility::settingsById($user->creatorId());
        $timezone = !empty($setting['timezone']) ? $setting['timezone'] : 'UTC';
        date_default_timezone_set($timezone);

        // Update last login time
        $user->update([
            'last_login_at' => Carbon::now()->toDateTimeString(),
        ]);

        // Log login details for non-company/non-super admin users
        if (!in_array($user->type, ['company', 'super admin'])) {
            $ip = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
            $query = @unserialize(file_get_contents('http://ip-api.com/php/' . $ip));

            $whichbrowser = new \WhichBrowser\Parser($_SERVER['HTTP_USER_AGENT']);
            if ($whichbrowser->device->type != 'bot') {
                $referrer = isset($_SERVER['HTTP_REFERER']) ? parse_url($_SERVER['HTTP_REFERER']) : null;

                $query['browser_name'] = $whichbrowser->browser->name ?? null;
                $query['os_name'] = $whichbrowser->os->name ?? null;
                $query['browser_language'] = $_SERVER['HTTP_ACCEPT_LANGUAGE'][0] ?? null;
                // $query['device_type'] = get_device_type($_SERVER['HTTP_USER_AGENT']);
                $query['referrer_host'] = $referrer['host'] ?? null;
                $query['referrer_path'] = $referrer['path'] ?? null;

                isset($query['timezone']) ? date_default_timezone_set($query['timezone']) : '';

                $login_detail = new LoginDetail();
                $login_detail->user_id = $user->id;
                $login_detail->ip = $ip;
                $login_detail->date = now();
                $login_detail->Details = json_encode($query);
                $login_detail->created_by = $user->creatorId();
                $login_detail->save();
            }
        }

        if ($user->type === 'super admin') {
            return redirect()->intended(RouteServiceProvider::SUPERADMIN_HOME);
        }

        if ($user->type === 'staff') {
            return redirect()->intended(RouteServiceProvider::STAFF_HOME);
        }

        if (in_array($user->type, ['company', 'Employee_Admin'])) {
            if (!$request->has('password')) {
                return redirect()->route('auth.google.login');
            }

            return redirect()->intended(RouteServiceProvider::COMPANY_HOME);
        }

        if (in_array($user->type, ['Employee'])) {
            if (!$request->has('password')) {
                return redirect()->route('auth.google.login');
            }

            return redirect()->intended(RouteServiceProvider::EMPLOYEE_HOME);
        }

        // Redirect based on user type
//        if ($user->type == 'super admin' || $user->type == 'staff') {
//            if ($user->type == 'staff') {
//                return redirect()->intended(RouteServiceProvider::STAFF_HOME);
//            }
//            return redirect()->intended(RouteServiceProvider::SUPERADMIN_HOME);
//        } elseif ($user->type == 'company' || $user->type == 'Employee_Admin') {
//            if (!$request->has('password')) {
//                return redirect()->route('auth.google.login');
//            } else {
//                return redirect()->intended(RouteServiceProvider::COMPANY_HOME);
//            }
//        } else {
//            if (!$request->has('password')) {
//                return redirect()->route('auth.google.login');
//            } elseif ($user->hasRole('stealth user')) {
//                return redirect()->intended(RouteServiceProvider::COMPANY_HOME);
//            } else {
//                return redirect()->intended(RouteServiceProvider::EMPLOYEE_HOME);
//            }
//        }
    }

    /**
     * @param Request $request
     * @return Application|RedirectResponse|Redirector|object
     */
    public function logout(Request $request)
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }

    public function createCompanyPlansFromCommon(int $companyId, int $maxUsers)
    {
        $commonPlans = Plan::where('plan_type', 'common')->get();
        $createdPlans = collect();

        foreach ($commonPlans as $commonPlan) {
            $pricePerUser = (float)$commonPlan->price;
            $taxPercent = (float)$commonPlan->tax;

            $subtotal = $pricePerUser * $maxUsers;
            $taxAmount = $subtotal * ($taxPercent / 100);
            $totalAmount = $subtotal + $taxAmount;

            $planData = [
                'company_id' => $companyId,
                'name' => $commonPlan->name,
                'price' => $pricePerUser,
                'duration' => $commonPlan->duration,
                'features' => $commonPlan->features,
                'description' => $commonPlan->description,
                'max_users' => $maxUsers,
                'tax' => $taxPercent,
                'total_amount' => $totalAmount,
                'plan_type' => 'company',
            ];

            $newPlan = Plan::create($planData);

            if (method_exists($commonPlan, 'permissions')) {
                $permissions = $commonPlan->permissions->pluck('id')->toArray();
                $newPlan->permissions()->sync($permissions);
            }

            // $cashfreeController = new CashfreeController();
            // $cashfreeController->createCashfreePlan($newPlan);

            $createdPlans->push($newPlan);
        }

        return $createdPlans;
    }

}
