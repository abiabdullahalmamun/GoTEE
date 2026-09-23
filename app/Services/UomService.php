<?php

namespace App\Services;

use App\Enums\ResponseStatus;
use App\Exceptions\ExceptionHandler;
use App\Http\Resources\Uoms\UomResource;
use App\Repositories\UomRepository;
use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Auth;

class UomService
{
    protected UomRepository $uomRepository;

    public function __construct(UomRepository $uomRepository)
    {
        $this->uomRepository = $uomRepository;
    }

    /**
     * @throws ExceptionHandler
     */
    public function getAllUoms(): AnonymousResourceCollection
    {
        try {
            $resp =  $this->uomRepository->getAll(true);
            return UomResource::collection($resp);
        }
        catch(QueryException $e){
            throw new ExceptionHandler(ResponseStatus::SERVER_ERROR,ResponseStatus::CODE_INTERNAL_SERVER_ERROR,$e);
        }
    }

    /**
     * @throws ExceptionHandler
     */
    public function createUom(array $data) : ?UomResource
    {
        try {
            $authId = Auth::id();
            $lastData = $this->uomRepository->getLastOne();
            $code = 100;
            if($lastData){
                $code = $lastData->code + 1;
            }
            $data['code'] = $code;
            $data['rel_fact'] = 1;
            $data['is_active'] = 1;
            $data['created_at'] = now()->format('Y-m-d H:i:s');
            $data['created_by'] = $authId;
            $resp = $this->uomRepository->create($data);
            return new UomResource($resp);
        }
        catch(QueryException $e){
            throw new ExceptionHandler(ResponseStatus::SERVER_ERROR,ResponseStatus::CODE_INTERNAL_SERVER_ERROR,$e);
        }
    }


    /**
     * @throws ExceptionHandler
     */
    public function getUomById(int $id): ?UomResource
    {
        try {

            $findData = $this->uomRepository->getById($id);

            return new UomResource($findData);
        }
        catch(QueryException $e){
            throw new ExceptionHandler(ResponseStatus::SERVER_ERROR,ResponseStatus::CODE_INTERNAL_SERVER_ERROR,$e);
        }
    }

    /**
     * @throws ExceptionHandler
     */
    public function updateUom(int $id, array $data): UomResource
    {
        try {

            $findData = $this->uomRepository->getById($id);
            $status = $findData->update($data);
            if(!$status){
                throw new ExceptionHandler(ResponseStatus::SERVER_ERROR,ResponseStatus::CODE_INTERNAL_SERVER_ERROR);
            }

            return new UomResource($findData);
        }
        catch(QueryException|Exception $e){
            throw new ExceptionHandler(ResponseStatus::SERVER_ERROR,ResponseStatus::CODE_INTERNAL_SERVER_ERROR,$e);
        }
    }

    /**
     * @throws ExceptionHandler
     */
    public function deleteUom(int $id): UomResource
    {
        try {
            $findData = $this->uomRepository->getById($id);
            $findData->is_active = 0;
            $findData->updated_by = Auth::id();
            $findData->updated_at = now()->format('Y-m-d H:i:s');

            $status = $findData->update();
            if(!$status){
                throw new ExceptionHandler(ResponseStatus::SERVER_ERROR,ResponseStatus::CODE_INTERNAL_SERVER_ERROR);
            }
            return new UomResource($findData);
        }
        catch(QueryException $e){
            throw new ExceptionHandler(ResponseStatus::SERVER_ERROR,ResponseStatus::CODE_INTERNAL_SERVER_ERROR,$e);
        }
    }
}
