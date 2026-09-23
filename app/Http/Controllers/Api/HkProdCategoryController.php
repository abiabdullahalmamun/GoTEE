<?php

namespace App\Http\Controllers\Api;

use App\Enums\ResponseStatus;
use App\Exceptions\ExceptionHandler;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\HkProdCategories\CategoryIdRequest;
use App\Http\Requests\Api\HkProdCategories\CreateCategoryRequest;
use App\Http\Requests\Api\HkProdCategories\UpdateCategoryRequest;
use App\Http\Responses\ApiResponse;
use App\Services\HkProdCategoryService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class HkProdCategoryController extends Controller
{

    protected HkProdCategoryService $itemCategoryService;

    public function __construct(HkProdCategoryService $itemCategoryService){
        $this->itemCategoryService = $itemCategoryService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $filters = [
                'search' => $request->query('search'),
                'is_active' => $request->query('is_active'),
                'sort_by' => $request->query('sort_by', 'id'),
                'sort_dir' => $request->query('sort_dir', 'desc'),
            ];

            $perPage = $request->query('per_page', 10);

            $resp = $this->itemCategoryService->getAllItemCategories($filters, $perPage);

            return ApiResponse::success($resp, ResponseStatus::SUCCESS);

        } catch (ExceptionHandler $e) {
            return ApiResponse::ExceptionHandlerResponse($e);
        } catch (Exception $e) {
            return ApiResponse::ServerError($e);
        }
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        try {
            $resp = $this->itemCategoryService->getAllHkItemCategories();

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
    public function store(CreateCategoryRequest $request): JsonResponse
    {
        try {
            $resp = $this->itemCategoryService->createItemCategory($request->validated());

            return ApiResponse::success($resp, 'Category inserted successful',ResponseStatus::CODE_CREATED);

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
    public function show(CategoryIdRequest $categoryId): JsonResponse
    {
        try {

            $resp = $this->itemCategoryService->getItemCategoryById($categoryId->id);

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
    public function update(UpdateCategoryRequest $request, CategoryIdRequest $categoryId): JsonResponse
    {
        try {
            $resp = $this->itemCategoryService->updateItemCategory($categoryId->id,$request->validated());

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
    public function destroy(CategoryIdRequest $categoryId): JsonResponse
    {
        try {

            $resp = $this->itemCategoryService->deleteItemCategory($categoryId->id);

            return ApiResponse::success($resp,'Category removed successfully');

        } catch (ExceptionHandler $e) {
            return ApiResponse::ExceptionHandlerResponse($e);
        }
        catch (Exception $e) {
            return ApiResponse::ServerError($e);
        }
    }

}
