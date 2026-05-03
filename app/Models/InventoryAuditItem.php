<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InventoryAuditItem extends Model
{
    protected $fillable = [
        'inventory_audit_id', 
        'batch_id', 
        'system_quantity', 
        'actual_quantity', 
        'difference'
    ];

    public function batch() {
        return $this->belongsTo(Batch::class);
    }
}