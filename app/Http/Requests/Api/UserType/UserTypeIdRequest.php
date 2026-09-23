<?php

namespace App\Http\Requests\Api\UserType;


use App\Enums\ResponseStatus;
use App\Http\Responses\ApiResponse;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class UserTypeIdRequest extends FormRequest
{

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'typeId' => ['required','numeric', 'exists:user_types,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'typeId.required' => 'Type is required',
            'typeId.numeric' => 'Phone is invalid',
            'typeId.exists' => 'Phone is invalid',
        ];
    }

    public function validated($key = null, $default = null): array
    {
        return [
            'type_id' => $this->input('typeId'),
        ];
    }

    protected function failedValidation(Validator $validator): void
    {
        ApiResponse::validationThrowException($validator);
    }

}
