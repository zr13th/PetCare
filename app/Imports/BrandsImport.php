<?php

namespace App\Imports;

use App\Models\Brand;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\OnEachRow;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Row;

class BrandsImport implements OnEachRow, WithHeadingRow
{
    public function onRow(Row $row)
    {
        $data = $row->toArray();

        // Bỏ qua dòng trống hoặc không có name
        if (empty($data['name'])) {
            return;
        }

        // Tạo slug nếu chưa có trong file Excel
        $slug = $data['slug'] ?? Str::slug($data['name']);

        // Cập nhật nếu trùng slug, ngược lại tạo mới
        Brand::updateOrCreate(
            ['slug' => $slug],
            [
                'name'        => $data['name'],
                'description' => $data['description'] ?? null,
                'logo'        => $data['logo'] ?? null,
            ]
        );
    }
}
