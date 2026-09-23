<?php

namespace App\Http\Resources\Issues;

use Illuminate\Http\Resources\Json\JsonResource;

class IssueListResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id'=>$this->id,
            'receiveReturnId'=>$this->receive_return_id,
            'transDate'=>$this->trans_date,
            'transNo'=>$this->trans_no,
            'challanDate'=>$this->challan_date,
            'challanNo'=>$this->challan_no,
            'store_id'=>$this->store_id,
            'storeName'=>optional($this->store)->name,
            'transTypeId'=>$this->trans_type_id,
            'transTypeName'=>optional($this->transType)->name,
            'customer_id'=>$this->customer_id,
            'customerName'=>optional($this->customer)->name,
            'customerPhone'=>optional($this->customer)->phone,
            'customerAddress'=>optional($this->customer)->address,
            'subTotal'=>(float) bcadd($this->sub_total_amount, '0', 2),
            'vatPercent'=>(float) bcadd($this->vat_percent, '0', 2),
            'vatAmount'=>(float) bcadd($this->vat_amount, '0', 2),
            'totalAmount'=>(float) bcadd($this->total_amount, '0', 2),
            'totalDiscount'=>(float) bcadd($this->total_discount, '0', 2),
            'totalPayable'=>(float) bcadd($this->total_payable, '0', 2),
            'paidAmount'=>(float) bcadd($this->paid_amount, '0', 2),
            'dueAmount'=>(float) bcadd($this->due_amount, '0', 2),
            'remark'=>$this->remark,
            'createdBy'=>optional($this->user)->name,
        ];
    }
}
