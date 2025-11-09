<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Staff extends Model
{
    protected $fillable = ['user_id', 'name', 'role', 'phone', 'email'];

    public function appointments()
    {
        return $this->hasMany(AppointmentService::class);
    }
}
