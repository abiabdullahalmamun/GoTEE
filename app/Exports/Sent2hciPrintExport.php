<?php

namespace App\Exports;

use App\Models\AppReceive;
use App\Models\User;
use App\Models\Center;
use App\Models\AppLog ; 
use Illuminate\Http\Request;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\BeforeSheet;
use Maatwebsite\Excel\Events\AfterSheet;

class Sent2hciPrintExport implements FromCollection, WithHeadings, WithEvents
{
    protected $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function collection()
    {
        $query = AppLog::query();

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


        return $query->with(['center', 'webref','user'])->where('stepId',2)->get()->map(function ($row) {
             return [
                $row->center->center_name ?? '',
                $row->Date,
                $row->webref->Webfile ?? '' ,
                $row->webref->ApplicantName ?? '' ,
                $row->webref->passport ?? '' ,
                $row->webref->contact ?? '' ,  
                $row->webref->stickerNo ?? '' ,  
                $row->user->name ?? '' ,
                $row->created_at ?? '' ,
               
            ];
        });
    }

    public function headings(): array
    {
        return [
           'Center', 'Date', 'Webfile', 'Name','passport', 'Contact','StickerNo', 'CreatedBy', 'CreatedAt'
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
                   $us = User::find($this->request->user) ; 
                   if($us){
                     $usr = $us->name ; 
                   }
                    
                }

                $cnt = 'All';
                if ($this->request->filled('center')) {
                    $ccn = Center::select('center_name')->find($this->request->center);
                    $cnt = $ccn ? $ccn->center_name : '';
                } 


                // STEP 3: Add title row
                $event->sheet->mergeCells('A1:G1');
                $event->sheet->setCellValue('A1', 'Sent To HCI Report');
                $event->sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);

                // STEP 4: Add filter rows
                $event->sheet->setCellValue('A2', 'From: ' . ($this->request->from_date ?? '') . '   To: ' . ($this->request->to_date ?? ''));
                $event->sheet->setCellValue('A3', 'Center: ' . $cnt . '    User: ' . $usr);
            },
        ];
    }

}