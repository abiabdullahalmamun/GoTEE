<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAppReceiveRequest;
// use App\Http\Requests\UpdateRegionRequest;
use App\Models\Region;
use App\Models\Center;
use App\Models\Counter;
use App\Models\CounterSvc ; 
use App\Models\VisaType;
use App\Models\VisaTypeTdd ;
use App\Models\VisaTypeApt;
use App\Models\AptOverride;
use App\Models\StickerMap;
use App\Models\Service;
use App\Models\CurrentQueue;
use App\Models\QueueCode ; 
use App\Models\TokenLog ; 
use App\Models\SslAptList ; 
use App\Models\NicAptList ; 
use App\Models\RejectReason ; 
use App\Models\RejectLog ; 
use App\Models\RejectLogReason ; 
use App\Models\AppSteps ; 
use App\Models\AppLog ;
use App\Models\AppReceive ; 
use App\Models\ForeignPass;
use App\Models\EntryType;
use App\Models\VisaDuration;
use App\Models\MoneyReceipt ;
use App\Models\CurrencyRate ; 
use App\Models\SmsOtp ; 
use App\Models\SmsLog ; 
use App\Models\User ; 
use App\Models\PortReceive ; 
use App\Models\PortLog ;
use App\Models\Port ; 
use App\Models\DisplayScroll ; 
use App\Jobs\SendSmsDCJob;
// use App\Imports\WebFileImport; 
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\DB;
use App\Services\ActionExceptionService;


