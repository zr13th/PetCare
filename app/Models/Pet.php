<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pet extends Model
{
    protected $fillable = ['owner_id', 'species_id', 'breed_id', 'name', 'gender', 'birth_date', 'weight', 'note'];

    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function species()
    {
        return $this->belongsTo(Species::class);
    }

    public function breed()
    {
        return $this->belongsTo(Breed::class);
    }

    public function medicalRecords()
    {
        return $this->hasMany(MedicalRecord::class);
    }
}
