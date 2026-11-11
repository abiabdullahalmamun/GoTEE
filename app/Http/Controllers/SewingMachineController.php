<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSewingMachineRequest;
use App\Http\Requests\UpdateSewingMachineRequest;
use App\Models\MachineList;
use App\Models\MachineType;
use App\Models\MachineBrand;

use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use App\Services\ActionExceptionService;


class SewingMachineController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $perPage = $request->input('per_page', 10);
        $search = $request->input('search');

        try {

            $query = MachineList::query()       
                             ->with(['brand', 'type', 'user'])
                             ->latest('id');
            if ($search) {
                $query->where(function ($query) use ($search) {
                    $fillableColumns = (new Floor)->getFillable();
                    foreach ($fillableColumns as $column) {
                        $query->orWhere($column, 'like', '%'.$search.'%');
                    }
                });
            }
            $MachineType= MachineType::where('status',1)->get() ;
            $MachineBrand = MachineBrand::where('status',1)->get() ;
            $query = $query->paginate($perPage);

            return view('pages.sewingMachine.index', with(['query' => $query,'MachineType' => $MachineType,'MachineBrand' => $MachineBrand]));

        } catch (Exception $e) {
            dd($e->getMessage()  ); 
            info('Error showing operators!', [$e]);

            return redirect()->back()->with('error', 'operator showing failed!.');
        }
    }

 
    /**
     * Store a newly created resource in storage.
     */

    public function store(StoreSewingMachineRequest $request): RedirectResponse
    {
        try {

            $validated = $request->validated();

            MachineList::create([
                'company_id'   => auth()->user()->company_id,
                'machine_id'   => $validated['machineId'],
                'machine_brand'=> $validated['machineBrand'],
                'machine_type' => $validated['machineType'],
                'machine_model'=> $validated['machineModel'],
                'Description'  => $validated['description'],
                'rented'       => $validated['rent'],
                'rented_ref'   => $validated['rentref'] ?? null,
                'status'       => $validated['status'],
                'created_by'   => auth()->id(),
                'created_at' => date('Y-m-d H:i:s'), 
                'updated_at' => date('Y-m-d H:i:s'), 
            ]);

            return redirect()
                ->route('sewingMachine.index')
                ->with('success', 'Machine added successfully.');

        } catch (\Exception $e) {
            dd($e->getMessage()) ;
            info('Machine data insert failed!', [
                'error' => $e->getMessage()
            ]);

            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Machine data insert failed!');
        }
    }


    /**
     * Update the specified resource in storage.
     */
    
public function update(UpdateSewingMachineRequest $request, MachineList $operator): RedirectResponse
{
    try {

        $data = $request->validated();

        // Find the machine record using recId
        $record = MachineList::findOrFail($data['recId']);

        // Keep old data for audit logging
        $oldData = $record->getOriginal();

        // Update machine information
        $is_update = $record->update([
            'machine_id'    => $data['machineId'],
            'machine_brand' => $data['machineBrand'],
            'machine_type'  => $data['machineType'],
            'machine_model' => $data['machineModel'],
            'Description'   => $data['description'],
            'rented'        => $data['rent'],
            'rented_ref'    => $data['rentref'] ?? null,
            'status'        => $data['status'],
            'updated_at'    => now(),
        ]);

        if ($is_update) {

            ActionExceptionService::store([
                'module'   => 'SewingMachine',
                'action'   => 'Updated',
                'centerId' => 1,
                'remarks'  => $oldData,
                'changeLog'=> $data,
            ]);

            return redirect()
                ->route('sewingMachine.index')
                ->with('success', $data['machineId'] . ' Updated Successfully');
        }

        return redirect()
            ->back()
            ->withInput()
            ->with('info', 'No changes were made.');

    } catch (\Illuminate\Database\QueryException $e) {

        if ($e->errorInfo[1] == 1062) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Machine ID already exists. Please enter a unique Machine ID.');
        }

        info('Machine update failed!', [
            'error' => $e->getMessage()
        ]);

        return redirect()
            ->back()
            ->withInput()
            ->with('error', 'Machine update failed!');

    } catch (\Exception $e) {

        info('Machine update failed!', [
            'error' => $e->getMessage()
        ]);

        return redirect()
            ->back()
            ->withInput()
            ->with('error', 'Machine update failed!');
    }
}

//     public function update(UpdateSewingMachineRequest $request, MachineList $operator): RedirectResponse
//     {
//          // dd($request->all());
//         try {
//             $data = $request->validated();
//             dd($data) ; 

//             $record = MachineList::find($data['recId']) ;  
//             $is_update = MachineList::where('id',$data['recId'])->update([
//                 'emp_id'     => $data['emp_id'],
//                 'emp_name'   => $data['emp_name'],
//                 // 'card_id'    => $data['card_id'],               //$data['card_id'],
//                 'emp_desig'   => $data['designation'],
//                 'emp_type'   => $data['optyppe'],
//                 'present_grade'   => $data['grade'],
//                  'gender1'   => $data['gender1'],
//                 'Date_of_join'   => $data['dateofjoin'],
//                  'evaluation'   => $data['evaluation'],
//                'present_salary'   => $data['salary'],
//                  'remarks'   => $data['remarks'],
//                 'status'     => $data['status'],
//                 'updated_at'=>Date('Y-m-d H:i:s')

//             ]);

//             if($is_update){
//                 ActionExceptionService::store([
//                     'module'      => 'SewingMachine',
//                     'action'      => 'Updated',
//                     'centerId'      => 1 ,
//                     'remarks'     => $record, 
//                     'changeLog'      =>  $data,
//                 ]);

//                 return redirect()->route('operators.index')->with('success',$data['emp_id'].' Updated Successfully');    
//             }
 
// // 
//         } catch (\Exception $e) {
//             $msss = $e->getMessage() ; 
//             if (str_contains($msss, 'Duplicate entry')) {
//                  return redirect()->route('operators.index')->with('error', 'Employee ID Duplicate!.');
//             }
//             else{
//                 dd($e->getMessage()); 
//                info('Floor updated failed!', [$e]);
//                 return redirect()->route('operators.index')->with('error', 'Floor updated failed!.');

//             }
//         }
//     }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id): RedirectResponse
    {
        try {
             $record = MachineList::find($id) ;  
             if($record){
                $record->delete();
                ActionExceptionService::store([
                    'module'      => 'SewingMachine',
                    'action'      => 'Deleted',
                    'centerId'      => 1 ,
                    'remarks'     => $record, 
                    'changeLog'      =>  '',
                ]);
                return redirect()->route('sewingMachine.index')->with('success', 'Data deleted successfully.');
             }
             else{
                   return redirect()->route('sewingMachine.index')->with('error', 'Data delete failed .');
             }

        } catch (\Exception $e) {
            info('Shop deleted failed!', [$e]);

            return redirect()->route('sewingMachine.index')->with('error', 'operator deleted failed!.');
        }
    }
}
