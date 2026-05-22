<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tạo bảng categories
     */
    public function up(): void
    {
        Schema::create('categories', function (Blueprint $table) {

            $table->id();
            // Khóa chính BIGINT auto increment (chuẩn Laravel)

            $table->foreignId('user_id')
                  ->nullable()
                  ->constrained('users')
                  ->cascadeOnDelete();
            /*
            user_id:
            NULL  → danh mục hệ thống (mặc định)
            NOT NULL → danh mục do user tạo
            */

            $table->string('name', 100);
            // Tên danh mục (Food, Salary, Transport...)

            $table->enum('type', ['income', 'expense']);
            /*
            Loại danh mục:
            income  → thu nhập
            expense → chi tiêu
            */

            $table->tinyInteger('is_default')
                  ->default(0)
                  ->comment('1: danh mục hệ thống, 0: danh mục cá nhân');
            /*
            is_default:
            1 → category mặc định của hệ thống
            0 → category do user tạo
            */

            $table->timestamps();
            // Laravel tự tạo created_at & updated_at

            /*
            Tránh trùng danh mục cho cùng 1 user
            Ví dụ:
            user_id = 1 không thể có 2 category:
            Food - expense
            */
            $table->unique(['user_id', 'name', 'type'], 'uq_category_user');

            /*
            Index giúp tăng tốc query:
            SELECT * FROM categories
            WHERE user_id = ? AND type = ?
            */
            $table->index(['user_id', 'type']);
        });
    }

    /**
     * Xóa bảng nếu rollback
     */
    public function down(): void
    {
        Schema::dropIfExists('categories');
    }
};