<?php

namespace App\Http\Requests\Api\OpeningBalances;

use App\Http\Responses\ApiResponse;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;

class CreateOpeningBalanceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'subTotal' => 'required|numeric',
            'totalAmount' => 'required|numeric',
            'remark' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.itemId' => [
                'required',
                'integer',
                Rule::exists('items', 'id'),
            ],
            'items.*.qty' => 'required|numeric|min:1',
            'items.*.uomId' => 'required|integer|exists:uoms,id',
            'items.*.pRate' => 'required|numeric|min:0',
            'items.*.rate' => 'required|numeric|min:0',
            'items.*.amount' => 'required|numeric|min:0',
            'items.*.warrantyMonth' => 'required|numeric|min:0',
            'items.*.serials' => 'nullable|array',
            'items.*.serials.*.id' => 'required|integer',
            'items.*.serials.*.name' => 'required|string|max:255',
        ];
    }

    public function messages(): array
    {
        return [];
    }

    public function validated($key = null, $default = null): array
    {
        return [
            'subTotal' => $this->input('subTotal'),
            'totalAmount' => $this->input('totalAmount'),
            'remark' => $this->input('remark'),
            'items' => array_map(function ($item) {
                return [
                    'itemId' => $item['itemId'],
                    'qty' => $item['qty'],
                    'pRate' => $item['pRate'],
                    'rate' => $item['rate'],
                    'warrantyMonth' => $item['warrantyMonth'],
                    'uomId' => $item['uomId'],
                    'amount' => $item['amount'],
                    'serials' => isset($item['serials']) ? array_map(function ($serial) {
                        return [
                            'id' => $serial['id'],
                            'name' => $serial['name'],
                        ];
                    }, $item['serials']) : [],
                ];
            }, $this->input('items', [])),
        ];
    }

    protected function failedValidation(Validator $validator): void
    {
        ApiResponse::validationThrowException($validator);
    }
}
