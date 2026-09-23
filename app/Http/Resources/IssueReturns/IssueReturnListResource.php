<?php

namespace App\Http\Resources\IssueReturns;

use Illuminate\Http\Resources\Json\JsonResource;

class IssueReturnListResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id'=>$this->id,
            'issueId'=>$this->issue_id,
            'transDate'=>$this->trans_date,
            'returnDate'=>$this->return_date,
            'returnNo'=>$this->return_no,
            'store_id'=>$this->store_id,
            'storeName'=>optional($this->store)->name,
            'returnTotalAmount'=>(float) bcadd($this->total_return_amount, '0', 2),
            'returnPaidAmount'=>(float) bcadd($this->return_paid_amount, '0', 2),
            'returnDueAmount'=>(float) bcadd($this->return_due_amount, '0', 2),
            'remark'=>$this->reason,
            'createdBy'=>optional($this->user)->name,
        ];
    }
}
