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
use App\Models\Region ; 
use App\Models\AppLog ;
use App\Models\AppReceive ; 
use App\Models\User ;
use Carbon\Carbon;

use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class WebSearchController extends Controller
{
    public function index(Request $request)
    {
        $userId = Auth::id();

        return   $userId ; 
    }

   
    public function search(Request $request)
    {
            $name = "";
         $webfile= "" ;
          $step = "" ; 
          $deldate = "";
        try{
            // $user = User::find(65); 
            // dd($user) ; 
            // $webToken = $user->createToken('web-client')->plainTextToken;
            // dd($webToken) ; 
            $message = "" ; 
            $statuscode  = 422 ; 
            // dd( $webToken) ; 
            // dd($request->all()) ; 
              $ips = request()->getClientIps(); 
            $lastHopIp = end($ips);
            // dd($lastHopIp) ; 
            $val = $request->input('payload.webfile');  
            // dd($val) ; 
            $dataa =[] ; 
            if(substr($val, 0,3) =='BGD'){
                 $re = substr($val, 0,4) ; 
                // dd($re) ; 
                $reg = Region::select('id')->where('region_text',$re)->first();
                if($reg){
                    // dd($reg) ; 
                    $dataa = AppReceive::where('regionId',$reg->id)->where('Webfile',$val)->orderBy('id','desc')->first();
                    // dd($dataa) ; 
                }
                else{
                    $message ='Invalid webfile';
                }
                // dd($dataa) ; 
            }
            else if(substr($val, 0,1) =='0'){

                $dataa = AppReceive::where('contact',$val)->orderBy('id','desc')->first();
            }
            else if(substr($val, 0,1) =='@'){
                $dataa = AppReceive::where('stickerNo',$val)->orderBy('id','desc')->first();
            }
            else{
                $dataa = AppReceive::where('passport',$val)->orderBy('id','desc')->first();
            }

            if($dataa){
               $name = $dataa->ApplicantName ; 
               $webfile = $dataa->Webfile ; 
               $step =$dataa->stepId ; 
               if( $step==5){
                 $deLog = AppLog::select('Date')->where('web_ref',$dataa->id)->where('stepId',5)->first() ; 
                  $deldate =  $deLog->Date ;  
               }
               $message ="Data found" ; 
               $statuscode = 200 ; 
            }
            else
            {   
                $message ="No Record found" ; 
                $statuscode = 422 ; 
            }

            return [

                    'message' =>  $message,
                    'statuscode' =>  $statuscode,
                    'name' =>  $name,
                    'webfile' =>  $webfile ,
                    'step' =>  $step,
                    'deldate' => $deldate ,
                ];

        }catch (Exception $e) {
            $msg = $e->getMessage(); 
            return [
                    'message' =>  $msg,
                    'statuscode' => 422,
                    'name' =>  $name,
                    'webfile' =>  $webfile ,
                    'step' =>  $step,
                ];
        }
    }

}
