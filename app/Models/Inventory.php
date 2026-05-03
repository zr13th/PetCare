<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Inventory extends Model
{
    protected $fillable = ['warehouse_id', 'batch_id', 'quantity'];

    public function warehouse(): BelongsTo {
        return $this->belongsTo(Warehouse::class);
    }

    public function batch(): BelongsTo {
        return $this->belongsTo(Batch::class);
    }

}