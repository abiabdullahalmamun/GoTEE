<?php

namespace App\Http\Requests\Api\Users;


use App\Enums\ResponseStatus;
use App\Http\Responses\ApiResponse;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class UserPhoneNumberRequest extends FormRequest
{



    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'phone' => ['required','string','regex:/^[0-9]{10,20}$/','exists:users,phone'],
        ];
    }

    public function messages(): array
    {
        return [
            'phone.required' => 'Phone is required',
            'phone.integer' => 'Phone must be integer',
            'phone.exists' => 'Phone is not register',
            'phone.regex' => 'Phone is invalid',
        ];
    }



    public function validated($key = null, $default = null): array
    {
        return [
            'phone' => $this->input('phone')
        ];
    }

    protected function failedValidation(Validator $validator): void
    {
        ApiResponse::validationThrowException($validator);
    }

}
