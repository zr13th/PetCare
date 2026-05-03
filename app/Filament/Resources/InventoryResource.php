<?php

namespace App\Filament\Resources;

use App\Filament\Resources\InventoryResource\Pages;
use App\Models\Inventory;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class InventoryResource extends Resource
{
    protected static ?string $model = Inventory::class;

    protected static ?string $navigationIcon = 'heroicon-o-archive-box';
    protected static ?string $navigationGroup = 'Quản lý kho';
    protected static ?string $modelLabel = 'Tồn kho';
    protected static ?string $pluralModelLabel = 'Bảng tồn kho';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Chi tiết tồn kho')
                    ->description('Dữ liệu tồn kho được cập nhật tự động từ các phiếu nhập/xuất.')
                    ->schema([
                        Forms\Components\Select::make('warehouse_id')
                            ->relationship('warehouse', 'name')
                            ->label('Kho hàng')
                            ->required()
                            ->disabled(),
                        
                        Forms\Components\Select::make('batch_id')
                            ->relationship('batch', 'batch_number')
                            ->label('Số lô hàng')
                            ->required()
                            ->disabled(),

                        Forms\Components\TextInput::make('quantity')
                            ->label('Số lượng tồn thực tế')
                            ->numeric()
                            ->required()
                            ->prefix('SL:'),
                    ])->columns(3),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('warehouse.name')
                    ->label('Kho hàng')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('batch.variant.product.name')
                    ->label('Sản phẩm')
                    ->description(fn (Inventory $record): string => "Biến thể: " . ($record->batch->variant->sku ?? 'N/A'))
                    ->searchable(),

                Tables\Columns\TextColumn::make('batch.batch_number')
                    ->label('Số lô')
                    ->copyable()
                    ->badge()
                    ->color('gray')
                    ->searchable(),

                Tables\Columns\TextColumn::make('quantity')
                    ->label('Số lượng')
                    ->numeric()
                    ->sortable()
                    ->alignCenter()
                    ->color(fn (Inventory $record): string => 
                        $record->quantity <= ($record->batch->variant->stock_alert ?? 0) ? 'danger' : 'success'
                    )
                    ->description(fn (Inventory $record): string => 
                        $record->quantity <= ($record->batch->variant->stock_alert ?? 0) ? 'Sắp hết hàng!' : ''
                    ),

                Tables\Columns\TextColumn::make('batch.expiry_date')
                    ->label('Hạn sử dụng')
                    ->date('d/m/Y')
                    ->sortable()
                    ->color(fn ($state) => $state && now()->gt($state) ? 'danger' : 'gray'),

                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Cập nhật cuối')
                    ->dateTime('H:i d/m/Y')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                // 1. Lọc theo kho hàng
                Tables\Filters\SelectFilter::make('warehouse_id')
                    ->label('Lọc theo kho')
                    ->relationship('warehouse', 'name'),
                
                // 2. Lọc sản phẩm sắp hết hàng
                Tables\Filters\Filter::make('low_stock')
                    ->label('Sắp hết hàng')
                    ->query(fn (Builder $query): Builder => $query
                        ->whereHas('batch.variant', function (Builder $subQuery) {
                            $subQuery->whereRaw('inventories.quantity <= product_variants.stock_alert');
                        })
                    ),
                
                // 3. Lọc hàng đã hết hạn
                Tables\Filters\Filter::make('expiring_soon')
                    ->label('Sắp hết hạn (30 ngày)')
                    ->query(fn (Builder $query): Builder => $query
                        ->whereHas('batch', fn (Builder $subQuery) => 
                            $subQuery->where('expiry_date', '>', now())
                                    ->where('expiry_date', '<=', now()->addDays(30))
                        )
                    ),
                // 4. Lọc hàng đã hết hạn
                Tables\Filters\Filter::make('expired')
                    ->label('Đã hết hạn')
                    ->query(fn (Builder $query): Builder => $query
                        ->whereHas('batch', fn (Builder $subQuery) => 
                            $subQuery->where('expiry_date', '<', now())
                        )
                    ),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListInventories::route('/'),
            'create' => Pages\CreateInventory::route('/create'),
            'edit' => Pages\EditInventory::route('/{record}/edit'),
        ];
    }
}