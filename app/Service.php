<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    protected $fillable = [
        'plan_id',
        'name',
        'description',
        'discount',
        'quantity',
        'price',
        'active',
    ];

    protected $casts = [
        'discount_options' => 'array'
    ];

    protected $dates = [
        'start_at',
        'end_at',
        'deleted_at',
    ];
}
