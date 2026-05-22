<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tạo bảng transactions
     */
    public function up(): void
    {
        Schema::create('transactions', function (Blueprint $table) {

            $table->id();
            // Khóa chính BIGINT UNSIGNED AUTO_INCREMENT

            // Khóa ngoại user (giao dịch thuộc về user nào)
            $table->foreignId('user_id')
                  ->constrained('users')
                  ->cascadeOnDelete();

            // Khóa ngoại category (Food, Salary, Transport...)
            $table->foreignId('category_id')
                  ->constrained('categories')
                  ->cascadeOnDelete();

            // Số tiền giao dịch
            $table->decimal('amount', 15, 2);

            /*
            Loại giao dịch
            income  = thu nhập (ví dụ lương)
            expense = chi tiêu hoặc chuyển sang tiết kiệm
            */
            $table->enum('type', ['income', 'expense']);

            /*
            Cờ đánh dấu giao dịch tiết kiệm
            0 = giao dịch bình thường
            1 = tiền chuyển vào tiết kiệm
            */
            $table->boolean('is_saving')
                  ->default(false);

            // Mô tả giao dịch
            $table->string('description', 255)->nullable();

            // Ngày giao dịch
            $table->date('transaction_date');

            // Laravel tự tạo created_at và updated_at
            $table->timestamps();

            /*
            Index giúp truy vấn nhanh khi:
            - thống kê theo tháng
            - tính ngân sách
            - AI phân tích chi tiêu
            */
            $table->index(['user_id', 'transaction_date']);
        });
    }

    /**
     * Xóa bảng khi rollback
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};