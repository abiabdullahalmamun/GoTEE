<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAppReceiveRequest;
// use App\Http\Requests\UpdateRegionRequest;
use App\Models\Region;
use App\Models\Center;
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
use App\Jobs\SendSmsJob;
// use App\Imports\WebFileImport; 
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;


class  Reject4mHCIController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        // dd($request) ; 
        $perPage = $request->input('per_page', 10);
        $search = $request->input('search');

        try {
            $cenId = auth()->user()->centerId ; 
            // dd($cenId) ; 
            $user = auth()->user()->id ; 
            if($cenId){
                $count = AppLog::where('Date', date('Y-m-d'))->where('created_by', $user )->where('stepId',32)->count() ; 

              $latest = AppLog::with(['webref' => function ($query) {
                $query->select('id', 'Webfile', 'passport', 'ApplicantName', 'contact');
                    }])
                    ->where('Date', date('Y-m-d'))
                    ->where('created_by', $user)
                    ->where('stepId', 32)
                    ->orderBy('id', 'desc')
                    ->take(20)
                    ->get();
                // dd($count) ; 
              return view('pages.Reject4mHci.index', [
                    'latest' => $latest,    
                    'count' => $count,
                     'smsg' => 'Please scan webfile...',
                ]);
            }
            else{

                 return view('pages.error.index', [
                        'error' => 'Page not permitted',
                    ]);
            }
         
     
        } catch (Exception $e) {
             // dd($e->getMessage()); 
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function getSend2hciList($centerId)
    {
        // dd($centerId) ;

        $dataList = AppReceive::select('Webfile','id')->where('centerId',$centerId)->where('stepId',1)->orderBy('Webfile','asc')->get() ; 

        // Return JSON
        return response()->json($dataList);
    }

   
 

    /**
     * Store a newly created resource in storage.
     */ 
    public function store(Request $request)   //: RedirectResponse  
    {    //: RedirectResponse    //StoreAppReceiveRequest
       // dd($request->all());
        try {
            $reply=[] ; 
            $success= false ; 
            $smsg = '';
            $request->validate([
                'web' => 'required|string'
            ]);

            $web =  $request->web ; 
            $web = str_replace(' ', '', $web);
            
            if(substr($web, 0, 3)== 'BGD'){

                $region = Region::select('id')->where('region_text',substr($web, 0, 4))->orderby('id', 'desc')->first() ; 

                $record = AppReceive::select('id','centerId','regionId','stepId')->where('regionId',  $region->id)->where('Webfile',$web)->first() ; 
            }
            else if(substr($web, 0, 1)== '@'){
                 $record = AppReceive::select('id','centerId','regionId','stepId')->where('stickerNo',$web)->first() ; 
            }
            else{
                $record = AppReceive::select('id','centerId','regionId','stepId')->where('passport',$web)->first() ; 
            }

           if($record){
                if($record->stepId == 2){
                    $update = AppReceive::find($record->id)->update([
                        'stepId'       =>  3, 
                        'updated_at'=>Date('Y-m-d H:i:s')
                    ]);

                    if($update){
                       $save = AppLog::upsert([
                            [
                                'regionId'     => $record->regionId,
                                'centerId'     => $record->centerId,
                                'Date'   => date('Y-m-d'),
                                'web_ref'     => $record->id  ,
                                'stepId'     => 32,
                                'remarks'     => '',
                                'created_by'=> auth()->user()->id ,
                                'created_at'=>Date('Y-m-d H:i:s'),
                                'updated_at'=>Date('Y-m-d H:i:s')
                            ]
                        ],
                        ['regionId' , 'web_ref', 'stepId'], // Unique constraint keys
                        ['updated_at',  'created_by']); // 

                    }

                    $smsg = "Updated Record..." ; 
                    
                    $success = true ; 
                 }
                 else{
                      $smsg = $record->stepId;
                        if($record->stepId==1){
                            $smsg = "passport not sent to HCI" ;
                        }
                        else if($record->stepId==3){
                            $smsg = "passport Already received from HCI" ;
                        }
                        else if($record->stepId==4){
                            $smsg = "passport ready at center" ;
                        }
                        else if($record->stepId==5){
                            $smsg = "passport Already Delivered" ;
                        }
                        // dd($smsg) ; 
                        $success = false ; 
                 }

            }
            else{
                $value = array(
                     "val" =>  $web ,  //  "2024-05-30" ,  // 
                );
                $PasstrackController = new PasstrackController();
                $reply = $PasstrackController->getPassTrackData($value)  ; 

                if($reply !=''){
                    $region = Region::select('id')->where('region_text',substr($reply, 0, 4))->orderby('id', 'desc')->first() ; 
                    // $reply = $region ; 
                    $record = AppReceive::select('id','centerId','regionId','stepId')->where('regionId',  $region->id)->where('Webfile',$reply)->first() ;  
                     if($record){
                        if($record->stepId == 2){
                            $update = AppReceive::find($record->id)->update([
                                'stepId'       =>  3, 
                                'updated_at'=>Date('Y-m-d H:i:s')
                            ]);

                            if($update){
                               $save = AppLog::upsert([
                                    [
                                        'regionId'     => $record->regionId,
                                        'centerId'     => $record->centerId,
                                        'Date'   => date('Y-m-d'),
                                        'web_ref'     => $record->id  ,
                                        'stepId'     => 32,
                                        'remarks'     => '',
                                        'created_by'=> auth()->user()->id ,
                                        'created_at'=>Date('Y-m-d H:i:s'),
                                        'updated_at'=>Date('Y-m-d H:i:s')
                                    ]
                                ],
                                ['regionId' , 'web_ref', 'stepId'], // Unique constraint keys
                                ['updated_at',  'created_by']); // 

                            }

                            $smsg = "Updated Record from PassTrack Server" ; 
                            $success = true ; 
                     }
                       else{
                              $smsg = $record->stepId;
                            if($record->stepId==1){
                                $smsg = "passport not sent to HCI" ;
                            }
                            else if($record->stepId==3){
                                $smsg = "passport Already received from HCI" ;
                            }
                            else if($record->stepId==4){
                                $smsg = "passport ready at center" ;
                            }
                            else if($record->stepId==5){
                                $smsg = "passport Already Delivered" ;
                            }
                            // dd($smsg) ; 
                            $success = false ; 
                       }

                    }
                    else{
                         $smsg = "No data avaiable in PassTrack" ; 
                        $success = false ; 
                    }   
                }
                else{
                    $smsg = "Application not available in HCI/AHCI" ; 
                    $success = false ; 
                }
            }
            $user =  auth()->user()->id ; 
            $latest = AppLog::with(['webref' => function ($query) {
                    $query->select('id', 'Webfile', 'passport', 'ApplicantName', 'contact', 'stepId', 'created_at', 'visatype')
                          ->with('visa:id,visa_type'); 
                    }])
                    ->where('Date', date('Y-m-d'))
                    ->where('created_by', $user)
                    ->where('stepId', 32)
                    ->orderBy('id', 'desc')
                    ->take(20)
                    ->get();
            // dd($latest) ; 
           $count = AppLog::where('Date', date('Y-m-d'))->where('created_by', $user )->where('stepId',32)->count() ; 
    

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
                    'stepId'       =>  2, 
                    'updated_at'=>Date('Y-m-d H:i:s')
                ]);

       $user =  auth()->user()->id ; 

        $latest = AppLog::with(['webref' => function ($query) {
                    $query->select('id', 'Webfile', 'passport', 'ApplicantName', 'contact');
                }])
                ->where('Date', date('Y-m-d'))
                ->where('created_by', $user)
                ->where('stepId', 32)
                ->orderBy('id', 'desc')
                ->take(20)
                ->get();
        $count = AppLog::where('Date', date('Y-m-d'))->where('created_by', $user )->where('stepId',32)->count() ; 
     
        return response()->json([
                         'success' => true,
                        'message' => "Data Deleted successfully", 
                        'count' => $count, 
                        'latest' => $latest,
                    ]);
    }


//     public function destroy($id) 
//     {
//          dd($id);
//         try {
//             $region = Region::find($region->id);  
//             if ($region) {
//                 $region->delete(); // Fires 'deleted' event → Loggable works
//                 return redirect()->route('regions.index')->with('success', 'Region deleted successfully.');
//             }

//             return redirect()->route('regions.index')->with('error', 'Region not found.');

//         } catch (\Exception $e) {
//              $msss = $e->getMessage() ;
//              if (str_contains($msss, 'a foreign key constraint fails')) {
//                  return redirect()->route('regions.index')->with('error', 'a foreign key constraint fails.');
//             }
//             else{
//                 // dd($e->getMessage()); 
//                 return redirect()->route('regions.index')->with('error','Region Delete failed!.'.$msss);
//             }


//             info('Region deleted failed!', [$e]);
// // dd($e->getMessage()); 
//             return redirect()->route('regions.index')->with('error', 'Region deleted failed!.'.$e->getMessage());
//         }
//     }


}
