<?php

namespace App\Filament\Resources\PetTypeResource\Pages;

use App\Filament\Resources\PetTypeResource;
use App\Imports\PetTypeImport;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Forms\Components\FileUpload;
use Filament\Notifications\Notification;
use Maatwebsite\Excel\Facades\Excel;

class ListPetTypes extends ListRecords
{
    protected static string $resource = PetTypeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),

            Actions\Action::make('import_pet_types')
                ->label('Nhập Excel')
                ->icon('heroicon-o-arrow-up-tray')
                ->color('success')
                ->form([
                    FileUpload::make('file')
                        ->label('Chọn file Excel (.xlsx) hoặc CSV')
                        ->required()
                        ->disk('public')
                        ->acceptedFileTypes([
                            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                            'text/csv',
                        ]),
                ])
                ->action(function (array $data) {
                    try {
                        $filePath = storage_path('app/public/' . $data['file']);
                        
                        Excel::import(new PetTypeImport, $filePath);

                        Notification::make()
                            ->title('Thành công')
                            ->body('Đã nhập dữ liệu loài thú cưng.')
                            ->success()
                            ->send();
                            
                    } catch (\Exception $e) {
                        Notification::make()
                            ->title('Lỗi nhập dữ liệu')
                            ->body('Có lỗi xảy ra: ' . $e->getMessage())
                            ->danger()
                            ->send();
                    }
                }),
        ];
    }
}