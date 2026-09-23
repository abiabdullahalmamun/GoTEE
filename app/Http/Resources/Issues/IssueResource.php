<?php

namespace App\Http\Resources\Issues;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class IssueResource extends JsonResource
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
            'items' => $this->issueItems->map(function($item) {
                $transDate = Carbon::parse($this->trans_date);
                $warrantyMonths = (float) $item->warranty_month;
                $warrantyExpireDate = $transDate->copy()->addMonths($warrantyMonths);
                $isWarrantyHave = now()->lessThanOrEqualTo($warrantyExpireDate);
                return [
                    'id' => $item->id,
                    'issueId' => $item->issue_id,
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
                    'warrantyMonth' => $warrantyMonths,
                    'isWarrantyHave' => $isWarrantyHave,
                    'warrantyExpireDate' => $warrantyExpireDate->format('Y-m-d'),
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
