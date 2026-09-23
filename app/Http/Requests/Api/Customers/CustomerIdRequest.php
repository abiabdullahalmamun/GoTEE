<?php
namespace App\Http\Requests\Api\Customers;


use App\Http\Responses\ApiResponse;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class CustomerIdRequest extends FormRequest{
    public function authorize() : bool
    {
        return true;
    }
    public function all($keys = null): array
    {
        $data['id'] = $this->route('customerId');
        return $data;
    }
    public function rules() : array
    {
        return [
            'id'=>[
                'required',
                'integer',
                Rule::exists('customers','id')
            ],

        ];
    }
    public function messages() : array
    {
        return [
            'id.exists'=>'Customer not found'
        ];
    }


    protected function failedValidation(Validator $validator): void
    {
        ApiResponse::validationThrowException($validator);
    }
}
