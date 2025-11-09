<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Vaccination extends Model
{
    protected $fillable = ['pet_id', 'vaccine_name', 'date_given', 'next_due'];

    public function pet()
    {
        return $this->belongsTo(Pet::class);
    }
}
