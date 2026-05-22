<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
// Thêm class này để liên kết
use App\Models\UserInteraction; 

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'balance',
        'role',
        'status',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Thiết lập mối quan hệ với bảng tương tác
     * Giúp Controller dùng được withCount()
     */
    public function interactions()
    {
        // Một User có nhiều hành động (interactions)
        return $this->hasMany(UserInteraction::class, 'user_id', 'id');
    }
}