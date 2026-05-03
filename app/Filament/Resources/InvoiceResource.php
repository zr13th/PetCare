<?php
// app/Filament/Resources/InvoiceResource.php

namespace App\Filament\Resources;

use App\Filament\Resources\InvoiceResource\Pages;
use App\Models\Invoice;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Infolists;
use Filament\Infolists\Infolist;

class InvoiceResource extends Resource
{
    protected static ?string $model = Invoice::class;
    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static ?string $navigationLabel = 'Hóa đơn';
    protected static ?string $modelLabel = 'Hóa đơn';
    protected static ?string $pluralModelLabel = 'Hóa đơn';
    protected static ?string $navigationGroup = 'Bán hàng';
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Select::make('status')
                ->label('Trạng thái')
                ->options([
                    'pending'    => 'Chờ xác nhận',
                    'confirmed'  => 'Đã xác nhận',
                    'processing' => 'Đang xử lý',
                    'shipped'    => 'Đang giao hàng',
                    'delivered'  => 'Đã giao hàng',
                    'cancelled'  => 'Đã hủy',
                ])
                ->required(),
        ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist->schema([
            Infolists\Components\Section::make('Thông tin đơn hàng')
                ->columns(3)
                ->schema([
                    Infolists\Components\TextEntry::make('invoice_number')
                        ->label('Mã hóa đơn')
                        ->copyable()
                        ->weight('bold'),
                    Infolists\Components\TextEntry::make('status')
                        ->label('Trạng thái')
                        ->badge()
                        ->color(fn($state) => match($state) {
                            'pending'    => 'warning',
                            'confirmed'  => 'info',
                            'processing' => 'info',
                            'shipped'    => 'primary',
                            'delivered'  => 'success',
                            'cancelled'  => 'danger',
                            default      => 'gray',
                        })
                        ->formatStateUsing(fn($state) => match($state) {
                            'pending'    => 'Chờ xác nhận',
                            'confirmed'  => 'Đã xác nhận',
                            'processing' => 'Đang xử lý',
                            'shipped'    => 'Đang giao hàng',
                            'delivered'  => 'Đã giao hàng',
                            'cancelled'  => 'Đã hủy',
                            default      => $state,
                        }),
                    Infolists\Components\TextEntry::make('created_at')
                        ->label('Ngày đặt')
                        ->dateTime('d/m/Y H:i'),
                ]),

            Infolists\Components\Section::make('Thông tin người nhận')
                ->columns(2)
                ->schema([
                    Infolists\Components\TextEntry::make('receiver_name')->label('Họ tên'),
                    Infolists\Components\TextEntry::make('receiver_phone')->label('Số điện thoại'),
                    Infolists\Components\TextEntry::make('address_line')->label('Địa chỉ'),
                    Infolists\Components\TextEntry::make('ward')->label('Phường/Xã'),
                    Infolists\Components\TextEntry::make('province')->label('Tỉnh/Thành phố'),
                    Infolists\Components\TextEntry::make('note')->label('Ghi chú')->placeholder('Không có'),
                ]),

            Infolists\Components\Section::make('Thanh toán & Vận chuyển')
                ->columns(2)
                ->schema([
                    Infolists\Components\TextEntry::make('payment.paymentMethod.name')
                        ->label('Phương thức thanh toán'),
                    Infolists\Components\TextEntry::make('payment.status')
                        ->label('Trạng thái thanh toán')
                        ->badge()
                        ->color(fn($state) => match($state) {
                            'completed' => 'success',
                            'pending'   => 'warning',
                            'failed'    => 'danger',
                            default     => 'gray',
                        })
                        ->formatStateUsing(fn($state) => match($state) {
                            'completed' => 'Đã thanh toán',
                            'pending'   => 'Chờ thanh toán',
                            'failed'    => 'Thất bại',
                            default     => $state,
                        }),
                    Infolists\Components\TextEntry::make('shipment.shipmentMethod.name')
                        ->label('Phương thức vận chuyển'),
                    Infolists\Components\TextEntry::make('shipment.status')
                        ->label('Trạng thái vận chuyển')
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
                    Infolists\Components\TextEntry::make('payment.transaction_id')
                        ->label('Mã giao dịch')
                        ->placeholder('—')
                        ->copyable(),
                    Infolists\Components\TextEntry::make('shipment.tracking_number')
                        ->label('Mã tracking')
                        ->placeholder('—')
                        ->copyable(),
                ]),

            Infolists\Components\Section::make('Sản phẩm')
                ->schema([
                    Infolists\Components\RepeatableEntry::make('items')
                        ->label('')
                        ->schema([
                            Infolists\Components\TextEntry::make('product_name')->label('Sản phẩm'),
                            Infolists\Components\TextEntry::make('variant_sku')->label('SKU'),
                            Infolists\Components\TextEntry::make('quantity')->label('SL'),
                            Infolists\Components\TextEntry::make('price')
                                ->label('Đơn giá')
                                ->money('VND'),
                            Infolists\Components\TextEntry::make('subtotal')
                                ->label('Thành tiền')
                                ->money('VND'),
                        ])
                        ->columns(5),
                ]),

            Infolists\Components\Section::make('Tổng tiền')
                ->columns(3)
                ->schema([
                    Infolists\Components\TextEntry::make('subtotal')
                        ->label('Tạm tính')
                        ->money('VND'),
                    Infolists\Components\TextEntry::make('shipping_fee')
                        ->label('Phí vận chuyển')
                        ->money('VND'),
                    Infolists\Components\TextEntry::make('total_amount')
                        ->label('Tổng cộng')
                        ->money('VND')
                        ->weight('bold')
                        ->color('warning'),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('invoice_number')
                    ->label('Mã HĐ')
                    ->searchable()
                    ->copyable()
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Khách hàng')
                    ->searchable(),
                Tables\Columns\TextColumn::make('receiver_name')
                    ->label('Người nhận')
                    ->searchable(),
                Tables\Columns\TextColumn::make('receiver_phone')
                    ->label('SĐT'),
                Tables\Columns\BadgeColumn::make('status')
                    ->label('Trạng thái')
                    ->colors([
                        'warning' => 'pending',
                        'info'    => ['confirmed', 'processing'],
                        'primary' => 'shipped',
                        'success' => 'delivered',
                        'danger'  => 'cancelled',
                    ])
                    ->formatStateUsing(fn($state) => match($state) {
                        'pending'    => 'Chờ xác nhận',
                        'confirmed'  => 'Đã xác nhận',
                        'processing' => 'Đang xử lý',
                        'shipped'    => 'Đang giao',
                        'delivered'  => 'Đã giao',
                        'cancelled'  => 'Đã hủy',
                        default      => $state,
                    }),
                Tables\Columns\TextColumn::make('total_amount')
                    ->label('Tổng tiền')
                    ->money('VND')
                    ->sortable(),
                Tables\Columns\TextColumn::make('payment.status')
                    ->label('Thanh toán')
                    ->badge()
                    ->color(fn($state) => match($state) {
                        'completed' => 'success',
                        'pending'   => 'warning',
                        'failed'    => 'danger',
                        default     => 'gray',
                    })
                    ->formatStateUsing(fn($state) => match($state) {
                        'completed' => 'Đã TT',
                        'pending'   => 'Chờ TT',
                        'failed'    => 'Thất bại',
                        default     => $state,
                    }),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Ngày đặt')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Trạng thái')
                    ->options([
                        'pending'    => 'Chờ xác nhận',
                        'confirmed'  => 'Đã xác nhận',
                        'processing' => 'Đang xử lý',
                        'shipped'    => 'Đang giao hàng',
                        'delivered'  => 'Đã giao hàng',
                        'cancelled'  => 'Đã hủy',
                    ]),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()->label('Xem'),
                Tables\Actions\Action::make('updateStatus')
                    ->label('Đổi trạng thái')
                    ->icon('heroicon-o-arrow-path')
                    ->color('warning')
                    ->form([
                        Forms\Components\Select::make('status')
                            ->label('Trạng thái mới')
                            ->options([
                                'pending'    => 'Chờ xác nhận',
                                'confirmed'  => 'Đã xác nhận',
                                'processing' => 'Đang xử lý',
                                'shipped'    => 'Đang giao hàng',
                                'delivered'  => 'Đã giao hàng',
                                'cancelled'  => 'Đã hủy',
                            ])
                            ->required(),
                    ])
                    ->action(function (Invoice $record, array $data) {
                        $record->update(['status' => $data['status']]);
                    })
                    ->successNotificationTitle('Đã cập nhật trạng thái!'),
            ])
            ->bulkActions([
                Tables\Actions\BulkAction::make('updateStatus')
                    ->label('Đổi trạng thái hàng loạt')
                    ->icon('heroicon-o-arrow-path')
                    ->form([
                        Forms\Components\Select::make('status')
                            ->label('Trạng thái mới')
                            ->options([
                                'confirmed'  => 'Đã xác nhận',
                                'processing' => 'Đang xử lý',
                                'shipped'    => 'Đang giao hàng',
                                'delivered'  => 'Đã giao hàng',
                                'cancelled'  => 'Đã hủy',
                            ])
                            ->required(),
                    ])
                    ->action(function ($records, array $data) {
                        $records->each->update(['status' => $data['status']]);
                    }),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListInvoices::route('/'),
            'view'  => Pages\ViewInvoice::route('/{record}'),
            'edit'  => Pages\EditInvoice::route('/{record}/edit'),
        ];
    }
}