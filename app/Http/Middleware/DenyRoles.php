<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class DenyRoles
{
    /**
     * Handle an incoming request.
     *
     * @param \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response) $next
     */
    public function handle(Request $request, Closure $next, $blockedRoles): Response
    {
        $user = Auth::user();

        $blockedRoles = explode(',', $blockedRoles);

        if (in_array($user->role, $blockedRoles)) {
            abort(403, 'Unauthorized action.');
        }

        return $next($request);
    }
}
