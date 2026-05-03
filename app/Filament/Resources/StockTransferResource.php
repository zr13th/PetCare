<?php

namespace App\Filament\Resources;

use App\Filament\Resources\StockTransferResource\Pages;
use App\Models\StockTransfer;
use App\Models\Inventory;
use App\Models\StockMovement;
use App\Models\Batch;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class StockTransferResource extends Resource
{
    protected static ?string $model = StockTransfer::class;
    protected static ?string $navigationIcon = 'heroicon-o-arrows-right-left';
    protected static ?string $navigationGroup = 'Quản lý kho';
    protected static ?string $navigationLabel = 'Chuyển kho';
    protected static ?string $modelLabel = 'Phiếu chuyển kho';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Thông tin phiếu chuyển')->schema([
                Forms\Components\TextInput::make('transfer_number')
                    ->label('Mã phiếu')
                    ->default(fn () => 'TRF-' . date('Ymd') . '-' . strtoupper(Str::random(4)))
                    ->readonly(),
                
                Forms\Components\Select::make('from_warehouse_id')
                    ->label('Kho xuất')
                    ->relationship('fromWarehouse', 'name')
                    ->required()
                    ->live() // Để cập nhật danh sách lô hàng bên dưới khi kho thay đổi
                    ->searchable()
                    ->preload()
                    ->disabled(fn ($record) => $record !== null),

                Forms\Components\Select::make('to_warehouse_id')
                    ->label('Kho nhập')
                    ->relationship('toWarehouse', 'name')
                    ->required()
                    ->different('from_warehouse_id')
                    ->searchable()
                    ->preload()
                    ->disabled(fn ($record) => $record !== null),
            ])->columns(3),

            Forms\Components\Hidden::make('created_by')
                ->default(auth()->id())
                ->required(),

            Forms\Components\Section::make('Sản phẩm chuyển kho')->schema([
                Forms\Components\Repeater::make('items')
                    ->label('')
                    ->relationship()
                    ->schema([
                        Forms\Components\Select::make('batch_id')
                            ->label('Lô hàng')
                            ->placeholder(fn (Forms\Get $get) => !$get('../../from_warehouse_id') 
                                ? 'Vui lòng chọn kho xuất trước...' 
                                : 'Chọn lô hàng')
                            ->options(function (Forms\Get $get) {
                                $fromWarehouseId = $get('../../from_warehouse_id');
                                if (!$fromWarehouseId) return [];
                                
                                return Inventory::where('warehouse_id', $fromWarehouseId)
                                    ->where('quantity', '>', 0)
                                    ->with('batch.variant.product')
                                    ->get()
                                    ->mapWithKeys(function ($inv) {
                                        $label = "{$inv->batch->full_name} - [Hiện có: {$inv->quantity}]";
                                        return [$inv->batch_id => $label];
                                    });
                            })
                            ->disabled(fn (Forms\Get $get) => !$get('../../from_warehouse_id'))
                            ->required()
                            ->searchable()
                            ->preload()
                            ->live()
                            ->columnSpan(3),

                        Forms\Components\TextInput::make('quantity')
                            ->label('SL chuyển')
                            ->numeric()
                            ->required()
                            ->minValue(1)
                            ->columnSpan(1)
                            ->helperText(function (Forms\Get $get) {
                                $batchId = $get('batch_id');
                                $fromWarehouseId = $get('../../from_warehouse_id');
                                if ($batchId && $fromWarehouseId) {
                                    $inv = Inventory::where('warehouse_id', $fromWarehouseId)
                                                    ->where('batch_id', $batchId)
                                                    ->first();
                                    return $inv ? "Tồn kho hiện tại: {$inv->quantity}" : null;
                                }
                            })
                            ->rules([
                                fn (Forms\Get $get): \Closure => function (string $attribute, $value, \Closure $fail) use ($get) {
                                    $batchId = $get('batch_id');
                                    $fromWarehouseId = $get('../../from_warehouse_id');
                                    if ($batchId && $fromWarehouseId) {
                                        $inv = Inventory::where('warehouse_id', $fromWarehouseId)
                                                        ->where('batch_id', $batchId)
                                                        ->first();
                                        if ($inv && $value > $inv->quantity) {
                                            $fail("Số lượng không được vượt quá tồn kho ({$inv->quantity}).");
                                        }
                                    }
                                },
                            ]),
                    ])
                    ->columns(4)
                    ->columnSpanFull()
                    ->addActionLabel('Thêm lô hàng')
                    ->itemLabel(fn (array $state): ?string => 
                        isset($state['batch_id']) 
                        ? Batch::find($state['batch_id'])?->full_name 
                        : null
                    ),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('transfer_number')->label('Mã phiếu')->searchable()->badge(),
                Tables\Columns\TextColumn::make('fromWarehouse.name')->label('Từ kho'),
                Tables\Columns\TextColumn::make('toWarehouse.name')->label('Đến kho'),
                Tables\Columns\TextColumn::make('status')
                    ->label('Trạng thái')
                    ->badge()
                    ->color(fn ($state) => match($state) {
                        'pending' => 'warning',
                        'completed' => 'success',
                        default => 'gray'
                    })
                    ->formatStateUsing(fn ($state) => $state === 'pending' ? 'Chờ xác nhận' : 'Hoàn thành'),
                Tables\Columns\TextColumn::make('created_at')->label('Ngày lập')->dateTime('d/m/Y'),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->visible(fn ($record) => $record->status === 'pending'),
                
                Tables\Actions\Action::make('complete_transfer')
                    ->label('Xác nhận')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->visible(fn ($record) => $record->status === 'pending')
                    ->action(function (StockTransfer $record) {
                        DB::transaction(function () use ($record) {
                            foreach ($record->items as $item) {
                                $sourceInv = Inventory::where([
                                    'warehouse_id' => $record->from_warehouse_id,
                                    'batch_id' => $item->batch_id
                                ])->first();

                                if (!$sourceInv || $sourceInv->quantity < $item->quantity) {
                                    Notification::make()->title('Lỗi!')->body("Lô {$item->batch_id} không đủ tồn kho.")->danger()->send();
                                    return;
                                }

                                // Trừ kho xuất - Cộng kho nhập
                                $sourceInv->decrement('quantity', $item->quantity);
                                Inventory::updateOrCreate(
                                    ['warehouse_id' => $record->to_warehouse_id, 'batch_id' => $item->batch_id],
                                    ['quantity' => DB::raw("quantity + $item->quantity")]
                                );

                                // Ghi log biến động
                                StockMovement::create([
                                    'batch_id' => $item->batch_id,
                                    'warehouse_id' => $record->from_warehouse_id,
                                    'type' => 'OUT',
                                    'quantity' => $item->quantity,
                                    'reference_type' => StockTransfer::class,
                                    'reference_id' => $record->id,
                                    'notes' => "Xuất chuyển kho sang {$record->toWarehouse->name}"
                                ]);

                                StockMovement::create([
                                    'batch_id' => $item->batch_id,
                                    'warehouse_id' => $record->to_warehouse_id,
                                    'type' => 'IN',
                                    'quantity' => $item->quantity,
                                    'reference_type' => StockTransfer::class,
                                    'reference_id' => $record->id,
                                    'notes' => "Nhập từ chuyển kho từ {$record->fromWarehouse->name}"
                                ]);
                            }
                            $record->update(['status' => 'completed']);
                        });
                        Notification::make()->title('Chuyển kho thành công!')->success()->send();
                    })
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListStockTransfers::route('/'),
            'create' => Pages\CreateStockTransfer::route('/create'),
            'edit' => Pages\EditStockTransfer::route('/{record}/edit'),
        ];
    }
}