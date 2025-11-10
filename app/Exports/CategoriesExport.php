<?php

namespace App\Exports;

use App\Models\Category;
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

class CategoriesExport implements FromCollection, WithHeadings, WithMapping, WithCustomStartCell, ShouldAutoSize, WithStyles, WithEvents
{
    public function collection()
    {
        return Category::with('parent')
            ->select('id', 'name', 'slug', 'parent_id', 'created_at')
            ->orderBy('id')
            ->get();
    }

    public function headings(): array
    {
        return [
            'id',
            'name',
            'slug',
            'parent_name',
            'created_at',
        ];
    }

    public function map($row): array
    {
        return [
            $row->id,
            $row->name,
            $row->slug,
            $row->parent->name ?? '(Không có)',
            $row->created_at->format('Y-m-d H:i:s'),
        ];
    }

    public function startCell(): string
    {
        return 'A1';
    }

    public function styles(Worksheet $sheet)
    {
        // Header row đậm và căn giữa
        $sheet->getStyle('A1:E1')->getFont()->setBold(true);
        $sheet->getStyle('A1:E1')->getAlignment()->setHorizontal('center');

        return [];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();

                // Tự động canh giữa ID, Ngày tạo
                $sheet->getStyle('A:E')->getAlignment()->setVertical('center');

                // Viền toàn bảng
                $sheet->getStyle('A1:E' . $sheet->getHighestRow())
                    ->getBorders()->getAllBorders()->setBorderStyle('thin');

                // Màu nền tiêu đề
                $sheet->getStyle('A1:E1')->getFill()
                    ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                    ->getStartColor()->setARGB('FFD6EAF8');

                // Font chữ chung
                $sheet->getStyle('A:E')->getFont()->setName('Calibri')->setSize(12);

                // Freeze hàng tiêu đề
                $sheet->freezePane('A2');
            },
        ];
    }
}
