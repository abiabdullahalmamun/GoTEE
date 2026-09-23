<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCounterRequest;
use App\Http\Requests\UpdateCounterRequest;
use App\Models\Counter;
use App\Models\CounterSvc ; 
use App\Models\Center;
use App\Models\Region;
use App\Models\Service;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use App\Services\ActionExceptionService;


class CounterController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        // $perPage = $request->input('per_page', 10);
        // $search = $request->input('search');

        try {
            $query = Counter::with('counterServices.service')->latest('id');
            // $query = Counter::with('counterServices')->query()->latest('id');
            // dd($query) ; 
            // if ($search) {
            //     $query->where(function ($query) use ($search) {
            //         $fillableColumns = (new Floor)->getFillable();
            //         foreach ($fillableColumns as $column) {
            //             $query->orWhere($column, 'like', '%'.$search.'%');
            //         }
            //     });
            // }

            $query = $query->paginate(500);
            // dd($query) ; 
            $center = Center::select('id','center_name')->where('status',1)->orderby('center_name','asc')->get() ; 
            $svc = Service::select('id','service_name')->where('status',1)->orderby('service_name','asc')->get() ; 

            $states = [0 => 'LoggedOut'] + $svc->pluck('service_name', 'id')->toArray();
            // dd($query) ; 
            return view('pages.counters.index', with(['query' => $query, 'centerList' =>$center, 'states' =>$states  ]));

        } catch (Exception $e) {

            info('Error showing Floor!', [$e]);

            return redirect()->back()->with('error', 'Failed to show data!'.$e);
        }
    }

      
    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCounterRequest $request): RedirectResponse
    {
       // dd($request->all());
        try {
             // dd($data) ; 
            $data = $request->validated();
            // dd($data) ; 
            $center = Center::select('region_id')->where('id', $data['cenId'])->first() ; 

            $save=Counter::create([
                'region_id'     => $center->region_id,
                'center_id'   => $data['cenId'],
                 'counter_id'   => $data['counterNo'],
                 'counter_name'   => $data['counterName'],
                 'created_by'   => $data['created_by'],
                 'status'   => 1,
                 'loginstate'   => 0,
                'mac'   => $data['mac'],
                 'ip'   => $data['ip'],
                 'host'   => $data['hostname'],
                'created_at'=> now(),
                'updated_at'=>now()
            ]);
            if($save){
                return redirect()->route('counters.index')->with('success', 'Data Inserted successfully.');
            }
            else{
                 return redirect()->route('counters.index')->with('error', 'Tag save failed!.');
            }
      
        } catch (Exception $e) {
             $mess = $e->getMessage(); 
            // dd($mess) ; 
            if (str_contains($mess, 'Duplicate entry')) {
                 return redirect()->route('counters.index')->with('error', 'CounterNo Already Exists');
            }
            else{
                return redirect()->route('counters.index')->with('error', 'Insert failed '.$mess);
            }
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCounterRequest $request, Counter $counter): RedirectResponse
    {
         // dd($request->all());
            try {
            $data = $request->validated();
            // dd($data) ; 
            $counter = Counter::find($data['counterId']);
            if($counter){
                // $cent = $data['cenId']
                $region = 1 ;
                $reg = Center::select('region_id')->where('id',$data['cenId'])->first() ; 
                if($reg){
                     $region = $reg->region_id ; 
                }
                $counter->region_id =$region;
                $counter->center_id = $data['cenId'];
                $counter->counter_name = $data['counterName'];
                $counter->mac = $data['mac'];
                $counter->ip = $data['ip'];
                $counter->host = $data['hostname'];
                $counter->status = $data['status'];
                $counter->updated_at = now(); 
                $counter->save(); // Fires 'updated' event → Loggable works

                return redirect()->route('counters.index')->with('success', 'Data Updated Successfully');
            }
            else{
                return redirect()->route('counters.index')->with('error', 'Failed to Update');
            }
         

        } catch (\Exception $e) {
             dd($e); 
            return redirect()->route('counters.index')->with('error', 'Data updated failed!.'.$e);
        }
    }


    public function CounterService(Counter $counter)
    {
         // dd($counter->id);
        try {
            $cnt = Counter::where('id',$counter->id)->first() ; 
            // $user = $emm->emp_id ; 
            // $userName = $emm->emp_name ; 
            // dd($userName) ; 
            $cntSvc = CounterSvc::where('counterId', $counter->id)->get() ; 
              // dd($empShop) ; 
            // $roles = Role::all();
            $service = Service::where('status',1)->latest()->get();
            return view('pages.counters.counterSvc', compact('cntSvc','service', 'cnt'));


        } catch (\Exception $e) {
            
            return redirect()->route('counters.index')->with('error', 'assignement failed!.');
        }
    }

    public function updateAssign(Request $request)
    {
             // dd($request->all());
     try {   
        $cnt = Counter::where('id', $request->cid)->first() ; 

        if($cnt){
            // dd('yes') ; 
            // dd( $request->assigned_shops) ; 
            $userId = Auth::id();
            CounterSvc::where('counterId',$request->cid)->delete();
           $cn = count($request->input('assigned_svc', []));
            for($i=0; $i<$cn; $i++){
          
                CounterSvc::upsert(
                    [
                        [
                            'counterId'      => $request->cid,
                            'svcId'     => $request->assigned_svc[$i],
                            'created_at' => Date('Y-m-d H:i:s'),
                            'updated_at' => Date('Y-m-d H:i:s'),
                             'created_by' =>  $userId
                        ],
                    ],
                    ['counterId', 'svcId'], // Unique keys to match
                    ['updated_at']       // Columns to update if exists
                );

            }

            if($cn>0){
                   ActionExceptionService::store([
                        'module'      => 'Counter Permission',
                        'action'      => 'ServiceUpdated',
                        'centerId'      =>  $cnt->center_id ,
                        'remarks'     =>'CounterNo: '.$cnt->counter_id.' CounterName: '.$cnt->counter_name.' Service Updated:'.$cn, // save only duplicated numbers
                    ]);
            }
           
           return redirect()->route('counters.index')->with('success', $cn.' Service updated successfully.');
         
           
        }
        else{
            return redirect()->route('counters.index')->with('error', 'Invalid Counter!.');
        }

      } catch (\Exception $e) {
            info('Shop deleted failed!', [$e]);
             dd($e->getMessage());
            return redirect()->route('counters.index')->with('error', 'operation failed!.');
        }
      
//
        // return redirect()->route('operators.index')->with('success', 'User updated successfully.');
    }
    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Counter $counter): RedirectResponse
    {
         // dd($shop->all());
        try {
           $counter = Counter::find($counter->id); 
             if($counter){
                $counter->delete();
                return redirect()->route('counters.index')->with('success', 'Data deleted successfully.');
             } 
             else{
                 return redirect()->route('counters.index')->with('error', 'Data delete failed .');
             }
          

        } catch (\Exception $e) {
          
            return redirect()->route('counters.index')->with('error', 'Data delete failed!.');
        }
    }
    
    public function enablecall(Counter $counter)  //(Request $request)
    {
        // dd($counter->id) ; 
        try{
            $updateEx = Counter::where('id',$counter->id)->update([
                    'enCall'  => 1, 
                    'updated_at'=>now()
                ]);  
            return redirect()->route('counters.index')->with('success', 'Call Enabled Successfully.');

        } catch (Exception $e) {
            // $msg = $e->getMessage(); 
            return redirect()->route('counters.index')->with('error', 'Data delete failed!.'.$msg);
        }


    }
}
