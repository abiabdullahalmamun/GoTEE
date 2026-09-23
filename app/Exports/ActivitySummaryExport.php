<?php

namespace App\Exports;

use App\Models\AppReceive;
use App\Models\User;
use App\Models\Center;
use App\Models\AppLog ; 
use App\Models\VisaType;
use App\Models\StickerMap;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\BeforeSheet;
use Maatwebsite\Excel\Events\AfterSheet;
use Illuminate\Support\Facades\DB;

class ActivitySummaryExport implements FromCollection, WithHeadings, WithEvents
{
    protected $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

public function collection()
{
    $data = collect(); // default empty collection

    if ($this->request->filled('from_date') && $this->request->filled('to_date')) {

        $from = $this->request->from_date;
        $to = $this->request->to_date;

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
            ->when($this->request->filled('center'), fn($q) => $q->where('centerId', $this->request->center))
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
            ->when($this->request->filled('center'), fn($q) => $q->where('centerId', $this->request->center))
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
    }

    return $data->values()->map(function ($row, $index) {
        return [
            $index + 1,
            $row->user->center->center_name ?? '',
            $row->user->name ?? '',
            $row->user->FullName ?? '',
            $row->total_forms ?? '',
            $row->total_rec ?? '',
            $row->total_ready ?? '',
            $row->total_del ?? '',
            $row->total_bio ?? '',
            $row->total_all ?? '',
        ];
    });
}

    public function headings(): array
    {
        return [
           'SL', 'Center', 'UserID','User Name','FormFill' ,'Receive', 'ReadyCenter', 'Delivery','Biometric', 'Total'
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
         
            // STEP 3: Add title row (merge across all columns)
            $lastColumn = $event->sheet->getHighestColumn(); // automatically find last column
            $event->sheet->mergeCells("A1:{$lastColumn}1");
            $event->sheet->setCellValue('A1', 'Activity Summary Report');
            $event->sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);

            // STEP 4: Add filter rows
            $event->sheet->mergeCells("A2:{$lastColumn}2");
            $event->sheet->setCellValue('A2', 'From: ' . ($this->request->from_date ?? '') . '   To: ' . ($this->request->to_date ?? ''));

            $event->sheet->mergeCells("A3:{$lastColumn}3");
            $event->sheet->setCellValue('A3', 'Center: ' . $cnt . '    User: ' . $usr );

            // STEP 5: Style the heading row (now at row 5 after inserting 4 rows)
            $headingRow = 5;
            $event->sheet->getStyle("A{$headingRow}:{$lastColumn}{$headingRow}")->getFont()->setBold(true);
        },
    ];
}


}