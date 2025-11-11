<?php

namespace App\Imports;

use App\Models\Product;
use App\Models\Category;
use App\Models\Brand;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\OnEachRow;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Row;

class ProductsImport implements OnEachRow, WithHeadingRow
{
    public function onRow(Row $row)
    {
        $data = $row->toArray();
        if (empty($data['name'])) return;

        $category = Category::where('name', $data['category'] ?? '')->first();
        $brand = Brand::where('name', $data['brand'] ?? '')->first();

        $slug = $data['slug'] ?? Str::slug($data['name']);

        Product::updateOrCreate(
            ['slug' => $slug],
            [
                'name' => $data['name'],
                'category_id' => $category?->id,
                'brand_id' => $brand?->id,
                'price' => $data['price'] ?? 0,
                'stock' => $data['stock'] ?? 0,
                'description' => $data['description'] ?? null,
                'image' => $data['image'] ?? null,
            ]
        );
    }
}
