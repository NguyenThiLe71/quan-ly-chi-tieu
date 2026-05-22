<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Chạy migration: tạo bảng monthly_statistics
     */
    public function up(): void
    {
        Schema::create('monthly_statistics', function (Blueprint $table) {

            $table->id();
            // Khóa chính BIGINT auto increment

            $table->foreignId('user_id')
                  ->constrained('users')
                  ->cascadeOnDelete();
            /*
            Khóa ngoại liên kết tới bảng users
            Nếu user bị xóa → thống kê cũng bị xóa
            */

            $table->tinyInteger('month');
            /*
            Tháng thống kê (1 → 12)
            Việc check 1-12 nên validate trong Laravel
            */

            $table->smallInteger('year');
            // Năm thống kê (ví dụ: 2026)

            $table->decimal('total_income', 15, 2)
                  ->default(0);
            // Tổng thu nhập trong tháng

            $table->decimal('total_expense', 15, 2)
                  ->default(0);
            // Tổng chi tiêu trong tháng

            $table->decimal('saving_rate', 5, 2)
                  ->default(0);
            /*
            Tỷ lệ tiết kiệm (%)
            Công thức:
            (income - expense) / income * 100
            */

            $table->timestamp('created_at')
                  ->useCurrent();
            // Thời điểm tạo thống kê

            /*
            UNIQUE:
            mỗi user chỉ có 1 thống kê cho 1 tháng
            */
            $table->unique(['user_id','month','year'], 'uq_monthly');

            // Index giúp truy vấn nhanh
            $table->index('user_id');
        });
    }

    /**
     * Rollback migration
     */
    public function down(): void
    {
        Schema::dropIfExists('monthly_statistics');
    }
};