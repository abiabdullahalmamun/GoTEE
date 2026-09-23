<?php

namespace App\Http\Resources\OpeningBalances;

use Illuminate\Http\Resources\Json\JsonResource;

class OpeningBalanceResource extends JsonResource
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
            'items' => $this->receiveItems->map(function($item) {
                return [
                    'id' => $item->id,
                    'receiveId' => $item->receive_id,
                    'categoryName' => optional($item->item->category)->name,
                    'brandName' => optional($item->item->brand)->name,
                    'itemId' => $item->item_id,
                    'itemName' => optional($item->item)->name,
                    'pRate' => (float) bcadd($item->p_rate, '0', 2),
                    'rate' => (float) bcadd($item->rate, '0', 2),
                    'qty' => (float) bcadd($item->qty, '0', 0),
                    'amount' => (float) bcadd($item->amount, '0', 2),
                    'warrantyMonth' => (float) bcadd($item->warranty_month, '0', 0),
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
