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
    Schema::create('recurring_transactions', function (Blueprint $table) {
        $table->id();
        $table->foreignId('user_id')->constrained()->onDelete('cascade'); // ID người dùng
        $table->string('name'); // Tên khoản nhắc (Ví dụ: Tiền phòng trọ)
        $table->decimal('amount', 15, 2)->nullable(); // Số tiền mặc định/gợi ý
        $table->enum('type', ['income', 'expense']); // Loại: Thu hoặc Chi
        
        // Liên kết với bảng categories sẵn có của bạn
        $table->foreignId('category_id')->constrained('categories')->onDelete('cascade'); 
        
        $table->integer('day_of_month'); // Ngày hẹn nhắc hằng tháng (từ 1 đến 31)
        $table->boolean('is_active')->default(true); // Trạng thái bật/tắt lịch nhắc
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('recurring_transactions');
    }
};
