<?php

namespace App\Exports;

use App\Models\AppReceive;
use App\Models\User;
use App\Models\Center;
use App\Models\SmsLog ; 
use Illuminate\Http\Request;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\BeforeSheet;
use Maatwebsite\Excel\Events\AfterSheet;

class SmsPrintExport implements FromCollection, WithHeadings, WithEvents
{
    protected $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function collection()
    {
        $query = SmsLog::query();

        if ($this->request->filled('from_date') && $this->request->filled('to_date')) {
            $query->whereBetween('Date', [
                $this->request->from_date,
                $this->request->to_date
            ]);
        }

        if ($this->request->filled('center')) {
            $query->where('centerId', $this->request->center);
        }

        if ($this->request->filled('type')) {
            $query->where('type', $this->request->user);
        }


        return $query->with(['center', 'web'])->get()->map(function ($row) {
             $typeLabels = [
                0 => 'OTP',
                1 => 'Receive',
                2 => 'Sent2HCI',
                3 => 'RecFrmHCI',
                4 => 'ReadyCenter',
                5 => 'Delivery',
            ];
             $typeLabel = $typeLabels[$row->type] ?? 'Unknown';
            return [
                $row->center->center_name ?? '',
                $row->Date,
                $row->web->Webfile ?? '' ,
                $typeLabel,
                $row->contact ?? '' ,
                $row->text ?? '',
                $row->created_at,
                $row->txn,
                $row->Delivery,
                $row->Delivery_time,
            ];
        });
    }

    public function headings(): array
    {
        return [
           'Center', 'Date', 'Webfile', 'Type','Contact', 'Text', 'CreatedAt', 'TXN', 'Status','DeliveryTime'
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
                if ($this->request->filled('type')) {
                    if($this->request->user==0){
                        $usr = 'OTP' ; 
                    }
                    else if($this->request->user==1){
                        $usr = 'Receive' ; 
                    }
                    else if($this->request->user==2){
                        $usr = 'Sent2HCI' ; 
                    }
                    
                }

                $cnt = 'All';
                if ($this->request->filled('center')) {
                    $ccn = Center::select('center_name')->find($this->request->center);
                    $cnt = $ccn ? $ccn->center_name : '';
                } 


                // STEP 3: Add title row
                $event->sheet->mergeCells('A1:G1');
                $event->sheet->setCellValue('A1', 'SMS Details Report');
                $event->sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);

                // STEP 4: Add filter rows
                $event->sheet->setCellValue('A2', 'From: ' . ($this->request->from_date ?? '') . '   To: ' . ($this->request->to_date ?? ''));
                $event->sheet->setCellValue('A3', 'Center: ' . $cnt . '    Type: ' . $usr);
            },
        ];
    }

}