<?php

namespace App\Exports;

use App\Models\Attribute;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class AttributeExport implements FromQuery, WithHeadings, WithMapping
{
    public function query()
    {
        return Attribute::query()->with('values');
    }

    public function headings(): array
    {
        return [
            'ID',
            'ten_thuoc_tinh',
            'slug',
            'danh_sach_gia_tri',
            'Ngay_tao',
        ];
    }

    public function map($attribute): array
    {
        return [
            $attribute->id,
            $attribute->name,
            $attribute->slug,
            $attribute->values->pluck('value')->implode(', '),
            $attribute->created_at->format('d/m/Y H:i'),
        ];
    }
}