<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('payment_methods', function (Blueprint $table) {
            $table->id();
            $table->string('name');                    // Tiền mặt, Chuyển khoản...
            $table->string('code')->unique();          // cod, bank_transfer, vnpay...
            $table->string('icon')->nullable();        // icon class hoặc url
            $table->text('description')->nullable();   // Mô tả hiển thị cho user
            $table->decimal('fee', 12, 2)->default(0); // Phí thanh toán nếu có
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_methods');
    }
};