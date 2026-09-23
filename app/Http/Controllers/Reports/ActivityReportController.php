<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;

use App\Models\User;
use App\Models\AppReceive ; 
use App\Models\AppLog ; 
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
use App\Exports\ActivitySummaryExport;

class ActivityReportController extends Controller
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
             // dd($role) ; 
             if($role== 5 ||  $role== 1 ||  $role== 13){
                $centers = Center::select('center_name','id')->where('status',1)->orderBy('center_name','asc')->get();
                 $cenId =''; 
             }
             else{
                 $centers = Center::select('center_name','id')->where('status',1)->where('id',$cenId)->orderBy('center_name','asc')->get();
                  // $query->where('centerId', $cenId);
             }

               $users = User::select('id', 'name', 'email')
                    ->where('centerId', $cenId)
                    ->whereHas('appReceive')   
                    ->orderBy('name', 'asc')
                    ->get();  
             $data = [] ; 
             $from = $request->input('from_date');   // e.g. 2025-10-01
             $to   = $request->input('to_date');     // e.g. 2025-10-03

             if ($request->filled('from_date') && $request->filled('to_date')) {

                $logQuery = AppLog::select(
                        'created_by',
                        DB::raw("SUM(CASE WHEN stepId = '1' THEN 1 ELSE 0 END) as total_rec"),
                        DB::raw("SUM(CASE WHEN stepId = '4' THEN 1 ELSE 0 END) as total_ready"),
                        DB::raw("SUM(CASE WHEN stepId = '5' THEN 1 ELSE 0 END) as total_del"),
                        DB::raw("SUM(CASE WHEN stepId = '11' THEN 1 ELSE 0 END) as total_bio"),
                        DB::raw("0 as total_forms")
                    )
                    ->when($from && $to, fn($q) => $q->whereBetween('Date', [$from, $to]))
                    ->where('remarks', '!=', 'ForceBySystem')
                    ->whereNotNull('created_by')
                    ->whereIn('stepId', [1,4,5,11])
                    ->when($request->filled('center'), function($q) use ($request) {
                        $q->where('centerId', $request->center);
                    })
                    ->groupBy('created_by');

                $formQuery = DB::table('tbl_form_fill')
                    ->select(
                        'created_by',
                        DB::raw("0 as total_rec"),
                        DB::raw("0 as total_ready"),
                        DB::raw("0 as total_del"),
                        DB::raw("0 as total_bio"),
                        DB::raw("COUNT(*) as total_forms")
                    )
                    ->when($request->filled('center'), function($q) use ($request) {
                        $q->where('centerId', $request->center);
                    })
                    ->when($from && $to, fn($q) => $q->whereBetween('Date', [$from, $to]))
                    ->groupBy('created_by');

                $data = DB::query()
                    ->fromSub($logQuery->unionAll($formQuery), 'u')
                    ->select(
                        'created_by',
                        DB::raw('SUM(total_rec) as total_rec'),
                        DB::raw('SUM(total_ready) as total_ready'),
                        DB::raw('SUM(total_del) as total_del'),
                        DB::raw('SUM(total_bio) as total_bio'),
                        DB::raw('SUM(total_forms) as total_forms'),
                        DB::raw('(SUM(total_rec) + SUM(total_ready) + SUM(total_del) + SUM(total_bio) + SUM(total_forms)) as total_all')
                    )
                    ->groupBy('created_by')
                    ->get();

 
                $data->each(function ($item) {
                    $item->user = User::with('center')->find($item->created_by);
                });


                $net_rec     = $data->sum('total_rec');
                $net_ready   = $data->sum('total_ready');
                $net_del     = $data->sum('total_del');
                $net_bio     = $data->sum('total_bio');
                $net_forms   = $data->sum('total_forms');
                $net_total   = $data->sum('total_all');
             }
             else{
                $net_rec     = '';
                $net_ready   = '';
                $net_del     = '';
                $net_bio   = '';
                $net_total     = '';
                $net_forms   = '';
             }
             // dd($data) ; 
            return view('reports.activity_reports.index', compact('data', 'from', 'to','centers','cenId','role','users','net_rec','net_del','net_ready','net_bio','net_forms','net_total'));
           
        } catch (Exception $e) {
             $mess = $e->getMessage(); 
             dd($mess) ; 
        }
    }
    public function exportExcel(Request $request)
    {
         // dd($request->all()) ; 
        if ($request->filled('from_date') && $request->filled('to_date')) {
            return Excel::download(new ActivitySummaryExport($request), 'Activity-Summary.xlsx');
        }
        else{
           return redirect()->route('daily-activity.index')->with('error', 'Please select Date & Center');
        }

    }

    public function exportPDF(Request $request)
    {
        // dd($request->all()) ; 
         if ($request->filled('from_date') && $request->filled('to_date')) {
                         $data = [] ; 
             $from = $request->input('from_date');   // e.g. 2025-10-01
             $to   = $request->input('to_date');     // e.g. 2025-10-03
            
            // $query = AppLog::select(
            //             'created_by',
            //         DB::raw("SUM(CASE WHEN stepId = '1' THEN 1 ELSE 0 END) as total_rec"),
            //         DB::raw("SUM(CASE WHEN stepId = '4' THEN 1 ELSE 0 END) as total_ready"),
            //         DB::raw("SUM(CASE WHEN stepId = '5' THEN 1 ELSE 0 END) as total_del"),
            //         DB::raw("SUM(CASE WHEN stepId = '11' THEN 1 ELSE 0 END) as total_bio"),
                
            //         DB::raw("(SELECT COUNT(*) FROM tbl_form_fill t2 WHERE t2.created_by = tbl_application_log.created_by) AS total_forms"),
            //         DB::raw("(SUM(CASE WHEN stepId IN ('1','4','5','11') THEN 1 ELSE 0 END)
            //             +
            //             (SELECT COUNT(*) FROM tbl_form_fill t2 WHERE t2.created_by = tbl_application_log.created_by)
            //         ) AS total_all")
            //     )
            //     ->when($from && $to, function($q) use ($from, $to) {
            //         $q->whereBetween('Date', [$from, $to]);
            //     })
            //      ->whereNotNull('created_by')
            //     ->whereIn('stepId',[1,4,5,11])
            //     ->groupBy('created_by')
            //     ->with('user'); 

            //   $cnt ='';
            // if ($request->filled('center')) {
            //     $query->where('centerId', $request->center);
            //     $ccn= Center::select('center_name')->find( $request->center ) ; 
            //     if($ccn){
            //         $cnt =$ccn->center_name; 
            //     }
            // }
            // $data = $query->get();
                  $logQuery = AppLog::select(
                        'created_by',
                        DB::raw("SUM(CASE WHEN stepId = '1' THEN 1 ELSE 0 END) as total_rec"),
                        DB::raw("SUM(CASE WHEN stepId = '4' THEN 1 ELSE 0 END) as total_ready"),
                        DB::raw("SUM(CASE WHEN stepId = '5' THEN 1 ELSE 0 END) as total_del"),
                        DB::raw("SUM(CASE WHEN stepId = '11' THEN 1 ELSE 0 END) as total_bio"),
                        DB::raw("0 as total_forms")
                    )
                    ->when($from && $to, fn($q) => $q->whereBetween('Date', [$from, $to]))
                    ->where('remarks', '!=', 'ForceBySystem')
                    ->whereNotNull('created_by')
                    ->whereIn('stepId', [1,4,5,11])
                    ->when($request->filled('center'), function($q) use ($request) {
                        $q->where('centerId', $request->center);
                    })
                    ->groupBy('created_by');

                $formQuery = DB::table('tbl_form_fill')
                    ->select(
                        'created_by',
                        DB::raw("0 as total_rec"),
                        DB::raw("0 as total_ready"),
                        DB::raw("0 as total_del"),
                        DB::raw("0 as total_bio"),
                        DB::raw("COUNT(*) as total_forms")
                    )
                    ->when($request->filled('center'), function($q) use ($request) {
                        $q->where('centerId', $request->center);
                    })
                    ->when($from && $to, fn($q) => $q->whereBetween('Date', [$from, $to]))
                    ->groupBy('created_by');

                $data = DB::query()
                    ->fromSub($logQuery->unionAll($formQuery), 'u')
                    ->select(
                        'created_by',
                        DB::raw('SUM(total_rec) as total_rec'),
                        DB::raw('SUM(total_ready) as total_ready'),
                        DB::raw('SUM(total_del) as total_del'),
                        DB::raw('SUM(total_bio) as total_bio'),
                        DB::raw('SUM(total_forms) as total_forms'),
                        DB::raw('(SUM(total_rec) + SUM(total_ready) + SUM(total_del) + SUM(total_bio) + SUM(total_forms)) as total_all')
                    )
                    ->groupBy('created_by')
                    ->get();

 
                $data->each(function ($item) {
                    $item->user = User::with('center')->find($item->created_by);
                });

             $net_rec     = $data->sum('total_rec');
            $net_ready   = $data->sum('total_ready');
            $net_del     = $data->sum('total_del');
            $net_bio     = $data->sum('total_bio');
            $net_forms   = $data->sum('total_forms');
            $net_total   = $data->sum('total_all');

            $cnt ='';
            if ($request->filled('center')) {
                 $ccn= Center::select('center_name')->find( $request->center ) ; 
                if($ccn){
                    $cnt =$ccn->center_name; 
                }
            }

           $pdf = Pdf::loadView('reports.pdf.activity_report',compact('data','from','to','cnt','net_rec','net_ready','net_del','net_total','net_forms','net_bio'))->setPaper('a4', 'landscape');

            // $pdf = Pdf::loadView('reports.pdf.activit_report', [
            //     'resultArray' => $data,
            //     'net_rec' => $net_rec,
            //      'net_ready' => $net_ready,
            //      'net_del' => $net_del,
            //       'net_total' => $net_total,
            //     // 'centerName' => $centerName
            // ])->setPaper('a4', 'landscape');

            return $pdf->download('activity_report.pdf');

        }
        else{

            return redirect()->route('Receive-Report.index')->with('error', 'Please select from & to Date');
        }


    }

    public function search(Request $request): View|RedirectResponse
    {
        // dd($request->all()) ; 

        if ($request->filled('from_date') && $request->filled('center')) {
           // dd($request->all()) ; 

            $from = $request->input('from_date');
            $cent = $request->input('center');
        // $res = AppReceive::where('Date','>=',$from)->paginate(25);  
            $data = AppReceive::select('stickerNo')
                ->where('Date', $from)
                ->where('centerId', $cent)
                ->get()
                ->map(function ($row) {
                    // Discard first 9 characters
                    $clean = substr($row->stickerNo, 9);

                    // Split into series (letters) and number (digits)
                    preg_match('/^([A-Za-z]+)([0-9]+)$/', $clean, $matches);

                    return [
                        'series' => $matches[1] ?? null,
                        'num'    => isset($matches[2]) ? (int)$matches[2] : null,
                    ];
                })
                ->filter(fn($item) => $item['series'] !== null && $item['num'] !== null);

            // Group by series
            $result = $data->groupBy('series')->map(function ($items, $series) {
                $nums = collect($items)->pluck('num')->sort()->values();
                $min  = $nums->first();
                $max  = $nums->last();

                $all = collect(range($min, $max));
                $missing = $all->diff($nums)->values()->toArray();

                return [
                    'series'  => $series,
                    'start'   => $min,
                    'end'     => $max,
                    'total'   => $nums->count(),    
                    'missing' => $missing,
                ];
            });

            $resultArray = $result->toArray();

            // return $resultArray ;
            return view('reports.missing_sticker.search_result', [
                'results' => $resultArray,
                'date' => $from,
                'center' =>  $cent,
                // 'query' => $res ?? null   // only if you need $res
            ]);

        }
        else{
             return redirect()->route('missing-sticker.index')->with('error', 'Please select Date & Center');
        }

      

    }
   
}
