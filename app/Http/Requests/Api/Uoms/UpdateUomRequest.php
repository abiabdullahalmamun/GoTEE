<?php

namespace App\Http\Requests\Api\Uoms;


use App\Http\Responses\ApiResponse;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class UpdateUomRequest extends FormRequest{
    public function authorize():bool
    {
        return true;
    }

    public function rules():array
    {
        $id = $this->route('uomId');
        return [

            'name' => [
                'required',
                'string',
                'max:50',
                Rule::unique('uoms','name')
                    ->ignore($id)
            ]
        ];
    }
    public function messages():array
    {
        return [
            'name.unique'=>'you already have a uom with this name'
        ];
    }


    public function validated($key = null, $default = null): array
    {
        return [
            'name' => $this->input('name')
        ];
    }

    protected function failedValidation(Validator $validator): void
    {
        ApiResponse::validationThrowException($validator);
    }
}
