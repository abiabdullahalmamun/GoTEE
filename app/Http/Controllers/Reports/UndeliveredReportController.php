<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;

use App\Models\User;
use App\Models\AppReceive ; 
use App\Models\Center;
use App\Models\Counter;
use App\Models\VisaType;
use App\Models\StickerMap;
use App\Models\UndelPass;

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
use App\Exports\UndeliveredPassExport;

class UndeliveredReportController extends Controller
{

    // public function __construct(protected TransactionReportService $TransactionReportService) {}
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View 
    {
         // dd($request->all());
        try {
            
             $role = auth()->user()->role_id ;    
             // dd($role) ; 
            if ($role === 5 || $role === 1 || $role === 13 ) {
                $centers = Center::select('center_name','id')
                ->where('status',1)
                ->orderBy('center_name','asc')
                ->get();
                $cenId = $request->input('center'); 
            } else {
                    $cenId = auth()->user()->centerId; // set first
                    $centers = Center::select('center_name','id')
                        ->where('status',1)
                        ->where('id', $cenId)
                        ->orderBy('center_name','asc')
                        ->get();
            }

            $combined=[] ; 
            // dd($request->days) ; 
            $type='';
            if($request->days === '0' || $request->days === null){
                // dd('ok') ; 
                $query = AppReceive::with(['visa', 'sticker', 'center']);
                if ($request->filled('from_date') && $request->filled('to_date')) {
                    if($request->from_date !=  $request->to_date )
                    {
                        $query->whereBetween('Date', [
                            $request->from_date,
                            $request->to_date
                        ]);
                    }
                }
                else{
                   $query->where('Date',date('Y-m-d'));
                }

                if ($request->filled('center')) {
                    $query->where('centerId', $request->center);
                }

                $app = $query->where('stepId', 4)
                    ->get()
                    ->map(function ($item) {
                        return [
                            'id'        => $item->id,
                            'date'      => $item->Date,
                            'center'    =>  optional($item->center)->center_name,
                            'passport'  => $item->passport,
                            'Webfile'   => $item->Webfile,
                            'Name'      => $item->ApplicantName,
                            'Contact'   => $item->contact,
                            'visa'      => optional($item->visa)->visa_type,
                            'sticker'   => optional($item->sticker)->sticker,
                            'stickerNo' => $item->stickerNo,
                            'source'    => 'AppReceive',
                        ];
                    });

                // dd($app);

                $undel= [] ; 
                $undel = UndelPass::where('centerId',$cenId)->get()->map(function($item) {
                    return [
                        'id'       => $item->id,
                        'date'     => $item->Date,
                        'passport' => $item->passport,
                        'Webfile' => null,
                        'Name' => null,
                        'Contact' =>null,
                        'visa' =>null,
                        'sticker' =>null,
                        'stickerNo' =>null,
                        'source'   => 'UndelPass'
                    ];
                });
                $combined = $app->concat($undel);
                $type = 1 ;  
            }   
            else{
               $readyAlert = DB::table('tbl_application_log')
                ->select(
                    'web_ref',
                    DB::raw('MAX(Date) as last_date'),
                    DB::raw('DATEDIFF(CURDATE(), MAX(Date)) as days_diff')
                )
                ->groupBy('web_ref')
                ->havingRaw('SUM(stepId = 4) > 0')
                ->havingRaw('SUM(stepId = 5) = 0')
                ->havingRaw('DATEDIFF(CURDATE(), MAX(Date)) IN (7,30,60,90,120,150,180)')
                ->orderBy('web_ref')
                ->get();
                // dd($readyAlert) ; 


                $webRefs = $readyAlert->pluck('web_ref');

                $extraData = AppReceive::with(['visa', 'sticker', 'center'])
                        ->whereIn('id', $webRefs)
                        ->get()
                        ->keyBy('id');

                $readyAlert->transform(function ($item) use ($extraData) {

                    $extra = $extraData[$item->web_ref] ?? null;
                    $item->Date = $extra->Date ?? null;
                    $item->Webfile = $extra->Webfile ?? null;
                    $item->passport = $extra->passport ?? null;
                    $item->ApplicantName = $extra->ApplicantName ?? null;
                    $item->contact = $extra->contact ?? null;
                    $item->centerId =  optional($extra->center)->center_name;
                    return $item;
                });
                
                $combined = $readyAlert;
                $type = 2 ;  
            }
    
    
            $page    = request()->get('page', 1);
            $perPage = 10000;

            $paginated = new \Illuminate\Pagination\LengthAwarePaginator(
                $combined->forPage($page, $perPage)->values(), // reset keys
                $combined->count(),  // total number of items
                $perPage,
                $page,
                [
                    'path'  => request()->url(),
                    'query' => request()->query(),
                ]
            );

            $total = $combined->count();
            // dd($paginated) ; 
            return view('reports.undelivered_pass.index', [
                'data'    => $paginated,
                'centers' => $centers,
                'cenId'   => $cenId,
                'role'    => $role,
                'total'   => $total,
                'type'   => $type,
            ]);

           
        } catch (Exception $e) {
             $mess = $e->getMessage(); 
             // dd($mess) ; 
        }
    }
    public function exportExcel(Request $request)
    {
         // dd($request->all()) ; 
        $role = auth()->user()->role_id ;   
        if ($role === 5 || $role === 1) { 
            if ($request->filled('from_date') && $request->filled('to_date')) {
                return Excel::download(new UndeliveredPassExport($request), 'undelivered-pass.xlsx');
            }
            else{
               return redirect()->route('undelivered-pass.index')->with('error', 'Please select from date & to date ');
            }
        }
        else{
             return Excel::download(new UndeliveredPassExport($request), 'undelivered-pass.xlsx');
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
           return view('reports.undelivered_pass.search_result', [
                // 'shop' => $shop, 
                // 'customer' =>  $input2, 
                //  'audit' =>  '', 
                'query' => $res
                 
            ]  );

        }
        else{
             return redirect()->route('undelivered-pass.index')->with('error', 'Please select Date & Center');
        }

      

    }
   
}
