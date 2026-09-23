<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreVisaTypeRequest;
use App\Http\Requests\UpdateVisaTypeRequest;
use App\Models\VisaType;
use App\Models\VisaTypeTdd ;
use App\Models\Center ; 
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class VisaTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        // $perPage = $request->input('per_page', 10);
        // $search = $request->input('search');

        try {

            $query = VisaType::query()->latest('id');

            // if ($search) {
            //     $query->where(function ($query) use ($search) {
            //         $fillableColumns = (new Floor)->getFillable();
            //         foreach ($fillableColumns as $column) {
            //             $query->orWhere($column, 'like', '%'.$search.'%');
            //         }
            //     });
            // }

            $query = $query->paginate(500);
          
            return view('pages.visa_types.index', with(['query' => $query]));

        } catch (Exception $e) {
            info('Error showing Floor!', [$e]);

            return redirect()->back()->with('error', 'Floor showing failed!.');
        }
    }

  
    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreVisaTypeRequest $request): RedirectResponse
    {
       // dd($request->all());
        try {
             // dd($data) ; 
            $data = $request->validated();
            // dd($data) ; 
              $save=VisaType::create([
                'centerId'     => 1,
                'visa_type'   => $data['visa_type'],
                 'symbol'   => $data['symbol'],
                 'days'   => $data['days'],
                 'created_by'   => $data['created_by'],
                 'status'   => 1,
                'created_at'=> now(),
                'updated_at'=>now()
            ]);
            if($save){
                return redirect()->route('visa_types.index')->with('success', 'Data Inserted successfully.');
            }
            else{
                 return redirect()->route('visa_types.index')->with('error', 'Tag save failed!.');
            }
        } catch (Exception $e) {
            dd($e) ;
            return redirect()->route('visa_types.index')->with('error', 'Data Insert failed!.');
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateVisaTypeRequest $request,  VisaType $visaType) : RedirectResponse  
    {
         // dd($request->all());
        try {
            $data = $request->validated();
  // dd($data) ; 
            $visaType = VisaType::find($data['visatypeId']);
            if($visaType){
                $visaType->visa_type = $data['visa_type'];
                $visaType->symbol = $data['symbol'];
                $visaType->days = $data['days'];
                $visaType->status = $data['status'];
                $visaType->updated_at = now(); 
                $visaType->save(); // Fires 'updated' event → Loggable works

                return redirect()->route('visa_types.index')->with('success', 'Data Updated Successfully');
            }
            else{
                return redirect()->route('visa_types.index')->with('error', 'Failed to Update');
            }        



        } catch (\Exception $e) {
             // dd($e); 
            info('Floor updated failed!', [$e]);

            return redirect()->route('visa_types.index')->with('error', 'Data updated failed!.');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(VisaType $visaType): RedirectResponse
    {
         // dd($visaType->all());
        try {
            $visa = VisaType::find($visaType->id); 
             if($visa){
                $visa->delete();
                return redirect()->route('visa_types.index')->with('success', 'Data deleted successfully.');
             } 
             else{
                 return redirect()->route('visa_types.index')->with('error', 'Data delete failed .');
             }
          


        } catch (\Exception $e) {
            info('Shop deleted failed!', [$e]);

            return redirect()->route('visa_types.index')->with('error', 'Shop deleted failed!.');
        }
    }
    public function VisaTypeTdd(visaType $visa_type)
    {
         // dd($visa_type->id);
        try {
            $visaType = VisaType::where('id',$visa_type->id)->first() ; 
            // dd($device) ; 

            $VisaTypeTdd = VisaTypeTdd::where('visatypeId',$visa_type->id)->get() ; 
              // dd($DevSvc) ; 

            // dd($VisaTypeTdd) ;
            // $roles = Role::all();
            $Center = Center::where('status',1)->latest()->get();
            return view('pages.visa_types.VisaTypeTdd', compact('visaType','VisaTypeTdd', 'Center'));

        } catch (\Exception $e) {
            // info('Operator Shop assignement!', [$e]);
            return redirect()->route('visa_types.index')->with('error', 'visatype tdd assignement failed!.');
        }
    }

    public function updateAssign(Request $request)
    {
     try {   
         // dd($request->all());
        $visaType = VisaType::where('id', $request->visaTypeId)->first() ; 
        $userId = auth()->user()->id ;  
        if($visaType){
            // dd('yes') ; 
            // dd( $request->assigned_shops) ; 
            VisaTypeTdd::where('visatypeId',$request->visaTypeId)->delete();
            $cn = count($request->assigned_devs ?? []);
            if($cn>0){
                for($i=0; $i<$cn; $i++){
                    VisaTypeTdd::upsert(
                        [
                            [
                                'visatypeId'      => $request->visaTypeId,
                                'centerId'      => $request->assigned_devs[$i],
                                'days'      =>  $request->extra_inputs[$i],
                                // 'tdd'      => $request->devId,
                                'created_by'     => $userId ,
                                'created_at' => Date('Y-m-d H:i:s'),
                                'updated_at' => Date('Y-m-d H:i:s')
                            ],
                        ],
                        ['visatypeId', 'centerId'], // Unique keys to match
                        ['updated_at','days']       // Columns to update if exists
                    );

                }
               return redirect()->route('visa_types.index')->with('success', $cn.' visa_types TDD updated successfully.');
            }
            else{
                return redirect()->route('visa_types.index')->with('error', 'Please add centers');
            }


        }
        else{
            return redirect()->route('visa_types.index')->with('error', 'Invalid visa_types!.');
        }

      } catch (\Exception $e) {
            info('Shop deleted failed!', [$e]);
             // dd($e->getMessage());
            return redirect()->route('visa_types.index')->with('error', 'operation failed!.'.$e->getMessage());
        }
    }

}
