<?php

namespace App\Http\Controllers\Api;

use App\Enums\ResponseStatus;
use App\Exceptions\ExceptionHandler;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Issues\SaleInvoiceRequest;
use App\Http\Requests\Api\Reports\FromDateToDateRequest;
use App\Http\Responses\ApiResponse;
use App\Services\ReportService;
use Exception;
use Illuminate\Http\JsonResponse;


class ReportController extends Controller
{

    protected ReportService $reportService;

    public function __construct(ReportService $reportService){
        $this->reportService = $reportService;
    }

    /**
     * Display a listing of the resource.
     */
    public function getSaleDetailByInvoice(SaleInvoiceRequest $request) : JsonResponse
    {
        try {
            $invoiceNo = $request->invoiceNo;
            $resp = $this->reportService->getSaleDetailByInvoice($invoiceNo);
            return ApiResponse::success($resp, ResponseStatus::SUCCESS);
        } catch (ExceptionHandler $e) {
            return ApiResponse::ExceptionHandlerResponse($e);
        }
        catch (Exception $e) {
            return ApiResponse::ServerError($e);
        }
    }
    /**
     * Display a listing of the resource.
     */
    public function getDateWiseSaleReport(FromDateToDateRequest $request) : JsonResponse
    {
        try {
            $fromDate = $request->fromDate;
            $toDate = $request->toDate;
            $resp = $this->reportService->getDateWiseSaleReport($fromDate,$toDate);
            return ApiResponse::success($resp, ResponseStatus::SUCCESS);
        } catch (ExceptionHandler $e) {
            return ApiResponse::ExceptionHandlerResponse($e);
        }
        catch (Exception $e) {
            return ApiResponse::ServerError($e);
        }
    }
}
