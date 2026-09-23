<?php

namespace App\Repositories;

use App\Interfaces\ReceiveRepositoryInterface;
use App\Models\Receive;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Laravel\Sanctum\HasApiTokens;

class ReceiveRepository implements ReceiveRepositoryInterface
{
    use HasApiTokens;

    public function getAllReceive(int $storeId,int $transTypeId) // : ?Receive
    {
        return Receive::where('store_id',$storeId)
            ->where('created_at', '>=', Carbon::now()->subMonths(2))
            ->where('trans_type_id',$transTypeId)
            ->orderBy('id','desc')
            ->get();

    }



    public function create($data) : ?Receive
    {
        return Receive::create($data);
    }

    public function getById(int $id) : ?Receive
    {
        return Receive::find($id);
    }

    public function getLastReceive() : ?Receive
    {
        return Receive::orderBy('id', 'desc')->first();
    }

}
