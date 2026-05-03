<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class StockMovement extends Model
{
    protected $fillable = ['batch_id', 'warehouse_id', 'type', 'quantity', 'reference_type', 'reference_id'];

    public function batch(): BelongsTo { return $this->belongsTo(Batch::class); }
    public function warehouse(): BelongsTo { return $this->belongsTo(Warehouse::class); }
    
    public function reference(): MorphTo {
        return $this->morphTo();
    }
}