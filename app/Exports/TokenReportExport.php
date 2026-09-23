<?php

namespace App\Exports;

use App\Models\TokenLog;
use App\Models\User;
use App\Models\Center;
use App\Models\Service;
use App\Models\StickerMap;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\BeforeSheet;
use Maatwebsite\Excel\Events\AfterSheet;

class TokenReportExport implements FromCollection, WithHeadings, WithEvents
{
    protected $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function collection()
    {
        $query = TokenLog::query();

        if ($this->request->filled('from_date') && $this->request->filled('to_date')) {
            $query->whereBetween('Date', [
                $this->request->from_date,
                $this->request->to_date
            ]);
        }

         $cenId = auth()->user()->centerId ; 
         $role = auth()->user()->role_id ;    
         // dd($role) ; 
         if($role== 5 ||  $role== 1 || $role== 13 ){
            $centers = Center::select('center_name','id')->where('status',1)->orderBy('center_name','asc')->get();
             if ($this->request->filled('center')) {
                $query->where('centerId', $this->request->center);
             }
             $cenId =''; 
         }
         else{
             $centers = Center::select('center_name','id')->where('status',1)->where('id',$cenId)->orderBy('center_name','asc')->get();
              $query->where('centerId', $cenId);
         }

         if ($this->request->filled('user')) {
            $query->where('servedby', $this->request->user);
        }
        if ($this->request->filled('service')) {
            $query->where('token_svc_no', $this->request->service);
        }
 

        // if ($this->request->filled('center')) {
        //     $query->where('centerId', $this->request->center);
        // }

        // if ($this->request->filled('user')) {
        //     $query->where('created_by', $this->request->user);
        // }
            $statsQuery = clone $query;

            // Get aggregate values
            $stats = $statsQuery->selectRaw('
                MAX(waiting) as max_waiting,
                MIN(waiting) as min_waiting,
                AVG(waiting) as avg_waiting,
                MAX(service) as max_service,
                MIN(service) as min_service,
                AVG(service) as avg_service,
                MAX(scan) as max_scan,
                MIN(scan) as min_scan,
                AVG(scan) as avg_scan
            ')->first();


             $data = $query->with(['center','user','serviceName'])
                  ->orderBy('id', 'desc')
                  ->paginate(5000)
                  ->appends($this->request->all());

             // dd($data) ;    
              $users = User::select('id', 'name', 'email')
                    ->where('centerId', $cenId)
                    ->whereHas('appReceive')   
                    ->orderBy('name', 'asc')
                    ->get();  

            $svcType = Service::select('service_name','id' )->where('status',1)->get() ; 
           
        return $query->with(['center', 'user'])->get()->map(function ($row, $index) {
            return [
                 $index + 1,
                $row->center->center_name ?? '',
                $row->Date,
                $row->tokenno,
                 $row->serviceName->service_name ?? 'N/A' ,
                mb_substr($row->tissuetime, 0, 19) ?? '',
                mb_substr($row->ststart, 0, 19) ?? '',
                mb_substr($row->scantime, 0, 19) ?? '',
                mb_substr($row->ststop, 0, 19) ?? '' ,
               $row->waiting !== null ? gmdate('H:i:s', $row->waiting) : '', 
                $row->service !== null ? gmdate('H:i:s', $row->service) : '',
                $row->scan !== null ? gmdate('H:i:s', $row->scan) : '',
               $row->cno ?? '',
                 $row->user->name ?? '' ,
             
            ];
        });
    }

    public function headings(): array
    {
        return [
           'SL', 'Center', 'Date', 'Token', 'Service', 'TokenIssueTime','CounterCalTime','ScanTime', 'ServiceStopTime','Wait', 'Service','A-Service', 'Counter', 'Executive'
        ];
    }

  public function registerEvents(): array
{
    return [
        AfterSheet::class => function (AfterSheet $event) {

            // STEP 1: Insert extra rows for title & filters
            $event->sheet->insertNewRowBefore(1, 4); // insert 4 rows above existing headings

            // STEP 2: Prepare variables
            $usr = $this->request->filled('user')
                ? optional(User::find($this->request->user))->name ?? 'All'
                : 'All';

            $cnt = $this->request->filled('center')
                ? optional(Center::find($this->request->center))->center_name ?? 'All'
                : 'All';
            $vst = $this->request->filled('visa')
                ? optional(VisaType::find($this->request->visa))->visa_type ?? 'All'
                : 'All';
            $stc = $this->request->filled('sticker')
                ? optional(StickerMap::find($this->request->sticker))->sticker ?? 'All'
                : 'All';

            // STEP 3: Add title row (merge across all columns)
            $lastColumn = $event->sheet->getHighestColumn(); // automatically find last column
            $event->sheet->mergeCells("A1:{$lastColumn}1");
            $event->sheet->setCellValue('A1', 'Token Report');
            $event->sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);

            // STEP 4: Add filter rows
            $event->sheet->mergeCells("A2:{$lastColumn}2");
            $event->sheet->setCellValue('A2', 'From: ' . ($this->request->from_date ?? '') . '   To: ' . ($this->request->to_date ?? ''));

            $event->sheet->mergeCells("A3:{$lastColumn}3");
            $event->sheet->setCellValue('A3', 'Center: ' . $cnt . '    User: ' . $usr) ;

            // STEP 5: Style the heading row (now at row 5 after inserting 4 rows)
            $headingRow = 5;
            $event->sheet->getStyle("A{$headingRow}:{$lastColumn}{$headingRow}")->getFont()->setBold(true);
        },
    ];
}


}