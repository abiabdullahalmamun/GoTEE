<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreFormFillRequest;
// use App\Http\Requests\UpdateRegionRequest;
use App\Models\Region;
use App\Models\Center;
use App\Models\FormFill ; 
use App\Models\Counter;
use App\Models\VisaType;
use App\Models\VisaTypeTdd ;
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
use App\Models\User ; 
use App\Models\DisplayScroll ; 
use App\Jobs\SendSmsDCJob;
// use App\Imports\WebFileImport; 
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\DB;

class  FormFillController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $perPage = $request->input('per_page', 10);
        $search = $request->input('search');

        try {
            $ips = request()->getClientIps(); 
            $lastHopIp = end($ips);
            // dd($lastHopIp)     ; 
            $cenId = auth()->user()->centerId ; 

            if($cenId){
                $cnt = Counter::select('center_id','counter_id','loginstate','tokenno','autoMan','visaType','stickerType')->where('ip',$lastHopIp )->first() ; 
                // dd($cnt) ; 
                if($cnt){
                    // dd($cnt) ; 
                    if($cnt->center_id === $cenId ){
                        $cntNo =$cnt->counter_id ;  
                        $lastSvc = $cnt->loginstate ;  
                        $lastTkn = $cnt->tokenno ;  
                        $lastoptype = $cnt->autoMan ;  
                        $lastvisa = $cnt->visaType ;  
                        $laststicker = $cnt->stickerType ; 

                        $user = auth()->user()->id ; 
                        $total = FormFill::where('Date', date('Y-m-d'))->where('created_by', $user)->where('centerId',$cenId)->count() ; 
            
                      return view('pages.FormFill.index', [
               
                            // 'rejectionReasons' => $rejectionReasons,
                            'total' => $total,
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
                        'error' => 'Counter Not Registered',
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

 

    public function getCurrQ($svctypeId, Request $request)
    {
        $cnt = $request->query('cnt');
        $cenId = auth()->user()->centerId ; 
        // Fetch token numbers based on selected service type
        $baseQuery = CurrentQueue::where('token_svc_no', $svctypeId)
                    ->where('centerId', $cenId)
                    ->where('Date', date('Y-m-d'))
                    ->orderBy('token_number', 'asc');

        $currQ = (clone $baseQuery)
                    ->where('token_type', 1)
                    ->take(15)
                    ->get(['token_number']);   
        $waitQ = (clone $baseQuery)
                    ->where('token_type', 2)
                    ->where('cnt', $cnt)
                    ->take(15)
                    ->get(['token_number']);         

         return response()->json([
                    'currQ' => $currQ,
                    'waitQ' => $waitQ,
                ]); 
        // $currQ = CurrentQueue::where('token_svc_no', $svctypeId)
        //              ->where('centerId', $cenId)
        //              ->where('Date', date('Y-m-d'))
        //              ->where('token_type',1)
        //              ->orderBy('token_number','asc')
        //              ->take(15)
        //              ->get(['token_number']);

        // Return JSON
        // return response()->json($currQ);
    }
    public function getDefQ($svctypeId)
    {
        $cenId = auth()->user()->centerId ; 
        // Fetch token numbers based on selected service type
        $defQ = CurrentQueue::where('token_svc_no', $svctypeId)
                     ->where('centerId', $cenId)
                     ->where('Date', date('Y-m-d'))
                     ->where('token_type',2)
                     ->orderBy('token_number','asc')
                     ->take(15)
                     ->get(['token_number']);

        // Return JSON
        return response()->json($defQ);
    }

    public function checkCodeweb(Request $request)
    {
        // dd($request) ; 
        $code = $request->input('code');
        $cenId = auth()->user()->centerId ; 
        $exists = QueueCode::where('random', $code)
                    ->where('centerId',$cenId)
                    ->where('Date',date('Y-m-d'))
                    ->where('status',1)
                    ->exists();

        return response()->json(['found' => $exists]);
    }

    public function counterLogin(Request $request)
    {
        try{
            $cnt = $request->input('cnt');
            $svc = $request->input('svc');
            // dd($svc) ; 
            $msg = ''; 
            $msg = $svc ; 
            $reply = [] ; 
            if($svc){
                $cenId = auth()->user()->centerId ; 
                $user = auth()->user()->id ; 

                $updateEx = Counter::where('loginId',$user)->update([
                        'loginstate'  => 0, 
                        'loginId'  =>  null, 
                        'tokenno'  =>  null, 
                        'autoMan'  =>  null, 
                        'visaType'  =>  null, 
                        'stickerType'  =>  null, 
                        'updated_at'=>now()
                    ]);  

                $update=Counter::where('center_id',$cenId)->where('counter_id',$cnt)->update([
                        'loginstate'  =>  $svc, 
                        'loginId'  =>  $user, 
                        'updated_at'=>now()
                    ]);  

                if($update){
                    $msg = 'Logged in Counter '.$cnt.' Service '.$svc ; 
                    return response()->json([
                        'found' => true,
                        'message' =>$msg,
                         'reply' =>$reply,
                    ]);
                }
                else{
                     return response()->json([
                         'found' => true,
                         'message' =>$msg,
                           'reply' =>$reply,
                    ]);
                }
            }
            else{
                $msg = 'Please Select ServiceType' ; 
                 return response()->json([
                        'found' => true,
                         'message' =>$msg,
                    ]);
            }
        } catch (Exception $e) {
            $msg = $e->getMessage(); 
            return response()->json([
                'found' => true,
                 'message' =>$msg,
            ]);
        }


    }
    
    public function saveOpType(Request $request)
    {
        try{
            $cnt = $request->input('cnt');
            $opntype = $request->input('opntype');
            // dd($svc) ; 
            $msg = ''; 
            $reply = [] ; 
            if($opntype){
                $cenId = auth()->user()->centerId ; 
                // $user = auth()->user()->id ; 
                $update=Counter::where('center_id',$cenId)->where('counter_id',$cnt)->update([
                        'autoMan'  =>  $opntype, 
                        'updated_at'=>now()
                    ]);  

                if($update){
                    $msg = 'Operation type saved for counter '.$cnt ; 
                    return response()->json([
                        'found' => true,
                        'message' =>$msg,
                         'reply' =>$reply,
                    ]);
                }
                else{
                      $msg = 'Failed to save ' ; 
                     return response()->json([
                         'found' => true,
                         'message' =>$msg,
                           'reply' =>$reply,
                    ]);
                }
            }
            else{
                $msg = 'Please Select operation type' ; 
                 return response()->json([
                        'found' => true,
                         'message' =>$msg,
                    ]);
            }
        } catch (Exception $e) {
            $msg = $e->getMessage(); 
            return response()->json([
                'found' => true,
                 'message' =>$msg,
            ]);
        }
    }

    public function saveVisaType(Request $request)
    {
        try{
            $cnt = $request->input('cnt');
            $visatype = $request->input('visatype');
            // dd($svc) ; 
            $msg = ''; 
            $reply = [] ; 
            if($visatype){
                $cenId = auth()->user()->centerId ; 
                // $user = auth()->user()->id ; 
                $update=Counter::where('center_id',$cenId)->where('counter_id',$cnt)->update([
                        'visaType'  =>  $visatype, 
                        'updated_at'=>now()
                    ]);  

                if($update){
                    $msg = 'Visa type saved for counter '.$cnt ; 
                    return response()->json([
                        'found' => true,
                        'message' =>$msg,
                         'reply' =>$reply,
                    ]);
                }
                else{
                      $msg = 'Failed to save ' ; 
                     return response()->json([
                         'found' => true,
                         'message' =>$msg,
                           'reply' =>$reply,
                    ]);
                }
            }
            else{
                $msg = 'Please Select operation type' ; 
                 return response()->json([
                        'found' => true,
                         'message' =>$msg,
                    ]);
            }
        } catch (Exception $e) {
            $msg = $e->getMessage(); 
            return response()->json([
                'found' => true,
                 'message' =>$msg,
            ]);
        }
    }

    public function saveStickerType(Request $request)
    {
        try{
            $cnt = $request->input('cnt');
            $st_type = $request->input('st_type');
            // dd($svc) ; 
            $msg = ''; 
            $reply = [] ; 
            if($st_type){
                $cenId = auth()->user()->centerId ; 
                // $user = auth()->user()->id ; 
                $update=Counter::where('center_id',$cenId)->where('counter_id',$cnt)->update([
                        'stickerType'  =>  $st_type, 
                        'updated_at'=>now()
                    ]);  

                if($update){
                    $msg = 'Sticker type saved for counter '.$cnt ; 
                    return response()->json([
                        'found' => true,
                        'message' =>$msg,
                         'reply' =>$reply,
                    ]);
                }
                else{
                      $msg = 'Failed to save ' ; 
                     return response()->json([
                         'found' => true,
                         'message' =>$msg,
                           'reply' =>$reply,
                    ]);
                }
            }
            else{
                $msg = 'Please Select operation type' ; 
                 return response()->json([
                        'found' => true,
                         'message' =>$msg,
                    ]);
            }
        } catch (Exception $e) {
            $msg = $e->getMessage(); 
            return response()->json([
                'found' => true,
                 'message' =>$msg,
            ]);
        }
    }
    public function checkbarcodeSticker(Request $request)
    {
        // dd($request) ; 
        $st = $request->input('stNo');
        $cenId = auth()->user()->centerId ; 

        // $centerName = '';
        $center = Center::select('center_name')->where('id',$cenId)->first() ; 
        if($center){
            $centerName  = substr($center->center_name, 0, 2) ; 
        }

        $subst = "@".$centerName.date('ymd') ; 
        if(substr($st, 0, 9)==$subst){

            $stc = AppReceive::select('id')->where('stickerNo',$st)->first() ; 
            if($stc){
                return response()->json([
                     'found' => false , 
                        'dd' => $subst,
                ]);
               
            }
            else{
                 return response()->json([
                    'found' => true,
                    'dd' => $subst,
                ]);
            }
           
        }
        else{
            return response()->json([
                'found' => false , 
                'dd' => $subst,
            ]);
        }

    }

    public function checkpassport(Request $request)
    {
        // dd($request) ; 
        $pass = $request->input('pass');
        $status = true ; 
        // $cenId = auth()->user()->centerId ; 
        $msg = '' ; 
        $passRecords = AppReceive::where('passport', $pass)
                    ->orderBy('Date', 'desc')
                    ->get();

        // $dd =   $passRecords ;            
        if ($passRecords) {
            $todayRecord = $passRecords->firstWhere('Date', today()->toDateString());
            $oldRecord = $passRecords->first(function ($r) {
                return $r->Date !== today()->toDateString() && $r->stepId != 5;
            });

            // dd($todayRecord) ; 
            if($todayRecord){
                 $msg =  "Passport Already Saved Today!!!" ; 
            }
            else{
                // dd($oldRecord) ; 
                if ($oldRecord) {
                   // dd($oldRecord->stepId) ; 
                    if($oldRecord->stepId == 1){
                        $msg = 'Undelivered Passport, Received on '.$oldRecord->Date ; 
                    }
                    else  if($oldRecord->stepId == 2){
                        $msg = 'Undelivered Passport in HCI, Received on '.$oldRecord->Date ; 
                    }
                    else  if($oldRecord->stepId == 3){
                        $msg = 'Undelivered Passport returned from HCI, Received on '.$oldRecord->Date ;
                    }
                    else if($oldRecord->stepId == 4){
                        $msg = 'Undelivered Passport at center, Received on '.$oldRecord->Date ;
                    }
                }
            }

        }
        if($msg!=''){
            $status = false ; 
        }
        else{
            $status = true ; 
        }
       return response()->json([
            'found' => $status , 
            'msg' => $msg,
        ]);

    }
 

 


    public function edit($id)
    {
        // dd($id) ; 
        $data = FormFill::where('id', $id)->first() ; 
        // dd($data) ; 
        $centerId = auth()->user()->centerId;

        // $cenId = auth()->user()->centerId ; 
        if($data->centerId != $centerId ){
            return redirect()->route('searchWebfile.index')->with('error', "Cannot edit other center data");
        }
        else{
            return view('pages.FormFill.partials.edit', compact('data' ));
        }

       
    }


    public function update(Request $request)
    {
        // dd($request->all());
        $request->validate([
            'Webfile' => 'required|string',
            'name' => 'required|string',
            'passport' => 'required|string',
             'contact' => 'required|string',
            'recId' => 'required|numeric',
             'corrfee' => 'required|numeric',
        ]);
        // dd($request->all()); 
        $apprec = FormFill::find($request->recId);
        if ($apprec) {
             $cenId = auth()->user()->centerId ; 
            if($apprec->centerId != $cenId ){
                  return redirect()->route('searchWebfile.index')->with('error', "Cannot edit other center data");

            }
            else{

                $apprec->Name = strtoupper($request->name);
                $apprec->webfile = $request->Webfile;
                $apprec->passport = strtoupper(str_replace(' ', '', trim($request->passport))) ;
                $apprec->contact = $request->contact;
                $apprec->fee = $request->corrfee;
                $apprec->updated_at = now();  
                $apprec->save(); // Fires 'updated' event → Loggable works

                $nicfile = NicAptList::where('webfile',$request->Webfile)->first() ; 

                if($nicfile){
                    $nicfile->passport =  strtoupper(str_replace(' ', '', trim($request->passport))) ;
                    $nicfile->Name = strtoupper($request->name);
                    $nicfile->contact = $request->contact;
                    $nicfile->save() ; 

                }

                return redirect()->route('searchWebfile.index')->with('success', 'Data updated successfully');
             }  
        }
        else{
              return redirect()->route('searchWebfile.index')->with('error', "no data found!!!");
        }
    }

    public function destroy($id){
        // dd($id) ;

        $apprec = FormFill::find($id); 
        // dd($apprec) ; 
        if ($apprec) {
            // dd($apprec->Webfile) ; 
            $cenId = auth()->user()->centerId ; 
            if($apprec->centerId != $cenId ){
                 return redirect()->route('searchWebfile.index')->with('error', "Cannot delete other center data");
            }
            else{
                $del = $apprec->delete(); // Fires 'deleted' event → Loggable works
                if($del){

                    return redirect()->route('searchWebfile.index')->with('success','Data Deleted successfully.');
                }
                else{
                     return redirect()->route('searchWebfile.index')->with('error','Failed to  Delete.');
                 
                }

            }
        
        }
        else{
             return redirect()->route('searchWebfile.index')->with('error','Data not found');
        }
      
    }


    /**
     * Store a newly created resource in storage.
     */ 
    public function store(StoreFormFillRequest $request): RedirectResponse  
    {    //: RedirectResponse    //StoreAppReceiveRequest
       // dd($request->all());
        try {

            $data = $request->validated();
            // dd($data) ; 
            $web = $data['wf_no'] ; 
            $cenId = auth()->user()->centerId ; 

            if(substr($data['contact'], 0, 1)!='0'){
                 return redirect()->route('form-fill.index')
                    ->with('error', "Contact should start with 0 !!!");
            }

            $rgstr = substr($web, 0, 4) ; 
            // dd($rgstr) ; 
            $reg = Region::select('id')->where('region_text',$rgstr)->first() ;         
            $msg = ''; 

            if($reg){
                $msg = ""; 
            }
            else{
                $msg =  "Invalid Webfile, no regiod found !!!" ; 
            }

            $webExist =  FormFill::select('id')->where('Webfile', $web)->first() ; 
            if($webExist){
                $msg =  "Webfile Already Saved !!!" ; 
            }
            else{
                // dd($msg) ; 
                // $center = Center::select('center_name')->where('id',$cenId)->first() ; 
                // if($center){
                //     $centerName  = substr($center->center_name, 0, 2) ; 
                // }

                $passRecords = FormFill::where('passport', $data['passport1'])
                        ->orderBy('Date', 'desc')
                        ->get();
            // dd( $passRecords) ; 
                if ($passRecords) {
                    $todayRecord = $passRecords->firstWhere('Date', today()->toDateString());
                    $oldRecord = $passRecords->first(function ($r) {
                        return $r->Date !== today()->toDateString() && $r->stepId != 5;
                    });

                    // dd($todayRecord) ; 
                    if($todayRecord){
                         $msg =  "Passport Already Saved Today!!!" ; 
                    }
                }
 
            }
            // dd($data['passport1'].$msg) ; 
            if($msg !='' ){
                 return redirect()->route('form-fill.index')->with('error', $msg);
            }
            else{
                $sl = (FormFill::where('Date', date('Y-m-d'))
                        ->where('centerId', $cenId)
                        // ->where('created_by', auth()->user()->id)
                        ->max('day_sl') ?? 0) + 1;

                $record = FormFill::updateOrCreate(
                    ['webfile' => $web], // Matching condition
                    [
                        'centerId'     => $cenId,
                        'Date'         => date('Y-m-d'),
                        'day_sl'         =>$sl,
                        'Name'=> $data['name'],
                        'passport'    => strtoupper(str_replace(' ', '', trim($data['passport1']))),
                        'status'       => 1,
                        'contact'      => $data['contact'],
                        'remarks'      => $data['remarks'],
                        'fee'      => $data['fee'],
                        'created_by'   => auth()->user()->id,
                    ]
                );

                $id = $record->id;
                // dd($id) ;
                if($id){
                    $save = NicAptList::upsert([
                    [
                        'regionId' => $reg->id,
                        'webfile'  =>  $web,
                        'passport'=>  strtoupper(str_replace(' ', '', trim($data['passport1']))),
                         'Name'     => $data['name'],
                         'contact'     =>  $data['contact'],
                            'reg_date'   => date('Y-m-d'),
                            'created_by'=> auth()->user()->id ,
                            'created_at'=>Date('Y-m-d H:i:s'),
                            'updated_at'=>Date('Y-m-d H:i:s')
                        ]
                    ],
                    [ 'webfile' ], // Unique constraint keys
                    ['updated_at',  'created_by']); // 


                    if($save){
                           return redirect()->route('form-fill.print', ['id' => $id]); 
                    }
                }
                else{
                     return redirect()->route('form-fill.index')->with('error', "Failed to save data");
                }

            }
           
        } catch (Exception $e) {
            $mess = $e->getMessage(); 
            // dd($mess) ; 
           return redirect()->route('form-fill.index')->with('error', 'Insert failed '.$mess);
           
        }
    }

    public function print($id)
    {
        // dd($id) ; 
        $data = FormFill::findOrFail($id);
        $cenId = auth()->user()->centerId ; 
       if($data->centerId != $cenId){
           return view('pages.error.index', [
                        'error' => 'Cannot print other center receipt',
                    ]);  
       }
       else{
            return view('pages.FormFill.form_receipt_print', [
                'datas' =>  $data,
            //     'code' => $codA->random ?? 'N/A',
            ]);
       
       }
    }


 

     public function getUsersByCenter($centerId)
    {
        // Fetch users by centerId
        $users = User::where('centerId', $centerId)
            ->select('id', 'name') // keep it lightweight
            ->orderBy('name')
            ->get();

        return response()->json($users);
    }


}
