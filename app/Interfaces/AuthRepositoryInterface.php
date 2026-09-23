<?php

namespace App\Interfaces;

use App\Models\User;

interface AuthRepositoryInterface
{
    public function findByPhone(string $phone);
    public function findByEmail(string $email);
    public function createToken(User $user);
}
