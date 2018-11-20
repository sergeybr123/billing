<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PlansFeature extends Model
{
    protected $fillable = [
        'plan_id', 'feature_id',
    ];

    public function plan()
    {
        return $this->belongsToMany('App\Plan');
    }

    public function feature()
    {
        return $this->belongsToMany('App\Feature');
    }
}
