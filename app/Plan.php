<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Plan extends Model
{
    protected $fillable = [
        'code',
        'name',
        'discount',
        'discount_option',
        'description',
        'price',
        'interval',
        'period',
        'trial_period_days',
        'sort_order',
        'on_show',
        'active',
        'bot_count'
    ];

    protected $casts = [
        'discount_options' => 'array'
    ];

    protected $dates = [
        'created_at',
        'updated_at',
    ];

    public function features()
    {
        return $this->belongsToMany('App\Feature', 'plans_features');
    }
}
