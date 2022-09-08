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
                'error' => 'Unauthorized',
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

    protected function createNewToken($token){
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
