<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreFormsHCIRequest;
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
use App\Models\AppReceive; 
use App\Models\AppLog; 
use App\Models\AppDigitization ; 
use App\Services\ActionExceptionService;
use App\Jobs\ProcessDigitizationRecords ; 
use App\Jobs\ProcessDigitizationMasterJob; 
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\DB;
// use App\Imports\WebFileImport; 
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;
use Carbon\Carbon;
use App\Http\Controllers\DigitizationScanController;


class  Forms4mHCIController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        // dd('dfgj') ; 
        $perPage = $request->input('per_page', 10);
        $search = $request->input('search');

        try {
            // $stickerTypeList = StickerMap::select('sticker','centerId','id',)->orderBy('sticker','asc')->get();
            // $svcType = Service::select('id','service_name')->where('status',1)->orderby('id','desc')->get() ; 
            // $VisaType = VisaTypeApt::select('visa_type','id' )->where('status',1)->orderBy('visa_type','asc')->get() ; 
            $hciList = Region::select('region_name','id')->where('status',1)->orderBy('region_name','asc')->get();

           return view('pages.Forms4mHCI.index', [
                // 'VisaType' => $VisaType,
                // 'svcType' => $svcType,
                'hciList' => $hciList,
            ]);
     
        } catch (Exception $e) {
             // dd($e->getMessage()); 
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

  
    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreFormsHCIRequest $request): RedirectResponse
    {
       // dd($request->all());
        try {

            $data = $request->validated();
            // dd($data) ; 
            $invalid = '' ; 

             if ($request->hasFile('import_file')) {
                $file = $request->file('import_file');
                // dd($file) ; 
                // Import and process file
                $rows = Excel::toArray([], $file)[0]; // Get first sheet
                $rowsArray = [];
                $count = 0; 
                $msg = ''; 
                $failed = '';
                $invalid = '';
                  $successCount = 0;
                    $failed = '';

                if($data['filetype']==1){
                    // dd('typaaa') ; 
                    $allDatesEmpty = collect($rows)->every(function ($row) {
                        $dep = $row[1] ?? null;
                        $ret = $row[2] ?? null;
                        $dvd = $row[3] ?? null;

                        return !is_numeric($dep) && !is_numeric($ret) && !is_numeric($dvd);
                    });

                    if ($allDatesEmpty) {
                        return redirect()->route('receiveDocument4mhci.index')->with('error', 'Please provide a valid ICON file with deposite_date, return_date, dvd_date');
                    }

                    foreach ($rows as $index => $row) {
                        // Skip header if needed
                        // if ($index === 0 && !is_numeric($row[0])) continue;
                        if (empty($row[0])) continue;
                        if (substr($row[0], 0, 3) != 'BGD') continue;
                        
                        $rowsArray[] = [
                            'webfile'      => trim($row[0]),
                            'deposite_date'      => !empty($row[1]) ? Carbon::instance(ExcelDate::excelToDateTimeObject($row[1])) : null,
                            'return_date'      => !empty($row[2]) ? Carbon::instance(ExcelDate::excelToDateTimeObject($row[2])) : null,
                            'dvd_date'      => !empty($row[3]) ? Carbon::instance(ExcelDate::excelToDateTimeObject($row[3])) : null,
                            'created_at' => now(),
                            'updated_at' => now(),
                            'remarks' => $data['remarks'],
                            'regionId' => $data['regionId'],
                            'status' => 1,
                            'created_by'=> auth()->user()->id ,
                            'Date' =>  $data['date'],
                        ];
                        
                    }
                    // dd( $rowsArray) ; 
                    $count += count($rowsArray) ;  
                    if($count>15000){
                         return redirect()->route('receiveDocument4mhci.index')->with('error', 'Cannot upload file with more than 15000 records!!!');
                    }

                    // collect($rowsArray)->chunk(5000)->each(function ($chunk) {
                    //     AppDigitization::upsert(
                    //         $chunk->toArray(),
                    //         ['webfile'],
                    //         ['deposite_date','return_date','dvd_date','created_at','updated_at','remarks','regionId', 'status',  'created_by']
                    //     );
                    // });
                    collect($rowsArray)->chunk(500)->each(function ($chunk) {

                        $data = $chunk->map(function ($row) {

                            return [
                                'webfile' => $row['webfile'],

                                // REQUIRED column (fix for your error)
                                'Date' => $row['Date'] ?? now()->format('Y-m-d'),
                                 'regionId' => $row['regionId'],
                                 'status' => 1,
                                // only update when valid
                                'dvd_date' => !empty($row['dvd_date']) ? $row['dvd_date'] : null,
                                 'created_by'=> auth()->user()->id ,
                                'created_at' => now(),
                                'updated_at' => now(),
                            ];
                        })->toArray();

                        AppDigitization::upsert(
                            $data,
                            ['webfile'],
                            ['dvd_date', 'updated_at', 'status']
                        );

                    });

                    $successCount =   $count ; 

                }
                elseif($data['filetype']==2){
                    // foreach ($rows as $row) {
                    $errors = [];
                    $controller = app(DigitizationScanController::class);
          
                    // DB::transaction(function () use ($rows, $controller) {
                        
                        foreach ($rows as $row) {

                            if (empty($row[0])) continue;
                            if (substr($row[0], 0, 3) != 'BGD') continue;

                            $request = new Request([
                                'web' => $row[0],  
                            ]);

                            $result = $controller->xlsstore($request);

                            // dd($result) ; 
                            if ($result['success']) {
                                $successCount++;
                            } else {
                                $failed = $failed.','.$row[0] ;
                                $errors[] = [
                                    'webfile' => $row[0],
                                    'message' => $result['message'],
                                ];
                            }
                        }
                    // });

                    // dd($successCount) ; 
                }



                // ProcessDigitizationMasterJob::dispatch();

                return redirect()->route('receiveDocument4mhci.index')->with('success', 'Uploaded '.$successCount.' Records, Failed'.$failed);
            } 
            else {
                return redirect()->route('receiveDocument4mhci.index')->with('error', 'No file uploaded.');
            }
      
              
        } catch (Exception $e) {
            $mess = $e->getMessage(); 
            // dd($mess) ; 
            if (str_contains($mess, 'Duplicate entry')) {
                 return redirect()->route('receiveDocument4mhci.index')->with('error', 'Data Already Exists');
            }
            else{
                return redirect()->route('receiveDocument4mhci.index')->with('error', 'Insert failed '.$mess);
            }
           
        }
    }


}
