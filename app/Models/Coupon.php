<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Coupon extends Model
{
    protected $fillable = ['code', 'description', 'discount_type', 'discount_value', 'expiry_date'];

    public function redemptions()
    {
        return $this->hasMany(CouponRedemption::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }
}
