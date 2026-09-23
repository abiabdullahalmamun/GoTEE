<?php

namespace App\Http\Requests\Api\ItemBrands;

use App\Http\Responses\ApiResponse;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class CreateBrandRequest extends FormRequest{

    public function authorize(): bool
    {
        return true;
    }
    public function rules(): array
    {
        return [
            'name'=>[
                'required',
                'string',
                'max:50',
                Rule::unique('item_brands','name')
            ],
        ];
    }
    public function messages(): array
    {
            return [
                'name.unique'=>'Brand name already exists'
            ];
    }


    public function validated($key = null, $default = null): array
    {
        return [
            'name' => $this->input('name'),
        ];
    }

    protected function failedValidation(Validator $validator): void
    {
        ApiResponse::validationThrowException($validator);
    }

}
