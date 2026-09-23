<?php

namespace App\Http\Requests\Api\Issues;

use App\Http\Responses\ApiResponse;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SaleInvoiceRequest extends FormRequest{

    public function authorize(): bool
    {
        return true;
    }
    public function all($keys = null): array
    {
        $data['invoiceNo'] = $this->route('invoiceNo');
        return $data;
    }
    public function rules(): array
    {
        return [
            'invoiceNo' => [
                'string',
                'required',
                Rule::exists('issues', 'trans_no')->where(fn ($query) => $query->where('trans_type_id', 3)),
            ],
        ];
    }
    public function messages(): array
    {
            return [
                'invoiceNo.exists'=>'Invalid Sale Invoice',
            ];
    }

    protected function failedValidation(Validator $validator): void
    {
        ApiResponse::validationThrowException($validator);
    }

}
