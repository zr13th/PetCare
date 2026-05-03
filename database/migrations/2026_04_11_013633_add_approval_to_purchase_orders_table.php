<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('purchase_orders', function (Blueprint $table) {
            // Thay đổi enum cũ để thêm trạng thái 'approved'
            // Lưu ý: Nếu dùng MySQL, bạn cần cài đặt doctrine/dbal hoặc viết raw SQL
            $table->enum('status', ['pending', 'approved', 'received', 'cancelled'])
                  ->default('pending')
                  ->change();

            $table->foreignId('approved_by')
                  ->nullable()
                  ->constrained('users')
                  ->nullOnDelete();

            $table->timestamp('approved_at')
                  ->nullable()
                  ->after('approved_by');
            
            $table->foreignId('warehouse_id')
                  ->nullable() 
                  ->constrained();
        });
    }

    public function down(): void
    {
        Schema::table('purchase_orders', function (Blueprint $table) {
            $table->dropForeign(['approved_by']);
            $table->dropForeign(['warehouse_id']);
            $table->dropColumn(['approved_by', 'approved_at', 'warehouse_id']);
            $table->enum('status', ['pending', 'received', 'cancelled'])->default('pending')->change();
        });
    }
};