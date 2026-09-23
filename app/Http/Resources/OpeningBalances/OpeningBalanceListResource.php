<?php

namespace App\Http\Resources\OpeningBalances;

use Illuminate\Http\Resources\Json\JsonResource;

class OpeningBalanceListResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id'=>$this->id,
            'transDate'=>$this->trans_date,
            'transNo'=>$this->trans_no,
            'store_id'=>$this->store_id,
            'storeName'=>optional($this->store)->name,
            'transTypeId'=>$this->trans_type_id,
            'transTypeName'=>optional($this->transType)->name,
            'subTotal'=>(float) bcadd($this->sub_total_amount, '0', 2),
            'totalAmount'=>(float) bcadd($this->total_amount, '0', 2),
            'remark'=>$this->remark,
            'createdBy'=>optional($this->user)->name,
        ];
    }
}
