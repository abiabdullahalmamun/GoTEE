<?php

namespace App\Services;

use App\Enums\ResponseStatus;
use App\Exceptions\ExceptionHandler;
use App\Http\Resources\Issues\DailySaleSummeryResource;
use App\Http\Resources\Issues\IssueResource;
use App\Http\Resources\Orders\OrderListResource;
use App\Http\Resources\Orders\ReceiveResource;
use App\Models\ItemVariant;
use App\Models\UserType;
use App\Repositories\IssueRepository;
use App\Repositories\OrderItemsRepository;
use App\Repositories\OrderRepository;
use App\Repositories\ReportRepository;
use App\Repositories\ShiftRepository;
use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ReportService
{
    protected ReportRepository $reportRepo;
    protected IssueRepository $issueRepo;

    public function __construct(ReportRepository $reportRepo, IssueRepository $issueRepo)
    {
        $this->reportRepo = $reportRepo;
        $this->issueRepo = $issueRepo;
    }

    /**
     * @throws ExceptionHandler
     */
    public function getSaleDetailByInvoice($invoiceNo) //: array
    {
        try {
            $authId = Auth::id();

            $issueData = $this->issueRepo->getInvoiceDetail($invoiceNo);
            return $result = IssueResource::make($issueData);

            $totalOrders = $orders->count();
            $totalOrdersAmount = $orders->whereNotIn('orderStatusCode', [106, 107])->sum('total_payable');
            $cancelOrdersAmount = $orders->whereIn('orderStatusCode', [106, 107])->sum('total_payable');
            $orderResources = OrderListResource::collection($response);

            return [
                'totalOrders' => $totalOrders,
                'totalOrdersAmount' => $totalOrdersAmount,
                'cancelOrdersAmount' => $cancelOrdersAmount,
                'orders' => $orderResources,
            ];
        }
        catch(QueryException $e){
            throw new ExceptionHandler(ResponseStatus::SERVER_ERROR,ResponseStatus::CODE_INTERNAL_SERVER_ERROR,$e);
        }
    }

    /**
     * @throws ExceptionHandler
     */
    public function getDateWiseSaleReport($fromDate,$toDate) : array
    {
        try {
            $storeId = Auth::user()->store_id;
            $fromDate =  $fromDate.' 00:00:00';
            $toDate =  $toDate.' 23:59:59';

            $issueData = $this->issueRepo->getDateWiseSalesReport($storeId,$fromDate,$toDate);
            $totalSaleAmount = collect($issueData)->sum('total_amount');
            $totalDiscountAmount = collect($issueData)->sum('total_discount');
            $totalVatAmount = collect($issueData)->sum('vat_amount');
            $totalSalePayableAmount = collect($issueData)->sum('total_payable');
            $totalSaleCashReceiveAmount = collect($issueData)->sum('paid_amount');
            $totalSaleDueAmount = collect($issueData)->sum('due_amount');

            return [
                "fromDate" => Date('Y-m-d', strtotime($fromDate)),
                "toDate" => Date('Y-m-d', strtotime($toDate)),
                "saleData" => DailySaleSummeryResource::collection($issueData),
                "totalSaleAmount"=>$totalSaleAmount,
                "totalVatAmount"=>$totalVatAmount,
                "totalDiscountAmount"=>$totalDiscountAmount,
                "totalSalePayableAmount"=>$totalSalePayableAmount,
                "totalSaleCashReceiveAmount"=>$totalSaleCashReceiveAmount,
                "totalSaleDueAmount"=>$totalSaleDueAmount,
            ];
        }
        catch(QueryException $e){
            throw new ExceptionHandler(ResponseStatus::SERVER_ERROR,ResponseStatus::CODE_INTERNAL_SERVER_ERROR,$e);
        }
    }


}
