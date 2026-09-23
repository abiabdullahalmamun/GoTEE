<?php

namespace App\Exports;

use App\Models\AppReceive;
use App\Models\User;
use App\Models\Center;
use App\Models\AptOverride ;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\BeforeSheet;
use Maatwebsite\Excel\Events\AfterSheet;

class OverridePrintExport implements FromCollection, WithHeadings, WithEvents
{
    protected $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function collection()
    {
        $query = AptOverride::query();

        if ($this->request->filled('from_date') && $this->request->filled('to_date')) {
            $query->whereBetween('Date', [
                $this->request->from_date,
                $this->request->to_date
            ]);
        }

        if ($this->request->filled('center')) {
            $query->where('centerId', $this->request->center);
        }

        if ($this->request->filled('user')) {
            $query->where('created_by', $this->request->user);
        }

       if ($this->request->filled('type')) {
            $query->where('active', $this->request->type);
        }
        $svcMap = [
            1 => 'Foreign Passport',
            2 => 'WAIVE',
            3 => 'Others',
        ];       
        return $query->with(['center', 'user'])->get()->map(function ($row) use ($svcMap) {
            return [
                $row->center->center_name ?? '',
                $row->Date,
                $row->WebFile_no ?? '' ,
                $row->visa->visa_type ?? '' ,
                $row->remarks ?? '' ,
                $row->user->email ?? '',
                $row->created_at,
                 $svcMap[$row->svcId] ?? '',
                 $row->userApp->name ?? '',
                  $row->approvedAt ?? '',

            ];
        });
    }

    public function headings(): array
    {
        return [
           'Center', 'Date', 'Webfile', 'VisaType','Remarks', 'CreatedBy', 'CreatedAt', 'Type','ApprovedBy', 'approvedAt'
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {

                // STEP 1: Insert extra rows for report title & filters
                $event->sheet->insertNewRowBefore(1, 4); // 4 rows before headings

                // STEP 2: Prepare variables
                $usr = 'All';
                if ($this->request->filled('user')) {
                    $us = User::select('name')->find($this->request->user);
                    $usr = $us ? $us->name : '';
                }

                $cnt = 'All';
                if ($this->request->filled('center')) {
                    $ccn = Center::select('center_name')->find($this->request->center);
                    $cnt = $ccn ? $ccn->center_name : '';
                } 


                // STEP 3: Add title row
                $event->sheet->mergeCells('A1:G1');
                $event->sheet->setCellValue('A1', 'Appointment Override Report');
                $event->sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);

                // STEP 4: Add filter rows
                $event->sheet->setCellValue('A2', 'From: ' . ($this->request->from_date ?? '') . '   To: ' . ($this->request->to_date ?? ''));
                $event->sheet->setCellValue('A3', 'Center: ' . $cnt . '    User: ' . $usr);
            },
        ];
    }

}