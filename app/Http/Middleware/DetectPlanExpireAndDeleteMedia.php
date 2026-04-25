<?php

namespace App\Http\Middleware;

use App\Jobs\CleanupExpiredMediaData;
use App\Models\Plan;
use App\Models\User;
use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class DetectPlanExpireAndDeleteMedia
{
    /**
     * @param Request $request
     * @param Closure $next
     * @return Response
     */
    public function handle(Request $request, Closure $next): Response
    {
        $authUser = Auth::user();

        if (!$authUser || !method_exists($authUser, 'creatorId')) {
            return $next($request);
        }

        $creator = User::find($authUser->creatorId());
        if (!$creator) {
            return $this->denyAccess($request, 'Creator account not found.');
        }

        $plan = Plan::find($creator->plan);

        if (empty($plan) || empty($creator->plan_expire_date)) {
            return $this->denyAccess($request, 'Contact Administrator.');
        }

        $datetime1 = new \DateTime($authUser->plan_expire_date);
        $datetime2 = new \DateTime(date('Y-m-d'));

        $interval = $datetime2->diff($datetime1);
        $days = $interval->format('%r%a');

        if ($days < 0) {
            return $this->denyAccess($request, 'Contact Administrator.');
        }

//        Log::info('Detected Plan Is Trigger');
//        CleanupExpiredMediaData::dispatch($creator->id);

        return $next($request);
    }

    /**
     * @param Request $request
     * @param string $message
     * @return JsonResponse|RedirectResponse
     */
    private function denyAccess(Request $request, string $message): JsonResponse|RedirectResponse
    {
        if ($request->expectsJson() || $request->is('api/*')) {
            return response()->json([
                'message' => $message,
                'is_success' => false,
            ], 403);
        }

        return redirect()->back()->with('success', $message);
    }
}
