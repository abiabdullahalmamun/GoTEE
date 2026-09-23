<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAppReceiveRequest;
// use App\Http\Requests\UpdateRegionRequest;
use App\Models\Region;
use App\Models\Center;
use App\Models\Counter;
use App\Models\VisaType;
use App\Models\VisaTypeApt;
use App\Models\AptOverride;
use App\Models\StickerMap;
use App\Models\Service;
use App\Models\CurrentQueue;
use App\Models\QueueCode ; 
use App\Models\SslAptList ; 
use App\Models\NicAptList ; 
use App\Models\RejectReason ; 
use App\Models\RejectLog ; 
use App\Models\AppSteps ; 
use App\Models\AppLog ;
use App\Models\AppReceive ; 
use App\Models\SmsOtp ; 
use App\Models\SmsLog ; 
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



class  BiometricController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        // dd($request) ; 
        $perPage = $request->input('per_page', 100);
        // $search = $request->input('search');

        try {

            $ips = request()->getClientIps(); 
            $lastHopIp = end($ips);
            // dd($lastHopIp) ; 
            $cenId = auth()->user()->centerId ; 
            // dd($cenId) ; 
            $user = auth()->user()->id ; 
            if($cenId){
                $cnt = Counter::select('center_id','counter_id')->where('ip',$lastHopIp )->first() ; 
                // dd($cnt) ; 
                if($cnt){
                    if($cnt->center_id === $cenId ){
                        $count = AppLog::where('Date', date('Y-m-d'))->where('created_by', $user )->where('stepId',11)->count() ; 
                // dd($count) ; 
                        return view('pages.Biometric.index', [
                            'count' => $count,
                             'smsg' => 'Please scan webfile...',
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
                        'error' => 'Page not permitted',
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
            $reply= []; 
            $success= false ; 
            $smsg = '';
            $request->validate([
                'web' => 'required|string'
            ]);
            $cenId = auth()->user()->centerId ; 
            $web =  $request->web ; 
            $web = str_replace(' ', '', $web);
            $remarks =  '' ; 
           
            if($request->filled('rmks')){
                 $remarks = $request->rmks ; 
            }    

            if(substr($web, 0, 3)== 'BGD'){

                $region = Region::select('id')->where('region_text',substr($web, 0, 4))->orderby('id', 'desc')->first() ; 

                $record = AppReceive::select('id','centerId','regionId','contact','Webfile','passport','stepId')->where('regionId',  $region->id)->where('Webfile',$web)->first() ; 
            }
            else if(substr($web, 0, 1)== '@'){
                 $record = AppReceive::select('id','centerId','regionId','contact','Webfile','passport','stepId')->where('stickerNo',$web)->first() ; 
            }
            else{
                $record = AppReceive::select('id','centerId','regionId','contact','Webfile','passport','stepId')->where('passport',$web)->first() ; 
            }
             // $reply  = $record ; 
           if($record){
             // if($record->stepId == 1){
                if($cenId === $record->centerId){
                        // $update = AppReceive::find($record->id)->update([
                        //     'stepId'       =>  11, 
                        //     'updated_at'=>Date('Y-m-d H:i:s')
                        // ]);

                        // if($update){
                           $save = AppLog::upsert([
                                [
                                    'regionId'     => $record->regionId,
                                    'centerId'     => $record->centerId,
                                    'Date'   => date('Y-m-d'),
                                    'web_ref'     => $record->id  ,
                                    'stepId'     => 11,
                                    'remarks'     => $remarks,
                                    'created_by'=> auth()->user()->id ,
                                    'created_at'=>Date('Y-m-d H:i:s'),
                                    'updated_at'=>Date('Y-m-d H:i:s')
                                ]
                            ],
                            ['regionId' , 'web_ref', 'stepId'], // Unique constraint keys
                            ['updated_at',  'created_by']); // 

                        // }
                        if (isset($request->rmks)) {
                            ActionExceptionService::store([
                                'module'      => 'Biometric Captured',
                                'action'      => 'BioScanExcept',
                                'centerId'      =>  $record->centerId ,
                                'remarks'     =>'Webfile: '.$record->Webfile.' Passport: '.$record->passport.' Bio Scan with: '.$request->rmks,
                            ]);
                        } 


                        $smsg = "successfully Updated Record..." ; 
                        $success = true ; 
                }
                else{
                    $smsg = "Cannot Scan Biometric for Other Center" ; 
                    $success = false ; 
                }
                
              // }
              // else{
              //     $smsg = $record->stepId;
              //       if($record->stepId==1){
              //           $smsg = "passport not sent to HCI" ;
              //       }
              //       else if($record->stepId==2){
              //           $smsg = "passport not Received from HCI" ;
              //       }
              //       else if($record->stepId==4){
              //           $smsg = "passport Already at center" ;
              //       }
              //       else if($record->stepId==5){
              //           $smsg = "passport Already Delivered" ;
              //       }
              //       // dd($smsg) ; 
              //       $success = false ; 
              // }
            }
            else{
                 $smsg = "Receive Data not available" ;
                 $success = false ; 
            }

            $user =  auth()->user()->id ; 
            // $latest = AppLog::with(['webref' => function ($query) {
            // $query->select('id', 'Webfile', 'passport', 'ApplicantName', 'contact','stickerNo','visatype');
            //         }])
            //         ->where('Date', date('Y-m-d'))
            //         ->where('created_by', $user)
            //         ->where('stepId', 11)
            //         ->orderBy('id', 'desc')
            //         ->take(10)
            //         ->get();
            $latest = AppLog::with([
                        'webref' => function ($query) {
                            $query->select(
                                'id',
                                'Webfile',
                                'passport',
                                'ApplicantName',
                                'contact',
                                'stickertype',
                                'stickerNo',
                                'visatype'   // FK
                            );
                        },
                        'webref.visa' => function ($query) {
                            $query->select('id', 'visa_type'); // MUST include id
                        },
                        'webref.sticker' => function ($query) {
                            $query->select('id', 'sticker'); // MUST include id
                        }
                    ])
                    ->where('Date', date('Y-m-d'))
                    ->where('created_by', $user)
                    ->where('stepId', 11)
                    ->orderBy('id', 'desc')
                    ->take(10)
                    ->get();

            // dd($latest) ; 
           $count = AppLog::where('Date', date('Y-m-d'))->where('created_by', $user )->where('stepId',11)->count() ; 
    

            return response()->json([
                 'reply' => $reply ,
                'success' => $success ,
                'latest' => $latest,
                'count' => $count, 
                'smsg' => $smsg
            ]);

            
        } catch (Exception $e) {
            $mess = $e->getMessage(); 
            // dd($mess) ; 
             return response()->json([
                'success' => $success ,
                // 'latest' => $latest,
                // 'count' => $count, 
                'smsg' => $mess,
                  'reply' => $reply ,
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



}
