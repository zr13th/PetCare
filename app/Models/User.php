<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | QUAN HỆ TRONG HỆ THỐNG PETCARE
    |--------------------------------------------------------------------------
    */

    // Hồ sơ khách hàng (1-1)
    public function profile()
    {
        return $this->hasOne(CustomerProfile::class);
    }

    // Địa chỉ khách hàng (1-n)
    public function addresses()
    {
        return $this->hasMany(Address::class);
    }

    // Thú cưng thuộc người dùng (1-n)
    public function pets()
    {
        return $this->hasMany(Pet::class, 'owner_id');
    }

    // Đơn hàng (1-n)
    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    // Lịch hẹn (1-n, dành cho khách hàng)
    public function appointments()
    {
        return $this->hasMany(Appointment::class, 'customer_id');
    }

    // Điểm thưởng (1-1)
    public function loyaltyPoints()
    {
        return $this->hasOne(LoyaltyPoint::class);
    }

    // Lịch sử điểm thưởng (1-n)
    public function loyaltyPointLogs()
    {
        return $this->hasMany(LoyaltyPointLog::class);
    }

    // Mã giảm giá đã sử dụng (1-n)
    public function couponRedemptions()
    {
        return $this->hasMany(CouponRedemption::class);
    }

    // Đánh giá sản phẩm/dịch vụ (1-n)
    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    // Thông báo (1-n)
    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }

    // Nhật ký hoạt động (1-n)
    public function activities()
    {
        return $this->hasMany(Activity::class);
    }
}
