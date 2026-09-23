<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreBarcodeRequest;
use App\Http\Requests\UpdateRegionRequest;
use App\Models\Region;
use App\Models\Center;
use App\Models\StickerMap;
use App\Models\StickerPrintLog;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use App\Services\ActionExceptionService;

class StickerPrintController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $perPage = $request->input('per_page', 10);
        $search = $request->input('search');

        try {

            $stickerTypeList = StickerMap::select('sticker','centerId','id',)->orderBy('sticker','asc')->get();
            $centerList = Center::select('center_name','id')->where('status',1)->orderBy('center_name','asc')->get();

           return view('pages.BarcodeSticker.index', [
                'stickerTypeList' => $stickerTypeList,
                'centerList' => $centerList,
            ]);
     
        } catch (Exception $e) {
             // dd($e->getMessage()); 
            return redirect()->back()->with('error', $e->getMessage());
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
    public function store(StoreBarcodeRequest $request): View
    // public function store(Request $request)
    {
       // dd($request->all());
        try {
             // dd($data) ; 
            $data = $request->validated();
            // dd($data) ; 
            $cn = Center::select('center_name')->where('id', $data['centerId'])->first() ; 
            $st = StickerMap::select('sticker')->where('id',$data['sticker'] )->first();
            // dd($st->sticker) ; 
            $selectedDate = Date('Y-m-d',strtotime($data['date']));
            $date = Date('ymd',strtotime($data['date']));
            $sticker = $st->sticker;
            $center = substr($cn->center_name,0,2);
          
            $startNo =$data['start_number'];
            $endNo = $data['end_number'];
            $barcode = '@'.$center.$date.$sticker;
            
                        // $userId  = Auth::user()->user_id;
            StickerPrintLog::create([
                'Date'=>$selectedDate,
                'stickerId'=>  $data['sticker'],
                'startNo'=> $data['start_number'],
                'endNo'=> $data['end_number'],
                'created_by'=> $data['created_by'],
                'centerId'=>$data['centerId'],
                'created_at'=>Date('Y-m-d H:i:s'),
                'updated_at'=>Date('Y-m-d H:i:s'),
            ]);

            $stickerId = $data['sticker'];
            $centerId  = $data['centerId'];

            $overlaps = StickerPrintLog::where('stickerId', $stickerId)
                ->where('centerId', $centerId)
                ->where('Date', $selectedDate)
                ->where(function ($q) use ($startNo, $endNo) {
                    $q->where('startNo', '<=', $endNo)
                      ->where('endNo', '>=', $startNo);
                })
                ->get();

            $duplicates = [];

            if ($overlaps->count() > 0) {
                // Count each sticker number in overlapping ranges
                foreach ($overlaps as $row) {
                    $from = max($row->startNo, $startNo);
                    $to   = min($row->endNo, $endNo);

                    for ($i = $from; $i <= $to; $i++) {
                        $duplicates[$i] = ($duplicates[$i] ?? 0) + 1;
                    }
                }

                // Keep only numbers that occur more than once
                $duplicates = array_filter($duplicates, fn($count) => $count > 1);

                if (!empty($duplicates)) {
                    ActionExceptionService::store([
                        'module'      => 'Duplicate Sticker',
                        'action'      => 'DuplicateSticker',
                        'centerId'      => $centerId ,
                        'remarks'     => json_encode(array_keys($duplicates)), // save only duplicated numbers
                    ]);
                }
            }




            // dd($dataB) ; 
            return view('pages.BarcodeSticker.sticker_barcode_print', [
                'barcode' => $barcode,
                'startNo' => $startNo,
                'endNo' =>  $endNo,
            ]);
     
        } catch (Exception $e) {
             dd($e->getMessage()); 
            info('Region data insert  failed!', [$e]);

            return redirect()->route('pages.BarcodeSticker.index')->with('error', 'Barcode Insert failed!.');
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
           $is_update = Region::where('id',$data['recId'])->update([
                'region_name'   => $data['region_name'],
                'region_text'   =>  $data['region_text'], 
                'status'       =>  $data['status'], 
                'updated_at'=>Date('Y-m-d H:i:s')
            ]);

           if($is_update){
                return redirect()->route('regions.index')->with('success','Data Updated Successfully');    
            }
            else{
                 return redirect()->route('regions.index')->with('error','Failed to  Update');   
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
         // dd($region);
        try {
            $region->delete();

            return redirect()->route('regions.index')->with('success', 'Region deleted successfully.');

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
