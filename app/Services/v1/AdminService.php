<?php

namespace App\Services\v1;

use App\Http\Requests\v1\StoreAdminRequest;
use App\Http\Requests\v1\UpdateAdminRequest;
use App\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AdminService
{
    public function invite(StoreAdminRequest $request)
    {
        $user = auth()->user();

        if (! $this->isPasswordReset($user)) {
            return response()->json([
                'message' => 'Unauthorized. You must reset your password',
            ], 401);
        }

        if (! $this->isAdmin($user)) {
            return response()->json([
                'message' => 'Unauthorized. Illegal action attempted',
            ], 401);
        }

        $newUser = new User();
        $password = Str::random(8);

        try{
            $newUser->first_name = strtoupper($request->first_name);
            $newUser->last_name = strtoupper($request->last_name);
            $newUser->email = strtolower($request->email);
            $newUser->password = Hash::make($password);
            $newUser->role = 'admin';
            $newUser->is_active = true;

            $newUser->save();
        } catch (QueryException $exception) {
            error_log($exception->getMessage());
            // TODO: email error to sysadmin
            return response()->json([
                'error' => 'Failed to create admin account. Try again!',
            ], 422);
        }

        return response()->json([
            'message' => 'User created successfully',
            'user' => [
                'first_name' => $newUser->first_name,
                'last_name' => $newUser->last_name,
                'email' => $newUser->email,
                'role' => $newUser->role,
                'password' => $password
            ]
        ], 201);
    }

    public function deactivate(int $userId)
    {
        $user = User::where('id', $userId)->first();
        try {
            User::where('id', $userId)
                ->where('role', 'admin')
                ->where('is_active', true)
                ->update(['is_active' => false]);
        } catch (QueryException $exception) {
            error_log($exception->getMessage());
            // TODO: email error to sysadmin
            return response()->json([
                'error' => 'Failed to deactivate',
            ], 500);
        }

        return response()->json([
            'message' => 'Successfully deactivated account of ' . $user->last_name,
        ], 200);
    }

    public function update(UpdateAdminRequest $request, int $id)
    {
        $user = User::where('id', $id)->first();
        try{
            User::where('id', $id)
                ->where('role', 'admin')
                ->update([
                    'first_name' => strtoupper($request->first_name),
                    'last_name' => strtoupper($request->last_name),
                    'email' => strtolower($request->email)
                ]);
        } catch (QueryException $exception) {
            error_log($exception->getMessage());
            // TODO: email error to sysadmin
            return response()->json([
                'error' => 'Failed to update',
            ], 500);
        }

        return $user;
    }

    protected function isPasswordReset(User $user)
    {
        if (! is_null($user->password_reset_at)){
            return true;
        }

        return false;
    }

    protected function isAdmin(User $user)
    {
        if ($user->role === 'admin') {
            return true;
        }

        return false;
    }
}
