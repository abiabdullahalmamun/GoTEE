<?php

namespace App\Services;

use App\Enums\ResponseStatus;
use App\Exceptions\ExceptionHandler;
use App\Http\Resources\Issues\IssueWIthReturnDetailResource;
use App\Http\Resources\Issues\IssueListResource;
use App\Http\Resources\Issues\IssueResource;
use App\Repositories\ItemSerialRepository;
use App\Repositories\IssueItemRepository;
use App\Repositories\IssueRepository;
use App\Services\Transactions\InventoryTranIssueService;
use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class IssueService
{
    protected IssueRepository $issueRepo;
    protected IssueItemRepository $issueItemsRepo;
    protected InventoryTranIssueService $inventoryService;
    protected ItemSerialRepository $itemSerialRepo;

    public function __construct
    (
        IssueRepository $issueRepo,
        IssueItemRepository $issueItemsRepo,
        InventoryTranIssueService $inventoryService,
        ItemSerialRepository $itemSerialRepo
    )
    {
        $this->issueRepo = $issueRepo;
        $this->issueItemsRepo = $issueItemsRepo;
        $this->inventoryService = $inventoryService;
        $this->itemSerialRepo = $itemSerialRepo;
    }

    /**
     * @throws ExceptionHandler
     */
    public function getAllIssues() : AnonymousResourceCollection
    {
        try {
            $storeId = Auth::user()->store_id;
            $transTypeId = 3;
            $resp =  $this->issueRepo->getAllIssue($storeId,$transTypeId);
            return IssueResource::collection($resp);
        }
        catch(QueryException $e){
            throw new ExceptionHandler(ResponseStatus::SERVER_ERROR,ResponseStatus::CODE_INTERNAL_SERVER_ERROR,$e);
        }
    }

    /**
     * @throws ExceptionHandler
     */
    public function createIssue(array $data) : ?IssueResource
    {
        DB::beginTransaction();
        try {
            $authData = Auth::user();
            $authId = $authData->id;
            $storeId = $authData->store_id;
            $subTotal = (float) $data['subTotal'];
            $totalDiscount = (float) $data['totalDiscount'] ?? 0;
            $vatPercent = (float) $data['vatPercent'] ?? 0;
            $vatAmount = (float) $data['vatAmount'] ?? 0;
            $totalIssueAmount = (float) $data['totalAmount'];
            $totalPayable = (float) $data['totalPayable'];
            $paid = (float) $data['paidAmount'];
            $due = ($totalPayable-$paid);
            $transTypeId = 3;
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
                $dd['discount'] = (float) $item['discount'];
                $dd['total'] = (float) $item['total'];

                $serialsCheck = collect($item['serials']);
                $duplicateIds = $serialsCheck->duplicates('name');
                if($duplicateIds->isNotEmpty()) {
                    throw new ExceptionHandler('Duplicate Serial found',ResponseStatus::CODE_UNPROCESSABLE_ENTITY);
                }

                $serials = [];
                foreach($item['serials'] as $serial)
                {
                    $isSerialExist  = $this->itemSerialRepo->getByItemSerialNo($serial['name'],$dd['itemId']);
                    if(!$isSerialExist){
                        throw new ExceptionHandler('Serial not found - ('.$serial['name'].')',ResponseStatus::CODE_UNPROCESSABLE_ENTITY);
                    }
                    else if(!$isSerialExist->is_stock){
                        throw new ExceptionHandler('Already sold serial - ('.$serial['name'].')',ResponseStatus::CODE_UNPROCESSABLE_ENTITY);
                    }

                    $isSerialExist->is_stock = 0;
                    $isSerialExist->save();

                    $serials[] = [
                        'id' => (int) $isSerialExist->id,
                        'name' => (string) $isSerialExist->value,
                    ];
                }
                $dd['serials'] = $serials;
                $items[] = $dd;
            }

            $transResp = $this->inventoryService->createIssue(
                challanDate:$data['challanDate'],
                challanNo:$data['challanNo'],
                transTypeId:$transTypeId,
                storeId:$storeId,
                customerId:$data['customerId'],
                subTotal:$subTotal,
                totalDiscount:$totalDiscount,
                vatPercent:$vatPercent,
                vatAmount:$vatAmount,
                totalAmount:$totalIssueAmount,
                totalPayable:$totalPayable,
                paid:$paid,
                due:$due,
                remark:$data['remark'],
                authId:$authId,
                items:$items
            );

            $issueData = $this->issueRepo->getById($transResp['issueId']);
            DB::commit();
            return new IssueResource($issueData);
        }
        catch(QueryException|Exception $e){
            DB::rollback();
            throw new ExceptionHandler(ResponseStatus::SERVER_ERROR,ResponseStatus::CODE_INTERNAL_SERVER_ERROR,$e);
        }
    }

    protected function _generateIssueInvoice($authId) : string
    {
        $issueData = $this->issueRepo->getLastIssue();
        if(!$issueData){
            $newItemNumber = 1;
        }else{
            $newItemNumber = $issueData->id + 1;
        }

        return 'CH-' . str_pad($newItemNumber, 4, '0', STR_PAD_LEFT);
    }


    /**
     * @throws ExceptionHandler
     */
    public function getIssueById(int $id): ?IssueResource
    {
        try {
            $findData = $this->issueRepo->getById($id);
            return new IssueResource($findData);
        }
        catch(QueryException $e){
            throw new ExceptionHandler(ResponseStatus::SERVER_ERROR,ResponseStatus::CODE_INTERNAL_SERVER_ERROR,$e);
        }
    }


    /**
     * @throws ExceptionHandler
     */
    public function getReturnableIssueByInvoice(string $issueInvoice): ?IssueWIthReturnDetailResource
    {
        try {
            $transTypeId = 3;
            $findData = $this->issueRepo->getReturnableByInvoiceNo($issueInvoice,$transTypeId);
            return new IssueWIthReturnDetailResource($findData);
        }
        catch(QueryException $e){
            throw new ExceptionHandler(ResponseStatus::SERVER_ERROR,ResponseStatus::CODE_INTERNAL_SERVER_ERROR,$e);
        }
    }

}
