<?php

namespace App\Http\Resources\Suppliers;

use Illuminate\Http\Resources\Json\JsonResource;

class SupplierResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'code' => $this->code,
            'phone' => $this->phone,
            'address' => $this->address,
            'image' => $this->image_url ? url($this->image_url) : null,
            'isActive' => ($this->is_active == 1 || $this->is_active) ? true : false,
        ];
    }
}
