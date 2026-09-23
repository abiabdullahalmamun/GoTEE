<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreStickerTypeRequest;
use App\Http\Requests\UpdateStickerTypeRequest;
use App\Models\VisaType;
use App\Models\VisaTypeTdd ;
use App\Models\Center ; 
use App\Models\StickerMap ; 
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class StickerTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        // $perPage = $request->input('per_page', 10);
        // $search = $request->input('search');

        try {

            $query = StickerMap::query()->latest('id');

            // if ($search) {
            //     $query->where(function ($query) use ($search) {
            //         $fillableColumns = (new Floor)->getFillable();
            //         foreach ($fillableColumns as $column) {
            //             $query->orWhere($column, 'like', '%'.$search.'%');
            //         }
            //     });
            // }
            $centers = Center::orderBy('center_name')->get();

            $query = $query->paginate(500);
          
            return view('pages.StickerMap.index', with(['query' => $query, 'centers' => $centers]));

        } catch (Exception $e) {
            info('Error showing data!', [$e]);

            return redirect()->back()->with('error', 'data showing failed!.');
        }
    }

  
    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreStickerTypeRequest $request): RedirectResponse
    {
       // dd($request->all());
        try {
             // dd($data) ; 
            $data = $request->validated();
            // dd($data) ; 
            // dd('wron') ; 
             
             $save = StickerMap::create([
                'StickerInfo' => $data['StickerInfo'],
                'sticker'     => $data['sticker'],
                'centerId'   => $data['center_id'],
                'remarks'     => $data['remarks'] ?? null,
                'created_by'     => auth()->id(), // or $data['user_id'] if you're passing it
                'created_at'  => now(),
                'updated_at'  => now(),
            ]);

            if($save){
                return redirect()->route('sticker_type.index')->with('success', 'Data Inserted successfully.');
            }
            else{
                 return redirect()->route('sticker_type.index')->with('error', 'Tag save failed!.');
            }
        } catch (Exception $e) {
             // dd($e->getMessage());
             if ($e instanceof \Illuminate\Database\QueryException &&
                    $e->errorInfo[1] == 1062) {

                    return redirect()
                        ->route('sticker_type.index')
                        ->with('error', 'Duplicate entry. This record already exists.');
                }

            return redirect()
                ->route('sticker_type.index')
                ->with('error', 'An unexpected error occurred.');

        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateStickerTypeRequest $request,  StickerMap $stickers) : RedirectResponse  
    {
         // dd($request->all());
        try {
            $data = $request->validated();
            // dd($data) ; 
            $sticker = StickerMap::findOrFail($data['stcId']);

            if($sticker){
                 $sticker->update([
                    'StickerInfo' => $data['StickerInfo'],
                    'sticker'     => $data['sticker'],
                    'centerId'    => $data['center_id'],
                    'remarks'     => $data['remarks'] ?? null,
                ]);
                return redirect()->route('sticker_type.index')->with('success', 'Data Updated Successfully');
            }
            else{
                 return redirect()->route('sticker_type.index')->with('error', 'Failed to Update');
            }
  
        } catch (\Exception $e) {
             // dd($e); 
            if ($e instanceof \Illuminate\Database\QueryException &&
                    $e->errorInfo[1] == 1062) {

                    return redirect()
                        ->route('sticker_type.index')
                        ->with('error', 'Duplicate entry. This record already exists.');
                }

            return redirect()
                ->route('sticker_type.index')
                ->with('error', 'An unexpected error occurred.');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(StickerMap $stickers): RedirectResponse
    {
         // dd($visaType->all());
        try {
            $stc = StickerMap::find($stickers->id); 
             if($stc){
                $stc->delete();
                return redirect()->route('sticker_type.index')->with('success', 'Data deleted successfully.');
             } 
             else{
                 return redirect()->route('sticker_type.index')->with('error', 'Data delete failed .');
             }
          


        } catch (\Exception $e) {
            info('data deleted failed!', [$e]);

            return redirect()->route('sticker_type.index')->with('error', 'Data deleted failed!.');
        }
    }
 

}
