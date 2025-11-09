<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AppointmentLog extends Model
{
    protected $fillable = ['appointment_id', 'note'];

    public function appointment()
    {
        return $this->belongsTo(Appointment::class);
    }
}
