<?php

namespace App\Services;

use App\Enums\ResponseStatus;
use App\Exceptions\ExceptionHandler;
use App\Http\Resources\IssueReturns\IssueReturnResource;
use App\Repositories\IssueItemRepository;
use App\Repositories\IssueReturnItemRepository;
use App\Repositories\IssueReturnRepository;
use App\Repositories\ItemRepository;
use App\Repositories\ItemSerialRepository;
use App\Repositories\ReceiveItemRepository;
use App\Repositories\ReceiveRepository;
use App\Services\Transactions\InventoryTranIssueReturnService;
use App\Services\Transactions\InventoryTranReceiveService;
use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class IssueReturnService
{
    protected ReceiveRepository $receiveRepo;
    protected IssueReturnRepository $issueReturnRepo;
    protected IssueReturnItemRepository $issueReturnItemsRepo;
    protected InventoryTranReceiveService $inventoryService;
    protected InventoryTranIssueReturnService $inventoryIssueReturnService;
    protected ItemSerialRepository $itemSerialRepo;
    protected IssueItemRepository $issueItemRepo;
    protected ItemRepository $itemRepo;

    public function __construct
    (
        ReceiveRepository $receiveRepo,
        IssueReturnRepository $issueReturnRepo,
        IssueReturnItemRepository $issueReturnItemsRepo,
        InventoryTranReceiveService $inventoryService,
        InventoryTranIssueReturnService $inventoryIssueReturnService,
        ItemSerialRepository $itemSerialRepo,
        IssueItemRepository $issueItemRepo,
        ItemRepository $itemRepo
    )
    {
        $this->receiveRepo = $receiveRepo;
        $this->issueReturnRepo = $issueReturnRepo;
        $this->issueReturnItemsRepo = $issueReturnItemsRepo;
        $this->inventoryService = $inventoryService;
        $this->inventoryIssueReturnService = $inventoryIssueReturnService;
        $this->itemSerialRepo = $itemSerialRepo;
        $this->issueItemRepo = $issueItemRepo;
        $this->itemRepo = $itemRepo;
    }

    /**
     * @throws ExceptionHandler
     */
    public function getAllIssueReturns() : AnonymousResourceCollection
    {
        try {
            $storeId = Auth::user()->store_id;
            $transTypeId = 7;
            $resp =  $this->issueReturnRepo->getAllIssueReturn($storeId,$transTypeId);
            return IssueReturnResource::collection($resp);
        }
        catch(QueryException $e){
            throw new ExceptionHandler(ResponseStatus::SERVER_ERROR,ResponseStatus::CODE_INTERNAL_SERVER_ERROR,$e);
        }
    }

    /**
     * @throws ExceptionHandler
     */
    public function createIssueReturn(array $data) : ?IssueReturnResource
    {
        DB::beginTransaction();

        try {
            $authData = Auth::user();
            $authId = $authData->id;
            $storeId = $authData->store_id;
            $returnDate = (string) $data['returnDate'];
            $issueId = (string) $data['issueId'];
            $grnDate = null;
            $grnNo = null;
            $supplierId = null;
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
                $itemId = (int) $item['itemId'];
                $issueItemId = (int) $item['issueItemId'];
                $qty = (float) $item['qty'];
                $dd['issueItemId'] = $issueItemId;
                $dd['itemId'] = $itemId;
                $dd['uomId'] = (int) $item['uomId'];
                $dd['qty'] = $qty;
                $dd['pRate'] = (float) $item['pRate'];
                $dd['rate'] = (float) $item['rate'];
                $dd['warrantyMonth'] = (float) $item['warrantyMonth'];
                $dd['amount'] = (float) $item['amount'];
                $serials = [];

                $issueItemData = $this->issueItemRepo->getById($issueItemId);
                $prevReturnQty = $this->issueReturnItemsRepo->getTotalReturnQtyByIssueItemId($issueItemId);
                $issueQty = (float) $issueItemData->qty;
                $totalReturnQty = $qty+$prevReturnQty;
                $remainingQty = $issueQty-$prevReturnQty;
                if($issueItemData->qty < $totalReturnQty){
                    $itemData = $this->itemRepo->getById($itemId);
                    $itemName = $itemData ? $itemData->name : '';
                    throw new ExceptionHandler("Item ($itemName) insufficient qty. $remainingQty qty remaining.",ResponseStatus::CODE_UNPROCESSABLE_ENTITY);
                }

                foreach($item['serials'] as $serial)
                {
                    $serialData  = $this->itemSerialRepo->getByItemSerialNo($serial['name'],$itemId);

                    if(!$serialData){
                        throw new ExceptionHandler('Serial not found ('.$serial['name'].')',ResponseStatus::CODE_UNPROCESSABLE_ENTITY);
                    }
                    elseif($serialData->is_stock){
                        throw new ExceptionHandler('Serial not sold before ('.$serial['name'].')',ResponseStatus::CODE_UNPROCESSABLE_ENTITY);
                    }

                    $serialData->store_id = $storeId;
                    $serialData->is_stock = true;
                    $serialData->updated_by =  $authId;
                    $serialData->updated_at =  $createdAt;
                    $serialData->save();

                    $serials[] = [
                        'id' => (int) $serialData->id,
                        'name' => (string) $serial['name'],
                    ];
                }
                $dd['serials'] = $serials;
                $items[] = $dd;
            }

            $itemList = $transResp = $this->inventoryIssueReturnService->createIssueReturn(
                issueId:  $issueId,
                returnDate:$returnDate,
                storeId:$storeId,
                total:$total,
                paid:$paid,
                due:$due,
                remark:$remark,
                authId:$authId,
                items:$items
            );

            $issueReturnId = $transResp['issueReturnId'];
            $newItemList = $transResp['itemList'];
            $stockData = $this->inventoryService->createReceive(
                issueReturnId: $issueReturnId,
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
                items:$newItemList
            );

            $resultData = $this->issueReturnRepo->getById($issueReturnId);
            DB::commit();
            return new IssueReturnResource($resultData);
        }
        catch(QueryException|Exception $e){
            DB::rollback();
            throw new ExceptionHandler(ResponseStatus::SERVER_ERROR,ResponseStatus::CODE_INTERNAL_SERVER_ERROR,$e);
        }
    }

    protected function _generateIssueReturnInvoice($authId) : string
    {
        $issueReturnData = $this->issueReturnRepo->getLastIssueReturn();
        if(!$issueReturnData){
            $newItemNumber = 1;
        }else{
            $newItemNumber = $issueReturnData->id + 1;
        }

        return 'SR-' . str_pad($newItemNumber, 4, '0', STR_PAD_LEFT);
    }


    /**
     * @throws ExceptionHandler
     */
    public function getIssueReturnById(int $id): ?IssueReturnResource
    {
        try {
            $findData = $this->issueReturnRepo->getById($id);
            return new IssueReturnResource($findData);
        }
        catch(QueryException $e){
            throw new ExceptionHandler(ResponseStatus::SERVER_ERROR,ResponseStatus::CODE_INTERNAL_SERVER_ERROR,$e);
        }
    }

}
