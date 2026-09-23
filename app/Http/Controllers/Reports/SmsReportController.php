<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;

use App\Models\User;
use App\Models\AppReceive ; 
use App\Models\Center;
use App\Models\Counter;
use App\Models\VisaTypeApt;
use App\Models\StickerMap;
use App\Models\SmsLog ; 

use App\Services\Reports\TransactionReportService;
use App\Services\Reports\SearchReportService;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\View\View;

use Barryvdh\DomPDF\Facade\Pdf;
use App\Exports\SmsPrintExport;

class SmsReportController extends Controller
{

    // public function __construct(protected TransactionReportService $TransactionReportService) {}
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View 
    {
         // dd($request->all());
        try {
             $query = SmsLog::query();

        // Filters
            if ($request->filled('from_date') && $request->filled('to_date')) {
                $query->whereBetween('Date', [
                    $request->from_date,
                    $request->to_date
                ]);
            }
            else{
               $query->where('Date',date('Y-m-d'));
            }
            // dd($query) ; 
             $cenId = auth()->user()->centerId ; 
             $role = auth()->user()->role_id ;    
             // dd($role) ; 
             if($role== 5 ||  $role== 1 ||  $role== 13){
                $centers = Center::select('center_name','id')->where('status',1)->orderBy('center_name','asc')->get();
                 if ($request->filled('center')) {
                    $query->where('centerId', $request->center);
                 }
             }
             else{
                 $centers = Center::select('center_name','id')->where('status',1)->where('id',$cenId)->orderBy('center_name','asc')->get();
                  $query->where('centerId', $cenId);
             }

           
            if ($request->filled('type')) {
                $query->where('type', $request->type);
            }

            $data = $query->with(['center','web'])
                ->orderBy('id', 'desc')
                ->paginate(5000)
                ->appends($request->all());

     

            return view('reports.sms_report.index', compact('data', 'centers', 'cenId','role'));
           
        } catch (Exception $e) {
             $mess = $e->getMessage(); 
             // dd($mess) ; 
        }
    }
    public function exportExcel(Request $request)
    {
        if ($request->filled('from_date') && $request->filled('to_date')) {
            return Excel::download(new SmsPrintExport($request), 'sms_report.xlsx');
        }
        else{
            return redirect()->route('sms-report.index')->with('error', 'Please select from & to Date');
        }

    }

    public function exportPDF(Request $request)
    {
        // dd($request->all()) ; 
        if ($request->filled('from_date') && $request->filled('to_date')) {
            
            $from =  $request->from_date ;
            $to =  $request->to_date ; 

            $query = SmsLog::query();

            $query->whereBetween('Date', [
                $request->from_date  ,
                $request->to_date 
            ]);

            $cnt ='';
            $ccn= Center::select('center_name')->find( $request->center ) ; 
            if($ccn){
                $cnt =$ccn->center_name; 
            }

           if ($request->filled('center')) {
                $query->where('centerId', $request->center);
            }
            // dd($query) ; 

            $type =$request->type;
            if ($request->filled('type')) {
                $query->where('type', $request->type);
            }

            $data = $query->with(['center', 'web'])->get();

            // dd($data) ; 
            $pdf = Pdf::loadView('reports.pdf.sms_detail',compact('data','from','to','cnt','type'))->setPaper('a4', 'landscape');
            return $pdf->download('sms_detail.pdf');

        }
        else{
            return redirect()->route('sms-report.index')->with('error', 'Please select from & to Date');
        }


    }

    public function search(Request $request): View|RedirectResponse
    {
        // dd($request->all()) ; 
        $from = $request->input('from_date');

        $res = AppReceive::where('Date','>=',$from)->paginate(25);  

        // dd($res) ; 

        return view('reports.receive_reports.search_result', [
                    // 'shop' => $shop, 
                    // 'customer' =>  $input2, 
                    //  'audit' =>  '', 
                    'query' => $res
                     
                ]  );
    }
   
}
