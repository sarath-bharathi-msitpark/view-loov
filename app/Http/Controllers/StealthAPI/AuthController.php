<?php

namespace App\Http\Controllers\StealthAPI;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function CreateStealthUser(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|unique:users,name',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
            'company_id' => 'required|exists:users,id',
        ], [
            'name.unique' => 'This name is already taken.',
            'email.unique' => 'This email is already registered.',
            'company_id.exists' => 'Invalid company ID.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'is_success' => false,
                'message' => 'Validation error.',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $company = User::findOrFail($request->company_id);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'is_success' => false,
                'message' => 'Company not found.',
            ], 404);
        }

        $activeUserCount = User::whereHas('roles', function ($query) {
            $query->whereIn('name', ['stealth user', 'standard user']);
        })
            ->join('employees', 'users.id', '=', 'employees.user_id')
            ->where('users.created_by', $company->id)
            ->where('employees.is_active', 1)
            ->count();

        $subscriptionAvailableCount = $company->max_users;

        if ($activeUserCount >= $subscriptionAvailableCount) {
            return response()->json([
                'is_success' => false,
                'message' => 'Subscription limit reached.',
            ], 409);
        }

        DB::beginTransaction();

        try {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'type' => 'Employee',
                'created_by' => $company->id,
                'password' => Hash::make($request->password),
            ]);

            $selectedRole = Role::where('name', 'stealth user')->first();
            if (!$selectedRole) {
                throw new \Exception("Role 'stealth user' not found.");
            }

            $user->roles()->sync([$selectedRole->id]);

            Employee::create([
                'user_id' => $user->id,
                'employee_id' => $request->employee_id ?? $user->id,
                'name' => $request->name,
                'email' => $request->email,
                'role_id' => $selectedRole->id,
                'is_active' => true,
                'created_by' => $company->id,
            ]);

            DB::commit();

            return response()->json([
                'is_success' => true,
                'token' => $user->createToken('API Token')->plainTextToken,
                'user' => $user,
                'message' => 'Stealth user created successfully.',
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'is_success' => false,
                'message' => 'Error creating stealth user.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function stealthProfile(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
        ]);

        $email = $request->input('email');

        $user = User::with([
            'employee',
            'employee.team',
            'employee.designation',
            'employee.shift',
        ])->where('email', $email)->first();

        if (!$user) {
            return response()->json([
                'is_success' => false,
                'message' => 'User not found.',
            ], 404);
        }

        return response()->json([
            'is_success' => true,
            'token' => $user->createToken('API Token')->plainTextToken,
            'user' => $user,
            'Dashboard_url' => url('/'),
            'message' => 'Stealth user profile fetched successfully.',
        ], 200);
    }

    /**
     * @return JsonResponse
     */
    public function logout()
    {
        auth()->user()->tokens()->delete();

        return response()->json(
            [
                'is_success' => true,
                'message' => 'Tokens Revoked',
            ],
            200,
        );
    }
}
