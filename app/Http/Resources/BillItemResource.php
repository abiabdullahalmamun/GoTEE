<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class BillItemResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'bill_head_id' => $this->bill_head_id,
            'amount' => $this->amount,
            'bill_head' => $this->billHead->name
        ];
    }
}
