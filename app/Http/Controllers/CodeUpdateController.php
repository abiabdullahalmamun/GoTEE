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
use App\Models\TokenLog ; 
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


class  CodeUpdateController extends Controller
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
                        $count = QueueCode::where('Date', date('Y-m-d'))->where('centerId', $cenId )->count() ; 
                // dd($count) ; 
                        return view('pages.CodeUpdate.index', [
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
            $success= true ; 
            $smsg = '';
            $request->validate([
                'web' => 'required|string'
            ]);
            $cenId = auth()->user()->centerId ; 
            $web =  $request->web ; 
    
            // $latest = QueueCode::with('service:id,service_name','tokenlog:cno,servedby')->where('Date', date('Y-m-d'))
            //         ->where('web', $web)
            //         ->where('centerId', $cenId)
            //         ->orderBy('id', 'desc')
            //         ->take(100)
            //         ->get();
            // dd($latest) ; 


            if(substr($web, 0, 3)== 'BGD'){
                $latest = QueueCode::with([
                    'service:id,service_name',
                    'tokenlog' => function ($q) {
                        $q->select('id','cno','servedby')
                          ->with('user:id,name');
                    }
                ])
                ->whereDate('Date', now())
                ->where('web', $web)
                ->where('centerId', $cenId)
                ->latest('id')
                ->limit(100)
                ->get();

            }
            else{
                $latest = QueueCode::with([
                    'service:id,service_name',
                    'tokenlog' => function ($q) {
                        $q->select('id','cno','servedby')
                          ->with('user:id,name');
                    }
                ])
                ->whereDate('Date', now())
                ->where('random', $web)
                ->where('centerId', $cenId)
                ->latest('id')
                ->limit(100)
                ->get();
            }



           $count = QueueCode::where('Date', date('Y-m-d'))->where('web', $web)->count() ; 
    
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
       //  $record = AppLog::find($id);
          $cenId = auth()->user()->centerId ; 
        $success = true ; 
        $msg = '';
        $record = QueueCode::find($id) ; 
        if($record){
           
            if($record->svclogId){
                 $svcLog = TokenLog::find($record->svclogId) ; 
                  $ddt = $svcLog ; 
                 $tokenType = $svcLog->cno !== null ? 3 : 1;

                $queue = CurrentQueue::create([
                    'Date' => now(),
                    'centerId' => $svcLog->centerId,
                    'token_type' => $tokenType,
                    'token_svc_no' => $svcLog->token_svc_no,
                    'token_number' => $svcLog->tokenno,
                    'cnt' => $svcLog->cno,
                ]);
            }
            // else{
            //      $ddt ='no' ; 
            // }
            $update = QueueCode::where('web', $record->web)->update([
                    'status'       =>  0, 
                    'updated_at'=>Date('Y-m-d H:i:s')
                ]);

            if($update){
                 $is_update = QueueCode::find($id)->update([
                    'status'       =>  1, 
                    'updated_at'=>Date('Y-m-d H:i:s')
                ]);

                $msg ="Code updated successfully" ; 
                $success = true ; 
            }
            else{
                $msg ="Failed to update" ; 
                $success = false ; 
            }
        }
        else{
            $msg ="No Record found" ; 
             $success = false ; 
        }

          //  // $record->delete();  
       //  $is_update = AppReceive::find($record->web_ref)->update([
       //              'stepId'       =>  3, 
       //              'updated_at'=>Date('Y-m-d H:i:s')
       //          ]);

       // $user =  auth()->user()->id ; 
        $web = '' ; 
        $latest = QueueCode::with('service:id,service_name')->where('Date', date('Y-m-d'))
                    ->where('web', $web)
                    ->where('centerId', $cenId)
                    ->orderBy('id', 'desc')
                    ->take(100)
                    ->get();
       $count = QueueCode::where('Date', date('Y-m-d'))->where('centerId', $cenId )->count() ; 
     
        return response()->json([
                        'success' => $success ,
                        'message' =>$msg , 
                        'count' => $count, 
                        'latest' => $latest,
                         'data' => $ddt,
                    ]);
    }



}
