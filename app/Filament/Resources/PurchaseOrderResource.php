<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PurchaseOrderResource\Pages;
use App\Models\PurchaseOrder;
use App\Models\ProductVariant;
use App\Models\Batch;
use App\Models\Inventory;
use App\Models\StockMovement;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Blade;

class PurchaseOrderResource extends Resource
{
    protected static ?string $model = PurchaseOrder::class;
    protected static ?string $navigationIcon = 'heroicon-o-shopping-cart';
    protected static ?string $navigationGroup = 'Quản lý kho';
    protected static ?string $navigationLabel = 'Nhập hàng';
    protected static ?string $modelLabel = 'Đơn nhập hàng';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Thông tin chung')->schema([
                Forms\Components\TextInput::make('po_number')
                    ->label('Mã đơn nhập')
                    ->default(fn () => 'PO-' . date('Ymd') . '-' . strtoupper(Str::random(4)))
                    ->readonly()
                    ->required(),

                Forms\Components\Select::make('supplier_id')
                    ->label('Nhà cung cấp')
                    ->relationship('supplier', 'name')
                    ->searchable()
                    ->preload()
                    ->required(),

                Forms\Components\Select::make('warehouse_id')
                    ->label('Kho nhập hàng')
                    ->relationship('warehouse', 'name')
                    ->searchable()
                    ->preload()
                    ->required(),

                Forms\Components\TextInput::make('total_amount')
                    ->label('Tổng giá trị đơn')
                    ->numeric()
                    ->prefix('VND')
                    ->placeholder('Tự động tính...')
                    ->readOnly()
                    ->dehydrated(),

