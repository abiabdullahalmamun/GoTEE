<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUndeliveredAdjustRequest;
// use App\Http\Requests\UpdateRegionRequest;
use App\Models\Region;
use App\Models\Center;
use App\Models\VisaType;
use App\Models\VisaTypeApt;
use App\Models\AptOverride;
use App\Models\StickerMap;
use App\Models\AppReceive;
use App\Models\AppLog ;
use App\Models\UndelPass;
use App\Imports\undelPassImport; 

use App\Jobs\UndeliveryJob;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;

class AdjustUndeliveredController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $perPage = $request->input('per_page', 10);
        $search = $request->input('search');

        try {
            $centerList = [] ; 
             $cenId = auth()->user()->centerId ; 
              $role = auth()->user()->role_id ;    
             // dd($cenId) ; 
             if($role=== 5 ||  $role=== 1 ){
                $centerList = Center::select('center_name','id')->where('status',1)->orderBy('center_name','asc')->get();
             }
             else{
                 $centerList = Center::select('center_name','id')->where('id',$cenId)->orderBy('center_name','asc')->get();
             }

    
           return view('pages.AdjUndelPass.index', [
                'centerList' => $centerList,
                 'cenId' => $cenId,
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
    public function store(StoreUndeliveredAdjustRequest $request) : RedirectResponse
    {
       // dd($request->all());
       // dd($request->file('import_file'));

        try {
            $count =0 ; 
            $noexist ='';
            $data = $request->validated();
            // dd($data) ;
            $cenId = $request->input('centerId');
            if(!$cenId){
                $cenId = auth()->user()->centerId ; 
            }
         
            $import = new undelPassImport;
            Excel::import($import, $request->file('import_file'));

            $passports = [];
            foreach ($import->rows as $row) {
                $value = trim($row[0]);

                if ($value) {
                    // Remove ALL spaces inside the string
                    $value = str_replace(' ', '', $value);
                    $passports[] = $value;
                }
            }

            // remove empties & duplicates and reindex
            $passports = array_values(array_unique(array_filter($passports)));

            // dd($passports) ; 
           $receiveIds = AppReceive::where('centerId', $cenId)->where('stepId',4)->pluck('id')->toArray();
         
           $is_update = AppReceive::where('centerId',$cenId)->whereNotIn('passport', $passports)->where('stepId', 4)->update([
                                'stepId'       =>  5, 
                                'updated_at'=>Date('Y-m-d H:i:s')
                            ]);
           $data = [];
           $userId = auth()->id();
           $reg = Center::select('region_id')->where('id',$cenId)->first() ; 
            foreach ($receiveIds as $rid) {
                $data[] = [
                    'regionId'   => $reg->region_id,
                    'centerId'   => $cenId,
                    'Date'       => date('Y-m-d'),
                    'web_ref'    => $rid,   // ← store the primary key here
                    'stepId'     => 5,
                    'remarks'    => 'ForceBySystem',
                    'created_by' => $userId,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }

            AppLog::upsert(
                $data,
                ['regionId', 'web_ref', 'stepId'], // unique keys
                ['updated_at', 'created_by', 'remarks']  
            );
            // dd('okk') ; 
            UndelPass::where('centerId', $cenId)->delete();

            $existingPassports = AppReceive::whereIn('passport', $passports)
                ->where('stepId', 4)
                ->where('centerId', $cenId)
                ->pluck('passport')
                ->toArray();

            $newPassports = array_diff($passports, $existingPassports);
            // dd('ok') ; 
      
            $insertData = [];
            foreach ($newPassports as $value) {
                $insertData[] = [
                    'passport'   => $value,
                    'centerId'   => $cenId,
                    'Date'       => now()->format('Y-m-d'),
                    'status'     => 1,
                    'app_ref'    => null,
                    'created_at' => now(),
                    'updated_at' => now(),
                    'created_by' => auth()->id(),
                ];
            }


            // Bulk insert (ignores duplicates if passport already exists)
            UndelPass::upsert($insertData, ['passport'], [
                'centerId', 'Date', 'status', 'app_ref', 'updated_at', 'created_by'
            ]);

            foreach ($passports as $passport) {
                UndeliveryJob::dispatch($passport);
            }

            $count = count($insertData) ; 
            // dd($count) ; 
           return redirect()->route('adjust-undelivered.index')->with('success',$count.' Data Saved'); 
              
        } catch (Exception $e) {
            $mess = $e->getMessage(); 
            dd($mess) ; 
           return redirect()->route('adjust-undelivered.index')->with('error', 'Appointment Already Exists'.$mess);
           
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
