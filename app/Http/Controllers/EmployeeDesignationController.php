<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreDesignationRequest;
use App\Http\Requests\UpdateDesignationRequest;
use App\Models\Device;
 
use App\Models\Service;
use App\Models\DeviceSvc ; 
use App\Models\OrderNo ; 
use App\Models\OpnCodeBank ; 
use App\Models\OperationCat ;
use App\Models\Buyer ; 
use App\Models\BuyerItem ; 
use App\Models\EmpDesignation;
use App\Models\EmpDesignationType;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;
use PhpMqtt\Client\MqttClient;
use PhpMqtt\Client\ConnectionSettings;
use App\Services\ActionExceptionService;


class EmployeeDesignationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        try {

            $query = EmpDesignation::with(['destype', 'user'])
                     ->get();
         
            // dd($query) ; 
            $cat = EmpDesignationType::where('status',1)->orderby('typename','asc')->get(); 
            return view('pages.designation.index', with(['query' => $query, 'cat' => $cat]));

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

public function store(StoreDesignationRequest $request): RedirectResponse
{
    try {
        $data = $request->validated();

        $save = EmpDesignation::create([
            'company_id'    => 13,
            'designation'         =>   ($data['name'] ?? ''),
            'groupId'      => $data['cat'],
            'OperationName' => $data['name'],
            'status'        => $data['status'] ?? 1,
            'created_by'    => $data['created_by'] ?? auth()->id(),
          
        ]);

        if ($save) {
            return redirect()
                ->route('designation.index')
                ->with('success', 'Operation inserted successfully.');
        }

        return redirect()
            ->route('designation.index')
            ->with('error', 'Operation insert failed!');

    } catch (\Illuminate\Database\QueryException $e) {

        if ($e->errorInfo[1] == 1062) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'designation already exists.');
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
public function update(UpdateDesignationRequest $request ): RedirectResponse
{
    try {
        $data = $request->validated();
        $record = EmpDesignation::find($data['recId']) ; 
        EmpDesignation::where('id', $data['recId'])->update([
            'designation'      => $data['name'],
            'groupId'         => $data['cat'],
            'status'        => $data['status'],
             'updated_at'    => date('Y-m-d H:i:s'),
        ]);

         ActionExceptionService::store([
            'module'      => 'OperationBank',
            'action'      => 'Updated',
            'centerId'      => 1 ,
            'remarks'     =>  $record , 
            'changeLog'      => $data,
        ]);

        return redirect()
            ->route('designation.index')
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
          $record = EmpDesignation::find($id) ;  
             if($record){
                $record->delete();

                ActionExceptionService::store([
                    'module'      => 'EmpDesignation',
                    'action'      => 'Deleted',
                    'centerId'      => 1 ,
                    'remarks'     => $record, 
                    'changeLog'      =>  '',
                ]);
                return redirect()->route('designation.index')->with('success', 'Data deleted successfully.');
             } 
             else{
                 return redirect()->route('designation.index')->with('error', 'Data delete failed .');
             }
          
        } catch (\Exception $e) {
           return redirect()
            ->route('operationBank.index')
            ->with('error', 'Delete failed: ' . $e->getMessage());
        }
    }


 
}
