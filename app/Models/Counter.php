<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Counter extends Model
{
    protected $fillable = [
        'counter_one_title',
        'counter_one_value',
        'counter_two_title',
        'counter_two_value',
        'counter_three_title',
        'counter_three_value',
        'counter_four_title',
        'counter_four_value',
    ];
}
