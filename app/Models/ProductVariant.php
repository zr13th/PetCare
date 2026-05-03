<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

use Gloudemans\Shoppingcart\Contracts\Buyable;

class ProductVariant extends Model implements Buyable
{
    protected $fillable = ['product_id', 'sku', 'price', 'compare_price', 'stock_alert', 'is_active'];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function variantAttributeValues()
    {
        return $this->hasMany(VariantAttributeValue::class, 'variant_id');
    }

    public function attributeValues()
    {
        return $this->belongsToMany(
            AttributeValue::class,
            'variant_attribute_values',     // bảng pivot
            'variant_id',                   // FK của model này
            'attribute_value_id'            // FK của model đích
        )->withPivot('attribute_id');
    }

    public function inventories(): HasManyThrough
    {
        return $this->hasManyThrough(
            Inventory::class,
            Batch::class,
            'variant_id',
            'batch_id',
            'id',
            'id'
        );
    }

    public function getBuyableIdentifier($options = null)
    {
        return $this->id;
    }

    public function getBuyableDescription($options = null)
    {
        $attrs = $this->attributeValues
            ->map(fn($av) => $av->attributeValue->value ?? $av->value)
            ->join(' / ');
        return $this->product->name . ($attrs ? " ({$attrs})" : '');
    }

    public function getBuyablePrice($options = null)
    {
        return $this->price;
    }

    public function getBuyableWeight($options = null)
    {
        return 0;
    }
}