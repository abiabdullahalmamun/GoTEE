<?php

namespace App\Http\Resources\IssueReturns;

use Illuminate\Http\Resources\Json\JsonResource;

class IssueReturnResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'issueStoreName'=>optional($this->issueRef->store)->name,
            'issueDate'=>optional($this->issueRef)->trans_date,
            'issueTransNo'=>optional($this->issueRef)->trans_no,
            'issueChallanDate'=>optional($this->issueRef)->challan_date,
            'issueChallanNo'=>optional($this->issueRef)->challan_no,
            'issueCustomerId'=>optional($this->issueRef->customer)->id,
            'issueCustomerName'=>optional($this->issueRef->customer)->name,
            'issueCustomerPhone'=>optional($this->issueRef->customer)->phone,
            'issueCustomerAddress'=>optional($this->issueRef->customer)->address,
            'issueSubTotal'=>(float) bcadd(optional($this->issueRef)->sub_total_amount,'0',2),
            'issueVatPercent'=>(float) bcadd(optional($this->issueRef)->vat_percent,'0',2),
            'issueVatAmount'=>(float) bcadd(optional($this->issueRef)->vat_amount,'0',2),
            'issueTotalAmount'=>(float) bcadd(optional($this->issueRef)->total_amount,'0',2),
            'issueDiscount'=>(float) bcadd(optional($this->issueRef)->total_discount,'0',2),
            'issueTotalPayable'=>(float) bcadd(optional($this->issueRef)->total_payable,'0',2),
            'issuePaidAmount'=>(float) bcadd(optional($this->issueRef)->paid_amount,'0',2),
            'issueDueAmount'=>(float) bcadd(optional($this->issueRef)->due_amount,'0',2),
            'id'=>$this->id,
            'issueId'=>$this->issue_id,
            'transDate'=>$this->trans_date,
            'returnDate'=>$this->return_date,
            'returnNo'=>$this->return_no,
            'storeId'=>$this->store_id,
            'storeName'=>optional($this->store)->name,
            'returnTotalAmount'=>(float) bcadd($this->total_return_amount, '0', 2),
            'returnPaidAmount'=>(float) bcadd($this->return_paid_amount, '0', 2),
            'returnDueAmount'=>(float) bcadd($this->return_due_amount, '0', 2),
            'remark'=>$this->reason,
            'createdBy'=>optional($this->user)->name,
            'items' => $this->issueReturnItems->map(function($item) {
                return [
                    'id' => $item->id,
                    'issueReturnId' => $item->issue_return_id,
                    'categoryName' => optional($item->item->category)->name,
                    'brandName' => optional($item->item->brand)->name,
                    'itemId' => $item->item_id,
                    'itemName' => optional($item->item)->name,
                    'pRate' => (float) bcadd($item->p_rate, '0', 2),
                    'rate' => (float) bcadd($item->rate, '0', 2),
                    'qty' => (float) bcadd($item->qty, '0', 0),
                    'amount' => (float) bcadd($item->amount, '0', 2),
                    'discount' => (float) bcadd($item->discount, '0', 2),
                    'total' => (float) bcadd($item->total, '0', 2),
                    'serials' => $item->stockSerials->map(function($serial) {
                        return [
                            'id' => $serial->id,
                            'name' => optional($serial->itemSerial)->value,
                        ];
                    }),
                ];
            }),
        ];
    }
}
