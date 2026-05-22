<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Cập nhật bảng ai_insights
     */
    public function up(): void
    {
        Schema::table('ai_insights', function (Blueprint $table) {

            $table->foreignId('category_id')
                  ->nullable()
                  ->after('user_id')
                  ->constrained('categories')
                  ->nullOnDelete();
            // Danh mục liên quan (nullable vì có insight tổng)

            $table->unsignedTinyInteger('month')
                  ->nullable()
                  ->after('data_json');
            // Tháng dữ liệu phân tích (1-12)

            $table->unsignedSmallInteger('year')
                  ->nullable()
                  ->after('month');
            // Năm dữ liệu phân tích

            // Index tối ưu truy vấn
            $table->index(['user_id', 'month', 'year']);
            $table->index('category_id');
            $table->index('insight_type');

        });
    }

    /**
     * Rollback migration
     */
    public function down(): void
    {
        Schema::table('ai_insights', function (Blueprint $table) {

            $table->dropForeign(['category_id']);
            $table->dropIndex(['user_id', 'month', 'year']);
            $table->dropIndex(['category_id']);
            $table->dropIndex(['insight_type']);

            $table->dropColumn(['category_id', 'month', 'year']);

        });
    }
};