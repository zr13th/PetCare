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
        Schema::create('shipments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('invoice_id')->constrained()->cascadeOnDelete();
            $table->foreignId('shipment_method_id')->constrained();
            $table->string('tracking_number')->nullable();
            $table->decimal('shipping_fee', 12, 2)->default(0);
            $table->enum('status', [
                'preparing',  // Đang chuẩn bị
                'shipping',   // Đang giao
                'delivered',  // Đã giao
                'cancelled',  // Đã hủy
            ])->default('preparing');
            $table->timestamp('shipped_at')->nullable();
            $table->timestamp('delivered_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shipments');
    }
};