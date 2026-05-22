<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MonthlyStatistic extends Model
{
    protected $table = 'monthly_statistics';

    protected $fillable = [
        'user_id',
        'month',
        'year',
        'total_income',
        'total_expense',
        'saving_rate'
    ];

    public $timestamps = false;
}