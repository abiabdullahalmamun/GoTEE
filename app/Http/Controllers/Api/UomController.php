<?php

namespace App\Http\Controllers\Api;

use App\Enums\ResponseStatus;
use App\Exceptions\ExceptionHandler;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Uoms\UomIdRequest;
use App\Http\Requests\Api\Uoms\CreateUomRequest;
use App\Http\Requests\Api\Uoms\UpdateUomRequest;
use App\Http\Responses\ApiResponse;
use App\Services\UomService;
use Exception;
use Illuminate\Http\JsonResponse;

class UomController extends Controller
{

    protected UomService $uomService;

    public function __construct(UomService $uomService){
        $this->uomService = $uomService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        try {
            $resp = $this->uomService->getAllUoms();

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
    public function store(CreateUomRequest $request): JsonResponse
    {
        try {
            $resp = $this->uomService->createUom($request->validated());

            return ApiResponse::success($resp, 'Uom inserted successful',ResponseStatus::CODE_CREATED);

        }
        catch (ExceptionHandler $e) {
            return ApiResponse::ExceptionHandlerResponse($e);
        }
        catch (Exception $e) {
            return ApiResponse::ServerError($e);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(UomIdRequest $brandId): JsonResponse
    {
        try {

            $resp = $this->uomService->getUomById($brandId->id);

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

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateUomRequest $request, UomIdRequest $brandId): JsonResponse
    {
        try {
            $resp = $this->uomService->updateUom($brandId->id,$request->validated());

            return ApiResponse::success($resp, ResponseStatus::SUCCESS);

        } catch (ExceptionHandler $e) {
            return ApiResponse::ExceptionHandlerResponse($e);
        }
        catch (Exception $e) {
            return ApiResponse::ServerError($e);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(UomIdRequest $brandId): JsonResponse
    {
        try {

            $resp = $this->uomService->deleteUom($brandId->id);

            return ApiResponse::success($resp,'Uom removed successfully');

        } catch (ExceptionHandler $e) {
            return ApiResponse::ExceptionHandlerResponse($e);
        }
        catch (Exception $e) {
            return ApiResponse::ServerError($e);
        }
    }

}
