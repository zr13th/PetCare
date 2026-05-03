<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShipmentMethod extends Model
{
    protected $fillable = ['name', 'code', 'description', 'fee', 'estimated_days', 'is_active', 'sort_order'];

    protected $casts = ['is_active' => 'boolean', 'fee' => 'decimal:2'];

    public function scopeActive($query)
    {
        return $query->where('is_active', true)->orderBy('sort_order');
    }
}