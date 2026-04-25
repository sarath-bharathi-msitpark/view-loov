<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * The path to the "home" route for your application.
     *
     * This is used by Laravel authentication to redirect users after login.
     *
     * @var string
     */
    public const HOME = 'account-dashboard';

    public const EMPHOME = 'hrm-dashboard';

    public const SUPERADMIN_HOME = '/admin/dashboard';
    public const COMPANY_HOME = '/organization/dashboard';
    public const EMPLOYEE_HOME = '/profile/my-reports';

    public const STAFF_HOME = '/general/dashboard';

    /**
     * The controller namespace for the application.
     *
     * When present, controller route declarations will automatically be prefixed with this namespace.
     *
     * @var string|null
     */
    protected $namespace = 'App\\Http\\Controllers';

    /**
     * Define your route model bindings, pattern filters, etc.
     *
     * @return void
     */
    public function boot()
    {
        $this->configureRateLimiting();

        $this->routes(function () {
            Route::prefix('api')
                ->middleware('api')
                ->namespace($this->namespace)
                ->group(base_path('routes/api.php'));

            Route::prefix('admin-apk')
                ->middleware('api')
                ->namespace($this->namespace)
                ->group(base_path('routes/admin-apk.php'));

            Route::prefix('stealth-apk')
                ->middleware('api')
                ->namespace($this->namespace)
                ->group(base_path('routes/stealth-apk.php'));

            Route::prefix('field-track-apk')
                ->middleware('api')
                ->namespace($this->namespace)
                ->group(base_path('routes/field-track-apk.php'));

//            Route::middleware('web')
//                ->namespace($this->namespace)
//                ->group(base_path('routes/web.php'));

            Route::middleware('web')
                ->namespace($this->namespace)
                ->group(base_path('routes/route.php'));

            Route::middleware('web')
                ->namespace($this->namespace)
                ->group(base_path('routes/field-track.php'));

            Route::middleware('web')
                ->namespace($this->namespace)
                ->group(base_path('routes/payment.php'));

            Route::prefix('landing')
                ->middleware('api')
                ->namespace($this->namespace)
                ->group(base_path('routes/landing.php'));
        });
    }

    /**
     * Configure the rate limiters for the application.
     *
     * @return void
     */
    protected function configureRateLimiting()
    {
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by(optional($request->user())->id ?: $request->ip());
        });
    }
}
