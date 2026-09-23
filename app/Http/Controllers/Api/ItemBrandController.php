<?php

namespace App\Http\Controllers\Api;

use App\Enums\ResponseStatus;
use App\Exceptions\ExceptionHandler;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\ItemBrands\BrandIdRequest;
use App\Http\Requests\Api\ItemBrands\CreateBrandRequest;
use App\Http\Requests\Api\ItemBrands\UpdateBrandRequest;
use App\Http\Responses\ApiResponse;
use App\Services\ItemBrandService;
use Exception;
use Illuminate\Http\JsonResponse;

class ItemBrandController extends Controller
{

    protected ItemBrandService $itemBrandService;

    public function __construct(ItemBrandService $itemBrandService){
        $this->itemBrandService = $itemBrandService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        try {
            $resp = $this->itemBrandService->getAllItemBrands();

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
    public function store(CreateBrandRequest $request): JsonResponse
    {
        try {
            $resp = $this->itemBrandService->createItemBrand($request->validated());

            return ApiResponse::success($resp, 'Brand inserted successful',ResponseStatus::CODE_CREATED);

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
    public function show(BrandIdRequest $brandId): JsonResponse
    {
        try {

            $resp = $this->itemBrandService->getItemBrandById($brandId->id);

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
    public function update(UpdateBrandRequest $request, BrandIdRequest $brandId): JsonResponse
    {
        try {
            $resp = $this->itemBrandService->updateItemBrand($brandId->id,$request->validated());

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
    public function destroy(BrandIdRequest $brandId): JsonResponse
    {
        try {

            $resp = $this->itemBrandService->deleteItemBrand($brandId->id);

            return ApiResponse::success($resp,'Brand removed successfully');

        } catch (ExceptionHandler $e) {
            return ApiResponse::ExceptionHandlerResponse($e);
        }
        catch (Exception $e) {
            return ApiResponse::ServerError($e);
        }
    }

}
