<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreManualReceiveRequest;
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
use Illuminate\Support\Facades\DB;

class  ManualAppReceiveController extends Controller
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
                $user = auth()->user()->id ; 
                $total = AppReceive::where('Date', date('Y-m-d'))->where('created_by', $user)->where('centerId',$cenId)->count() ; 
                $currQ = CurrentQueue::select('token_number')->where('centerId', $cenId)->where('Date', date('Y-m-d'))->where('token_svc_no',1)->where('token_type',1)->orderBy('token_number','asc')->get() ;
                $svcType = Service::select('service_name','id' )->where('status',1)->get() ; 
                $VisaType = VisaType::select('visa_type','id' )->where('status',1)->orderBy('visa_type','asc')->get() ; 
                $centerList = Center::select('center_name','id')->where('status',1)->orderBy('center_name','asc')->get();

                $rejectionReasons = RejectReason::select('id','reason_name')->orderBy('reason_name','asc')->get() ; 
                $stickerList = StickerMap::select('id','sticker')->orderBy('sticker','asc')->get() ; 
                  return view('pages.ManualReceive.index', [
                        'currQ' => $currQ,
                        'svcType' => $svcType,
                        'VisaType' => $VisaType,
                        'centerList' => $centerList,
                        'stickerList' => $stickerList,
                        'rejectionReasons' => $rejectionReasons,
                        'total' => $total,
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

    /**
     * Store a newly created resource in storage.
     */ 
    public function store(StoreManualReceiveRequest $request)  : RedirectResponse
    {    //: RedirectResponse    //StoreAppReceiveRequest
       // dd($request->all());
        try {

            $data = $request->validated();
            // dd($data) ; 
       
            $web = $data['Webfile'] ; 
            $rgstr = substr($web, 0, 4) ; 
            // dd($rgstr) ; 

            if (substr($data['contact'], 0, 1) != '0' || strlen($data['contact']) != 11) {
                return redirect()->route('manual-receive.index')
                    ->with('error', "Contact must start with 0 and be exactly 11 digits!");
            }

            $reg = Region::select('id')->where('region_text',$rgstr)->first() ; 
            // dd($reg->id) ;
            $cenId = auth()->user()->centerId ; 

            $msg = ''; 
            $webExist =  AppReceive::select('id')->where('regionId',$reg->id)->where('Webfile', $web)->first() ; 
            // dd($webExist) ; 
            if($webExist){
                $msg =  "Webfile Already Saved !!!" ; 
            }
            else{
                $centerName='';
                $center = Center::select('center_name')->where('id',$cenId)->first() ; 
                if($center){
                    $centerName  = substr($center->center_name, 0, 2) ; 
                }
                // dd($centerName) ; 
                // $subst = "@".$centerName.date('ymd') ; 
                 $subst = "@".$centerName ; 
                // dd($subst) ;
                if(substr($data['stickerNo'], 0, 3)!=$subst){
                     $msg =  "Invalid Sticker No !!!" ; 
                }
                else{

                    $stc =  AppReceive::select('id')->where('Date',date('Y-m-d'))->where('stickerNo', $data['stickerNo'])->first() ;
                    // dd($stc) ; 
                    if($stc){
                          $msg =  "Sticker No Already Saved !!!" ; 
                    }
                    else{
                        $passport = AppReceive::select('id')->where('Date',date('Y-m-d'))->where('passport', $data['passport'])->first() ; 

                        if($passport){
                             $msg =  "Passport Already Saved !!!" ; 
                        }
                    }
                }
            }
            // dd($msg) ; 
             if($msg !='' ){
                 return redirect()->route('manual-receive.index')->with('error', $msg);
            }
            else{
              $record = AppReceive::updateOrCreate(
                    ['regionId' => $reg->id, 'Webfile' => $web], // Matching condition
                    [
                        'centerId'     => $cenId,
                        'Date'         => $data['rec_date'],
                        'ApplicantName'=> $data['name'],
                        'passport'     => $data['passport'],
                        'stickertype'  => $data['stickertype'],
                        'stickerNo'    => $data['stickerNo'],
                        'status'       => 1,
                        'stepId'       => 1,
                        'remarks'      => 'Manual Entry',
                        'contact'      => $data['contact'],
                        'visatype'     => $data['visatype'],
                        'created_by'   => $data['created_by'],
                    ]
                );
                $id = $record->id;
                // dd($id) ;

                if($id){
                      $save = AppLog::upsert([
                        [
                            'regionId'     => $reg->id,
                            'centerId'     => $cenId,
                            'Date'   =>  $data['rec_date'],
                            'web_ref'     => $id   ,
                            'stepId'     => 1,
                            'remarks'     => 'Manual Entry',
                            'created_by'=> auth()->user()->id ,
                            'created_at'=>Date('Y-m-d H:i:s'),
                            'updated_at'=>Date('Y-m-d H:i:s')
                        ]
                    ],
                    ['regionId' , 'web_ref', 'stepId'], // Unique constraint keys
                    ['updated_at',  'created_by']); // 

                      if($save){
                          return redirect()->route('manual-receive.index')->with('success', 'Data Saved Successfully');
                      }
                      else{
                         return redirect()->route('manual-receive.index')->with('error', 'Insert failed ');
                      }
                  }
                  else{
                     return redirect()->route('manual-receive.index')->with('error', 'Insert failed ');
                  }
            }
           
        } catch (Exception $e) {
            $mess = $e->getMessage(); 
            // dd($mess) ; 
            dd($mess) ; 
           return redirect()->route('manual-receive.index')->with('error', 'Insert failed '.$mess);
           
        }
    }


}
