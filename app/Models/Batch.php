<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Batch extends Model
{
    protected $fillable = [
        'variant_id',
        'batch_number',
        'manufacture_date',
        'expiry_date',
        'cost_price',
    ];

    protected $casts = [
        'expiry_date' => 'date',
        'manufacture_date' => 'date',
        'cost_price' => 'decimal:2',
    ];
    
    protected static function booted()
    {
        static::created(function ($batch) {
            $variant = $batch->variant;

            if ($variant) {
                $markup = 1.2; 
                $newPrice = $batch->cost_price * $markup;

                $variant->update([
                    'price' => $newPrice,
                    'compare_price' => $newPrice * 1.2,
                ]);
            }
        });
    }

    public function variant(): BelongsTo {
        return $this->belongsTo(ProductVariant::class, 'variant_id');
    }

    public function inventories(): HasMany {
        return $this->hasMany(Inventory::class, 'batch_id');
    }

    public function getFullNameAttribute(): string
    {
        return "{$this->variant->product->name} (Lô: {$this->batch_number})";
    }
}