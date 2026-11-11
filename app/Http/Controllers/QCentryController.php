<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAppReceiveRequest;
// use App\Http\Requests\UpdateRegionRequest;
use App\Models\LineQC ; 
use App\Models\Employee ;
use App\Models\BundleInfo ; 
use App\Models\Buyer ; 
use App\Models\FaultList ; 
use App\Models\OrderNo; 
use App\Models\OperationList; 
use App\Models\NodeDataMapped; 
use App\Models\QCfault; 
// use App\Models\Region;
// use App\Models\Center;
// use App\Models\Counter;
// use App\Models\VisaType;
// use App\Models\VisaTypeApt;
// use App\Models\AptOverride;
// use App\Models\StickerMap;
// use App\Models\Service;
// use App\Models\CurrentQueue;
// use App\Models\QueueCode ; 
// use App\Models\SslAptList ; 
// use App\Models\NicAptList ; 
// use App\Models\RejectReason ; 
// use App\Models\RejectLog ; 
// use App\Models\AppSteps ; 
// use App\Models\AppLog ;
// use App\Models\AppReceive ; 
// use App\Models\SmsOtp ; 
// use App\Models\SmsLog ; 
use App\Jobs\SendSmsDCJob;
// use App\Imports\WebFileImport; 
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use App\Services\ActionExceptionService;



