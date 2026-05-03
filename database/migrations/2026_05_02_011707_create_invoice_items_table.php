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
        Schema::create('invoice_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('invoice_id')->constrained()->cascadeOnDelete();
            $table->foreignId('variant_id')->constrained('product_variants');
            $table->foreignId('batch_id')->nullable()->constrained();

            // Snapshot tên + giá tại thời điểm đặt (tránh bị thay đổi sau này)
            $table->string('product_name');
            $table->string('variant_sku');
            $table->integer('quantity');
            $table->decimal('price', 12, 2);      // Giá tại thời điểm mua
            $table->decimal('subtotal', 12, 2);   // price * quantity
            $table->timestamps();
        });
    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoice_items');
    }
};