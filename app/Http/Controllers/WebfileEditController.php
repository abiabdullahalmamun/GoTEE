<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAppReceiveRequest;
// use App\Http\Requests\UpdateRegionRequest;
use App\Models\Region;
use App\Models\Center;
use App\Models\Counter;
use App\Models\CounterSvc ; 
use App\Models\VisaType;
use App\Models\VisaTypeTdd ;
use App\Models\VisaTypeApt;
use App\Models\AptOverride;
use App\Models\StickerMap;
use App\Models\Service;
use App\Models\CurrentQueue;
use App\Models\QueueCode ; 
use App\Models\TokenLog ; 
use App\Models\TokenLogWeb ; 
use App\Models\SslAptList ; 
use App\Models\NicAptList ; 
use App\Models\RejectReason ; 
use App\Models\RejectLog ; 
use App\Models\RejectLogReason ; 
use App\Models\AppSteps ; 
use App\Models\AppLog ;
use App\Models\AppReceive ; 
use App\Models\ForeignPass;
use App\Models\EntryType;
use App\Models\VisaDuration;
use App\Models\MoneyReceipt ;
use App\Models\CurrencyRate ; 
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
use App\Services\ActionExceptionService;


class  WebfileEditController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $perPage = $request->input('per_page', 10);
        $search = $request->input('search');

        try {
            // dd('ok') ; 
            $cnt = AppReceive::first() ; 
             // dd('ok') ; 
            return view('pages.WebfileReplace.index', [
                            'count' => 0,
                             'smsg' => 'Please scan webfile...',
                        ]);
     
        } catch (Exception $e) {
             // dd($e->getMessage()); 
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function edit($id)
    {
        // dd($id) ; 
        $data = AppReceive::where('id', $id)->first() ; 
        // dd($data) ; 
        $centerId = auth()->user()->centerId;

        // $cenId = auth()->user()->centerId ; 
        if($data->centerId != $centerId ){
            return redirect()->route('searchWebfile.index')->with('error', "Cannot edit other center data");
        }
        else{

            if ($data->remarks === 'FOREIGN') {
                $fpdata = ForeignPass::where('web_ref', $data->id)->first();
            }
            else{
                 $fpdata = [] ; 
            }
            $visaType = VisaType::select('id','visa_type')->get() ; 
            $stickers = StickerMap::select('id','sticker')->get() ; 

            $lastBook = MoneyReceipt::latest('id')->value('BookNo') ?? 0;
            $visaDuration = VisaDuration::where('status',1)->get() ;
            $entrytype = EntryType::where('status',1)->get() ;
            $currencyRate = CurrencyRate::latest()->first();

            return view('pages.WebfileReplace.webedit', compact( 'visaType',  'data','stickers','fpdata','lastBook','visaDuration','entrytype','currencyRate'));
        }
       
    }


    public function update(Request $request)
    {
        // dd($request->all());
        // $request->merge([
        //     'remarks' => strtoupper(trim($request->remarks))
        // ]);
           $request->validate([
            'Webfile' => 'required|string',
            'name' => 'required|string',
            'passport' => 'required|string',
            'visatype' => 'required|numeric',
            'stickerNo' => 'required|string',
            'stickertype' => 'required|numeric',
            'contact' => 'required|string',
            'recId' => 'required|numeric',
            'corrfee' => 'required|numeric',
            'remarks' => 'required|string',

            'gratis'      => 'nullable|numeric|required_if:remarks,FOREIGN',
            'nationality' => 'nullable|string|required_if:remarks,FOREIGN',
            'duration'    => 'nullable|numeric|required_if:remarks,FOREIGN',
            'entryType'   => 'nullable|numeric|required_if:remarks,FOREIGN',
            'BookNo'      => 'nullable|numeric|required_if:remarks,FOREIGN',
            'RecptNo'     => 'nullable|numeric|required_if:remarks,FOREIGN',
            'rupee_rate'  => 'nullable|numeric|required_if:remarks,FOREIGN',
            'visafee'     => 'nullable|numeric|required_if:remarks,FOREIGN',
            'icwf'        => 'nullable|numeric|required_if:remarks,FOREIGN',
            'faxcharge'   => 'nullable|numeric|required_if:remarks,FOREIGN',
            'visaApp'     => 'nullable|numeric|required_if:remarks,FOREIGN',
            'totalfee'    => 'nullable|numeric|required_if:remarks,FOREIGN',
        ]);
            
       $request->merge([
            'remarks' =>'WEBFILE REPLACED:'.strtoupper(trim($request->remarks))
        ]);
         // dd($request->all()); 
        $apprec = AppReceive::find($request->recId);
        $oldWeb=  $apprec->Webfile ; 
        if ($apprec) {
             $cenId = auth()->user()->centerId ; 
            if($apprec->centerId != $cenId ){
                  return redirect()->route('web-replace.index')->with('error', "Cannot edit other center data");
            }
            else{
                $apprec->Webfile = $request->Webfile;
                $apprec->ApplicantName = $request->name;
                $apprec->passport = $request->passport;
                $apprec->visatype = $request->visatype;
                $apprec->stickertype = $request->stickertype;
                $apprec->stickerNo = $request->stickerNo;
                $apprec->contact = $request->contact;
                $apprec->corrFee = $request->corrfee;
                $apprec->updated_at = now(); // automatically handled but you can set manually
                $apprec->save(); // Fires 'updated' event → Loggable works
 
                ActionExceptionService::store([
                    'module'      => 'WebfileEdit',
                    'action'      => 'WebfileEdit',
                    'centerId'      => $cenId ,
                    'remarks'     =>'Old Webfile: '.$oldWeb.' Updated to Webfile No: '.$request->Webfile, 
                ]);

                return redirect()->route('web-replace.index')->with('success', 'Data updated successfully');
             }  
        }
        else{
              return redirect()->route('web-replace.index')->with('error', "no data found!!!");
        }
    }

    public function destroy($id){
        // dd($id) ;

        $apprec = AppReceive::find($id); 
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
                     $deLog = AppLog::where('web_ref', $id)->delete() ; 
                     $deLog2 = ForeignPass::where('web_ref', $id)->delete() ; 
                     if($apprec->payId){
                         $is_update = SslAptList::find($apprec->payId)->update([
                                    'status'       =>  1, 
                                    'updated_at'=>Date('Y-m-d H:i:s')
                                ]);
                        }
                        else{
                             $is_update = SslAptList::where('WebFile_no',$apprec->Webfile)->update([
                                        'status'       =>  1, 
                                        'updated_at'=>Date('Y-m-d H:i:s')
                                    ]);
                        }

                    if($apprec->codeId){
                        QueueCode::where('id', $apprec->codeId)
                                ->update([
                                    'status'     => 1,
                                    'updated_at' => now()
                                ]);
                    }
       
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
            $web =  $request->web ; 
            $web = str_replace(' ', '', $web);
            if(substr($web, 0, 3)== 'BGD'){

                $region = Region::select('id')->where('region_text',substr($web, 0, 4))->orderby('id', 'desc')->first() ; 

                $record = AppReceive::select('id','centerId','regionId','contact','Webfile','stepId')->where('regionId',  $region->id)->where('Webfile',$web)->first() ; 
            }
            else if(substr($web, 0, 1)== '@'){
                 $record = AppReceive::select('id','centerId','regionId','contact','Webfile','stepId')->where('stickerNo',$web)->first() ; 
            }
            else{
                $record = AppReceive::select('id','centerId','regionId','contact','Webfile','stepId')->where('passport',$web)->first() ; 
            }
             // $reply  = $record ; 
            // dd($record->stepId ) ; 
           if($record){
             if($record->stepId == 2){
                if($cenId === $record->centerId){
                    $user =  auth()->user()->id ; 
                    $latest = AppLog::with(['webref' => function ($query) {
                    $query->select('id', 'Webfile', 'passport', 'ApplicantName', 'contact','stickerNo','stickertype','visatype')->with('sticker:id,sticker')->with('visa:id,visa_type'); }])
                            ->where('web_ref', $record->id)
                            ->where('stepId',1)
                            ->get();
                        $smsg = "successfully retrievd data..." ; 
                        $success = true ; 
                }
                else{
                    $smsg = "Cannot Receive Other Center Passport..." ; 
                    $success = false ; 
                }


                
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
                        $smsg = "passport Already at center" ;
                    }
                    else if($record->stepId==5){
                        $smsg = "passport Already Delivered" ;
                    }
                    // dd($smsg) ; 
                    $success = false ; 
              }
            }
            $count = 0; 

            // dd($latest) ; 
           // $count = AppLog::where('Date', date('Y-m-d'))->where('created_by', $user )->where('stepId',4)->count() ; 
    

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




    public function print($id)
    {
        // dd($id) ; 
        $data = AppReceive::findOrFail($id);
        $cenId = auth()->user()->centerId ; 
       if($data->centerId != $cenId){
           return view('pages.error.index', [
                        'error' => 'Cannot print other center receipt',
                    ]);  
       }
       else{
           if($data->remarks=="Manual Entry"){
                return view('pages.error.index', [
                        'error' => 'Manual Entry Cannot print receipt ',
                    ]);  
           }
           // else if($data->Date != date('Y-m-d')){
           //       return view('pages.error.index', [
           //              'error' => 'Cannot Print Old Receipt',
           //          ]);  
           // }
           // else if($data->stepId != 1){
           //       return view('pages.error.index', [
           //              'error' => 'Cannot Print Old Receipt',
           //          ]);  
           // }
           else{
                // dd($data) ; 
               $codA = QueueCode::select('random')
                        ->where('Date',date('Y-m-d'))
                        ->where('centerId',$data->centerId)
                        ->where('web',$data->Webfile)
                        ->orderBy('id','desc')->first() ; 
            
                return view('pages.AppReceive.receipt_print', [
                    'datas' =>  $data,
                    'code' => $codA->random ?? 'N/A',
                ]);
           }
       }





        // return view('app-receive.print', compact('data'));
    }


    public function updatetdd(Request $request)
    {
        $message = '' ; 
        $updatetdd = VisaTypeTdd::max('tdd_update');

        // if (!$updatetdd || date('Y-m-d', strtotime($updatetdd)) != date('Y-m-d')) {
            // Run your update logic
            try{
                $tkn = 1 ; 
                $tokens = DB::select('CALL GetDelDate(?)', [$tkn]);
                // dd($tokens) ;        
                $message = 'Updated TDD'; 

             } catch (Exception $e) {
                  $message ="error".$e->getMessage(); 
            }
        // }
        // else{
        //     $message = 'TDD Already Updated'; 
        // }

        return response()->json([
                    'found' => true,
                    'token' => $updatetdd,
                    'message' => $message,
                ]);

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
