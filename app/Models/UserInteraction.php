<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\User;

class UserInteraction extends Model
{
    use HasFactory;

    protected $fillable = [
    'user_id',
    'module',
    'action',
    'keyword',
    'note',
    'ip',
    'user_agent',
];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}