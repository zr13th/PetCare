<?php

namespace App\Filament\Resources;

use App\Filament\Resources\WarehouseResource\Pages;
use App\Models\Warehouse;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

use pxlrbt\FilamentExcel\Actions\Tables\ExportBulkAction;

class WarehouseResource extends Resource
{
    protected static ?string $model = Warehouse::class;

    protected static ?string $navigationGroup = 'Quản lý kho';
    protected static ?string $navigationIcon = 'heroicon-o-home-modern';
    protected static ?string $navigationLabel = 'Kho hàng';
    protected static ?string $modelLabel = 'Kho hàng';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Thông tin kho hàng')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Tên kho')
                            ->placeholder('Ví dụ: Kho Quận 1')
                            ->required()
                            ->maxLength(255),

                        Forms\Components\TextInput::make('address')
                            ->label('Địa chỉ')
                            ->placeholder('Nhập địa chỉ đầy đủ')
                            ->maxLength(255),

                        Forms\Components\Toggle::make('is_active')
                            ->label('Trạng thái hoạt động')
                            ->default(true),
                    ])->columns(2)
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Tên kho')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('address')
                    ->label('Địa chỉ')
                    ->limit(50)
                    ->searchable(),

                Tables\Columns\ToggleColumn::make('is_active')
                    ->label('Hoạt động')
                    ->onColor('warning')
                    ->offColor('danger')
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Ngày tạo')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Trạng thái')
                    ->placeholder('Tất cả trạng thái')
                    ->trueLabel('Đang hoạt động')
                    ->falseLabel('Đang tắt'),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    ExportBulkAction::make(),

                ]),
            ])
            ->emptyStateHeading('Chưa có kho hàng nào')
            ->emptyStateDescription('Hãy bắt đầu bằng cách tạo một kho hàng mới.');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListWarehouses::route('/'),
            'create' => Pages\CreateWarehouse::route('/create'),
            'edit' => Pages\EditWarehouse::route('/{record}/edit'),
        ];
    }
}