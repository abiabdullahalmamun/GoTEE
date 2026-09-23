<?php

namespace App\Http\Controllers\Api;

use App\Enums\ResponseStatus;
use App\Exceptions\ExceptionHandler;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\IssueReturns\CreateIssueReturnRequest;
use App\Http\Requests\Api\IssueReturns\IssueReturnIdRequest;
use App\Http\Responses\ApiResponse;
use App\Services\IssueReturnService;
use Exception;
use Illuminate\Http\JsonResponse;


class IssueReturnController extends Controller
{

    protected IssueReturnService $issueReturnService;

    public function __construct(IssueReturnService $issueReturnService){
        $this->issueReturnService = $issueReturnService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        try {
            $resp = $this->issueReturnService->getAllIssueReturns();

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
    public function store(CreateIssueReturnRequest $request): JsonResponse
    {
        try {
            $resp = $this->issueReturnService->createIssueReturn($request->validated());

            return ApiResponse::success($resp, 'IssueReturn inserted successful',ResponseStatus::CODE_CREATED);

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
    public function show(IssueReturnIdRequest $issueReturnId): JsonResponse
    {
        try {

            $resp = $this->issueReturnService->getIssueReturnById($issueReturnId->id);

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

}
