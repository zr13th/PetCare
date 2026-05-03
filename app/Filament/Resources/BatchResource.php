<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BatchResource\Pages;
use App\Models\Batch;
use App\Models\StockMovement;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

class BatchResource extends Resource
{
    protected static ?string $model = Batch::class;
    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';
    protected static ?string $navigationGroup = 'Quản lý kho';
    protected static ?string $modelLabel = 'Lô hàng';
    protected static ?string $pluralModelLabel = 'Lô hàng';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Thông tin lô hàng')
                    ->description('Quản lý nguồn gốc, giá nhập và hạn dùng của sản phẩm.')
                    ->schema([
                        Forms\Components\Select::make('variant_id')
                            ->relationship('variant', 'sku')
                            ->label('Biến thể sản phẩm (SKU)')
                            ->getOptionLabelFromRecordUsing(fn ($record) => "{$record->product->name} - {$record->sku}")
                            ->required()
                            ->searchable()
                            ->preload()
                            ->columnSpanFull(),

                        Forms\Components\TextInput::make('batch_number')
                            ->label('Số lô')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->default(fn () => 'BATCH-' . strtoupper(Str::random(8))),

                        Forms\Components\TextInput::make('cost_price')
                            ->label('Giá nhập (Vốn)')
                            ->numeric()
                            ->required()
                            ->prefix('VND')
                            ->maxValue(999999999999),

                        Forms\Components\DatePicker::make('manufacture_date')
                            ->label('Ngày sản xuất')
                            ->native(false)
                            ->displayFormat('d/m/Y'),

                        Forms\Components\DatePicker::make('expiry_date')
                            ->label('Hạn sử dụng')
                            ->native(false)
                            ->displayFormat('d/m/Y')
                            ->required(),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('batch_number')
                    ->label('Mã lô')
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->badge()
                    ->color('gray'),

                Tables\Columns\TextColumn::make('variant.product.name')
                    ->label('Sản phẩm')
                    ->description(fn (Batch $record): string => $record->variant->sku ?? 'N/A')
                    ->searchable(),

                Tables\Columns\TextColumn::make('cost_price')
                    ->label('Giá vốn')
                    ->money('VND')
                    ->sortable(),

                Tables\Columns\TextColumn::make('expiry_date')
                    ->label('Hạn sử dụng')
                    ->date('d/m/Y')
                    ->sortable()
                    ->color(fn (Batch $record): string => 
                        $record->expiry_date && $record->expiry_date->isPast() ? 'danger' : 
                        ($record->expiry_date && $record->expiry_date->diffInDays(now()) <= 30 ? 'warning' : 'success')
                    )
                    ->description(fn (Batch $record): string => 
                        $record->expiry_date && $record->expiry_date->isPast() 
                            ? 'Đã hết hạn!' 
                            : ($record->expiry_date && $record->expiry_date->diffInDays(now()) <= 30 
                                ? 'Sắp hết hạn!' 
                                : '')
                    ),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Ngày nhập lô')
                    ->dateTime('d/m/Y H:i')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\Filter::make('expired')
                    ->label('Đã hết hạn')
                    ->query(fn (Builder $query): Builder => $query->where('expiry_date', '<', now())),

                Tables\Filters\Filter::make('near_expiry')
                    ->label('Sắp hết hạn (30 ngày)')
                    ->query(fn (Builder $query): Builder => $query->whereBetween('expiry_date', [now(), now()->addDays(30)])),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(), // Thêm nút xem chi tiết
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ]);
    }

    // Trang chi tiết để xem Stock Timeline
    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Section::make('Tổng quan lô hàng')
                    ->schema([
                        Infolists\Components\TextEntry::make('batch_number')->label('Mã lô hàng')->badge()->color('gray'),
                        Infolists\Components\TextEntry::make('variant.product.name')->label('Sản phẩm'),
                        Infolists\Components\TextEntry::make('cost_price')->label('Giá vốn nhập')->money('VND'),
                        Infolists\Components\TextEntry::make('expiry_date')->label('Ngày hết hạn')->date('d/m/Y'),
                    ])->columns(4),

                Infolists\Components\Section::make('Lịch sử biến động kho (Stock Timeline)')
                    ->description('Dòng chảy hàng hóa của lô hàng này qua các kho.')
                    ->schema([
                        Infolists\Components\ViewEntry::make('movements')
                            ->label('')
                            ->view('filament.components.stock-timeline') // Gọi file blade timeline
                            ->getStateUsing(fn ($record) => [
                                'movements' => StockMovement::where('batch_id', $record->id)
                                    ->with('warehouse')
                                    ->latest()
                                    ->get()
                            ])
                    ])
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListBatches::route('/'),
            'create' => Pages\CreateBatch::route('/create'),
            'view' => Pages\ViewBatch::route('/{record}'), // Đăng ký trang view
            'edit' => Pages\EditBatch::route('/{record}/edit'),
        ];
    }
}