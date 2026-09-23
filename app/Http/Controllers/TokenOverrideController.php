<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTokenTypeChangeRequest;
use App\Http\Requests\UpdateRegionRequest;
use App\Models\Region;
use App\Models\Center;
use App\Models\VisaType;
use App\Models\VisaTypeApt;
use App\Models\AptOverride;
use App\Models\StickerMap;
use App\Models\Service;
use App\Models\QueueCode;
use App\Models\TokenLog;
use App\Models\CurrentQueue;
use App\Services\ActionExceptionService;
// use App\Imports\WebFileImport; 
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;

class  TokenOverrideController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $perPage = $request->input('per_page', 10);
        $search = $request->input('search');

        try {
            // $stickerTypeList = StickerMap::select('sticker','centerId','id',)->orderBy('sticker','asc')->get();
            $svcType = Service::select('id','service_name')->where('status',1)->orderby('id','desc')->get() ; 
            // $VisaType = VisaTypeApt::select('visa_type','id' )->where('status',1)->orderBy('visa_type','asc')->get() ; 
            // $centerList = Center::select('center_name','id')->where('status',1)->orderBy('center_name','asc')->get();

           return view('pages.ChangeTokenType.index', [
                // 'VisaType' => $VisaType,
                'svcType' => $svcType,
            ]);
     
        } catch (Exception $e) {
             // dd($e->getMessage()); 
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

  
    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreTokenTypeChangeRequest $request): RedirectResponse
    {
       // dd($request->all());
        try {

            $data = $request->validated();
            // dd($data) ; 
            // dd($st->sticker) ; 
            if($data['WebFile_no']){

                $codeDetail = QueueCode::where('web', $data['WebFile_no'])->where('Date', date('Y-m-d'))->orderby('id','desc')->first() ;

                if($codeDetail){
                     // dd($codeDetail) ; 

                     $update = QueueCode::where('id',$codeDetail->id )->update([
                            'svc' => $data['svcType'],
                            'status' => 1,
                            'updated_at' => now()
                        ]);

                     if($update){
                         $svcupdate = TokenLog::where('id',$codeDetail->svclogId )->update([
                            'token_svc_no' => $data['svcType'],
                            'updated_at' => now()
                        ]);

                        $del = CurrentQueue::where('centerId',$codeDetail->centerId )->where('token_svc_no',$codeDetail->svc )->where('token_number',$codeDetail->token )->where('Date', date('Y-m-d'))->delete();

                        // dd($del) ; 
                           $doit = CurrentQueue::create([
                                'centerId'=> $codeDetail->centerId,
                                'Date'=> date('Y-m-d'),
                                 'token_type'=> 3,
                                'token_number'=>$codeDetail->token ,
                                'token_svc_no'=>  $data['svcType'],
                                'created_at'=>Date('Y-m-d H:i:s'),
                                'updated_at'=>Date('Y-m-d H:i:s'),
                              ]);


                          $svcName = Service::select('service_name')->where('id',$data['svcType'])->first() ; 

                          $msg = $data['WebFile_no'].' Token No. '.$codeDetail->token.', Type: '.$svcName->service_name.'  Updated Successfully ' ;
                            ActionExceptionService::store([
                                'module'      => 'TokenQueue',
                                'action'      => 'TypeChange',
                                'centerId'      => $codeDetail->centerId,
                                'remarks'     =>  $msg.', remarks: '.$data['remarks'],
                            ]);
                           return redirect()->route('changeTokenType.index')->with('success', $msg ); 
                     }
                     else{
                         return redirect()->route('changeTokenType.index')->with('error','failed to update'); 
                     }
                }
                else{
                    return redirect()->route('changeTokenType.index')->with('error','Webfile not found today'); 
                }
               
            }
            else{

             if ($request->hasFile('import_file')) {
                $file = $request->file('import_file');
                // dd($file) ; 
                // Import and process file
                $rows = Excel::toArray([], $file)[0]; // Get first sheet
                $count = 0; 
                $msg = ''; 
                $failed = '';
                $invalid = '';
                    foreach ($rows as $index => $row) {
                        // Skip header if needed
                        // if ($index === 0 && !is_numeric($row[0])) continue;
                        if (empty($row[0])) continue;
                        
                        $web =  $row[0] ; 

                        $codeDetail = QueueCode::where('web', $web )->where('Date', date('Y-m-d'))->orderby('id','desc')->first() ;

                        if($codeDetail){
                            $update = QueueCode::where('id',$codeDetail->id )->update([
                                'svc' => $data['svcType'],
                                'status' => 1,
                                'updated_at' => now()
                            ]);

                            if($update){
                                 $svcupdate = TokenLog::where('id',$codeDetail->svclogId )->update([
                                    'token_svc_no' => $data['svcType'],
                                    'updated_at' => now()
                                ]);

                                $del = CurrentQueue::where('centerId',$codeDetail->centerId )->where('token_svc_no',$codeDetail->svc )->where('token_number',$codeDetail->token )->where('Date', date('Y-m-d'))->delete();

                                // dd($del) ; 
                                   $doit = CurrentQueue::create([
                                        'centerId'=> $codeDetail->centerId,
                                        'Date'=> date('Y-m-d'),
                                         'token_type'=> 3,
                                        'token_number'=>$codeDetail->token ,
                                        'token_svc_no'=>  $data['svcType'],
                                        'created_at'=>Date('Y-m-d H:i:s'),
                                        'updated_at'=>Date('Y-m-d H:i:s'),
                                      ]);


                                  $svcName = Service::select('service_name')->where('id',$data['svcType'])->first() ; 

                                  $msg = $web.' Token No. '.$codeDetail->token.', Type: '.$svcName->service_name.'  Updated Successfully ' ;
                                    ActionExceptionService::store([
                                        'module'      => 'TokenQueue',
                                        'action'      => 'TypeChange',
                                        'centerId'      => $codeDetail->centerId,
                                        'remarks'     =>  $msg.', remarks: '.$data['remarks'],
                                    ]);

                                    $count += 1 ; 
                                  
                             }
                             else{
                                 $failed = $failed.' '.$web ;   
                             }

                        }
                        else{
                            $invalid = $invalid.' '.$web ; 
                        }


                    }

                    return redirect()->route('changeTokenType.index')->with('success', 'Updated '.$count.' Failed: '.$failed.' invalid: '.$invalid);
                } 
                else {
                    return redirect()->route('changeTokenType.index')->with('error', 'No WebFile_no provided or file uploaded.');
                }


                 // return redirect()->route('apt-override.index')->with('success','Data Updated Successfully --------'); 
            }
              
        } catch (Exception $e) {
            $mess = $e->getMessage(); 
            // dd($mess) ; 
            if (str_contains($mess, 'Duplicate entry')) {
                 return redirect()->route('changeTokenType.index')->with('error', 'Appointment Already Exists');
            }
            else{
                return redirect()->route('changeTokenType.index')->with('error', 'Insert failed '.$mess);
            }
           
        }
    }


}
