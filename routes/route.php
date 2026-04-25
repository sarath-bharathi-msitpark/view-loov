<?php

use App\Http\Controllers\Web\Admin\DashboardController;
use App\Http\Controllers\Web\Admin\SystemController;
use App\Http\Controllers\Web\Admin\AiTemplateController;
use App\Http\Controllers\Web\Admin\UserController;
use App\Http\Controllers\Web\Authentication\AuthController;
use App\Http\Controllers\Web\Authentication\SocialAuthController;
use App\Http\Controllers\Web\Shared\StripePaymentController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Web\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Web\Shared\PlanController;
use App\Http\Controllers\Web\Shared\PlanRequestController;
use App\Http\Controllers\Web\Shared\CouponController;
use App\Http\Controllers\Web\Admin\BlogCategoryController;
use App\Http\Controllers\Web\Admin\BlogController;
use App\Http\Controllers\Web\Admin\RoleController;
use App\Http\Controllers\Web\Admin\OtherUserController;
use App\Http\Controllers\Web\LandingPage\PageController;
use App\Http\Controllers\Web\LandingPage\BlogPageController;
use App\Http\Controllers\Web\Company\LeadController;
use App\Http\Controllers\Web\Company\LeadStageController;


use App\Http\Controllers\Web\Shared\SubscriptionController;

Route::get('/subscribe', [SubscriptionController::class, 'showCheckout'])->name('subscribe.show');
Route::post('/subscribe', [SubscriptionController::class, 'createCheckoutSession'])->name('subscribe.create');
Route::get('/success', [SubscriptionController::class, 'success'])->name('subscribe.success');
Route::get('/cancel', [SubscriptionController::class, 'cancel'])->name('subscribe.cancel');

// Manage subscriptions
Route::get('/subscription/manage', [SubscriptionController::class, 'manage'])->name('subscription.manage');
Route::post('/subscription/change', [SubscriptionController::class, 'change'])->name('subscription.change');
Route::post('/subscription/cancel', [SubscriptionController::class, 'cancelSubscription'])->name('subscription.cancel');


require __DIR__ . '/auth.php';

Route::get('/new-forgot-password/{lang?}', [AuthenticatedSessionController::class, 'showLinkRequestForm'])
    ->middleware('guest')
    ->name('password.request');

Route::get('/sign-in/{lang?}', [AuthController::class, 'showLoginForm'])->middleware('guest')->name('signin');

Route::get('/sign-up/{lang?}', [AuthController::class, 'showRegisterForm'])->middleware('guest')->name('signup');

Route::get('/terms-and-conditions', [PageController::class, 'terms'])->name('terms');
Route::get('/privacy-policy', [PageController::class, 'privacy_policy'])->name('privacy.policy');

Route::prefix('blogs')->middleware(['auth', 'XSS', 'revalidate'])->name('blogs.')->group(function () {
    Route::get('/', [BlogPageController::class, 'index'])->name('index');
    Route::get('/{category_slug}', [BlogPageController::class, 'categoryDetails'])->name('category');
    Route::get('/{category_slug}/{blog_slug}', [BlogPageController::class, 'blogDetails'])->name('details');
});

Route::prefix('auth')->name('auth.')->group(function () {

    // Login
    Route::get('login', [AuthController::class, 'showLoginForm'])->middleware(['guest'])->name('login');
    Route::post('login', [AuthController::class, 'login'])->middleware(['guest'])->name('login.verify');

    Route::get('admin/login', [AuthController::class, 'adminLoginForm'])->middleware(['guest'])->name('adminlogin');

    // Register
    Route::get('register', [AuthController::class, 'showRegisterForm'])->middleware(['guest'])->name('register');
    Route::post('register', [AuthController::class, 'register'])->middleware(['guest'])->name('register.store');

    Route::get('auth/google', [SocialAuthController::class, 'redirectToGoogle'])->middleware(['guest'])->name('google.login');
    Route::get('google/callback', [SocialAuthController::class, 'handleGoogleCallback'])->middleware(['guest']);

    // Logout
    Route::post('logout', [AuthController::class, 'logout'])->name('logout');
});

