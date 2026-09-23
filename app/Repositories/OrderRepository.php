<?php

namespace App\Repositories;

use App\Interfaces\OrderRepositoryInterface;
use App\Models\Order;
use Illuminate\Support\Facades\Auth;
use Laravel\Sanctum\HasApiTokens;

class OrderRepository implements OrderRepositoryInterface
{
    use HasApiTokens;

    public function getAll(int $authId,int | null $status)
    {
        if($status){
            return Order::where(function($query) use ($authId) {
                        $query->where('supplier_id', $authId)
                            ->orWhere('customer_id', $authId);
                    })
                    ->where('is_active',$status)
                    ->orderBy('id','desc')
                    ->get();
        }else{
            return Order::where(function($query) use ($authId) {
                        $query->where('supplier_id', $authId)
                            ->orWhere('customer_id', $authId);
                    })
                    ->orderBy('id','desc')
                    ->get();
            }

    }

    public function create($data) : ?Order
    {
        return Order::create($data);
    }

    public function getById(int $id) : ?Order
    {
        return Order::find($id);
    }
    public function getLastOrder() : ?Order
    {
        return Order::orderBy('id', 'desc')->first();
    }

}
