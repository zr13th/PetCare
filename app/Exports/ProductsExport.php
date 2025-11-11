<?php

namespace App\Exports;

use App\Models\Product;
use Maatwebsite\Excel\Concerns\{
    FromCollection,
    WithHeadings,
    WithMapping,
    ShouldAutoSize
};

class ProductsExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize
{
    public function collection()
    {
        return Product::with(['category', 'brand'])->get();
    }

    public function headings(): array
    {
        return ['id', 'name', 'slug', 'category', 'brand', 'price', 'stock', 'description', 'image', 'created_at'];
    }

    public function map($p): array
    {
        return [
            $p->id,
            $p->name,
            $p->slug,
            $p->category?->name,
            $p->brand?->name,
            $p->price,
            $p->stock,
            $p->description,
            $p->image,
            optional($p->created_at)->format('Y-m-d H:i:s'),
        ];
    }
}
