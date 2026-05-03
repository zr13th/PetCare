<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PaymentMethodSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $methods = [
            [
                'name'        => 'Thanh toán khi nhận hàng (COD)',
                'code'        => 'cod',
                'description' => 'Thanh toán bằng tiền mặt khi nhận hàng',
                'fee'         => 0,
                'sort_order'  => 1,
                'is_active'   => true,
            ],
            [
                'name'        => 'VNPay',
                'code'        => 'vnpay',
                'description' => 'Thanh toán qua cổng VNPay (ATM, QR, thẻ tín dụng)',
                'fee'         => 0,
                'sort_order'  => 2,
                'is_active'   => true,
            ],
            [
                'name'        => 'Sandbox Payment (Test)',
                'code'        => 'sandbox',
                'description' => 'Giả lập thanh toán online — chỉ dùng để test',
                'fee'         => 0,
                'sort_order'  => 99,
                'is_active'   => true,
            ],
        ];

        foreach ($methods as $method) {
            \App\Models\PaymentMethod::updateOrCreate(['code' => $method['code']], $method);
        }
    }
}