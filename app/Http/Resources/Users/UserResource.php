<?php

namespace App\Http\Resources\Users;

use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
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
            'imageUrl' => $this->profile_photo_path ? url('/').$this->profile_photo_path : null,
            'isActive' => $this->is_active ? 1 : 0,
            // User Type Information (Handled Safely)
            'typeId'   => optional($this->userType)->id,
            'typeName' => optional($this->userType)->name,
            'typeCode' => optional($this->userType)->code,


            // Company Information (Handled Safely)
            'comp_id'   => optional($this->companyStore->companyInfo)->id,
            'comp_name' => optional($this->companyStore->companyInfo)->name,
            'comp_short_name' => optional($this->companyStore->companyInfo)->short_name,
            'comp_phone' => optional($this->companyStore->companyInfo)->phone,
            'comp_phone2' => optional($this->companyStore->companyInfo)->phone_2,
            'comp_address' => optional($this->companyStore->companyInfo)->address,
            'comp_email' => optional($this->companyStore->companyInfo)->email,
            'comp_website' => optional($this->companyStore->companyInfo)->website,
            'comp_logo' => optional($this->companyStore->companyInfo)->logo_url,

            // Store Information (Handled Safely)
            'store_id'   => optional($this->companyStore)->id,
            'store_name' => optional($this->companyStore)->name,
            'store_code' => optional($this->companyStore)->code,
        ];
    }

}
