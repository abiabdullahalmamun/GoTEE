<?php

namespace App\Repositories;
//
//use App\Interfaces\AuthRepositoryInterface;
use App\Interfaces\AuthRepositoryInterface;
use App\Models\User;
use Laravel\Sanctum\HasApiTokens;

class AuthRepository implements AuthRepositoryInterface
{
    use HasApiTokens;

    public function findByPhone(string $phone)
    {
        return User::where('phone', $phone)->with(['companyStore.companyInfo', 'userType'])->first();
    }
    public function findByEmail(string $email)
    {
        return User::where('email', $email)->with(['companyStore.companyInfo', 'userType'])->first();
    }
    public function createToken(User $user): string
    {
        $token = $user->createToken('auth_token')->plainTextToken;
        return $token;
    }


}
