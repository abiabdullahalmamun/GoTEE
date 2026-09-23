<?php

namespace App\Http\Requests\Api\Items;

use App\Enums\ResponseStatus;
use App\Http\Responses\ApiResponse;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class CreateItemRequest extends FormRequest{

    public function authorize(): bool
    {
        return true;
    }
    public function rules(): array
    {
        $userId = Auth::id();
        return [
            'name' => [
                'required',
                'string',
                'max:100',
                Rule::unique('items', 'name'),
            ],
            'typeId' => ['required','integer',Rule::exists('item_types','id')],
            'categoryId' => ['required','integer',Rule::exists('item_categories','id')],
            'brandId' => ['required','integer',Rule::exists('item_brands','id')],
            'uomId' => ['required','integer',Rule::exists('uoms','id')],
            'description' => 'nullable|string|max:1000',
            'videoUrl' => 'nullable|url',
            'isActive' => 'required|boolean',
            'purchasePrice' => 'required|numeric',
            'sellPrice' => 'required|numeric',
            'warrantyMonth' => 'nullable|numeric',
            'minRecordQty' => 'nullable|numeric',
            'variants' => 'nullable|array',
            'images' => 'nullable|array',
            'images.*' => 'nullable|string',
            'variants.*.name' => 'nullable|string|max:30',
            'variants.*.regularPrice' => 'nullable|numeric',
            'variants.*.initStock' => 'nullable|integer',
            'variants.*.isDefault' => 'nullable|boolean',
            'attribute' => 'nullable|array',
            'attributes.*.name' => 'nullable|string|max:50',
            'attributes.*.description' => 'nullable|string|max:300',
        ];
    }

    public function withValidator(Validator $validator)
    {
        $validator->sometimes('veriants.*.value', 'required|numeric', function ($input) {
            return $input->useStock == true;
        });

        $validator->sometimes('veriants.*.value', 'nullable', function ($input) {
            return $input->useStock == false;
        });
    }

    public function messages(): array
    {
            return [
                'name.unique'=>'You have already item with this name',
                'categoryId.required' => 'The category ID is required.',
                'brandId.required' => 'The brand ID is required.',
                'description.required' => 'The description is required.',
                'purchasePrice.required' => 'The purchasePrice is required.',
                'sellPrice.required' => 'The sellPrice is required.',
                'purchasePrice.numeric' => 'The purchasePrice is invalid.',
                'sellPrice.numeric' => 'The sellPrice is invalid.',
                'typeId.required' => 'The type ID is required.',
                'videoUrl.required' => 'The video URL is required.',
                'isActive.required' => 'The is active field is required.',
                'variants.required' => 'The variants are required.',
                'variants.*.name.required' => 'Each variant name is required.',
                'variants.*.value.required' => 'Each variant value is required.',
                'variants.*.regularPrice.required' => 'Each variant regular price is required.',
                'variants.*.sellPrice.required' => 'Each variant sell price is required.',
                'variants.*.initStock.required' => 'Each variant init stock is required.',
                'variants.*.isDefault.required' => 'Each variant isDefault is required.',
                'attributes.required' => 'The details are required.',
                'attributes.*.name.required' => 'Each detail name is required.',
                'attributes.*.description.required' => 'Each detail description is required.',
            ];
    }

    public function validated($key = null, $default = null): array
    {
        return [
            'name' => $this->input('name'),
            'category_id' => $this->input('categoryId'),
            'brand_id' => $this->input('brandId'),
            'description' => $this->input('description'),
            'type_id' => $this->input('typeId'),
            'uom_id' => $this->input('uomId'),
            'video_url' => $this->input('videoUrl'),
            'purchase_price' => $this->input('purchasePrice'),
            'sell_price' => $this->input('sellPrice'),
            'warranty_month' => $this->input('warrantyMonth'),
            'min_record' => $this->input('minRecordQty'),
            'is_active' => $this->input('isActive'),
            'images' => array_map(function($imgString) {
                return $imgString; // Simply return each image string
            }, $this->input('images', [])),
            'variants' => array_map(function($variant) {
                return [
                    'name' => $variant['name'],
                    'value' => $variant['value'],
                    'regular_price' => $variant['regularPrice'],
                    'sell_price' => $variant['sellPrice'],
                    'init_stock_qty' => $variant['initStock'],
                    'isDefault'=>$variant['isDefault'],
                ];
            }, $this->input('variants', [])),
            'attributes' => array_map(function($detail) {
                return [
                    'name' => $detail['name'],
                    'description' => $detail['description'],
                ];
            }, $this->input('attributes', [])),
        ];
    }


    protected function failedValidation(Validator $validator): void
    {
        ApiResponse::validationThrowException($validator);
    }

}
