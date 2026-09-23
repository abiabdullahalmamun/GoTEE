<?php

namespace App\Services\Auth;


use App\Models\User;


class TokenService
{
    public function createToken(User $user): string
    {
        $token = $user->createToken('auth_token')->plainTextToken;
        return $token;
    }
}
