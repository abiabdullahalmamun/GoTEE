<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAppIDRequest;
use App\Http\Requests\UpdateAppIDRequest;
use App\Models\Device;
use App\Models\Floor;
use App\Models\Line;
use App\Models\LineQC;
// use App\Models\Service;
// use App\Models\DeviceSvc ; 

use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;
use PhpMqtt\Client\MqttClient;
use PhpMqtt\Client\ConnectionSettings;

class LineQCController extends Controller
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
            // dd('ok') ; 
            $query = LineQC::query()->latest('id');
            // dd($query) ; 
            $query = $query->paginate(500);
         
          
            $lines = Line::where('status',1)->get(); 
            $devices = Device::where('devType',3)->where('status',1)->get(); 
            
            return view('pages.lineQCdev.index', with(['query' => $query, 'lineList' =>$lines, 'deviceList' =>$devices]));

        } catch (Exception $e) {
            // info('Error showing Floor!', [$e]);
             $mess = $e->getMessage(); 
             dd($mess) ; 
            return redirect()->back()->with('error', 'device showing failed!.');
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreAppIDRequest $request): RedirectResponse
    {
       // dd($request->all());
        try {
            
            $data = $request->validated();
            // dd($data) ; 
            
            $save=LineQC::create([
                 'lineId'   => $data['lineId'],
                 'devId'   => $data['devId'],
                 'appId'   => $data['appId'],
                 'created_by'   => $data['created_by'],
                'status'   => $data['status'],
                'created_at'=> now(),
                'updated_at'=>now()
            ]);
            if($save){
             
                return redirect()->route('lineQC-terminal.index')->with('success', 'Data Inserted successfully.');
            }
            else{
                 return redirect()->route('lineQC-terminal.index')->with('error', 'Data Insert failed!.');
            }

        } catch (Exception $e) {
             // dd($e); 
    
              $mess = $e->getMessage(); 
            // dd($mess) ; 
            if (str_contains($mess, 'Duplicate entry')) {
                 return redirect()->route('lineQC-terminal.index')->with('error', 'Device Already Exists');
            }
            else{
                return redirect()->route('lineQC-terminal.index')->with('error', 'Insert failed '.$mess);
            }
        }
    }

    /**
     * Update the specified resource in storage.
     */
      // public function update(Request $request ) 
    public function update(UpdateAppIDRequest $request, LineQC $device): RedirectResponse
    {
         // dd($request->all());
        try {
            $data = $request->validated();
  // dd($data) ; 
            $record = LineQC::find($data['recId']);
            if($record){
                $record->lineId = $data['lineId'];
                $record->appId = $data['appId'];
                $record->devId = $data['devId'];
                $record->status = $data['status'];
                $record->updated_at = now(); 
                $record->save(); // Fires 'updated' event → Loggable works

                return redirect()->route('lineQC-terminal.index')->with('success', 'Data Updated Successfully');
            }
            else{
                return redirect()->route('lineQC-terminal.index')->with('error', 'Failed to Update');
            }
       
        } catch (\Exception $e) {
              $mess = $e->getMessage(); 
            // dd($mess) ; 
            if (str_contains($mess, 'Duplicate entry')) {
                 return redirect()->route('lineQC-terminal.index')->with('error', 'Device Already Exists');
            }
            else{
                return redirect()->route('lineQC-terminal.index')->with('error', 'Insert failed '.$mess);
            }
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(LineQC $device): RedirectResponse
    {
         // dd($device->alml());
        try {
          $dev = LineQC::find($device->id); 
             if($dev){
                $dev->delete();
                return redirect()->route('lineQC-terminal.index')->with('success', 'Data deleted successfully.');
             } 
             else{
                 return redirect()->route('lineQC-terminal.index')->with('error', 'Data delete failed .');
             }
          
        } catch (\Exception $e) {
            info('Shop deleted failed!', [$e]);

            return redirect()->route('lineQC-terminal.index')->with('error', 'Shop deleted failed!.');
        }
    }


    public function DevSvc(Device $device)
    {
         // dd($device->id);
        try {
            $device = Device::where('id',$device->id)->first() ; 
            // dd($device) ; 

            $DevSvc = DeviceSvc::where('devId', $device->id)->get() ; 
              // dd($DevSvc) ; 
            // $roles = Role::all();
            $Service = Service::where('status',1)->latest()->get();
            return view('pages.devices.DisplaySvc', compact('device','DevSvc', 'Service'));


        } catch (\Exception $e) {
            info('Operator Shop assignement!', [$e]);

            return redirect()->route('devices.index')->with('error', 'device assignement failed!.');
        }
    }


    public function updateAssign(Request $request)
    {
             // dd($request->all());
     try {   
        $dev = Device::where('id', $request->devId)->first() ; 

        if($dev){
            // dd('yes') ; 
            // dd( $request->assigned_shops) ; 
            DeviceSvc::where('devId',$request->devId)->delete();
            $cn = count($request->assigned_devs) ; 
            for($i=0; $i<$cn; $i++){
                DeviceSvc::upsert(
                    [
                        [
                            'centerId'      => $request->centerId,
                            'devId'      => $request->devId,
                            'svcId'     => $request->assigned_devs[$i],
                            'rowcount'     => $request->rowcount[$i],
                            'created_at' => Date('Y-m-d H:i:s'),
                            'updated_at' => Date('Y-m-d H:i:s')
                        ],
                    ],
                    ['centerId', 'devId', 'svcId'], // Unique keys to match
                    ['updated_at']       // Columns to update if exists
                );

            }
           return redirect()->route('devices.index')->with('success', $cn.' Record updated successfully.');
        }
        else{
            return redirect()->route('devices.index')->with('error', 'Invalid Operator!.');
        }

      } catch (\Exception $e) {
            info('Record deleted failed!', [$e]);
 dd($e->getMessage());
            return redirect()->route('devices.index')->with('error', 'operation failed!.');
        }
    }
}
