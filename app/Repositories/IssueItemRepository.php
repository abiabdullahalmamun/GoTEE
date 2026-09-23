<?php

namespace App\Repositories;

use App\Interfaces\IssueItemsRepositoryInterface;
use App\Interfaces\IssueRepositoryInterface;
use App\Models\Order;
use App\Models\IssueItem;
use Illuminate\Support\Facades\Auth;
use Laravel\Sanctum\HasApiTokens;

class IssueItemRepository implements IssueItemsRepositoryInterface
{
    use HasApiTokens;

    public function getAll(bool $status)
    {
        return IssueItem::where('user_id', Auth::id())->where('is_active',$status)->get();
    }

    public function create($data) : IssueItem
    {
        return IssueItem::create($data);
    }

    public function getById(int $id) : ?IssueItem
    {
        return IssueItem::find($id);
    }

}
