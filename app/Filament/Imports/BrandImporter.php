<?php

namespace App\Filament\Imports;

use App\Models\Brand;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;
use Illuminate\Support\Str;

class BrandImporter extends Importer
{
    protected static ?string $model = Brand::class;

    public static function getColumns(): array
    {
        return [
            // Cột Tên thương hiệu
            ImportColumn::make('name')
                ->requiredMapping() // Bắt buộc phải khớp cột này trong Excel
                ->rules(['required', 'max:255']),

            // Cột Slug
            ImportColumn::make('slug')
                ->rules(['required', 'max:255']),
        ];
    }

    public function resolveRecord(): ?Brand
    {
        /** * Logic: Nếu tìm thấy thương hiệu cùng tên thì cập nhật, 
         * nếu không thì tạo mới.
         */
        $brand = Brand::firstOrNew([
            'name' => $this->data['name'],
        ]);

        // Tự động tạo slug nếu trong file Excel cột slug bị trống
        if (empty($this->data['slug'])) {
            $brand->slug = Str::slug($this->data['name']);
        } else {
            $brand->slug = $this->data['slug'];
        }

        return $brand;
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = 'Nhập dữ liệu thương hiệu hoàn tất! ' . number_format($import->successful_rows) . ' ' . str('dòng')->plural($import->successful_rows) . ' đã được nhập thành công.';

        if ($failedRowsCount = $import->getFailedRowsCount()) {
            $body .= ' Có ' . number_format($failedRowsCount) . ' dòng bị lỗi.';
        }

        return $body;
    }
}