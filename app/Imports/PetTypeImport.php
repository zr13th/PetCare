<?php

namespace App\Imports;

use App\Models\PetType;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class PetTypeImport implements ToModel, WithHeadingRow, WithValidation
{
    public function model(array $row)
    {
        $name = trim($row['ten_loai'] ?? $row['name'] ?? '');

        if (empty($name)) {
            return null;
        }

        $slug = !empty($row['slug']) ? Str::slug($row['slug']) : Str::slug($name);

        return PetType::updateOrCreate(
            ['slug' => $slug],
            ['name' => $name]
        );
    }

    public function rules(): array
    {
        return [
            'ten_loai' => ['nullable', 'string', 'max:255'],
            'name'     => ['nullable', 'string', 'max:255'],
            'slug'     => ['nullable', 'string', 'max:255'],
        ];
    }
}