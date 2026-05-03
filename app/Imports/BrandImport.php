<?php

namespace App\Imports;

use App\Models\Brand;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class BrandImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        $name = trim($row['name'] ?? $row['ten_thuong_hieu'] ?? '');
        
        if (empty($name)) return null;

        $slug = !empty($row['slug']) ? Str::slug($row['slug']) : Str::slug($name);

        return Brand::updateOrCreate(
            ['slug' => $slug],
            ['name' => $name]
        );
    }
}