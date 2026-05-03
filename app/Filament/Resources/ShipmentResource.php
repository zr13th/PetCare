<?php
// app/Filament/Resources/ShipmentResource.php

namespace App\Filament\Resources;

use App\Filament\Resources\InvoiceResource;
use App\Filament\Resources\ShipmentResource\Pages;
use App\Models\Shipment;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class ShipmentResource extends Resource
{
    protected static ?string $model = Shipment::class;
    protected static ?string $navigationIcon = 'heroicon-o-truck';
    protected static ?string $navigationLabel = 'Vận chuyển';
    protected static ?string $modelLabel = 'Vận chuyển';
    protected static ?string $pluralModelLabel = 'Vận chuyển';
    protected static ?string $navigationGroup = 'Bán hàng';
    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make()->columns(2)->schema([
                Forms\Components\Select::make('invoice_id')
                    ->label('Hóa đơn')
                    ->relationship('invoice', 'invoice_number')
                    ->searchable()
                    ->preload()
                    ->required(),
                Forms\Components\Select::make('shipment_method_id')
                    ->label('Phương thức vận chuyển')
                    ->relationship('shipmentMethod', 'name')
                    ->required(),
                Forms\Components\Select::make('status')
                    ->label('Trạng thái')
                    ->options([
                        'preparing' => 'Đang chuẩn bị',
                        'shipping'  => 'Đang giao',
                        'delivered' => 'Đã giao',
                        'cancelled' => 'Đã hủy',
                    ])
                    ->required(),
                Forms\Components\TextInput::make('tracking_number')
                    ->label('Mã vận đơn')
                    ->maxLength(255),
                Forms\Components\TextInput::make('shipping_fee')
                    ->label('Phí vận chuyển')
                    ->numeric()
                    ->default(0)
                    ->suffix('VND'),
                Forms\Components\DateTimePicker::make('shipped_at')
                    ->label('Thời gian giao hàng'),
                Forms\Components\DateTimePicker::make('delivered_at')
                    ->label('Thời gian nhận hàng'),
            ]),
        ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist->schema([
            Infolists\Components\Section::make('Thông tin vận chuyển')
                ->columns(2)
                ->schema([
                    Infolists\Components\TextEntry::make('invoice.invoice_number')
                        ->label('Mã hóa đơn')
                        ->copyable()
                        ->weight('bold')
                        ->url(fn($record) => InvoiceResource::getUrl('view', [
                            'record' => $record->invoice_id,
                        ])),
                    Infolists\Components\TextEntry::make('shipmentMethod.name')
                        ->label('Phương thức'),
                    Infolists\Components\TextEntry::make('status')
                        ->label('Trạng thái')
                        ->badge()
                        ->color(fn($state) => match($state) {
                            'preparing' => 'warning',
                            'shipping'  => 'info',
                            'delivered' => 'success',
                            'cancelled' => 'danger',
                            default     => 'gray',
                        })
                        ->formatStateUsing(fn($state) => match($state) {
                            'preparing' => 'Đang chuẩn bị',
                            'shipping'  => 'Đang giao',
                            'delivered' => 'Đã giao',
                            'cancelled' => 'Đã hủy',
                            default     => $state,
                        }),
                    Infolists\Components\TextEntry::make('tracking_number')
                        ->label('Mã vận đơn')
                        ->placeholder('Chưa có')
                        ->copyable()
                        ->url(fn($record) => $record->tracking_number
                            ? 'https://tracking.ghn.dev/?order_code=' . $record->tracking_number
                            : null
                        )
                        ->openUrlInNewTab()
                        ->color('primary'),
                    Infolists\Components\TextEntry::make('shipping_fee')
                        ->label('Phí vận chuyển')
                        ->money('VND'),
                    Infolists\Components\TextEntry::make('shipped_at')
                        ->label('Thời gian giao')
                        ->dateTime('d/m/Y H:i')
                        ->placeholder('Chưa giao'),
                    Infolists\Components\TextEntry::make('delivered_at')
                        ->label('Thời gian nhận')
                        ->dateTime('d/m/Y H:i')
                        ->placeholder('Chưa nhận'),
                    Infolists\Components\TextEntry::make('created_at')
                        ->label('Tạo lúc')
                        ->dateTime('d/m/Y H:i'),
                ]),

            Infolists\Components\Section::make('Thông tin người nhận')
                ->columns(2)
                ->schema([
                    Infolists\Components\TextEntry::make('invoice.receiver_name')
                        ->label('Họ tên'),
                    Infolists\Components\TextEntry::make('invoice.receiver_phone')
                        ->label('Số điện thoại'),
                    Infolists\Components\TextEntry::make('invoice.address_line')
                        ->label('Địa chỉ'),
                    Infolists\Components\TextEntry::make('invoice.ward')
                        ->label('Phường/Xã'),
                    Infolists\Components\TextEntry::make('invoice.province')
                        ->label('Tỉnh/Thành phố'),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('invoice.invoice_number')
                    ->label('Mã HĐ')
                    ->searchable()
                    ->copyable()
                    ->weight('bold')
                    ->url(fn($record) => InvoiceResource::getUrl('view', [
                        'record' => $record->invoice_id,
                    ])),
                Tables\Columns\TextColumn::make('invoice.receiver_name')
                    ->label('Người nhận')
                    ->searchable(),
                Tables\Columns\TextColumn::make('shipmentMethod.name')
                    ->label('Phương thức'),
                Tables\Columns\TextColumn::make('tracking_number')
                    ->label('Mã vận đơn')
                    ->placeholder('—')
                    ->copyable()
                    ->url(fn($record) => $record->tracking_number
                        ? 'https://tracking.ghn.dev/?order_code=' . $record->tracking_number
                        : null
                    )
                    ->openUrlInNewTab()
                    ->color('primary'),
                Tables\Columns\BadgeColumn::make('status')
                    ->label('Trạng thái')
                    ->colors([
                        'warning' => 'preparing',
                        'info'    => 'shipping',
                        'success' => 'delivered',
                        'danger'  => 'cancelled',
                    ])
                    ->formatStateUsing(fn($state) => match($state) {
                        'preparing' => 'Đang chuẩn bị',
                        'shipping'  => 'Đang giao',
                        'delivered' => 'Đã giao',
                        'cancelled' => 'Đã hủy',
                        default     => $state,
                    }),
                Tables\Columns\TextColumn::make('shipping_fee')
                    ->label('Phí ship')
                    ->money('VND'),
                Tables\Columns\TextColumn::make('shipped_at')
                    ->label('Giao lúc')
                    ->dateTime('d/m/Y H:i')
                    ->placeholder('—'),
                Tables\Columns\TextColumn::make('delivered_at')
                    ->label('Nhận lúc')
                    ->dateTime('d/m/Y H:i')
                    ->placeholder('—'),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Trạng thái')
                    ->options([
                        'preparing' => 'Đang chuẩn bị',
                        'shipping'  => 'Đang giao',
                        'delivered' => 'Đã giao',
                        'cancelled' => 'Đã hủy',
                    ]),
                Tables\Filters\SelectFilter::make('shipment_method_id')
                    ->label('Phương thức')
                    ->relationship('shipmentMethod', 'name'),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->label('Xem'),

                // ★ Tạo mã vận đơn — chỉ hiện khi đang chuẩn bị
                Tables\Actions\Action::make('generateTracking')
                    ->label('Tạo mã vận đơn')
                    ->icon('heroicon-o-truck')
                    ->color('primary')
                    ->visible(fn(Shipment $record) => $record->status === 'preparing')
                    ->form([
                        Forms\Components\Placeholder::make('guide')
                            ->label('')
                            ->content('Mã vận đơn được tự động sinh. Bạn có thể chỉnh sửa nếu muốn dùng mã thật từ GHN.'),
                        Forms\Components\TextInput::make('tracking_number')
                            ->label('Mã vận đơn')
                            ->default('GHN-' . strtoupper(Str::random(8)))
                            ->required()
                            ->helperText('Mã giả lập — thay bằng mã GHN thật khi production'),
                        Forms\Components\DateTimePicker::make('shipped_at')
                            ->label('Thời gian giao cho GHN')
                            ->default(now()),
                    ])
                    ->action(function (Shipment $record, array $data) {
                        $record->update([
                            'tracking_number' => $data['tracking_number'],
                            'shipped_at'      => $data['shipped_at'],
                            'status'          => 'shipping',
                        ]);

                        $record->invoice->update(['status' => 'shipped']);
                    })
                    ->successNotificationTitle('Đã tạo mã vận đơn!'),

                // ★ Xác nhận đã giao — chỉ hiện khi đang giao
                Tables\Actions\Action::make('confirmDelivered')
                    ->label('Xác nhận đã giao')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn(Shipment $record) => $record->status === 'shipping')
                    ->requiresConfirmation()
                    ->modalHeading('Xác nhận đã giao hàng?')
                    ->modalDescription('Thao tác này sẽ cập nhật trạng thái đơn hàng thành "Đã giao hàng".')
                    ->action(function (Shipment $record) {
                        $record->update([
                            'status'       => 'delivered',
                            'delivered_at' => now(),
                        ]);

                        $record->invoice->update(['status' => 'delivered']);
                    })
                    ->successNotificationTitle('Đã xác nhận giao hàng thành công!'),

                // ★ Hủy vận chuyển
                Tables\Actions\Action::make('cancelShipment')
                    ->label('Hủy')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->visible(fn(Shipment $record) => in_array($record->status, ['preparing', 'shipping']))
                    ->requiresConfirmation()
                    ->modalHeading('Hủy vận chuyển?')
                    ->modalDescription('Thao tác này sẽ hủy đơn vận chuyển và cập nhật hóa đơn.')
                    ->form([
                        Forms\Components\Textarea::make('cancel_reason')
                            ->label('Lý do hủy')
                            ->placeholder('Nhập lý do hủy...')
                            ->required(),
                    ])
                    ->action(function (Shipment $record, array $data) {
                        $record->update(['status' => 'cancelled']);
                        $record->invoice->update(['status' => 'cancelled']);
                    })
                    ->successNotificationTitle('Đã hủy vận chuyển!'),

                Tables\Actions\EditAction::make()
                    ->label('Sửa'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListShipments::route('/'),
            'create' => Pages\CreateShipment::route('/create'),
            'view'   => Pages\ViewShipment::route('/{record}'),
            'edit'   => Pages\EditShipment::route('/{record}/edit'),
        ];
    }
}