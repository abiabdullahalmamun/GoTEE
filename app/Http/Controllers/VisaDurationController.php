<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreVisaDurationRequest;
use App\Http\Requests\UpdateVisaDurationRequest;
use App\Models\VisaDuration;
use App\Models\Center;
use App\Models\Service;
use App\Models\DeviceSvc ; 
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class VisaDurationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        // $perPage = $request->input('per_page', 10);
        // $search = $request->input('search');
        // dd($search) ; 
        try {

            $query = VisaDuration::query()->latest('id');

        // dd($query) ; 
            $query = $query->paginate(500);
            $center = Center::select('id','center_name')->where('status',1)->orderby('center_name','asc')->get() ; 
           
           $service = Service::select('id', 'service_name')->where('status',1)->orderby('service_name','asc')->get() ; 
            // $states = [0 => 'LoggedOut'] + $svc->pluck('service_name', 'id')->toArray();
            return view('pages.visaDuration.index', with(['query' => $query, 'centerList' =>$center, 'serviceList' =>$service]));

            // return view('pages.devices.index', with(['query' => $query]));

        } catch (Exception $e) {
            // info('Error showing Floor!', [$e]);
             $mess = $e->getMessage(); 
             // dd($mess) ; 
            return redirect()->back()->with('error', 'device showing failed!.');
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreVisaDurationRequest $request): RedirectResponse
    {
       // dd($request->all());
        try {
 
            $data = $request->validated();
            // dd($data) ; 
      
            $save=VisaDuration::create([
                'name'     => $data['nameD'],
                'created_by'   => $data['created_by'],
                'status'   => $data['status'],
                'created_at'=> now(),
                'updated_at'=>now()
            ]);
            if($save){
                return redirect()->route('visaDuration.index')->with('success', 'Data Inserted successfully.');
            }
            else{
                 return redirect()->route('visaDuration.index')->with('error', 'Data Insert failed!.');
            }

        } catch (Exception $e) {
             // dd($e); 
    
              $mess = $e->getMessage(); 
            // dd($mess) ; 
            if (str_contains($mess, 'Duplicate entry')) {
                 return redirect()->route('visaDuration.index')->with('error', 'Device Already Exists');
            }
            else{
                return redirect()->route('visaDuration.index')->with('error', 'Insert failed '.$mess);
            }
        }
    }

    /**
     * Update the specified resource in storage.
     */
      // public function update(Request $request ) 
    public function update(UpdateVisaDurationRequest $request, VisaDuration $visaduration): RedirectResponse
    {
         // dd($request->all());
        try {
            $data = $request->validated();
            $visaD = VisaDuration::find($data['recId']);
            if($visaD){
                $visaD->name = $data['nameD'];
                $visaD->status = $data['status'];
                $visaD->updated_at = now(); 
                $visaD->save(); // Fires 'updated' event → Loggable works

                return redirect()->route('visaDuration.index')->with('success', 'Data Updated Successfully');
            }
            else{
                return redirect()->route('visaDuration.index')->with('error', 'Failed to Update');
            }
       
        } catch (\Exception $e) {
              $mess = $e->getMessage(); 
            // dd($mess) ; 
            if (str_contains($mess, 'Duplicate entry')) {
                 return redirect()->route('visaDuration.index')->with('error', 'visaDuration Already Exists');
            }
            else{
                return redirect()->route('visaDuration.index')->with('error', 'Insert failed '.$mess);
            }
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(VisaDuration $visaduration): RedirectResponse
    {
         // dd($visaduration->all());
        try {
          $dev = VisaDuration::find($visaduration->id); 
             if($dev){
                $dev->delete();
                return redirect()->route('visaDuration.index')->with('success', 'Data deleted successfully.');
             } 
             else{
                 return redirect()->route('visaDuration.index')->with('error', 'Data delete failed .');
             }
          
        } catch (\Exception $e) {
            info('Shop deleted failed!', [$e]);

            return redirect()->route('visaDuration.index')->with('error', 'Shop deleted failed!.');
        }
    }

 
}
