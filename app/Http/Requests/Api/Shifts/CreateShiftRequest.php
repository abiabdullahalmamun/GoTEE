<?php

namespace App\Http\Requests\Api\Shifts;


use App\Http\Responses\ApiResponse;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class CreateShiftRequest extends FormRequest{

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
                'max:100',
                Rule::unique('shifts','name')
                    ->where('user_id',Auth::id())
            ],
            'startAt'=>['required','time:H:i'],
            'endAt'=>['required','time:H:i','after:start'],
        ];
    }
    public function messages(): array
    {
            return [
                'name.unique'=>'Shift name already exists'
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
        ApiResponse::validation($validator);
    }

}
