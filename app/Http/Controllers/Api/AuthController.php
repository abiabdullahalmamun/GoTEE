<?php

namespace App\Http\Controllers\Api;

use App\Enums\ResponseStatus;
use App\Exceptions\ExceptionHandler;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Auth\LoginRequest;
use App\Http\Responses\ApiResponse;
use App\Services\Auth\AuthService;
use Exception;
use Illuminate\Http\JsonResponse;

class AuthController extends Controller
{
    protected AuthService $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    public function unauthorized(): JsonResponse
    {
        return ApiResponse::error(ResponseStatus::UNAUTHORIZED, ResponseStatus::UNAUTHORIZED, ResponseStatus::CODE_UNAUTHORIZED);
    }

    public function login(LoginRequest $request): JsonResponse
    {
        try {
            $resData = $this->authService->login($request->validated());

            return ApiResponse::success($resData, 'Login successful');

        }catch (ExceptionHandler $e) {
            return ApiResponse::ExceptionHandlerResponse($e);
        }
        catch (Exception $e) {
            return ApiResponse::ServerError($e);
        }

    }
}
