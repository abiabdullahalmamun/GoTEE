<?php

namespace App\Http\Resources\HkProdCategories;

use Illuminate\Http\Resources\Json\JsonResource;

class ShiftResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id'=>$this->id,
            'name'=>$this->name,
            'startAt'=>$this->start_at,
            'endAt'=>$this->end_at,
        ];
    }
}
