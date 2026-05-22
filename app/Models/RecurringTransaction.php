<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RecurringTransaction extends Model
{
    use HasFactory;

    // Khai báo các trường được phép lưu dữ liệu vào bảng
    protected $fillable = [
        'user_id',
        'name',
        'amount',
        'type',
        'category_id',
        'day_of_month',
        'is_active',
    ];

    // Định nghĩa mối quan hệ: Lịch nhắc này thuộc về 1 Người dùng
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Định nghĩa mối quan hệ: Lịch nhắc này thuộc về 1 Danh mục
    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}