<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Chạy migration: tạo bảng transaction_logs
     */
    public function up(): void
    {
        Schema::create('transaction_logs', function (Blueprint $table) {

            $table->id();
            // Khóa chính BIGINT auto increment

            $table->foreignId('transaction_id')
                  ->constrained('transactions')
                  ->cascadeOnDelete();
            /*
            Khóa ngoại liên kết tới bảng transactions
            Xác định giao dịch nào bị thay đổi

            Nếu transaction bị xóa → log cũng bị xóa
            */

            $table->enum('action', [
                'created',
                'updated',
                'deleted'
            ]);
            /*
            Hành động xảy ra với giao dịch

            created = tạo mới
            updated = chỉnh sửa
            deleted = xóa
            */

            $table->decimal('old_amount', 15, 2)->nullable();
            /*
            Số tiền trước khi thay đổi
            NULL nếu là giao dịch mới
            */

            $table->decimal('new_amount', 15, 2)->nullable();
            /*
            Số tiền sau khi thay đổi
            NULL nếu giao dịch bị xóa
            */

            $table->timestamp('changed_at')
                  ->useCurrent();
            // Thời điểm thay đổi giao dịch
        });
    }

    /**
     * Rollback migration
     */
    public function down(): void
    {
        Schema::dropIfExists('transaction_logs');
    }
};