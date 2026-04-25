<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DebugModeController extends Controller
{
    /**
     * @param Request $request
     * @param $id
     * @return Factory|View|Application|object
     */
    public function index(Request $request, $id)
    {
        $activeUsers = User::where('created_by', $id)->where('is_active', true)->get();
        return view('admin.debug.index', compact('activeUsers'));
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function updateDebugMode(Request $request)
    {
        $user = User::findOrFail($request->id);
        $user->is_debug_mode = $request->is_debug_mode;
        $user->save();

        return response()->json(['success' => true]);
    }
}
