<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreRejectReasonRequest;
use App\Http\Requests\UpdateDeviceRequest;
use App\Models\RejectReason;
use App\Models\Center;
use App\Models\Service;
use App\Models\DeviceSvc ; 
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class RejectReasonController extends Controller
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

            $query = RejectReason::query()->latest('id');
            $query = $query->paginate(500);
             return view('pages.reject_reasons.index', with(['query' => $query]));


        } catch (Exception $e) {
            // info('Error showing Floor!', [$e]);
             $mess = $e->getMessage(); 
             // dd($mess) ; 
            return redirect()->back()->with('error', 'data showing failed!.'.$mess);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreRejectReasonRequest $request): RedirectResponse
    {
       // dd($request->all());
        try {

            $data = $request->validated();
            // dd($data) ; 
         
            $save=RejectReason::create([
                'reason_name'     =>  $data['reasonName'],
                 'created_by'   => $data['created_by'],
                'status'   => $data['status'],
                'created_at'=> now(),
                'updated_at'=>now()
            ]);
            if($save){
               
                return redirect()->route('reject_reasons.index')->with('success', 'Data Inserted successfully.');
            }
            else{
                 return redirect()->route('reject_reasons.index')->with('error', 'Data Insert failed!.');
            }

        } catch (Exception $e) {
             // dd($e); 
    
              $mess = $e->getMessage(); 
            // dd($mess) ; 
            if (str_contains($mess, 'Duplicate entry')) {
                 return redirect()->route('reject_reasons.index')->with('error', 'data Already Exists');
            }
            else{
                return redirect()->route('reject_reasons.index')->with('error', 'Insert failed '.$mess);
            }
        }
    }

    /**
     * Update the specified resource in storage.
     */
      // public function update(Request $request ) 
    public function update(UpdateDeviceRequest $request, Device $device): RedirectResponse
    {
         // dd($request->all());
        try {
            $data = $request->validated();
  // dd($data) ; 
            $device = Device::find($data['recId']);
            if($device){
                $device->centerId = $data['cenId'];
                $device->devType = $data['devType'];
                $device->mac = $data['mac'];
                $device->ip = $data['ip'];
                $device->location = $data['location'];
                $device->status = $data['status'];
                $device->updated_at = now(); 
                $device->save(); // Fires 'updated' event → Loggable works

                return redirect()->route('reject_reasons.index')->with('success', 'Data Updated Successfully');
            }
            else{
                return redirect()->route('reject_reasons.index')->with('error', 'Failed to Update');
            }
       
        } catch (\Exception $e) {
              $mess = $e->getMessage(); 
            // dd($mess) ; 
            if (str_contains($mess, 'Duplicate entry')) {
                 return redirect()->route('devices.index')->with('error', 'Device Already Exists');
            }
            else{
                return redirect()->route('devices.index')->with('error', 'Insert failed '.$mess);
            }
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(RejectReason $RejectReason): RedirectResponse
    {
         // dd($RejectReason->all());
        try {
          $Reject = RejectReason::find($RejectReason->id); 
             if($Reject){
                $Reject->delete();
                return redirect()->route('reject_reasons.index')->with('success', 'Data deleted successfully.');
             } 
             else{
                 return redirect()->route('reject_reasons.index')->with('error', 'Data delete failed .');
             }
          
        } catch (\Exception $e) {
            info('data deleted failed!', [$e]);

            return redirect()->route('reject_reasons.index')->with('error', 'data deleted failed!.');
        }
    }



}
