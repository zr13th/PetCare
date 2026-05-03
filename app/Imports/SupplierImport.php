<?php

namespace App\Imports;

use App\Models\Supplier;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class SupplierImport implements ToModel, WithHeadingRow, WithValidation
{
    public function model(array $row)
    {
        $name = trim($row['ten_nha_cung_cap'] ?? $row['name'] ?? '');

        if (empty($name)) {
            return null;
        }

        return Supplier::updateOrCreate(
            ['name' => $name],
            [
                'phone'   => $row['so_dien_thoai'] ?? $row['phone'] ?? null,
                'email'   => $row['email'] ?? null,
                'address' => $row['dia_chi'] ?? $row['address'] ?? null,
            ]
        );
    }

    public function rules(): array
    {
        return [
            'ten_nha_cung_cap' => ['nullable', 'string', 'max:255'],
            'name'             => ['nullable', 'string', 'max:255'],
            'email'            => ['nullable', 'email', 'max:255'],
        ];
    }
}