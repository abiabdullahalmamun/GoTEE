<?php
namespace App\Http\Requests\Api\Uoms;

use App\Http\Responses\ApiResponse;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class UomIdRequest extends FormRequest{
    public function authorize() : bool
    {
        return true;
    }
    public function all($keys = null): array
    {
        $data['id'] = $this->route('uomId');
        return $data;
    }

    public function rules() : array
    {
        return [
            'id'=>[
                'required',
                'integer',
                Rule::exists('item_uoms','id')
                    ->where('is_active',true)
            ],

        ];
    }
    public function messages() : array
    {
        return [
            'id.exists'=>'Uom not found'
        ];
    }

    protected function failedValidation(Validator $validator): void
    {
        ApiResponse::validationThrowException($validator);
    }
}
