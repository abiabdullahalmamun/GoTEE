<?php

namespace App\Http\Controllers\Api;

use App\Enums\ResponseStatus;
use App\Exceptions\ExceptionHandler;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Items\CreateItemRequest;
use App\Http\Requests\Api\Items\ItemIdRequest;
use App\Http\Requests\Api\Items\UpdateItemRequest;
use App\Http\Requests\Api\Shifts\ShiftIdRequest;
use App\Http\Requests\Api\Shifts\CreateShiftRequest;
use App\Http\Requests\Api\Shifts\UpdateShiftRequest;
use App\Http\Responses\ApiResponse;
use App\Services\ItemService;
use Exception;
use Illuminate\Http\JsonResponse;


class ItemController extends Controller
{

    protected ItemService $itemService;

    public function __construct(ItemService $itemService){
        $this->itemService = $itemService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        try {
            $resp = $this->itemService->getAllItems();

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
            $resp = $this->itemService->getAllDependancyHK();

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
    public function store(CreateItemRequest $request): JsonResponse
    {
        try {
            $resp = $this->itemService->createItem($request->validated());

            return ApiResponse::success($resp, 'Item inserted successful',ResponseStatus::CODE_CREATED);

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
    public function show(ItemIdRequest $itemId): JsonResponse
    {
        try {

            $resp = $this->itemService->getItemById($itemId->id);

            return ApiResponse::success($resp, ResponseStatus::SUCCESS);

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
    public function itemLastStockPerStore(ItemIdRequest $itemId): JsonResponse
    {
        try {

            $resp = $this->itemService->getItemLastStockPerStore($itemId->id);

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
    public function update(UpdateItemRequest $request, ItemIdRequest $itemId)//: JsonResponse
    {
        try {

            $resp = $this->itemService->updateItem($itemId->id,$request->validated());
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
    public function destroy(ItemIdRequest $itemId): JsonResponse
    {
        try {

            $resp = $this->itemService->deleteItem($itemId->id);
            return ApiResponse::success($resp,'Item removed successfully');

        } catch (ExceptionHandler $e) {
            return ApiResponse::ExceptionHandlerResponse($e);
        }
        catch (Exception $e) {
            return ApiResponse::ServerError($e);
        }
    }

}
