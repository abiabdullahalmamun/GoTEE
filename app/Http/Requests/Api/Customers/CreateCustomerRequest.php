<?php

namespace App\Http\Requests\Api\Customers;

use App\Enums\ResponseStatus;
use App\Http\Responses\ApiResponse;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class CreateCustomerRequest extends FormRequest{

    public function authorize(): bool
    {
        return true;
    }
    public function rules(): array
    {
        $userId = Auth::id();
        return [
            'name' => ['required','string','max:100',],
            'phone' => ['required','string','regex:/^[0-9]{10,20}$/','unique:customers,phone'],
            'address' => 'nullable|string|max:200',
            'isActive' => 'required|boolean',
            'image' => 'nullable|string',
        ];
    }

    public function messages(): array
    {
            return [
            ];
    }

    public function validated($key = null, $default = null): array
    {
        return [
            'name' => $this->input('name'),
            'phone' => $this->input('phone'),
            'address' => $this->input('address'),
            'image' => $this->input('image'),
            'is_active' => $this->input('isActive'),
        ];
    }


    protected function failedValidation(Validator $validator): void
    {
        ApiResponse::validationThrowException($validator);
    }

}
