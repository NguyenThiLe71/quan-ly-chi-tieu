<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SavingGoal extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'target_amount',
        'current_amount',
        'deadline',
        'status'
    ];

    protected $casts = [
        'target_amount' => 'float',
        'current_amount' => 'float',
        'deadline' => 'date',
    ];

    // 🔗 Quan hệ
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // 📊 % tiến độ
    public function getProgressAttribute()
    {
        if ($this->target_amount == 0) return 0;
        return ($this->current_amount / $this->target_amount) * 100;
    }

    // ✅ kiểm tra hoàn thành
    public function isCompleted()
    {
        return $this->current_amount >= $this->target_amount;
    }

    // 🔄 auto cập nhật status
    protected static function booted()
    {
        static::saving(function ($goal) {
            if ($goal->current_amount >= $goal->target_amount) {
                $goal->status = 'completed';
            }
        });
    }
}