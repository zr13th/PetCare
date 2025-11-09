<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MedicalRecord extends Model
{
    protected $fillable = ['pet_id', 'vet_id', 'appointment_id', 'diagnosis'];

    public function pet()
    {
        return $this->belongsTo(Pet::class);
    }

    public function vet()
    {
        return $this->belongsTo(Staff::class, 'vet_id');
    }

    public function appointment()
    {
        return $this->belongsTo(Appointment::class);
    }

    public function treatments()
    {
        return $this->hasMany(Treatment::class);
    }

    public function prescriptions()
    {
        return $this->hasMany(Prescription::class);
    }
}
