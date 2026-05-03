<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PurchaseOrderItem extends Model
{
    protected $fillable = ['purchase_order_id', 'warehouse_id', 'variant_id', 'quantity', 'cost_price', 'expiry_date'];

    public function variant(): BelongsTo { return $this->belongsTo(ProductVariant::class); }
    public function warehouse(): BelongsTo { return $this->belongsTo(Warehouse::class); }
}