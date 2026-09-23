<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;

use App\Models\User;
use App\Models\AppReceive ; 
use App\Models\Center;
use App\Models\Counter;
use App\Models\VisaType;
use App\Models\StickerMap;
use App\Models\ForeignPass;
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
use App\Exports\FrpReceiveExport;

class FrpReceiveReportController extends Controller
{

    // public function __construct(protected TransactionReportService $TransactionReportService) {}
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View 
    {
         // dd($request->all());
        try {
             $query = ForeignPass::query();

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

            // $paymentSummary = (clone $query)
            //     ->select('pmethod', DB::raw('COUNT(*) as total'))
            //     ->groupBy('pmethod')
            //     ->pluck('total', 'pmethod');
            
            // $corrTotal = (clone $query)
            //             ->where('corrFee','>',0)
            //             ->sum('corrFee') ; 

            // $data = $query->with(['center', 'user','visa','sticker'])
            //     ->orderBy('id', 'desc')
            //     ->paginate(5000)
            //     ->appends($request->all());
            $sumTotalAmount = (clone $query)->sum('total_amount');

             $data = $query->with(['center','user','webref'])
                    ->orderBy('id', 'desc')
                    ->paginate(5000)
                    ->appends($request->all());
             // dd($data) ;    
              $users = User::select('id', 'name', 'email')
                    ->where('centerId', $cenId)
                    ->whereHas('appReceive')   
                    ->orderBy('name', 'asc')
                    ->get();  


             // $users = User::select('id','name','email')->where('centerId',$cenId)->orderby('name','asc')->get() ; 

            //  $VisaType = VisaType::select('visa_type','id' )->where('status',1)->orderBy('visa_type','asc')->get() ; 
            
            // $stickerTypeList = StickerMap::select('sticker','centerId','id',)->orderBy('sticker','asc')->get();

            return view('reports.foreign_reports.index', compact('data', 'centers', 'users','cenId','role','sumTotalAmount'));
           
        } catch (Exception $e) {
             $mess = $e->getMessage(); 
             dd($mess) ; 
               return view('reports.foreign_reports.index', [
                    'data' => collect(),
                    'centers' => collect(),
                    'users' => collect(),
                    'cenId' => null,
                    'role' => null,
                    'VisaType' => collect(),
                    'stickerTypeList' => collect(),
                    'paymentSummary' => collect(),
                    'corrTotal' => 0,
                    'error' => $e->getMessage()
                ]);
        }
    }
    public function exportExcel(Request $request)
    {
        if ($request->filled('from_date') && $request->filled('to_date')) {
            return Excel::download(new FrpReceiveExport($request), 'foreign_passport_report.xlsx');
        }
        else{
            return redirect()->route('frpReceiveReport.index')->with('error', 'Please select from & to Date');
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
            return redirect()->route('frpReceiveReport.index')->with('error', 'Please select from & to Date');
        }


    }

