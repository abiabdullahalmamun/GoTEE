<?php

namespace App\Http\Resources\Users;

use Illuminate\Http\Resources\Json\JsonResource;

class UserWithConnectionsRecentVisitsResource extends JsonResource
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
            'typeId' => $this->type_id,
            'imageUrl' => $this->profile_photo_path ? url('/').$this->profile_photo_path : null,
            'isActive' => $this->is_active ? 1 : 0,
            'recents'=>$this->userConnections->map(function ($connectedUser) {
                return [
                    'id' => $connectedUser->id,
                    'name' => $connectedUser->name,
                    'phone' => $connectedUser->phone,
                    'address' => $connectedUser->address,
                    'code' => $connectedUser->code,
                    'imageUrl' => $connectedUser->profile_photo_path ? url('/').$connectedUser->profile_photo_path : null,
                ];
            })->toArray(),
        ];
    }

}
