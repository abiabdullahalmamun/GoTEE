<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;

use App\Models\User;
use App\Models\AppReceive ; 
use App\Models\AppLog ;
use App\Models\AppDigitization ;  
use App\Models\Center;
use App\Models\Counter;
use App\Models\VisaType;
use App\Models\StickerMap;
use App\Models\DBquery;

use App\Services\Reports\TransactionReportService;
use App\Services\Reports\SearchReportService;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\View\View;

use Barryvdh\DomPDF\Facade\Pdf;
use App\Exports\DigitizationExport;

class DBqueryController extends Controller
{

    // public function __construct(protected TransactionReportService $TransactionReportService) {}
    /**
     * Display a listing of the resource.
     */

    public function index(Request $request): View
{
    try {

        $tables = DBquery::get();

        $schema = [];
        foreach ($tables as $table) {
            $schema[$table->id] = Schema::getColumnListing($table->table_name);
        }

        $data = collect();

        // RUN QUERY ONLY IF FORM SUBMITTED
        if ($request->has('tableQ')) {
            $table = DBquery::findOrFail($request->tableQ);
            $query = DB::table($table->table_name);

            // =========================
            // WHERE CONDITIONS (ARRAY)
            // =========================
            if ($request->columnQ) {

                foreach ($request->columnQ as $i => $column) {

                    $condition = $request->conditionQ[$i] ?? '=';
                    $value     = $request->valueQ[$i] ?? null;

                    // handle NULL conditions
                    if (in_array($condition, ['IS NULL', 'IS NOT NULL'])) {

                        if ($condition == 'IS NULL') {
                            $query->whereNull($column);
                        } else {
                            $query->whereNotNull($column);
                        }

                    } else {
                        $query->where($column, $condition, $value);
                    }
                }
            }

            // =========================
            // SORT
            // =========================
            if ($request->sortQ) {
                $query->orderBy(
                    $request->sortQ,
                    $request->orderQ ?? 'ASC'
                );
            }

            // =========================
            // LIMIT
            // =========================
            if ($request->limitQ) {
                $query->limit((int) $request->limitQ);
            } else {
                $query->limit(100);
            }

            $data = $query->get();
        }

        return view('reports.query_builder.index', [
            'data'   => $data,
            'tables' => $tables,
            'schema' => $schema,
        ]);

    } catch (\Exception $e) {

         return view('reports.query_builder.index', [
            'data'   => $data,
             'tables' => $tables,
            'schema' => $schema,
        ]);
    }
}
    // public function index(Request $request): View 
    // {
    //      dd($request->all());
    //     try {
            
    //          $role = auth()->user()->role_id ;    
    //          $query = DBquery::query();
    //         $paginated = $query->orderBy('id','asc')->paginate(10000);

    //         $tables  = DBquery::get() ; 
    //         $schema = [];

    //         foreach ($tables as $table) {
    //             $schema[$table->id] = Schema::getColumnListing($table->table_name);
    //         }

    //         return view('reports.query_builder.index', [
    //             'data'    => $paginated,
    //             'tables' => $tables,
    //             'schema' => $schema,
    //         ]);

    //     } catch (Exception $e) {
    //          $mess = $e->getMessage(); 
    //          // dd($mess) ; 
    //     }
    // }
    public function exportExcel(Request $request)
    {
         // dd($request->all()) ; 
        $role = auth()->user()->role_id ;   
        if ($role === 5 || $role === 1 || $role === 13) { 
             if ($request->filled('from_date') && $request->filled('to_date')) {
                return Excel::download(new DigitizationExport($request), 'digitization-report.xlsx');
            }
            else{
               return redirect()->route('digitization-report.index')->with('error', 'Please select Date ');
            }
        }
        else{
             return Excel::download(new DigitizationExport($request), 'digitization-report.xlsx');
        }

    }

    public function exportPDF(Request $request)
    {
        $cenId = $request->input('center') ?? auth()->user()->centerId;
        $centerName = $cenId ? Center::find($cenId)?->center_name : 'All';
        $displayDate = $request->input('date') ?? 'All Dates';


$combined = collect();

AppReceive::where('stepId', 4)
    ->when($cenId, fn($q) => $q->where('centerId', $cenId))
    ->chunk(500, function($rows) use (&$combined) {
        foreach ($rows as $index => $item) {
            $combined->push([
                'Serial'   => $combined->count() + 1,
                'passport' => $item->passport,
                'date'     => $item->Date,
                'Webfile'  => $item->Webfile,
                'Name'     => $item->ApplicantName,
                'Contact'  => $item->contact,
                'sticker'  => $item->sticker->sticker,
                'stickerNo'  => $item->stickerNo,
                'visa'  => $item->visa->visa_type,
                'source'   => 'AppReceive',
            ]);
        }
    });

UndelPass::when($cenId, fn($q) => $q->where('centerId', $cenId))
    ->chunk(500, function($rows) use (&$combined) {
        foreach ($rows as $index => $item) {
            $combined->push([
                'Serial'   => $combined->count() + 1,
                'passport' => $item->passport,
                'date'     => $item->Date,
                'Webfile'  => null,
                'Name'     => null,
                'Contact'  => null,
                'sticker'  => null,
                'stickerNo'  => null,
                'visa'  => null,
                'source'   => 'UndelPass',
            ]);
        }
    });
 
 
        // $combined = $app->concat($undel);

        // Generate PDF
        $pdf = Pdf::loadView('reports.pdf.undelivered_pass', [
            'records'    => $combined,
            'displayDate'=> $displayDate,
            'centerName' => $centerName,
        ])->setPaper('a4', 'landscape');

        return $pdf->download('undelivered_pass_report.pdf');
    }


