<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('saving_goals', function (Blueprint $table) {
            // 🚀 THÊM INDEX: Giúp MySQL tìm kiếm tên mục tiêu và lọc theo ngày siêu nhanh
            $table->index(['user_id', 'deadline']);
            $table->index('name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('saving_goals', function (Blueprint $table) {
            // 🔄 HỦY INDEX: Nếu sau này ông rollback migration thì nó tự xóa index đi
            $table->dropIndex(['user_id', 'deadline']);
            $table->dropIndex(['name']);
        });
    }
};