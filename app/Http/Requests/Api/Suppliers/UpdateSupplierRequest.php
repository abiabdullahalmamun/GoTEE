<?php

namespace App\Http\Requests\Api\Suppliers;

use App\Http\Responses\ApiResponse;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class UpdateSupplierRequest extends FormRequest{

    public function authorize(): bool
    {
        return true;
    }
    public function all($keys = null): array
    {
        $data = parent::all($keys);
        $data['id'] = $this->route('supplierId');
        return $data;
    }
    public function rules(): array
    {
        return [
            'name' => ['required','string','max:100',],
            'phone' => [
                'required',
                'string',
                'max:30',
                Rule::unique('suppliers', 'phone')->ignore($this->id),
            ],
            'address' => 'nullable|string|max:200',
            'isActive' => 'required|boolean',
            'image' => 'nullable|string',
        ];
    }

    public function messages(): array
    {
        return [
//            'phone.unique'=>'You have already item with this name',

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
