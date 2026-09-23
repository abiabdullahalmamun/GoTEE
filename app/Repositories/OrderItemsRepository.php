<?php

namespace App\Repositories;

use App\Interfaces\OrderItemsRepositoryInterface;
use App\Interfaces\OrderRepositoryInterface;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Support\Facades\Auth;
use Laravel\Sanctum\HasApiTokens;

class OrderItemsRepository implements OrderItemsRepositoryInterface
{
    use HasApiTokens;

    public function getAll(bool $status)
    {
        return OrderItem::where('user_id', Auth::id())->where('is_active',$status)->get();
    }

    public function create($data) : bool
    {
        return OrderItem::insert($data);
    }

    public function getById(int $id) : ?OrderItem
    {
        return OrderItem::find($id);
    }

}
