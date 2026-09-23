<?php

namespace App\Http\Resources\Orders;

use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id'=>$this->id,
            'orderDate'=>$this->order_date,
            'invoice'=>$this->invoice_no,
            'customerId'=>$this->customer_id,
            'customerName'=>$this->customer->name,
            'supplierId'=>$this->supplier_id,
            'supplierName'=>$this->supplier->name,
            'subTotal'=>(float) bcadd($this->sub_total, '0', 2),
            'totalDiscount'=>(float) bcadd($this->total_discount, '0', 2),
            'totalPayable'=>(float) bcadd($this->total_payable, '0', 2),
            'orderStatusCode'=>$this->order_status_code,
            'orderStatus'=>$this->orderStatus->name,
            'orderItems' => $this->orderItems->map(function($orderItem) {
                return [
                    'id' => $orderItem->id,
                    'itemId' => $orderItem->item_id,
                    'itemName' => $orderItem->item->name,
                    'vId' =>$orderItem->v_id,
                    'vName' =>$orderItem->v_name,
                    'vQty' => (float) bcadd($orderItem->v_qty, '0', 2),
                    'pQty' => (float) bcadd($orderItem->qty, '0', 2),
                    'pAmount' => (float) bcadd($orderItem->total_amount, '0', 2),
                ];
            }),
        ];
    }
}
