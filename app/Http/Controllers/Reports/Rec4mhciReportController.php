<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;

use App\Models\User;
use App\Models\AppReceive ; 
use App\Models\AppLog ; 
use App\Models\Center;
use App\Models\Counter;
use App\Models\VisaTypeApt;
use App\Models\StickerMap;
use App\Models\SmsLog ; 

use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\View\View;

use Barryvdh\DomPDF\Facade\Pdf;
use App\Exports\Rec4mhciPrintExport;

class Rec4mhciReportController extends Controller
{

    // public function __construct(protected TransactionReportService $TransactionReportService) {}
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View 
    {
         // dd($request->all());
        try {
  
             $query = AppLog::query();
             $users = [] ; 
             $data=[] ; 
             $centers = [] ; 
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
                if($role== 5 ||  $role== 1 ||  $role== 13){
                    $userIds = AppLog::select('created_by')->whereBetween('Date',[$request->from_date, $request->to_date])->whereIn('stepId',[31,32,33]); 
                }
                else{
                    $userIds = AppLog::select('created_by')->whereBetween('Date',[$request->from_date, $request->to_date])->where('centerId',$cenId)->whereIn('stepId',[31,32,33]); 
                }
                if($userIds){
                    $users = User::select('id', 'name', 'email')
                             ->whereIn('id', $userIds)
                            ->where('centerId', $cenId)
                            ->whereHas('appaction')   
                            ->orderBy('name', 'asc')
                            ->get();    
                }


                 if($role == 5 ||  $role == 1 ||  $role == 13){
                    // dd('ok') ; 
                    $centers = Center::select('center_name','id')->where('status',1)->orderBy('center_name','asc')->get();
                     if ($request->filled('center')) {
                        $query->where('centerId', $request->center);
                     }
                 }
                 else{
                     $centers = Center::select('center_name','id')->where('status',1)->where('id',$cenId)->orderBy('center_name','asc')->get();
                      $query->where('centerId', $cenId);
                 }
                    // dd($centers) ; 
               
                if ($request->filled('user')) {
                    $query->where('created_by', $request->user);
                }

                // dd($users) ;         
                $data = $query->with(['center','user','webref'])
                    ->whereIn('stepId',[31,32,33])
                    ->orderBy('id', 'desc')
                    ->paginate(100)
                    ->appends($request->all());

            // }
            // else{
            //    $query->where('Date',date('Y-m-d'));
            // }
            // dd($query) ; 
          
             // dd($role) ; 
         

            return view('reports.rec4mhci_report.index', compact('data', 'centers', 'cenId','role','users'));
           
        } catch (Exception $e) {
             $mess = $e->getMessage(); 
             // dd($mess) ; 
        }
    }
    public function exportExcel(Request $request)
    {
        if ($request->filled('from_date') && $request->filled('to_date')) {
            return Excel::download(new Rec4mhciPrintExport($request), 'rec4mhci-report.xlsx');
        }
        else{
            return redirect()->route('rec4mhci-report.index')->with('error', 'Please select from & to Date');
        }

    }

    public function exportPDF(Request $request)
    {
        // dd($request->all()) ; 
        if ($request->filled('from_date') && $request->filled('to_date')) {
            
            $from =  $request->from_date ;
            $to =  $request->to_date ; 
            $role = auth()->user()->role_id ;   
            $cenId = auth()->user()->centerId ;   
            $query = AppLog::query();

            $query->whereBetween('Date', [
                $request->from_date  ,
                $request->to_date 
            ]);
            
            $cnt ='';
            $ccn= Center::select('center_name')->find( $request->center ) ; 
            if($ccn){
                $cnt =$ccn->center_name; 
            }

           
           if($role == 5 ||  $role == 1 ){
                if ($request->filled('center')) {
                    $query->where('centerId', $request->center);

                    $cnt ='';
                    $ccn= Center::select('center_name')->find( $request->center ) ; 
                    if($ccn){
                        $cnt =$ccn->center_name; 
                    }
                }
           }
           else{
               $query->where('centerId', $cenId);

                $cnt ='';
                $ccn= Center::select('center_name')->find( $cenId ) ; 
                if($ccn){
                    $cnt =$ccn->center_name; 
                }
           }
     
            // dd($query) ; 
            $usr =''; 
            if ($request->filled('user')) {
                $query->where('created_by', $request->user);
                $us = User::select('name')->find($request->user) ; 
                if($us){
                     $usr = $us->name ; 
                }
            }


            $data = $query->with(['center','user','webref'])->whereIn('stepId',[31,32,33])->get();

            // dd($data) ; 
            $pdf = Pdf::loadView('reports.pdf.rec4mhci',compact('data','from','to','cnt','usr'))->setPaper('a4', 'landscape');
            return $pdf->download('rec4mhci.pdf');

        }
        else{
            return redirect()->route('rec4mhci-report.index')->with('error', 'Please select from & to Date');
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
