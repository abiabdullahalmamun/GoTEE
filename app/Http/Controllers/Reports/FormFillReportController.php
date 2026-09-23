<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;

use App\Models\User;
use App\Models\FormFill ; 
use App\Models\Center;
use App\Models\Counter;
use App\Models\VisaType;
use App\Models\StickerMap;

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
use App\Exports\FormFillPrintExport;

class FormFillReportController extends Controller
{

    // public function __construct(protected TransactionReportService $TransactionReportService) {}
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View 
    {
         // dd($request->all());
        try {
             $query = FormFill::query();

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

             $cenId = auth()->user()->centerId ; 
             $role = auth()->user()->role_id ;    
             // dd($role) ; 
             if($role== 5 ||  $role== 1 ||  $role== 13){
                $centers = Center::select('center_name','id')->where('status',1)->orderBy('center_name','asc')->get();
                 if ($request->filled('center')) {
                    $query->where('centerId', $request->center);
                 }
                 $cenId =''; 
             }
             else{
                 $centers = Center::select('center_name','id')->where('status',1)->where('id',$cenId)->orderBy('center_name','asc')->get();
                  $query->where('centerId', $cenId);
             }

          
            if ($request->filled('user')) {
                $query->where('created_by', $request->user);
            }
 
            $data = $query->with(['center', 'user' ])
                ->orderBy('id', 'desc')
                ->paginate(5000)
                ->appends($request->all());

             // dd($data) ;    
              $users = User::select('id', 'name', 'email')
                    ->where('centerId', $cenId)
                    // ->whereHas('appReceive')   
                    ->orderBy('name', 'asc')
                    ->get();  


             // $users = User::select('id','name','email')->where('centerId',$cenId)->orderby('name','asc')->get() ; 
 

            return view('reports.formfill_reports.index', compact('data', 'centers', 'users','cenId','role'));
           
        } catch (Exception $e) {
             $mess = $e->getMessage(); 
             dd($mess) ; 
               return view('reports.formfill_reports.index', [
                    'data' => collect(),
                    'centers' => collect(),
                    'users' => collect(),
                    'cenId' => null,
                    'role' => null,
                      'error' => $e->getMessage()
                ]);
        }
    }
    public function exportExcel(Request $request)
    {
        if ($request->filled('from_date') && $request->filled('to_date')) {
            return Excel::download(new FormFillPrintExport($request), 'formfill-Report.xlsx');
        }
        else{
            return redirect()->route('formfill-Report.index')->with('error', 'Please select from & to Date');
        }

    }

    public function exportPDF(Request $request)
    {
        // dd($request->all()) ; 
        if ($request->filled('from_date') && $request->filled('to_date')) {
            
            $from =  $request->from_date ;
            $to =  $request->to_date ; 

            $query = AppReceive::query();

            $query->whereBetween('Date', [
                $request->from_date  ,
                $request->to_date 
            ]);

            $usr =  '' ; 
            $us = User::select('name')->find($request->user) ;
            if($us){
                $usr  =$us->name ; 
            }

            $cnt ='';
            $cn = Center::select('center_name')->find( $request->center ) ; 
            if($cn){
                $cnt =$cn->center_name; 
            }

            $vst ='';
            $vs = VisaType::select('visa_type')->find( $request->visa ) ; 
            if($vs){
                $vst =$vs->visa_type; 
            }

            $stc ='';
            $st= StickerMap::select('sticker')->find( $request->sticker ) ; 
            if($st){
                $stc =$st->sticker; 
            }


           if ($request->filled('center')) {
                $query->where('centerId', $request->center);
            }
            // dd($query) ; 

            if ($request->filled('user')) {
                $query->where('created_by', $request->user);
            }
            if ($request->filled('visa')) {
                $query->where('visatype', $request->visa);
            }
            if ($request->filled('sticker')) {
                $query->where('stickertype', $request->sticker);
            }
             $paymentSummary = (clone $query)
                ->select('pmethod', DB::raw('COUNT(*) as total'))
                ->groupBy('pmethod')
                ->pluck('total', 'pmethod');
            
            $corrTotal = (clone $query)
                        ->where('corrFee','>',0)
                        ->sum('corrFee') ; 
                        
            $data = $query->with(['center', 'user','visa','sticker'])->get();

            // dd($data) ; 
            
            $pdf = Pdf::loadView('reports.pdf.app-receive',compact('data','from','to','cnt','usr','stc','vst','paymentSummary','corrTotal'))->setPaper('a4', 'landscape');
            return $pdf->download('app_receive_report.pdf');

        }
        else{
            return redirect()->route('Receive-Report.index')->with('error', 'Please select from & to Date');
        }


    }

    public function search(Request $request): View|RedirectResponse
    {
        dd($request->all()) ; 
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
    public function summary(Request $request): View|RedirectResponse
    {
        // dd($request->all()) ; 

        if ($request->filled('from_date') && $request->filled('to_date')) {
             // dd($request->all()) ; 
            $from = $request->input('from_date');
            $to = $request->input('to_date');
//summary_type
            $query = AppReceive::query();

            $query->whereBetween('Date', [
                $request->from_date  ,
                $request->to_date 
            ]);
            if ($request->filled('center')) {
                $query->where('centerId', $request->center);
            }
            // dd($query) ; 

            if ($request->filled('user')) {
                $query->where('created_by', $request->user);
            }

            if($request->input('summary_type')=='visa'){
                $data = $query
                    ->select('visatype', DB::raw('COUNT(*) as total'))
                    ->groupBy('visatype')
                    ->with('visa') // to get visa name if relation exists
                    ->get();
                 $report = "VisaType Summary"   ; 
                 $grandTotal = $data->sum('total');

            }
            else{
                 $data = $query
                    ->select('stickertype', DB::raw('COUNT(*) as total'))
                    ->groupBy('stickertype')
                    ->with('sticker') // to get visa name if relation exists
                    ->get();
                 $report = "Sticker Summary"   ; 
                 $grandTotal = $data->sum('total');
            }

            $cnt ='All';
            $cn = Center::select('center_name')->find( $request->center ) ; 
            if($cn){
                $cnt =$cn->center_name; 
            }
            // dd($data) ; 
            return view('reports.receive_reports.receive-summary', [
                        'from_date' => $from,
                        'to_date' => $to,
                        'center' => $request->input('center'),
                        'results' => $data,
                        'report' => $report,
                        'grandTotal' => $grandTotal,
                        'center' => $cnt
                           
                    ]);
        }
        else{
              return redirect()->route('Receive-Report.index')->with('error', 'Please select from & to Date');
        }
    }
   
}
