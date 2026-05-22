<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
   public function up()
{
    Schema::table('transaction_logs', function (Blueprint $table) {
        // Thêm cột 'note' để lưu chi tiết cho Admin, để sau cột description
        $table->text('note')->nullable()->after('description');
    });
}

public function down()
{
    Schema::table('transaction_logs', function (Blueprint $table) {
        $table->dropColumn('note');
    });
}
};
