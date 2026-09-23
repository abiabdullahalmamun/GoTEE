<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreDeviceRequest;
use App\Http\Requests\UpdateDeviceRequest;
use App\Models\Device;
use App\Models\Center;
use App\Models\Service;
use App\Models\DeviceSvc ; 

use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;
use PhpMqtt\Client\MqttClient;
use PhpMqtt\Client\ConnectionSettings;

class DeviceController extends Controller
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

            $query = Device::query()->latest('id');

        // dd($query) ; 
            $query = $query->paginate(500);
            $center = Center::select('id','center_name')->where('status',1)->orderby('center_name','asc')->get() ; 
            $service = [] ; 
            return view('pages.devices.index', with(['query' => $query, 'centerList' =>$center, 'serviceList' =>$service]));

            // return view('pages.devices.index', with(['query' => $query]));

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
    public function store(StoreDeviceRequest $request): RedirectResponse
    {
       // dd($request->all());
        try {
            // if($request->input('devType')==2){
            //    if (!$request->filled('serviceId')) {
            //         return redirect()->route('devices.index')->with('error', 'Please select Service for Display!.');
            //     }
            // }

            $data = $request->validated();
            // dd($data) ; 
            // $center = Center::select('region_id')->where('id', $data['cenId'])->first() ; 

            $save=Device::create([
                 'devID'   => $data['devID'],
                 'devType'   => $data['devType'],
                 'mac'   => $data['mac'],
                 'created_by'   => $data['created_by'],
                'status'   => $data['status'],
                'motor_state'   => 0,
                'created_at'=> now(),
                'updated_at'=>now()
            ]);
            if($save){
             
                return redirect()->route('devices.index')->with('success', 'Data Inserted successfully.');
            }
            else{
                 return redirect()->route('devices.index')->with('error', 'Data Insert failed!.');
            }

        } catch (Exception $e) {
             // dd($e); 
    
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
                DB::transaction(function () use ($device, $data) {

                    $device->update([
                        'companyId' => $data['cenId'],
                        'devType'   => $data['devType'],
                        'opstate'   => $data['led'],
                        'mac'       => $data['mac'],
                        'ip'        => $data['ip'],
                        'message'   => $data['message'],
                        'location'  => $data['location'],
                        'status'    => $data['status'],
                    ]);

                    $server = config('mqtt.host');
                    $port = config('mqtt.port');
                    $clientId = 'laravel-' . uniqid();
                    $client = new MqttClient($server, $port, $clientId);

                    $settings = (new ConnectionSettings)
                        ->setUsername(config('mqtt.username'))
                        ->setPassword(config('mqtt.password'));

                    $client->connect($settings, true);

                    $payload = [
                        'Action'      => 'led',
                        'led'      => $device->opstate,
                        'message'  => $device->message,
                        'time'     => now()->toDateTimeString(),
                    ];

                    $client->publish(
                        $device->companyId.'/'.$device->devID,
                        json_encode($payload),
                        1 // QoS
                    );

                    $client->disconnect();
                    // publish MQTT here
                });


                // $device->companyId = $data['cenId'];
                // $device->devType = $data['devType'];
                //  $device->opstate = $data['led'];
                // $device->mac = $data['mac'];
                // $device->ip = $data['ip'];
                //  $device->message = $data['message'];
                // $device->location = $data['location'];
                // $device->status = $data['status'];
                // $device->updated_at = now(); 
                // $device->save(); // Fires 'updated' event → Loggable works

                return redirect()->route('devices.index')->with('success', 'Data Updated Successfully');
            }
            else{
                return redirect()->route('devices.index')->with('error', 'Failed to Update');
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
    public function destroy(Device $device): RedirectResponse
    {
         // dd($device->alml());
        try {
          $dev = Device::find($device->id); 
             if($dev){
                $dev->delete();
                return redirect()->route('devices.index')->with('success', 'Data deleted successfully.');
             } 
             else{
                 return redirect()->route('devices.index')->with('error', 'Data delete failed .');
             }
          
        } catch (\Exception $e) {
            info('Shop deleted failed!', [$e]);

            return redirect()->route('shops.index')->with('error', 'Shop deleted failed!.');
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
