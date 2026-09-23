<?php

namespace App\Http\Requests\Api\Orders;

use App\Enums\ResponseStatus;
use App\Http\Responses\ApiResponse;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class CreateOrderRequest extends FormRequest{

    public function authorize(): bool
    {
        return true;
    }
    public function rules(): array
    {
        return [
            'supplierId' => 'required|integer|exists:users,id',
            'subTotal' => 'required|numeric',
            'discountAmount' => 'required|numeric',
            'totalPayable' => 'required|numeric',
            'payResp' => 'required|string',
            'items' => 'required|array',
            'items.*.itemId' => ['required','integer',Rule::exists('items','id')->where('user_id', $this->input('supplierId'))],
            'items.*.vId' => ['required','integer',Rule::exists('item_variants','id')->where('item_id', $this->input('items.*.itemId'))],
            'items.*.vQty' => 'required|numeric',
            'items.*.vPrice' => 'required|numeric',
            'items.*.pQty' => 'required|numeric',
            'items.*.pAmount' => 'required|numeric'
        ];
    }

    public function messages(): array
    {
        return [
            'supplierId.exists'=>'This supplier does not exist.',
            'payResp'=>'Payment Response is required',
            'items'=>'items cannot be empty.',
            'items.*.itemId.exists'=>'This item does not exist on this supplier.',
        ];
    }

    public function validated($key = null, $default = null): array
    {
        return [
            'supplierId' => $this->input('supplierId'),
            'subTotal' => $this->input('subTotal'),
            'discountAmount' => $this->input('discountAmount'),
            'totalPayable' => $this->input('totalPayable'),
            'payResp' => $this->input('payResp'),
            'items' => array_map(function($item) {
                return [
                    'itemId' => $item['itemId'],
                    'vId' => $item['vId'],
                    'vQty' => $item['vQty'],
                    'vPrice' => $item['vPrice'],
                    'pQty' => $item['pQty'],
                    'pAmount' => $item['pAmount']
                ];
            }, $this->input('items', [])),

        ];
    }


    protected function failedValidation(Validator $validator): void
    {
        ApiResponse::validationThrowException($validator);
    }

}
