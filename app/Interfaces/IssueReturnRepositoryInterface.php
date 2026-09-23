<?php

namespace App\Interfaces;

use App\Models\Shift;


interface IssueReturnRepositoryInterface
{
    public function create($data);
    public function getById(int $id);
    public function getAllIssueReturn(int $storeId,int $transTypeId);
    public function getLastIssueReturn();
}
