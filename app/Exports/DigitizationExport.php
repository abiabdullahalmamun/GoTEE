<?php

namespace App\Exports;

use App\Models\AppReceive;
use App\Models\User;
use App\Models\Center;
use App\Models\VisaType;
use App\Models\StickerMap;
use App\Models\AppDigitization ;  
use Illuminate\Http\Request;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\BeforeSheet;
use Maatwebsite\Excel\Events\AfterSheet;

class DigitizationExport implements FromCollection, WithHeadings, WithEvents
{
    protected $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function collection()
    {
        $query = AppDigitization::with(['webReference.center','region']);

        if ($this->request->filled('from_date') && $this->request->filled('to_date')) {
            $query->whereBetween('sent2hci_date', [
                $this->request->from_date,
                $this->request->to_date
            ]);
        }

        // if ($this->request->filled('center')) {
        //     $query->where('centerId', $this->request->center);
        // }

if ($this->request->filled('center')) {
    $query->whereHas('webReference', function ($q) {
        $q->where('centerId', $this->request->center);
    });
}
 

        return $query->with(['webReference','region'])->get()->values()->map(function ($row, $index) {
  
            return [
                 $index + 1,
                $row->webReference?->center?->center_name ?? '',
                $row->sent2hci_date ?? '',
                $row->webfile ?? '',
                $row->return_date ,
                $row->dvd_date,
                
            ];
        });
    }

    public function headings(): array
    {
        return [
           'SL', 'Center', 'Deposite Date', 'Webfile', 'Received From HCI', 'Date of Digitization'
        ];
    }

  public function registerEvents(): array
{
    return [
        AfterSheet::class => function (AfterSheet $event) {

            // STEP 1: Insert extra rows for title & filters
            $event->sheet->insertNewRowBefore(1, 4); // insert 4 rows above existing headings

            // STEP 2: Prepare variables
            // $usr = $this->request->filled('user')
            //     ? optional(User::find($this->request->user))->name ?? 'All'
            //     : 'All';

            $cnt = $this->request->filled('center')
                ? optional(Center::find($this->request->center))->center_name ?? 'All'
                : 'All';
            // $vst = $this->request->filled('visa')
            //     ? optional(VisaType::find($this->request->visa))->visa_type ?? 'All'
            //     : 'All';
            // $stc = $this->request->filled('sticker')
            //     ? optional(StickerMap::find($this->request->sticker))->sticker ?? 'All'
            //     : 'All';

            // STEP 3: Add title row (merge across all columns)
            $lastColumn = $event->sheet->getHighestColumn(); // automatically find last column
            $event->sheet->mergeCells("A1:{$lastColumn}1");
            $event->sheet->setCellValue('A1', 'Foreign Passport Received Report');
            $event->sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);

            // STEP 4: Add filter rows
            $event->sheet->mergeCells("A2:{$lastColumn}2");
            $event->sheet->setCellValue('A2', 'From: ' . ($this->request->from_date ?? '') . '   To: ' . ($this->request->to_date ?? ''));

            $event->sheet->mergeCells("A3:{$lastColumn}3");
            $event->sheet->setCellValue('A3', 'Center: ' . $cnt  );

            // STEP 5: Style the heading row (now at row 5 after inserting 4 rows)
            $headingRow = 5;
            $event->sheet->getStyle("A{$headingRow}:{$lastColumn}{$headingRow}")->getFont()->setBold(true);
        },
    ];
}


}