                Forms\Components\Select::make('status')
                    ->label('Trạng thái')
                    ->options([
                        'pending' => 'Chờ duyệt',
                        'approved' => 'Đã duyệt',
                        'received' => 'Đã nhập kho',
                        'cancelled' => 'Đã hủy',
                    ])
                    ->default('pending')
                    ->disabled()
                    ->dehydrated(),
            ])->columns(5), // Tăng column để hiển thị đẹp hơn

            Forms\Components\Repeater::make('items')
                ->label('Danh sách sản phẩm nhập')
                ->relationship()
                ->live()
                ->afterStateUpdated(function (Forms\Get $get, Forms\Set $set) {
                    $items = $get('items') ?? [];
                    $total = 0;
                    foreach ($items as $item) {
                        $total += (float)($item['quantity'] ?? 0) * (float)($item['cost_price'] ?? 0);
                    }
                    $set('total_amount', $total);
                })
                ->schema([
                    Forms\Components\Select::make('variant_id')
                        ->label('Sản phẩm')
                        ->relationship(
                            name: 'variant', 
                            titleAttribute: 'sku',
                            modifyQueryUsing: fn (Builder $query) => $query->with('product')
                        )
                        ->getOptionLabelFromRecordUsing(fn (Model $record) => "{$record->product->name} - ({$record->sku})")
                        ->searchable(['sku'])
                        ->required()
                        ->columnSpan(3),

                    Forms\Components\TextInput::make('quantity')
                        ->label('Số lượng')
                        ->numeric()
                        ->default(1)
                        ->required()
                        ->live(onBlur: true),

                    Forms\Components\TextInput::make('cost_price')
                        ->label('Giá vốn')
                        ->numeric()
                        ->prefix('VND')
                        ->required()
                        ->live(onBlur: true),

                    Forms\Components\DatePicker::make('expiry_date')
                        ->label('Hạn sử dụng')
                        ->native(false),
                ])
                ->columns(6)
                ->columnSpanFull()
                ->addActionLabel('Thêm sản phẩm nhập'),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('po_number')->label('Mã PO')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('supplier.name')->label('Nhà cung cấp'),
                Tables\Columns\TextColumn::make('warehouse.name')->label('Kho nhập'),
                Tables\Columns\TextColumn::make('total_amount')
                    ->label('Tổng tiền')
                    ->money('VND')
                    ->sortable()
                    ->weight(\Filament\Support\Enums\FontWeight::Bold)
                    ->color('primary'),
                Tables\Columns\TextColumn::make('status')
                    ->label('Trạng thái')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'pending' => 'Chờ duyệt',
                        'approved' => 'Đã duyệt',
                        'received' => 'Đã nhập kho',
                        'cancelled' => 'Đã hủy',
                        default => $state,
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'warning',
                        'approved' => 'info',
                        'received' => 'success',
                        'cancelled' => 'danger',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('created_at')->label('Ngày lập')->date('d/m/Y'),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->visible(fn ($record) => $record->status === 'pending'),

                Tables\Actions\Action::make('approve_order')
                    ->label('Duyệt đơn')
                    ->icon('heroicon-o-check-badge')
                    ->color('info')
                    ->requiresConfirmation()
                    ->visible(fn ($record) => 
                        $record->status === 'pending' && 
                        auth()->user()->can('approve_purchase::order')
                    )
                    ->action(function (PurchaseOrder $record) {
                        $record->update(['status' => 'approved']);
                        Notification::make()->title('Đã duyệt đơn hàng!')->success()->send();
                    }),

                Tables\Actions\Action::make('confirm_received')
                    ->label('Nhập kho')
                    ->icon('heroicon-o-truck')
                    ->color('success')
                    ->requiresConfirmation()
                    ->visible(fn ($record) => 
                        $record->status === 'approved' && 
                        auth()->user()->can('receive_purchase::order')
                    )
                    ->action(function (PurchaseOrder $record) {
                        if (!$record->warehouse_id) {
                            Notification::make()->title('Lỗi: Chưa chọn kho nhập!')->danger()->send();
                            return;
                        }

                        DB::transaction(function () use ($record) {
                            foreach ($record->items as $item) {
                                // 1. Tạo lô hàng
                                $batch = Batch::create([
                                    'variant_id' => $item->variant_id,
                                    'batch_number' => 'BATCH-' . $record->po_number . '-' . $item->id,
                                    'cost_price' => $item->cost_price,
                                    'expiry_date' => $item->expiry_date, 
                                ]);

                                // 2. Cộng tồn kho (Lấy warehouse_id từ đơn hàng cha)
                                Inventory::updateOrCreate(
                                    ['warehouse_id' => $record->warehouse_id, 'batch_id' => $batch->id],
                                    ['quantity' => DB::raw("quantity + $item->quantity")]
                                );

                                // 3. Ghi log biến động
                                StockMovement::create([
                                    'batch_id' => $batch->id,
                                    'warehouse_id' => $record->warehouse_id,
                                    'type' => 'IN',
                                    'quantity' => $item->quantity,
                                    'reference_type' => PurchaseOrder::class,
                                    'reference_id' => $record->id,
                                ]);
                            }
                            $record->update(['status' => 'received']);
                        });
                        Notification::make()->title('Hàng đã nhập kho thành công!')->success()->send();
                    }),
                Tables\Actions\Action::make('print_pdf')
                    ->label('')
                    ->icon('heroicon-o-printer')
                    ->color('gray')
                    ->action(function (PurchaseOrder $record) {
                        $pdf = Pdf::loadView('pdf.purchase-order', ['record' => $record]);
                        
                        return response()->streamDownload(function () use ($pdf) {
                            echo $pdf->stream();
                        }, "Phieu_Nhap_{$record->po_number}.pdf");
                    }),
            ]);

    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPurchaseOrders::route('/'),
            'create' => Pages\CreatePurchaseOrder::route('/create'),
            'edit' => Pages\EditPurchaseOrder::route('/{record}/edit'),
        ];
    }

    public static function getPermissionPrefixes(): array
    {
        return [
            'view',
            'view_any',
            'create',
            'update',
            'delete',
            'delete_any',
            'approve',
            'receive',
        ];
    }
}