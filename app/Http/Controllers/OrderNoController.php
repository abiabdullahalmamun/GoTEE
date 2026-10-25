<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreOrderNoRequest;
use App\Http\Requests\UpdateOrderNoRequest;
use App\Models\Device;
 
use App\Models\Service;
use App\Models\DeviceSvc ; 
use App\Models\OrderNo ; 
use App\Models\Buyer ; 
use App\Models\BuyerItem ; 
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;
use PhpMqtt\Client\MqttClient;
use PhpMqtt\Client\ConnectionSettings;

class OrderNoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        try {

            $query = OrderNo::query()->latest('id');
            $query = $query->paginate(500);
            $buyer = Buyer::where('status',1)->orderby('buyer','asc')->get(); 
           $item = BuyerItem::where('status',1)->orderby('itemName','asc')->get(); 
            return view('pages.orderNo.index', with(['query' => $query, 'buyer' =>$buyer,  'itemList' =>$item]));

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


public function store(StoreOrderNoRequest $request): RedirectResponse
{
    try {
        $data = $request->validated();

        $save = OrderNo::create([
            'OrderCode'  => $data['OrderCode'],
            'OrderNo'    => $data['OrderNo'],
            'attr'       => $data['attr'] ?? null,
            'Style'      => $data['Style'] ?? null,
            'buyer'      => $data['buyer'] ?? null,
            'status'     => $data['status'] ?? 1,
            'created_by' => $data['created_by'] ?? auth()->id(),
            'totalsmv'   => $data['totalsmv'] ?? null,
            'item'       => $data['item'] ?? null,
        ]);

        if ($save) {
            return redirect()
                ->route('orders.index')
                ->with('success', 'Order inserted successfully.');
        }

        return redirect()
            ->route('orders.index')
            ->with('error', 'Order insert failed!');

    } catch (Exception $e) {

        $mess = $e->getMessage();

        if (str_contains($mess, 'Duplicate entry')) {
            return redirect()
                ->route('orders.index')
                ->with('error', 'Order already exists.');
        }

        return redirect()
            ->route('orders.index')
            ->with('error', 'Insert failed: ' . $mess);
    }
}


    /**
     * Update the specified resource in storage.
     */
      // public function update(Request $request ) 
    public function update(UpdateOrderNoRequest $request, OrderNo $device): RedirectResponse
    {
        // dd($request) ; 
         // dd($request->all());
        try {
            $data = $request->validated();

            $record = OrderNo::where('OrderCode', $data['OrderCode'])->first();
            if($record){
                $record->OrderNo = $data['OrderNo'];
                 $record->attr = $data['attr'];
                $record->Style = $data['Style'];
                $record->buyer = $data['buyer'];
                 $record->status = $data['status'];
                $record->totalsmv  = $data['totalsmv'];
                $record->item = $data['item'];
                $record->updated_at = now(); 
                $record->save(); // Fires 'updated' event → Loggable works

                return redirect()->route('orders.index')->with('success', 'Data Updated Successfully');
            }
            else{
                return redirect()->route('orders.index')->with('error', 'Failed to Update');
            }
       
        } catch (\Exception $e) {
              $mess = $e->getMessage(); 
            // dd($mess) ; 
            if (str_contains($mess, 'Duplicate entry')) {
                 return redirect()->route('orders.index')->with('error', 'Device Already Exists');
            }
            else{
                return redirect()->route('orders.index')->with('error', 'Insert failed '.$mess);
            }
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id): RedirectResponse
    {
        // dd($id) ; 
        try {
          $record = OrderNo::find($id) ;  
             if($record){
                $record->delete();
                return redirect()->route('orders.index')->with('success', 'Data deleted successfully.');
             } 
             else{
                 return redirect()->route('orders.index')->with('error', 'Data delete failed .');
             }
          
        } catch (\Exception $e) {
           return redirect()
            ->route('orders.index')
            ->with('error', 'Delete failed: ' . $e->getMessage());
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
