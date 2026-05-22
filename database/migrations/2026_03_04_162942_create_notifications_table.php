<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Chạy migration: tạo bảng notifications
     */
    public function up(): void
    {
        Schema::create('notifications', function (Blueprint $table) {

            $table->id();
            // Khóa chính BIGINT auto increment

            $table->foreignId('user_id')
                  ->constrained('users')
                  ->cascadeOnDelete();
            /*
            Khóa ngoại liên kết bảng users
            Nếu user bị xóa → thông báo cũng bị xóa
            */

            $table->string('title',150);
            // Tiêu đề thông báo

            $table->text('message');
            // Nội dung chi tiết của thông báo

            $table->boolean('is_read')
                  ->default(false);
            /*
            Trạng thái đọc:
            false = chưa đọc
            true  = đã đọc
            */

            $table->timestamp('created_at')
                  ->useCurrent();
            // Thời điểm tạo thông báo

            // Index giúp truy vấn nhanh danh sách thông báo của user
            $table->index('user_id');
        });
    }

    /**
     * Rollback migration
     */
    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};