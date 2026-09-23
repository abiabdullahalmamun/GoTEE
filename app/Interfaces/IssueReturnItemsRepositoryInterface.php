<?php

namespace App\Interfaces;

use App\Models\Shift;


interface IssueReturnItemsRepositoryInterface
{
    public function create($data);
    public function getById(int $id);
    public function checkItemIssueReturnStatus(int $itemId);
    public function getTotalReturnQtyByIssueItemId(int $issueItemId);
}
