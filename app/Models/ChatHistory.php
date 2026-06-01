<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChatHistory extends Model
{
    use HasFactory;

    /**
     * Tên bảng trong database (mặc định là chat_histories, 
     * nếu bạn đặt tên khác thì sửa lại ở đây).
     */
    protected $table = 'chat_histories';

    /**
     * Các cột cho phép gán giá trị (Mass Assignment).
     */
    protected $fillable = [
        'user_id',
        'message',
        'answer',
    ];

    /**
     * Thiết lập mối quan hệ: Mỗi lịch sử chat thuộc về một người dùng.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}