Route::prefix('general')->middleware(['auth', 'XSS', 'revalidate'])->name('general.')->group(function () {

    Route::get('/dashboard', [DashboardController::class, 'staffDashboard'])->name('dashboard');


    Route::get('profile/change-password', [UserController::class, 'changePassword'])->name('change.password');
    Route::post('change-password', [UserController::class, 'updatePassword'])->name('update.password');

    /** Start Subscription **/
    Route::resource('plans', PlanController::class);
    Route::get('plan-trial/{id}', [PlanController::class, 'planTrial'])->name('plan.trial');
    Route::post('plan-disable', [PlanController::class, 'planDisable'])->name('plan.disable');
    Route::resource('coupons', CouponController::class);

    // Plan Request Module
    Route::get('plan_request', [PlanRequestController::class, 'index'])->name('plan_request.index');
    Route::get('request_frequency/{id}', [PlanRequestController::class, 'requestView'])->name('request.view');
    Route::get('request_send/{id}', [PlanRequestController::class, 'userRequest'])->name('send.request');
    Route::get('request_response/{id}/{response}', [PlanRequestController::class, 'acceptRequest'])->name('response.request');
    Route::get('request_cancel/{id}', [PlanRequestController::class, 'cancelRequest'])->name('request.cancel');
    Route::get('request_send_trail/{id}', [PlanRequestController::class, 'userRequestTrail'])->name('send.request.trail');
    Route::get('plan/license/increase/{id}', [PlanRequestController::class, 'increaseLicense'])->name('request.license');

    // Orders
    Route::get('/orders', [StripePaymentController::class, 'index'])->name('order.index');
    Route::get('/refund/{id}/{user_id}', [StripePaymentController::class, 'refund'])->name('order.refund');
    Route::get('/stripe/{code}/{mode?}', [StripePaymentController::class, 'stripe'])->name('stripe');
    Route::post('/stripe', [StripePaymentController::class, 'stripePost'])->name('stripe.post');
    Route::get('/apply-coupon', [CouponController::class, 'applyCoupon'])->name('apply.coupon');
    /** End Subscription **/

    Route::resource('blog-categories', BlogCategoryController::class);
    Route::resource('blogs', BlogController::class);

});

Route::prefix('admin')->middleware(['auth', 'XSS', 'revalidate', 'role:super admin'])->name('admin.')->group(function () {

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    /** Start Settings  **/
    Route::resource('systems', SystemController::class);
    Route::post('email-settings', [SystemController::class, 'saveEmailSettings'])->name('email.settings');
    Route::post('company-email-settings', [SystemController::class, 'saveCompanyEmailSettings'])->name('company.email.settings');
    Route::post('company-settings', [SystemController::class, 'saveCompanySettings'])->name('company.settings');
    Route::post('system-settings', [SystemController::class, 'saveSystemSettings'])->name('system.settings');
    Route::post('chatgpt-settings', [SystemController::class, 'chatgptSetting'])->name('chatgpt.settings');
    Route::post('downloadlink-settings', [SystemController::class, 'downloadlinkSetting'])->name('downloadlink.settings');

    Route::post('zoom-settings', [SystemController::class, 'saveZoomSettings'])->name('zoom.settings');
    Route::post('tracker-settings', [SystemController::class, 'saveTrackerSettings'])->name('tracker.settings');
    Route::post('slack-settings', [SystemController::class, 'saveSlackSettings'])->name('slack.settings');
    Route::post('telegram-settings', [SystemController::class, 'saveTelegramSettings'])->name('telegram.settings');
    Route::post('twilio-settings', [SystemController::class, 'saveTwilioSettings'])->name('twilio.setting');
    Route::get('print-setting', [SystemController::class, 'printIndex'])->name('print.setting');
    Route::get('settings', [SystemController::class, 'companyIndex'])->name('settings')->middleware(['XSS']);
    Route::post('business-setting', [SystemController::class, 'saveBusinessSettings'])->name('business.setting');
    Route::post('company-payment-setting', [SystemController::class, 'saveCompanyPaymentSettings'])->name('company.payment.settings');
    Route::post('currency-settings', [SystemController::class, 'saveCurrencySettings'])->name('currency.settings');
    Route::post('company-preview', [SystemController::class, 'currencyPreview'])->name('currency.preview');
    Route::any('test-mail', [SystemController::class, 'testMail'])->name('testing.mail');
    Route::post('test-mail/send', [SystemController::class, 'testSendMail'])->name('testing.send.mail');
    Route::post('stripe-settings', [SystemController::class, 'savePaymentSettings'])->name('payment.settings');
    Route::post('pusher-setting', [SystemController::class, 'savePusherSettings'])->name('pusher.setting');
    Route::post('recaptcha-settings', [SystemController::class, 'recaptchaSettingStore'])->name('recaptcha.settings.store')->middleware(['auth', 'XSS']);
    Route::post('seo-settings', [SystemController::class, 'seoSettings'])->name('seo.settings.store')->middleware(['auth', 'XSS']);
    Route::any('webhook-settings', [SystemController::class, 'webhook'])->name('webhook.settings')->middleware(['auth', 'XSS']);
    Route::get('webhook-settings/create', [SystemController::class, 'webhookCreate'])->name('webhook.create')->middleware(['auth', 'XSS']);
    Route::post('webhook-settings/store', [SystemController::class, 'webhookStore'])->name('webhook.store');
    Route::get('webhook-settings/{wid}/edit', [SystemController::class, 'webhookEdit'])->name('webhook.edit')->middleware(['auth', 'XSS']);
    Route::post('webhook-settings/{wid}/edit', [SystemController::class, 'webhookUpdate'])->name('webhook.update')->middleware(['auth', 'XSS']);
    Route::delete('webhook-settings/{wid}', [SystemController::class, 'webhookDestroy'])->name('webhook.destroy')->middleware(['auth', 'XSS']);
    Route::post('cookie-setting', [SystemController::class, 'saveCookieSettings'])->name('cookie.setting');
    Route::post('cache-settings', [SystemController::class, 'cacheSettingStore'])->name('cache.settings.store')->middleware(['auth', 'XSS']);

    //Storage Setting
    Route::post('storage-settings', [SystemController::class, 'storageSettingStore'])->name('storage.setting.store')->middleware(['auth', 'XSS']);
    Route::get('generate/{template_name}', [AiTemplateController::class, 'create'])->name('generate');
    Route::post('generate/keywords/{id}', [AiTemplateController::class, 'getKeywords'])->name('generate.keywords');
    Route::post('generate/response', [AiTemplateController::class, 'AiGenerate'])->name('generate.response');

    //AI module for grammar check
    Route::get('grammar/{template}', [AiTemplateController::class, 'grammar'])->name('grammar')->middleware(['auth', 'XSS']);
    Route::post('grammar/response', [AiTemplateController::class, 'grammarProcess'])->name('grammar.response')->middleware(['auth', 'XSS']);
    /** End Settings  **/

    // Company users
    Route::resource('users', UserController::class);
    Route::any('user-reset-password/{id}', [UserController::class, 'userPassword'])->name('users.reset');
    Route::post('user-reset-password/{id}', [UserController::class, 'userPasswordReset'])->name('user.password.update');
    Route::get('company-info/{id}', [UserController::class, 'companyInfo'])->name('company.info');
    Route::post('user-unable', [UserController::class, 'userUnable'])->name('user.unable');
    Route::get('user/{id}/plan', [UserController::class, 'upgradePlan'])->name('plan.upgrade');
    Route::get('user/{id}/plan/{pid}', [UserController::class, 'activePlan'])->name('plan.active');
    Route::get('users/{id}/login-with-company', [UserController::class, 'LoginWithCompany'])->name('login.with.company');

    Route::get('user-login/{id}', [UserController::class, 'LoginManage'])->name('users.login');

    Route::get('get-active-users-for-debug/{id}', [\App\Http\Controllers\Web\Admin\DebugModeController::class, 'index'])->name('debug.index');
    Route::post('debug-user-status-update/{id}', [\App\Http\Controllers\Web\Admin\DebugModeController::class, 'updateDebugMode'])->name('debug.toggleDebugMode');

    Route::resource('other-users', OtherUserController::class);
    Route::post('other-users/change-password', [OtherUserController::class, 'updatePassword'])->name('otheruser.update.password');
    Route::any('other-user-reset-password/{id}', [OtherUserController::class, 'userPassword'])->name('otheruser.reset');
    Route::post('other-user-reset-password/{id}', [OtherUserController::class, 'userPasswordReset'])->name('otheruser.password.update');
    Route::post('other-user-unable', [OtherUserController::class, 'userUnable'])->name('otheruser.unable');
    Route::get('other-user-login/{id}', [OtherUserController::class, 'LoginManage'])->name('otheruser.login');

    Route::resource('roles', RoleController::class);
});

