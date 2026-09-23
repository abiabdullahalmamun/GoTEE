<?php

namespace App\Services;

use App\Enums\ResponseStatus;
use App\Exceptions\ExceptionHandler;
use App\Http\Resources\Orders\OrderListResource;
use App\Http\Resources\Orders\ReceiveResource;
use App\Models\ItemVariant;
use App\Models\UserType;
use App\Repositories\OrderItemsRepository;
use App\Repositories\OrderRepository;
use App\Repositories\ShiftRepository;
use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class OrderService
{
    protected OrderRepository $orderRepo;
    protected OrderItemsRepository $orderItemsRepo;

    public function __construct(OrderRepository $orderRepo,OrderItemsRepository $orderItemsRepo)
    {
        $this->orderRepo = $orderRepo;
        $this->orderItemsRepo = $orderItemsRepo;
    }

    /**
     * @throws ExceptionHandler
     */
    public function getAllOrders() : AnonymousResourceCollection
    {
        try {
            $authId = Auth::id();
//            $userType = UserType::find($typeId);
//            $userCode = $userType ? $userType->code : null;
//            if($userCode == '103'){
//                $filedName = 'supplier_id';
//            }
//            elseif($userCode == '104'){
//                $filedName = 'customer_id';
//            }else{
//                throw new ExceptionHandler('Invalid user type',ResponseStatus::CODE_UNPROCESSABLE_ENTITY);
//            }
//            dd($filedName);
            $resp =  $this->orderRepo->getAll($authId,null);
            return OrderListResource::collection($resp);

        }
        catch(QueryException $e){
            throw new ExceptionHandler(ResponseStatus::SERVER_ERROR,ResponseStatus::CODE_INTERNAL_SERVER_ERROR,$e);
        }
    }

    /**
     * @throws ExceptionHandler
     */
    public function createOrder(array $data) : ?ReceiveResource
    {
        DB::beginTransaction();
        try {
            $authId = Auth::id();
            $createdAt = now();
             $orderData = [
                'order_date' => $createdAt,
                'invoice_no' => $this->generateOrderInvoice($authId),
                'customer_id' => $authId,
                'supplier_id' => $data['supplierId'],
                'sub_total' =>(float) $data['subTotal'],
                'total_discount' =>(float) $data['discountAmount'],
                'total_payable' =>(float) $data['totalPayable'],
                'trans_resp' =>$data['payResp'],
                'order_status_code'=>101,
                'created_at' => $createdAt,
                'updated_at' => $createdAt,
                'created_by' => $authId,
                'updated_by' => $authId,
            ];

            $OrderMaster = $this->orderRepo->create($orderData);

            $orderItems = $this->getOrderItemsFormate($data['items'],$OrderMaster->id,$authId,$createdAt);

            $this->orderItemsRepo->create($orderItems);

            DB::commit();
            return new ReceiveResource($OrderMaster);
        }
        catch(QueryException|Exception $e){
            DB::rollback();
            throw new ExceptionHandler(ResponseStatus::SERVER_ERROR,ResponseStatus::CODE_INTERNAL_SERVER_ERROR,$e);
        }
    }

    protected function generateOrderInvoice($authId) : string
    {
        $orderData = $this->orderRepo->getLastOrder();
        if(!$orderData){
            $newItemNumber = 1;
        }else{
            $newItemNumber = $orderData->id + 1;
        }

        return 'ORD-' . str_pad($newItemNumber, 4, '0', STR_PAD_LEFT);
    }

    protected function getOrderItemsFormate($items,$OrderMasterId,$authId,$createdAt) : array
    {
        return array_map(function($item) use ($OrderMasterId,$authId,$createdAt) {
            $itemVariant = ItemVariant::find($item['vId']);
            $vName = $itemVariant ? $itemVariant->name : null;
            return [
                'order_id' => $OrderMasterId,
                'item_id' => $item['itemId'],
                'v_id' => $item['vId'],
                'v_name' => $vName,
                'v_qty' => $item['vQty'],
                'v_price' => $item['vPrice'],
                'qty' =>(float) $item['pQty'],
                'total_amount' =>(float) $item['pAmount'],
                'created_by' => $authId,
                'created_at' => $createdAt,
            ];
        }, $items);
    }

    /**
     * @throws ExceptionHandler
     */
    public function getOrderById(int $id): ?ReceiveResource
    {
        try {
            $findData = $this->orderRepo->getById($id);
            return new ReceiveResource($findData);
        }
        catch(QueryException $e){
            throw new ExceptionHandler(ResponseStatus::SERVER_ERROR,ResponseStatus::CODE_INTERNAL_SERVER_ERROR,$e);
        }
    }

    /**
     * @throws ExceptionHandler
     */
    public function updateOrderStatus($id,$orderStatusCode) : ?ReceiveResource
    {
        try {
            $order = $this->orderRepo->getById($id);
            $order->order_status_code = $orderStatusCode;
            $order->save();

            return new ReceiveResource($order);
        }
        catch(QueryException $e){
            throw new ExceptionHandler(ResponseStatus::SERVER_ERROR,ResponseStatus::CODE_INTERNAL_SERVER_ERROR,$e);
        }
    }

    /**
     * @throws ExceptionHandler
     */
//    public function deleteShift(int $id): OrderResource
//    {
//        try {
//            $findData = $this->shiftRepo->getById($id);
//            $findData->is_active = 0;
//            $findData->updated_by = Auth::id();
//            $findData->updated_at = now();
//            $status = $findData->update();
//            if(!$status){
//                throw new ExceptionHandler(ResponseStatus::SERVER_ERROR,ResponseStatus::CODE_INTERNAL_SERVER_ERROR);
//            }
//            return new OrderResource($findData);
//        }
//        catch(QueryException $e){
//            throw new ExceptionHandler(ResponseStatus::SERVER_ERROR,ResponseStatus::CODE_INTERNAL_SERVER_ERROR,$e);
//        }
//    }
}
