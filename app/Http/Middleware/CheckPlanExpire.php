<?php

namespace App\Http\Middleware;

use App\Providers\RouteServiceProvider;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Plan;

class CheckPlanExpire
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  ...$guards
     * @return mixed
     */
    public function handle(Request $request, Closure $next, ...$guards)
    {
        $excludedRoutes = [
            'auth.logout',
        ];
    
        if (in_array($request->route()->getName(), $excludedRoutes)) {
            return $next($request);
        }
        if (Auth::check() && Auth::user()->type == 'company') {
            $user = Auth::user();
        
            if (empty($user->plan) || empty($user->plan_expire_date)) {
                return redirect()->route('general.plans.index')
                    ->with('error', __("You dont have an active plan. Please choose a plan to continue."));
            }
        
            $datetime1 = new \DateTime($user->plan_expire_date);
            $datetime2 = new \DateTime(date('Y-m-d'));
        
            $interval = $datetime2->diff($datetime1);
            $days     = $interval->format('%r%a');
        
            if ($days < 0) {
                $plan = Plan::find($user->plan);
                if(!empty($plan)) {
                    // return redirect()->route('general.stripe', \Illuminate\Support\Facades\Crypt::encrypt($plan->id))
                    return redirect()->route('general.plans.index')
                    ->with('error', "You don't have an active plan. Please make a payment to continue. If you've already made a payment, please wait while we verify it.");
                }
                return redirect()->route('general.plans.index')
                    ->with('error', "Your plan has expired. Please renew your plan to access the dashboard.");
            }
        }

        return $next($request);
    }
}
