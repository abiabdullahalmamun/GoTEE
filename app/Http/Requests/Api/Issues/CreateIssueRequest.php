<?php

namespace App\Http\Requests\Api\Issues;

use App\Http\Responses\ApiResponse;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;

class CreateIssueRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'challanDate' => 'required|date',
            'challanNo' => 'nullable|string',
            'customerId' => 'required|integer|exists:customers,id',
            'subTotal' => 'required|numeric|min:1',
            'vatPercent' => 'nullable|numeric|min:0',
            'vatAmount' => 'nullable|numeric|min:0',
            'totalAmount' => 'required|numeric|min:1',
            'totalDiscount' => 'nullable|numeric|min:0',
            'totalPayable' => 'required|numeric|min:1',
            'paidAmount' => 'required|numeric|min:0',
            'dueAmount' => 'required|numeric|min:0',
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
            'items.*.warrantyMonth' => 'required|numeric|min:0',
            'items.*.amount' => 'required|numeric|min:0',
            'items.*.discount' => 'required|numeric|min:0',
            'items.*.total' => 'required|numeric|min:0',
            'items.*.serials' => 'nullable|array',
            'items.*.serials.*.id' => 'required|integer',
            'items.*.serials.*.name' => 'required|string|max:255',
        ];
    }

    public function messages(): array
    {
        return [
            'customerId.exists' => 'This customer does not exist.',
            'items.required' => 'Items cannot be empty.',
            'items.*.itemId.exists' => 'This item does not exist for this customer.',
            'items.*.qty.min' => 'Quantity must be at least 1.',
            'items.*.amount.min' => 'Amount must be at least 0.',

            'items.*.serials.array' => 'Serials must be an array.',
            'items.*.serials.*.id.required' => 'Each serial must have an ID.',
            'items.*.serials.*.id.integer' => 'Serial ID must be a valid integer.',
            'items.*.serials.*.id.exists' => 'The provided serial ID does not exist.',
            'items.*.serials.*.name.required' => 'Each serial must have a name.',
            'items.*.serials.*.name.string' => 'Serial name must be a valid string.',
            'items.*.serials.*.name.max' => 'Serial name must not exceed 255 characters.',
        ];
    }

    public function validated($key = null, $default = null): array
    {
        return [
            'challanDate' => $this->input('challanDate'),
            'challanNo' => $this->input('challanNo'),
            'customerId' => $this->input('customerId'),
            'subTotal' => $this->input('subTotal'),
            'vatPercent' => $this->input('vatPercent'),
            'vatAmount' => $this->input('vatAmount'),
            'totalAmount' => $this->input('totalAmount'),
            'totalDiscount' => $this->input('totalDiscount'),
            'totalPayable' => $this->input('totalPayable'),
            'paidAmount' => $this->input('paidAmount'),
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
                    'discount' => $item['discount'],
                    'total' => $item['total'],
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
