<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tạo bảng ai_insights
     */
    public function up(): void
    {
        Schema::create('ai_insights', function (Blueprint $table) {

            $table->id();
            // Khóa chính BIGINT auto increment

            $table->foreignId('user_id')
                  ->constrained('users')
                  ->cascadeOnDelete();
            // User nhận insight từ AI

            $table->enum('insight_type', [
                'pattern',
                'warning',
                'prediction',
                'recommendation'
            ]);
            /*
            pattern        = phát hiện thói quen chi tiêu
            warning        = cảnh báo chi tiêu bất thường
            prediction     = dự đoán tài chính
            recommendation = gợi ý tiết kiệm
            */

            $table->text('content');
            // Nội dung insight hiển thị cho user

            $table->json('data_json')->nullable();
            /*
            Dữ liệu JSON để hiển thị biểu đồ
            Ví dụ:
            {
                "category": "Food",
                "percent": 35
            }
            */

            $table->boolean('is_read')
                  ->default(false);
            // Đánh dấu user đã đọc insight chưa

            $table->timestamps();
            // created_at + updated_at

            // Index giúp query nhanh
            $table->index('user_id');
        });
    }

    /**
     * Rollback migration
     */
    public function down(): void
    {
        Schema::dropIfExists('ai_insights');
    }
};