Route::prefix('organization')->middleware(['auth', 'plan'])->name('organization.')->group(function () {

    // Dashboard
    Route::group(['middleware' => ['role_or_permission:administrator|dashboard']], function () {
        Route::get('/dashboard', [\App\Http\Controllers\Web\Company\DashboardController::class, 'index'])->name('dashboard');
        Route::post('/update-apk-update-notify', [\App\Http\Controllers\Web\Company\DashboardController::class, 'updateAPKUpdateNotify'])->name('update.apk.update.notify');
    });

    // Impersonate
    Route::group(['middleware' => ['role_or_permission:administrator|settings|company_setting_user']], function () {
        Route::get('/exit-with-company/exit', [UserController::class, 'ExitCompany'])->name('exit.company')->withoutMiddleware('plan');
    });

    // Screenshots
    Route::group(['prefix' => 'screenshot', 'middleware' => ['role_or_permission:administrator|screenshot']], function () {
        Route::get('/', [\App\Http\Controllers\Web\Company\ScreenshotController::class, 'index'])->name('screenshot.index');
        Route::post('/toggle-highlight/{id}', [\App\Http\Controllers\Web\Company\ScreenshotController::class, 'toggleHighlight'])->name('screenshot.toggle-highlight');

        Route::get('/organization/screenshot/incidents/{employee}', [\App\Http\Controllers\Web\Company\ScreenshotController::class, 'fetchIncidents'])
            ->name('screenshot.fetch-incidents');
    });

    // Live Screen shot
    Route::group(['prefix' => 'live-screenshot', 'middleware' => ['role_or_permission:administrator|live_shot']], function () {
        Route::get('/', [\App\Http\Controllers\Web\Company\LivestreamController::class, 'screenshotIndex'])->name('live_screenshot.index');
        Route::post('/request-screenshot', [\App\Http\Controllers\Web\Company\LivestreamController::class, 'requestImage'])->name('live_screenshot.getLive');
        Route::get('/check-status', [\App\Http\Controllers\Web\Company\LivestreamController::class, 'checkScreenshotStatus'])->name('live_screenshot.checkStatus');
    });

    // Live Web - Cam shot
    Route::group(['prefix' => 'live-cam-shot', 'middleware' => ['role_or_permission:administrator|live_cam_shot']], function () {
        Route::get('/', [\App\Http\Controllers\Web\Company\LivestreamController::class, 'webCamShotIndex'])->name('live_cam_shot.index');
    });

    // Apps & URL
    Route::group(['prefix' => 'apps-and-url', 'middleware' => ['role_or_permission:administrator|apps_and_urls']], function () {
        Route::get('/', [\App\Http\Controllers\Web\Company\AppsAndUrlController::class, 'index'])->name('apps_and_urls.index');
    });

    // Reports
    Route::group(['prefix' => 'report', 'middleware' => ['role_or_permission:administrator|reports|break_report|daily_attendance_report|activity_report|apps_and_urls_report|highlights_report']], function () {
        Route::get('/', [\App\Http\Controllers\Web\Company\ReportController::class, 'index'])->name('report.index');
        Route::get('/break', [\App\Http\Controllers\Web\Company\ReportController::class, 'breakReport'])->name('report.break');
        Route::get('/attendance', [\App\Http\Controllers\Web\Company\ReportController::class, 'attendanceReport'])->name('report.attendance');
        Route::get('/today-attendance', [\App\Http\Controllers\Web\Company\ReportController::class, 'todayAttendanceReport'])->name('report.today.attendance');
        Route::get('/attendance-employee/{id}', [\App\Http\Controllers\Web\Company\ReportController::class, 'attendanceReportIndividual'])->name('report.attendance.individual');
        Route::get('/activity', [\App\Http\Controllers\Web\Company\ReportController::class, 'activityReport'])->name('report.activity');
        Route::get('/apps-and-urls', [\App\Http\Controllers\Web\Company\ReportController::class, 'appsAndUrlsReport'])->name('report.apps_and_urls');
        Route::get('/highlight', [\App\Http\Controllers\Web\Company\ReportController::class, 'highlight'])->name('report.highlight');
    });

    // Settings
    Route::group(['prefix' => 'setting', 'middleware' => ['role_or_permission:administrator|settings']], function () {

        // Administrators
        Route::group(['prefix' => 'administrator', 'middleware' => ['role_or_permission:administrator|settings|company_setting_admin']], function () {
            Route::get('', [\App\Http\Controllers\Web\Company\AdministratorSettingsController::class, 'index'])->name('setting.administrator.index');
            Route::post('/store', [\App\Http\Controllers\Web\Company\AdministratorSettingsController::class, 'store'])->name('setting.administrator.store');
            Route::put('/update/{id}', [\App\Http\Controllers\Web\Company\AdministratorSettingsController::class, 'update'])->name('setting.administrator.update');
            Route::get('/download', [\App\Http\Controllers\Web\Company\AdministratorSettingsController::class, 'download'])->name('setting.administrator.download');
            Route::post('/{id}/toggle-active', [\App\Http\Controllers\Web\Company\UserController::class, 'toggleActive'])->name('setting.administrator.toggle_active');
        });

        // User
        Route::group(['prefix' => 'user', 'middleware' => ['role_or_permission:administrator|settings|company_setting_user']], function () {
            Route::get('/', [\App\Http\Controllers\Web\Company\UserController::class, 'index'])->name('settings.user');
            Route::post('/store', [\App\Http\Controllers\Web\Company\UserController::class, 'store'])->name('settings.user.store');
            Route::get('/edit/{id}', [\App\Http\Controllers\Web\Company\UserController::class, 'edit'])->name('settings.user.edit');
            Route::put('/update/{id}', [\App\Http\Controllers\Web\Company\UserController::class, 'update'])->name('settings.user.update');
            Route::delete('/delete/{id}', [\App\Http\Controllers\Web\Company\UserController::class, 'destroy'])->name('settings.user.destroy');
            Route::get('/download', [\App\Http\Controllers\Web\Company\UserController::class, 'download'])->name('settings.user.download');
            Route::post('/{id}/toggle-active', [\App\Http\Controllers\Web\Company\UserController::class, 'toggleActive'])->name('settings.user.toggleactive');
            Route::post('/bulk-status-update', [\App\Http\Controllers\Web\Company\UserController::class, 'bulkStatusUpdate'])->name('settings.user.bulk-status-update');
        });

        // Role
        Route::group(['prefix' => 'role', 'middleware' => ['role_or_permission:administrator|settings|company_setting_roles']], function () {
            Route::get('/', [\App\Http\Controllers\Web\Company\RoleController::class, 'index'])->name('settings.role');
            Route::get('/create', [\App\Http\Controllers\Web\Company\RoleController::class, 'create'])->name('settings.role.create');
            Route::post('/store', [\App\Http\Controllers\Web\Company\RoleController::class, 'store'])->name('settings.role.store');
            Route::get('/edit/{id}', [\App\Http\Controllers\Web\Company\RoleController::class, 'edit'])->name('settings.role.edit');
            Route::put('/update/{id}', [\App\Http\Controllers\Web\Company\RoleController::class, 'update'])->name('settings.role.update');
            Route::delete('/destroy/{id}', [\App\Http\Controllers\Web\Company\RoleController::class, 'destroy'])->name('settings.role.destroy');
        });

        // Team
        Route::group(['prefix' => 'team', 'middleware' => ['role_or_permission:administrator|settings|company_setting_teams']], function () {
            Route::get('/', [\App\Http\Controllers\Web\Company\TeamController::class, 'index'])->name('settings.team');
            Route::post('/store', [\App\Http\Controllers\Web\Company\TeamController::class, 'store'])->name('settings.team.store');
            Route::get('/edit/{id}', [\App\Http\Controllers\Web\Company\TeamController::class, 'edit'])->name('settings.team.edit');
            Route::post('/update/{id}', [\App\Http\Controllers\Web\Company\TeamController::class, 'update'])->name('settings.team.update');
            Route::delete('/destroy/{id}', [\App\Http\Controllers\Web\Company\TeamController::class, 'destroy'])->name('settings.team.destroy');
            Route::post('/{team}/update-shift', [\App\Http\Controllers\Web\Company\TeamController::class, 'updateShift'])->name('settings.team.updateshift');
            Route::post('/{team}/update-policy', [\App\Http\Controllers\Web\Company\TeamController::class, 'updatePolicy'])->name('settings.team.updatepolicy');
            Route::post('/{team}/show-team-settings', [\App\Http\Controllers\Web\Company\TeamController::class, 'showTeamSettings'])->name('settings.teams.showteamsettings');
            Route::put('/{id}', [\App\Http\Controllers\Web\Company\TeamController::class, 'teamtrackUpdate'])->name('settings.team.teamtrackupdate');
        });

        // Designation
        Route::group(['prefix' => 'designation', 'middleware' => ['role_or_permission:administrator|settings|company_setting_designation']], function () {
            Route::get('/', [\App\Http\Controllers\Web\Company\DesignationController::class, 'index'])->name('settings.designation');
            Route::post('/store', [\App\Http\Controllers\Web\Company\DesignationController::class, 'store'])->name('settings.designation.store');
            Route::get('/edit/{id}', [\App\Http\Controllers\Web\Company\DesignationController::class, 'edit'])->name('settings.designation.edit');
            Route::post('/update/{id}', [\App\Http\Controllers\Web\Company\DesignationController::class, 'update'])->name('settings.designation.update');
        });

        // Shift
        Route::group(['prefix' => 'shift', 'middleware' => ['role_or_permission:administrator|settings|company_setting_shifts']], function () {
            Route::get('/', [\App\Http\Controllers\Web\Company\ShiftController::class, 'index'])->name('settings.shift');
            Route::post('/store', [\App\Http\Controllers\Web\Company\ShiftController::class, 'store'])->name('settings.shift.store');
            Route::get('/edit/{id}', [\App\Http\Controllers\Web\Company\ShiftController::class, 'edit'])->name('settings.shift.edit');
            Route::post('/update/{id}', [\App\Http\Controllers\Web\Company\ShiftController::class, 'update'])->name('settings.shift.update');
        });

        // Workplace
        Route::group(['prefix' => 'workplace', 'middleware' => ['role_or_permission:administrator|settings|company_setting_workspace']], function () {
            Route::get('/', [\App\Http\Controllers\Web\Company\WorkPlaceController::class, 'index'])->name('settings.workplace');
            Route::post('/update', [\App\Http\Controllers\Web\Company\WorkPlaceController::class, 'update'])->name('settings.workplace.update');
        });

        // Break
        Route::group(['prefix' => 'break', 'middleware' => ['role_or_permission:administrator|settings|company_setting_break']], function () {
            Route::get('/', [\App\Http\Controllers\Web\Company\BreakController::class, 'index'])->name('settings.break');
            Route::post('/store', [\App\Http\Controllers\Web\Company\BreakController::class, 'store'])->name('settings.break.store');
            Route::get('/edit/{id}', [\App\Http\Controllers\Web\Company\BreakController::class, 'edit'])->name('settings.break.edit');
            Route::put('/update/{id}', [\App\Http\Controllers\Web\Company\BreakController::class, 'update'])->name('settings.break.update');
            Route::delete('/{break}/destroy', [\App\Http\Controllers\Web\Company\BreakController::class, 'destroy'])->name('settings.break.destroy');
        });
    });

    // Impersonate As Employee
    Route::get('users/{id}/login-with-employee', [\App\Http\Controllers\Web\Company\UserController::class, 'LoginWithStandardUser'])->name('login.with.standard-user');
    Route::get('/exit-with-employee/exit', [\App\Http\Controllers\Web\Company\UserController::class, 'exitEmployee'])->name('exit.employee');

    // Project Management
    Route::group(['prefix' => 'project-management'], function () {
        Route::resource('projects', \App\Http\Controllers\Web\Company\ProjectController::class);
        Route::get('project/{view?}', [\App\Http\Controllers\Web\Company\ProjectController::class, 'index'])->name('projects.list');
        Route::post('invite-project-user-member', [\App\Http\Controllers\Web\Company\ProjectController::class, 'inviteProjectUserMember'])->name('invite.project.user.member');
        Route::get('invite-project-member/{id}', [\App\Http\Controllers\Web\Company\ProjectController::class, 'inviteMemberView'])->name('invite.project.member.view')->middleware(['auth', 'XSS']);
        Route::get('projects-users', [\App\Http\Controllers\Web\Company\ProjectController::class, 'loadUser'])->name('project.user');
        Route::delete('projects/{id}/users/{uid}', [\App\Http\Controllers\Web\Company\ProjectController::class, 'destroyProjectUser'])->name('projects.user.destroy')->middleware(['auth', 'XSS']);
        Route::get('projects-view', [\App\Http\Controllers\Web\Company\ProjectController::class, 'filterProjectView'])->name('filter.project.view');
    });

    // Task Management
    Route::group(['prefix' => 'project-management/task'], function () {
        Route::get('/projects/{id}/task', [\App\Http\Controllers\Web\Company\ProjectTaskController::class, 'index'])->name('projects.tasks.index');

        Route::get('/projects/{pid}/task/{sid}', [\App\Http\Controllers\Web\Company\ProjectTaskController::class, 'create'])->name('projects.tasks.create')->middleware(['auth', 'XSS']);
        Route::post('/projects/{pid}/task/{sid}', [\App\Http\Controllers\Web\Company\ProjectTaskController::class, 'store'])->name('projects.tasks.store')->middleware(['auth', 'XSS']);
        Route::get('/projects/{id}/task/{tid}/show-detail', [\App\Http\Controllers\Web\Company\ProjectTaskController::class, 'showDetail'])->name('projects.tasks.showDetail')->middleware(['auth', 'XSS']);
        Route::get('/projects/{id}/task/{tid}/edit', [\App\Http\Controllers\Web\Company\ProjectTaskController::class, 'edit'])->name('projects.tasks.edit')->middleware(['auth', 'XSS']);
        Route::post('/projects/{id}/task/update/{tid}', [\App\Http\Controllers\Web\Company\ProjectTaskController::class, 'update'])->name('projects.tasks.update')->middleware(['auth', 'XSS']);
        Route::delete('/projects/{id}/task/{tid}', [\App\Http\Controllers\Web\Company\ProjectTaskController::class, 'destroy'])->name('projects.tasks.destroy')->middleware(['auth', 'XSS']);

        Route::get('/projects/{id}/task/{tid}/show', [\App\Http\Controllers\Web\Company\ProjectTaskController::class, 'show'])->name('projects.tasks.show')->middleware(['auth', 'XSS']);
        Route::patch('/projects/{id}/task/order', [\App\Http\Controllers\Web\Company\ProjectTaskController::class, 'taskOrderUpdate'])->name('tasks.update.order')->middleware(['auth', 'XSS']);
        Route::patch('update-task-priority-color', [\App\Http\Controllers\Web\Company\ProjectTaskController::class, 'updateTaskPriorityColor'])->name('update.task.priority.color')->middleware(['auth', 'XSS']);

        Route::post('/projects/{id}/checklist/{tid}', [\App\Http\Controllers\Web\Company\ProjectTaskController::class, 'checklistStore'])->name('checklist.store');
        Route::post('/projects/{id}/checklist/update/{cid}', [\App\Http\Controllers\Web\Company\ProjectTaskController::class, 'checklistUpdate'])->name('checklist.update');
        Route::delete('/projects/{id}/checklist/{cid}', [\App\Http\Controllers\Web\Company\ProjectTaskController::class, 'checklistDestroy'])->name('checklist.destroy');

        Route::post('/projects/{id}/comment/{tid}/file', [\App\Http\Controllers\Web\Company\ProjectTaskController::class, 'commentStoreFile'])->name('comment.store.file')->middleware(['auth', 'XSS']);
        Route::delete('/projects/{id}/comment/{tid}/file/{fid}', [\App\Http\Controllers\Web\Company\ProjectTaskController::class, 'commentDestroyFile'])->name('comment.destroy.file');

        Route::post('/projects/{id}/comment/{tid}', [\App\Http\Controllers\Web\Company\ProjectTaskController::class, 'commentStore'])->name('task.comment.store');
        Route::delete('/projects/{id}/comment/{tid}/{cid}', [\App\Http\Controllers\Web\Company\ProjectTaskController::class, 'commentDestroy'])->name('comment.destroy');

        Route::patch('/organization/tasks/change-stage/{project}', [\App\Http\Controllers\Web\Company\ProjectTaskController::class, 'changeStage'])
            ->name('tasks.change.stage');

        Route::post('/projects/{id}/change/{tid}/fav', [\App\Http\Controllers\Web\Company\ProjectTaskController::class, 'changeFav'])->name('change.fav');
        Route::post('/projects/{id}/change/{tid}/complete', [\App\Http\Controllers\Web\Company\ProjectTaskController::class, 'changeCom'])->name('change.complete');
        Route::post('/projects/{id}/change/{tid}/progress', [\App\Http\Controllers\Web\Company\ProjectTaskController::class, 'changeProg'])->name('change.progress');
        Route::get('/projects/task/{id}/get', [\App\Http\Controllers\Web\Company\ProjectTaskController::class, 'taskGet'])->name('projects.tasks.get')->middleware(['auth', 'XSS']);
        Route::get('/calendar/{id}/show', [\App\Http\Controllers\Web\Company\ProjectTaskController::class, 'calendarShow'])->name('task.calendar.show')->middleware(['auth', 'XSS']);
        Route::post('/calendar/{id}/drag', [\App\Http\Controllers\Web\Company\ProjectTaskController::class, 'calendarDrag'])->name('task.calendar.drag');
        Route::get('calendar/{task}/{pid?}', [\App\Http\Controllers\Web\Company\ProjectTaskController::class, 'calendarView'])->name('task.calendar')->middleware(['auth', 'XSS']);

        Route::get('taskboard/{view?}', [\App\Http\Controllers\Web\Company\ProjectTaskController::class, 'taskBoard'])->name('taskBoard.view');
        Route::get('taskboard-view', [\App\Http\Controllers\Web\Company\ProjectTaskController::class, 'taskboardView'])->name('project.taskboard.view');

//        Route::resource('project-task-stages', TaskStageController::class)->middleware(['auth', 'XSS']);
//        Route::post('/project-task-stages/order', [TaskStageController::class, 'order'])->name('project-task-stages.order');
//
//        Route::post('project-task-new-stage', [TaskStageController::class, 'storingValue'])->name('new-task-stage')->middleware(['auth', 'XSS']);

    });

    // Bug Report
    Route::group(['prefix' => 'bugs'], function () {
//        Route::resource('projectstages', ProjectstagesController::class);
//        Route::post('/projectstages/order', [ProjectstagesController::class, 'order'])->name('projectstages.order')->middleware(['auth', 'XSS']);
        Route::post('projects/bug/kanban/order', [\App\Http\Controllers\Web\Company\ProjectController::class, 'bugKanbanOrder'])->name('bug.kanban.order');
        Route::get('projects/{id}/bug/kanban', [\App\Http\Controllers\Web\Company\ProjectController::class, 'bugKanban'])->name('task.bug.kanban');
        Route::get('projects/{id}/bug', [\App\Http\Controllers\Web\Company\ProjectController::class, 'bug'])->name('task.bug');
        Route::get('projects/{id}/bug/create', [\App\Http\Controllers\Web\Company\ProjectController::class, 'bugCreate'])->name('task.bug.create');
        Route::post('projects/{id}/bug/store', [\App\Http\Controllers\Web\Company\ProjectController::class, 'bugStore'])->name('task.bug.store');
        Route::get('projects/{id}/bug/{bid}/edit', [\App\Http\Controllers\Web\Company\ProjectController::class, 'bugEdit'])->name('task.bug.edit');
        Route::post('projects/{id}/bug/{bid}/update', [\App\Http\Controllers\Web\Company\ProjectController::class, 'bugUpdate'])->name('task.bug.update');
        Route::delete('projects/{id}/bug/{bid}/destroy', [\App\Http\Controllers\Web\Company\ProjectController::class, 'bugDestroy'])->name('task.bug.destroy');
        Route::get('projects/{id}/bug/{bid}/show', [\App\Http\Controllers\Web\Company\ProjectController::class, 'bugShow'])->name('task.bug.show');
        Route::post('projects/{id}/bug/{bid}/comment', [\App\Http\Controllers\Web\Company\ProjectController::class, 'bugCommentStore'])->name('bug.comment.store');
        Route::post('projects/bug/{bid}/file', [\App\Http\Controllers\Web\Company\ProjectController::class, 'bugCommentStoreFile'])->name('bug.comment.file.store');
        Route::delete('projects/bug/comment/{id}', [\App\Http\Controllers\Web\Company\ProjectController::class, 'bugCommentDestroy'])->name('bug.comment.destroy');
        Route::delete('projects/bug/file/{id}', [\App\Http\Controllers\Web\Company\ProjectController::class, 'bugCommentDestroyFile'])->name('bug.comment.file.destroy');

        Route::resource('bugstatus', \App\Http\Controllers\Web\Company\BugStatusController::class);
        Route::post('/bugstatus/order', [\App\Http\Controllers\Web\Company\BugStatusController::class, 'order'])->name('bugstatus.order');
        Route::get('bugs-report/{view?}', [\App\Http\Controllers\Web\Company\ProjectTaskController::class, 'allBugList'])->name('bugs.view')->middleware(['auth', 'XSS']);

    });

    Route::post('/stages/order', [\App\Http\Controllers\Web\Company\StageController::class, 'order'])->name('stages.order');
    Route::post('/stages/json', [\App\Http\Controllers\Web\Company\StageController::class, 'json'])->name('stages.json');

    Route::resource('stages', \App\Http\Controllers\Web\Company\StageController::class);
    Route::resource('pipelines', \App\Http\Controllers\Web\Company\PipelineController::class);
    Route::resource('labels', \App\Http\Controllers\Web\Company\LabelController::class);
    Route::resource('sources', \App\Http\Controllers\Web\Company\SourceController::class);
    // Leads Module
    Route::middleware(['auth', 'XSS'])->group(function () {

        // Lead Stages
        Route::post('/lead_stages/order', [LeadStageController::class, 'order'])
            ->name('lead_stages.order');

        Route::resource('lead_stages', LeadStageController::class);

        // Leads
        Route::prefix('leads')->name('leads.')->group(function () {

            Route::post('/json', [LeadController::class, 'json'])->name('json');
            Route::post('/order', [LeadController::class, 'order'])->name('order');
            Route::get('/list', [LeadController::class, 'lead_list'])->name('list');
            Route::get('/export', [LeadController::class, 'export'])->name('export');

            // Files
            Route::post('/{id}/file', [LeadController::class, 'fileUpload'])->name('file.upload');
            Route::get('/{id}/file/{fid}', [LeadController::class, 'fileDownload'])->name('file.download');
            Route::delete('/{id}/file/delete/{fid}', [LeadController::class, 'fileDelete'])->name('file.delete');

            // Notes
            Route::post('/{id}/note', [LeadController::class, 'noteStore'])->name('note.store');

            // Labels
            Route::get('/{id}/labels', [LeadController::class, 'labels'])->name('labels');
            Route::post('/{id}/labels', [LeadController::class, 'labelStore'])->name('labels.store');

            // Users
            Route::get('/{id}/users', [LeadController::class, 'userEdit'])->name('users.edit');
            Route::put('/{id}/users', [LeadController::class, 'userUpdate'])->name('users.update');
            Route::delete('/{id}/users/{uid}', [LeadController::class, 'userDestroy'])->name('users.destroy');

            // Products
            Route::get('/{id}/products', [LeadController::class, 'productEdit'])->name('products.edit');
            Route::put('/{id}/products', [LeadController::class, 'productUpdate'])->name('products.update');
            Route::delete('/{id}/products/{uid}', [LeadController::class, 'productDestroy'])->name('products.destroy');

            // Sources
            Route::get('/{id}/sources', [LeadController::class, 'sourceEdit'])->name('sources.edit');
            Route::put('/{id}/sources', [LeadController::class, 'sourceUpdate'])->name('sources.update');
            Route::delete('/{id}/sources/{uid}', [LeadController::class, 'sourceDestroy'])->name('sources.destroy');

            // Discussions
            Route::get('/{id}/discussions', [LeadController::class, 'discussionCreate'])->name('discussions.create');
            Route::post('/{id}/discussions', [LeadController::class, 'discussionStore'])->name('discussion.store');

            // Convert to Deal
            Route::get('/{id}/show_convert', [LeadController::class, 'showConvertToDeal'])->name('convert.deal');
            Route::post('/{id}/convert', [LeadController::class, 'convertToDeal'])->name('convert.to.deal');

            // Calls
            Route::get('/{id}/call', [LeadController::class, 'callCreate'])->name('calls.create');
            Route::post('/{id}/call', [LeadController::class, 'callStore'])->name('calls.store');
            Route::get('/{id}/call/{cid}/edit', [LeadController::class, 'callEdit'])->name('calls.edit');
            Route::put('/{id}/call/{cid}', [LeadController::class, 'callUpdate'])->name('calls.update');
            Route::delete('/{id}/call/{cid}', [LeadController::class, 'callDestroy'])->name('calls.destroy');

            // Emails
            Route::get('/{id}/email', [LeadController::class, 'emailCreate'])->name('emails.create');
            Route::post('/{id}/email', [LeadController::class, 'emailStore'])->name('emails.store');

            // Import
            Route::get('/import/file', [LeadController::class, 'importFile'])->name('import');
            Route::get('/import/modal', [LeadController::class, 'fileImportModal'])->name('import.modal');
            Route::post('/import', [LeadController::class, 'leadImportdata'])->name('import.data');
            Route::post('/import/file', [LeadController::class, 'fileImport'])->name('file.import');
            Route::get('/{id}/download', [LeadController::class, 'download'])->name('download');
            Route::get('/downloadPdf/{leadid}', [LeadController::class, 'downloadPdf'])->name('downloadPdf');
        });

        Route::resource('leads', LeadController::class);
    });

    // end Leads Module

});

// Standard User
Route::prefix('profile/my-reports')->middleware(['auth', 'XSS', 'detect_plan_expire_And_delete_media'])->name('employee.')->group(function () {
    Route::get('/', [\App\Http\Controllers\Web\User\DashboardController::class, 'attendance'])->name('self-report');
    Route::post('/update-work-log', [\App\Http\Controllers\Web\User\DashboardController::class, 'storeWorkLog'])->name('update.work.Log');
    Route::get('/attendance/monthly', [\App\Http\Controllers\Web\User\DashboardController::class, 'getMonthlyAttendance'])->name('attendance.monthly');
    Route::get('/break-insight', [\App\Http\Controllers\Web\User\DashboardController::class, 'breakInsight'])->name('breakInsight');
    Route::get('/activity', [\App\Http\Controllers\Web\User\DashboardController::class, 'activity'])->name('activity');
});
