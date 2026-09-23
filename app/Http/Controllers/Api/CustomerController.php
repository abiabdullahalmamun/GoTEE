<?php

namespace App\Http\Controllers\Api;

use App\Enums\ResponseStatus;
use App\Exceptions\ExceptionHandler;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Customers\CreateCustomerRequest;
use App\Http\Requests\Api\Customers\CustomerIdRequest;
use App\Http\Requests\Api\Customers\UpdateCustomerRequest;
use App\Http\Responses\ApiResponse;
use App\Services\CustomerService;
use Exception;
use Illuminate\Http\JsonResponse;


class CustomerController extends Controller
{

    protected CustomerService $customerService;

    public function __construct(CustomerService $customerService){
        $this->customerService = $customerService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        try {
            $resp = $this->customerService->getAllCustomer();

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
            $resp = $this->customerService->getAllDependancyHK();

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
    public function store(CreateCustomerRequest $request): JsonResponse
    {
        try {
            $resp = $this->customerService->createCustomer($request->validated());

            return ApiResponse::success($resp, 'Customer inserted successful',ResponseStatus::CODE_CREATED);

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
    public function show(CustomerIdRequest $supplier): JsonResponse
    {
        try {

            $resp = $this->customerService->getCustomerById($supplier->id);

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
    public function update(UpdateCustomerRequest $request, CustomerIdRequest $supplier)//: JsonResponse
    {
        try {

            $resp = $this->customerService->updateCustomer($supplier->id,$request->validated());
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
    public function destroy(CustomerIdRequest $supplier): JsonResponse
    {
        try {

            $resp = $this->customerService->deleteCustomer($supplier->id);
            return ApiResponse::success($resp,'Customer removed successfully');

        } catch (ExceptionHandler $e) {
            return ApiResponse::ExceptionHandlerResponse($e);
        }
        catch (Exception $e) {
            return ApiResponse::ServerError($e);
        }
    }

}
