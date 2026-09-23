<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Controllers\GatewayController;
// use App\Http\Resources\BillItemResource;
// use App\Http\Resources\BillResource;

use App\Models\Counter;
use App\Models\Center;
use App\Models\Device;
use App\Models\DeviceSvc ; 
use App\Models\AptOverride;
use App\Models\VisaTypeApt;
use App\Models\SslAptList ; 
use App\Models\QueueCode ; 
use App\Models\DisplayScroll ;
use App\Models\PlayAudio ;
use App\Models\Service ;
use App\Models\TokenLog ;

use Carbon\Carbon;

use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class HardwareController extends Controller
{
    public function index(Request $request)
    {
        $userId = Auth::id();


        return $userId;
    }

    public function store(Request $request)
    {
        dd($request) ; 
    }
    public function delAudio(Request $request)
    {
        $ids =   $request->input('ids');  
        // return $ids ; 
        $del = PlayAudio::destroy($ids);
        if($del){
            return [
                'message' => 'SUCCESS',
                'ids' => $ids,
            ];     
        }
        else{
             return [
                'message' => 'FAIL',
                'ids' => $ids,
            ];   
        }
    }
    public function issueAudio(Request $request)
    {
        $reply ='' ;
        $logs = [] ;
        $cenId = $request->input('center');
        $svcId = $request->input('svc');
        $logs = PlayAudio::select('id','token_number','token_counter')->where('Date',date('Y-m-d'))->where('centerId',$cenId)->where('svc_no',$svcId)->orderby('id', 'asc')->limit(20)->get() ; 
        if($logs){
            $reply = 'SUCCESS' ; 
        }
        else{
             $reply = 'NO DATA' ; 
        }

        return [
            'message' => $reply,
            'logs' => $logs,
        ];
    }
    public function issueDisplay(Request $request)
    {
       // dd($request->all())  ; 
        $reply = '' ; 
        $logs = [] ;
        $mac = $request->input('payload.mac');
        $type = $request->input('payload.type');
        $ip = $request->input('payload.ip');

        // dd($mac) ; 

        $dev = Device::where('mac',$mac)->where('devType',$type)->first() ; 
        // dd($dev) ; 
        if($dev){
            $is_update =  Device::where('mac',$mac)->update([
                'ip'   => $ip, 
                'lastCom'=>Date('Y-m-d H:i:s')
            ]);

            $cenId = $dev->centerId ; 
            $ids = $dev->id ; 
            // return $ids.'-'.$cenId ; 
            $svcData = DeviceSvc::select('svcId','rowcount')
                ->where('devId', $ids)
                ->where('centerId', $cenId)
                ->get()
                ->toArray(); // convert collection to array

            $svcIds = array_column($svcData, 'svcId');
            $rowcn  = array_column($svcData, 'rowcount');

            // Get service names in same order
            $svcName = Service::whereIn('id', $svcIds)->pluck('service_name')->toArray();

            $logs = []; // initialize logs array

            for ($i = 0; $i < count($svcIds); $i++) {
                $list = DisplayScroll::select('tokenno','counterNo')
                    ->where('Date', date('Y-m-d'))
                    ->where('centerId', $cenId)
                    ->where('svc_no', $svcIds[$i])   // <-- fixed here
                    ->orderBy('id','desc')
                    ->limit($rowcn[$i])
                    ->get();

                $logs[] = [
                    'svcName' => $svcName[$i],
                    'list'    => $list
                ];
            }

           // $svcData = DeviceSvc::select('svcId','rowcount' )->where('devId', $ids)->where('centerId',$cenId)->get() ;
           // $ddta  = json_decode($svcData, true);
           // $svcIds = array_column($ddta, 'svcId');
           // $rowcn = array_column($ddta, 'rowcount');
           // // return $svcIds ; 
           //  $svcName = Service::whereIn('id', $svcIds)->pluck('service_name')->toArray();
           //  // return $svcIds;
           //  for($i=0; $i< count($svcIds) ; $i++){
               
           //      // dd($svcIds[$i]) ; 
           //      $list = DisplayScroll::select('tokenno','counterNo')->where('Date', date('Y-m-d'))->where('centerId',$cenId)->whereIn('svc_no',$svcIds[$i])->orderby('id','desc')->limit(5)->get() ; 
           //      // dd($list) ; 
           //      array_push($logs, ['svcName'=> $svcName[$i],'list'=> $list, ]) ; 
           //  }

             $reply ='SUCCESS' ; 
        }
        else{
            $reply ='Invalid Device' ; 
        }

        return [
            'message' => $reply,
            'logs' => $logs,
        ];
    }
    public function issueToken(Request $request)
    {
        // dd($request) ; 
        $webOK = array();
        $v_type =array();
        $ac_visa = array(); 
        $txnId = array() ;
        $webINVALID ='';
        $webVALID='';
        $svcname = '';
        $tokenNo = '';
        $waitTime = '';

        try{
 
        $cc=0 ; 
        $mac = $request->input('payload.mac');
        $type = $request->input('payload.type');
        $webfiles =  $request->input('payload.webfileList');
        $center = ''; 
         $centerName = ''; 
         $reply = '';   

        $rand = ''; 
        $dev = Device::where('mac',$mac)->first() ; 
        // dd($dev) ; 

        if($dev){
            $cenId = $dev->centerId ; 
            $devtype = $dev->devType ; 
            // $devd = $dev->id
            $devd = substr(sprintf('%02d', $dev->id), -2);
            $rand = rand(1001,9999).date("His").$devd; 
         
            $cen = Center::select('center_name','starthr','endhr','gtw_name','apt_tol','end_tol')->where('id', $cenId)->first() ;
            // dd($cen) ; 
            $center = $cen->gtw_name ; 
            $tolerance = $cen->apt_tol  ?? 0;
            $end_tolerance = $cen->end_tol  ?? 0;
            $centerName = $cen->center_name ; 
            if($devtype=="1"){
                for($i=0;$i<count($webfiles);$i++)
                {
                    $wf_no  =  $webfiles[$i] ; 
                    $exists = QueueCode::where('web', $wf_no)
                                        ->where('Date',date('Y-m-d'))
                                        ->exists();
                    if($exists){
                        if($webINVALID=='')
                        {
                            $webINVALID  =$wf_no.'(CODE EXISTS)' ;   
                        }
                        else
                        {
                            $webINVALID  = $webINVALID.','.$wf_no.'(CODE EXISTS)' ;
                        }
                        continue;
                    }

                      $checkApt = "NO" ; 
                    // dd($webfiles[$i]) ; 
                      $webfileNo ='';
                      $Aptdate_org='';
                      $Aptdate_res = '';
                      $Apthour ='' ;
                      $paystatus='';
                      $check_user='';
                      $checked_on='';
                      $order_id = '';
                      $amount='';
                      $visaType='';
                      $ivac_name='';
                      $txn_date='';
                    $act_visa_type = '';
                    $passportApt='';
                    $data = array(
                         "web"    =>  $wf_no ,  //  "2024-05-30" ,  // 
                    );
                    // dd($data) ; 
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
                          $act_visa_type = $reply['data']['actual_visa_type'];
                          $apt_start = $reply['data']['apt_start'];
                          $apt_end = $reply['data']['apt_end'];
                          $passportApt= $reply['data']['passport_no'];
                          $Apthour= null ;     // dd()
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
                                    'actVisa' => $act_visa_type,
                                    'passport' => $passportApt,
                                    'center' => $ivac_name,
                                    'txn_date' => $txn_date,
                                    'apt_start' => $apt_start,
                                    'apt_end' => $apt_end,
                                    'status' => 1,
                                    'created_at' => now(),
                                    'updated_at' => now(),
                                ]
                            ],
                            ['center', 'WebFile_no'], // Unique constraint keys
                            ['prev_date', 'curr_date', 'apt_hr', 'paystatus', 'checked_user','checked_on','txnId','amount','visatype','txn_date','updated_at','actVisa','passport']); // 

                          // dd($save) ; 
                            // dd( $paystatus) ; 
                           if($center!=$ivac_name){
                             if($webINVALID=='')
                             {
                                $webINVALID  =$wf_no.'(INVALID IVAC)' ;   
                             }
                             else
                             {
                                $webINVALID  = $webINVALID.','.$wf_no.'(INVALID IVAC)' ;
                             }
                          }
                          else{
                            if($paystatus === 'Yes'){
                                // dd($checked_on) ; 
                                // $checked_on ='' ;  //for test
                                // dd('ok') ; 
                                if($checked_on!=''){
                                    if($webINVALID=='')
                                    {
                                      $webINVALID  =$wf_no.'(Online Payment Already Checked)' ;   
                                    }
                                    else
                                    {
                                       $webINVALID  = $webINVALID.','.$wf_no.'(Online Payment Already Checked)' ;   
                                    } 
                                }
                                else{
                                    // dd($tolerance) ; 
                                     $hr = date("H") ;
                                     $mm = date('i') ;
                                     $mintt = 60 - $tolerance ;   
                                     // dd($mintt) ; 
                                     $Apthour = $hr ;  
                                     $date = date('Y-m-d') ; 
                                     // dd($Aptdate_res.'-'.$date) ; 
                                     // dd($Apthour ) ;
                                     $date_res = date('Y-m-d', strtotime($Aptdate_res));
                                     $date_org = date('Y-m-d', strtotime($Aptdate_org));

                                      // $date_res =  $date ; //test
                                     // dd($date_org.'---'.$date_res) ; 
                                     if($date_org==$date  || $date_res==$date){
                                        // dd('ok') ; 
                                        // dd($tolerance) ; 
                                        // dd($Apthour) ; 
                                        if(!$apt_start){
                                            $apt_start = date('H:i:s') ; 
                                        }
                                        if(!$apt_end){
                                            $apt_end = date('H:i:s') ; 
                                        }

                                // $apt_start = Carbon::now();    
                                // $apt_end  =  Carbon::now();  
                                        //uncomment these two for test
                                $now = Carbon::now();
                                // $now = Carbon::parse('2026-07-01 19:15:01');
                            $start = Carbon::parse($apt_start)->subMinutes($tolerance);
                            $end = Carbon::parse($apt_end)->addMinutes($end_tolerance);
                              
                                if ($now->between($start, $end)) {
                                    if($webVALID=='')
                                    {
                                      $webVALID  =$wf_no ;   
                                    }
                                    else
                                    {
                                       $webVALID  = $webVALID.','.$wf_no ;   
                                    }

                                    array_push($webOK,  $wf_no  );
                                    array_push($v_type,  $visaType);
                                    array_push($ac_visa, $act_visa_type); 
                                    array_push($txnId,  $order_id) ;
                                    $checkApt = "NO" ; 
                                    $cc = $cc+1;
                                } else {
                                    if($webINVALID=='')
                                    {
                                      $webINVALID  =$wf_no.'(Invalid Time:'.$apt_start.'-'.$apt_end.')' ;   
                                    }
                                    else
                                    {
                                       $webINVALID  = $webINVALID.','.$wf_no.'(Invalid Time:'.$apt_start.'-'.$apt_end.')' ;   
                                    }
                                     $checkApt = "YES" ; 
                                }

                                        if($act_visa_type=='FAMILY VISA' ){
                                            $webVALID =  $webVALID.'(FAMILY)' ; 
                                        }
                                     }
                                     else{
                                        $checkApt = "YES" ; 
                                     }
                                }
                                // dd('d1') ; 
                            }
                            else{
                                $checkApt = "YES" ; 
                            }

                          }
                            // dd('d2') ; 
                        }
                        else{
                            $checkApt = "YES" ; 
                        }
                    }
                    else{
                        $checkApt = "YES" ; 
                    }

                    // dd($webINVALID) ; 
                    // dd($checkApt) ; 
                    // dd($txnId );

// dd($v_type) ; 
                    $visatypeName = '';
                    if( $checkApt == "YES"){
                        // dd($wf_no, $cenId) ; 
                         $aptOver = AptOverride::select('visatypeId')->where('WebFile_no',$wf_no)->where('Date', date('Y-m-d'))->where('centerId',$cenId )->where('active',1)->first();    
                         // dd($aptOver)  ; 

                         if($aptOver){

                            $vis =  VisaTypeApt::select('visa_type')->where('id',$aptOver->visatypeId)->first() ; 
                            if($vis){
                                if($visatypeName==''){
                                     $visatypeName = $vis->visa_type ;
                                }
                            }    

                            // dd($visatypeName) ; 
                            if($webVALID=='')
                            {
                              $webVALID  =$wf_no ;   
                            }
                            else
                            {
                               $webVALID  = $webVALID.','.$wf_no ;   
                            }
                            array_push($webOK,  $wf_no  );
                            array_push($v_type , $visatypeName );
                            $cc = $cc+1;

                            if (strpos($webINVALID, 'BGDRV0C83026') !== false) {
                                $webINVALID = '';
                            }

                            // dd($webINVALID);
                         } 
                         else{
                            if($webINVALID=='')
                            {   
                                $webINVALID  =$wf_no.'(Invalid Appointment)' ;
                            }
                            else
                            {
                                $webINVALID  = $webINVALID.','.$wf_no.'(Invalid Appointment)' ;
                            }
                         }
                    }

                }
                // dd($v_type, $ac_visa) ; 
                // dd($webINVALID) ; 
               //visatype ignored on FEB19_26. BACKUP available on the same date.
                $v_type = array_map('stripslashes', $v_type);
                $ac_visa = array_map('stripslashes', $ac_visa);
                // dd($v_type, $ac_visa) ; 
                if(count($v_type)>0){
                    $unique = array_unique($v_type);
                    // $unique = array_unique($txnId);
               
                    // dd($unique) ; 
                    if (count($unique) === 1) {
                        // dd($txnId[0]) ; 
                        $svcType = VisaTypeApt::select('svcId')->where('visa_type',$v_type[0])->first() ; 
                        // dd($svcType) ; 
                        $svc = $svcType->svcId ; 
                        // dd($svc) ; 
                        $qty = count($v_type); 
                        $center =  $cenId ; 
                        $svcIds = '';
                        // dd($center) ; 
                        $tokens = DB::select('CALL issueToken(?,?,?)', [$svc, $qty, $center]);
                        // dd($tokens) ; 
                        if (!empty($tokens)) {

                            $tokenData = $tokens[0]; 
                            $svcname = $tokenData->svcname;
                            $tokenNo = $tokenData->TKN;
                            $waitTime = $tokenData->wTime;
                            $svcIds = $tokenData->idc;
                            // dd($webOK) ; 
                             $reply  ='SUCCESS' ; 

                            for($j=0;$j<count($webOK);$j++)
                            {
                                 $save = QueueCode::upsert([
                                    [
                                        'Date' => now(), 
                                        'centerId' =>  $cenId,
                                        'token' => $tokenNo,
                                        'svc' =>  $svc ,
                                        'web' => $webOK[$j],
                                        'random' => $rand,
                                        'mac' => $mac,
                                        'status' => 1,
                                        'created_at' => now(),
                                        'updated_at' => now(),
                                        'svclogId' =>  $svcIds,
                                        'txnId' => $order_id,
                                    ]
                                ],
                                ['Date', 'centerId','web','random'], // Unique constraint keys
                                ['token', 'svc', 'random', 'status', 'mac','updated_at']); // 
                                 // dd($save) ;
                            }

                        }
                        else{
                            $reply  = 'Failed to issue token' ; 
                        }
                        // }    // commented May 5, 26
                    }
                    else if(count($unique) === 2){
                        $v_type = array_unique($v_type);
                        $ac_visa = array_unique($ac_visa);

                        sort($v_type);
                        sort($ac_visa);
                        // dd($v_type, $ac_visa) ; 
                        $validPairs = [
                                        [
                                            'v_type' => [
                                                'MEDICAL/MEDICAL ATTENDANT VISA',
                                                'OTHERS VISA'
                                            ],
                                            'ac_visa' => [
                                                'MISCELLANEOUS VISA',
                                                'MEDICAL/MEDICAL ATTENDANT VISA'
                                            ]
                                        ],
                                        [
                                            'v_type' => [
                                                'MEDICAL/MEDICAL ATTENDANT VISA',
                                                'OTHERS VISA'
                                            ],
                                            'ac_visa' => [
                                                'FAMILY VISA',
                                                'MEDICAL/MEDICAL ATTENDANT VISA'
                                            ]
                                        ],
                                        [
                                            'v_type' => [
                                                'BUSINESS VISA',
                                                'OTHERS VISA'
                                            ],
                                            'ac_visa' => [
                                                'BUSINESS VISA',
                                                'FAMILY VISA'
                                            ]
                                        ]
                                    ];


                        $matched = false;

                        foreach ($validPairs as $pair) {
                            sort($pair['v_type']);
                            sort($pair['ac_visa']);

                            if ($v_type === $pair['v_type'] && $ac_visa === $pair['ac_visa']) {
                                $matched = true;
                                break;
                            }
                        }

                        // dd($matched) ; 
                        // if(collect($v_type)->contains(fn($item) => str_contains($item, 'MEDICAL/MEDICAL ATTENDANT VISA')) &&  collect($ac_visa)->contains(fn($item) => trim($item) === 'ENTRY VISA')  )
                        if ($matched) {
                            // dd('match') ; 
                            $visaaa = collect($v_type)
                                        ->first(fn ($item) => $item !== 'OTHERS VISA');
                            $svcType = VisaTypeApt::select('svcId')->where('visa_type',$visaaa)->first() ; 
                            // dd($svcType) ; 
                            $svc = $svcType->svcId ; 
                            // dd($svc) ; 
                            $qty = count($v_type); 
                            $center =  $cenId ; 
                            $svcIds = '';
                            // dd($center) ; 
                            $tokens = DB::select('CALL issueToken(?,?,?)', [$svc, $qty, $center]);
                            // dd($tokens) ; 
                            if (!empty($tokens)) {

                                $tokenData = $tokens[0]; 
                                $svcname = $tokenData->svcname;
                                $tokenNo = $tokenData->TKN;
                                $waitTime = $tokenData->wTime;
                                $svcIds = $tokenData->idc;
                                // dd($webOK) ; 
                                 $reply  ='SUCCESS' ; 

                                for($j=0;$j<count($webOK);$j++)
                                {
                                     $save = QueueCode::upsert([
                                        [
                                            'Date' => now(), 
                                            'centerId' =>  $cenId,
                                            'token' => $tokenNo,
                                            'svc' =>  $svc ,
                                            'web' => $webOK[$j],
                                            'random' => $rand,
                                            'mac' => $mac,
                                            'status' => 1,
                                            'created_at' => now(),
                                            'updated_at' => now(),
                                            'svclogId' =>  $svcIds,
                                            'txnId' => $order_id,
                                        ]
                                    ],
                                    ['Date', 'centerId','web','random'], // Unique constraint keys
                                    ['token', 'svc', 'random', 'status', 'mac','updated_at']); // 
                                     // dd($save) ;
                                }

                            }
                            else{
                                $reply  = 'Failed to issue token' ; 
                            }

                        }
                        else{
                             $reply = 'Cannot mix visatype/Application' ;
                             // dd($reply) ;  
                        }

                    } 
                    else {
                        $reply = 'Cannot mix visatype/Application' ; 
                    }
                }
                else{
                    $reply = 'No Valid Webfile' ; 
                }
            }
            else{
               $reply = 'Invalid DeviceType' ; 
            }
        }
        else{
            $reply = 'Device Not Registered' ; 
        }
