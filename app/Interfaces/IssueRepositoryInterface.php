<?php

namespace App\Interfaces;

use App\Models\Shift;


interface IssueRepositoryInterface
{
    public function create($data);
    public function getById(int $id);
    public function getAllIssue(int $storeId,int $transTypeId);
    public function getLastIssue();
    public function getReturnableByInvoiceNo(string $invoiceNo,int $transTypeId);
    public function getInvoiceDetail(string $invoiceNo);
    public function getDateWiseSalesReport(int $storeId, string $fromDate,string $toDate);


}
