<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PurchaseOrder extends Model
{

    protected $fillable = [
        'supplier_id', 
        'warehouse_id',
        'po_number', 
        'total_amount', 
        'status',
        'approved_by'
    ];

    public function items(): HasMany 
    { 
        return $this->hasMany(PurchaseOrderItem::class); 
    }

    public function supplier(): BelongsTo 
    { 
        return $this->belongsTo(Supplier::class); 
    }

    public function warehouse(): BelongsTo 
    { 
        return $this->belongsTo(Warehouse::class); 
    }

    public function approver(): BelongsTo 
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function isApproved(): bool 
    {
        return $this->status === 'approved';
    }
}