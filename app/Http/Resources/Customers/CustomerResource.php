<?php

namespace App\Http\Resources\Customers;

use Illuminate\Http\Resources\Json\JsonResource;

class CustomerResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'code' => $this->code,
            'phone' => $this->phone,
            'address' => $this->address,
            'image' => $this->image_url ? asset($this->image_url) : null,
            'isActive' => ($this->is_active == 1 || $this->is_active) ? true : false,
        ];
    }
}
