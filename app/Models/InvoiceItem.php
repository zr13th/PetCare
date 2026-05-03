<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class InvoiceItem extends Model
{
    protected $fillable = [
        'invoice_id', 'variant_id', 'batch_id',
        'product_name', 'variant_sku', 'quantity', 'price', 'subtotal',
    ];

    public function invoice() { return $this->belongsTo(Invoice::class); }
    public function variant() { return $this->belongsTo(ProductVariant::class, 'variant_id'); }
}