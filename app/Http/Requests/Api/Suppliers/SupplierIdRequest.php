<?php
namespace App\Http\Requests\Api\Suppliers;


use App\Http\Responses\ApiResponse;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class SupplierIdRequest extends FormRequest{
    public function authorize() : bool
    {
        return true;
    }
    public function all($keys = null): array
    {
        $data['id'] = $this->route('supplierId');
        return $data;
    }
    public function rules() : array
    {
        return [
            'id'=>[
                'required',
                'integer',
                Rule::exists('suppliers','id')
            ],

        ];
    }
    public function messages() : array
    {
        return [
            'id.exists'=>'Supplier not found'
        ];
    }


    protected function failedValidation(Validator $validator): void
    {
        ApiResponse::validationThrowException($validator);
    }
}
