<?php

namespace App\Http\Resources\Users;

use Illuminate\Http\Resources\Json\JsonResource;

class UserWithConnectionsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request): array
    {
        $filter = $request->input('filter_data', '');
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
            'recents'=>$this->userConnections->map(function ($connectedUser) {
                return [
                    'id' => $connectedUser->id,
                    'name' => $connectedUser->name,
                    'imageUrl' => $connectedUser->profile_photo_path ? url('/').$connectedUser->profile_photo_path : null,
                    'phone' => $connectedUser->phone,
                    'address' => $connectedUser->address,
                    'code' => $connectedUser->code,
                ];
            })->toArray(),
            'connections' => $this->userConnections->filter(function ($connectedUser) use ($filter) {
                return (stripos($connectedUser->name, $filter) !== false) ||
                    (stripos($connectedUser->phone, $filter) !== false);
                })
                ->values()
                ->map(function ($connectedUser) {
                    return [
                        'id' => $connectedUser->id,
                        'name' => $connectedUser->name,
                        'imageUrl' => $connectedUser->profile_photo_path ? url('/').$connectedUser->profile_photo_path : null,
                        'phone' => $connectedUser->phone,
                        'address' => $connectedUser->address,
                        'code' => $connectedUser->code,
                    ];
                })
                ->toArray(),

        ];
    }

}
