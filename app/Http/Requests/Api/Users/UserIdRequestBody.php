<?php

namespace App\Http\Requests\Api\Users;

use App\Enums\ResponseCode;
use App\Enums\ResponseStatus;
use App\Http\Responses\ApiResponse;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class UserIdRequestBody extends FormRequest
{

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'id' => ['required','integer','exists:users,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'id.required' => 'ID is required',
            'id.integer' => 'ID must be integer',
            'id.exists' => 'Invalid ID.',
        ];
    }



    public function all($keys = null): array
    {
        $data = parent::all($keys);
        $data['id'] = $this->input('userId');
        return $data;
    }

    protected function failedValidation(Validator $validator): void
    {
        ApiResponse::validationThrowException($validator);
    }

}
