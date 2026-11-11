<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreOperationBankRequest;
use App\Http\Requests\UpdateOperationBankRequest;
use App\Models\Device;
 
use App\Models\Service;
use App\Models\DeviceSvc ; 
use App\Models\OrderNo ; 
use App\Models\OpnCodeBank ; 
use App\Models\OperationCat ;
use App\Models\Buyer ; 
use App\Models\BuyerItem ; 
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;
use PhpMqtt\Client\MqttClient;
use PhpMqtt\Client\ConnectionSettings;
use App\Services\ActionExceptionService;


class OperationBankController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        try {

            $query = OpnCodeBank::with(['cat', 'user'])
                ->where('status', 1)
                ->get();
         
            $cat = OperationCat::where('status',1)->orderby('Category_id','asc')->get(); 
            return view('pages.operationBank.index', with(['query' => $query, 'cat' =>$cat]));

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

public function store(StoreOperationBankRequest $request): RedirectResponse
{
    try {
        $data = $request->validated();

        $save = OpnCodeBank::create([
            'company_id'    => 13,
            'OprNo'         => ($data['codeA'] ?? '') . ($data['codeB'] ?? ''),
            'Category'      => $data['catA'],
            'OperationName' => $data['name'],
            'status'        => $data['status'] ?? 1,
            'created_by'    => $data['created_by'] ?? auth()->id(),
             'def_opn' => $data['defopn'],
        ]);

        if ($save) {
            return redirect()
                ->route('operationBank.index')
                ->with('success', 'Operation inserted successfully.');
        }

        return redirect()
            ->route('operationBank.index')
            ->with('error', 'Operation insert failed!');

    } catch (\Illuminate\Database\QueryException $e) {

        if ($e->errorInfo[1] == 1062) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Operation code already exists.');
        }

        return redirect()
            ->back()
            ->withInput()
            ->with('error', 'Insert failed: ' . $e->getMessage());
    }
}
 


    /**
     * Update the specified resource in storage.
     */
      // public function update(Request $request ) 
public function update(UpdateOperationBankRequest $request, OpnCodeBank $device): RedirectResponse
{
    try {
        $data = $request->validated();

        $device->update([
            'Category'      => $data['catA'],
            'OprNo'         => $data['codeB'],
            'OperationName' => $data['name'],
            'def_opn'       => $data['defopn'],
            'status'        => $data['status'],
             'updated_at'    => date('Y-m-d'),
        ]);

         ActionExceptionService::store([
            'module'      => 'OperationBank',
            'action'      => 'Updated',
            'centerId'      => 1 ,
            'remarks'     => '', 
            'changeLog'      => $data,
        ]);

        return redirect()
            ->route('operationBank.index')
            ->with('success', 'Operation updated successfully.');

    } catch (\Illuminate\Database\QueryException $e) {

        if ($e->errorInfo[1] == 1062) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Operation code already exists.');
        }

        return redirect()
            ->back()
            ->withInput()
            ->with('error', 'Update failed: ' . $e->getMessage());

    } catch (\Exception $e) {

        return redirect()
            ->back()
            ->withInput()
            ->with('error', 'Update failed: ' . $e->getMessage());
    }
}

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id): RedirectResponse
    {
        // dd($id) ; 
        try {
          $record = OpnCodeBank::find($id) ;  
             if($record){
                $record->delete();

                ActionExceptionService::store([
                    'module'      => 'OperationBank',
                    'action'      => 'Deleted',
                    'centerId'      => 1 ,
                    'remarks'     => $record, 
                    'changeLog'      =>  '',
                ]);
                return redirect()->route('operationBank.index')->with('success', 'Data deleted successfully.');
             } 
             else{
                 return redirect()->route('operationBank.index')->with('error', 'Data delete failed .');
             }
          
        } catch (\Exception $e) {
           return redirect()
            ->route('operationBank.index')
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
