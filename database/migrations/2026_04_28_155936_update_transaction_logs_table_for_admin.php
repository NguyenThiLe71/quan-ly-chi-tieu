<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('transaction_logs', function (Blueprint $table) {
            // 1. Thêm user_id nếu chưa có (Để Admin biết ai làm)
            if (!Schema::hasColumn('transaction_logs', 'user_id')) {
                $table->foreignId('user_id')->nullable()->after('id')->constrained('users')->cascadeOnDelete();
            }

            // 2. Ép kiểu transaction_id thành nullable (để lưu Log khi tìm kiếm)
            $table->unsignedBigInteger('transaction_id')->nullable()->change();
        });

        // 3. Cập nhật ENUM action để thêm 'searched' (Dùng SQL thuần)
        DB::statement("ALTER TABLE transaction_logs MODIFY COLUMN action ENUM('created', 'updated', 'deleted', 'searched') NOT NULL");
    }

    public function down(): void
    {
        Schema::table('transaction_logs', function (Blueprint $table) {
            if (Schema::hasColumn('transaction_logs', 'user_id')) {
                $table->dropForeign(['user_id']);
                $table->dropColumn('user_id');
            }
            $table->unsignedBigInteger('transaction_id')->nullable(false)->change();
        });
        DB::statement("ALTER TABLE transaction_logs MODIFY COLUMN action ENUM('created', 'updated', 'deleted') NOT NULL");
    }
};