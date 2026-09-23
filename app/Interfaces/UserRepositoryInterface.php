<?php

namespace App\Interfaces;

use App\Models\UserConnection;


interface UserRepositoryInterface
{
    public function authUserList(int $userId);
    public function findById(int $id);
    public function getUserPagination($filter, int $page,int $size);
    public function findByPhone(string $phone);
    public function registerOTP($data);
    public function findAvailableOTP($phone);
    public function findOtpById($id);
    public function findAvailableByOTP($phone, $otp);
    public function register($data);
    public function updateUser(object $authData);
    public function checkUserSingleConnection(int $sourceUserId,int $targetUserId);
    public function checkUserCrossConnection(int $sourceUserId,int $targetUserId);
    public function createOrUpdateUserConnection($data);
    public function destroyUserConnection(UserConnection $connection);
}
