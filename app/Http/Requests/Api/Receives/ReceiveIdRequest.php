<?php
namespace App\Http\Requests\Api\Receives;


use App\Http\Responses\ApiResponse;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class ReceiveIdRequest extends FormRequest{

    public function authorize() : bool
    {
        return true;
    }

    public function all($keys = null): array
    {
        $data['id'] = $this->route('receiveId');
        return $data;
    }

    public function rules() : array
    {
        return [
            'id'=>[
                'required',
                'integer',
                Rule::exists('receives', 'id')->where(fn ($query) => $query->where('trans_type_id', 2)),
            ],

        ];
    }

    public function messages() : array
    {
        return [
            'id.exists'=>'Receive not found'
        ];
    }


    protected function failedValidation(Validator $validator): void
    {
        ApiResponse::validationThrowException($validator);
    }

}
