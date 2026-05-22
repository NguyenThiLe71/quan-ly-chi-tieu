<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tạo bảng budgets
     */
    public function up(): void
    {
        Schema::create('budgets', function (Blueprint $table) {

            $table->id();
            // Khóa chính BIGINT auto increment

            $table->foreignId('user_id')
                  ->constrained('users')
                  ->cascadeOnDelete();
            // User sở hữu ngân sách

            $table->foreignId('category_id')
                  ->constrained('categories')
                  ->cascadeOnDelete();
            // Danh mục áp dụng ngân sách (Food, Shopping...)

            $table->decimal('amount_limit', 15, 2);
            // Số tiền giới hạn chi tiêu

            $table->tinyInteger('month');
            // Tháng áp dụng ngân sách (1-12)

            $table->smallInteger('year');
            // Năm áp dụng (ví dụ 2026)

            $table->timestamps();
            // created_at + updated_at

            /*
            UNIQUE:
            mỗi user chỉ có 1 budget cho 1 category trong 1 tháng
            */
            $table->unique(
                ['user_id','category_id','month','year'],
                'uq_budget'
            );

            /*
            Index giúp truy vấn nhanh khi kiểm tra
            ngân sách của user trong tháng
            */
            $table->index(['user_id','month','year']);
        });
    }

    /**
     * Xóa bảng khi rollback
     */
    public function down(): void
    {
        Schema::dropIfExists('budgets');
    }
};