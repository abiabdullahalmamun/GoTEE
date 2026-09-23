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
use App\Jobs\SendSmsDCJob;
// use App\Imports\WebFileImport; 
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;


class  Send2HCIController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        // dd($request) ; 
        $perPage = $request->input('per_page', 10);
        $search = $request->input('search');

        try {
            $cenId = auth()->user()->centerId ; 
            // dd($cenId) ; 

            if($cenId){
                $cen = Center::select('region_id')->where('id',$cenId)->orderby('id','desc')->first();
                // dd($cen->region_id) ;
                $centerList = Center::select('center_name','id')->where('region_id',$cen->region_id )->where('status',1)->orderBy('center_name','asc')->get();

                  return view('pages.Send2HCI.index', [
                        'centerList' => $centerList,
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


    public function getSend2hciList(Request $request)
    {
        $centerId = $request->input('centerId');
        $date = $request->input('date');

    // public function getSend2hciList($centerId)
    // {
        // dd($centerId) ;
        $dataList = AppReceive::select('Webfile','id')->where('centerId',$centerId)->where('Date',  $date)->where('stepId',1)->orderBy('Webfile','asc')->get() ; 

        // Return JSON
        return response()->json($dataList);
    }

   
 

    /**
     * Store a newly created resource in storage.
     */ 
    public function store(Request $request)   //: RedirectResponse  
    {    //: RedirectResponse    //StoreAppReceiveRequest
       // dd($request->all());
        try {

            $ids = $request->input('selected_items');

            if (!$ids) {
                return redirect()->route('app-send2hci.index')->with('error', "No items selected.");
            }

            DB::transaction(function () use ($ids) {

                // 1. Update AppReceive in batch
                AppReceive::whereIn('id', $ids)->update([
                    'stepId' => 2,
                    'updated_at' => now(),
                ]);

                // 2. Get all necessary data in one query
                $records = AppReceive::whereIn('id', $ids)
                    ->get(['id', 'regionId', 'centerId', 'contact', 'Webfile']);

                // 3. Prepare batch insert for AppLog
                $logs = $records->map(function ($record) {
                    return [
                        'regionId'   => $record->regionId,
                        'centerId'   => $record->centerId,
                        'Date'       => now()->toDateString(),
                        'web_ref'    => $record->id,
                        'stepId'     => 2,
                        'remarks'    => '',
                        'created_by' => auth()->id(),
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                })->toArray();

                // Use upsert to avoid duplicates
                AppLog::upsert(
                    $logs,
                    ['regionId', 'web_ref', 'stepId'],
                    ['updated_at', 'created_by']
                );

                $smsLogs = [];

                foreach ($records as $record) {
                    $text = 'Your visa application Webfile:' . $record->Webfile .
                            ' has been sent to HCI/AHCI. To check status, please visit passtrack.net';

                    $smsLogs[] = [
                        'Date'      => now()->toDateString(),
                        'centerId'  => $record->centerId,
                        'type'      => 2,
                        'contact'   => $record->contact,
                        'text'      => $text,
                        'lang'      => 1,
                        'webref'    => $record->id,
                        'created_at'=> now(),
                        'updated_at'=> now(),
                    ];
                }

                // 1. Insert logs
                SmsLog::insert($smsLogs);

                // 2. Fetch back with IDs
                $insertedLogs = SmsLog::whereIn('webref', collect($smsLogs)->pluck('webref'))
                                      ->where('type',2)  
                                      ->where('Date', now()->toDateString()) // optional: safety
                                      ->get();

                // 3. Dispatch jobs with log IDs
                foreach ($insertedLogs as $log) {
                    SendSmsDCJob::dispatch($log->contact, $log->text, $log->id);
                }


 
            });

            // Return success message
            return redirect()->route('app-send2hci.index')
                ->with('success', count($ids) . " Data sent to HCI successfully.");

           
        } catch (Exception $e) {
            $mess = $e->getMessage(); 
            // dd($mess) ; 
           
            return redirect()->route('app-send2hci.index')->with('error', 'Insert failed '.$mess);
           
        }
    }


}
