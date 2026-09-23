<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\BillItemResource;
use App\Http\Resources\BillResource;
use App\Models\BillHead;
use App\Models\BillMaster;
use App\Models\BillChild;
use App\Models\PaymentRequest;
use App\Models\Room;
use App\Models\RoomBook;
use App\Models\User;

use App\Models\Counter;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class OldDataController extends Controller
{
    public function index(Request $request)
    {
        $userId = Auth::id();
        $sortField = $request->sort_field ?? 'bill_date';
        $sortDirection = $request->sort_direction ?? 'asc';
        if($sortField){
            $query = BillMaster::query()
                ->with(['user', 'booking'])
                ->whereNull('merge_master_id')
                ->orderBy($sortField, $sortDirection);
        }else{
            $query = BillMaster::query()
                ->with(['user', 'booking'])
                ->whereNull('merge_master_id')
                ->orderBy('bill_date', 'desc');
        }


        // Apply search filter
        if ($request->has('search')) {
            $query->where('invoice_no', 'like', '%' . $request->search . '%');
        }
        $query->where('user_id', $userId);
        // Apply status filter
        if ($request->has('status') && $request->status !== 'all') {
            $paidStatus = (strtolower($request->status) == 'paid') ? 1 : 0;

            $query->where('paid_status', $paidStatus);
        }

        // Get paginated results
        $perPage = $request->per_page ?? 10;
        $bills = $query->paginate($perPage);

        return BillResource::collection($bills);
    }

    public function store(Request $request)
    {
        dd($request) ; 


    }
    public function getkey(Request $request)
    {
        // dd($request) ; 
        $mac = $request->input('payload.mac');
        $ip = $request->input('payload.ip');
        $host = $request->input('payload.host');
        $status = $request->input('payload.status');
        // dd($mac) ; 
        $randomString = bin2hex(random_bytes(16));
        // $encryptedKey = encrypt($randomString); 
         $encryptedKey = substr(hash('sha256', $randomString), 0, 64);
        $val = Counter::where('mac',$mac)->first() ; 
        // dd($val) ; 
        $center =  $val->center_id ; 
        $counterNo =  $val->counter_id ; 
        // dd($center) ; 
       $is_update =  Counter::where('mac',$mac)->update([
            'token'   => $encryptedKey ,
            'ip'        =>  $ip, 
             'host'        =>   $host, 
            'updated_at'=>Date('Y-m-d H:i:s')
        ]);

       // dd($is_update) ; 
              // dd($encryptedKey); 
       return [
            'center' => $center,
            'counterNo' => $counterNo,
            'key' =>$encryptedKey,
        ];

    }

    public function items(BillMaster $bill)
    {
        $items = BillChild::with('billHead')
            ->where('bill_master_id', $bill->id)
            ->get();

        return BillItemResource::collection($items);
    }

    public function getUserInfoByIdno(Request $request){
            $idNo = $request->idNo;

            $userData = User::where('user_id', $idNo)->first();
            if($userData){
                return [
                    'status' => true,
                    'id' => $userData->id,
                    'idNo' => $userData->user_id,
                    'name' => $userData->name,
                    'email' => $userData->email,
                    'phone' => $userData->phone,
                ];
            }else{
                return [
                    'status' => false,
                    'id' => null,
                    'idNo' => null,
                    'name' => null,
                    'email' => null,
                    'phone' => null,
                ];
            }
    }



    // UserController.php
    public function getUserByBaNo($user_ba_no)
    {
        $user = User::where('user_id', $user_ba_no)->first();

        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        return response()->json($user);
    }


    function generateUniqueTransactionNo(): string
    {
        do {
            $code = 'TXN-' . strtoupper(Str::random(8));
        } while (BillMaster::where('paid_txn_no', $code)->exists());

        return $code;
    }



    function generateUniquePaymentRequestTransactionNo(): string
    {
        do {
            $code = strtoupper(Str::random(15));
        } while (PaymentRequest::where('txn_no', $code)->exists());

        return $code;
    }




    //************** Counter payment **********//
    // BillController.php
    public function getUnpaidBills($user_id)
    {
        $bills = BillMaster::where('user_id', $user_id)
            ->where('paid_status', 0)
            ->whereNull('merge_master_id')
            ->get();

        return response()->json($bills);
    }

    public function getBillItems($bill_id)
    {
        $items = BillChild::
        where('bill_master_id', $bill_id)
        ->get();

        return response()->json($items);
    }

    // BillHeadController.php
    public function getBillHeads()
    {
        $heads = BillHead::all();
        return response()->json($heads);
    }

    // PaymentController.php
    public function counterPaymentProcessPayment(Request $request)
    {
        date_default_timezone_set("Asia/Dhaka");
        $request->validate([
            'bill_ids' => 'required|array',
            'total_amount' => 'required|numeric',
            'user_id' => 'required|exists:users,id'
        ]);

        DB::beginTransaction();

        try {
            // Mark bills as paid
                $txnNo = $this->generateUniqueTransactionNo();
                BillMaster::whereIn('id', $request->bill_ids)
                ->update([
                    'paid_status' => 1,
                    'paid_txn_no' => $txnNo,
                    'paid_at' => now(),
                    'pay_type' => 1 // Assuming 1 is for cash
                ]);

            // Here you would typically integrate with a payment gateway
            // For now, we'll just return a success response

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Payment processed successfully',
                'id' => $txnNo
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'payment failed: ' . $e->getMessage(),
                'id'=>null
            ], 500);
        }
    }

    public function paymentRequest(Request $request){
        try {
            date_default_timezone_set("Asia/Dhaka");
            $data = $request->all();
            $txnNo = $this->generateUniquePaymentRequestTransactionNo();
            $id = DB::transaction(function () use ($request,$data,$txnNo) {
                $insertData =  PaymentRequest::create([
                    'data' => json_encode($data),
                    'txn_no' => $txnNo,
                    'created_at' => now(),
                    'status' => 0,
                    'created_by' => $request->user_id,
                ]);
                return $insertData->txn_no;
            });

            return response()->json([
                'success' => true,
                'id' => $id,
                'message' => 'Payment request created successfully.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'id' => null,
                'message' => 'Payment request failed.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function billPaymentRequestProcess(Request $request){

        date_default_timezone_set("Asia/Dhaka");
       $data =  PaymentRequest::where('txn_no', $request->id)->latest()->first();
       if($data){

           $requestData =  json_decode($data->data);


           return response()->json([
               'success' => true,
               'message' => 'Payment request processed successfully.'
           ]);
       }else{
           return response()->json(
               [
                   'success' => false,
                   'message' => 'Payment request not found.'
               ]
           );
       }
    }

    public function roomsByType(Request $request){

        $roomTypeId = $request->room_type_id;
        $id = $request->id;
        $bookingData = RoomBook::with('user')->where('id', $id)->first();
        if (!$bookingData) {
            return [];
        }

        $checkinDate  = Carbon::parse($bookingData->checkin_at)->format('Y-m-d');
        $checkoutDate = Carbon::parse($bookingData->checkout_at)->format('Y-m-d');

        $availableRooms = Room::with('roomType')
            ->where('room_type_id', $roomTypeId)
            ->whereDoesntHave('bookings', function ($query) use ($checkinDate, $checkoutDate, $roomTypeId) {
                $query->where('is_released', 0)
                    ->where('room_type_id', $roomTypeId)
                    ->where(function ($q) use ($checkinDate, $checkoutDate) {
                        $q->where('checkin_at', '<', $checkoutDate)
                            ->where('checkout_at', '>', $checkinDate);
                    });
            })
            ->get();

        return response()->json(['rooms' => $availableRooms]);
    }


}
