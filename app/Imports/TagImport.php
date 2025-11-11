<?php

namespace App\Imports;

use App\Models\Tag;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\OnEachRow;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Row;

class TagImport implements OnEachRow, WithHeadingRow
{
    public function onRow(Row $row)
    {
        $data = $row->toArray();

        if (empty($data['name'])) return;

        $slug = $data['slug'] ?? Str::slug($data['name']);

        Tag::updateOrCreate(
            ['slug' => $slug],
            [
                'name' => $data['name'],
                'description' => $data['description'] ?? null,
            ]
        );
    }
}
