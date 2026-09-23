<?php

namespace App\Http\Requests\Api\Users;


use App\Http\Responses\ApiResponse;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;

class OTPUserRequest extends FormRequest
{

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'phone' => ['required','string','regex:/^[0-9]{10,20}$/'],
            'otp' => ['required', 'string', 'min:4','max:6'],
        ];
    }





    public function validated($key = null, $default = null): array
    {
        return [
            'phone' => $this->input('phone'),
            'otp' => $this->input('otp')
        ];
    }

    protected function failedValidation(Validator $validator): void
    {
        ApiResponse::validationThrowException($validator);
    }


}
