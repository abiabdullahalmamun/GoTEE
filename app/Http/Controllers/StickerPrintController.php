<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreBarcodeRequest;
use App\Http\Requests\UpdateRegionRequest;

use App\Models\OrderNo ; 
use App\Models\StickerMap;
use App\Models\StickerPrintLog;
use App\Models\BundleInfo;
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
            $orderList = OrderNo::where('status',1)->get() ; 

           return view('pages.BarcodeSticker.index', [
                'orderList' => $orderList,
            ]);
     
        } catch (Exception $e) {
             // dd($e->getMessage()); 
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

 
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
            // dd($st->sticker) ; 
            $selectedDate = Date('Y-m-d',strtotime($data['date']));
            $date = Date('ymd',strtotime($data['date']));
            // $sticker = $st->sticker;
            // $center = substr($cn->center_name,0,2);
            $ord = OrderNo::where('OrderCode',$data['orderCode'])->first() ; 
            // dd($ord) ; 
            $startNo =$data['start_number'];
            $endNo = $data['end_number'];
            // $barcode = '@'.$center.$date.$sticker;
      
              $ids = [];
            for ($i = $startNo; $i <= $endNo; $i++) {

                $record = BundleInfo::updateOrCreate(
                    [
                        'bundle_no' => $i,
                        'CutNo'     => 1,
                        'OrderCode' => $data['orderCode'],
                    ],
                    [
                        'Date'       => $selectedDate,
                        'batchNo'    => 1,
                        'bundle_qty' => 1,
                        'operator'   => $data['created_by'],
                    ]
                );

                $ids[] = $record->id;
            }
            // dd($dataB) ; 
            return view('pages.BarcodeSticker.qr_code_print', [
                'ids' => $ids,
                'orderCode' =>$data['orderCode'] ,
                 'OrderNo' =>  $ord->OrderNo ,
                 'attr' => $ord->attr ,
                'Style' => $ord->Style ,
                 'buyer' => $ord->buyer ,
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
