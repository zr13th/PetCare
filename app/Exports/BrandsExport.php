<?php

namespace App\Exports;

use App\Models\Brand;
use Maatwebsite\Excel\Concerns\{
    FromCollection,
    WithHeadings,
    WithMapping,
    WithCustomStartCell,
    ShouldAutoSize,
    WithStyles,
    WithEvents
};
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Events\AfterSheet;

class BrandsExport implements FromCollection, WithHeadings, WithMapping, WithCustomStartCell, ShouldAutoSize, WithStyles, WithEvents
{
    public function collection()
    {
        return Brand::select('id', 'name', 'slug', 'description', 'logo', 'created_at')
            ->orderBy('id')
            ->get();
    }

    /** ✅ Tiêu đề trùng tên trường trong DB */
    public function headings(): array
    {
        return [
            'id',
            'name',
            'slug',
            'description',
            'logo',
            'created_at',
        ];
    }

    public function map($row): array
    {
        return [
            $row->id,
            $row->name,
            $row->slug,
            $row->description,
            $row->logo,
            optional($row->created_at)->format('Y-m-d H:i:s'),
        ];
    }

    public function startCell(): string
    {
        return 'A1';
    }

    public function styles(Worksheet $sheet)
    {
        // Đậm hàng tiêu đề, căn giữa
        $sheet->getStyle('A1:F1')->getFont()->setBold(true);
        $sheet->getStyle('A1:F1')->getAlignment()->setHorizontal('center');
        return [];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();

                // Căn giữa dọc
                $sheet->getStyle('A:F')->getAlignment()->setVertical('center');

                // Viền toàn bảng
                $sheet->getStyle('A1:F' . $sheet->getHighestRow())
                    ->getBorders()->getAllBorders()->setBorderStyle('thin');

                // Font mặc định
                $sheet->getStyle('A:F')->getFont()->setName('Calibri')->setSize(12);

                // Freeze hàng đầu
                $sheet->freezePane('A2');
            },
        ];
    }
}
