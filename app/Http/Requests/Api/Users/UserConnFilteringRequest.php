<?php

namespace App\Http\Requests\Api\Users;


use App\Http\Responses\ApiResponse;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;

class UserConnFilteringRequest extends FormRequest
{

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'filter' => ['nullable','string','max:50'],
        ];
    }





    public function validated($key = null, $default = null): array
    {
        return [
            'filter' => $this->input('filter')
        ];
    }

    protected function failedValidation(Validator $validator): void
    {
        ApiResponse::validationThrowException($validator);
    }


}
