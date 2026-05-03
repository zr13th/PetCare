<?php
// app/Filament/Resources/ShipmentMethodResource.php

namespace App\Filament\Resources;

use App\Filament\Resources\ShipmentMethodResource\Pages;
use App\Models\ShipmentMethod;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ShipmentMethodResource extends Resource
{
    protected static ?string $model = ShipmentMethod::class;
    protected static ?string $navigationIcon = 'heroicon-o-truck';
    protected static ?string $navigationLabel = 'Phương thức vận chuyển';
    protected static ?string $modelLabel = 'Phương thức vận chuyển';
    protected static ?string $pluralModelLabel = 'Phương thức vận chuyển';
    protected static ?string $navigationGroup = 'Cấu hình';
    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make()->columns(2)->schema([
                Forms\Components\TextInput::make('name')
                    ->label('Tên')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('code')
                    ->label('Mã code')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->maxLength(50),
                Forms\Components\Textarea::make('description')
                    ->label('Mô tả')
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('fee')
                    ->label('Phí vận chuyển')
                    ->numeric()
                    ->default(0)
                    ->suffix('VND'),
                Forms\Components\TextInput::make('estimated_days')
                    ->label('Số ngày giao dự kiến')
                    ->numeric()
                    ->default(3)
                    ->suffix('ngày'),
                Forms\Components\TextInput::make('sort_order')
                    ->label('Thứ tự hiển thị')
                    ->numeric()
                    ->default(0),
                Forms\Components\Toggle::make('is_active')
                    ->label('Kích hoạt')
                    ->default(true),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('sort_order')->label('#')->sortable(),
                Tables\Columns\TextColumn::make('name')->label('Tên')->searchable(),
                Tables\Columns\TextColumn::make('code')->label('Code')->badge()->color('gray'),
                Tables\Columns\TextColumn::make('fee')->label('Phí ship')->money('VND'),
                Tables\Columns\TextColumn::make('estimated_days')->label('Ngày giao')->suffix(' ngày'),
                Tables\Columns\IconColumn::make('is_active')->label('Kích hoạt')->boolean(),
            ])
            ->reorderable('sort_order')
            ->defaultSort('sort_order')
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListShipmentMethods::route('/'),
            'create' => Pages\CreateShipmentMethod::route('/create'),
            'edit'   => Pages\EditShipmentMethod::route('/{record}/edit'),
        ];
    }
}