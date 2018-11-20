<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Plan extends Model
{
    protected $fillable = [
        'code', 'name', 'discount', 'description', 'price', 'interval', 'trial_period_days', 'sort_order', 'on_show', 'active'
    ];

    public function features()
    {
        return $this->belongsToMany('App\Feature', 'plans_features');
    }
}
