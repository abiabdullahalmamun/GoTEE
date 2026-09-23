<?php

namespace App\Repositories;

use App\Interfaces\StockItemSerialRepositoryInterface;
use App\Interfaces\ReceiveItemsRepositoryInterface;
use App\Interfaces\ReceiveRepositoryInterface;
use App\Models\Order;
use App\Models\ReceiveItem;
use App\Models\StockItemSerial;
use Illuminate\Support\Facades\Auth;
use Laravel\Sanctum\HasApiTokens;

class StockItemSerialRepository implements StockItemSerialRepositoryInterface
{
    use HasApiTokens;

    public function getAll(bool $status)
    {
        return StockItemSerial::where('user_id', Auth::id())->where('is_active',$status)->get();
    }

    public function create($data) : bool
    {
        return StockItemSerial::insert($data);
    }

    public function getById(int $id) : ?StockItemSerial
    {
        return StockItemSerial::find($id);
    }

}
