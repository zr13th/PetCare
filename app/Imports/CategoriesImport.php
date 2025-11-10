<?php

namespace App\Imports;

use App\Models\Category;
use Maatwebsite\Excel\Concerns\OnEachRow;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Row;

class CategoriesImport implements OnEachRow, WithHeadingRow
{
    public function onRow(Row $row)
    {
        $data = $row->toArray();

        // Tìm danh mục cha theo tên (nếu có)
        $parent = Category::where('name', $data['parent_name'] ?? '')->first();

        // Cập nhật nếu trùng slug, ngược lại thì tạo mới
        Category::updateOrCreate(
            ['slug' => $data['slug']], // điều kiện trùng
            [
                'name'      => $data['name'],
                'parent_id' => $parent->id ?? null,
            ]
        );
    }
}
