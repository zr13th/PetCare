<?php
// app/Filament/Resources/PaymentResource.php

namespace App\Filament\Resources;

use App\Filament\Resources\PaymentResource\Pages;
use App\Models\Payment;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Infolists;
use Filament\Infolists\Infolist;

class PaymentResource extends Resource
{
    protected static ?string $model = Payment::class;
    protected static ?string $navigationIcon = 'heroicon-o-banknotes';
    protected static ?string $navigationLabel = 'Thanh toán';
    protected static ?string $modelLabel = 'Thanh toán';
    protected static ?string $pluralModelLabel = 'Thanh toán';
    protected static ?string $navigationGroup = 'Bán hàng';
    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Select::make('status')
                ->label('Trạng thái')
                ->options([
                    'pending'   => 'Chờ thanh toán',
                    'completed' => 'Đã thanh toán',
                    'failed'    => 'Thất bại',
                    'refunded'  => 'Đã hoàn tiền',
                ])
                ->required(),
            Forms\Components\TextInput::make('transaction_id')
                ->label('Mã giao dịch')
                ->maxLength(255),
            Forms\Components\DateTimePicker::make('paid_at')
                ->label('Thời gian thanh toán'),
            Forms\Components\Textarea::make('note')
                ->label('Ghi chú')
                ->rows(3),
        ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist->schema([
            Infolists\Components\Section::make('Thông tin thanh toán')
                ->columns(3)
                ->schema([
                    Infolists\Components\TextEntry::make('invoice.invoice_number')
                        ->label('Mã hóa đơn')
                        ->copyable()
                        ->weight('bold')
                        ->url(fn($record) => InvoiceResource::getUrl('view', ['record' => $record->invoice_id])),
                    Infolists\Components\TextEntry::make('status')
                        ->label('Trạng thái')
                        ->badge()
                        ->color(fn($state) => match($state) {
                            'completed' => 'success',
                            'pending'   => 'warning',
                            'failed'    => 'danger',
                            'refunded'  => 'info',
                            default     => 'gray',
                        })
                        ->formatStateUsing(fn($state) => match($state) {
                            'completed' => 'Đã thanh toán',
                            'pending'   => 'Chờ thanh toán',
                            'failed'    => 'Thất bại',
                            'refunded'  => 'Đã hoàn tiền',
                            default     => $state,
                        }),
                    Infolists\Components\TextEntry::make('created_at')
                        ->label('Ngày tạo')
                        ->dateTime('d/m/Y H:i'),
                ]),

            Infolists\Components\Section::make('Chi tiết giao dịch')
                ->columns(2)
                ->schema([
                    Infolists\Components\TextEntry::make('paymentMethod.name')
                        ->label('Phương thức thanh toán'),
                    Infolists\Components\TextEntry::make('transaction_id')
                        ->label('Mã giao dịch')
                        ->placeholder('—')
                        ->copyable(),
                    Infolists\Components\TextEntry::make('amount')
                        ->label('Số tiền')
                        ->money('VND')
                        ->weight('bold')
                        ->color('warning'),
                    Infolists\Components\TextEntry::make('paid_at')
                        ->label('Thời gian thanh toán')
                        ->dateTime('d/m/Y H:i')
                        ->placeholder('—'),
                    Infolists\Components\TextEntry::make('note')
                        ->label('Ghi chú')
                        ->placeholder('Không có')
                        ->columnSpanFull(),
                ]),

            Infolists\Components\Section::make('Thông tin khách hàng')
                ->columns(2)
                ->schema([
                    Infolists\Components\TextEntry::make('invoice.user.name')
                        ->label('Khách hàng'),
                    Infolists\Components\TextEntry::make('invoice.receiver_name')
                        ->label('Người nhận'),
                    Infolists\Components\TextEntry::make('invoice.receiver_phone')
                        ->label('Số điện thoại'),
                    Infolists\Components\TextEntry::make('invoice.total_amount')
                        ->label('Tổng đơn hàng')
                        ->money('VND'),
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
                    ->url(fn($record) => InvoiceResource::getUrl('view', ['record' => $record->invoice_id])),
                Tables\Columns\TextColumn::make('invoice.user.name')
                    ->label('Khách hàng')
                    ->searchable(),
                Tables\Columns\TextColumn::make('paymentMethod.name')
                    ->label('Phương thức'),
                Tables\Columns\TextColumn::make('transaction_id')
                    ->label('Mã giao dịch')
                    ->placeholder('—')
                    ->copyable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('amount')
                    ->label('Số tiền')
                    ->money('VND')
                    ->sortable(),
                Tables\Columns\BadgeColumn::make('status')
                    ->label('Trạng thái')
                    ->colors([
                        'success' => 'completed',
                        'warning' => 'pending',
                        'danger'  => 'failed',
                        'info'    => 'refunded',
                    ])
                    ->formatStateUsing(fn($state) => match($state) {
                        'completed' => 'Đã thanh toán',
                        'pending'   => 'Chờ thanh toán',
                        'failed'    => 'Thất bại',
                        'refunded'  => 'Đã hoàn tiền',
                        default     => $state,
                    }),
                Tables\Columns\TextColumn::make('paid_at')
                    ->label('Thanh toán lúc')
                    ->dateTime('d/m/Y H:i')
                    ->placeholder('—')
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Ngày tạo')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Trạng thái')
                    ->options([
                        'pending'   => 'Chờ thanh toán',
                        'completed' => 'Đã thanh toán',
                        'failed'    => 'Thất bại',
                        'refunded'  => 'Đã hoàn tiền',
                    ]),
                Tables\Filters\SelectFilter::make('payment_method_id')
                    ->label('Phương thức')
                    ->relationship('paymentMethod', 'name'),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()->label('Xem'),
                Tables\Actions\Action::make('updateStatus')
                    ->label('Cập nhật')
                    ->icon('heroicon-o-arrow-path')
                    ->color('warning')
                    ->form([
                        Forms\Components\Select::make('status')
                            ->label('Trạng thái')
                            ->options([
                                'pending'   => 'Chờ thanh toán',
                                'completed' => 'Đã thanh toán',
                                'failed'    => 'Thất bại',
                                'refunded'  => 'Đã hoàn tiền',
                            ])
                            ->required(),
                        Forms\Components\TextInput::make('transaction_id')
                            ->label('Mã giao dịch'),
                        Forms\Components\DateTimePicker::make('paid_at')
                            ->label('Thời gian thanh toán'),
                        Forms\Components\Textarea::make('note')
                            ->label('Ghi chú')
                            ->rows(2),
                    ])
                    ->fillForm(fn(Payment $record) => [
                        'status'         => $record->status,
                        'transaction_id' => $record->transaction_id,
                        'paid_at'        => $record->paid_at,
                        'note'           => $record->note,
                    ])
                    ->action(function (Payment $record, array $data) {
                        $record->update($data);

                        // Sync trạng thái invoice nếu payment completed
                        if ($data['status'] === 'completed') {
                            $record->invoice->update(['status' => 'confirmed']);
                        }
                    })
                    ->successNotificationTitle('Đã cập nhật thanh toán!'),
            ])
            ->bulkActions([
                Tables\Actions\BulkAction::make('markCompleted')
                    ->label('Đánh dấu đã thanh toán')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->action(function ($records) {
                        $records->each(function ($record) {
                            $record->update([
                                'status'  => 'completed',
                                'paid_at' => now(),
                            ]);
                            $record->invoice->update(['status' => 'confirmed']);
                        });
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
            'index' => Pages\ListPayments::route('/'),
            'view'  => Pages\ViewPayment::route('/{record}'),
            'edit'  => Pages\EditPayment::route('/{record}/edit'),
        ];
    }
}