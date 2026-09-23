<?php

namespace App\Http\Responses;

use App\Enums\ResponseStatus;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;

class ApiResponse
{
    public static function success($data, $message = 'Success', $status = ResponseStatus::CODE_OK): JsonResponse
    {
        return response()->json([
            'status_code' => $status,
            'status' => ResponseStatus::SUCCESS,
            'message' => $message,
            'result' => $data
        ], $status);
    }

    public static function error($message, $errors = null, $status = ResponseStatus::CODE_BAD_REQUEST): JsonResponse
    {
        return response()->json([
            'status_code' => $status,
            'status' => ResponseStatus::ERROR,
            'message' => $message,
            'errors' => $errors
        ], $status);
    }


    public static function validationThrowException($validator): JsonResponse
    {
        throw new HttpResponseException(
            ApiResponse::error(
                ResponseStatus::VALIDATION_ERROR,
                $validator->errors(),
                ResponseStatus::CODE_UNPROCESSABLE_ENTITY
            )
        );
    }

    public static function ExceptionHandlerResponse($e): JsonResponse
    {
        return  ApiResponse::error($e->getMessage(),$e->getPrevious()?->getMessage(), $e->getCode());
    }

    public static function ServerError($e): JsonResponse
    {
        return ApiResponse::error(ResponseStatus::SERVER_ERROR, $e->getMessage(), ResponseStatus::CODE_INTERNAL_SERVER_ERROR);
    }

}
