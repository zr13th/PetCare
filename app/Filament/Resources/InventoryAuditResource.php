<?php

namespace App\Filament\Resources;

use App\Filament\Resources\InventoryAuditResource\Pages;
use App\Models\InventoryAudit;
use App\Models\Inventory;
use App\Models\StockMovement;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class InventoryAuditResource extends Resource
{
    protected static ?string $model = InventoryAudit::class;
    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-check';
    protected static ?string $navigationGroup = 'Quản lý kho';
    protected static ?string $navigationLabel = 'Kiểm kê';
    protected static ?string $modelLabel = 'Phiếu kiểm kê';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Thông tin đợt kiểm kê')->schema([
                Forms\Components\TextInput::make('audit_number')
                    ->label('Mã phiếu')
                    ->default(fn () => 'AUD-' . date('Ymd') . '-' . strtoupper(Str::random(4)))
                    ->readonly()
                    ->required(),

                Forms\Components\Select::make('warehouse_id')
                    ->label('Kho kiểm kê')
                    ->relationship('warehouse', 'name')
                    ->searchable()
                    ->preload()
                    ->required()
                    ->disabled(fn ($record) => $record !== null) // Không cho sửa kho sau khi đã tạo
                    ->live()
                    ->afterStateUpdated(function ($state, Forms\Set $set) {
                        if (!$state) {
                            $set('items', []);
                            return;
                        }

                        // Tự động lấy toàn bộ sản phẩm đang có trong kho này
                        $inventoryItems = Inventory::where('warehouse_id', $state)
                            ->with(['batch.variant.product'])
                            ->get()
                            ->map(function ($inv) {
                                return [
                                    'batch_id' => $inv->batch_id,
                                    'system_quantity' => $inv->quantity,
                                    'actual_quantity' => $inv->quantity, // Mặc định bằng máy
                                    'difference' => 0,
                                ];
                            })->toArray();

                        $set('items', $inventoryItems);
                    }),

                Forms\Components\Select::make('created_by')
                    ->label('Người lập')
                    ->relationship('creator', 'name')
                    ->default(auth()->id())
                    ->disabled()
                    ->dehydrated(),

                Forms\Components\Textarea::make('notes')
                    ->label('Ghi chú')
                    ->columnSpanFull(),
            ])->columns(3),

            Forms\Components\Section::make('Chi tiết kiểm kê')->schema([
                Forms\Components\Repeater::make('items')
                    ->label('Danh sách hàng hóa')
                    ->relationship()
                    ->schema([
                        Forms\Components\Select::make('batch_id')
                            ->label('Sản phẩm (Lô)')
                            ->relationship('batch', 'batch_number')
                            ->getOptionLabelFromRecordUsing(fn ($record) => 
                                "{$record->variant->product->name} - [Lô: {$record->batch_number}]"
                            )
                            ->disabled()
                            ->dehydrated()
                            ->required()
                            ->columnSpan(2),

                        Forms\Components\TextInput::make('system_quantity')
                            ->label('Tồn máy')
                            ->numeric()
                            ->readOnly()
                            ->suffix('SP'),

                        Forms\Components\TextInput::make('actual_quantity')
                            ->label('Thực tế')
                            ->numeric()
                            ->required()
                            ->minValue(0)
                            ->live(onBlur: true)
                            ->afterStateUpdated(function ($state, Forms\Get $get, Forms\Set $set) {
                                $system = (int) $get('system_quantity');
                                $actual = (int) $state;
                                $set('difference', $actual - $system);
                            }),

                        Forms\Components\TextInput::make('difference')
                            ->label('Chênh lệch')
                            ->numeric()
                            ->readOnly()
                            ->placeholder('0')
                            ->prefix(fn ($state) => $state > 0 ? '+' : ''),
                    ])
                    ->columns(5)
                    ->addable(false) // Không cho thêm dòng lẻ để đảm bảo tính toàn vẹn của kho
                    ->deletable(false)
                    ->columnSpanFull(),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('audit_number')->label('Mã phiếu')->searchable(),
                Tables\Columns\TextColumn::make('warehouse.name')->label('Kho'),
                Tables\Columns\TextColumn::make('status')
                    ->label('Trạng thái')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'draft' => 'gray',
                        'completed' => 'success',
                    })
                    ->formatStateUsing(fn ($state) => $state === 'draft' ? 'Đang kiểm' : 'Hoàn thành'),
                Tables\Columns\TextColumn::make('creator.name')->label('Người lập'),
                Tables\Columns\TextColumn::make('created_at')->label('Ngày tạo')->dateTime('d/m/Y H:i'),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make()
                    ->visible(fn ($record) => $record->status === 'draft'),

                // Nút Chốt kiểm kê - Quan trọng nhất
                Tables\Actions\Action::make('complete_audit')
                    ->label('Chốt kho')
                    ->icon('heroicon-o-check-badge')
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalHeading('Xác nhận hoàn tất kiểm kê')
                    ->modalDescription('Sau khi chốt, số lượng tồn kho sẽ được cập nhật lại theo số thực tế. Bạn không thể sửa phiếu này nữa.')
                    ->visible(fn ($record) => $record->status === 'draft')
                    ->action(function (InventoryAudit $record) {
                        DB::transaction(function () use ($record) {
                            foreach ($record->items as $item) {
                                if ($item->difference != 0) {
                                    // 1. Cập nhật bảng Inventory chính
                                    Inventory::where([
                                        'warehouse_id' => $record->warehouse_id,
                                        'batch_id' => $item->batch_id
                                    ])->update(['quantity' => $item->actual_quantity]);

                                    // 2. Ghi log biến động kho
                                    StockMovement::create([
                                        'batch_id' => $item->batch_id,
                                        'warehouse_id' => $record->warehouse_id,
                                        'type' => $item->difference > 0 ? 'IN' : 'OUT',
                                        'quantity' => abs($item->difference),
                                        'reference_type' => InventoryAudit::class,
                                        'reference_id' => $record->id,
                                        'notes' => "Điều chỉnh kiểm kê ({$record->audit_number})",
                                    ]);
                                }
                            }
                            $record->update(['status' => 'completed']);
                        });

                        Notification::make()
                            ->title('Kiểm kê hoàn tất!')
                            ->body('Số lượng tồn kho đã được cập nhật chính xác.')
                            ->success()
                            ->send();
                    }),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListInventoryAudits::route('/'),
            'create' => Pages\CreateInventoryAudit::route('/create'),
            'edit' => Pages\EditInventoryAudit::route('/{record}/edit'),
        ];
    }
}