<?php

namespace App\Exports;

use App\Models\AppReceive;
use App\Models\User;
use App\Models\Center;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\BeforeSheet;
use Maatwebsite\Excel\Events\AfterSheet;

class MissingStickerExport implements FromCollection, WithHeadings, WithEvents
{
    protected $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function collection()
    {
        $data = AppReceive::select('stickerNo')
            ->where('Date', $this->request->date)
            ->where('centerId', $this->request->center)
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
                'missing' => implode(', ', $missing), // formatted for Excel cell
            ];
        });

        return collect($result->values());
    }

    public function headings(): array
    {
        return ['Series', 'Start', 'End', 'Total', 'Missing'];
    }

     public function registerEvents(): array
        {
            return [
                AfterSheet::class => function (AfterSheet $event) {
                    $event->sheet->insertNewRowBefore(1, 2);
                    $event->sheet->mergeCells('A1:E1');
                    $event->sheet->setCellValue('A1', 'Sticker Missing Report');
                    $event->sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);

                    $event->sheet->setCellValue('A2', 'Date: ' . ($this->request->date ?? ''));
                    $event->sheet->setCellValue('C2', 'Center: ' . ($this->request->center ?? ''));
                },
            ];
        }

}