<?php

namespace App\Http\Controllers\Api;

use App\Enums\ResponseStatus;
use App\Exceptions\ExceptionHandler;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Receives\CreateReceiveRequest;
use App\Http\Requests\Api\Receives\ReceiveIdRequest;
use App\Http\Responses\ApiResponse;
use App\Services\ReceiveService;
use Exception;
use Illuminate\Http\JsonResponse;


class ReceiveController extends Controller
{

    protected ReceiveService $receiveService;

    public function __construct(ReceiveService $receiveService){
        $this->receiveService = $receiveService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        try {
            $resp = $this->receiveService->getAllReceives();

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
    public function store(CreateReceiveRequest $request): JsonResponse
    {
        try {
            $resp = $this->receiveService->createReceive($request->validated());

            return ApiResponse::success($resp, 'Receive inserted successful',ResponseStatus::CODE_CREATED);

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
    public function show(ReceiveIdRequest $receiveId): JsonResponse
    {
        try {

            $resp = $this->receiveService->getReceiveById($receiveId->id);

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
