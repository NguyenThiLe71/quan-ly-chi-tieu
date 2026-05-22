<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tạo bảng saving_goals
     */
    public function up(): void
    {
        Schema::create('saving_goals', function (Blueprint $table) {

            $table->id();
            // Khóa chính BIGINT auto increment

            $table->foreignId('user_id')
                  ->constrained('users')
                  ->cascadeOnDelete();
            // User sở hữu mục tiêu tiết kiệm

            $table->string('name',150);
            // Tên mục tiêu (Mua laptop, Du lịch...)

            $table->decimal('target_amount',15,2);
            // Số tiền cần đạt

            $table->decimal('current_amount',15,2)
                  ->default(0);
            // Số tiền đã tiết kiệm

            $table->date('deadline')->nullable();
            // Thời hạn hoàn thành (có thể bỏ trống)

            $table->enum('status',['active','completed','cancelled'])
                  ->default('active');
            /*
            active = đang tiết kiệm
            completed = đã đạt mục tiêu
            cancelled = đã hủy
            */

            $table->timestamps();
            // created_at + updated_at

            // index giúp truy vấn nhanh
            $table->index('user_id');
        });
    }

    /**
     * Rollback migration
     */
    public function down(): void
    {
        Schema::dropIfExists('saving_goals');
    }
};