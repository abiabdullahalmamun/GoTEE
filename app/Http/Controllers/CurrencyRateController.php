<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCurrencyRateRequest;
use App\Http\Requests\UpdateCurrencyRateRequest;
use App\Models\CurrencyRate;
use App\Models\Center;
use App\Models\Service;
use App\Models\DeviceSvc ; 
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CurrencyRateController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        // $perPage = $request->input('per_page', 10);
        // $search = $request->input('search');
        // dd($search) ; 
        try {

            $query = CurrencyRate::query()->latest('id');

        // dd($query) ; 
            $query = $query->paginate(500);
            $center = Center::select('id','center_name')->where('status',1)->orderby('center_name','asc')->get() ; 
           
           $service = Service::select('id', 'service_name')->where('status',1)->orderby('service_name','asc')->get() ; 
            // $states = [0 => 'LoggedOut'] + $svc->pluck('service_name', 'id')->toArray();
            return view('pages.currencyRate.index', with(['query' => $query, 'centerList' =>$center, 'serviceList' =>$service]));

            // return view('pages.devices.index', with(['query' => $query]));

        } catch (Exception $e) {
            // info('Error showing Floor!', [$e]);
             $mess = $e->getMessage(); 
             // dd($mess) ; 
            return redirect()->back()->with('error', 'device showing failed!.');
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCurrencyRateRequest $request): RedirectResponse
    {
       // dd($request->all());
        try {
            $data = $request->validated();
            // dd($data) ; 
            
            $save=CurrencyRate::create([
                'currency_name'   => $data['nameD'],
                 'currency_rate'   => $data['rate'],
                 'created_by'   => $data['created_by'],
                'created_at'=> now(),
                'updated_at'=>now()
            ]);
            if($save){
                $lastId = $save->id;
              
                return redirect()->route('currencyRate.index')->with('success', 'Data Inserted successfully.');
            }
            else{
                 return redirect()->route('currencyRate.index')->with('error', 'Data Insert failed!.');
            }

        } catch (Exception $e) {
             // dd($e); 
    
              $mess = $e->getMessage(); 
            // dd($mess) ; 
            if (str_contains($mess, 'Duplicate entry')) {
                 return redirect()->route('currencyRate.index')->with('error', 'Data Already Exists');
            }
            else{
                return redirect()->route('currencyRate.index')->with('error', 'Insert failed '.$mess);
            }
        }
    }

    /**
     * Update the specified resource in storage.
     */
      // public function update(Request $request ) 
    public function update(UpdateReceiptRequest $request, MoneyReceipt $moneyreceipt): RedirectResponse
    {
         // dd($request->all());
        try {
            $data = $request->validated();
  // dd($data) ; 

           if($data['endNo']<=$data['startNo']){
                return redirect()->route('currencyRate.index')->with('error', 'Please select StartNo and EndNo properly.');
               
            }

            $receipt = MoneyReceipt::find($data['recId']);
            if($receipt){
                 $receipt->BookNo = $data['BookNo'];
                $receipt->startNo = $data['startNo'];
                $receipt->endNo = $data['endNo'];
                $receipt->status = $data['status'];
                $receipt->updated_at = now(); 
                $receipt->save(); // Fires 'updated' event → Loggable works

                return redirect()->route('currencyRate.index')->with('success', 'Data Updated Successfully');
            }
            else{
                return redirect()->route('currencyRate.index')->with('error', 'Failed to Update');
            }
       
        } catch (\Exception $e) {
              $mess = $e->getMessage(); 
            // dd($mess) ; 
            if (str_contains($mess, 'Duplicate entry')) {
                 return redirect()->route('currencyRate.index')->with('error', 'Receipt Already Exists');
            }
            else{
                return redirect()->route('moneyReceipt.index')->with('error', 'Insert failed '.$mess);
            }
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(CurrencyRate $currency): RedirectResponse
    {
         // dd($currency->all());
        try {
          $CurrencyRate = CurrencyRate::find($currency->id); 
             if($CurrencyRate){
                $CurrencyRate->delete();
                return redirect()->route('currencyRate.index')->with('success', 'Data deleted successfully.');
             } 
             else{
                 return redirect()->route('currencyRate.index')->with('error', 'Data delete failed .');
             }
          
        } catch (\Exception $e) {
            info('Shop deleted failed!', [$e]);

            return redirect()->route('currencyRate.index')->with('error', 'Data deleted failed!.');
        }
    }


 
 
}
