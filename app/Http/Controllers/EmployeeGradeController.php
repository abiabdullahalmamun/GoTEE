<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreGradeRequest;
use App\Http\Requests\UpdateGradeRequest;
use App\Models\Device;
 
use App\Models\Service;
use App\Models\DeviceSvc ; 
use App\Models\OrderNo ; 
use App\Models\EmpGrade ; 
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


class EmployeeGradeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        try {

            $query = EmpGrade::with([ 'user'])
                ->where('status', 1)
                ->get();
         
         
            return view('pages.grade.index', with(['query' => $query ]));

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

public function store(StoreGradeRequest $request): RedirectResponse
{
    try {
        $data = $request->validated();

        $save = EmpGrade::create([
            'gradeName' => $data['name'],
            'status'        => $data['status'] ?? 1,
            'created_by'    => $data['created_by'] ?? auth()->id(),
             'created_at'    => date('Y-m-d H:i:s'),
               'updated_at'    => date('Y-m-d H:i:s'),
         
        ]);

        if ($save) {
            return redirect()
                ->route('empgrade.index')
                ->with('success', 'Operation inserted successfully.');
        }

        return redirect()
            ->route('empgrade.index')
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
public function update(UpdateGradeRequest $request ): RedirectResponse
{
    try {
        $data = $request->validated();

        EmpGrade::where('id', $data['recId'])->update([
            'gradeName' => $data['name'],
            'status'        => $data['status'],
             'updated_at'    => date('Y-m-d'),
        ]);

         ActionExceptionService::store([
            'module'      => 'OperatorGrade',
            'action'      => 'Updated',
            'centerId'      => 1 ,
            'remarks'     => '', 
            'changeLog'      => $data,
        ]);

        return redirect()
            ->route('empgrade.index')
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
          $record = EmpGrade::find($id) ;  
             if($record){
                $record->delete();

                ActionExceptionService::store([
                    'module'      => 'OperatorGrade',
                    'action'      => 'Deleted',
                    'centerId'      => 1 ,
                    'remarks'     => $record, 
                    'changeLog'      =>  '',
                ]);
                return redirect()->route('empgrade.index')->with('success', 'Data deleted successfully.');
             } 
             else{
                 return redirect()->route('empgrade.index')->with('error', 'Data delete failed .');
             }
          
        } catch (\Exception $e) {
           return redirect()
            ->route('empgrade.index')
            ->with('error', 'Delete failed: ' . $e->getMessage());
        }
    }

 
}
