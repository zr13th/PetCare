<?php

namespace App\Exports;

use App\Models\Tag;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class TagExport implements FromCollection, WithHeadings
{
    public function collection()
    {
        return Tag::select('name', 'slug', 'description')->get();
    }

    public function headings(): array
    {
        return ['name', 'slug', 'description'];
    }
}
