<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class BillResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'invoice_no' => $this->invoice_no,
            'bill_date' => $this->bill_date->format('Y-m-d'),
            'total_amount' => number_format($this->total_payable, 2),
            'paid_status' => ($this->paid_status == 1) ? 'paid' : 'unpaid',
            'paid_at' => $this->paid_at?->format('Y-m-d H:i:s'),
            'remark' => $this->remark,
            'user' => $this->user?->name
        ];
    }
}
