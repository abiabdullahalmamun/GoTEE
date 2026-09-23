<?php

namespace App\Repositories;

use App\Interfaces\ReportRepositoryInterface;
use App\Models\Order;
use Illuminate\Support\Facades\Auth;
use Laravel\Sanctum\HasApiTokens;

class ReportRepository implements ReportRepositoryInterface
{
    use HasApiTokens;

    public function getDateWise(int $authId, string $dateVal, int | null $status)
    {
        if($status){
            return Order::where(function($query) use ($authId) {
                        $query->where('supplier_id', $authId)
                            ->orWhere('customer_id', $authId);
                    })
                    ->whereDate('order_date', $dateVal)
                    ->where('is_active',$status)
                    ->orderBy('id','desc')
                    ->get();
        }else{
            return Order::where(function($query) use ($authId) {
                        $query->where('supplier_id', $authId)
                            ->orWhere('customer_id', $authId);
                    })
                    ->whereDate('order_date', $dateVal)
                    ->orderBy('id','desc')
                    ->get();
            }

    }

}
