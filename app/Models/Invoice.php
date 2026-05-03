<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    protected $fillable = [
        'user_id', 'invoice_number',
        'receiver_name', 'receiver_phone', 'address_line', 'province', 'district', 'ward',
        'subtotal', 'shipping_fee', 'total_amount', 'status', 'note',
    ];

    public function user()       { return $this->belongsTo(User::class); }
    public function items()      { return $this->hasMany(InvoiceItem::class); }
    public function payment()    { return $this->hasOne(Payment::class); }
    public function shipment()   { return $this->hasOne(Shipment::class); }

    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            'pending'    => 'Chờ xác nhận',
            'confirmed'  => 'Đã xác nhận',
            'processing' => 'Đang xử lý',
            'shipped'    => 'Đang giao hàng',
            'delivered'  => 'Đã giao hàng',
            'cancelled'  => 'Đã hủy',
            default      => $this->status,
        };
    }
}