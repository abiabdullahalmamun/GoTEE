<?php

namespace App\Http\Controllers\Api;

use App\Enums\ResponseStatus;
use App\Exceptions\ExceptionHandler;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Issues\CreateIssueRequest;
use App\Http\Requests\Api\Issues\IssueIdRequest;
use App\Http\Requests\Api\Issues\SaleInvoiceRequest;
use App\Http\Responses\ApiResponse;
use App\Services\IssueService;
use Exception;
use Illuminate\Http\JsonResponse;


class IssueController extends Controller
{

    protected IssueService $issueService;

    public function __construct(IssueService $issueService){
        $this->issueService = $issueService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        try {
            $resp = $this->issueService->getAllIssues();

            return ApiResponse::success($resp, ResponseStatus::SUCCESS);

        } catch (ExceptionHandler $e) {
            return ApiResponse::ExceptionHandlerResponse($e);
        }
        catch (Exception $e) {
            return ApiResponse::ServerError($e);
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CreateIssueRequest $request): JsonResponse
    {
        try {
            $resp = $this->issueService->createIssue($request->validated());

            return ApiResponse::success($resp, 'Issue inserted successful',ResponseStatus::CODE_CREATED);

        } catch (ExceptionHandler $e) {
            return ApiResponse::ExceptionHandlerResponse($e);
        }
        catch (Exception $e) {
            return ApiResponse::ServerError($e);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(IssueIdRequest $issueId): JsonResponse
    {
        try {

            $resp = $this->issueService->getIssueById($issueId->id);

            return ApiResponse::success($resp, ResponseStatus::SUCCESS);

        } catch (ExceptionHandler $e) {
            return ApiResponse::ExceptionHandlerResponse($e);
        }
        catch (Exception $e) {
            return ApiResponse::ServerError($e);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    public function getIssueReturnableDetailByInvoice(SaleInvoiceRequest $request): JsonResponse
    {
        try {
            $invoiceNo = $request->invoiceNo;
            $resp = $this->issueService->getReturnableIssueByInvoice($invoiceNo);

            return ApiResponse::success($resp, ResponseStatus::SUCCESS);

        } catch (ExceptionHandler $e) {
            return ApiResponse::ExceptionHandlerResponse($e);
        }
        catch (Exception $e) {
            return ApiResponse::ServerError($e);
        }
    }
}
