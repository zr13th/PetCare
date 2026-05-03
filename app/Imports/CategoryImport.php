<?php

namespace App\Imports;

use App\Models\Category;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class CategoryImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        $name = trim($row['ten_danh_muc'] ?? '');
        $parentName = trim($row['danh_muc_cha'] ?? '');
        $isActive = $row['hoat_dong'] ?? 1;

        if (empty($name)) {
            return null;
        }

        $parentId = null;
        if (!empty($parentName)) {
            $parentCategory = Category::where('name', $parentName)->first();
            if ($parentCategory) {
                $parentId = $parentCategory->id;
            }
        }

        $slug = Str::slug($name);

        return Category::updateOrCreate(
            [
                'name' => $name,
            ],
            [
                'parent_id' => $parentId,
                'slug'      => $slug,
                'is_active' => (bool)$isActive,
            ]
        );
    }
}