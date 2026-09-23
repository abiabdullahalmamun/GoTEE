<?php

namespace App\Http\Requests\Api\HkProdCategories;


use App\Http\Responses\ApiResponse;
use App\Models\HkProdCategory;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class UpdateCategoryRequest extends FormRequest{
    public function authorize():bool
    {
        return true;
    }

    public function rules():array
    {
        $id = $this->route('categoryId');
        return [

            'name' => [
                'required',
                'string',
                'max:50',
                Rule::unique((new HkProdCategory)->getTable(),'name')
                    ->ignore($id)
            ],
            'sequence' => ['nullable', 'numeric'],
            'is_active'=>['required','boolean'],
        ];
    }
    public function messages():array
    {
        return [
            'name.unique'=>'you already have a category with this name'
        ];
    }


    public function validated($key = null, $default = null): array
    {
        return [
            'name' => $this->input('name'),
            'sequence' => $this->input('sequence'),
            'is_active' => $this->input('is_active'),
        ];
    }

    protected function failedValidation(Validator $validator): void
    {
        ApiResponse::validationThrowException($validator);
    }
}
