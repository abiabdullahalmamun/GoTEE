<?php

namespace App\Repositories;

use App\Interfaces\ReceiveRepositoryInterface;
use App\Interfaces\StockRepositoryInterface;
use App\Models\Receive;
use App\Models\Stock;
use Illuminate\Support\Facades\Auth;
use Laravel\Sanctum\HasApiTokens;

class StockRepository implements StockRepositoryInterface
{
    use HasApiTokens;

    public function getAll(int $storeId,int | null $status) // : ?Stock
    {
        return Stock::where('store_id',$storeId)
            ->orderBy('id','desc')
            ->limit(100)
            ->get();

    }

    public function create($data) : ?Stock
    {
        return Stock::create($data);
    }

    public function getById(int $id) : ?Stock
    {
        return Stock::find($id);
    }
    public function getLastStock() : ?Stock
    {
        return Stock::orderBy('id', 'desc')->first();
    }

}
