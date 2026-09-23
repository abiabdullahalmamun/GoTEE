<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAptOverrideRequest;
use App\Http\Requests\UpdateRegionRequest;
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

// use App\Imports\WebFileImport; 
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;


class  CallTokenController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $perPage = $request->input('per_page', 10);
        $search = $request->input('search');

        try {
            $cenId = auth()->user()->centerId ; 
            // dd($cenId) ; 
            if($cenId){
                $currQ = CurrentQueue::select('token_number')->where('centerId', $cenId)->where('Date', date('Y-m-d'))->where('token_svc_no',1)->where('token_type',1)->orderBy('token_number','asc')->get() ;
                $svcType = Service::select('service_name','id' )->where('status',1)->get() ; 
                $VisaType = VisaType::select('visa_type','id' )->where('status',1)->orderBy('visa_type','asc')->get() ; 
                $centerList = Center::select('center_name','id')->where('status',1)->orderBy('center_name','asc')->get();

                $stickerList = StickerMap::select('id','sticker')->orderBy('sticker','asc')->get() ; 
                  return view('pages.AppReceive.index', [
                        'currQ' => $currQ,
                        'svcType' => $svcType,
                        'VisaType' => $VisaType,
                        'centerList' => $centerList,
                        'stickerList' => $stickerList,
                    ]);
            }
            else{
                  return view('pages.AppReceive.index', [
                        'currQ' => $currQ,
                        'svcType' => $svcType,
                        'VisaType' => $VisaType,
                        'centerList' => $centerList,
                         'stickerList' => $stickerList,
                    ]);
            }
         
     
        } catch (Exception $e) {
             dd($e->getMessage()); 
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

  


    public function callLatestToken(Request $request)
    {
        // dd($request) ; 
        $svc = $request->input('svc');
        $user =  auth()->user()->id ; 
        $counter = $request->input('cn');
        $center = auth()->user()->centerId ; 
        $type = $request->input('type');
        $tkn = $request->input('tkn');

        $tokens = DB::select('CALL CallToken(?,?,?,?,?,?)', [$user, $counter,$center,$svc, $type,  $tkn ]);
        // dd($tokens) ;   
        $tokenNo = '';
        $qty = '';
        if (!empty($tokens)) {
            $tokenData = $tokens[0]; 
            $tokenNo = $tokenData->TOKEN;
            $qty = $tokenData->CN;
            $totalToken =  $tokenData->pending;
            session()->put('TokenNo',  $tokenNo );

            return response()->json([
                    'found' => true,
                    'token' => $tokenNo,
                    'qty' => $qty,
                    'totalToken' =>$totalToken,
                ]);
        }
        else{

          return response()->json([
                    'found' => false,
                    'token' => $tokenNo,
                    'qty' => $qty,
                    'totalToken' =>0,
                ]);

        }
    }

    public function deferLatestToken(Request $request)
    {
        try{
            $svc = $request->input('svc');
            $user =  auth()->user()->id ; 
            $counter = $request->input('cn');
            $center = auth()->user()->centerId ; 
            $type = 2 ;
            $tkn =$request->input('tkn') ; 

            if($tkn!=''){
                $tokens = DB::select('CALL deferToken(?,?,?,?,?,?)', [$user, $counter,$center,$svc, $type,  $tkn ]);
                // dd($tokens) ;   
                $tokenNo = '';
                $qty = '';
                $mess = '';
                if (!empty($tokens)) {
                    $tokenData = $tokens[0]; 
                    // dd($tokenData) ; 
                    $tokenNo = $tokenData->tkn;
                    $mess = $tokenData->msg;

                    // session()->put('TokenNo',  $tokenNo );

                    if($mess=='OK'){
                        return response()->json([
                            'found' => true,
                            'token' => $tokenNo,
                            'qty' => $qty,
                            'msg' => $mess,
                        ]);
                    }
                    else if($mess=='WAIT EXCEEDED'){
                        return response()->json([
                           'found' => false,
                          'token' => '',
                          'qty' => '',
                          'msg' => $mess,
                        ]);
                    }
                    else{
                        return response()->json([
                            'found' => false,
                            'token' => $tokenNo,
                            'qty' => $qty,
                             'msg' => 'Please wait minimum time',
                        ]);
                    }

                }
                else{

                  return response()->json([
                            'found' => false,
                            'token' => $tokenNo,
                            'qty' => $qty,
                             'msg' => 'Failed to connect DB',
                        ]);

                }
            }
            else{
                   return response()->json([
                    'found' => false,
                    'token' => '',
                    'qty' => '',
                     'msg' => 'No Token Selected',
                ]);
            }


         } catch (Exception $e) {
            $msg = $e->getMessage(); 
              return response()->json([
                        'found' => false,
                        'token' => $tokenNo,
                        'qty' => $qty,
                      'msg' =>  $msg
                ]);
        }
    }

}
