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
use App\Jobs\SendSmsJob;
// use App\Imports\WebFileImport; 
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use App\Services\ActionExceptionService;

class  StageUpdateController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        // dd($request) ; 
        $perPage = $request->input('per_page', 100);
        $search = $request->input('search');

        try {
            $ips = request()->getClientIps(); 
            $lastHopIp = end($ips);
            $cenId = auth()->user()->centerId ; 
            // dd($cenId) ; 
            $user = auth()->user()->id ; 
            if($cenId){
                $cnt = Counter::select('center_id')->where('ip',$lastHopIp )->first() ; 
                if($cnt){
                    if($cnt->center_id === $cenId ){
                        $count = AppLog::where('Date', date('Y-m-d'))->where('created_by', $user )->where('stepId',4)->count() ; 

                    // dd($count) ; 
                        return view('pages.StageUpdate.index', [
                            'count' => $count,
                             'smsg' => 'Please scan webfile...',
                        ]);
                    }
                    else{

                     return view('pages.error.index', [
                            'error' => 'Page not permitted',
                        ]);
                    }
                }
                else{
                     return view('pages.error.index', [
                            'error' => 'Counter not registered',
                        ]);
                }
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
            $latest =[] ; 
            $record = [] ; 
            $success= false ; 
            $count = 0 ; 
            $smsg = '';
            $request->validate([
                'web' => 'required|string'
            ]);
            $cenId = auth()->user()->centerId ; 
            $web =  $request->web ; 
           //  if(substr($web, 0, 3)== 'BGD'){

            if(substr($web, 0, 3)== 'BGD'){
                $region = Region::select('id')->where('region_text',substr($web, 0, 4))->orderby('id', 'desc')->first() ; 

                $record = AppReceive::select('id','centerId','regionId','contact','Webfile')->where('regionId',  $region->id)->where('Webfile',$web)->first() ; 

                $order = [5, 4, 33, 32, 31, 2, 1];

                $latest = AppLog::with(['webref' => function ($query) {
                $query->select('id', 'Webfile', 'passport', 'ApplicantName', 'contact');
                        }])
                        ->where('web_ref',$record->id)
                        ->where('stepId','!=',11)
                        ->orderByRaw('FIELD(stepId, ' . implode(',', $order) . ')')
                        // ->orderBy('stepId', 'asc')
                        ->take(20)
                        ->get();
                    
                $success = true ; 

            }
            else{
                 $success = false ; 
                 $smsg = 'Invalid Webfile' ; 
            }

            return response()->json([
                        'reply' => $reply ,
                        'data' => $record ,
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
        $succ = false ; 
        $message = 'Data delete failed';
        $record = AppLog::find($id);
        $latest = [] ; 
        if($record){
            $web =  $record->web_ref ; 

            if($record->stepId!=1){
                $record->delete(); 
                AppReceive::where('id', $record->web_ref)
                            ->where('stepId', '!=', 1)
                            ->decrement('stepId', 1, [
                                'updated_at' => now(),
                            ]); 

                $webfileD = AppReceive::where('id', $record->web_ref)->first() ; 
                if($webfileD){
                    ActionExceptionService::store([
                        'module'      => 'ApplicationStage',
                        'action'      => 'StageModified',
                        'centerId'      => $webfileD->centerId ,
                        'remarks'     =>'id:'.$webfileD->id.' Web:'.$webfileD->Webfile.' Passport:'.$webfileD->passport.' step:'.$record->stepId , // save only duplicated numbers
                    ]);
                }       
               
                $succ = true ; 
                $message = 'Data updated successfully';
            }
            else{
                $succ = false ; 
                $message = 'Cannot update receive data';
            }


            $order = [5, 4, 33, 32, 31, 2, 1];

            $latest = AppLog::with(['webref' => function ($query) {
            $query->select('id', 'Webfile', 'passport', 'ApplicantName', 'contact');
                    }])
                    ->where('web_ref', $web)
                    ->orderByRaw('FIELD(stepId, ' . implode(',', $order) . ')')
                    // ->orderBy('stepId', 'asc')
                    ->take(20)
                    ->get();
        }

        // $cenId = auth()->user()->centerId ; 

       // $user =  auth()->user()->id ; 

       

        // $latest = AppLog::with(['webref' => function ($query) {
        //             $query->select('id', 'Webfile', 'passport', 'ApplicantName', 'contact');
        //         }])
        //         // ->where('Date', date('Y-m-d'))
        //         // ->where('created_by', $user)
        //         // ->where('stepId', 4)
        //         ->orderBy('id', 'desc')
        //         ->take(20)
        //         ->get();
        // $count = AppLog::where('Date', date('Y-m-d'))->where('created_by', $user )->where('stepId',4)->count() ; 
        $count=0 ; 

        return response()->json([
                        'success' => $succ,
                        'message' => $message, 
                        'count' =>  $count, 
                        'latest' => $latest,
                    ]);
    }



}
