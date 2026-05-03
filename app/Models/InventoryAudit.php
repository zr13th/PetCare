<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InventoryAudit extends Model
{
    protected $fillable = ['warehouse_id', 'audit_number', 'notes', 'status', 'created_by'];

    public function items() { return $this->hasMany(InventoryAuditItem::class); }
    public function warehouse() { return $this->belongsTo(Warehouse::class); }
    public function creator() { return $this->belongsTo(User::class, 'created_by'); }
}