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
use App\Exports\ReadyCenterPrintExport;

class ReadyCenterReportController extends Controller
{

    // public function __construct(protected TransactionReportService $TransactionReportService) {}
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View 
    {
         // dd($request->all());
        try {
             $cenId = auth()->user()->centerId ; 
             $role = auth()->user()->role_id ;    
             $query = AppLog::query();
             $users = [] ; 
             $centers = [] ; 
             $data=[] ; 
        // Filters
            if ($request->filled('from_date') && $request->filled('to_date')) {
                $query->whereBetween('Date', [
                    $request->from_date,
                    $request->to_date
                ]);

                if($role== 5 ||  $role== 1 ){
                    $userIds = AppLog::select('created_by')->whereBetween('Date',[$request->from_date, $request->to_date])->where('stepId',4); 
                }
                else{
                    $userIds = AppLog::select('created_by')->whereBetween('Date',[$request->from_date, $request->to_date])->where('centerId',$cenId)->where('stepId',4); 
                }
                if($userIds){
                    $users = User::select('id', 'name', 'email')
                             ->whereIn('id', $userIds)
                            ->where('centerId', $cenId)
                            ->whereHas('appaction')   
                            ->orderBy('name', 'asc')
                            ->get();    
                }


                 if($role== 5 ||  $role== 1 ){
                    $centers = Center::select('center_name','id')->where('status',1)->orderBy('center_name','asc')->get();
                     if ($request->filled('center')) {
                        $query->where('centerId', $request->center);
                     }
                 }
                 else{
                     $centers = Center::select('center_name','id')->where('status',1)->where('id',$cenId)->orderBy('center_name','asc')->get();
                      $query->where('centerId', $cenId);
                 }

               
                if ($request->filled('user')) {
                    $query->where('created_by', $request->user);
                }

                // dd($users) ;         
                $data = $query->with(['center','user','webref'])
                    ->where('stepId',4)
                    ->orderBy('id', 'desc')
                    ->paginate(5000)
                    ->appends($request->all());

            }
             // dd($cenId) ; 
            return view('reports.readyCenter_report.index', compact('data', 'centers', 'cenId','role','users'));
           
        } catch (Exception $e) {
             $mess = $e->getMessage(); 
             // dd($mess) ; 
        }
    }
    public function exportExcel(Request $request)
    {
        if ($request->filled('from_date') && $request->filled('to_date')) {
            return Excel::download(new ReadyCenterPrintExport($request), 'readyCenter-report.xlsx');
        }
        else{
            return redirect()->route('readyCenter-report.index')->with('error', 'Please select from & to Date');
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


            $data = $query->with(['center','user','webref'])->where('stepId',4)->get();

            // dd($data) ; 
            $pdf = Pdf::loadView('reports.pdf.readyCenter',compact('data','from','to','cnt','usr'))->setPaper('a4', 'landscape');
            return $pdf->download('readyCenter.pdf');

        }
        else{
            return redirect()->route('readyCenter-report.index')->with('error', 'Please select from & to Date');
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
