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
use App\Models\AppDigitization ; 
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


class  ReadyDelDeleteController extends Controller
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
                        $count = AppLog::where('Date', date('Y-m-d'))->where('centerId', $cenId )->whereIn('stepId',[4,5])->count() ; 

                // dd($count) ; 
                        return view('pages.ReadyDelDelete.index', [
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
            $latest = [] ; 
            $success= false ; 
            $smsg = '';
            $request->validate([
                'web' => 'required|string'
            ]);
            $cenId = auth()->user()->centerId ; 
            $web =  $request->web ; 
 
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
                $success= true ; 
                              // $user =  auth()->user()->id ; 
                $latest = AppLog::with(['webref' => function ($query) {
                    $query->select('id', 'Webfile', 'passport', 'ApplicantName', 'contact', 'stepId', 'created_at', 'visatype', 'corrFee','remarks','txn','stickertype','stickerNo')->with('sticker:id,sticker')->with('visa:id,visa_type'); 
                        }])->with('user')
                        ->where('web_ref', $record->id)
                        ->whereIn('stepId',[4,5,11,8,9])
                        ->orderBy('id', 'desc')
                        ->take(10)
                        ->get();
                // dd($latest) ; 
               $count = AppLog::where('web_ref', $record->id)->whereIn('stepId',[4,5,11,8,9])->count() ; 
            }

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
        $cenId = auth()->user()->centerId ; 
        $success = false ;     
        $message = '';
        if($record->stepId==4){
            $exists = AppLog::where('web_ref', $record->web_ref)
                    ->where('stepId',5)
                    ->exists();

            if($exists){
                 $success = false ;   
                 $message  = "Delivery Data exists" ; 
            }
            else{
                $is_update = AppReceive::find($record->web_ref)->update([
                    'stepId'       =>  3, 
                    'updated_at'=>Date('Y-m-d H:i:s')
                ]);
              $record->delete();  
              $success = true ;   
                $message  =  "Data Deleted successfully" ; 
            }          
  
        }
        else if($record->stepId==5){
             $is_update = AppReceive::find($record->web_ref)->update([
                    'stepId'       =>  4, 
                    'updated_at'=>Date('Y-m-d H:i:s')
                ]);
             $record->delete();  
             $success = true ;     
             $message  =  "Data Deleted successfully" ; 
        }
        else if($record->stepId==11){
             $record->delete();  
             $success = true ;     
             $message  =  "Data Deleted successfully" ; 
        }
        else if($record->stepId==8){
            $exists = AppLog::where('web_ref', $record->web_ref)
                    ->where('stepId',9)
                    ->exists();
            if($exists){
                 $success = false ;   
                 $message  = "DVD Data exists" ; 
            }
            else{
                AppDigitization::where('webref', $record->web_ref)->delete();
                $record->delete();  
                $success = true ;     
                $message  =  "Data Deleted successfully" ; 
            }
            
        }
        else if($record->stepId==9){
               AppDigitization::where('webref', $record->web_ref)->update([
                'dvd_date'     => null,
                'updated_at' => now(),
                ]);

             $record->delete();  
             $success = true ;     
             $message  =  "Data Deleted successfully" ; 
        }
        else{
             $success = false ;     
            $message = "no data found" ; 
        }
       
        $latest = []; 
            // dd($latest) ; 
        $count = AppLog::where('Date', date('Y-m-d'))->where('centerId', $cenId)->whereIn('stepId',[4,5,11,8,9])->count() ; 
    
     
        return response()->json([
                         'success' =>  $success,
                        'message' =>   $message , 
                        'count' => $count, 
                        'latest' => $latest,
                    ]);
    }



}
