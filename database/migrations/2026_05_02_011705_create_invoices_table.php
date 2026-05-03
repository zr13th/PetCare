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
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('invoice_number')->unique();
            
            // Thông tin người nhận (snapshot tại thời điểm đặt)
            $table->string('receiver_name');
            $table->string('receiver_phone');
            $table->string('address_line');
            $table->string('province');
            $table->string('district');
            $table->string('ward');

            // Tiền
            $table->decimal('subtotal', 12, 2);           // Tổng tiền hàng
            $table->decimal('shipping_fee', 12, 2)->default(0);
            $table->decimal('total_amount', 12, 2);       // subtotal + shipping_fee

            // Trạng thái
            $table->enum('status', [
                'pending',    // Chờ xác nhận
                'confirmed',  // Đã xác nhận
                'processing', // Đang xử lý
                'shipped',    // Đang giao
                'delivered',  // Đã giao
                'cancelled',  // Đã hủy
            ])->default('pending');

            $table->text('note')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};