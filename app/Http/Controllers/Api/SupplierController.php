<?php

namespace App\Http\Controllers\Api;

use App\Enums\ResponseStatus;
use App\Exceptions\ExceptionHandler;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Suppliers\CreateSupplierRequest;
use App\Http\Requests\Api\Suppliers\SupplierIdRequest;
use App\Http\Requests\Api\Suppliers\UpdateSupplierRequest;
use App\Http\Responses\ApiResponse;
use App\Services\SupplierService;
use Exception;
use Illuminate\Http\JsonResponse;


class SupplierController extends Controller
{

    protected SupplierService $supplierService;

    public function __construct(SupplierService $supplierService){
        $this->supplierService = $supplierService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        try {
            $resp = $this->supplierService->getAllSupplier();

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
        try {
            $resp = $this->supplierService->getAllDependancyHK();

            return ApiResponse::success($resp, ResponseStatus::SUCCESS);

        } catch (ExceptionHandler $e) {
            return ApiResponse::ExceptionHandlerResponse($e);
        }
        catch (Exception $e) {
            return ApiResponse::ServerError($e);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CreateSupplierRequest $request): JsonResponse
    {
        try {
            $resp = $this->supplierService->createSupplier($request->validated());

            return ApiResponse::success($resp, 'Supplier inserted successful',ResponseStatus::CODE_CREATED);

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
    public function show(SupplierIdRequest $supplier): JsonResponse
    {
        try {

            $resp = $this->supplierService->getSupplierById($supplier->id);

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
    public function update(UpdateSupplierRequest $request, SupplierIdRequest $supplier)//: JsonResponse
    {
        try {

            $resp = $this->supplierService->updateSupplier($supplier->id,$request->validated());
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
    public function destroy(SupplierIdRequest $supplier): JsonResponse
    {
        try {

            $resp = $this->supplierService->deleteSupplier($supplier->id);
            return ApiResponse::success($resp,'Supplier removed successfully');

        } catch (ExceptionHandler $e) {
            return ApiResponse::ExceptionHandlerResponse($e);
        }
        catch (Exception $e) {
            return ApiResponse::ServerError($e);
        }
    }

}
