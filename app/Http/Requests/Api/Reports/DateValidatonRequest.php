<?php

namespace App\Http\Requests\Api\Reports;

use App\Http\Responses\ApiResponse;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class DateValidatonRequest extends FormRequest{

    public function authorize(): bool
    {
        return true;
    }
    public function rules(): array
    {
        return [
            'dateVal' => [
                'required',      // The field is required
                'date',          // The field must be a valid date
                'date_format:Y-m-d'
            ],
        ];
    }
    public function messages(): array
    {
            return [
                'dateVal.date_format'=>'Invalid date format. follow (Y-m-d)',
            ];
    }

    public function all($keys = null): array
    {
        $data['dateVal'] = $this->input('dateVal');
        return $data;
    }

    protected function failedValidation(Validator $validator): void
    {
        ApiResponse::validationThrowException($validator);
    }

}
