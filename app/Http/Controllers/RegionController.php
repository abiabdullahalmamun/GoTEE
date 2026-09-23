<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreRegionRequest;
use App\Http\Requests\UpdateRegionRequest;
use App\Models\Region;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class RegionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $perPage = $request->input('per_page', 10);
        $search = $request->input('search');

        try {

            $query = Region::query()->with(['user'])->latest('id');

            if ($search) {
                $query->where(function ($query) use ($search) {
                    $fillableColumns = (new Floor)->getFillable();
                    foreach ($fillableColumns as $column) {
                        $query->orWhere($column, 'like', '%'.$search.'%');
                    }
                });
            }

            $query = $query->paginate($perPage);

            return view('pages.regions.index', with(['query' => $query]));

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
    public function store(StoreRegionRequest $request): RedirectResponse
    {
       // dd($request->all());
        try {
             // dd($data) ; 
            $data = $request->validated();
            // dd($data) ; 
            $save=Region::create([
                'region_name'     => $data['region_name'],
                'region_text'   => $data['region_text'],
                'status'     => 1,
                'created_by'=> $data['created_by'],
                'created_at'=>Date('Y-m-d H:i:s'),
                'updated_at'=>Date('Y-m-d H:i:s')
            ]);
            if($save){
                return redirect()->route('regions.index')->with('success', 'Data Inserted successfully.');
            }
            else{
                 return redirect()->route('regions.index')->with('error', 'Tag save failed!.');
            }




            return redirect()->route('regions.index')->with('success', 'Floor Inserted successfully.');

        } catch (Exception $e) {
             dd($e->getMessage()); 
            info('Region data insert  failed!', [$e]);

            return redirect()->route('regions.index')->with('error', 'Region Insert failed!.');
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateRegionRequest $request, Region $region): RedirectResponse
    {
         // dd($request->all());
        try {
            $data = $request->validated();
  // dd($data) ; 

            $region = Region::find($data['recId']);
            if ($region) {
                $region->region_name = $data['region_name'];
                $region->region_text = $data['region_text'];
                $region->status = $data['status'];
                $region->updated_at = now(); 
                $region->save(); // Fires 'updated' event → Loggable works

                return redirect()->route('regions.index')->with('success', 'Data Updated Successfully');
            }
            else{
                 return redirect()->route('regions.index')->with('error', 'Failed to Update');
             }

   
        } catch (\Exception $e) {
             // dd($e); 
            info('Floor updated failed!', [$e]);

            return redirect()->route('regions.index')->with('error', 'Update failed!.'.$e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Region $region): RedirectResponse
    {
         // dd($region->id);
        try {
            $region = Region::find($region->id);  
            if ($region) {
                $region->delete(); // Fires 'deleted' event → Loggable works
                return redirect()->route('regions.index')->with('success', 'Region deleted successfully.');
            }
            else{
                 return redirect()->route('regions.index')->with('error', 'Region not found.');
            }

        } catch (\Exception $e) {
             $msss = $e->getMessage() ;
             if (str_contains($msss, 'a foreign key constraint fails')) {
                 return redirect()->route('regions.index')->with('error', 'a foreign key constraint fails.');
            }
            else{
                // dd($e->getMessage()); 
                return redirect()->route('regions.index')->with('error','Region Delete failed!.'.$msss);
            }


            info('Region deleted failed!', [$e]);
// dd($e->getMessage()); 
            return redirect()->route('regions.index')->with('error', 'Region deleted failed!.'.$e->getMessage());
        }
    }
}
