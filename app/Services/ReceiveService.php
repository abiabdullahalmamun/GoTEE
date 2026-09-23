<?php

namespace App\Services;

use App\Enums\ResponseStatus;
use App\Exceptions\ExceptionHandler;
use App\Http\Resources\Receives\ReceiveListResource;
use App\Http\Resources\Receives\ReceiveResource;
use App\Repositories\ItemSerialRepository;
use App\Repositories\ReceiveItemRepository;
use App\Repositories\ReceiveRepository;
use App\Services\Transactions\InventoryTranReceiveService;
use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ReceiveService
{
    protected ReceiveRepository $receiveRepo;
    protected ReceiveItemRepository $receiveItemsRepo;
    protected InventoryTranReceiveService $inventoryService;
    protected ItemSerialRepository $itemSerialRepo;

    public function __construct
    (
        ReceiveRepository $receiveRepo,
        ReceiveItemRepository $receiveItemsRepo,
        InventoryTranReceiveService $inventoryService,
        ItemSerialRepository $itemSerialRepo
    )
    {
        $this->receiveRepo = $receiveRepo;
        $this->receiveItemsRepo = $receiveItemsRepo;
        $this->inventoryService = $inventoryService;
        $this->itemSerialRepo = $itemSerialRepo;
    }

    /**
     * @throws ExceptionHandler
     */
    public function getAllReceives() : AnonymousResourceCollection
    {
        try {
            $storeId = Auth::user()->store_id;
            $transTypeId = 2;
            $resp =  $this->receiveRepo->getAllReceive($storeId,$transTypeId);
            return ReceiveResource::collection($resp);
        }
        catch(QueryException $e){
            throw new ExceptionHandler(ResponseStatus::SERVER_ERROR,ResponseStatus::CODE_INTERNAL_SERVER_ERROR,$e);
        }
    }

    /**
     * @throws ExceptionHandler
     */
    public function createReceive(array $data) : ?ReceiveResource
    {
        DB::beginTransaction();
        try {
            $authData = Auth::user();
            $authId = $authData->id;
            $storeId = $authData->store_id;
            $grnDate = $data['grnDate'];
            $grnNo = $data['grnNo'];
            $supplierId = $data['supplierId'];
            $remark = $data['remark'];
            $subTotal = (float) $data['subTotal'];
            $total = (float) $data['totalAmount'];
            $paid = (float) $data['paidAmount'];
            $due = ($total-$paid);
            $transTypeId = 2;
            $createdAt = now();
            $items = [];
            foreach($data['items'] as $item)
            {
                $dd['itemId'] = (int) $item['itemId'];
                $dd['uomId'] = (int) $item['uomId'];
                $dd['qty'] = (float) $item['qty'];
                $dd['pRate'] = (float) $item['pRate'];
                $dd['rate'] = (float) $item['rate'];
                $dd['warrantyMonth'] = (float) $item['warrantyMonth'];
                $dd['amount'] = (float) $item['amount'];
                $serials = [];
                foreach($item['serials'] as $serial)
                {
                    $isSerialExist  = $this->itemSerialRepo->getBySerialNo($serial['name']);

                    if($isSerialExist){
                        throw new ExceptionHandler('Serial Already Exist ('.$serial['name'].')',ResponseStatus::CODE_UNPROCESSABLE_ENTITY);
                    }

                   $serialData = $this->itemSerialRepo->create([
                        'store_id'=>(int) $storeId,
                        'item_id'=>(int) $item['itemId'],
                        'value' => (string) $serial['name'],
                        'is_stock' => 1,
                        'created_by'=>$authId,
                        'created_at'=>$createdAt,
                    ]);

                    $serials[] = [
                        'id' => (int) $serialData->id,
                        'name' => (string) $serial['name'],
                    ];
                }
                $dd['serials'] = $serials;
                $items[] = $dd;
            }

            $transResp = $this->inventoryService->createReceive(
                grnDate:$grnDate,
                grnNo:$grnNo,
                transTypeId:$transTypeId,
                storeId:$storeId,
                supplierId:$supplierId,
                subTotal:$subTotal,
                total:$total,
                paid:$paid,
                due:$due,
                remark:$remark,
                authId:$authId,
                items:$items
            );

            $receiveData = $this->receiveRepo->getById($transResp['receiveId']);
            DB::commit();
            return new ReceiveResource($receiveData);
        }
        catch(QueryException|Exception $e){
            DB::rollback();
            throw new ExceptionHandler(ResponseStatus::SERVER_ERROR,ResponseStatus::CODE_INTERNAL_SERVER_ERROR,$e);
        }
    }

    protected function _generateReceiveInvoice($authId) : string
    {
        $receiveData = $this->receiveRepo->getLastReceive();
        if(!$receiveData){
            $newItemNumber = 1;
        }else{
            $newItemNumber = $receiveData->id + 1;
        }

        return 'GRN-' . str_pad($newItemNumber, 4, '0', STR_PAD_LEFT);
    }


    /**
     * @throws ExceptionHandler
     */
    public function getReceiveById(int $id): ?ReceiveResource
    {
        try {
            $findData = $this->receiveRepo->getById($id);
            return new ReceiveResource($findData);
        }
        catch(QueryException $e){
            throw new ExceptionHandler(ResponseStatus::SERVER_ERROR,ResponseStatus::CODE_INTERNAL_SERVER_ERROR,$e);
        }
    }

}
