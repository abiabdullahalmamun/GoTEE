<?php

namespace App\Repositories;

use App\Interfaces\IssueRepositoryInterface;
use App\Models\Issue;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Laravel\Sanctum\HasApiTokens;

class IssueRepository implements IssueRepositoryInterface
{
    use HasApiTokens;

    public function getAllIssue(int $storeId,int $transTypeId) // : ?Issue
    {
        return Issue::where('store_id',$storeId)
            ->where('created_at', '>=', Carbon::now()->subMonths(2))
            ->where('trans_type_id',$transTypeId)
            ->orderBy('id','desc')
            ->get();

    }

    public function create($data) : ?Issue
    {
        return Issue::create($data);
    }

    public function getById(int $id) : ?Issue
    {
        return Issue::find($id);
    }

    public function getLastIssue() : ?Issue
    {
        return Issue::orderBy('id', 'desc')->first();
    }

    public function getReturnableByInvoiceNo(string $invoiceNo,$transTypeId) : ?Issue
    {
        return  Issue::with(['issueItems.issueReturnItems.receiveItem.stockSerials.itemSerial'])
            ->where('trans_no', $invoiceNo)
            ->where('trans_type_id', $transTypeId)
            ->whereNull('receive_return_id')
            ->first();
    }


    public function getInvoiceDetail(string $invoiceNo) : ?Issue
    {
        return Issue::where('trans_no',$invoiceNo)->orderBy('id', 'desc')->first();
    }


    public function getDateWiseSalesReport(int $storeId,string $fromDate,string $toDate) //: ?Issue
    {
        return Issue::where('store_id',$storeId)->whereBetween('trans_date', [$fromDate, $toDate])->orderBy('id', 'desc')->orderBy('id')->get();
    }




}
