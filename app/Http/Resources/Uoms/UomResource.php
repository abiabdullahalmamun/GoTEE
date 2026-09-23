<?php

namespace App\Http\Resources\Uoms;

use Illuminate\Http\Resources\Json\JsonResource;

class UomResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id'    => $this->id,
            'name'  => $this->name,
            'code'  => $this->code,
            'relFact'  => (float) bcadd($this->rel_fact, '0', 2),
//            'sequence'=>$this->sequence,
        ];
    }
}
