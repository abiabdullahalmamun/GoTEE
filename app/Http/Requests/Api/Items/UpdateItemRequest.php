<?php

namespace App\Http\Requests\Api\Items;

use App\Http\Responses\ApiResponse;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class UpdateItemRequest extends FormRequest{

    public function authorize(): bool
    {
        return true;
    }
    public function all($keys = null): array
    {
        $data = parent::all($keys);
        $data['id'] = $this->route('itemId');
        return $data;
    }
    public function rules(): array
    {
        $userId = Auth::id();
        return [
            'name' => [
                'required',
                'string',
                'max:100',
            ],
            'categoryId' => ['required','integer',Rule::exists('item_categories','id')],
            'brandId' => ['required','integer',Rule::exists('item_brands','id')],
            'uomId' => ['required','integer',Rule::exists('uoms','id')],
            'description' => 'nullable|string|max:1000',
            'videoUrl' => 'nullable|url',
            'useStock' => 'nullable|boolean',
            'isActive' => 'required|boolean',
            'images' => 'nullable|array',
            'sellPrice' => 'required|numeric',
            'purchasePrice' => 'required|numeric',
            'warrantyMonth' => 'nullable|numeric',
            'minRecordQty' => 'nullable|numeric',
            'images.*' => 'nullable|string',
            'network_images' => 'nullable|array',
            'network_images.*' => 'nullable|string',
            'variants' => 'nullable|array',
            'variants.*.name' => 'required|string|max:30',
            'variants.*.regularPrice' => 'required|numeric',
            'variants.*.initStock' => 'nullable|integer',
            'variants.*.isDefault' => 'required|boolean',
            'attribute' => 'nullable|array',
            'attributes.*.name' => 'required|string|max:50',
            'attributes.*.description' => 'required|string|max:300',
        ];
    }

    public function withValidator(Validator $validator): void
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
            'typeId.required' => 'The type ID is required.',
            'videoUrl.required' => 'The video URL is required.',
            'purchasePrice.required' => 'The purchasePrice is required.',
            'sellPrice.required' => 'The sellPrice is required.',
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
            'networkImages' => array_map(function($imgString) {
                return $imgString; // Simply return each image string
            }, $this->input('network_images', [])),
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
