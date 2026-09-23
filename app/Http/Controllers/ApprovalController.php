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

// use App\Imports\WebFileImport; 
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;

class  ApprovalController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $perPage = $request->input('per_page', 10);
        $search = $request->input('search');

        try {
             $count = 0; 
            // $stickerTypeList = StickerMap::select('sticker','centerId','id',)->orderBy('sticker','asc')->get();
            // $VisaType = VisaTypeApt::select('visa_type','id' )->where('status',1)->orderBy('visa_type','asc')->get() ; 
            // $centerList = Center::select('center_name','id')->where('status',1)->orderBy('center_name','asc')->get();

           return view('pages.AdminApprove.index', [
                 'count' => $count,
                  'smsg' => '',
                // 'VisaType' => $VisaType,
                // 'centerList' => $centerList,
            ]);
     
        } catch (Exception $e) {
             // dd($e->getMessage()); 
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

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
            
             $user = auth()->user()->id ;
             if($request->filled('act') && $request->filled('fromDate'))
             { 
     
                if($request->act==2){
                    $latest = AptOverride::with(['visa:id,visa_type', 'user:id,name', 
                        'userApp:id,name','center:id,center_name'])
                    ->select('id', 'WebFile_no', 'Date', 'remarks', 'centerId','visatypeId', 'created_at','created_by','active','approvedBy','approvedAt','svcId')
                    ->where('Date', $request->fromDate ?? date('Y-m-d'))
                    ->orderBy('id','desc')
                    ->get();


                    $count = AptOverride::where('Date',$request->fromDate ?? date('Y-m-d'))->count()  ;   
                    $success = true ;  

                }
                else{
                    $latest = AptOverride::with(['visa:id,visa_type', 'user:id,name', 
                        'userApp:id,name','center:id,center_name'])
                    ->select('id', 'WebFile_no', 'Date', 'remarks', 'centerId','visatypeId', 'created_at','created_by','active','approvedBy','approvedAt','svcId')
                    ->where('active', $request->act)
                    ->where('Date', $request->fromDate ?? date('Y-m-d'))
                    ->orderBy('id','desc')
                    ->get();

                    $count = AptOverride::where('Date',$request->fromDate ?? date('Y-m-d'))      
                                ->where('active',  $request->act)
                                  // ->where('approvedBy',$user)
                                ->count()  ;   
                    $success = true ;  
                }
       
             }
             else{
              $latest = [] ; 

                 $smsg ="No Record Available" ;    
                $count = 0 ; 
 
                $success = true ; 
             }
             $smsg=$request->fromDate ; 
        
            return response()->json([
                        'reply' => $reply ,
                        'data' => $record ,
                         'actt' => $request->act,
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
       $user =  auth()->user()->id ; 
       $is_update = AptOverride::find($id)->update([
                    'active'       =>  1,     
                    'approvedBy'       => $user, 
                    'approvedAt'       =>  Date('Y-m-d H:i:s'), 
                    'updated_at'=>Date('Y-m-d H:i:s')
                ]);

      
       $latest = AptOverride::with(['visa:id,visa_type', 'user:id,name', 'userApp:id,name','center:id,center_name'])
                ->select('id', 'WebFile_no', 'Date', 'remarks', 'centerId','visatypeId', 'created_at','created_by','active','approvedBy','approvedAt','svcId')
                ->where('active', 0)
                ->where('Date', $request->fromDate ?? date('Y-m-d'))
                ->orderBy('id','desc')
                ->get();
          $count = AptOverride::where('Date',$request->fromDate ?? date('Y-m-d'))      
                                ->where('active',  0)
                                ->count()  ;   
     
        return response()->json([
                        'success' => true,
                        'message' => "Data Updated successfully", 
                        'count' => $count, 
                        'latest' => $latest,
                    ]);
    }
 


}