// dd($reply) ; 
      
       return [
            'message' => $reply,
            'center' => $centerName,
            'TIME' =>  date('Y-m-d H:i:s'),
            'NAME' =>  $svcname ,
            'maxtoken' =>  $tokenNo,
            'PT' => $waitTime ,
            'CODE' =>  $rand ,
            'VALID' => $webVALID ,
            'INVALID' => $webINVALID ,
        ];

      } 
       catch(Exception $e){
             $mess = $e->getMessage(); 
             return response()->json([
                'message' => 'Token request received',
                 'mess'  => $mess,
            ]);
            // return $data ;
       }


    }
    public function syncDev(Request $request)
    {
        // dd($request) ; 
        $center = '';
        $mac = $request->input('payload.mac');
        $ip = $request->input('payload.ip');
        $host = $request->input('payload.host');
        $status = $request->input('payload.status');
        // dd($mac) ; 
        $dev = Device::where('mac',$mac)->first() ; 

        if($dev){
                    // dd($val) ; 
            $cenId = $dev->centerId ; 
            $devtype = $dev->devType ; 
            $reply = '';
            $cen = Center::select('center_name','starthr','endhr')->where('id', $cenId)->first() ;
            // dd($cen) ; 
           $center = $cen->center_name ; 
            if($devtype=="1"){
                // dd('ok') ; 
                $c_time =  date("H:i:s");

                // dd($c_time) ; 
                if($c_time>date('H:i:s', strtotime($cen->starthr)) && $c_time<date('H:i:s', strtotime($cen->endhr)))
                {
                   $reply = 'Service OK';
                }
                else
                {
                    $reply='Service Closed';
                }

            }
            else{
               $reply = 'Invalid DeviceType' ; 
            }
            // $center =  $val->center_id ; 
            // $counterNo =  $val->counter_id ; 
            // dd($center) ; 
           $is_update =  Device::where('mac',$mac)->update([
                'ip'   => $ip, 
                'lastCom'=>Date('Y-m-d H:i:s')
            ]);

        }
        else{
            $reply ='Device Not Registered' ; 
        }

       // dd($is_update) ; 
              // dd($encryptedKey); 
       return [
            'message' => $reply,
            'center' => $center,
           
        ];

    }

}
