<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ShipmentMethodSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $methods = [
            [
                'name'           => 'Giao hàng nhanh',
                'code'           => 'fast_delivery',
                'description'    => 'Giao hàng trong 2-3 ngày làm việc',
                'fee'            => 30000,
                'estimated_days' => 3,
                'sort_order'     => 1,
            ],
            [
                'name'           => 'Tự đến lấy',
                'code'           => 'self_pickup',
                'description'    => 'Đến lấy tại cửa hàng: 123 Nguyễn Văn A, Q.1, TP.HCM',
                'fee'            => 0,
                'estimated_days' => 0,
                'sort_order'     => 2,
            ],
        ];

        foreach ($methods as $method) {
            \App\Models\ShipmentMethod::updateOrCreate(['code' => $method['code']], $method);
        }
    }
}