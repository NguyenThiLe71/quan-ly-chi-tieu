<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $fillable = [
        'user_id',
        'category_id',
        'amount',
        'type',
        'is_saving',
        'description',
        'transaction_date'
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}