    public function search(Request $request): View|RedirectResponse
    {
        // dd($request->all()) ; 
        $from = $request->input('from_date');

        $res = ForeignPass::where('Date','>=',$from)->paginate(25);  

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
         $cenId = auth()->user()->centerId ; 
         $role = auth()->user()->role_id ;    
        $data=[] ; 
        $centers = [] ; 
        $users = [] ; 
         if (! $request->filled('from_date') || ! $request->filled('to_date')) {
            $sumTotalAmount = 0 ; 
              return redirect()->back()->with('error', 'From and To date required');
       }

        $from = $request->from_date;
        $to   = $request->to_date;
        // dd($from) ; 
        $query = ForeignPass::query()
            ->whereBetween('Date', [$from, $to]);

        $cnt = 'All';
      
        if ($request->filled('center')) {
             $cn = Center::select('center_name')->find($request->center);
            $cnt = $cn->center_name ?? 'All';
            $query->where('centerId', $request->center);
        }

        if ($request->filled('user')) {
            $query->where('created_by', $request->user);
        }
 
        $data = $query->with(['center','user','webref'])
                    ->orderBy('id', 'desc')
                    ->paginate(5000)
                    ->appends($request->all());
        // dd($data) ; 
         $summaryType = $request->input('summary_type');
        $maxRate = (clone $query)->whereNotNull('rupee_rate')->max('rupee_rate');
        $bookNo =  (clone $query)->whereNotNull('BookNo')->max('BookNo');
        $recMin =  (clone $query)->whereNotNull('ReceiptNo')->min('ReceiptNo');
        $recMax =  (clone $query)->whereNotNull('ReceiptNo')->max('ReceiptNo');
       
        $totalBDTvisa = (clone $query)->sum('visa_fee');
        $totalBDTfax =  (clone $query)->sum('fax_trans_charge');
        $totalBDTicwf = (clone $query)->sum('icwf');
        $totalBDTvisaapp = (clone $query)->sum('visa_app_charge');
       // dd($data) ; 
        $totalBDT =  $totalBDTvisa+$totalBDTfax+$totalBDTvisaapp ; 
        $totalRsvisa =  $totalBDTvisa/$maxRate ; 
        $totalRsfax = $totalBDTfax/$maxRate ; 
        $totalRsvisaapp = $totalBDTvisaapp/$maxRate ; 
        $totalRs =  $totalRsvisa+ $totalRsfax +$totalRsvisaapp ; 

        // $totalBDTicwf = (clone $query)->sum('icwf');
        $totalRsicwf =  $totalBDTicwf/$maxRate ; 

        $totalAllBDT =  $totalBDTvisa+ $totalBDTfax+ $totalBDTicwf+ $totalBDTvisaapp ; 
        if($from==$to){
            $formattedDate = Carbon::parse($from)->format('d-m-Y');
        }
        else{
            $formattedDate = Carbon::parse($from)->format('d-m-Y').' - '.Carbon::parse($to)->format('d-m-Y');
        }

        if($summaryType=='visafax'){
            
               return view('reports.foreign_reports.visafax', [
                    'bookNo' =>  '('.$bookNo.') '.$recMin.' - '.$recMax,
                    'totalBDTvisa' =>$totalBDTvisa , 
                    'totalBDTfax' =>$totalBDTfax , 
                    'totalBDTvisaapp' =>$totalBDTvisaapp , 
                    'totalBDT' =>$totalBDT , 
                    
                    'totalRsvisa' =>$totalRsvisa , 
                    'totalRsfax' =>$totalRsfax , 
                     'totalRsvisaapp' =>$totalRsvisaapp  , 
                    'totalRs' =>$totalRs , 
                    'formattedDate' => $formattedDate ,
                ]);
        }
        else if($summaryType=='icwf'){
                return view('reports.foreign_reports.icwf', [
                    'bookNo' =>  '('.$bookNo.') '.$recMin.' - '.$recMax,
                     'totalBDTicwf' =>$totalBDTicwf , 
                    'totalRsicwf' =>$totalRsicwf , 
                    'formattedDate' => $formattedDate ,
                ]);
        }
        else if($summaryType=='sendingstatus'){
               return view('reports.foreign_reports.sendingstatus', [
                    'data' =>  $data,
                     // 'from' => $from , 
                     //   'to' => $to ,  
                        'formattedDate' => $formattedDate ,  
                ]);
        }
        else if($summaryType=='statement'){
               return view('reports.foreign_reports.statement', [
                    'data' =>  $data,
                    'totalBDTvisa' =>  $totalBDTvisa ,
                    'totalBDTfax' =>  $totalBDTfax ,
                    'totalBDTicwf' =>  $totalBDTicwf,
                    'totalBDTvisaapp' =>  $totalBDTvisaapp,
                    'totalAllBDT' =>  $totalAllBDT,
                    'formattedDate' => $formattedDate ,
                    'bookNo' => $bookNo ,
                    'recMin' =>$recMin ,    
                    'recMax' =>$recMax ,
                ]);
        }
    }
  
}
