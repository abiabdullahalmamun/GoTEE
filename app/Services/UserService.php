<?php

namespace App\Services;

use App\Enums\ResponseStatus;
use App\Exceptions\ExceptionHandler;
use App\Http\Requests\Api\Users\UserIdRequest;
use App\Http\Resources\Users\UserWithConnectionsRecentVisitsResource;
use App\Http\Resources\Users\UserWithConnectionsResource;
use App\Http\Resources\Users\UserResource;
use App\Http\Resources\Users\UserWithItemsResource;
use App\Interfaces\UserRepositoryInterface;
use App\Services\Auth\AuthService;
use Illuminate\Database\QueryException;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class UserService
{
    protected UserRepositoryInterface $userRepository;
    protected AuthService $authService;

    public function __construct(UserRepositoryInterface $userRepository,AuthService  $authService)
    {
        $this->userRepository = $userRepository;
        $this->authService = $authService;
    }

    /**
     * @throws RandomException
     */
    public function generateOtp($length = 6): int
    {
        $min = pow(10, $length - 1); // Minimum value for a given length (e.g., 100000 for 6 digits)
        $max = pow(10, $length) - 1; // Maximum value for a given length (e.g., 999999 for 6 digits)

        return random_int($min, $max);
    }

  /**
   * @throws ConnectionException
   */
  public function senedOTP($phone, $otp): void
    {
      $url = "https://www.fast2sms.com/dev/bulkV2";
      $apiKey = "ic6nUzTKc9xUqho1kgMFDh8JZ0ZuJ9HOLQZx9mLwKDWMTzzIaNjX82Z5FSNd";
      $numbers = urlencode("$phone");
      $variablesValues = "$otp";
      $route = "otp";
      $response = Http::withHeaders([
        'cache-control' => 'no-cache'
      ])->get($url, [
        'authorization' => $apiKey,
        'variables_values' => $variablesValues,
        'route' => $route,
        'numbers' => $numbers,
      ]);

      if ($response->successful()) {
        $responseData = $response->body();
        // Process your response data here
      } else {
        $errorMessage = $response->body();
        // Handle the error here
      }
    }
    public function getOTP($phone,$userModel): array
    {
      $otpExpiry = now()->addMinutes(3)->format('Y-m-d H:i:s');
      $otp = $this->generateOtp();
      if(!$otp){
        $otp = $this->generateOtp();
      }

      $this->senedOTP($phone,$otp);

      return $otpData = [
        'phone'=>$phone,
        'otp'=>$otp,
        'otp_expired_at'=>$otpExpiry,
        'request_data'=>json_encode($userModel),
        'created_at'=>now()->format('Y-m-d H:i:s'),
        'verified_at'=>null
      ];
    }
    /**
     * @throws ExceptionHandler|RandomException
     */
    public function register($data): array
    {
        try {
            $available = $this->userRepository->findAvailableOTP($data['phone']);
            if($available){
                return [
                    'message' => 'We already sent a verification code to your mobile. Enter the code from the mobile in the field below.',
                    'data'=>[
                        'id'=>$available->id,
                        'phone'=>$available->phone,
                        'otp_expiry'=>Date('Y-m-d H:i:s', strtotime($available->otp_expired_at)),
                    ],
                ];
            }

            if(isset($data['image'])){
                $fileName = 'u_'.uniqid() . '.' . $data['image']->getClientOriginalExtension();
                $filePath = $data['image']->storeAs('public/images/users', $fileName);
                $imageUrl = Storage::url($filePath);
            }
            if(isset($data['status'])){
              $status = ($data['status'] == '1') ? 1 : 0;
            }else{
              $status = 1;
            }
            if(isset($data['created_by'])){
              $createdBy = $data['created_by'];
            }else{
              $createdBy = null;
            }
            $userModel = [
                'name'=>$data['name'],
//                'code'=>104,
                'phone'=>$data['phone'],
                'type_id'=>$data['type_id'],
                'address'=>$data['address'],
                'email'=>$data['email'],
                'email_verified_at'=>now()->format('Y-m-d H:i:s'),
                'password'=>$data['password'],
                'profile_photo_path'=> isset($imageUrl) ? $imageUrl : null,
                'created_by'=>$createdBy,
                'is_active'=>$status
            ];


            $otpData = $this->getOTP($data['phone'],$userModel);


            $data = $this->userRepository->registerOTP($otpData);
            if(!$data){
                throw new ExceptionHandler(ResponseStatus::SERVER_ERROR,ResponseStatus::CODE_INTERNAL_SERVER_ERROR);
            }
            return [
                'message' => 'We sent a verification code to your mobile. Enter the code from the mobile in the field below.',
                'data'=>[
                    'id'=>$data['id'],
                    'phone'=>$data['phone'],
                    'otp_expiry'=>Date('Y-m-d H:i:s', strtotime($otpData['otp_expired_at']))
                ],
            ];

        }
        catch (QueryException  $e) {
            throw new ExceptionHandler(ResponseStatus::SERVER_ERROR,ResponseStatus::CODE_INTERNAL_SERVER_ERROR,$e);
        }
    }

    /**
     * @throws ExceptionHandler
     */
    public function getUserList($filter = '',int $page = 1,int $size = 10)
    {
      try {

        $data = $this->userRepository->getUserPagination($filter, $page, $size);
        if(!$data){
          throw new ExceptionHandler(ResponseStatus::NOT_FOUND,ResponseStatus::CODE_INTERNAL_SERVER_ERROR);
        }
        return $data;
      }
      catch (QueryException  $e) {
        throw new ExceptionHandler(ResponseStatus::SERVER_ERROR,ResponseStatus::CODE_INTERNAL_SERVER_ERROR,$e);
      }
    }

    public function resendRequestOTP($phone,$id): array
    {
      try {
        $available = $this->userRepository->findOtpById($id);

        if($available){
          $otpData = $this->getOTP($available->phone,json_decode($available->request_data,true));
          $data = $this->userRepository->registerOTP($otpData);
          return [
            'status'=>true,
            'message' => 'New verification code send. Enter the code from the mobile in the field below.',
            'data'=>[
              'id'=>$data->id,
              'phone'=>$data->phone,
              'otp_expiry'=>Date('Y-m-d H:i:s', strtotime($data->otp_expired_at)),
            ],
          ];
        }
        else{
          return [
            'status'=>false,
            'message' => 'Invalid OTP Request',
            'data'=>[
              'id'=>$id,
              'phone'=>$phone,
              'otp_expiry'=>'',
            ],
          ];
        }

      }
        catch (QueryException  $e) {
        throw new ExceptionHandler(ResponseStatus::SERVER_ERROR,ResponseStatus::CODE_INTERNAL_SERVER_ERROR,$e);
      }
    }

    /**
     * @throws ExceptionHandler
     */
    public function otpVerification($data): array
    {
        try {
            $available = $this->userRepository->findAvailableByOTP($data['phone'], $data['otp']);
            if(!$available){
                throw new ExceptionHandler('OTP not valid',ResponseStatus::CODE_FORBIDDEN);
            }
            $duplicateUser = $this->userRepository->findByPhone($data['phone']);
            if($duplicateUser){
                throw new ExceptionHandler('Phone is already registered',ResponseStatus::CODE_FORBIDDEN);
            }

            $userModel = json_decode($available->request_data,true);
            $available->verified_at = now()->format('Y-m-d H:i:s');
            $available->save();
            $loginData = [
                'phone'=>$userModel['phone'],
                'password'=>$userModel['password'],
            ];
            $userModel['password'] = Hash::make($userModel['password']);
            $data =  $this->userRepository->register($userModel);
            if(!$data){
                throw new ExceptionHandler(ResponseStatus::SERVER_ERROR,ResponseStatus::CODE_INTERNAL_SERVER_ERROR);
            }
            return $this->authService->login($loginData);
        }
        catch (QueryException  $e) {
            throw new ExceptionHandler(ResponseStatus::SERVER_ERROR,ResponseStatus::CODE_INTERNAL_SERVER_ERROR,$e);
        }
    }

    /**
     * @throws ExceptionHandler
     */
    public function getSingleUser(int $id): UserResource
    {
        try {

            $data = $this->userRepository->findById($id);

            if(!$data){
                throw new ExceptionHandler(ResponseStatus::NOT_FOUND,ResponseStatus::CODE_INTERNAL_SERVER_ERROR);
            }

            return UserResource::make($data);
        }
        catch (QueryException  $e) {
            throw new ExceptionHandler(ResponseStatus::SERVER_ERROR,ResponseStatus::CODE_INTERNAL_SERVER_ERROR,$e);
        }
    }

    public function setUserConnectionByPhone(string $phone): UserResource
    {
        try {
            $authId = Auth::user()->id;

            $targetUserData = $this->userRepository->findByPhone($phone);

            if(!$targetUserData){
                throw new ExceptionHandler(ResponseStatus::NOT_FOUND,ResponseStatus::CODE_INTERNAL_SERVER_ERROR);
            }elseif($authId == $targetUserData->id){
                throw new ExceptionHandler('Connection is not valid',ResponseStatus::CODE_CONFLICT);
            }

            $checkConnection = $this->userRepository->checkUserCrossConnection($authId,$targetUserData->id);

            if($checkConnection['status']){
                throw new ExceptionHandler('User already connected',ResponseStatus::CODE_CONFLICT);
            }

            $data = [
                [
                    'user_id' => $authId,
                    'connected_user_id' => $targetUserData->id,
                    'created_by' => $authId,
                ],
                [
                    'user_id' => $targetUserData->id,
                    'connected_user_id' => $authId,
                    'created_by' => $authId,
                ],
            ];

            $this->userRepository->createOrUpdateUserConnection($data);

            return UserResource::make($targetUserData);
        }
        catch (QueryException  $e) {
            throw new ExceptionHandler(ResponseStatus::SERVER_ERROR,ResponseStatus::CODE_INTERNAL_SERVER_ERROR,$e);
        }
    }


    public function unsetUserConnectionById(int $targetId) : ?UserWithConnectionsResource
    {
        try {
            $authData = Auth::user();

            if($authData->id == $targetId){
                throw new ExceptionHandler('Cannot unset yourself',ResponseStatus::CODE_CONFLICT);
            }

            $checkConnection = $this->userRepository->checkUserSingleConnection($authData->id,$targetId);

            if(!$checkConnection){
                throw new ExceptionHandler('No connection available',ResponseStatus::CODE_CONFLICT);
            }

            $this->userRepository->destroyUserConnection($checkConnection);

            return UserWithConnectionsResource::make($authData);
        }
        catch (QueryException  $e) {
            throw new ExceptionHandler(ResponseStatus::SERVER_ERROR,ResponseStatus::CODE_INTERNAL_SERVER_ERROR,$e);
        }
    }

    public function updateSingleUserType(int $typeId) : ?UserResource
    {
        try {
            $authData = Auth::user();
            if($typeId == $authData->type_id){
                throw new ExceptionHandler('You have already this type',ResponseStatus::CODE_CONFLICT);
            }
            $authData->fill(['type_id'=>$typeId]);
            $result = $this->userRepository->updateUser($authData);

            return UserResource::make($result);
        }
        catch (QueryException  $e) {
            throw new ExceptionHandler(ResponseStatus::SERVER_ERROR,ResponseStatus::CODE_INTERNAL_SERVER_ERROR,$e);
        }

    }

    public function updateSingleUser(array $updateData) //: ?UserResource
    {
        try {

            if(isset($updateData['id'])){
              $id = $updateData['id'];
            }else{
              $id = Auth::id();
            }
            $authData = $this->userRepository->findById($id);
            if(isset($updateData['name'])){
                $authData->name = $updateData['name'];
            }
            if(isset($updateData['type_id'])) {
                $authData->type_id = $updateData['type_id'];
            }
            if(isset($updateData['email'])) {
                $authData->email = $updateData['email'];
            }
            if(isset($updateData['address'])) {
                $authData->address = $updateData['address'];
            }
            if(isset($data['status'])){
              $authData->is_active = ($data['status'] == '1') ? 1 : 0;
            }


            if(isset($updateData['image'])){
                $fileName = 'u_'.uniqid() . '.' . $updateData['image']->getClientOriginalExtension();
                $filePath = $updateData['image']->storeAs('public/images/users', $fileName);
                $imageUrl = Storage::url($filePath);
                $authData->profile_photo_path = $imageUrl;
            }
            $result = $this->userRepository->updateUser($authData);

            return UserResource::make($result);
        }
        catch (QueryException  $e) {
            throw new ExceptionHandler(ResponseStatus::SERVER_ERROR,ResponseStatus::CODE_INTERNAL_SERVER_ERROR,$e);
        }

    }

    public function getSingleUserConnectionList($request,$filterQuery) : array
    {
        try {
            $userData = Auth::user();

            $request->merge(['filter_data' => $filterQuery]);
            return UserWithConnectionsResource::make($userData)->toArray($request);
        }
        catch (QueryException  $e) {
            throw new ExceptionHandler(ResponseStatus::SERVER_ERROR,ResponseStatus::CODE_INTERNAL_SERVER_ERROR,$e);
        }
    }

    public function getSingleUserConnectionRecentVisits() : UserWithConnectionsRecentVisitsResource
    {
        try {
            $userData = Auth::user();

            return UserWithConnectionsRecentVisitsResource::make($userData);
        }
        catch (QueryException  $e) {
            throw new ExceptionHandler(ResponseStatus::SERVER_ERROR,ResponseStatus::CODE_INTERNAL_SERVER_ERROR,$e);
        }
    }

    public function getSingleUserItemList($userId) : UserWithItemsResource
    {
        try {
            $userData = $this->userRepository->findById($userId);

            return UserWithItemsResource::make($userData);
        }
        catch (QueryException  $e) {
            throw new ExceptionHandler(ResponseStatus::SERVER_ERROR,ResponseStatus::CODE_INTERNAL_SERVER_ERROR,$e);
        }
    }

    public function getAuthUsers(){
        try {
          $userId = Auth::id();
          $userData = $this->userRepository->authUserList($userId);

          return UserResource::collection($userData);
        }
        catch (QueryException  $e) {
          throw new ExceptionHandler(ResponseStatus::SERVER_ERROR,ResponseStatus::CODE_INTERNAL_SERVER_ERROR,$e);
        }

    }





}
