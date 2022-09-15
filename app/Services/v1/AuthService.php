<?php

namespace App\Services\v1;

use App\Http\Requests\v1\LoginRequest;
use Illuminate\Support\Facades\Auth;

class AuthService
{
    public function login(LoginRequest $request)
    {
        $credentials = [
            'email' => $request->email,
            'password' => $request->password,
        ];
        if (! $token = auth()->attempt($credentials)) {
            return response()->json([
                'error' => 'Unauthenticated',
                'message' => 'Invalid credentials'
            ], 401);
        }

        // TODO: insert into login table
        return $this->createNewToken($token);
    }

    public function logout()
    {
        auth()->logout();
        return response()->json([
            'message' => 'User successfully logged out'
        ]);
    }

    public function refreshToken()
    {
        return $this->createNewToken(auth()->refresh());
    }

    protected function createNewToken($token){
        $user = auth()->user();
        if (! $user->is_active) {
            return response()->json([
                'error' => 'Unauthenticated',
                'message' => 'Attempt to login with inactive account'
            ], 401);
        }

        return response()->json(
            [
                'access_token' => $token,
                'token_type' => 'bearer',
                'expires_in' => auth()->factory()->getTTL() * 60,
                'user' => auth()->user(),
            ],
            200
        );
    }

}
