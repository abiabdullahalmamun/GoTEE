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

class  AptOverrideController extends Controller
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
                $save = AptOverride::create([
                        'Date'=>$selctedDate,
                        'WebFile_no'=>  $data['WebFile_no'],
                        'visatypeId'=> $data['visatypeId'],
                        'centerId'=> $data['centerId'],
                         'remarks'=> $data['remarks'],
                         'phone'=> $data['phone'],
                        'created_by'=> $data['created_by'],
                        'created_at'=>Date('Y-m-d H:i:s'),
                        'updated_at'=>Date('Y-m-d H:i:s'),
                        'svcId'=> $data['formtype'],
                        'active'=> 0 ,
                    ]);

                 if($save){
                    $PasstrackController = new PasstrackController();
                    $reply = $PasstrackController->AprOverride($data)  ; 
                    // dd($reply) ; 
                    return redirect()->route('apt-override.index')->with('success','Data Updated Successfully'); 
                 }
                 else{
                    return redirect()->route('apt-override.index')->with('error','failed to save'); 
                 }  
                
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
                        
                        $save = AptOverride::upsert([
                            [
                                'Date' => $selctedDate,
                                'WebFile_no' => $row[0],
                                'visatypeId' => $data['visatypeId'],
                                'centerId' => $data['centerId'],
                                'remarks' => $data['remarks'],
                                'phone' => $data['phone'],
                                'created_by' => $data['created_by'],
                                'created_at' => now(),
                                'updated_at' => now(),
                                 'svcId'=> $data['formtype'],
                                  'active'=> 0 ,
                            ]
                        ],
                        ['WebFile_no', 'Date'], // Unique constraint keys
                        ['visatypeId', 'centerId', 'remarks', 'phone', 'updated_at']); // Fields to update on conflict

                        // dd($save) ;     
                        if($save){
                            // dd('try') ; 
                             $data['WebFile_no'] = $row[0] ;
                             // dd($data) ; 
                            $PasstrackController = new PasstrackController();
                            $reply = $PasstrackController->AprOverride($data)  ; 
                            // dd($reply) ; 
                            $count++;
                        }
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


}
