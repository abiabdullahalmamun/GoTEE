<?php
namespace App\Exports;

use App\Models\AppReceive;
use App\Models\UndelPass;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\BeforeSheet;
use Maatwebsite\Excel\Events\AfterSheet;

class UndeliveredPassExport implements FromCollection, WithHeadings, WithEvents
{
    protected $request;
    protected $cenId;

    public function __construct(Request $request)
    {
          $this->request = $request;
          $this->cenId = $request->center ?? auth()->user()->centerId;
    }

    public function collection()
    {
           $query = AppReceive::with(['visa', 'sticker', 'center']);

            if ($this->request->filled('from_date') && $this->request->filled('to_date')) {
                if($this->request->from_date !=  $this->request->to_date )
                {
                    $query->whereBetween('Date', [
                        $this->request->from_date,
                        $this->request->to_date
                    ]);
                }
            }
            else{
               $query->where('Date',date('Y-m-d'));
            }

            if ($this->request->filled('center')) {
                $query->where('centerId', $this->request->center);
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

        // 1️⃣ AppReceive
        // $app = AppReceive::where('stepId', 4)
        //     ->when($this->cenId, fn($q) => $q->where('centerId', $this->cenId))
        //     ->get()
        //     ->values() // reset keys for correct serial
        //     ->map(function ($item, $index) {
        //         return [
        //             'Serial'   => $index + 1,
        //             'passport' => $item->passport,
        //             'date'     => $item->Date,
        //             'Webfile'  => $item->Webfile,
        //             'Name'     => $item->ApplicantName,
        //             'Contact'  => $item->contact,
        //              'sticker'  => $item->sticker->sticker,
        //                'stickerNo'  => $item->stickerNo,
        //               'visa'  => $item->visa->visa_type,
        //             'source'   => 'AppReceive',
        //         ];
        //     });

        // 2️⃣ UndelPass
        $startIndex = $app->count(); // continue serial numbers
        // $undel = UndelPass::when($this->cenId, fn($q) => $q->where('centerId', $this->cenId))
        //     ->get()
        //     ->values()
        //     ->map(function ($item, $index) use ($startIndex) {
        //         return [
        //             'Serial'   => $startIndex + $index + 1,
        //             'passport' => $item->passport,
        //             'date'     => $item->Date,
        //             'Webfile'  => null,
        //             'Name'     => null,
        //             'Contact'  => null,
        //             'sticker' =>null,
        //             'stickerNo' =>null,
        //              'visa' =>null,
        //             'source'   => 'UndelPass',
        //         ];
        //     });
        $undel = [] ; 
        // 3️⃣ Combine
        return $app->concat($undel);
    }

    public function headings(): array
    {
        return ['Sl', 'Date', 'center', 'Passport', 'Webfile', 'Name', 'Contact','visatype','stickerType', 'stickerNo','Source'];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                // Get center name
                $centerName = null;
                if ($this->request->center) {
                    $center = \App\Models\Center::find($this->request->center);
                    $centerName = $center?->center_name;
                }

                // Insert header rows
                $event->sheet->insertNewRowBefore(1, 2);
                $event->sheet->mergeCells('A1:G1'); // adjust columns if needed
                $event->sheet->setCellValue('A1', 'Undelivered Passport');
                $event->sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);

                // Show center name and date
                $event->sheet->setCellValue('A2', 'Center: ' . ($centerName ?? 'All'));
            },
        ];
    }


}