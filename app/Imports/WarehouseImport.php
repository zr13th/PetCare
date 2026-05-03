<?php

namespace App\Imports;

use App\Models\Warehouse;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class WarehouseImport implements ToModel, WithHeadingRow, WithValidation
{
    public function model(array $row)
    {
        $name = trim($row['ten_kho'] ?? $row['name'] ?? '');

        if (empty($name)) {
            return null;
        }

        return Warehouse::updateOrCreate(
            ['name' => $name],
            [
                'address'   => $row['dia_chi'] ?? $row['address'] ?? null,
                'is_active' => filter_var($row['hoat_dong'] ?? $row['is_active'] ?? true, FILTER_VALIDATE_BOOLEAN),
            ]
        );
    }

    public function rules(): array
    {
        return [
            'ten_kho' => ['nullable', 'string', 'max:255'],
            'name'    => ['nullable', 'string', 'max:255'],
        ];
    }
}