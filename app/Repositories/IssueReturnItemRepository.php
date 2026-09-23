<?php

namespace App\Repositories;

use App\Interfaces\IssueReturnItemsRepositoryInterface;
use App\Interfaces\IssueReturnRepositoryInterface;
use App\Models\Order;
use App\Models\IssueReturnItem;
use Illuminate\Support\Facades\Auth;
use Laravel\Sanctum\HasApiTokens;
use phpDocumentor\Reflection\PseudoTypes\Numeric_;

class IssueReturnItemRepository implements IssueReturnItemsRepositoryInterface
{
    use HasApiTokens;

    public function create($data) : IssueReturnItem
    {
        return IssueReturnItem::create($data);
    }

    public function getById(int $id) : ?IssueReturnItem
    {
        return IssueReturnItem::find($id);
    }

    public function checkItemIssueReturnStatus(int $itemId) : bool
    {
        return IssueReturnItem::where('item_id',$itemId)->exists();
    }
    public function getTotalReturnQtyByIssueItemId(int $issueItemId) : float
    {
        return IssueReturnItem::where('issue_item_id',$issueItemId)->sum('qty');
    }

}
