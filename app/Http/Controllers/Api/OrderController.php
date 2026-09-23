<?php

namespace App\Http\Controllers\Api;

use App\Enums\ResponseStatus;
use App\Exceptions\ExceptionHandler;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Orders\CreateOrderRequest;
use App\Http\Requests\Api\Orders\OrderIdRequest;
use App\Http\Requests\Api\Orders\UpdateOrderStatusRequest;
use App\Http\Responses\ApiResponse;
use App\Services\OrderService;
use Exception;
use Illuminate\Http\JsonResponse;


class OrderController extends Controller
{

    protected OrderService $orderService;

    public function __construct(OrderService $orderService){
        $this->orderService = $orderService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        try {
            $resp = $this->orderService->getAllOrders();

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
    public function store(CreateOrderRequest $request): JsonResponse
    {
        try {
            $resp = $this->orderService->createOrder($request->validated());

            return ApiResponse::success($resp, 'Order inserted successful',ResponseStatus::CODE_CREATED);

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
    public function show(OrderIdRequest $orderId): JsonResponse
    {
        try {

            $resp = $this->orderService->getOrderById($orderId->id);

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
    public function update(UpdateOrderStatusRequest $request, OrderIdRequest $orderId): JsonResponse
    {
        try {
            $orderStatusCode = $request->validated()['statusCode'];
            $resp = $this->orderService->updateOrderStatus($orderId->id,$orderStatusCode);
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
//    public function destroy(ShiftIdRequest $shiftId): JsonResponse
//    {
//        try {
//
//            $resp = $this->shiftService->deleteShift($shiftId->id);
//
//            return ApiResponse::success($resp,'Shift removed successfully');
//
//        } catch (ExceptionHandler $e) {
//            return ApiResponse::ExceptionHandlerResponse($e);
//        }
//        catch (Exception $e) {
//            return ApiResponse::ServerError($e);
//        }
//    }

}
