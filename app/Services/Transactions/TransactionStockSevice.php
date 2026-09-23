<?php

namespace App\Services\Transactions;

use App\Enums\ResponseStatus;
use App\Exceptions\ExceptionHandler;
use App\Models\Stock;
use App\Models\StockItem;
use App\Models\StockItemSerial;
use App\Repositories\StockItemRepository;
use App\Repositories\StockItemSerialRepository;
use App\Repositories\StockRepository;

class TransactionStockSevice
{
    protected StockRepository $stockRepo;
    protected StockItemRepository $stockItemRepo;
    protected StockItemSerialRepository $stockItemSerialRepo;

    public function __construct
    (
        StockRepository $stockRepo,
        StockItemRepository $stockItemRepo,
        StockItemSerialRepository $stockItemSerialRepo
    )
    {
        $this->stockRepo = $stockRepo;
        $this->stockItemRepo = $stockItemRepo;
        $this->stockItemSerialRepo = $stockItemSerialRepo;
    }


    /**
     * @throws ExceptionHandler
     */
    public function _generateStockNo()  : string
    {
        $stockData = $this->stockRepo->getLastStock();
        if(!$stockData){
            $newItemNumber = 1;
        }else{
            $newItemNumber = $stockData->id + 1;
        }

        return 'STK-' . str_pad($newItemNumber, 4, '0', STR_PAD_LEFT);
    }


    /****************** START (Receive Stock Master) **************/
    #region
    /**
     * @throws ExceptionHandler
     */
    public function createReceiveStock(
        int $receiveMasterId,
        string $transDate,
        int $transTypeId,
        int | null $supplierId,
        int $total,
        int $authId,
    )  : Stock
    {
        return $this->stockRepo->create([
            'receive_id'=>$receiveMasterId,
            'stock_date' => $transDate,
            'trans_type_id' => $transTypeId,
            'stock_no'=> $this->_generateStockNo(),
            'supplier_id'=> $supplierId,
            'total_amount'=> $total,
            'created_by'=> $authId,
            'created_at'=> $transDate,
        ]);
    }

    #endregion
    /****************** END **************/


    /****************** START (Receive Stock Child) **************/
    #region
    /**
     * @throws ExceptionHandler
     */
    public function createReceiveStockItems(
        int $stockMasterId,
        int $receiveChildId,
        int $storeId,
        int $itemId,
        string $transDate,
        int $transTypeId,
        float $pRate,
        float $tranQty,
        float $tranRate,
        float $tranAmount,
        int $authId,
        array $serials
    )  : StockItem
    {
        $itemStockData = StockItem::getLastStock($itemId,$storeId);
        $opQty = 0;
        $opRate = 0;
        $opAmount = 0;

        if ($itemStockData) {
            $opQty = $itemStockData->cls_qty ?? 0;
            $opRate = $itemStockData->cls_rate ?? 0;
            $opAmount = $itemStockData->cls_amount ?? 0;
        }

        $clsQty = $opQty + $tranQty;
        $clsAmount = $opAmount + ($tranQty * $tranRate);
        $clsRate = $clsQty > 0 ? $clsAmount / $clsQty : 0;

//        Formula for Credit Note (Sales Return):
//        $clsQty = $opQty + $tranQty; // Increase stock
//        $clsAmount = $opAmount + ($tranQty * $opRate); // Increase total amount using old rate
//        $clsRate = $clsQty > 0 ? $clsAmount / $clsQty : 0; // Recalculate rate

//        Formula for Transfer In:
//        $clsQty = $opQty + $tranQty; // Increase stock in the receiving store
//        $clsAmount = $opAmount + ($tranQty * $tranRate); // Add transferred stock cost
//        $clsRate = $clsQty > 0 ? $clsAmount / $clsQty : 0; // Recalculate closing rate




        $stockItems = [
            'stock_id' => $stockMasterId,
            'receive_item_id' => $receiveChildId,
            'store_id' => $storeId,
            'item_id' => $itemId,
            'p_rate' => $pRate,
            'trans_rate' => $tranRate,
            'trans_qty' => $tranQty,
            'trans_amount' => $tranAmount,
            'op_qty' => $opQty,
            'op_rate' => $opRate,
            'op_amount' => $opAmount,
            'cls_qty' => $clsQty,
            'cls_rate' => $clsRate,
            'cls_amount' => $clsAmount,
            'created_by' => $authId,
            'created_at' => $transDate,
        ];

        $serialData = array_map(function ($serial) use ($transTypeId, $itemId, $receiveChildId) {
            return [
                'item_serial_id' => (int) $serial['id'],
                'receive_item_id' => $receiveChildId,
                'trans_type_id' => $transTypeId,
                'item_id' => $itemId,
            ];
        }, $serials);

        $this->stockItemSerialRepo->create($serialData);

        return $this->stockItemRepo->create($stockItems);
    }

