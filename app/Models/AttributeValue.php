<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BeLongsTo;

class AttributeValue extends Model
{
    protected $fillable = ['attribute_id', 'value'];

    public function attribute() :BeLongsTo
    {
        return $this->belongsTo(Attribute::class);
    }
}