<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Shipment extends Model
{
    protected $fillable = [
        'invoice_id', 'shipment_method_id', 'tracking_number',
        'shipping_fee', 'status', 'shipped_at', 'delivered_at',
    ];

    protected $casts = ['shipped_at' => 'datetime', 'delivered_at' => 'datetime'];

    public function invoice()        { return $this->belongsTo(Invoice::class); }
    public function shipmentMethod() { return $this->belongsTo(ShipmentMethod::class); }
}