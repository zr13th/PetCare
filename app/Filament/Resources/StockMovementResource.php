<?php

namespace App\Filament\Resources;

use App\Filament\Resources\StockMovementResource\Pages;
use App\Models\StockMovement;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class StockMovementResource extends Resource
{
    protected static ?string $model = StockMovement::class;

    protected static ?string $navigationIcon = 'heroicon-o-arrow-path-rounded-square';
    protected static ?string $navigationGroup = 'Quản lý kho';
    protected static ?string $modelLabel = 'Nhật ký kho';
    protected static ?string $pluralModelLabel = 'Lịch sử kho';

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Thời gian')
                    ->dateTime('H:i d/m/Y')
                    ->sortable(),

                Tables\Columns\TextColumn::make('type')
                    ->label('Loại')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'IN' => 'success',
                        'OUT' => 'danger',
                        'TRANSFER' => 'info',
                        'ADJUST' => 'warning',
                    })
                    ->searchable(),

                Tables\Columns\TextColumn::make('batch.variant.sku')
                    ->label('Mã sản phẩm')
                    ->searchable(),

                Tables\Columns\TextColumn::make('warehouse.name')
                    ->label('Kho')
                    ->sortable(),

                Tables\Columns\TextColumn::make('quantity')
                    ->label('Số lượng')
                    ->color(fn ($record) => $record->type === 'IN' ? 'success' : 'danger')
                    ->prefix(fn ($record) => $record->type === 'IN' ? '+' : '-'),

                Tables\Columns\TextColumn::make('reference_type')
                    ->label('Nguồn gốc')
                    ->formatStateUsing(fn ($state) => class_basename($state)),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('type')
                    ->options([
                        'IN' => 'Nhập hàng',
                        'OUT' => 'Xuất hàng',
                        'TRANSFER' => 'Chuyển kho',
                        'ADJUST' => 'Điều chỉnh',
                    ]),
            ]);
    }

    // Vì đây là bảng nhật ký, thường chúng ta chỉ cho xem (để đảm bảo tính trung thực của dữ liệu)
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListStockMovements::route('/'),
        ];
    }
}