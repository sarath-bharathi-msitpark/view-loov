<?php

namespace App\Http\Controllers\AdminAPI;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class AdminController extends Controller
{
    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|string|email',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return response()->json([
                'is_success' => false,
                'message' => 'User not found.',
            ], 404);
        }

//        if ($user->type !== 'company') {
//            return response()->json([
//                'is_success' => false,
//                'message' => 'Access denied. Only company are allowed.',
//            ], 403);
//        }

        Auth::login($user);

        return response()->json([
            'is_success' => true,
            'token' => $user->createToken('API Token')->plainTextToken,
            'user' => $user,
            'message' => 'Login successful.',
        ], 200);
    }

    /**
     * @return JsonResponse
     */
    public function profile()
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json([
                'is_success' => false,
                'message' => 'Unauthenticated.',
            ], 401);
        }

//        if ($user->type !== 'company') {
//            return response()->json([
//                'is_success' => false,
//                'message' => 'Access denied. Only company are allowed.',
//            ], 403);
//        }

        return response()->json([
            'is_success' => true,
            'user' => $user,
            'message' => 'Profile retrieved successfully',
        ], 200);
    }

    /**
     * @return JsonResponse
     */
    public function logout()
    {
        auth()->user()->tokens()->delete();

        return response()->json([
            'is_success' => true,
            'message' => 'Logout successfully',
        ], 200);
    }
}
