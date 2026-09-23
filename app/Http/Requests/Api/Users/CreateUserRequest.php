<?php

namespace App\Http\Requests\Api\Users;


use App\Http\Responses\ApiResponse;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;

class CreateUserRequest extends FormRequest
{

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'userName' => ['required', 'string', 'max:200'],
            'phone' => ['required','string','regex:/^[0-9]{10,20}$/','unique:users,phone'],
            'password' => ['required', 'string', 'min:6','max:30'],
            'email' => ['nullable','string', 'email', 'max:70','unique:users,email'],
            'address' => ['nullable','string', 'max:200'],
            'typeId' => ['required','numeric', 'exists:user_types,id'],
            'image' => ['nullable','image','mimes:jpeg,png,jpg','max:2048'], // Max size of 2MB
        ];
    }

    public function messages(): array
    {
        return [
            'userName.required' => 'User name is required',
            'phone.unique' => 'Phone is already registered',
            'phone.required' => 'Phone is required',
            'phone.regex' => 'Phone is invalid',
            'email.email' => 'Email is not valid',
            'password.required' => 'Password is required',
        ];
    }



    public function validated($key = null, $default = null): array
    {
        return [
            'name' => $this->input('userName'),
            'type_id' => $this->input('typeId'),
            'phone' => $this->input('phone'),
            'password' => $this->input('password'),
            'email' => $this->input('email'),
            'address' => $this->input('address'),
            'image' => $this->hasFile('image') ? $this->file('image') : null,
        ];
    }

    protected function failedValidation(Validator $validator): void
    {
        ApiResponse::validationThrowException($validator);
    }


}
