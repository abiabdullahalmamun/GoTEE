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
use App\Models\AppLog ;
use App\Models\AppReceive ; 
use App\Models\User ; 
// use App\Imports\WebFileImport; 
use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;

class PasstrackController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function AppReceive($r){
        dd($r) ; 
    }

    public function getPassTrackData($r){
        // dd($r) ; 

        try{
        $webfile_no = '';
        $data = array(
            'WebFile_no' =>$r['val'] ,
        );

       $response = Http::withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
        ])->post('http://passtrack.net/passtrack/api/getWebRecord.php', $data);

        $status = $response->status();
        $body = $response->body();

        // Force decode JSON
        $data = json_decode($body, true);
        // dd($data) ; 
        $message = $data['message'] ; 
        if($data['message'] === 'SUCCESS' && $data['center'] !== null){
            $webfile_no = $data['webfile_no'] ;
            $rgstr = substr($webfile_no, 0, 4) ; 
            // dd($rgstr) ; 
            $reg = Region::select('id')->where('region_text',$rgstr)->first() ; 

            $center = $data['center'] ; 
            
            $cenId = Center::select('id')->where('center_name', strtoupper($center))->first() ; 
         
            $sttype = null ; 
            $stt = StickerMap::select('id')->where('sticker',strtoupper($data['sticker_type']))->first() ; 
            if($stt){
                $sttype = $stt->id ; 
             }

             $vtype = null ; 
             // $vtt = VisaType::select('id')->where('visa_type',strtoupper($data['visa_type']) )->first() ; 
            // dd($data['visa_type']) ; 
            $vtt = VisaType::select('id')
                ->whereRaw('? LIKE CONCAT(visa_type, "%")', [strtoupper($data['visa_type'])])
                ->first();

             // $vtt = VisaType::select('id')
             //    ->where('visa_type', 'LIKE', strtoupper($data['visa_type']) . '%')
             //    ->first();
              if($vtt){
                $vtype = $vtt->id ; 
             }

             $uid  = null ; 
             $uuid = User::select('id')->where('name','LIKE', "{$data['rec_cen_by']}%" )->first() ; 
             if($uuid){
                $uid  = $uuid->id ; 
             }

             if(strtoupper($data['Pmethod']) == 'ONLINE'){
                $pm = 1 ; 
             }
             else if(strtoupper($data['Pmethod']) == 'CASH'){
                $pm = 2 ; 
             }
             else if (strtoupper($data['Pmethod']) == 'WAIVE'){
                $pm = 3 ; 
             }
             else{
                 $pm = 2 ; 
                 $data['Remarks'] = $data['Remarks']."No paymethod PassTrack" ; 
             }

             if($data['corrFee']==""){
                $data['corrFee'] = 0 ; 
             }

            $stpId = 1 ; 

            if (filled($data['rec_cen_time'])) {
                $stpId = 1 ;   
            }
            
            if (filled($data['sent2hci_time'])) {
                $stpId = 2 ;   
            }
            
            if (filled($data['ready_gul_time'])) {
                $stpId = 3 ;   
            }
            
            if (filled($data['readycen_time'])) {
                $stpId = 4 ;   
            } 

            if (filled($data['del2app_time'])) {
                $stpId = 5 ;   
            }

            $phone ='';
            if(strlen($data['contact'])==10){
                $phone = '0'.$data['contact'] ; 
            }
            else{
                $phone = $data['contact'] ; 
            }

            // dd($stpId) ; 
            // dd($webfile_no) ; 
             $record = AppReceive::updateOrCreate(
                    ['regionId' => $reg->id, 'Webfile' => $webfile_no], // Matching condition
                    [
                        'centerId'     => $cenId->id,
                        'Date'         => $data['rec_date'],
                        'ApplicantName'=> $data['applicant_name'],
                        'passport'     => $data['passport'],

                        'stickertype'  => $sttype,
                        'stickerNo'    => $data['sticker_no'],
                        'status'       => 1,
                        'contact'      =>  $phone,

                        'visatype'     => $vtype,
                        'pmethod'      => $pm,
                        'txn'          => $data['ucashTxn'] ,
                        'remarks'      => $data['Remarks'],
                        'psQty'        => $data['psQty'],
                        'corrFee'      => $data['corrFee'],
                        'bioType'      => null,
                        'stepId'       => $stpId ,
                        'cntNo'        => null,
                        'tknNo'        => null,
                        'created_by'   => $uid ,
                        'created_at'=>$data['rec_cen_time'],
                        'updated_at'=> now()
                    ]
                );
                $ids = $record->id;

                // dd($ids) ; 
                if (filled($data['rec_cen_time'])) {

                    $save = AppLog::upsert([
                        [
                            'regionId'     => $reg->id,
                            'centerId'     => $cenId->id,
                            'Date'  => date('Y-m-d', strtotime($data['rec_cen_time'])),
                            'web_ref'     => $ids   ,
                            'stepId'     => 1,
                            'remarks'     => '',
                            'created_by'=> $uid ,
                            'created_at'=>$data['rec_cen_time'],
                            'updated_at'=> now()
                        ]
                    ],
                    ['regionId' , 'web_ref', 'stepId'], // Unique constraint keys
                    ['updated_at',  'created_by']); // 
                }
                
                if (filled($data['sent2hci_time'])) {
                     $usid  = null ; 
                     $userid = User::select('id')->where('name','LIKE', "{$data['sent2hci_by']}%" )->first() ; 
                     if($userid){
                         $usid = $userid->id ; 
                     }

                     $save = AppLog::upsert([
                        [
                            'regionId'     => $reg->id,
                            'centerId'     => $cenId->id,
                            'Date'  => date('Y-m-d', strtotime($data['sent2hci_time'])),
                            'web_ref'     => $ids  ,
                            'stepId'     => 2,
                            'remarks'     => '',
                            'created_by'=> $usid ,
                            'created_at'=>$data['sent2hci_time'],
                            'updated_at'=> now()
                        ]
                    ],
                    ['regionId' , 'web_ref', 'stepId'], // Unique constraint keys
                    ['updated_at',  'created_by']); // 
                }
                
                if (filled($data['ready_gul_time'])) {
                     $usid  = null ; 
                     $userid = User::select('id')->where('name','LIKE', "{$data['ready_gul_by']}%" )->first() ; 
                     if($userid){
                         $usid = $userid->id ; 
                     }

                     $sstp = 3 ; 
                     if($data['status']=='AcceptedF'){
                        $sstp = 31 ; 
                     }
                     else if($data['status']=='RejectedF'){
                        $sstp = 32 ; 
                     }
                     else if($data['status']=='DirectHci'){
                         $sstp = 33 ; 
                     }


                     $save = AppLog::upsert([
                        [
                            'regionId'     => $reg->id,
                            'centerId'     => $cenId->id,
                            'Date'   =>date('Y-m-d', strtotime($data['ready_gul_time'])),
                            'web_ref'     => $ids   ,
                            'stepId'     => $sstp,
                            'remarks'     => '',
                            'created_by'=> $usid ,
                            'created_at'=>$data['ready_gul_time'],
                            'updated_at'=> now()
                        ]
                    ],
                    ['regionId' , 'web_ref', 'stepId'], // Unique constraint keys
                    ['updated_at',  'created_by']); // 
                }
                
                if (filled($data['readycen_time'])) {

                     $usid  = null ; 
                     $userid = User::select('id')->where('name','LIKE', "{$data['readycen_by']}%" )->first() ; 
                     if($userid){
                         $usid = $userid->id ; 
                     }
                     $save = AppLog::upsert([
                        [
                            'regionId'     => $reg->id,
                            'centerId'     => $cenId->id,
                            'Date'   =>date('Y-m-d', strtotime($data['readycen_time'])),
                            'web_ref'     => $ids   ,
                            'stepId'     => 4,
                            'remarks'     => '',
                            'created_by'=> $usid ,
                            'created_at'=>$data['readycen_time'],
                            'updated_at'=> now()
                        ]
                    ],
                    ['regionId' , 'web_ref', 'stepId'], // Unique constraint keys
                    ['updated_at',  'created_by']); // 
                } 

                if (filled($data['del2app_time'])) {

                     $usid  = null ; 
                     $userid = User::select('id')->where('name','LIKE', "{$data['del2app_by']}%" )->first() ; 
                     if($userid){
                         $usid = $userid->id ; 
                     }
                     $save = AppLog::upsert([
                        [
                            'regionId'     => $reg->id,
                            'centerId'     => $cenId->id,
                            'Date'   =>date('Y-m-d', strtotime($data['del2app_time'])),
                            'web_ref'     => $ids   ,
                            'stepId'     => 5,
                            'remarks'     => '',
                            'created_by'=> $usid ,
                            'created_at'=>$data['del2app_time'],
                            'updated_at'=> now()
                        ]
                    ],
                    ['regionId' , 'web_ref', 'stepId'], // Unique constraint keys
                    ['updated_at',  'created_by']); // 
                }
        }
      
           
        }   
        catch (Exception $e) {
            $mess = $e->getMessage(); 
            dd($mess) ;
        }
         return $webfile_no ;
    }

    public function AprOverride($r){
      
      $cen = Center::select('center_name')->where('id',$r['centerId'])->first() ; 
      $visa= VisaTypeApt::select('visa_type')->where('id',$r['visatypeId'] )->first() ; 
      $data = array(
            'Date' => $r['date'],
            'WebFile_no' =>$r['WebFile_no'],
            'visatype' => $visa->visa_type ,   //$r['visatypeId'] ,
            'center' =>$cen->center_name,  //    $r['centerId'] ,
            'remarks' => $r['remarks'] ,
            'created_by' =>  auth()->user()->name, 
            'type' => 0,
            'phone' =>  $r['phone'],
        );

       $response = Http::withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
        ])->post('http://148.66.132.140/passtrack/api/SaveAptOverride.php', $data);

        $status = $response->status();
        $body = $response->body();

        // Force decode JSON
        $data = json_decode($body, true);
        return $data ;

    }


    public function index(Request $request): View
    {
        $perPage = $request->input('per_page', 10);
        $search = $request->input('search');

        try {

            // $stickerTypeList = StickerMap::select('sticker','centerId','id',)->orderBy('sticker','asc')->get();
            $VisaType = VisaTypeApt::select('visa_type','id' )->where('status',1)->orderBy('visa_type','asc')->get() ; 
            $centerList = Center::select('center_name','id')->where('status',1)->orderBy('center_name','asc')->get();

           return view('pages.AptOverride.index', [
                'VisaType' => $VisaType,
                'centerList' => $centerList,
            ]);
     
        } catch (Exception $e) {
             // dd($e->getMessage()); 
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

            // Shop::create([
            //         'ShopName'=> $r->name,
            //         'Contact'=>$r->Contact,
            //         'message'=>$r->mess,
            //         'status'=>1,
            //         'created_by'=>Auth::user()->user_id,
            //         'created_at'=>Date('Y-m-d H:i:s'),
            //         'updated_at'=>Date('Y-m-d H:i:s')
            //     ]);
    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreAptOverrideRequest $request): RedirectResponse
    {
       // dd($request->all());
        try {

            $data = $request->validated();
            // dd($data) ; 
            // dd($st->sticker) ; 
            $selctedDate = Date('Y-m-d',strtotime($data['date']));

            if($data['WebFile_no']){
                AptOverride::create([
                    'Date'=>$selctedDate,
                    'WebFile_no'=>  $data['WebFile_no'],
                    'visatypeId'=> $data['visatypeId'],
                    'centerId'=> $data['centerId'],
                     'remarks'=> $data['remarks'],
                     'phone'=> $data['phone'],
                    'created_by'=> $data['created_by'],
                    'created_at'=>Date('Y-m-d H:i:s'),
                    'updated_at'=>Date('Y-m-d H:i:s'),
                ]);

                 return redirect()->route('apt-override.index')->with('success','Data Updated Successfully'); 
            }
            else{

             if ($request->hasFile('import_file')) {
                $file = $request->file('import_file');

                // Import and process file
                $rows = Excel::toArray([], $file)[0]; // Get first sheet
                $count = 0;
                    foreach ($rows as $index => $row) {
                        // Skip header if needed
                        // if ($index === 0 && !is_numeric($row[0])) continue;
                        if (empty($row[0])) continue;
                        AptOverride::create([
                            'Date' => $selctedDate,
                            'WebFile_no' => $row[0],  // Assuming WebFile_no is first column
                            'visatypeId' => $data['visatypeId'],
                            'centerId' => $data['centerId'],
                            'remarks' => $data['remarks'],
                            'phone' => $data['phone'],
                            'created_by' => $data['created_by'],
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);

                          $count++;
                    }

                    return redirect()->route('apt-override.index')->with('success', "File data inserted successfully. Imported {$count} records.");
                } 
                else {
                    return redirect()->route('apt-override.index')->with('error', 'No WebFile_no provided or file uploaded.');
                }


                 // return redirect()->route('apt-override.index')->with('success','Data Updated Successfully --------'); 
            }
              
        } catch (Exception $e) {
            $mess = $e->getMessage(); 
            // dd($mess) ; 
            if (str_contains($mess, 'Duplicate entry')) {
                 return redirect()->route('apt-override.index')->with('error', 'Appointment Already Exists');
            }
            else{
                return redirect()->route('apt-override.index')->with('error', 'Insert failed '.$mess);
            }
           
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateRegionRequest $request, Region $region): RedirectResponse
    {
         // dd($request->all());
        try {
            $data = $request->validated();
  // dd($data) ; 
           $is_update = Region::where('id',$data['recId'])->update([
                'region_name'   => $data['region_name'],
                'region_text'   =>  $data['region_text'], 
                'status'       =>  $data['status'], 
                'updated_at'=>Date('Y-m-d H:i:s')
            ]);

           if($is_update){
                return redirect()->route('regions.index')->with('success','Data Updated Successfully');    
            }
            else{
                 return redirect()->route('regions.index')->with('error','Failed to  Update');   
            }

        } catch (\Exception $e) {
             // dd($e); 
            info('Floor updated failed!', [$e]);

            return redirect()->route('regions.index')->with('error', 'Update failed!.'.$e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Region $region): RedirectResponse
    {
         // dd($region);
        try {
            $region->delete();

            return redirect()->route('regions.index')->with('success', 'Region deleted successfully.');

        } catch (\Exception $e) {
             $msss = $e->getMessage() ;
             if (str_contains($msss, 'a foreign key constraint fails')) {
                 return redirect()->route('regions.index')->with('error', 'a foreign key constraint fails.');
            }
            else{
                // dd($e->getMessage()); 
                return redirect()->route('regions.index')->with('error','Region Delete failed!.'.$msss);
            }


            info('Region deleted failed!', [$e]);
// dd($e->getMessage()); 
            return redirect()->route('regions.index')->with('error', 'Region deleted failed!.'.$e->getMessage());
        }
    }
}
