<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Shipment extends Model
{
    protected $fillable = ['order_id', 'carrier_id', 'status', 'shipped_at', 'delivered_at'];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function carrier()
    {
        return $this->belongsTo(Carrier::class);
    }

    public function items()
    {
        return $this->hasMany(ShipmentItem::class);
    }
}
