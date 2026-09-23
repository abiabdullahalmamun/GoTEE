<?php

namespace App\Services;

use App\Enums\ResponseStatus;
use App\Exceptions\ExceptionHandler;
use App\Http\Resources\OpeningBalances\OpeningBalanceResource;
use App\Repositories\ItemRepository;
use App\Repositories\ItemSerialRepository;
use App\Repositories\ReceiveItemRepository;
use App\Repositories\ReceiveRepository;
use App\Services\Transactions\InventoryTranReceiveService;
use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class OpeningBalanceService
{
    protected ReceiveRepository $receiveRepo;
    protected ReceiveItemRepository $receiveItemsRepo;
    protected InventoryTranReceiveService $inventoryService;
    protected ItemSerialRepository $itemSerialRepo;
    protected ItemRepository $itemRepository;

    public function __construct
    (
        ReceiveRepository $receiveRepo,
        ReceiveItemRepository $receiveItemsRepo,
        InventoryTranReceiveService $inventoryService,
        ItemSerialRepository $itemSerialRepo,
        ItemRepository $itemRepository
    )
    {
        $this->receiveRepo = $receiveRepo;
        $this->receiveItemsRepo = $receiveItemsRepo;
        $this->inventoryService = $inventoryService;
        $this->itemSerialRepo = $itemSerialRepo;
        $this->itemRepository = $itemRepository;
    }

    /**
     * @throws ExceptionHandler
     */
    public function getAllOpeningBalances() : AnonymousResourceCollection
    {
        try {
            $storeId = Auth::user()->store_id;
            $transTypeId = 1;
            $resp =  $this->receiveRepo->getAllReceive($storeId,$transTypeId);
            return OpeningBalanceResource::collection($resp);
        }
        catch(QueryException $e){
            throw new ExceptionHandler(ResponseStatus::SERVER_ERROR,ResponseStatus::CODE_INTERNAL_SERVER_ERROR,$e);
        }
    }

    /**
     * @throws ExceptionHandler
     */
    public function createOpeningBalance(array $data) : ?OpeningBalanceResource
    {
        DB::beginTransaction();
        try {
            $authData = Auth::user();
            $authId = $authData->id;
            $storeId = $authData->store_id;
            $grnDate = null;
            $grnNo = null;
            $supplierId = null;
            $remark = $data['remark'];
            $subTotal = (float) $data['subTotal'];
            $total = (float) $data['totalAmount'];
            $paid = 0;
            $due = 0;
            $transTypeId = 1;
            $createdAt = now();
            $items = [];
            foreach($data['items'] as $item)
            {
                $itemId = (int) $item['itemId'];
                $isExist  = $this->inventoryService->checkItemStockStatus($itemId,$storeId);
                if($isExist){
                    $itemData = $this->itemRepository->getById($itemId);
                    $itemName = '';
                    if($itemData){
                        $itemName = $itemData->name;
                    }
                    throw new ExceptionHandler('Item ('.$itemName.') Opening Data Already Exist',ResponseStatus::CODE_UNPROCESSABLE_ENTITY);
                }

                $dd['itemId'] = $itemId;
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
            return new OpeningBalanceResource($receiveData);
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

        return 'OP-' . str_pad($newItemNumber, 4, '0', STR_PAD_LEFT);
    }


    /**
     * @throws ExceptionHandler
     */
    public function getOpeningBalanceById(int $id): ?OpeningBalanceResource
    {
        try {
            $findData = $this->receiveRepo->getById($id);
            return new OpeningBalanceResource($findData);
        }
        catch(QueryException $e){
            throw new ExceptionHandler(ResponseStatus::SERVER_ERROR,ResponseStatus::CODE_INTERNAL_SERVER_ERROR,$e);
        }
    }

}
