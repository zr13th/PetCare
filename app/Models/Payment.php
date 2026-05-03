<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $fillable = [
        'invoice_id', 'payment_method_id', 'amount',
        'transaction_id', 'status', 'paid_at', 'meta',
    ];

    protected $casts = ['paid_at' => 'datetime', 'meta' => 'array'];

    public function invoice()       { return $this->belongsTo(Invoice::class); }
    public function paymentMethod() { return $this->belongsTo(PaymentMethod::class); }
}