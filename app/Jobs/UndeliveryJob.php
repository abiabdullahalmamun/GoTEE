<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\{InteractsWithQueue, SerializesModels};
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Models\Region;
use App\Models\Center;
use App\Models\VisaType;
use App\Models\VisaTypeApt;
use App\Models\AptOverride;
use App\Models\StickerMap;
use App\Models\AppLog ;
use App\Models\AppReceive ; 
use App\Models\User ; 
use App\Models\UndelPass;

class UndeliveryJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    // protected $phone;
    protected $passport;

    /**
     * Create a new job instance.
     */
    public function __construct($passport )
    {
        // $this->phone = $phone;
        $this->passport = $passport;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // Replace with your SMS sending logic (e.g., API call)
        // Example using HTTP client:
        try{
            // dd( $this->passport) ; 
 
            $data = array(
                'WebFile_no' => $this->passport ,
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
             // dd($message) ; 
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
                $vtt = VisaType::select('id')
                    ->whereRaw('? LIKE CONCAT(visa_type, "%")', [strtoupper($data['visa_type'])])
                    ->first();
                if($vtt){
                    $vtype = $vtt->id ; 
                }
                // dd($vtt) ; 
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
                 $stpId = 4 ; 
                $phone ='';
                if(strlen($data['contact'])==10){
                    $phone = '0'.$data['contact'] ; 
                }
                else{
                    $phone = $data['contact'] ; 
                }

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
                        'updated_at'=>$data['rec_cen_time']
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
                            'updated_at'=>$data['rec_cen_time']
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
                            'updated_at'=>$data['sent2hci_time']
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
                            'updated_at'=>$data['ready_gul_time']
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
                            'updated_at'=>$data['readycen_time']
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
                            'updated_at'=>$data['del2app_time']
                        ]
                    ],
                    ['regionId' , 'web_ref', 'stepId'], // Unique constraint keys
                    ['updated_at',  'created_by']); // 
                }

                if ($this->passport) {
                    UndelPass::where('passport', $this->passport)->delete();
                }
            }


        }
        catch(\Exception $e){
            dd($e->getMessage()) ; 
              Log::error('SMS sending failed: ' . $e->getMessage());
            throw $e; // rethrow if you want it to retry or fail visibly
        }

    }
}
