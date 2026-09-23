<?php

namespace App\Repositories;

use App\Interfaces\UserRepositoryInterface;
use App\Models\OtpRequests;
use App\Models\User;
use App\Models\UserConnection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Laravel\Sanctum\HasApiTokens;

class UserRepository implements UserRepositoryInterface
{
    use HasApiTokens;

    public function getUserPagination($filter, int $page, int $size): \Illuminate\Contracts\Pagination\LengthAwarePaginator
    {
      $query = User::query()
        ->join('user_types', 'user_types.id', '=', 'users.type_id')
        ->join('users as creator', 'creator.id', '=', 'users.created_by')
        ->select(
          'users.id',
          'users.name',
          'users.email',
          'users.phone',
          'user_types.id as typeId',
          'user_types.name as typeName',
          'user_types.code as typeCode',
          'users.profile_photo_path as imageURL',
          'users.address',
          'users.created_by',
          'creator.name as createdByName',
          'users.is_active as isActive'
        )
        ->distinct();

      if (!empty($filter)) {
        $query->where(function ($q) use ($filter) {
          $q->where('users.name', 'like', '%' . $filter . '%')
            ->orWhere('users.phone', 'like', '%' . $filter . '%');
        });
      }
      $query->orderBy('users.id', 'DESC');

      return $query->paginate($size, ['*'], 'page', $page);

    }

    public function authUserList(int $userId)
    {
       return $data = User::where('created_by', $userId)->orderBy('id','DESC')->get();
    }

    public function findById(int $id): User | null
    {
       return $data = User::where('id', $id)->first();
    }

    public function findByPhone(string $phone)
    {
        return $data = User::where('phone', $phone)->first();
    }

    public function findAvailableOTP($phone): OtpRequests | null
    {
        return $data = OtpRequests::where('phone', $phone)->whereNull('verified_at')->where('otp_expired_at', '>', now()->format('Y-m-d H:i:s'))->orderBy('id','DESC')->first();
    }

    public function findOtpById($id): OtpRequests | null
    {
        return $data = OtpRequests::where('id',$id)->first();
    }
    public function findAvailableByOTP($phone,$otp): OtpRequests | null
    {
        return $data = OtpRequests::where('phone', $phone)->where('otp',$otp)->where('otp_expired_at', '>', now()->format('Y-m-d H:i:s'))->orderBy('id','DESC')->first();
    }
    public function registerOTP($data): OtpRequests
    {
        return $data = OtpRequests::create($data);
    }

    public function register($data): User
    {
        return $data = User::create($data);
    }

    public function checkUserSingleConnection(int $sourceUserId, int $targetUserId) : ?UserConnection
    {
        return UserConnection::where('user_id', $sourceUserId)->where('connected_user_id', $targetUserId)->first();
    }

    public function checkUserCrossConnection(int $sourceUserId, int $targetUserId): array
    {
        $connection = UserConnection::where(function ($query) use ($sourceUserId, $targetUserId) {
            $query->where('user_id', $sourceUserId)
                ->where('connected_user_id', $targetUserId);
        })->orWhere(function ($query) use ($sourceUserId, $targetUserId) {
            $query->where('user_id', $targetUserId)
                ->where('connected_user_id', $sourceUserId);
        })->count();

        return [
            'status' => $connection == 2,
            'count' => $connection
        ];

    }

    public function createOrUpdateUserConnection($data): void
    {
        foreach ($data as $connection) {
            UserConnection::updateOrCreate(
                [
                    'user_id' => $connection['user_id'],
                    'connected_user_id' => $connection['connected_user_id'],
                ],
                [
                    'created_by' => $connection['created_by'],
                ]
            );
        }
    }

    public function destroyUserConnection(UserConnection $connection): ?bool
    {
        return $connection->delete();
    }

    public function updateUser(object $authData) : ?object
    {
        $authData->save();

        return $authData;
    }





}
