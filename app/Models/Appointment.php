<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Appointment extends Model
{
    protected $fillable = ['customer_id', 'pet_id', 'room_id', 'appointment_time', 'status'];

    public function customer()
    {
        return $this->belongsTo(User::class, 'customer_id');
    }

    public function pet()
    {
        return $this->belongsTo(Pet::class);
    }

    public function room()
    {
        return $this->belongsTo(Room::class);
    }

    public function services()
    {
        return $this->hasMany(AppointmentService::class);
    }

    public function logs()
    {
        return $this->hasMany(AppointmentLog::class);
    }
}
