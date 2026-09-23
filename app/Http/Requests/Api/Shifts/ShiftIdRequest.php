<?php
namespace App\Http\Requests\Api\Shifts;


use App\Http\Responses\ApiResponse;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class ShiftIdRequest extends FormRequest{
    public function authorize() : bool
    {
        return true;
    }
    public function all($keys = null): array
    {
        $data['id'] = $this->route('shiftId');
        return $data;
    }

    public function rules() : array
    {
        return [
            'id'=>[
                'required',
                'integer',
                Rule::exists('shifts','id')
                    ->where('user_id',Auth::id())
            ],

        ];
    }

    public function messages() : array
    {
        return [
            'id.exists'=>'Shift not found'
        ];
    }


    protected function failedValidation(Validator $validator): void
    {
        ApiResponse::validationThrowException($validator);
    }
}
