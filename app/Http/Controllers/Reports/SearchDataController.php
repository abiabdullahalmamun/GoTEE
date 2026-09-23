<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;
use App\Http\Controllers\GatewayController;
use App\Http\Controllers\PasstrackController ;
// use App\Models\BillMaster;
// use App\Models\Room;
// use App\Models\RoomBook;

use App\Models\User;
// use App\Models\Shop;
use App\Models\NicAptList ; 
use App\Models\AppLog ;
use App\Models\AppReceive ; 
use App\Models\FormFill ; 
use App\Models\ForeignPass ; 
use App\Models\RejectLog ; 

use App\Models\Region;
use App\Models\SmsLog ; 
use App\Models\SslAptList ; 

use App\Models\TransactionLog;

// use App\Services\Reports\BillingReportService;
use App\Services\Reports\TransactionReportService;
// use App\Services\Reports\SearchReportService;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class SearchDataController extends Controller
{

    public function __construct(protected TransactionReportService $TransactionReportService) {}
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
         // dd($request->all());
        try {
      
            $shop =[] ; 
            return view('reports.search_report.index', [
                    'shop' => $shop
                ]  );

        } catch (Exception $e) {
            info('Error showing Billing Report!', [$e]);

            return redirect()->back()->with('error', 'Billing Report showing failed!');
        }
    }

    // public function searchContact(Request $request): View|RedirectResponse
    // {
    //     dd($request->all()) ; 
    // }
    // public function searchWebfile(Request $request): View|RedirectResponse
   
    public function search(Request $request): View|RedirectResponse
    {
        // dd($request->all()) ;
        $pgreply = [] ;
        $dataa = [] ;
        $log = [] ;
        $apt = [] ; 
        $smsD = [] ; 
        $formdata = []; 
         $fpdata = [] ; 
         $reject = [] ; 
         $nic=[] ; 
        if (is_null($request['searchTx1'])) {
            return view('reports.search_report.search_web', [
                 'query' => $dataa,
                 'logs' => $log,
                 'smsD' => $smsD,
                 'apt' => $apt,
            ]  );
            // null, empty string, 0, false, or []
        }


        $message ='';
        $val = $request['searchTx1'] ; 
        // dd($val) ; 
        $param = [] ;           

        $cenId = auth()->user()->centerId ; 
        $role  = auth()->user()->role_id ; 
        if(substr($val, 0,3) =='BGD'){
            $re = substr($val, 0,4) ; 
            // dd($re) ; 
            $reg = Region::select('id')->where('region_text',$re)->first();
            if($reg){
                // $records = AppReceive::where('regionId',$reg->id)->where('Webfile',$val)->get();  
                $records = AppReceive::where('Webfile',$val)->get();  
                $param   = $records->pluck('id')->toArray();
                $dataa   = $records->sortByDesc('Date')->first();
            }
            else{
                $message ='Invalid webfile';
            }
            $formdata =  FormFill::where('webfile',$val)->orderBy('id','desc')->get() ;
            $nic = NicAptList::where('webfile',$val)->orderBy('id','desc')->get(); 
        }
        else if(substr($val, 0,1) =='s'){
            $wf_no = substr($val, 1);
            $data = array(
                         "web"    =>  $wf_no ,  //  "2024-05-30" ,  // 
                    );
            $GatewayController = new GatewayController();
            $reply = $GatewayController->checkApt($data)  ;  
            // dd($reply) ;

            if($reply){
                if($reply['code']==200){
                    $apt = (object) [
                        'WebFile_no' =>  $reply['data']['webfile_no'],
                        'prev_date' => $reply['data']['previous_appointment_date'],
                        'curr_date' =>  $reply['data']['current_appointment_date'],
                        'apt_hr' => $reply['data']['appointment_hour'],
                        'paystatus' =>$reply['data']['status'],
                        'checked_user' => $reply['data']['user_id'],
                        'checked_on' => $reply['data']['checked_on'],
                        'txnId' => $reply['data']['order_id'],
                        'txn_date' => $reply['data']['trans_date'],
                        'amount' => $reply['data']['amount'],
                        'center' => $reply['data']['ivac_name'],
                        'visatype' => $reply['data']['visa_type'],
                        'passport'=> $reply['data']['passport_no'] ,
                    ];
               }
          }
        }
        else if(substr($val, 0,1) =='*'){
            $wf_no = substr($val, 1);
            $data = array(
                         "web"    =>  $wf_no ,  //  "2024-05-30" ,  // 
                    );
            $GatewayController = new GatewayController();
            $pgreply = $GatewayController->checkApt($data)  ;  
             // dump($reply) ;
        }
        else if(substr($val, 0,1) =='0'){
            $records = AppReceive::where('contact', $val)->get();   
            $param   = $records->pluck('id')->toArray();
            $dataa   = $records->sortByDesc('Date')->first();
        }
        else if(substr($val, 0,1) =='@'){
            $records = AppReceive::where('stickerNo', $val)->get();   
            $param   = $records->pluck('id')->toArray();
            $dataa   = $records->sortByDesc('Date')->first();
        }
        else{
            $val = str_replace(' ', '', $val);
            $records = AppReceive::where('passport', $val)->get();
            $param   = $records->pluck('id')->toArray();
            $dataa   = $records->sortByDesc('Date')->first();
            $formdata =  FormFill::where('passport',$val)->orderBy('id','desc')->get() ;
            $nic = NicAptList::where('passport',$val)->orderBy('id','desc')->get();         
        }
        // dd($param) ; 
       if($dataa){
            // if($role==1 || $role==5){
               if($role==10 ){
                    $log = AppLog::with('webref')->whereIn('web_ref',$param)->whereNotIn('stepId',[31,32,33])->orderBy('created_at','desc')->get() ;
               }
               else{
                    $log = AppLog::with('webref')->whereIn('web_ref',$param)->orderBy('created_at','desc')->get() ;
               }     
                $smsD = SmsLog::where('webref',$dataa->id)->orderBy('id','desc')->get() ; 
               // dd($dataa->Webfile)  ; 
                $apt = SslAptList::where('WebFile_no',$dataa->Webfile)->orderBy('id','desc')->first() ; 

                if ($dataa && $dataa->remarks === 'FOREIGN') {
                    $fpdata = ForeignPass::where('web_ref', $dataa->id)->first();
                }

            // else{
            //     if($dataa->centerId!=$cenId)
            //     {
            //         $dataa=[] ;
            //         $smsD = [] ;
            //         $apt = [] ; 
            //     }
            // }
        }
        else{

            $reject = RejectLog::with(['center', 'user' ,'rejectReasons'  ])->where('Webfile',$val)->first() ;  
            // dd($reject) ; 
            if($reject){
                $reject = $reject ; 
            }
            else{
                $value = array(
                         "val" => $val ,  //  "2024-05-30" ,  // 
                    );
                $PasstrackController = new PasstrackController();
                $reply = $PasstrackController->getPassTrackData($value)  ; 
                // dd($reply) ; 
                if($reply !=''){
                    $re = substr($reply, 0,4) ; 
                    // dd($re) ; 
                    $reg = Region::select('id')->where('region_text',$re)->first();
                    if($reg){
                        // $dataa = AppReceive::where('regionId',$reg->id)->where('Webfile',$reply)->orderBy('Date','desc')->first();
                        $dataa = AppReceive::where('Webfile',$reply)->orderBy('Date','desc')->first();
                       
                        // dd($dataa) ; 
                        if($dataa){
                            // if($role==1 || $role==5){

                                $log = AppLog::where('web_ref',$dataa->id)->orderBy('created_at','desc')->get() ;
                                $smsD = SmsLog::where('webref',$dataa->id)->orderBy('created_at','desc')->get() ; 
                               // dd($dataa->Webfile)  ; 
                                $apt = SslAptList::where('WebFile_no',$dataa->Webfile)->first() ; 
                            // }
                            // else{
                            //     if($dataa->centerId!=$cenId)
                            //     {
                            //         $dataa=[] ;
                            //         $smsD = [] ;
                            //         $apt = [] ; 
                            //     }
                            // }
                        }
                    }
                    
                }
            }


        }
        // dd($fpdata) ; 

        return view('reports.search_report.search_web', [
             'query' => $dataa,
             'logs' => $log,
             'smsD' => $smsD,
             'apt' => $apt,
             'role' => $role ,
            'formdata' =>  $formdata , 
             'fpdata' => $fpdata ,
             'reject' =>$reject , 
             'pgreply' => $pgreply ,
              'nicData' => $nic ,
        ]  );

    }


}
