<?php

namespace App\Http\Controllers\Api;

use App\Enums\ResponseStatus;
use App\Exceptions\ExceptionHandler;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Users\CreateUserRequest;
use App\Http\Requests\Api\Users\OTPUserRequest;
use App\Http\Requests\Api\Users\UpdateUserRequest;
use App\Http\Requests\Api\Users\UserConnFilteringRequest;
use App\Http\Requests\Api\Users\UserPhoneNumberRequest;
use App\Http\Requests\Api\Users\UserIdRequest;
use App\Http\Requests\Api\UserType\UserTypeIdRequest;
use App\Http\Responses\ApiResponse;
use App\Services\UserService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UserController extends Controller
{
    protected UserService $userService;

    public function __construct(UserService $authService)
    {
        $this->userService = $authService;
    }

    public function getUserList(Request $request): JsonResponse{
      try {
        $page = (trim($request->page) != '') ? trim($request->page) : 1;
        $size = (trim($request->size) != '') ? trim($request->size) : 10;
        $filter = $request->filter;
        $resp = $this->userService->getUserList($filter,$page,$size);
        return ApiResponse::success($resp, ResponseStatus::SUCCESS);
      }catch (ExceptionHandler $e) {
        return ApiResponse::ExceptionHandlerResponse($e);
      }
      catch (Exception $e) {
        return ApiResponse::ServerError($e);
      }
    }

    public function register(CreateUserRequest $request): JsonResponse
    {
        try {
            $resp = $this->userService->register($request->validated());

            return ApiResponse::success($resp['data'], $resp['message'],ResponseStatus::CODE_CREATED);

        }catch (ExceptionHandler $e) {
            return ApiResponse::ExceptionHandlerResponse($e);
        }
        catch (Exception $e) {
            return ApiResponse::ServerError($e);
        }
    }
    public function otpVerification(OTPUserRequest $request): JsonResponse
    {
        try {
            $resp = $this->userService->otpVerification($request->validated());

            return ApiResponse::success($resp, 'Your OTP successfully verified.',ResponseStatus::CODE_CREATED);

        }catch (ExceptionHandler $e) {
            return ApiResponse::ExceptionHandlerResponse($e);
        }
        catch (Exception $e) {
            return ApiResponse::ServerError($e);
        }
    }
    public function getSingleUser(UserIdRequest $request): JsonResponse
    {
        try {
            $id = $request->validated()['id'];
            $resp = $this->userService->getSingleUser($id);
            return ApiResponse::success($resp, ResponseStatus::SUCCESS);
        }catch (ExceptionHandler $e) {
            return ApiResponse::ExceptionHandlerResponse($e);
        }
        catch (Exception $e) {
            return ApiResponse::ServerError($e);
        }
    }

    public function setUserConnectionByPhone(UserPhoneNumberRequest $request): JsonResponse
    {
        try {
            $phone = $request->validated()['phone'];

            $resp = $this->userService->setUserConnectionByPhone($phone);

            return ApiResponse::success($resp, 'User successfully connected');
        }catch (ExceptionHandler $e) {
            return ApiResponse::ExceptionHandlerResponse($e);
        }
        catch (Exception $e) {
            return ApiResponse::ServerError($e);
        }
    }
    public function unsetUserConnectionById(UserIdRequest $request): JsonResponse
    {
        try {
            $id = $request->validated()['id'];
            $resp = $this->userService->unsetUserConnectionById($id);


            return ApiResponse::success($resp, 'User successfully remove');
        }
        catch (ExceptionHandler $e) {
            return ApiResponse::ExceptionHandlerResponse($e);
        }
        catch (Exception $e) {
            return ApiResponse::ServerError($e);
        }
    }

    public function updateSingleUserType(UserTypeIdRequest $request): JsonResponse
    {
        try {
            $typeId = $request->validated()['type_id'];
            $resp = $this->userService->updateSingleUserType($typeId);

            return ApiResponse::success($resp, ResponseStatus::SUCCESS);
        }catch (ExceptionHandler $e) {
            return ApiResponse::ExceptionHandlerResponse($e);
        }
        catch (Exception $e) {
            return ApiResponse::ServerError($e);
        }
    }
    public function updateSingleUser(UpdateUserRequest $request): JsonResponse
    {
        try {
            $resp = $this->userService->updateSingleUser($request->validated());

            return ApiResponse::success($resp, ResponseStatus::SUCCESS);

        } catch (ExceptionHandler $e) {
            return ApiResponse::ExceptionHandlerResponse($e);
        }
        catch (Exception $e) {
            return ApiResponse::ServerError($e);
        }
    }

    public function getSingleUserConnectionListByUserId(UserConnFilteringRequest $request): JsonResponse
    {
      try {
        $filterQuery = $request->validated()['filter'];
        $resp = $this->userService->getSingleUserConnectionList($request,$filterQuery);
        return ApiResponse::success($resp, ResponseStatus::SUCCESS);
      } catch (ExceptionHandler $e) {
        return ApiResponse::ExceptionHandlerResponse($e);
      }
      catch (Exception $e) {
        return ApiResponse::ServerError($e);
      }
    }

    public function getSingleUserConnectionRecentVisits(UserConnFilteringRequest $request): JsonResponse
    {
        try {
            $filterQuery = $request->validated()['filter'];
            $resp = $this->userService->getSingleUserConnectionList($request,$filterQuery);
            return ApiResponse::success($resp, ResponseStatus::SUCCESS);
        } catch (ExceptionHandler $e) {
            return ApiResponse::ExceptionHandlerResponse($e);
        }
        catch (Exception $e) {
            return ApiResponse::ServerError($e);
        }
    }
    public function getSingleUserItemListByUserId(UserIdRequest $idData): JsonResponse
    {
        try {
            $resp = $this->userService->getSingleUserItemList($idData->id);
            return ApiResponse::success($resp, ResponseStatus::SUCCESS);
        } catch (ExceptionHandler $e) {
            return ApiResponse::ExceptionHandlerResponse($e);
        }
        catch (Exception $e) {
            return ApiResponse::ServerError($e);
        }
    }
}