    #endregion
    /****************** END **************/




    /****************** START (Issue Stock Master) **************/
    #region
    /**
     * @throws ExceptionHandler
     */
    public function createIssueStock(
        int $issueMasterId,
        string $transDate,
        int $transTypeId,
        int | null $customerId,
        int $total,
        int $authId,
    )  : Stock
    {
        return $this->stockRepo->create([
            'issue_id'=>$issueMasterId,
            'stock_date' => $transDate,
            'trans_type_id' => $transTypeId,
            'stock_no'=> $this->_generateStockNo(),
            'customer_id'=> $customerId,
            'total_amount'=> $total,
            'created_by'=> $authId,
            'created_at'=> $transDate,
        ]);
    }

    #endregion
    /****************** END **************/


    /****************** START (ISSUE Stock Child) **************/
    #region
    /**
     * @throws ExceptionHandler
     */
    public function createIssueStockItems(
        int $stockMasterId,
        int $issueChildId,
        int $storeId,
        int $itemId,
        string $transDate,
        int $transTypeId,
        float $pRate,
        float $tranQty,
        float $tranRate,
        float $tranAmount,
        int $authId,
        array $serials
    )  : StockItem
    {
        $itemStockData = StockItem::getLastStock($itemId,$storeId);
        $opQty = 0;
        $opRate = 0;
        $opAmount = 0;
        if($itemStockData){
            $opQty = $itemStockData->cls_qty ?? 0;
            $opRate = $itemStockData->cls_rate ?? 0;
            $opAmount = $itemStockData->cls_amount ?? 0;
        }

        $clsQty = $opQty - $tranQty;
        $clsAmount = $opAmount - ($tranQty * $opRate);
        $clsRate = $clsQty > 0 ? $clsAmount / $clsQty : 0;

        if ($opQty < $tranQty) {
            throw new ExceptionHandler('Not enough stock available.',ResponseStatus::CODE_UNPROCESSABLE_ENTITY);
        }
//        Formula for Debit Note (Purchase Return):
//        $clsQty = $opQty - $tranQty; // Reduce stock
//        $clsAmount = $opAmount - ($tranQty * $opRate); // Reduce total amount using old rate
//        $clsRate = $clsQty > 0 ? $clsAmount / $clsQty : 0; // Recalculate rate

//        Formula for Transfer Out:
//        $clsQty = $opQty - $tranQty; // Reduce stock in the sending store
//        $clsAmount = $opAmount - ($tranQty * $opRate); // Reduce total amount using existing rate
//        $clsRate = $clsQty > 0 ? $clsAmount / $clsQty : 0; // Recalculate closing rate



        $stockItems = [
            'stock_id' => $stockMasterId,
            'issue_item_id' => $issueChildId,
            'store_id' => $storeId,
            'item_id' => $itemId,
            'p_rate' => $pRate,
            'trans_rate' => $tranRate,
            'trans_qty' => $tranQty,
            'trans_amount' => $tranAmount,
            'op_qty' => $opQty,
            'op_rate' => $opRate,
            'op_amount' => $opAmount,
            'cls_qty' => $clsQty,
            'cls_rate' => $clsRate,
            'cls_amount' => $clsAmount,
            'created_by' => $authId,
            'created_at' => $transDate,
        ];

        $serialData = array_map(function ($serial) use ($transTypeId, $itemId, $issueChildId) {
            return [
                'item_serial_id' => (int) $serial['id'],
                'issue_item_id' => $issueChildId,
                'trans_type_id' => $transTypeId,
                'item_id' => $itemId,
            ];
        }, $serials);

        $this->stockItemSerialRepo->create($serialData);

        return $this->stockItemRepo->create($stockItems);
    }

    #endregion
    /****************** END **************/




}
