<?php

namespace App\Imports;

use App\Models\Attribute;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class AttributeImport implements ToModel, WithHeadingRow, WithValidation
{
    public function model(array $row)
    {
        $name = trim($row['ten_thuoc_tinh'] ?? $row['name'] ?? '');
        if (empty($name)) {
            return null;
        }

        $slug = !empty($row['slug']) ? Str::slug($row['slug']) : Str::slug($name);

        $attribute = Attribute::updateOrCreate(
            ['slug' => $slug],
            ['name' => $name]
        );

        $valuesString = $row['danh_sach_gia_tri'] ?? $row['values'] ?? '';
        if (!empty($valuesString)) {
            $valuesArray = explode(',', $valuesString);
            
            foreach ($valuesArray as $val) {
                $trimmedVal = trim($val);
                if (!empty($trimmedVal)) {
                    $attribute->values()->updateOrCreate(
                        ['value' => $trimmedVal]
                    );
                }
            }
        }

        return $attribute;
    }

    public function rules(): array
    {
        return [
            'ten_thuoc_tinh' => ['nullable', 'string', 'max:255'],
            'name'           => ['nullable', 'string', 'max:255'],
        ];
    }
}