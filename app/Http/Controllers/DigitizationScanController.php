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
use App\Models\AppDigitization ; 
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


class DigitizationScanController extends Controller
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
                        $count = AppLog::where('Date', date('Y-m-d'))->where('created_by', $user )->where('stepId',4)->count() ; 
                // dd($count) ; 
                        return view('pages.DigitizationScan.index', [
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
            $regId = Center::where('id', $cenId)->pluck('region_id')->first() ;
            $web =  $request->web ; 
            $web = str_replace(' ', '', $web);
            if(substr($web, 0, 3)== 'BGD'){

                $region = Region::select('id')->where('region_text',substr($web, 0, 4))->orderby('id', 'desc')->first() ; 

                $record = AppReceive::select('id','Date','centerId','regionId','contact','Webfile','stepId')->where('regionId',  $region->id)->where('Webfile',$web)->first() ; 
            }
            else if(substr($web, 0, 1)== '@'){
                 $record = AppReceive::select('id','Date','centerId','regionId','contact','Webfile','stepId')->where('stickerNo',$web)->first() ; 
            }
            else{
                $record = AppReceive::select('id','Date','centerId','regionId','contact','Webfile','stepId')->where('passport',$web)->first() ; 
            }
             // $reply  = $record ; 
            // dd($record) ; 
           if($record){
              if (in_array($record->stepId, [4, 5])) {
   
              // if($record->stepId == 3){
                if($regId === $record->regionId){
                    AppDigitization::updateOrCreate(
                        ['webfile' => $record->Webfile,], // unique key
                        [
                            'Date' =>  date('Y-m-d'),
                            'deposite_date' =>  $record->Date,
                            'return_date'   =>  date('Y-m-d'),
                            'remarks'       =>  'File Updated' , 
                            'regionId'      =>  $record->regionId,
                            'status'        => 0, 
                            'created_by'    => auth()->user()->id ,
                            'sent2hci_date'    => $record->Date,
                             'webref'    => $record->id  ,

                        ]
                    );

                   $save = AppLog::upsert([
                        [
                            'regionId'     => $record->regionId,
                            'centerId'     => $record->centerId,
                            'Date'   => date('Y-m-d'),
                            'web_ref'     => $record->id  ,
                             'stepId'     => 8,
                            'remarks'     => 'Document Received',
                            'created_by'=> auth()->user()->id ,
                            'created_at'=>Date('Y-m-d H:i:s'),
                            'updated_at'=>Date('Y-m-d H:i:s')
                        ]
                    ],
                    ['regionId' , 'web_ref', 'stepId'], // Unique constraint keys
                    ['updated_at',  'created_by']); // 

                    $smsg = "successfully Updated Record..." ; 
                    $success = true ; 
                }
                else{
                    $smsg = "Cannot Receive Other Region Passport..." ; 
                    $success = false ; 
                }


                
              }
              else{
                  $smsg = $record->stepId;
                    if($record->stepId==1){
                        $smsg = "passport not sent to HCI" ;
                    }
                    else if($record->stepId==2){
                        $smsg = "passport not Received from HCI" ;
                    }
                    else if($record->stepId==3){
                        $smsg = "passport not at center" ;
                    }
                   
                    // dd($smsg) ; 
                    $success = false ; 
              }
            }
            else{
                 $smsg = "No Record Available..." ; 
                    $success = false ; 
            }
 
            $user =  auth()->user()->id ; 
            $latest = AppLog::with(['webref' => function ($query) {
            $query->select('id', 'Webfile', 'passport', 'ApplicantName', 'contact');
                    }])
                    ->where('Date', date('Y-m-d'))
                    ->where('created_by', $user)
                    ->where('stepId', 8)
                    ->orderBy('id', 'desc')
                    ->take(10)
                    ->get();
            // dd($latest) ; 
           $count = AppLog::where('Date', date('Y-m-d'))->where('created_by', $user )->where('stepId',8)->count() ; 
    

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

    public function xlsstore(Request $request)   //: RedirectResponse  
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
            $regId = Center::where('id', $cenId)->pluck('region_id')->first() ;
            $web =  $request->web ; 
            $web = str_replace(' ', '', $web);
            if(substr($web, 0, 3)== 'BGD'){

                $region = Region::select('id')->where('region_text',substr($web, 0, 4))->orderby('id', 'desc')->first() ; 

                $record = AppReceive::select('id','Date','centerId','regionId','contact','Webfile','stepId')->where('regionId',  $region->id)->where('Webfile',$web)->first() ; 
            }
            else if(substr($web, 0, 1)== '@'){
                 $record = AppReceive::select('id','Date','centerId','regionId','contact','Webfile','stepId')->where('stickerNo',$web)->first() ; 
            }
            else{
                $record = AppReceive::select('id','Date','centerId','regionId','contact','Webfile','stepId')->where('passport',$web)->first() ; 
            }
             // $reply  = $record ; 
            // dd($record) ; 
           if($record){
              if (in_array($record->stepId, [4, 5])) {
                    // dd($record->stepId) ; 
              // if($record->stepId == 3){
                if($regId === $record->regionId){
                    // dd($record->Webfile) ; 
//                     try {

//     $save = AppDigitization::updateOrCreate(
//         ['webfile' =>  $web],
//         [
//             'Date' => date('Y-m-d'),
//             'deposite_date' => $record->Date,
//             'return_date' => date('Y-m-d'),
//             'remarks' => 'File Updated',
//             'regionId' => $record->regionId,
//             'status' => 0,
//             'created_by' => auth()->user()->id,
//             'sent2hci_date' => $record->Date,
//             'webref' => $record->id,
//         ]
//     );

//     dd([
//         'saved_model' => $save,
//         'was_created' => $save->wasRecentlyCreated,
//         'was_changed' => $save->wasChanged(),
//     ]);

// } catch (\Exception $e) {
//     dd($e->getMessage());
// }

                    $save =  AppDigitization::updateOrCreate(
                        ['webfile' =>  $web], // unique key
                        [
                            'Date' =>  date('Y-m-d'),
                            'deposite_date' =>  $record->Date,
                            'return_date'   =>  date('Y-m-d'),
                            'remarks'       =>  'File Updated' , 
                            'regionId'      =>  $record->regionId,
                            'status'        => 0, 
                            'created_by'    => auth()->user()->id ,
                            'sent2hci_date'    => $record->Date,
                             'webref'    => $record->id  ,

                        ]
                    );
                    //  dd( $save) ; 
                   $save = AppLog::upsert([
                        [
                            'regionId'     => $record->regionId,
                            'centerId'     => $record->centerId,
                            'Date'   => date('Y-m-d'),
                            'web_ref'     => $record->id  ,
                             'stepId'     => 8,
                            'remarks'     => 'Document Received',
                            'created_by'=> auth()->user()->id ,
                            'created_at'=>Date('Y-m-d H:i:s'),
                            'updated_at'=>Date('Y-m-d H:i:s')
                        ]
                    ],
                    ['regionId' , 'web_ref', 'stepId'], // Unique constraint keys
                    ['updated_at',  'created_by']); // 

                    $smsg = "successfully Updated Record..." ; 
                    $success = true ; 
                    // dd( $success) ; 
                }
                else{
                    // dd('n') ; 
                    $smsg = "Cannot Receive Other Region Passport..." ; 
                    $success = false ; 
                }
                
              }
              else{
                  $smsg = $record->stepId;
                    if($record->stepId==1){
                        $smsg = "passport not sent to HCI" ;
                    }
                    else if($record->stepId==2){
                        $smsg = "passport not Received from HCI" ;
                    }
                    else if($record->stepId==3){
                        $smsg = "passport not at center" ;
                    }
                   
                    // dd($smsg) ; 
                    $success = false ; 
              }
            }
            else{
                 $smsg = "No Record Available..." ; 
                    $success = false ; 
            }

            return [
                'success' => $success,
                'message' => $smsg,
            ];
            
        } catch (Exception $e) {
             return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
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