class  QCentryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $perPage = $request->input('per_page', 100);
      
        try {
            $ips = request()->getClientIps(); 
            $lastHopIp = end($ips);
            // dd($lastHopIp) ; 
            $user = auth()->user()->id ; 

            $deviceId = session('device_id'); 
            $lineID = '';
            // $deviceId = "f5f3b527ddfdd0f" ; 
            $operator= '';
            $oprID = '' ; 
            $count = 0 ;
            if ($deviceId) {
                $ll = LineQC::where('appId', $deviceId)->first();

                if ($ll) {
                    $lineID = $ll->line?->lineID;
                    $emp = Employee::find($ll->opratorId);

                    if ($emp) {
                        $oprID = $ll->opratorId ; 
                        $operator = $emp->emp_id . ' | ' . $emp->emp_name;
                        $faults = QCfault::where('Date', date('Y-m-d'))
                                ->where('qcId', $oprID)
                                ->selectRaw('
                                    SUM(CASE WHEN DefectCode = 0 THEN Qty ELSE 0 END) as count,
                                    SUM(CASE WHEN DefectCode != 0 THEN Qty ELSE 0 END) as count2
                                ')
                                ->first();

                            $pass  = $faults->count ?? 0;
                            $alt = $faults->count2 ?? 0;
                        $count  = $pass.'('.$alt.')' ; 
                        
                    }
                }
            }
          
            $faultList = FaultList::where('status', 1)
                            ->orderBy('defectName')
                            ->get([
                                'sl',
                                'defectCode',
                                'defectName',
                                'defectCatagory',
                                'defCat',
                                'defectCodeId', 
                            ]);
                        
           

            // $operator = null;
            // dd( $deviceId ) ; 
            return view('pages.QCEntry.index', [
                'deviceId' => $deviceId ,
                'lineID' => $lineID ,
                'operator' => $operator ,
                 'oprId' => $oprID  ,
                'count' => $count ,
                'FaultList' => $faultList ,
                 'smsg' => 'Please scan RFID...',
            ]);
     
        } catch (Exception $e) {
             dd($e->getMessage()); 
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    // public function getSend2hciList($centerId)
    // {
    //     // dd($centerId) ;

    //     $dataList = AppReceive::select('Webfile','id')->where('centerId',$centerId)->where('stepId',1)->orderBy('Webfile','asc')->get() ; 

    //     // Return JSON
    //     return response()->json($dataList);
    // }

   
 

    /**
     * Store a newly created resource in storage.
     */ 
    public function store(Request $request)   //: RedirectResponse  
    {    //: RedirectResponse    //StoreAppReceiveRequest
            // dd($request->all());
        try {
            $success = false ; 
            $count = 0 ; 
            $smsg = "" ; 

           $data = $request->validate([
                'oprId'             => ['required', 'integer'],
                'web'               => ['required', 'integer', 'min:0'],
                'web2'              => ['required', 'integer', 'min:0'],
                'orderId'           => ['required', 'integer'],
                'appdev'          => ['required', 'string'],
                 'bundleSl'          => ['required', 'integer'],
                'selected_faults'   => ['required', 'json'],
                'selected_process'  => ['required', 'json'],
            ]);
 // dd($data['appdev']) ; 
            // dd($data);
           $orderCode = $data['orderId'] ; 
           $bundleSl = $data['bundleSl'] ; 
            $passqty = $data['web'] ; 
            $qcId =  $data['oprId'] ; 
            $defectCode = 0; 
            $operationCode = 90001; 
            $qcType = 6 ; 
            $companyId =13 ; 
            $batchNo ='';
            $cutNo = '';
            $color = '';
            $size = '';
            $bundleNo = '';
            $rfidSerial = '';
            $line = '';
            $devId = ''; 
             $operatorId =  $data['oprId'] ; 
             $loginCode= 0;
           $bundle = BundleInfo::find($bundleSl) ; 

           if($bundle){
                $batchNo =$bundle->batchNo    ;
                $cutNo = $bundle->CutNo    ;
                $color = $bundle->color    ;
                $size = $bundle->size    ;
                $bundleNo = $bundle->bundle_no    ;
                $rfidSerial = $bundle->bundle_rfid    ;
           }

            $ll = LineQC::where('appId',$data['appdev'])->first();
            // dd($ll) ; 
            if ($ll) {
                $line = $ll->lineId;
                $devId = $ll->devId; 
            }
           
            $save =  QCfault::updateOrCreate(
                [
                    'company_id'   => $companyId,
                    'bundleNo'     => $bundleNo,
                    'OrderCode'    => $orderCode,
                    'batchNo'      => $batchNo,
                    'CutNo'        => $cutNo,
                    'color'        => $color,
                    'DefectCode'   => $defectCode,
                    'devID'        => $devId,
                    'qcType'       => $qcType,
                   
                ],
                [
                    'Date'         => date('Y-m-d'), 
                    'hr'           => date('H') ,
                    'bundleSl'     => $bundleSl,
                    'OperationCode'=> $operationCode,
                    'size'         => $size,
                    'RFID_serial'  => $rfidSerial,
                    'operatorID'   => $operatorId,
                    'qcId'         => $qcId,
                    'Qty'          => $passqty,
                    'line'         => $line,
                    'logincode'    => $loginCode,
                ]
            );
            if($save){

                $faults = json_decode($request->selected_faults, true);
                $processes = json_decode($request->selected_process, true);

                foreach ($faults as $index => $defectCode) {

                    $operationCode = $processes[$index] ?? null;

                    if($operationCode){
                        $mapped = NodeDataMapped::Select('operatorID','terminal','processID')->where('bundleSl',$bundleSl)->where('processID', $operationCode)->orderby('sl','desc')->first(); 
                        if( $mapped){
                            $operatorId= $mapped->operatorID ;
                        }
                    }
  

                    $save = QCfault::updateOrCreate(
                        [
                            'company_id'   => $companyId,
                            'bundleNo'     => $bundleNo,
                            'OrderCode'    => $orderCode,
                            'batchNo'      => $batchNo,
                            'CutNo'        => $cutNo,
                            'color'        => $color,
                            'DefectCode'   => $defectCode,
                            'devID'        => $devId,
                            'qcType'       => $qcType,
                        ],
                        [
                            'Date'          => date('Y-m-d'),
                            'hr'            => date('H'),
                            'bundleSl'      => $bundleSl,
                            'OperationCode' => $operationCode,
                            'size'          => $size,
                            'RFID_serial'   => $rfidSerial,
                            'operatorID'    => $operatorId,
                            'qcId'           => $qcId,
                            'Qty'            => 1,
                            'line'           => $line,
                            'logincode'      => $loginCode,
                        ]
                    );
                }




                 LineQC::where('appId',$data['appdev'])->update([ 'cardId' => null , 'updated_at' => date('Y-m-d H:i:s') ,]) ; 
                return redirect()->route('qc-entry.index')->with('success', 'Data Inserted successfully.');
            }
            else{
                 return redirect()->route('qc-entry.index')->with('error', 'Data save failed!.');
            }
           
        } catch (Exception $e) {
            $mess = $e->getMessage(); 
            // dd($mess) ; 
             return response()->json([
                'success' => $success ,
                'smsg' => $mess,
        
            ]);

           
           
        }
    }

    public function destroy($id)
    {
        // dd($id) ; 
        $record = AppLog::find($id);


        // $cenId = auth()->user()->centerId ; 
        $record->delete();  
        $is_update = AppReceive::find($record->web_ref)->update([
                    'stepId'       =>  3, 
                    'updated_at'=>Date('Y-m-d H:i:s')
                ]);

       $user =  auth()->user()->id ; 

        $latest = AppLog::with(['webref' => function ($query) {
                    $query->select('id', 'Webfile', 'passport', 'ApplicantName', 'contact');
                }])
                ->where('Date', date('Y-m-d'))
                ->where('created_by', $user)
                ->where('stepId', 4)
                ->orderBy('id', 'desc')
                ->take(20)
                ->get();
        $count = AppLog::where('Date', date('Y-m-d'))->where('created_by', $user )->where('stepId',4)->count() ; 
     
        return response()->json([
                        'success' => true,
                        'message' => "Data Deleted successfully", 
                        'count' => $count, 
                        'latest' => $latest,
                    ]);
    }


    public function checkQCcard(Request $request)
    {
        $message = '';
        $operator = '';
        $operatorId = '';
        $ordercode='';
        $orderRef='';
        $style='';
        $color=''; 
        $size = '';
        $bundleNo = '';
        $bundleqty ='';
        $buyer = '';
        $bundleSl =  ''; 
        $processList=[]; 
         $mapped = [] ; 
        // dd($request->all()) ; 
        if (empty($request->operatorId)) {

            $card = LineQC::select('cardId')
                ->where('appId', $request->deviceId)
                ->first();

            if ($card && $card->cardId) {

               $emp = Employee::where('card_id', $card->cardId)
                    ->where('emp_type', 2)
                    ->where('status', 1)
                    ->orderBy('id', 'desc')
                    ->first();

                if ($emp) {
                    $operatorId = $emp->id;
                    $operator = $emp->emp_id . ' | ' . $emp->emp_name;
                    LineQC::where('appId',$request->deviceId)->update([ 'opratorId' => $operatorId , 'updated_at' => date('Y-m-d H:i:s') ,]) ; 
                     $message = 'Successful' ; 
                } else {
                    $message = 'Invalid Operator';
                }

            } else {
                $message = 'Invalid AppId/Line';
            }
        }
        else{
            $card = LineQC::select('cardId')
                ->where('appId', $request->deviceId)
                ->first();

            if ($card && $card->cardId) {
                
                $bundle = BundleInfo::where('bundle_rfid', $card->cardId)
                        ->where('in_production', 1)
                        ->where('status', 1)
                        ->orderBy('id', 'desc')
                        ->first();

                if ($bundle) {
                    // dd($bundle) ; 
                    $ordercode = $bundle->OrderCode ;
                    $orderRef=  $bundle->orderRef ;
                    $style= $bundle->style_no  ;
                    $color= $bundle->color; 
                    $size =  $bundle->size;
                    $bundleNo =  $bundle->bundle_no;
                    $bundleqty = $bundle->bundle_qty;
                    $bundleSl = $bundle->id ; 
                    if($bundle->buyer){
                        $bb = Buyer::where('id',$bundle->buyer)->first() ; 
                         $buyer =$bb->buyer ; 
                    }
                    
                    $processList = OperationList::select('OrderCode','OperationCode','OperationName','MC_Type','OperationSeqNo','IsLastOperation')->where('OrderCode',$ordercode  )->whereNull('IsLastOperation')->get() ; 


                    $message = 'Successful' ; 
                } else {
                    $message = 'Invalid Bundle';
                }

            } else {
                $message = 'Scan Bundle';
            }
           
        }

        return response()->json([
            'operator'   => $operator,
            'message'    => $message,
            'operatorId' => $operatorId,
             'ordercode' => $ordercode, 
             'orderRef' =>   $orderRef, 
             'style' =>    $style, 
             'color' =>    $color, 
             'size' =>   $size , 
             'bundleNo' =>   $bundleNo , 
             'bundleqty' =>    $bundleqty , 
             'buyer' =>   $buyer , 
             'bundleSl' =>   $bundleSl , 
             'processList' =>  $processList ,
             'mapped' =>  $mapped ,

        ]);
    }

}
