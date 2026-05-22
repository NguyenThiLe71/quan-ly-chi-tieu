<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Insight extends Model
{
    protected $table = 'ai_insights';

    protected $fillable = [
        'user_id',
        'insight_type',
         'category_id',
        'content',
        'data_json',
        'month',
        'year',
        'is_read'
    ];

    protected $casts = [
        'data_json' => 'array'
    ];
}