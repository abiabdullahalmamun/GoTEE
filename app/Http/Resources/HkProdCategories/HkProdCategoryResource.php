<?php

namespace App\Http\Resources\HkProdCategories;

use Illuminate\Http\Resources\Json\JsonResource;

class HkProdCategoryResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id'    => $this->id,
            'code'  => $this->code,
            'slug'  => $this->slug,
            'name'  => $this->name,
            'isActive'=>($this->is_active == 1) ? true : false,
            'sequence'=>$this->sequence,
        ];
    }
}
