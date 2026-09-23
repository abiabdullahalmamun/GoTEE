<?php
namespace App\Http\Requests\Api\HkProdCategories;

use App\Http\Responses\ApiResponse;
use App\Models\HkProdCategory;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class CategoryIdRequest extends FormRequest{
    public function authorize() : bool
    {
        return true;
    }
    public function all($keys = null): array
    {
        $data['id'] = $this->route('categoryId');
        return $data;
    }

    public function rules() : array
    {
        return [
            'id'=>[
                'required',
                'integer',
                Rule::exists((new HkProdCategory)->getTable(), 'id'),
            ],

        ];
    }
    public function messages() : array
    {
        return [
            'id.exists'=>'Category not found'
        ];
    }

    protected function failedValidation(Validator $validator): void
    {
        ApiResponse::validationThrowException($validator);
    }
}
