<?php

namespace App\Services;

use App\Enums\ResponseStatus;
use App\Exceptions\ExceptionHandler;
use App\Http\Resources\HkProdCategories\ShiftResource;
use App\Repositories\ShiftRepository;
use Illuminate\Database\QueryException;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Auth;

class ShiftService
{
    protected ShiftRepository $shiftRepo;

    public function __construct(ShiftRepository $shiftRepo)
    {
        $this->shiftRepo = $shiftRepo;
    }

    /**
     * @throws ExceptionHandler
     */
    public function getAllShifts() : AnonymousResourceCollection
    {
        try {

            $resp =  $this->shiftRepo->getAll(true);
            return ShiftResource::collection($resp);

        }
        catch(QueryException $e){
            throw new ExceptionHandler(ResponseStatus::SERVER_ERROR,ResponseStatus::CODE_INTERNAL_SERVER_ERROR,$e);
        }
    }

    /**
     * @throws ExceptionHandler
     */
    public function createShift(array $data) : ?ShiftResource
    {
        try {
            $authId = Auth::id();
            $data['user_id'] = $authId;
            $data['is_active'] = 1;
            $data['created_at'] = now()->format('Y-m-d H:i:s');
            $data['created_by'] = $authId;
            $resp = $this->shiftRepo->create($data);
            return new ShiftResource($resp);
        }
        catch(QueryException $e){
            throw new ExceptionHandler(ResponseStatus::SERVER_ERROR,ResponseStatus::CODE_INTERNAL_SERVER_ERROR,$e);
        }
    }

    /**
     * @throws ExceptionHandler
     */
    public function getShiftById(int $id): ?ShiftResource
    {
        try {

            $findData = $this->shiftRepo->getById($id);

            return new ShiftResource($findData);
        }
        catch(QueryException $e){
            throw new ExceptionHandler(ResponseStatus::SERVER_ERROR,ResponseStatus::CODE_INTERNAL_SERVER_ERROR,$e);
        }
    }

    /**
     * @throws ExceptionHandler
     */
    public function updateShift(int $id, array $data) : ?ShiftResource
    {
        try {

            $findData = $this->shiftRepo->getById($id);
            $status = $findData->update($data);
            if(!$status){
                throw new ExceptionHandler(ResponseStatus::SERVER_ERROR,ResponseStatus::CODE_INTERNAL_SERVER_ERROR);
            }

            return new ShiftResource($findData);
        }
        catch(QueryException $e){
            throw new ExceptionHandler(ResponseStatus::SERVER_ERROR,ResponseStatus::CODE_INTERNAL_SERVER_ERROR,$e);
        }
    }

    /**
     * @throws ExceptionHandler
     */
    public function deleteShift(int $id): ShiftResource
    {
        try {
            $findData = $this->shiftRepo->getById($id);
            $findData->is_active = 0;
            $findData->updated_by = Auth::id();
            $findData->updated_at = now()->format('Y-m-d H:i:s');
            $status = $findData->update();
            if(!$status){
                throw new ExceptionHandler(ResponseStatus::SERVER_ERROR,ResponseStatus::CODE_INTERNAL_SERVER_ERROR);
            }
            return new ShiftResource($findData);
        }
        catch(QueryException $e){
            throw new ExceptionHandler(ResponseStatus::SERVER_ERROR,ResponseStatus::CODE_INTERNAL_SERVER_ERROR,$e);
        }
    }
}
