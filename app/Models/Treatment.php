<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Treatment extends Model
{
    protected $fillable = ['medical_record_id', 'treatment_name', 'details'];

    public function medicalRecord()
    {
        return $this->belongsTo(MedicalRecord::class);
    }
}
