<?php

namespace App\Http\Controllers\Api;

use App\Enums\ResponseStatus;
use App\Exceptions\ExceptionHandler;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\OpeningBalances\CreateOpeningBalanceRequest;
use App\Http\Requests\Api\OpeningBalances\OpeningBalanceIdRequest;
use App\Http\Responses\ApiResponse;
use App\Services\OpeningBalanceService;
use Exception;
use Illuminate\Http\JsonResponse;


class OpeningBalanceController extends Controller
{

    protected OpeningBalanceService $openingBalanceService;

    public function __construct(OpeningBalanceService $openingBalanceService){
        $this->openingBalanceService = $openingBalanceService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        try {
            $resp = $this->openingBalanceService->getAllOpeningBalances();

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
    public function store(CreateOpeningBalanceRequest $request): JsonResponse
    {
        try {
            $resp = $this->openingBalanceService->createOpeningBalance($request->validated());

            return ApiResponse::success($resp, 'OpeningBalance inserted successful',ResponseStatus::CODE_CREATED);

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
    public function show(OpeningBalanceIdRequest $receiveId): JsonResponse
    {
        try {

            $resp = $this->openingBalanceService->getOpeningBalanceById($receiveId->id);

            return ApiResponse::success($resp, ResponseStatus::SUCCESS);

        } catch (ExceptionHandler $e) {
            return ApiResponse::ExceptionHandlerResponse($e);
        }
        catch (Exception $e) {
            return ApiResponse::ServerError($e);
        }
    }

}
