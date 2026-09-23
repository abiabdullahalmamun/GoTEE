<?php

namespace App\Http\Requests\Api\Orders;

use App\Enums\ResponseStatus;
use App\Http\Responses\ApiResponse;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class UpdateOrderStatusRequest extends FormRequest{

    public function authorize(): bool
    {
        return true;
    }
    public function rules(): array
    {
        return [
            'statusCode' => 'required|numeric|exists:order_status,code',
        ];
    }


    public function validated($key = null, $default = null): array
    {
        return [
            'statusCode' => $this->input('statusCode')
        ];
    }


    protected function failedValidation(Validator $validator): void
    {
        ApiResponse::validationThrowException($validator);
    }

}
