<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAptListImportRequest;
use App\Http\Requests\UpdateRegionRequest;
use App\Models\Region;
use App\Models\Center;
use App\Models\VisaType;
use App\Models\VisaTypeApt;
use App\Models\AptOverride;
use App\Models\StickerMap;
use App\Models\NicAptList;

// use App\Imports\WebFileImport; 
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;

class AptListImportController extends Controller
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
            $regionList = Region::select('region_name','id')->where('status',1)->orderBy('region_name','asc')->get();

           return view('pages.AptListImport.index', [
                'VisaType' => $VisaType,
                'regionList' => $regionList,
            ]);
     
        } catch (Exception $e) {
             // dd($e->getMessage()); 
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    /**
     * Store a newly created resource in storage.
     */       // :RedirectResponse
     // public function store(Request $request) 
    public function store(StoreAptListImportRequest $request) : RedirectResponse
    {
        // dd('ok') ; 
       // dd($request->all());
       // dd($request->file('import_file'));

        try {
            $count =0 ; 
            $data = $request->validated();
            // dd($data) ; 
            $file = $request->file('import_file');
            $contents = file_get_contents($file->getRealPath());
            $separator = 'BGDD!'; // <-- example custom separator
            $items = preg_split('/BGDD!|BGDK!|BGDC!|BGDR!|BGDS!/', $contents);
            // Convert to array
            // $items = explode($separator, $contents);

            // Optional: trim each item
            $items = array_map('trim', $items);
            // dd($items) ; 

            $batch = [];
            $prefixes = collect($items)
                ->map(fn($itm) => substr(explode('!', $itm)[0] ?? '', 0, 4))
                ->unique()
                ->filter()
                ->values()
                ->toArray();

            // Step 2: Get all regions matching these prefixes
            $regionMap = Region::whereIn('region_text', $prefixes)
                ->pluck('id', 'region_text')
                ->toArray(); // ['DHK1' => 1, 'CTG2' => 2, ...]

            foreach ($items as $itm) {
                 if (str_contains($itm, 'BGD')) {
                    $myrecord = explode('!', $itm);
                    // dd($myrecord) ; 

                    $reg_str = substr($myrecord[0], 0, 4);
                    // dd($reg_str) ; 
                    $regionId =1 ; 
                    // $region = Region::select('id')->where('region_text',$reg_str)->orderBy('id','desc')->first() ; 
                    // if($region){
                    //      $regionId = $region->id ; 
                    // }

                    if (isset($myrecord[19]) && $myrecord[25] === 'BGD') {
                        if (isset($myrecord[0], $myrecord[2], $myrecord[1], $myrecord[14], $myrecord[15], $myrecord[30])) {

                           $reg_str = substr($myrecord[0], 0, 4);
                            $regionId = $regionMap[$reg_str] ?? 1; // fallback to 1 if not found

                            $batch[] = [
                                'regionId' =>  $regionId,
                                'webfile' => $myrecord[0],
                                'passport' => $myrecord[2],
                                'nid' => $myrecord[22],
                                'Name' => $myrecord[15] . ' ' . $myrecord[14],
                                'contact' => $myrecord[30],
                                'dob' => $myrecord[17],
                                'profession' => $myrecord[109],
                                'gender' => $myrecord[16],
                                'district' => $myrecord[18],
                                'reg_date' => $myrecord[1],
                                'email' => $myrecord[32],
                                'created_by' => $data['created_by'],
                                'created_at' => now(),
                                'updated_at' => now(),
                            ];
                        }
                    }
                }
            }
            // dd($batch) ; 
            if (!empty($batch)) {
                // NicAptList::insert($batch);
                // NicAptList::upsert(
                //     $batch,
                //     ['webfile'],                
                //     ['updated_at', 'passport','Name','contact','reg_date']    
                // );  

                foreach (array_chunk($batch, 500) as $chunk) {
                    NicAptList::upsert(
                        $chunk,
                        ['webfile'],
                        ['updated_at', 'passport','Name','contact','reg_date', 'nid','profession','gender' , 'district', 'email' ,'dob' ]
                    );
                }

                 $count = count($batch);

            }

            // dd($count) ; 
           return redirect()->route('applicant_list_import.index')->with('success',$count.' Data Saved'); 
              
        } catch (Exception $e) {
            $mess = $e->getMessage(); 
            dd($mess) ; 
           return redirect()->route('applicant_list_import.index')->with('error', 'Appointment Already Exists');
           
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
