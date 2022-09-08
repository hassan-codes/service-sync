<?php

namespace App\Services\v1;

use App\Http\Requests\v1\StoreAdminRequest;
use App\Http\Requests\v1\UpdateAdminRequest;
use App\Http\Resources\v1\AdminResource;
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

        try {
            User::where('id', $userId)
                ->where('role', 'admin')
                ->where('is_active', true)
                ->update(['is_active' => false]);
            $admin = User::where('id', $userId)->first();
        } catch (QueryException $exception) {
            error_log($exception->getMessage());
            // TODO: email error to sysadmin
            return response()->json([
                'error' => 'Failed to deactivate',
            ], 422);
        }

        return response()->json([
            'message' => 'Successfully deactivated account of ' . $admin->last_name,
        ], 200);
    }

    public function update(UpdateAdminRequest $request, int $id)
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

        try{
            $admin = User::where('id', $id)
                ->where('role', 'admin')
                ->update([
                    'first_name' => strtoupper($request->first_name),
                    'last_name' => strtoupper($request->last_name),
                    'email' => strtolower($request->email)
                ]);
            $admin = User::where('id', $id)->first();
        } catch (QueryException $exception) {
            error_log($exception->getMessage());
            // TODO: email error to sysadmin
            return response()->json([
                'error' => 'Failed to update',
            ], 422);
        }

        return $admin;
    }

    public function fetch(int $userId = 0)
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

        try {
            $agents = AdminResource::collection(User::where('role', 'admin')
                ->orderBy('first_name', 'ASC')
                ->orderBy('last_name', 'ASC')
                ->paginate());

            if ($userId !== 0) {
                $agents = AdminResource::collection(User::where('role', 'admin')
                    ->where('id', $userId)
                    ->orderBy('first_name', 'ASC')
                    ->orderBy('last_name', 'ASC')
                    ->paginate());
            }
        } catch (QueryException $exception) {
            error_log($exception->getMessage());
            // TODO: email error to sysadmin
            return response()->json([
                'error' => 'Failed to list agents',
            ], 422);
        }

        return $agents;
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
