<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCenterRequest;
use App\Http\Requests\UpdateCenterRequest;
use App\Models\Center;
use App\Models\Region;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CenterController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $perPage = $request->input('per_page', 10);
        $search = $request->input('search');

        try {

            $query = Center::query()->latest('id');
            // dd($query) ; 
            if ($search) {
                $query->where(function ($query) use ($search) {
                    $fillableColumns = (new Floor)->getFillable();
                    foreach ($fillableColumns as $column) {
                        $query->orWhere($column, 'like', '%'.$search.'%');
                    }
                });
            }

            $query = $query->paginate($perPage);
            // dd($query); 
            $region = Region::select('id','region_name')->where('status',1)->orderby('region_name','asc')->get() ; 
            return view('pages.centers.index', with(['query' => $query,'regionList' =>$region]));

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
    public function store(StoreCenterRequest $request) : RedirectResponse  
    {
       // dd($request->all());
        try {
             // dd($data) ; 
            $data = $request->validated();
            // dd($data) ; 
             $save=Center::create([
                'region_id'     => $data['regionId'],
                'center_name'   => $data['centerName'],
                 'starthr'   => $data['startHr'],
                 'endhr'   => $data['endHr'],
                 'gtw_name'   => $data['gtw_name'],
                 'apt_tol'   => $data['apt_tol'],
                  'end_tol'   => $data['end_tol'],
                 'hotline'   => $data['hotline'],
                 'info'   => $data['info'],
                 'del_time'   => $data['del_time'],        
                'status'     => 1,
                'created_by'=> $data['created_by'],
                'created_at'=> now(),
                'updated_at'=>now()
            ]);
            if($save){
                return redirect()->route('centers.index')->with('success', 'Data Inserted successfully.');
            }
            else{
                 return redirect()->route('centers.index')->with('error', 'Tag save failed!.');
            }


        } catch (Exception $e) {
             // dd($e); 
    
            info('Shop data insert  failed!', [$e]);

            return redirect()->route('shops.index')->with('error', 'Shop Insert failed!.');
        }
    }

    /**
     * Update the specified resource in storage.
     */
     // public function update(Request $request)
    public function update(UpdateCenterRequest $request, Center $center): RedirectResponse
    {
         // dd($request->all());
        try {
            $data = $request->validated();
            // dd($data) ; 
            $center = Center::find($data['cenId']);
            if($center){
                $center->region_id = $data['regionId'];
                $center->center_name = $data['centerName'];
                $center->starthr = $data['startHr'];
                $center->endhr = $data['endHr'];
                $center->gtw_name = $data['gtw_name'];
                $center->apt_tol = $data['apt_tol'];
                 $center->end_tol = $data['end_tol'];
                $center->hotline = $data['hotline'];
                $center->info = $data['info'];
                $center->del_time = $data['del_time'];
                $center->status = $data['status'];
                $center->updated_at = now(); 
                $center->save(); // Fires 'updated' event → Loggable works

                return redirect()->route('centers.index')->with('success', 'Data Updated Successfully');
            }
            else{
                return redirect()->route('centers.index')->with('error', 'Failed to Update');
            }
         

        } catch (\Exception $e) {
             // dd($e); 
            return redirect()->route('centers.index')->with('error', 'Data updated failed!.'.$e);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Center $center): RedirectResponse
    {
         // dd($center->id);
        try {
             $center = Center::find($center->id); 
             if($center){
                $center->delete();
                return redirect()->route('centers.index')->with('success', 'Center deleted successfully.');
             } 
             else{
                 return redirect()->route('centers.index')->with('error', 'Data delete failed .');
             }
          

        } catch (\Exception $e) {
            info('Shop deleted failed!', [$e]);

            return redirect()->route('shops.index')->with('error', 'Shop deleted failed!.');
        }
    }
}
