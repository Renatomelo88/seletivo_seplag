<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class LoginController
{

    public const  TOKEN_EXPIRACAO = 5;

    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        if (auth()->attempt($credentials)) {
            $user = auth()->user();
            $token = $user->createToken('api-token', ['*'], Carbon::now()->addMinutes(static::TOKEN_EXPIRACAO));
            return response()->json(['token' => $token->plainTextToken]);
        }

        return response()->json(['error' => 'Unauthorized'], 401);
    }

    public function renovaToken(Request $request)
    {
        $user = $request->user();

        $token = $user->currentAccessToken();

        $token->expires_at = Carbon::now()->addMinutes(static::TOKEN_EXPIRACAO);
        $token->save();

        return response()->json([
            'nova_expiracao' => $token->expires_at->toDateTimeString(),
        ]);

    }
}