class  PortReceiveController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $perPage = $request->input('per_page', 10);
        $search = $request->input('search');

        try {
            $ips = request()->getClientIps(); 
            $lastHopIp = end($ips);
            // dd($lastHopIp)     ; 
            $cenId = auth()->user()->centerId ; 

            if($cenId){
                $cnt = Counter::select('id','center_id','counter_id','loginstate','tokenno','autoMan','visaType','stickerType','enCall')->where('ip',$lastHopIp )->first() ; 
                // dd($cnt) ; 
                if($cnt){
                    // dd($cnt) ; 
                    if($cnt->center_id === $cenId ){
                        $cntNo =$cnt->counter_id ;  
                        $lastSvc = $cnt->loginstate ;  
                        $lastTkn = $cnt->tokenno ;  
                        $lastoptype = $cnt->autoMan ;  
                        $lastvisa = $cnt->visaType ;  
                        $laststicker = $cnt->stickerType ; 
                        $enCall =  $cnt->enCall ; 
                        // dd($enCall) ; 
                        $user = auth()->user()->id ; 
                        $total = AppReceive::where('Date', date('Y-m-d'))->where('created_by', $user)->where('centerId',$cenId)->count() ; 
                        // $currQ = CurrentQueue::select('token_number')->where('centerId', $cenId)->where('Date', date('Y-m-d'))->where('token_svc_no',$lastSvc)->where('token_type',1)->orderBy('token_number','asc')->take(15)->get() ;

                        $baseQuery = CurrentQueue::where('token_svc_no', $lastSvc)
                                    ->where('centerId', $cenId)
                                    ->where('Date', date('Y-m-d'))
                                    ->orderBy('token_number', 'asc');

                        $currQ = (clone $baseQuery)
                                    ->where('token_type', 1)
                                    ->take(15)
                                    ->get(['token_number']);   
                        $waitQ = (clone $baseQuery)
                                    ->where('token_type', 2)
                                    ->where('cnt', $cntNo)
                                    ->take(15)
                                    ->get(['token_number']);         

                        if( $lastSvc=="1"){
                             $recallQ = (clone $baseQuery)
                                    ->where('token_type', 3)
                                    ->where('cnt', $cntNo)
                                    ->take(15)
                                    ->get(['token_number']);  
                        }
                        else{
                             $recallQ = (clone $baseQuery)
                                    ->where('token_type', 3)
                                    // ->where('cnt', $cntNo)
                                    ->take(15)
                                    ->get(['token_number']);  
                        }
                        

                        $svcIds = Service::select('service_name','id','type')
                        // ->where('type', 0)
                        ->whereIn('id', CounterSvc::where('counterId',$cnt->id)->pluck('svcId'))
                        ->get();
 // dd($svcIds) ; 
                        // if ($svcIds->count() == 0) {
                        //     $svcIds = Service::select('service_name','id','type')->where('status',1)->get();
                        // }
                        $svcType = $svcIds->firstWhere('id', $lastSvc)?->type ?? '';
                        // dd($svcType) ; 
                        $VisaType = VisaType::select('visa_type','id' )->where('status',1)->orderBy('visa_type','asc')->get() ; 
                        $centerList = Center::select('center_name','id')->where('status',1)->orderBy('center_name','asc')->get();
                        // $counterList = Counter::select('counter_id','id')->where('center_id',$cenId)->where('status',1)->orderby('counter_id','asc')->get() ;
                        $rejectionReasons = RejectReason::select('id','reason_name')->orderBy('reason_name','asc')->get() ; 
                        $stickerList = StickerMap::select('id','sticker')->orderBy('sticker','asc')->get() ; 

                        // dd($enCall) ; 
                      return view('pages.PortReceive.index', [
                            'currQ' => $currQ,
                            'recallQ' => $recallQ,
                            'waitQ' => $waitQ,
                            'svcIds' => $svcIds,
                            'svcType' => $svcType,
                            'VisaType' => $VisaType,
                            'centerList' => $centerList,
                            // 'counterList' => $counterList,
                            'counter' =>  $cntNo,

                            'lastSvc' =>  $lastSvc,
                            'lastTkn' =>  $lastTkn,
                            'lastoptype' =>  $lastoptype,
                            'lastvisa' =>  $lastvisa,
                            'laststicker' =>  $laststicker,
                            'enCall' =>  $enCall,
                            // 'tokenhistory' =>  $tokenhistory,

                            'stickerList' => $stickerList,
                            'rejectionReasons' => $rejectionReasons,
                            'total' => $total,
                        ]);

                    }
                    else{
                         return view('pages.error.index', [
                            'error' => 'Counter not registered for this Center',
                        ]);
                    }
                   
                }
                else{
                     return view('pages.error.index', [
                        'error' => 'Counter Not Registered',
                    ]);
                }
            }
            else{
                 return view('pages.error.index', [
                        'error' => 'Page not permitted',
                    ]);
            }
         
     
        } catch (Exception $e) {
             dd($e->getMessage()); 
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

            // Shop::create([
            //         'ShopName'=> $r->name,
            //         'Contact'=>$r->Contact,
            //         'message'=>$r->mess,
            //         'status'=>1,
            //         'created_by'=>Auth::user()->user_id,
            //         'created_at'=>Date('Y-m-d H:i:s'),
            //         'updated_at'=>Date('Y-m-d H:i:s')
            //     ]);

    public function getCurrQ($svctypeId, Request $request)
    {
        $cnt = $request->query('cnt');
        $cenId = auth()->user()->centerId ; 
        // Fetch token numbers based on selected service type
        $baseQuery = CurrentQueue::where('token_svc_no', $svctypeId)
                    ->where('centerId', $cenId)
                    ->where('Date', date('Y-m-d'))
                    ->orderBy('token_number', 'asc');

        $currQ = (clone $baseQuery)
                    ->where('token_type', 1)
                    ->take(15)
                    ->get(['token_number']);   
        $waitQ = (clone $baseQuery)
                    ->where('token_type', 2)
                    ->where('cnt', $cnt)
                    ->take(15)
                    ->get(['token_number']);         

        if($svctypeId=='1'){
             $recallQ = (clone $baseQuery)
                    ->where('token_type', 3)
                    ->where('cnt', $cnt)
                    ->take(15)
                    ->get(['token_number']);  
        }
        else{
             $recallQ = (clone $baseQuery)
                    ->where('token_type', 3)
                    ->take(15)
                    ->get(['token_number']);  
        }            

       
                               
         return response()->json([
                    // 'currQ' => $currQ,
                    'waitQ' => $waitQ,
                    'currQ' => $recallQ,
                ]); 
        // $currQ = CurrentQueue::where('token_svc_no', $svctypeId)
        //              ->where('centerId', $cenId)
        //              ->where('Date', date('Y-m-d'))
        //              ->where('token_type',1)
        //              ->orderBy('token_number','asc')
        //              ->take(15)
        //              ->get(['token_number']);

        // Return JSON
        // return response()->json($currQ);
    }
    public function getDefQ($svctypeId)
    {
        $cenId = auth()->user()->centerId ; 
        // Fetch token numbers based on selected service type
        $defQ = CurrentQueue::where('token_svc_no', $svctypeId)
                     ->where('centerId', $cenId)
                     ->where('Date', date('Y-m-d'))
                     ->where('token_type',2)
                     ->orderBy('token_number','asc')
                     ->take(15)
                     ->get(['token_number']);

        // Return JSON
        return response()->json($defQ);
    }

    public function checkCodeweb(Request $request)
    {
        // dd($request) ; 
        $exists = false; 
        $code = $request->input('code');
        $token =  $request->input('tkn');
        $svc =  $request->input('svc');
        $type =  $request->input('type');
        $cntNo = $request->input('cont');
        if(!$svc){
             return response()->json(['found' => false,'tokenNo' => '' ]);
        }

        if(!$type){
             $svcType = Service::select('type')->where('id',$svc)->first() ;
             $type = $svcType->type ; 
        }
       
         $tokenNo = '' ; 
        // dd($type->type) ; 
        $cenId = auth()->user()->centerId ; 

        $CodeRec = QueueCode::where('random', $code)
            ->where('centerId', $cenId)
            ->where('Date', date('Y-m-d'))
            ->where('status', 1)
            ->first(['token', 'svclogId']);

        if($CodeRec){
            if($type==1){
                $exists = true ; 
                $tokenNo = $CodeRec->token ; 
            }
            else{
                if($token==$CodeRec->token){
                    $exists = true ; 
                    $tokenNo = $CodeRec->token ; 
                }
                else{
                    $exists = false ; 
                    $tokenNo = $CodeRec->token ; 
                }
            }
        }
        else{
            $exists = false ; 
            $tokenNo = '' ; 
        }    

        if($exists==true){
            if($type==1){
                $update=TokenLog::where('id',$CodeRec->svclogId)->whereNull('scantime')->update([
                        'ststart'  => now(), 
                        'scantime'  => now(), 
                        'waiting'    => DB::raw('TIMESTAMPDIFF(SECOND, tissuetime, NOW())'),
                        'cno'  => $cntNo, 
                        'servedby'  => auth()->user()->id ,
                        'updated_at'=>now()
                ]);  
            }
            else{
                $update=TokenLog::where('id',$CodeRec->svclogId)->whereNull('scantime')->update([
                        'scantime'  => now(), 
                        'updated_at'=>now()
                    ]);  
            }
          
        }

        return response()->json(['found' => $exists,'tokenNo' => $tokenNo ]);
    }

    public function counterLogin(Request $request)
    {
        try{
            $cnt = $request->input('cnt');
            $svc = $request->input('svc');
            // dd($svc) ; 
            $msg = ''; 
            $msg = $svc ; 
            $reply = [] ; 
            $totalToken=0 ; 
            if($svc){
                $cenId = auth()->user()->centerId ; 
                $user = auth()->user()->id ; 

                $updateEx = Counter::where('loginId',$user)->update([
                        'loginstate'  => 0, 
                        'loginId'  =>  null, 
                        'tokenno'  =>  null, 
                        'autoMan'  =>  null, 
                        'visaType'  =>  null, 
                        'stickerType'  =>  null, 
                        'updated_at'=>now()
                    ]);  

                $update=Counter::where('center_id',$cenId)->where('counter_id',$cnt)->update([
                        'loginstate'  =>  $svc, 
                        'loginId'  =>  $user, 
                        'updated_at'=>now()
                    ]);  

                 $totalToken = CurrentQueue::where('Date', date('Y-m-d'))->where('token_svc_no',$svc)->where('token_type',1)->where('centerId',$cenId)->count() ; 
                if($update){
                    $msg = 'Logged in Counter '.$cnt.' Service '.$svc ; 
                    return response()->json([
                        'found' => true,
                        'message' =>$msg,
                         'reply' =>$reply,
                          'totalToken' =>$totalToken,
                    ]);
                }
                else{
                     return response()->json([
                         'found' => true,
                         'message' =>$msg,
                         'reply' =>$reply,
                          'totalToken' =>$totalToken,
                    ]);
                }
           
            }
            else{
                $msg = 'Please Select ServiceType' ; 
                 return response()->json([
                        'found' => true,
                         'message' =>$msg,
                        'totalToken' =>$totalToken,
                    ]);
            }
        } catch (Exception $e) {
            $msg = $e->getMessage(); 
            return response()->json([
                'found' => true,
                 'message' =>$msg,
                'totalToken' =>$totalToken,
            ]);
        }


    }
    
    public function saveOpType(Request $request)
    {
        try{
            $cnt = $request->input('cnt');
            $opntype = $request->input('opntype');
            // dd($svc) ; 
            $msg = ''; 
            $reply = [] ; 
            if($opntype){
                $cenId = auth()->user()->centerId ; 
                // $user = auth()->user()->id ; 
                $update=Counter::where('center_id',$cenId)->where('counter_id',$cnt)->update([
                        'autoMan'  =>  $opntype, 
                        'updated_at'=>now()
                    ]);  

                if($update){
                    $msg = 'Operation type saved for counter '.$cnt ; 
                    return response()->json([
                        'found' => true,
                        'message' =>$msg,
                         'reply' =>$reply,
                    ]);
                }
                else{
                      $msg = 'Failed to save ' ; 
                     return response()->json([
                         'found' => true,
                         'message' =>$msg,
                           'reply' =>$reply,
                    ]);
                }
            }
            else{
                $msg = 'Please Select operation type' ; 
                 return response()->json([
                        'found' => true,
                         'message' =>$msg,
                    ]);
            }
        } catch (Exception $e) {
            $msg = $e->getMessage(); 
            return response()->json([
                'found' => true,
                 'message' =>$msg,
            ]);
        }
    }

    public function saveVisaType(Request $request)
    {
        try{
            $cnt = $request->input('cnt');
            $visatype = $request->input('visatype');
            // dd($svc) ; 
            $msg = ''; 
            $reply = [] ; 
            if($visatype){
                $cenId = auth()->user()->centerId ; 
                // $user = auth()->user()->id ; 
                $update=Counter::where('center_id',$cenId)->where('counter_id',$cnt)->update([
                        'visaType'  =>  $visatype, 
                        'updated_at'=>now()
                    ]);  

                if($update){
                    $msg = 'Visa type saved for counter '.$cnt ; 
                    return response()->json([
                        'found' => true,
                        'message' =>$msg,
                         'reply' =>$reply,
                    ]);
                }
                else{
                      $msg = 'Failed to save ' ; 
                     return response()->json([
                         'found' => true,
                         'message' =>$msg,
                           'reply' =>$reply,
                    ]);
                }
            }
            else{
                $msg = 'Please Select operation type' ; 
                 return response()->json([
                        'found' => true,
                         'message' =>$msg,
                    ]);
            }
        } catch (Exception $e) {
            $msg = $e->getMessage(); 
            return response()->json([
                'found' => true,
                 'message' =>$msg,
            ]);
        }
    }

    public function saveStickerType(Request $request)
    {
        try{
            $cnt = $request->input('cnt');
            $st_type = $request->input('st_type');
            // dd($svc) ; 
            $msg = ''; 
            $reply = [] ; 
            if($st_type){
                $cenId = auth()->user()->centerId ; 
                // $user = auth()->user()->id ; 
                $update=Counter::where('center_id',$cenId)->where('counter_id',$cnt)->update([
                        'stickerType'  =>  $st_type, 
                        'updated_at'=>now()
                    ]);  

                if($update){
                    $msg = 'Sticker type saved for counter '.$cnt ; 
                    return response()->json([
                        'found' => true,
                        'message' =>$msg,
                         'reply' =>$reply,
                    ]);
                }
                else{
                      $msg = 'Failed to save ' ; 
                     return response()->json([
                         'found' => true,
                         'message' =>$msg,
                           'reply' =>$reply,
                    ]);
                }
            }
            else{
                $msg = 'Please Select operation type' ; 
                 return response()->json([
                        'found' => true,
                         'message' =>$msg,
                    ]);
            }
        } catch (Exception $e) {
            $msg = $e->getMessage(); 
            return response()->json([
                'found' => true,
                 'message' =>$msg,
            ]);
        }
    }
    public function checkbarcodeSticker(Request $request)
    {
        // dd($request) ; 
        $st = $request->input('stNo');
        $cenId = auth()->user()->centerId ; 

        // $centerName = '';
        $center = Center::select('center_name')->where('id',$cenId)->first() ; 
        if($center){
            $centerName  = substr($center->center_name, 0, 2) ; 
        }

        $subst = "@".$centerName.date('ymd') ; 
        if(substr($st, 0, 9)==$subst){

            $stc = AppReceive::select('id')->where('stickerNo',$st)->first() ; 
            if($stc){
                return response()->json([
                     'found' => false , 
                        'dd' => $subst,
                         'st' => 1,
                ]);
               
            }
            else{
                 return response()->json([
                    'found' => true,
                    'dd' => $subst,
                     'st' => 2,
                ]);
            }
           
        }
        else{
            return response()->json([
                'found' => false , 
                'dd' => $subst,
                 'st' => 3,
            ]);
        }

    }

    public function checkpassport(Request $request)
    {
        // dd($request) ; 
        $pass = $request->input('pass');
        $status = true ; 
        // $cenId = auth()->user()->centerId ; 
        $msg = '' ; 
        $passRecords = AppReceive::where('passport', $pass)
                    ->orderBy('Date', 'desc')
                    ->get();

        // $dd =   $passRecords ;            
        if ($passRecords) {
            $todayRecord = $passRecords->firstWhere('Date', today()->toDateString());
            $oldRecord = $passRecords->first(function ($r) {
                return $r->Date !== today()->toDateString() && $r->stepId != 5;
            });

            // dd($todayRecord) ; 
            if($todayRecord){
                 $msg =  "Passport Already Saved Today!!!" ; 
            }
            else{
                // dd($oldRecord) ; 
                if ($oldRecord) {
                   // dd($oldRecord->stepId) ; 
                    if($oldRecord->stepId == 1){
                        $msg = 'Undelivered Passport, Received on '.$oldRecord->Date ; 
                    }
                    else  if($oldRecord->stepId == 2){
                        $msg = 'Undelivered Passport in HCI, Received on '.$oldRecord->Date ; 
                    }
                    else  if($oldRecord->stepId == 3){
                        $msg = 'Undelivered Passport returned from HCI, Received on '.$oldRecord->Date ;
                    }
                    else if($oldRecord->stepId == 4){
                        $msg = 'Undelivered Passport at center, Received on '.$oldRecord->Date ;
                    }
                }
            }

        }
        if($msg!=''){
            $status = false ; 
        }
        else{
            $status = true ; 
        }
       return response()->json([
            'found' => $status , 
            'msg' => $msg,
        ]);

    }
    public function checkwebpay(Request $request)
    {
        // dd($request) ; 
        $web = $request->input('web');
        $opT = $request->input('opT');  
        $code = $request->input('code');  
        $token = $request->input('tkn');
        $regText = '';
        $cenId = auth()->user()->centerId ; 

        $exists = QueueCode::where('random', $code)
                    ->where('token',$token)
                    ->where('centerId',$cenId)
                    ->where('web',$web)
                    ->where('Date',date('Y-m-d'))
                    ->where('status',1)
                    ->exists();

        if(!$exists){
            return response()->json(['found' => false, 'message' => 'Tokne, Code & Webfile match not active!!!']);
        }


        $regData = Center::select('region_id')->where('id',$cenId)->first() ; 
        if($regData){
            $region = Region::select('region_text')
                        ->where('id',$regData->region_id)
                        ->first();
            if($region){
                 $regText =  $region->region_text ; 
            }

            $webExist =  AppReceive::select('id')->where('regionId',$regData->region_id)->where('Webfile', $web)->first() ; 
             if($webExist){
                return response()->json(['found' => false, 'message' => 'Webfile Already Saved']);
             }
             else{
                 $rejectExist = RejectLog::select('id')->where('Webfile', $web)->first() ; 
                 if($rejectExist){
                     return response()->json(['found' => false, 'message' => 'Webfile Already Rejected!!!']);
                 }
             }
        }

        if(strlen($web) !=12){
            return response()->json(['found' => false, 'message' => 'Webfile Should be 12 characters']);
        }

        if(substr($web, 0, 4) !=  $regText){
            return response()->json(['found' => false, 'message' => 'Webfile Should start with '.$regText]);
        }

    

        $name = '';
        $passport = '' ;
        $contact = '';
        $onlinepay = ''; 
        $visatty = '';
        $txnId ='';
        $txndate = '';
        $txnamt = '' ; 
        if($opT=="auto"){
            $ddt = NicAptList::select('passport','Name','contact')->where('webfile',$web)->first() ; 
            if($ddt){
                $name = $ddt->Name ; 
                $passport = $ddt->passport ; 
                $contact = $ddt->contact ; 
            }
        }
       
        $record = SslAptList::select('txnId', 'visatype','txn_date','amount' )
                    ->where('WebFile_no', $web)
                    ->where('status', 1)
                    ->first();
        if ($record) {
            $onlinepay = 'Ok'; 
            $visatty  = $record->visatype ; 
            $txnId = $record->txnId ; 
            $txndate = $record->txn_date ; 
            $txnamt = $record->amount ; 
        } else {
             $data = array(
                         "web"    =>  $web ,  //  "2024-05-30" ,  // 
                    );
            $GatewayController = new GatewayController();
            $reply = $GatewayController->checkApt($data)  ;  
            // dd($reply) ;

            if($reply){
                if($reply['code']==200){
                  $webfileNo =$reply['data']['webfile_no'];
                  $Aptdate_org= $reply['data']['previous_appointment_date'];
                  $Aptdate_res = $reply['data']['current_appointment_date'];
                  $Apthour =$reply['data']['appointment_hour'];
                  $paystatus=$reply['data']['status'];
                  $check_user=$reply['data']['user_id'];
                  $checked_on=$reply['data']['checked_on'];
                  $order_id = $reply['data']['order_id'];
                  $amount=$reply['data']['amount'];
                  $visaType=$reply['data']['visa_type'];
                  $ivac_name=$reply['data']['ivac_name'];
                  $txn_date=$reply['data']['trans_date'];

                            // dd()
                $save = SslAptList::upsert([
                        [
                            'prev_date' => $Aptdate_org, 
                            'curr_date' => $Aptdate_res,
                            'apt_hr' => $Apthour,
                            'WebFile_no' => $webfileNo,
                            'paystatus' => $paystatus,
                            'checked_user' => $check_user,
                            'checked_on' => $checked_on,
                            'txnId' => $order_id,
                            'amount' => $amount,
                            'visatype' => $visaType,
                            'center' => $ivac_name,
                            'txn_date' => $txn_date,
                            'status' => 1,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]
                    ],
                    ['center', 'WebFile_no'], // Unique constraint keys
                    ['prev_date', 'curr_date', 'apt_hr', 'paystatus', 'checked_user','checked_on','txnId','amount','visatype','txn_date','updated_at','status']); // 

                    $onlinepay = 'Ok'; 
                    $visatty  = $visaType ; 
                    $txnId = $order_id ; 
                    $txndate = $txn_date  ; 
                    $txnamt = $amount ; 
                }
                else
                {
                     $onlinepay = 'Nil'; 
                }
            }
            else{
                 $onlinepay = 'Nil'; 
            }
           
        }

        return response()->json([
            'found' => true,
            'visatype' => $visatty ,
            'onlinepay' => $onlinepay,
            'name' => $name,
            'passport' =>  $passport,
            'contact' => $contact ,
            'txnId' => $txnId ,
            'txndate' => $txndate , 
            'txnamt' => $txnamt , 
            'request'=> $code
        ]);


    }


    public function reject(Request $request)
    {
        // dd($request->all()) ; 

        $request->validate([
            'rejection_reasons' => 'required|array|min:1',
        ]);

        $reason = $request->rejection_reasons ; 
     
        $web = $request->webNo ; 
        $cenId = auth()->user()->centerId ; 
 
        $reject = RejectLog::updateOrCreate(
            [
                'Webfile' => $web,
            ],
            [
                'centerId'   => $cenId,
                'Date'       => date('Y-m-d'),
                'ApplicantName'   => $request->a_name,
                'passport'   => $request->a_pass,
                'contact'   => $request->a_contact,
                'visatype'   => $request->a_visaType,
                'created_by' => auth()->id(),
            ]
        );
        $lastId = $reject->id; 

        // dd($lastId) ; 
        for($i=0; $i<count($reason); $i++){
            $rejectId = RejectLogReason::updateOrCreate(
                [
                    'web_ref' => $lastId,
                    'reasonId' => $reason[$i],
                ],
                [
                    'created_by' => auth()->id(),
                ]
            );

        }

         $is_update = SslAptList::where('WebFile_no',$web)->update([
                    'status'       =>  0, 
                    'updated_at'=>Date('Y-m-d H:i:s')
                ]);

         $update = QueueCode::where('Date',date('Y-m-d'))->where('centerId',$cenId)->where('web',$web)->update([
            'status'       =>  0, 
            'updated_at'=>Date('Y-m-d H:i:s')
        ]);

         $ips = request()->getClientIps(); 
        $lastHopIp = end($ips);

        $update=Counter::where('center_id',$cenId)->where('ip',$lastHopIp)->update([
                    'enCall'  =>  1, 
                    'updated_at'=>now()
                ]);  

        return redirect()->route('app-receive.index')->with('success', 'Webfile Rejected successfully.');
       
    }



    public function edit($id)
    {
        // dd($id) ; 
        $data = AppReceive::where('id', $id)->first() ; 
        // dd($data) ; 
        $centerId = auth()->user()->centerId;

        // $cenId = auth()->user()->centerId ; 
        if($data->centerId != $centerId ){
            return redirect()->route('searchWebfile.index')->with('error', "Cannot edit other center data");
        }
        else{

            if ($data->remarks === 'FOREIGN') {
                $fpdata = ForeignPass::where('web_ref', $data->id)->first();
            }
            else{
                 $fpdata = [] ; 
            }
            $visaType = VisaType::select('id','visa_type')->get() ; 
            $stickers = StickerMap::select('id','sticker')->get() ; 

            $lastBook = MoneyReceipt::latest('id')->value('BookNo') ?? 0;
            $visaDuration = VisaDuration::where('status',1)->get() ;
            $entrytype = EntryType::where('status',1)->get() ;
            $currencyRate = CurrencyRate::latest()->first();

            return view('pages.AppReceive.partials.edit', compact( 'visaType',  'data','stickers','fpdata','lastBook','visaDuration','entrytype','currencyRate'));
        }

       
    }


    public function update(Request $request)
    {
        // dd($request->all());
        $request->merge([
            'remarks' => strtoupper(trim($request->remarks))
        ]);
           $request->validate([
            'Webfile' => 'required|string',
            'name' => 'required|string',
            'passport' => 'required|string',
            'visatype' => 'required|numeric',
            'stickerNo' => 'required|string',
            'stickertype' => 'required|numeric',
            'contact' => 'required|string',
            'recId' => 'required|numeric',
            'corrfee' => 'required|numeric',
            'remarks' => 'required|string',

            'gratis'      => 'nullable|numeric|required_if:remarks,FOREIGN',
            'nationality' => 'nullable|string|required_if:remarks,FOREIGN',
            'duration'    => 'nullable|numeric|required_if:remarks,FOREIGN',
            'entryType'   => 'nullable|numeric|required_if:remarks,FOREIGN',
            'BookNo'      => 'nullable|numeric|required_if:remarks,FOREIGN',
            'RecptNo'     => 'nullable|numeric|required_if:remarks,FOREIGN',
            'rupee_rate'  => 'nullable|numeric|required_if:remarks,FOREIGN',
            'visafee'     => 'nullable|numeric|required_if:remarks,FOREIGN',
            'icwf'        => 'nullable|numeric|required_if:remarks,FOREIGN',
            'faxcharge'   => 'nullable|numeric|required_if:remarks,FOREIGN',
            'visaApp'     => 'nullable|numeric|required_if:remarks,FOREIGN',
            'totalfee'    => 'nullable|numeric|required_if:remarks,FOREIGN',
        ]);

        // dd($request->all()); 
        $apprec = AppReceive::find($request->recId);
        if ($apprec) {
             $cenId = auth()->user()->centerId ; 
            if($apprec->centerId != $cenId ){
                  return redirect()->route('searchWebfile.index')->with('error', "Cannot edit other center data");

            }
            else{

                $apprec->ApplicantName = $request->name;
                $apprec->passport = $request->passport;
                $apprec->visatype = $request->visatype;
                $apprec->stickertype = $request->stickertype;
                $apprec->stickerNo = $request->stickerNo;
                $apprec->contact = $request->contact;
                $apprec->corrFee = $request->corrfee;
                $apprec->updated_at = now(); // automatically handled but you can set manually
                $apprec->save(); // Fires 'updated' event → Loggable works


                if($request->remarks=='FOREIGN'){
                    $book = MoneyReceipt::where('BookNo', $request->BookNo)->first();

                    if (!$book) {
                        return back()->withErrors([
                            'BookNo' => 'Invalid Book Number.'
                        ]);
                    }

                    if (
                        $request->RecptNo < $book->startNo ||  $request->RecptNo > $book->endNo
                     ) {
                        return back()->withErrors([
                            'RecptNo' => "Receipt number must be between {$book->startNo} and {$book->endNo} for BookNo {$request->BookNo}."
                        ]);
                    }

                    $recptExist = ForeignPass::select('id')->where('BookNo',$request->BookNo)->where('ReceiptNo',$request->RecptNo)->where('web_ref','!=',$request->recId)->first() ; 
                    if($recptExist){
                          return back()->withErrors([
                            'RecptNo' => "Receipt number: {$request->RecptNo} for Book {$request->BookNo} Already used."
                        ]);
                    }

                    $frp = ForeignPass::where('web_ref', $request->recId)->first();
                    $frp->gratis = $request->gratis;
                    $frp->BookNo = $request->BookNo;
                    $frp->ReceiptNo =$request->RecptNo;
                    $frp->nationality =$request->nationality;
                    $frp->visa_fee =$request->visafee;
                    $frp->fax_trans_charge =$request->faxcharge;
                    $frp->icwf =$request->icwf;
                    $frp->visa_app_charge =$request->visaApp;
                    $frp->total_amount = $request->visafee+$request->faxcharge+$request->icwf+$request->visaApp ; 
                    $frp->rupee_rate =$request->rupee_rate;
                    $frp->total_rupee = ($request->visafee+$request->faxcharge+$request->icwf+$request->visaApp )/$request->rupee_rate;
                    $frp->duration =$request->duration;
                    $frp->entryType =$request->entryType;
                    $frp->updated_at = now();
                    $frp->save();
                }


                return redirect()->route('searchWebfile.index')->with('success', 'Data updated successfully');
             }  
        }
        else{
              return redirect()->route('searchWebfile.index')->with('error', "no data found!!!");
        }
    }

    public function destroy($id){
        // dd($id) ;

        $apprec = AppReceive::find($id); 
        // dd($apprec) ; 
        if ($apprec) {
            // dd($apprec->Webfile) ; 
            $cenId = auth()->user()->centerId ; 
            if($apprec->centerId != $cenId ){
                 return redirect()->route('searchWebfile.index')->with('error', "Cannot delete other center data");
            }
            else{
                $del = $apprec->delete(); // Fires 'deleted' event → Loggable works
                if($del){
                     $deLog = AppLog::where('web_ref', $id)->delete() ; 
                     $deLog2 = ForeignPass::where('web_ref', $id)->delete() ; 
                     if($apprec->payId){
                         $is_update = SslAptList::find($apprec->payId)->update([
                                    'status'       =>  1, 
                                    'updated_at'=>Date('Y-m-d H:i:s')
                                ]);
                        }
                        else{
                             $is_update = SslAptList::where('WebFile_no',$apprec->Webfile)->update([
                                        'status'       =>  1, 
                                        'updated_at'=>Date('Y-m-d H:i:s')
                                    ]);
                        }

                    if($apprec->codeId){
                        QueueCode::where('id', $apprec->codeId)
                                ->update([
                                    'status'     => 1,
                                    'updated_at' => now()
                                ]);
                    }
       
                    return redirect()->route('searchWebfile.index')->with('success','Data Deleted successfully.');
                }
                else{
                     return redirect()->route('searchWebfile.index')->with('error','Failed to  Delete.');
                 
                }

            }
        
        }
        else{
             return redirect()->route('searchWebfile.index')->with('error','Data not found');
        }
      
    }


    /**
     * Store a newly created resource in storage.
     */ 
    public function store(StoreAppReceiveRequest $request): RedirectResponse  
    {    //: RedirectResponse    //StoreAppReceiveRequest
       // dd($request->all());
        try {

            $data = $request->validated();
            // dd($data) ; 
            $web = $data['wf_no'] ; 
            $cenId = auth()->user()->centerId ; 

            if ($request->svcType == 1 && !$request->filled('comment')) {
                 return redirect()->route('app-receive.index')
                    ->with('error', "Please add comment !!!");
            }

            if(substr($data['contact'], 0, 1)!='0'){
                 return redirect()->route('app-receive.index')
                    ->with('error', "Contact should start with 0 !!!");
            }
            // dd('cknvksf') ; 
            // $queueId = QueueCode::where('random', $data['code'])
            //     ->where('centerId', $cenId)
            //     ->where('web', $web)
            //     ->where('Date', date('Y-m-d'))
            //     ->where('status', 1)
            //     ->value('id');
            $queueId = QueueCode::where('random', $data['code'])
                ->where('centerId', $cenId)
                ->where('web', $web)
                ->where('token', $data['TokenNo'])
                ->where('Date', date('Y-m-d'))
                ->where('status', 1)
                ->first(['id', 'svc', 'token','svclogId']);
            if (!$queueId) {
                return redirect()->route('app-receive.index')
                    ->with('error', "Token, Code & Webfile match not active!!!");
            }


            if($data['payment_method']==1){
                // dd('oopp' ) ; 
               $payId = SslAptList::where('WebFile_no',$web)
                             ->where('curr_date',date('Y-m-d'))
                            ->where('status',1)
                            ->value('id');

                if (!$payId) {
                    return redirect()->route('app-receive.index')
                        ->with('error', "Active payment not found!!!");
                }
            }
            else{
                $payId = null ; 
            }

            $passExist =  AppReceive::select('id')
                            ->where('passport',$data['passport1'])
                             ->where('Date', date('Y-m-d'))
                            ->first() ; 
            if($passExist){
                $msg =  "passport Already Saved Today !!!" ; 
            }

            $rgstr = substr($web, 0, 4) ; 
            // dd($rgstr) ; 

            $reg = Region::select('id')->where('region_text',$rgstr)->first() ; 
        
            $msg = ''; 
            $webExist =  AppReceive::select('id')->where('regionId',$reg->id)->where('Webfile', $web)->first() ; 
            if($webExist){
                $msg =  "Webfile Already Saved !!!" ; 
            }
            else{
                $rejectExist = RejectLog::select('id')->where('Webfile', $web)->first() ; 
                 if($rejectExist){
                    return redirect()->route('frpReceive.index')->with('error', "Webfile Already Rejected!!!");
                 }

                // dd($data['op_type']) ; 
                if( $data['op_type']=='auto'){
                    if($data['passport1']!=$data['passport2']){
                         $msg =  "Passport Number Does not match !!!" ; 
                    }
                }


                $center = Center::select('center_name')->where('id',$cenId)->first() ; 
                if($center){
                    $centerName  = substr($center->center_name, 0, 2) ; 
                }

                $subst = "@".$centerName.date('ymd') ; 
                if(substr($data['sticker_no'], 0, 9)!=$subst){
                     $msg =  "Invalid Sticker No !!!" ; 
                }
                else{
                    $stc =  AppReceive::select('id')->where('Date',date('Y-m-d'))->where('stickerNo', $data['sticker_no'])->first() ;

                    if($stc){
                          $msg =  "Sticker No Already Saved !!!" ; 
                    }
                    else{
                        $passRecords = AppReceive::where('passport', $data['passport1'])
                                ->orderBy('Date', 'desc')
                                ->get();

                        if ($passRecords) {
                            $todayRecord = $passRecords->firstWhere('Date', today()->toDateString());
                            $oldRecord = $passRecords->first(function ($r) {
                                return $r->Date !== today()->toDateString() && $r->stepId != 5;
                            });

                            // dd($todayRecord) ; 
                            if($todayRecord){
                                 $msg =  "Passport Already Saved Today!!!" ; 
                            }
                            else{
                                // dd($oldRecord) ; 
                                if ($oldRecord) {
                                   // dd($oldRecord->stepId) ; 
                                    if($oldRecord->stepId == 1){
                                        $msg = 'Undelivered Passport, Received on '.$oldRecord->Date ; 
                                    }
                                    else  if($oldRecord->stepId == 2){
                                        $msg = 'Undelivered Passport in HCI, Received on '.$oldRecord->Date ; 
                                    }
                                    else  if($oldRecord->stepId == 3){
                                        $msg = 'Undelivered Passport returned from HCI, Received on '.$oldRecord->Date ;
                                    }
                                    else if($oldRecord->stepId == 4){
                                        $msg = 'Undelivered Passport at center, Received on '.$oldRecord->Date ;
                                    }
                                }
                            }

                        }

                        // $passport = AppReceive::select('id')->where('Date',date('Y-m-d'))->where('passport', $data['passport1'])->first() ; 

                        // if($passport){
                        //      $msg =  "Passport Already Saved !!!" ; 
                        // }
                  
                    }
                }
            }
            // dd($data['passport1'].$msg) ; 
            if($msg !='' ){
                 return redirect()->route('app-receive.index')->with('error', $msg);
            }
            else{
                $tdd = '';
                $tdds = VisaTypeTdd::select('tdd')->where('visatypeId',$data['visaType'])->where('centerId',$cenId )->first() ; 
                if($tdds){
                     $tdd = $tdds->tdd ; 
                }

              $record = AppReceive::updateOrCreate(
                    ['regionId' => $reg->id, 'Webfile' => $web], // Matching condition
                    [
                        'centerId'     => $cenId,
                        'Date'         => date('Y-m-d'),
                        'ApplicantName'=> $data['name'],
                        'passport'     => strtoupper(str_replace(' ', '', trim($data['passport1']))),
                        'stickertype'  => $data['st_color'],
                        'stickerNo'    => $data['sticker_no'],
                        'status'       => 1,
                        'contact'      => $data['contact'],
                        'visatype'     => $data['visaType'],
                        'pmethod'      => $data['payment_method'],
                        'txn'          => $data['fmember'],
                        'remarks'      => $data['remarks'],
                        'psQty'        => $data['OldPass'],
                        'corrFee'      => $data['corfee'],
                        'profee'      => $data['profee'],
                        'spfee'      => $data['spfee'],
                        'bioType'      => $data['bio_st'],
                        'stepId'       => 1,
                        'tknNo'       => $data['TokenNo'],
                        'cntNo'         => $data['counterNo'],
                        'tdd'        =>  $tdd,
                        'codeId'        =>  $queueId->id,
                        'payId'        =>  $payId,
                        'created_by'   => auth()->user()->id,
                    ]
                );

                $id = $record->id;

                if($id){
                      $save = AppLog::upsert([
                        [
                            'regionId'     => $reg->id,
                            'centerId'     => $cenId,
                            'Date'   => date('Y-m-d'),
                            'web_ref'     => $id   ,
                            'stepId'     => 1,
                            'remarks'     => '',
                            'created_by'=> auth()->user()->id ,
                            'created_at'=>Date('Y-m-d H:i:s'),
                            'updated_at'=>Date('Y-m-d H:i:s')
                        ]
                    ],
                    ['regionId' , 'web_ref', 'stepId'], // Unique constraint keys
                    ['updated_at',  'created_by']); // 

                    if($save){
                        if($data['svcType']==1){
                            $service = Service::find($data['svctypeId']);
                            $svcN = $service ? $service->service_name : null;

                            ActionExceptionService::store([
                                'module'      => 'Received Without Calling',
                                'action'      => 'AppReceived',
                                'centerId'      => $cenId ,
                                'remarks'     =>'CounterNo: '.$data['counterNo'].' Webfile: '.$web.' Passport:'.$data['passport1'].' Received as:'.$svcN.' with comment: '.$data['comment'], 
                            ]);
                        }
                  

                         $is_update = SslAptList::where('WebFile_no',$web)->update([
                            'status'       =>  0, 
                            'updated_at'=>Date('Y-m-d H:i:s')
                        ]);

                         $update = QueueCode::where('Date',date('Y-m-d'))->where('centerId',$cenId)->where('web',$web)->update([
                            'status'       =>  0, 
                            'updated_at'=>Date('Y-m-d H:i:s')
                        ]);
                   
                   $removeQ = CurrentQueue::where('Date',date('Y-m-d'))->where('centerId',$cenId)->where('token_svc_no',  $queueId->svc)->where('token_number', $data['TokenNo'])->delete() ; 
                   // dd($queueId) ; 
                   $updateT = TokenLog::where('id', $queueId->svclogId)
                         ->update([
                            'ststop'     => now(),
                            'service'    => DB::raw('TIMESTAMPDIFF(SECOND, scantime, NOW())'),
                            'scan'       => DB::raw('TIMESTAMPDIFF(SECOND, scantime, NOW())'),
                            'updated_at' => now()
                        ]);

             
                     $remainingFile = QueueCode::where('Date', date('Y-m-d'))
                            ->where('centerId', $cenId)
                            ->where('token', $data['TokenNo'])
                            ->where('svc', $queueId->svc)
                            ->where('status',1)
                            ->exists();

                            if (!$remainingFile) {
                                $ips = request()->getClientIps(); 
                                $lastHopIp = end($ips);
                                $update=Counter::where('center_id',$cenId)->where('ip',$lastHopIp)->update([
                                        'enCall'  =>  1, 
                                        'updated_at'=>now()
                                    ]); 
                            }   

                            $contact = $data['contact'] ;
                            $cenName =''; 
                            $cenn = Center::select('center_name')->where('id',$cenId)->first() ; 
                            if($cenn){
                                $cenName =$cenn->center_name ; 
                            }
                            $text = 'Your visa application Webfile:'.$web.' received at IVAC '.$cenName.'. To check status, please visit passtrack.net' ; 
                           
                            $Doit= SmsLog::create([
                                    'Date'=> date('Y-m-d'),
                                    'centerId'=> $cenId ,
                                    'type'=>  1,
                                    'contact'=> $contact,
                                    'text'=> $text,
                                    'lang'=> 1,
                                    'webref'=>$id ,
                                    'created_at'=>Date('Y-m-d H:i:s'),
                                    'updated_at'=>Date('Y-m-d H:i:s')
                                ]);

                            if($Doit){
                                $recId = $Doit->id; 
                                $send = SendSmsDCJob::dispatch($contact, $text, $recId );
                            }
               
                         return redirect()->route('app-receive.print', ['id' => $id]); 

                      }
                }
                else{
                     return redirect()->route('app-receive.index')->with('error', "Failed to save data");
                }

            }
           
        } catch (Exception $e) {
            $mess = $e->getMessage(); 
            // dd($mess) ; 
           return redirect()->route('app-receive.index')->with('error', 'Insert failed '.$mess);
           
        }
    }

    public function print($id)
    {
        // dd($id) ; 
        $data = AppReceive::findOrFail($id);
        $cenId = auth()->user()->centerId ; 
       if($data->centerId != $cenId){
           return view('pages.error.index', [
                        'error' => 'Cannot print other center receipt',
                    ]);  
       }
       else{
           if($data->remarks=="Manual Entry"){
                return view('pages.error.index', [
                        'error' => 'Manual Entry Cannot print receipt ',
                    ]);  
           }
           // else if($data->Date != date('Y-m-d')){
           //       return view('pages.error.index', [
           //              'error' => 'Cannot Print Old Receipt',
           //          ]);  
           // }
           // else if($data->stepId != 1){
           //       return view('pages.error.index', [
           //              'error' => 'Cannot Print Old Receipt',
           //          ]);  
           // }
           else{
                // dd($data) ; 
               $codA = QueueCode::select('random')
                        ->where('Date',date('Y-m-d'))
                        ->where('centerId',$data->centerId)
                        ->where('web',$data->Webfile)
                        ->orderBy('id','desc')->first() ; 
            
                return view('pages.AppReceive.receipt_print', [
                    'datas' =>  $data,
                    'code' => $codA->random ?? 'N/A',
                ]);
           }
       }





        // return view('app-receive.print', compact('data'));
    }


    public function updatetdd(Request $request)
    {
        $message = '' ; 
        $updatetdd = VisaTypeTdd::max('tdd_update');

        // if (!$updatetdd || date('Y-m-d', strtotime($updatetdd)) != date('Y-m-d')) {
            // Run your update logic
            try{
                $tkn = 1 ; 
                $tokens = DB::select('CALL GetDelDate(?)', [$tkn]);
                // dd($tokens) ;        
                $message = 'Updated TDD'; 

             } catch (Exception $e) {
                  $message ="error".$e->getMessage(); 
            }
        // }
        // else{
        //     $message = 'TDD Already Updated'; 
        // }

        return response()->json([
                    'found' => true,
                    'token' => $updatetdd,
                    'message' => $message,
                ]);

    }

     public function getUsersByCenter($centerId)
    {
        // Fetch users by centerId
        $users = User::where('centerId', $centerId)
            ->select('id', 'name') // keep it lightweight
            ->orderBy('name')
            ->get();

        return response()->json($users);
    }


}
