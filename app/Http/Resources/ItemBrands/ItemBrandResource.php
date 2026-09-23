<?php

namespace App\Http\Resources\ItemBrands;

use Illuminate\Http\Resources\Json\JsonResource;

class ItemBrandResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id'    => $this->id,
            'name'  => $this->name,
//            'sequence'=>$this->sequence,
        ];
    }
}
