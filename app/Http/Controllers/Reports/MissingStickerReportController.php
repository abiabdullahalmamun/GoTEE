<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;

use App\Models\User;
use App\Models\AppReceive ; 
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
use App\Exports\MissingStickerExport;

class MissingStickerReportController extends Controller
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

      
             $data = [] ; 
            return view('reports.missing_sticker.index', compact('data','centers','cenId','role'));
           
        } catch (Exception $e) {
             $mess = $e->getMessage(); 
             // dd($mess) ; 
        }
    }
    public function exportExcel(Request $request)
    {
         // dd($request->all()) ; 
        if ($request->filled('date') && $request->filled('center')) {
            return Excel::download(new MissingStickerExport($request), 'sticker_missing.xlsx');
        }
        else{
           return redirect()->route('missing-sticker.index')->with('error', 'Please select Date & Center');
        }

    }

    public function exportPDF(Request $request)
    {
        // dd($request->all()) ; 
        if ($request->filled('date') && $request->filled('center')) {
            
            $from = $request->input('date');
            $cent = $request->input('center');

            $centerName = $cent ? Center::find($cent)?->center_name : 'All';

            // Format the date nicely for display
            $displayDate = $from ? Carbon::parse($from)->format('d-M-Y') : 'All Dates';

            // Generate results
            $data = AppReceive::select('stickerNo')
                ->where('Date', $from)
                ->where('centerId', $cent)
                ->get()
                ->map(function ($row) {
                    $clean = substr($row->stickerNo, 9);
                    preg_match('/^([A-Za-z]+)([0-9]+)$/', $clean, $matches);

                    return [
                        'series' => $matches[1] ?? null,
                        'num'    => isset($matches[2]) ? (int)$matches[2] : null,
                    ];
                })
                ->filter(fn($item) => $item['series'] !== null && $item['num'] !== null);

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
                    'missing' => implode(', ', $missing), // nicely formatted for PDF
                ];
            });

            $resultArray = $result->toArray();
            $pdf = Pdf::loadView('reports.pdf.missing_sticker', [
                'resultArray' => $resultArray,
                'displayDate' => $displayDate,
                'centerName' => $centerName
            ])->setPaper('a4', 'landscape');


            // $pdf = Pdf::loadView('reports.pdf.missing_sticker',compact('resultArray','displayDate','centerName'))->setPaper('a4', 'landscape');
            return $pdf->download('missing_sticker_report.pdf');

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
                    // preg_match('/^([A-Za-z]+)([0-9]+)$/', $clean, $matches);
                    preg_match('/^([A-Za-z\-]+)([0-9]+)$/', $clean, $matches);
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
