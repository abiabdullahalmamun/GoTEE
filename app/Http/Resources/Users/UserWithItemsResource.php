<?php

namespace App\Http\Resources\Users;

use App\Http\Resources\Items\ItemResourceOld;
use Illuminate\Http\Resources\Json\JsonResource;

class UserWithItemsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'address' => $this->address,
            'typeId' => $this->userType->id,
            'typeName' => $this->userType->name,
            'typeCode' => $this->userType->code,
            'imageUrl' => $this->profile_photo_path ? url('/').$this->profile_photo_path : null,
            'isActive' => $this->is_active ? 1 : 0,
            'items' => ItemResourceOld::collection($this->items),
        ];
    }

}
