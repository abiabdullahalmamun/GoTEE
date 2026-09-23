<?php

namespace App\Http\Resources\Receives;

use Illuminate\Http\Resources\Json\JsonResource;

class ReceiveListResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id'=>$this->id,
            'issueReturnId'=>$this->issue_return_id,
            'transDate'=>$this->trans_date,
            'transNo'=>$this->trans_no,
            'grnDate'=>$this->grn_date,
            'grnNo'=>$this->grn_no,
            'store_id'=>$this->store_id,
            'storeName'=>optional($this->store)->name,
            'transTypeId'=>$this->trans_type_id,
            'transTypeName'=>optional($this->transType)->name,
            'supplier_id'=>$this->supplier_id,
            'supplierName'=>optional($this->supplier)->name,
            'subTotal'=>(float) bcadd($this->sub_total_amount, '0', 2),
            'totalAmount'=>(float) bcadd($this->total_amount, '0', 2),
            'paidAmount'=>(float) bcadd($this->paid_amount, '0', 2),
            'dueAmount'=>(float) bcadd($this->due_amount, '0', 2),
            'remark'=>$this->remark,
            'createdBy'=>optional($this->user)->name,
        ];
    }
}
