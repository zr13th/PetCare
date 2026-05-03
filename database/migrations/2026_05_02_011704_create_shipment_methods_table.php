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
        Schema::create('shipment_methods', function (Blueprint $table) {
            $table->id();
            $table->string('name');                        // Giao hàng nhanh, Tự đến lấy...
            $table->string('code')->unique();              // fast_delivery, self_pickup...
            $table->text('description')->nullable();
            $table->decimal('fee', 12, 2)->default(0);    // Phí ship flat rate
            $table->integer('estimated_days')->default(3); // Số ngày giao dự kiến
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
        Schema::dropIfExists('shipment_methods');
    }
};