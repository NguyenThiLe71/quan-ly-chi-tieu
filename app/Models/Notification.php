<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Notification extends Model
{
    // Chỉ dùng created_at, tắt updated_at vì thông báo thường không sửa nội dung
    const UPDATED_AT = null; 
    public $timestamps = true;

    protected $fillable = [
        'user_id',
        'title',
        'message',
        'is_read',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'is_read' => 'boolean',
    ];

    /**
     * QUAN TRỌNG: Thêm hàm này để fix lỗi RelationNotFoundException
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    protected static function boot()
    {
        parent::boot();

        // Tự động sắp xếp thông báo mới nhất lên đầu
        static::addGlobalScope('order', function ($builder) {
            $builder->orderBy('created_at', 'desc');
        });

        /* LƯU Ý: Tạm thời t bỏ cái static::creating tự gán Auth::id() 
           vì đây là trang Admin, nếu m gửi cho "Tất cả" thì user_id phải là NULL.
           Nếu m để tự gán, nó sẽ lấy ID của Admin gán vào người nhận đó.
        */
    }
}