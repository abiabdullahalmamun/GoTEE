<?php

namespace App\Http\Resources\Orders;

use Illuminate\Http\Resources\Json\JsonResource;

class OrderListResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id'=>$this->id,
            'orderDate'=>$this->order_date,
            'invoice'=>$this->invoice_no,
            'invoiceUrl'=>'https://www.antennahouse.com/hubfs/xsl-fo-sample/pdf/basic-link-1.pdf',
            'customerId'=>$this->customer_id,
            'customerName'=>$this->customer->name,
            'supplierId'=>$this->supplier_id,
            'supplierName'=>$this->supplier->name,
            'subTotal'=>(float) bcadd($this->sub_total, '0', 2),
            'totalDiscount'=>(float) bcadd($this->total_discount, '0', 2),
            'totalPayable'=>(float) bcadd($this->total_payable, '0', 2),
            'orderStatusCode'=>$this->order_status_code,
            'orderStatus'=>$this->orderStatus->name,
        ];
    }
}
