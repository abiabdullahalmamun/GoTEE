<?php

namespace App\Services;

use App\Enums\ResponseStatus;
use App\Exceptions\ExceptionHandler;
use App\Http\Resources\Common\PaginatedResourceCollection;
use App\Http\Resources\HkProdCategories\HkProdCategoryResource;
use App\Models\HkProdCategory;
use App\Repositories\HkProdCategoryRepository;
use App\Services\Helpers\CodeSlugGeneratorService;
use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Auth;

class HkProdCategoryService
{
    protected HkProdCategoryRepository $itemCategoryRepository;

    public function __construct(HkProdCategoryRepository $itemCategoryRepository,private CodeSlugGeneratorService $generator)
    {
        $this->itemCategoryRepository = $itemCategoryRepository;
    }

    /**
     * @throws ExceptionHandler
     */
    public function getAllItemCategories(array $filters = [], int $perPage = 10) : PaginatedResourceCollection
    {
        try {
            $resp = $this->itemCategoryRepository->getAll($filters, $perPage);
            return new PaginatedResourceCollection($resp, HkProdCategoryResource::class);
        } catch(QueryException $e){
            throw new ExceptionHandler(ResponseStatus::SERVER_ERROR, ResponseStatus::CODE_INTERNAL_SERVER_ERROR, $e);
        }
    }

    /**
     * @throws ExceptionHandler
     */
    public function getAllHkItemCategories()
    {
        try {
            $resp = $this->itemCategoryRepository->getAllHk(true);
            return HkProdCategoryResource::collection($resp);
        } catch(QueryException $e){
            throw new ExceptionHandler(ResponseStatus::SERVER_ERROR, ResponseStatus::CODE_INTERNAL_SERVER_ERROR, $e);
        }
    }

    /**
     * @throws ExceptionHandler
     */
    public function createItemCategory(array $data) : ?HkProdCategoryResource
    {
        try {
            $authId = authUser()->id;
            $cateModel = new HkProdCategory();
            $code = $this->generator->generateCode($cateModel);
            $slug = $this->generator->generateSlug($cateModel,$data['name']);
            $sequence = $this->generator->generateSequence($cateModel);
            $data['code'] = $code;
            $data['slug'] = $slug;
            $data['sequence'] = $sequence;
            $data['created_by'] = $authId;
            $data['created_at'] = now()->format('Y-m-d H:i:s');

            $resp = $this->itemCategoryRepository->create($data);
            return new HkProdCategoryResource($resp);
        }
        catch(QueryException $e){
            throw new ExceptionHandler(ResponseStatus::SERVER_ERROR,ResponseStatus::CODE_INTERNAL_SERVER_ERROR,$e);
        }
    }


    /**
     * @throws ExceptionHandler
     */
    public function getItemCategoryById(int $id): ?HkProdCategoryResource
    {
        try {

            $findData = $this->itemCategoryRepository->getById($id);

            return new HkProdCategoryResource($findData);
        }
        catch(QueryException $e){
            throw new ExceptionHandler(ResponseStatus::SERVER_ERROR,ResponseStatus::CODE_INTERNAL_SERVER_ERROR,$e);
        }
    }

    /**
     * @throws ExceptionHandler
     */
    public function updateItemCategory(int $id, array $data): HkProdCategoryResource
    {
        try {
            $user = authUser();
            $findData = $this->itemCategoryRepository->getById($id);
            $updateData = [
                'name' => $data['name'],
                'sequence' => $data['sequence'],
                'is_active' => $data['is_active'],
                'updated_at' => now()->format('Y-m-d H:i:s'),
                'updated_by' => $user->id
            ];

            $status = $findData->update($updateData);
            if(!$status){
                throw new ExceptionHandler(ResponseStatus::SERVER_ERROR,ResponseStatus::CODE_INTERNAL_SERVER_ERROR);
            }

            return new HkProdCategoryResource($findData);
        }
        catch(QueryException|Exception $e){
            throw new ExceptionHandler(ResponseStatus::SERVER_ERROR,ResponseStatus::CODE_INTERNAL_SERVER_ERROR,$e);
        }
    }

    /**
     * @throws ExceptionHandler
     */
    public function deleteItemCategory(int $id): HkProdCategoryResource
    {
        try {
            $findData = $this->itemCategoryRepository->getById($id);
            $findData->is_active = 0;
            $findData->updated_by = authUser()->id;
            $findData->updated_at = now()->format('Y-m-d H:i:s');

            $status = $findData->update();
            if(!$status){
                throw new ExceptionHandler(ResponseStatus::SERVER_ERROR,ResponseStatus::CODE_INTERNAL_SERVER_ERROR);
            }
            return new HkProdCategoryResource($findData);
        }
        catch(QueryException $e){
            throw new ExceptionHandler(ResponseStatus::SERVER_ERROR,ResponseStatus::CODE_INTERNAL_SERVER_ERROR,$e);
        }
    }
}
