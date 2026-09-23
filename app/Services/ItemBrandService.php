<?php

namespace App\Services;

use App\Enums\ResponseStatus;
use App\Exceptions\ExceptionHandler;
use App\Http\Resources\ItemBrands\ItemBrandResource;
use App\Repositories\ItemBrandRepository;
use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Auth;

class ItemBrandService
{
    protected ItemBrandRepository $itemBrandRepository;

    public function __construct(ItemBrandRepository $itemBrandRepository)
    {
        $this->itemBrandRepository = $itemBrandRepository;
    }

    /**
     * @throws ExceptionHandler
     */
    public function getAllItemBrands(): AnonymousResourceCollection
    {
        try {
            $resp =  $this->itemBrandRepository->getAll(true);
            return ItemBrandResource::collection($resp);
        }
        catch(QueryException $e){
            throw new ExceptionHandler(ResponseStatus::SERVER_ERROR,ResponseStatus::CODE_INTERNAL_SERVER_ERROR,$e);
        }
    }

    /**
     * @throws ExceptionHandler
     */
    public function createItemBrand(array $data) : ?ItemBrandResource
    {
        try {
            $authId = Auth::id();
            $data['is_active'] = 1;
            $data['created_at'] = now()->format('Y-m-d H:i:s');
            $data['created_by'] = $authId;
            $resp = $this->itemBrandRepository->create($data);
            return new ItemBrandResource($resp);
        }
        catch(QueryException $e){
            throw new ExceptionHandler(ResponseStatus::SERVER_ERROR,ResponseStatus::CODE_INTERNAL_SERVER_ERROR,$e);
        }
    }


    /**
     * @throws ExceptionHandler
     */
    public function getItemBrandById(int $id): ?ItemBrandResource
    {
        try {

            $findData = $this->itemBrandRepository->getById($id);

            return new ItemBrandResource($findData);
        }
        catch(QueryException $e){
            throw new ExceptionHandler(ResponseStatus::SERVER_ERROR,ResponseStatus::CODE_INTERNAL_SERVER_ERROR,$e);
        }
    }

    /**
     * @throws ExceptionHandler
     */
    public function updateItemBrand(int $id, array $data): ItemBrandResource
    {
        try {

            $findData = $this->itemBrandRepository->getById($id);
            $status = $findData->update($data);
            if(!$status){
                throw new ExceptionHandler(ResponseStatus::SERVER_ERROR,ResponseStatus::CODE_INTERNAL_SERVER_ERROR);
            }

            return new ItemBrandResource($findData);
        }
        catch(QueryException|Exception $e){
            throw new ExceptionHandler(ResponseStatus::SERVER_ERROR,ResponseStatus::CODE_INTERNAL_SERVER_ERROR,$e);
        }
    }

    /**
     * @throws ExceptionHandler
     */
    public function deleteItemBrand(int $id): ItemBrandResource
    {
        try {
            $findData = $this->itemBrandRepository->getById($id);
            $findData->is_active = 0;
            $findData->updated_by = Auth::id();
            $findData->updated_at = now()->format('Y-m-d H:i:s');

            $status = $findData->update();
            if(!$status){
                throw new ExceptionHandler(ResponseStatus::SERVER_ERROR,ResponseStatus::CODE_INTERNAL_SERVER_ERROR);
            }
            return new ItemBrandResource($findData);
        }
        catch(QueryException $e){
            throw new ExceptionHandler(ResponseStatus::SERVER_ERROR,ResponseStatus::CODE_INTERNAL_SERVER_ERROR,$e);
        }
    }
}
