<?php

namespace App\Http\Resources\Items;

use Illuminate\Http\Resources\Json\JsonResource;

class ItemResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'itemCode' => $this->item_code,
            'categoryId' => $this->category_id ?? null,
            'categoryName' => $this->category->name ?? null,
            'brandId' => $this->brand_id ?? null,
            'brandName' => $this->brand->name ?? null,
            'typeId' => $this->type_id ?? null,
            'typeName' => optional($this->itemType)->name ?? null,
            'uomId' => $this->uom_id ?? null,
            'uomName' => optional($this->itemUOM)->name ?? null,
            'typeCode' => optional($this->itemType)->code ?? null,
            'description' => $this->description,
            'videoUrl' => $this->video_url,
            'purchasePrice'=> (float) bcadd($this->purchase_price, '0', 2),
            'sellPrice'=>(float) bcadd($this->sell_price, '0', 2),
            'warrantyMonth'=>(float) bcadd($this->warranty_month, '0', 2),
            'minRecordQty'=>(float) bcadd($this->min_record, '0', 2),
            'defaultImage' => optional($this->firstImage)->image_url ? url(optional($this->firstImage)->image_url) : null,
            'isActive' => ($this->is_active == 1 || $this->is_active) ? true : false,
            'stockQty'=>(float) bcadd($this->lastStock->cls_qty ?? 0, 0, 2),
            'itemSerials' => $this->serials->map(function($srl) {
                return [
                    'id' => $srl->id,
                    'name' => $srl->value,
                ];
            }),
//            'vId'=>optional($this->defaultVariants)->id,
//            'vName'=>optional($this->defaultVariants)->name,
//            'vValue'=> (float) optional($this->defaultVariants)->value,
//            'defaultVariantId' => $this->default_variant_id,
//            'images' => $this->images->map(function($img) {
//                return [
//                    'id' => $img->id,
//                    'url' => $img->image_url ? url('/').$img->image_url : null,
//                ];
//            }),
//            'variants' => $this->variants->map(function($variant) {
//                return [
//                    'id' => $variant->id,
//                    'name' => $variant->name,
//                    'value' => (float) $variant->value,
//                    'regularPrice' => (float) bcadd($variant->regular_price, '0', 2),
//                    'sellPrice' => (float) bcadd($variant->sell_price, '0', 2),
//                    'initStock' => (float) bcadd($variant->init_stock_qty, '0', 2),
//                ];
//            }),
//            'attributes' => $this->attributes->map(function($attribute) {
//                return [
//                    'id' => $attribute->id,
//                    'name' => $attribute->name,
//                    'description' => $attribute->description,
//                ];
//            }),
        ];
    }
}
