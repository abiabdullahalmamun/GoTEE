<?php

namespace App\Http\Requests\Api\Users;


use App\Enums\ResponseStatus;
use App\Http\Responses\ApiResponse;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class UpdateUserRequest extends FormRequest
{

    public function authorize(): bool
    {
        return true;
    }
    public function all($keys = null): array
    {
      $data = parent::all($keys);
      $data['id'] = $this->route('id'); // Merge route parameter 'id' with request data
      return $data;
    }
    public function rules(): array
    {
        return [
            'userName' => ['required', 'string', 'max:200'],
            'email' => [
              'nullable',
              'string',
              'email',
              'max:70',
              Rule::unique('users', 'email')->ignore($this->route('id')),
            ],
            'address' => ['nullable','string', 'max:200'],
            'typeId' => ['required','numeric', 'exists:user_types,id'],
            'image' => ['nullable','image','mimes:jpeg,png,jpg','max:2048'], // Max size of 2MB
        ];
    }

    public function messages(): array
    {
        return [
            'userName.required' => 'User name is required',
            'email.email' => 'Email is not valid',
        ];
    }


    public function validated($key = null, $default = null): array
    {
        return [
            'name' => $this->input('userName'),
            'type_id' => $this->input('typeId'),
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