    public function summary(Request $request): View|RedirectResponse
    {
        // dd($request->all()) ; 

        if ($request->filled('from_date') && $request->filled('to_date')) {
             // dd($request->all()) ; 
            $from = $request->input('from_date');
            $to = $request->input('to_date');
 

          $records = AppDigitization::whereBetween('sent2hci_date', [$from, $to])
                    ->get();

        // Unique sent2hci_date
        $uniqueDates = $records->pluck('sent2hci_date')->unique()->values();
 
        // $datesWithCount = $uniqueDates->map(function ($date) {
        //     $count = AppReceive::whereDate('Date', $date)->count();

        //     return [
        //         'sent2hci_date' => $date,
        //         'totalSend' => $count,
        //     ];
        // });
$datesWithCount = $uniqueDates->map(function ($date) {
    // Count total AppReceive for this date
    $totalSend = AppReceive::whereDate('Date', $date)->count();

    // Get webrefs for this date
    $webrefsForDate = AppReceive::whereDate('Date', $date)->pluck('id')->values();

    // Count AppLog for stepId = 8 and web_ref in this webrefs
    $step8Count = AppLog::where('stepId', 8)
        ->whereIn('web_ref', $webrefsForDate)
        ->count();

    // Count AppLog for stepId = 9 and web_ref in this webrefs
    $step9Count = AppLog::where('stepId', 9)
        ->whereIn('web_ref', $webrefsForDate)
        ->count();

    return [
        'sent2hci_date' => $date,
        'totalSend' => $totalSend,
        'step8Count' => $step8Count,
        'step9Count' => $step9Count,
    ];
});
  

  $grandTotal = [
    'totalSend' => $datesWithCount->sum('totalSend'),
    'step8Count' => $datesWithCount->sum('step8Count'),
    'step9Count' => $datesWithCount->sum('step9Count'),
];
// dd($datesWithCount) ;
                // dd($dateVal) ; 

  
         

            $cnt ='All';
            $cn = Center::select('center_name')->find( $request->center ) ; 
            if($cn){
                $cnt =$cn->center_name; 
            }
            // dd($data) ; 
            return view('reports.digitization_report.digitization', [
                        'from_date' => $from,
                        'to_date' => $to,
                        'center' => $request->input('center'),
                        'results' => $datesWithCount,
                        'report' => 'Digitization summary report',
                        'grandTotal' => $grandTotal,
                        'center' => $cnt
                           
                    ]);
        }
        else{
              return redirect()->route('Receive-Report.index')->with('error', 'Please select from & to Date');
        }
    }

    // public function getColumns($id)
    // {
    //     $table = QueryTable::findOrFail($id);

    //     $columns = Schema::getColumnListing($table->table_name);

    //     return response()->json($columns);
    // }
    public function search(Request $request): View|RedirectResponse
    {
        // dd($request->all()) ; 

        if ($request->filled('center')) {
            $cent = $request->input('center');
        // $res = AppReceive::where('Date','>=',$from)->paginate(25);  
            // $data = AppReceive::select('passport','id')->where('centerId', $cent)->where('stepId', 4)->get() ; 
            $res = UndelPass::where('centerId', $cent)->paginate(25);  
         
            // Group by series
            // $result = $data->groupBy('series')->map(function ($items, $series) {
            //     $nums = collect($items)->pluck('num')->sort()->values();
            //     $min  = $nums->first();
            //     $max  = $nums->last();

            //     $all = collect(range($min, $max));
            //     $missing = $all->diff($nums)->values()->toArray();

            //     return [
            //         'series'  => $series,
            //         'start'   => $min,
            //         'end'     => $max,
            //         'total'   => $nums->count(),    
            //         'missing' => $missing,
            //     ];
            // });

            // $resultArray = $result->toArray();

            // return $resultArray ;
            // return view('reports.undelivered_pass.search_result', [
            //     'results' => $resultArray,
            //     'date' => $from,
            //     'center' =>  $cent,
            //     // 'query' => $res ?? null   // only if you need $res
            // ]);
           return view('reports.digization_report.search_result', [
                // 'shop' => $shop, 
                // 'customer' =>  $input2, 
                //  'audit' =>  '', 
                'query' => $res
                 
            ]  );

        }
        else{
             return redirect()->route('digization-report.index')->with('error', 'Please select Date & Center');
        }

      

    }
   
}
