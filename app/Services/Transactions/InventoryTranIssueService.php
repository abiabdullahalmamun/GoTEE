<?php

namespace App\Services\Transactions;

use App\Exceptions\ExceptionHandler;
use App\Models\StockItem;
use App\Repositories\IssueItemRepository;
use App\Repositories\IssueRepository;
use App\Repositories\StockItemRepository;
use App\Repositories\StockRepository;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class InventoryTranIssueService
{

    protected IssueRepository $issueRepo;
    protected IssueItemRepository $issueItemsRepo;
    protected StockRepository $stockRepo;
    protected StockItemRepository $stockItemRepo;
    protected TransactionStockSevice $stockService;

    public function __construct
    (
        IssueRepository $issueRepo,
        IssueItemRepository $issueItemsRepo,
        StockRepository $stockRepo,
        StockItemRepository $stockItemsRepo,
        TransactionStockSevice $stockService
    )
    {
        $this->issueRepo = $issueRepo;
        $this->issueItemsRepo = $issueItemsRepo;
        $this->stockRepo = $stockRepo;
        $this->stockItemRepo = $stockItemsRepo;
        $this->stockService = $stockService;
    }

    protected function _generateIssueTransNo($authId) : string
    {
        $issueData = $this->issueRepo->getLastIssue();
        if(!$issueData){
            $newItemNumber = 1;
        }else{
            $newItemNumber = $issueData->id + 1;
        }

        return 'INV'.Date('ymd').'-CITY-' . $newItemNumber;
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
    public function createIssue
    (
        string $challanDate,
        int $transTypeId,
        int $storeId,
        float $subTotal,
        float $totalDiscount,
        float $vatPercent,
        float $vatAmount,
        float $totalAmount,
        float $totalPayable,
        float $paid,
        float $due,
        int $authId,
        array $items,
        ?string $receiveReturnId = null,
        ?int $customerId = null,
        ?string $challanNo = null,
        ?string $remark = null,
    ): array
    {
        $this->_validateItems($items);
        $transDate = now();
        $transNo = $this->_generateIssueTransNo($authId);

        $issueMaster = $this->issueRepo->create(
            [
                'receive_return_id' => $receiveReturnId,
                'trans_no' => $transNo,
                'trans_date' => $transDate,
                'challan_date' => $challanDate,
                'challan_no' => $challanNo,
                'trans_type_id' => $transTypeId,
                'store_id' => $storeId,
                'customer_id' => $customerId,
                'sub_total_amount' => $subTotal,
                'vat_percent' => $vatPercent,
                'vat_amount' => $vatAmount,
                'total_amount' => $totalAmount,
                'total_discount' => $totalDiscount,
                'total_payable' => $totalPayable,
                'paid_amount' => $paid,
                'due_amount' => $due,
                'remark' => $remark,
                'created_at' => $transDate,
                'updated_at' => null,
                'created_by' => $authId,
                'updated_by' => null,
            ]
        );

        $issueMasterId = $issueMaster->id;

        $stockMaster = $this->stockService->createIssueStock(
            issueMasterId: $issueMasterId,
            transTypeId: $transTypeId,
            transDate: $transDate,
            customerId: $customerId,
            total: $totalPayable,
            authId: $authId,
        );

        $stockMasterId = $stockMaster->id;

        $this->_createIssueChild(
            issueMasterId: $issueMasterId,
            stockMasterId: $stockMasterId,
            storeId: $storeId,
            items: $items,
            transDate: $transDate,
            transTypeId:$transTypeId,
            authId: $authId,
        );

        return [
            'issueId' => $issueMasterId,
            'stockMasterId' => $stockMasterId,
        ];
    }

    /**
     * @throws ExceptionHandler
     */
    protected function _createIssueChild(
        int $issueMasterId,
        int $stockMasterId,
        int $storeId,
        array $items,
        string $transDate,
        int $transTypeId,
        int $authId
    )
    {
        foreach ($items as $item) {
            $receiveReturnItemId = $item['receiveReturnItemId'] ??  null;
            $itemId = $item['itemId'];
            $pRate = $item['pRate'];
            $tranRate = $item['rate'];
            $tranQty = $item['qty'];
            $warrantyMonth = $item['warrantyMonth'] ?? 0;
            $tranAmount = $item['amount'];
            $discount = $item['discount'];
            $total = $item['total'];

            $serials = $item['serials'];

            $issueChild = $this->issueItemsRepo->create(
                [
                    'issue_id' => $issueMasterId,
                    'receive_return_item_id' => $receiveReturnItemId,
                    'item_id' => $itemId,
                    'p_rate' => $pRate,
                    'rate' => $tranRate,
                    'qty' => $tranQty,
                    'amount' => $tranAmount,
                    'discount' => $discount,
                    'total' => $total,
                    'warranty_month' =>$warrantyMonth,
                    'created_by' => $authId,
                    'created_at' => $transDate,
                ]
            );
            $issueChildId = $issueChild->id;
            $itemNewRate = $total/$tranQty;
            $this->stockService->createIssueStockItems(
                stockMasterId:$stockMasterId,
                issueChildId:$issueChildId,
                storeId:$storeId,
                itemId:$itemId,
                transDate:$transDate,
                transTypeId:$transTypeId,
                pRate:$pRate,
                tranQty:$tranQty,
                tranRate:$itemNewRate,
                tranAmount:$total,
                authId:$authId,
                serials: $serials
            );
        }

    }

}
