<?php

namespace App\Filament\Resources\AttributeResource\Pages;

use App\Filament\Resources\AttributeResource;
use App\Imports\AttributeImport;
use App\Exports\AttributeExport;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Forms\Components\FileUpload;
use Filament\Notifications\Notification;
use Maatwebsite\Excel\Facades\Excel;

class ListAttributes extends ListRecords
{
    protected static string $resource = AttributeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),

            Actions\Action::make('export')
                ->label('Xuất Excel')
                ->icon('heroicon-o-arrow-down-tray')
                ->color('info')
                ->action(fn () => Excel::download(new AttributeExport, 'danh-sach-thuoc-tinh.xlsx')),

            Actions\Action::make('import_attributes')
                ->label('Nhập Excel')
                ->icon('heroicon-o-arrow-up-tray')
                ->color('success')
                ->form([
                    FileUpload::make('file')
                        ->label('Chọn file Excel hoặc CSV')
                        ->required()
                        ->disk('public')
                        ->acceptedFileTypes([
                            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                            'application/vnd.ms-excel',
                            'text/csv',
                        ]),
                ])
                ->action(function (array $data) {
                    try {
                        $filePath = storage_path('app/public/' . $data['file']);
                        
                        Excel::import(new AttributeImport, $filePath);

                        Notification::make()
                            ->title('Thành công')
                            ->body('Đã nhập thuộc tính và các giá trị tương ứng.')
                            ->success()
                            ->send();
                            
                    } catch (\Exception $e) {
                        Notification::make()
                            ->title('Lỗi nhập dữ liệu')
                            ->body($e->getMessage())
                            ->danger()
                            ->send();
                    }
                }),
        ];
    }
}