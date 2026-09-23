<?php

namespace App\Repositories;

use App\Interfaces\IssueReturnRepositoryInterface;
use App\Models\IssueReturn;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Laravel\Sanctum\HasApiTokens;

class IssueReturnRepository implements IssueReturnRepositoryInterface
{
    use HasApiTokens;

    public function getAllIssueReturn(int $storeId,int $transTypeId) // : ?IssueReturn
    {
        return IssueReturn::where('store_id',$storeId)
            ->where('created_at', '>=', Carbon::now()->subMonths(2))
            ->orderBy('id','desc')
            ->get();

    }



    public function create($data) : ?IssueReturn
    {
        return IssueReturn::create($data);
    }

    public function getById(int $id) : ?IssueReturn
    {
        return IssueReturn::find($id);
    }

    public function getLastIssueReturn() : ?IssueReturn
    {
        return IssueReturn::orderBy('id', 'desc')->first();
    }

}
