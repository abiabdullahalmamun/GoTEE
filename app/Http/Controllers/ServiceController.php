<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreServiceRequest;
use App\Http\Requests\UpdateServiceRequest;
use App\Models\Service;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ServiceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $perPage = $request->input('per_page', 10);
        $search = $request->input('search');

        try {

            $query = Service::query()->latest('id');

            if ($search) {
                $query->where(function ($query) use ($search) {
                    $fillableColumns = (new Floor)->getFillable();
                    foreach ($fillableColumns as $column) {
                        $query->orWhere($column, 'like', '%'.$search.'%');
                    }
                });
            }

            $query = $query->paginate($perPage);

            return view('pages.services.index', with(['query' => $query]));

        } catch (Exception $e) {
            info('Error showing Floor!', [$e]);

            return redirect()->back()->with('error', 'Floor showing failed!.');
        }
    }

            // Shop::create([
            //         'ShopName'=> $r->name,
            //         'Contact'=>$r->Contact,
            //         'message'=>$r->mess,
            //         'status'=>1,
            //         'created_by'=>Auth::user()->user_id,
            //         'created_at'=>Date('Y-m-d H:i:s'),
            //         'updated_at'=>Date('Y-m-d H:i:s')
            //     ]);
    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreServiceRequest $request): RedirectResponse
    {
       // dd($request->all());
        try {
             // dd($data) ; 
            $data = $request->validated();
            // dd($data) ; 
             $save=Service::create([
                'center_id'     => 1,
                'service_name'   => $data['svcName'],
                 'type'   => $data['type'],
                 'defultsec'   => $data['defsec'],        
                'status'     => $data['status'],
                'created_by'=> $data['created_by'],
                'created_at'=> now(),
                'updated_at'=>now()
            ]);
            if($save){
                return redirect()->route('services.index')->with('success', 'Data Inserted successfully.');
            }
            else{
                 return redirect()->route('services.index')->with('error', 'Save failed!.');
            }

        } catch (Exception $e) {
             dd($e); 
            return redirect()->route('services.index')->with('error', 'Data Insert failed!.');
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateServiceRequest $request, Service $service): RedirectResponse
    {
         // dd($request->all());
        try {
            $data = $request->validated();
  // dd($data) ; 
          $services = Service::find($data['svcId']);
            if($services){
                $services->service_name = $data['svcName'];
                $services->type = $data['type'];
                $services->defultsec = $data['defsec'];
                $services->status = $data['status'];
                $services->updated_at = now(); 
                $services->save(); // Fires 'updated' event → Loggable works

                return redirect()->route('services.index')->with('success', 'Data Updated Successfully');
            }
            else{
                return redirect()->route('services.index')->with('error', 'Failed to Update');
            }

        } catch (\Exception $e) {
            //  dd($e); 
            // info('Floor updated failed!', [$e]);

            return redirect()->route('services.index')->with('error', ' updated failed!.');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Service $service): RedirectResponse
    {
         // dd($service->all());
        try {
            $service->delete();

            return redirect()->route('services.index')->with('success', 'service deleted successfully.');

        } catch (\Exception $e) {
            // info('service deleted failed!', [$e]);

            return redirect()->route('services.index')->with('error', 'service deleted failed!.');
        }
    }
}
