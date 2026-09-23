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


class  AcceptRejectController extends Controller
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
            $hciact = [31,32,33] ; 
            $cenId = auth()->user()->centerId ; 
            // dd($cenId) ; 
            $user = auth()->user()->id ; 
            if($cenId){
                $count = AppLog::where('Date', date('Y-m-d'))->where('created_by', $user )->whereIn('stepId',$hciact)->count() ; 

                // dd($count) ; 
              return view('pages.AcceptReject.index', [
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
            $latest =[] ; 
            $record = [] ; 
            $success= false ; 
            $count = 0 ; 
            $smsg = '';
            
             // $hciact = [31,32,33] ; 
             $user = auth()->user()->id ;
             if($request->filled('act'))
             {
                $latest = AppLog::with(['webref' => function ($query) {
                        $query->select('id', 'Webfile', 'passport', 'ApplicantName', 'contact', 'stepId', 'created_at', 'visatype')
                              ->with('visa:id,visa_type'); 
                        }])
                        ->where('Date',date('Y-m-d'))
                        ->where('created_by',$user)
                        ->where('stepId',  $request->act)
                        ->orderBy('id','desc')
                        ->get(); 

               $count = AppLog::where('Date',date('Y-m-d'))->where('created_by',$user)->where('stepId',  $request->act)->count()  ;   
                $success = true ;  
             }
             else{
                 $hciact = [31,32,33] ; 
                $latest = AppLog::with(['webref' => function ($query) {
                        $query->select('id', 'Webfile', 'passport', 'ApplicantName', 'contact', 'stepId', 'created_at', 'visatype')
                              ->with('visa:id,visa_type'); 
                        }])
                        ->where('Date',date('Y-m-d'))
                        ->where('created_by',$user)
                        ->whereIn('stepId', $hciact)
                        ->orderBy('id','desc')
                        ->get();
                
                 $count = AppLog::where('Date',date('Y-m-d'))->whereIn('stepId', $hciact)->where('created_by',$user)->count()  ;  
                 $success = true ; 
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
        $record = AppLog::find($id);

         $hciact = [31,32,33] ; 
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
                ->whereIn('stepId', $hciact)
                ->orderBy('id', 'desc')
                ->take(20)
                ->get();
        $count = AppLog::where('Date', date('Y-m-d'))->where('created_by', $user )->whereIn('stepId',$hciact)->count() ; 
     
        return response()->json([
                        'success' => true,
                        'message' => "Data Deleted successfully", 
                        'count' => $count, 
                        'latest' => $latest,
                    ]);
    }



}
