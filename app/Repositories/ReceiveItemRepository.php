<?php

namespace App\Repositories;

use App\Interfaces\ReceiveItemsRepositoryInterface;
use App\Interfaces\ReceiveRepositoryInterface;
use App\Models\Order;
use App\Models\ReceiveItem;
use Illuminate\Support\Facades\Auth;
use Laravel\Sanctum\HasApiTokens;

class ReceiveItemRepository implements ReceiveItemsRepositoryInterface
{
    use HasApiTokens;

    public function getAll(bool $status)
    {
        return ReceiveItem::where('user_id', Auth::id())->where('is_active',$status)->get();
    }

    public function create($data) : ReceiveItem
    {
        return ReceiveItem::create($data);
    }

    public function getById(int $id) : ?ReceiveItem
    {
        return ReceiveItem::find($id);
    }

    public function checkItemReceiveStatus(int $itemId) : bool
    {
        return ReceiveItem::where('item_id',$itemId)->exists();
    }

}
