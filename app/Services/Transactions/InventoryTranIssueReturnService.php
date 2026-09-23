<?php

namespace App\Services\Transactions;

use App\Enums\ResponseStatus;
use App\Exceptions\ExceptionHandler;
use App\Models\StockItem;
use App\Repositories\IssueItemRepository;
use App\Repositories\IssueRepository;
use App\Repositories\IssueReturnItemRepository;
use App\Repositories\IssueReturnRepository;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class InventoryTranIssueReturnService
{

    protected IssueReturnRepository $issueReturnRepo;
    protected IssueReturnItemRepository $issueReturnItemsRepo;
    protected IssueRepository $issueRepo;
    protected IssueItemRepository $issueItemRepo;

    public function __construct
    (
        IssueReturnRepository $issueReturnRepo,
        IssueReturnItemRepository $issueReturnItemsRepo,
        IssueRepository $issueRepo,
        IssueItemRepository $issueItemRepo,
    )
    {
        $this->issueReturnRepo = $issueReturnRepo;
        $this->issueReturnItemsRepo = $issueReturnItemsRepo;
        $this->issueRepo = $issueRepo;
        $this->issueItemRepo = $issueItemRepo;
    }

    protected function _generateIssueReturnTransNo($authId) : string
    {
        $issueReturnData = $this->issueReturnRepo->getLastIssueReturn();
        if(!$issueReturnData){
            $newItemNumber = 1;
        }else{
            $newItemNumber = $issueReturnData->id + 1;
        }

        return 'SR-'.Date('ymd').'-CITY-' . $newItemNumber;
    }

    private function _validateItems(array $items): void
    {

        $rules = [
            'items' => 'required|array|min:1',
            'items.*.itemId' => ['required','integer',],
            'items.*.issueItemId' => ['required','integer',],
            'items.*.qty' => 'required|numeric|min:1',
            'items.*.uomId' => 'required|integer',
            'items.*.pRate' => 'required|numeric|min:0',
            'items.*.rate' => 'required|numeric|min:0',
            'items.*.warrantyMonth' => 'nullable|numeric|min:0',
            'items.*.amount' => 'required|numeric|min:0',
        ];

        $validator = Validator::make(['items' => $items], $rules);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }
    }



    /**
     * @throws ExceptionHandler
     */
    public function createIssueReturn
    (
        int $issueId,
        string | null $returnDate,
        int $storeId,
        float $total,
        float $paid,
        float $due,
        int $authId,
        string | null $remark,
        array $items,
    ): array
    {
        $this->_validateItems($items);
        $transDate = now();
        $transNo = $this->_generateIssueReturnTransNo($authId);


        $issueReturnMaster = $this->issueReturnRepo->create(
            [
                'issue_id' => $issueId,
                'trans_date' => $transDate,
                'return_no' => $transNo,
                'return_date' => $returnDate,
                'store_id' => $storeId,
                'total_return_amount' => $total,
                'return_paid_amount' => $paid,
                'return_due_amount' => $due,
                'reason' => $remark,
                'created_at' => $transDate,
                'updated_at' => null,
                'created_by' => $authId,
                'updated_by' => null,
            ]
        );

        $issueReturnId = $issueReturnMaster->id;


        $itemList = $this->_createIssueReturnChild(
            issueReturnId: $issueReturnId,
            items: $items,
            transDate: $transDate,
            authId: $authId,
        );
        return [
            'issueReturnId' => $issueReturnId,
            'itemList' => $itemList,
        ];
    }

    /**
     * @throws ExceptionHandler
     */
    protected function _createIssueReturnChild(
        int $issueReturnId,
        array $items,
        string $transDate,
        int $authId
    ) : array
    {

        $newItemList = [];
        foreach ($items as $i=>$item) {
            $issueItemId = $item['issueItemId'];
            $itemId = $item['itemId'];
            $pRate = $item['pRate'];
            $tranRate = $item['rate'];
            $tranQty = $item['qty'];
            $tranAmount = $item['amount'];
            $warrantyMonth = $item['warrantyMonth'];

            $respData = $this->issueReturnItemsRepo->create(
                [
                    'issue_return_id' => $issueReturnId,
                    'issue_item_id' => $issueItemId,
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

            $item['issueReturnItemId'] = $respData->id;

            $newItemList[] = $item;
        }
        return $newItemList;
    }

}
