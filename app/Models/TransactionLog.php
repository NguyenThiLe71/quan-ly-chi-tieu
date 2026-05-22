<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TransactionLog extends Model
{
    // Bảng này không dùng timestamps mặc định (created_at, updated_at) 
    // vì ông dùng changed_at rồi
    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'transaction_id',
        'action',
        'description',
        'note',
        'old_amount',
        'new_amount',
        'changed_at'
    ];

    // Liên kết với User để biết ai làm
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}