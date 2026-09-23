<?php

namespace App\Services\Transactions;

use App\Exceptions\ExceptionHandler;
use App\Models\StockItem;
use App\Repositories\ReceiveItemRepository;
use App\Repositories\ReceiveRepository;
use App\Repositories\StockItemRepository;
use App\Repositories\StockRepository;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class InventoryTranReceiveService
{

    protected ReceiveRepository $receiveRepo;
    protected ReceiveItemRepository $receiveItemsRepo;
    protected StockRepository $stockRepo;
    protected StockItemRepository $stockItemRepo;
    protected TransactionStockSevice $stockService;

    public function __construct
    (
        ReceiveRepository $receiveRepo,
        ReceiveItemRepository $receiveItemsRepo,
        StockRepository $stockRepo,
        StockItemRepository $stockItemsRepo,
        TransactionStockSevice $stockService
    )
    {
        $this->receiveRepo = $receiveRepo;
        $this->receiveItemsRepo = $receiveItemsRepo;
        $this->stockRepo = $stockRepo;
        $this->stockItemRepo = $stockItemsRepo;
        $this->stockService = $stockService;
    }

    public function checkItemStockStatus($itemId,$storeId){
       return $this->stockItemRepo->checkItemStockStatusByStore($itemId,$storeId);
    }

    protected function _generateReceiveTransNo($authId) : string
    {
        $receiveData = $this->receiveRepo->getLastReceive();
        if(!$receiveData){
            $newItemNumber = 1;
        }else{
            $newItemNumber = $receiveData->id + 1;
        }

        return Date('ymd').'-CITY-' . $newItemNumber;
    }

    private function _validateItems(array $items): void
    {

        $rules = [
            'items' => 'required|array|min:1',
            'items.*.itemId' => ['required','integer',],
            'items.*.qty' => 'required|numeric|min:1',
            'items.*.uomId' => 'required|integer',
            'items.*.pRate' => 'required|numeric|min:0',
            'items.*.rate' => 'required|numeric|min:0',
            'items.*.warrantyMonth' => 'required|numeric|min:0',
            'items.*.amount' => 'required|numeric|min:0',
            'items.*.serials' => 'nullable|array',
            'items.*.serials.*.id' => 'required|integer',
            'items.*.serials.*.name' => 'required|string|max:255',
        ];

        $validator = Validator::make(['items' => $items], $rules);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }
    }



    /**
     * @throws ExceptionHandler
     */
    public function createReceive
    (
        int $transTypeId,
        int $storeId,
        float $subTotal,
        float $total,
        float $paid,
        float $due,
        int $authId,
        array $items,
        ?int $issueReturnId = null,
        ?string $grnDate = null,
        ?string $grnNo  = null,
        ?int $supplierId = null,
        ?string $remark = null
    ): array
    {
        $this->_validateItems($items);
        $transDate = now();
        $transNo = $this->_generateReceiveTransNo($authId);

        $receiveMaster = $this->receiveRepo->create(
            [
                'issue_return_id' => $issueReturnId,
                'trans_no' => $transNo,
                'trans_date' => $transDate,
                'grn_date' => $grnDate,
                'grn_no' => $grnNo,
                'trans_type_id' => $transTypeId,
                'store_id' => $storeId,
                'supplier_id' => $supplierId,
                'sub_total_amount' => $subTotal,
                'total_amount' => $total,
                'paid_amount' => $paid,
                'due_amount' => $due,
                'remark' => $remark,
                'created_at' => $transDate,
                'updated_at' => null,
                'created_by' => $authId,
                'updated_by' => null,
            ]
        );

        $receiveMasterId = $receiveMaster->id;

        $stockMaster = $this->stockService->createReceiveStock(
            receiveMasterId: $receiveMasterId,
            transTypeId: $transTypeId,
            transDate: $transDate,
            supplierId: $supplierId,
            total: $total,
            authId: $authId,
        );

        $stockMasterId = $stockMaster->id;

        $this->_createReceiveChild(
            receiveMasterId: $receiveMasterId,
            stockMasterId: $stockMasterId,
            storeId: $storeId,
            items: $items,
            transDate: $transDate,
            transTypeId:$transTypeId,
            authId: $authId,
        );

        return [
            'receiveId' => $receiveMasterId,
            'stockMasterId' => $stockMasterId,
        ];
    }

    /**
     * @throws ExceptionHandler
     */
    protected function _createReceiveChild(
        int $receiveMasterId,
        int $stockMasterId,
        int $storeId,
        array $items,
        string $transDate,
        int $transTypeId,
        int $authId
    )
    {
        foreach ($items as $item) {
            $issueReturnItemId = $item['issueReturnItemId'] ??  null;
            $itemId = $item['itemId'];
            $pRate = $item['pRate'];
            $tranRate = $item['rate'];
            $tranQty = $item['qty'];
            $tranAmount = $item['amount'];
            $warrantyMonth = $item['warrantyMonth'];
            $serials = $item['serials'];


            $receiveChild = $this->receiveItemsRepo->create(
                [
                    'issue_return_item_id' => $issueReturnItemId,
                    'receive_id' => $receiveMasterId,
                    'item_id' => $itemId,
                    'p_rate' => $pRate,
                    'rate' => $tranRate,
                    'qty' => $tranQty,
                    'amount' => $tranAmount,
                    'warranty_month' =>$warrantyMonth,
                    'created_by' => $authId,
                    'created_at' => $transDate,
                ]
            );
            $receiveChildId = $receiveChild->id;

            $this->stockService->createReceiveStockItems(
                stockMasterId:$stockMasterId,
                receiveChildId:$receiveChildId,
                storeId:$storeId,
                itemId:$itemId,
                transDate:$transDate,
                transTypeId:$transTypeId,
                pRate:$pRate,
                tranQty:$tranQty,
                tranRate:$tranRate,
                tranAmount:$tranAmount,
                authId:$authId,
                serials: $serials
            );
        }

    }

}
