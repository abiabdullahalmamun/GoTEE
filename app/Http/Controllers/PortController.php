<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePortRequest;
use App\Http\Requests\UpdatePortRequest;
use App\Models\Port;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PortController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $perPage = $request->input('per_page', 10);
        $search = $request->input('search');

        try {

            $query = Port::query()->latest('id');

            if ($search) {
                $query->where(function ($query) use ($search) {
                    $fillableColumns = (new Floor)->getFillable();
                    foreach ($fillableColumns as $column) {
                        $query->orWhere($column, 'like', '%'.$search.'%');
                    }
                });
            }

            $query = $query->paginate($perPage);

            return view('pages.ports.index', with(['query' => $query]));

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
    public function store(StoreShopRequest $request): RedirectResponse
    {
       // dd($request->all());
        try {
             // dd($data) ; 
            $data = $request->validated();
            // dd($data) ; 
            Shop::create($data);

            return redirect()->route('shops.index')->with('success', 'Floor Inserted successfully.');

        } catch (Exception $e) {
             // dd($e); 
    
            info('Shop data insert  failed!', [$e]);

            return redirect()->route('shops.index')->with('error', 'Shop Insert failed!.');
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateShopRequest $request, Shop $shop): RedirectResponse
    {
         dd($request->all());
        try {
            $data = $request->validated();
  // dd($data) ; 
            $shop->update($data);

            return redirect()->route('shops.index')->with('success', 'Floor updated successfully.');

        } catch (\Exception $e) {
             dd($e); 
            info('Floor updated failed!', [$e]);

            return redirect()->route('shops.index')->with('error', 'Floor updated failed!.');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Shop $shop): RedirectResponse
    {
         // dd($shop->all());
        try {
            $shop->delete();

            return redirect()->route('shops.index')->with('success', 'Shop deleted successfully.');

        } catch (\Exception $e) {
            info('Shop deleted failed!', [$e]);

            return redirect()->route('shops.index')->with('error', 'Shop deleted failed!.');
        }
    }
}
