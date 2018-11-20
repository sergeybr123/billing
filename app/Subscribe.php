<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Subscribe extends Model
{
    protected $fillable = [
        'user_id',
        'plan_id',
        'interval',
        'trial_ends_at',
        'start_at',
        'end_at',
        'active',
    ];

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    public function plans()
    {
        return $this->belongsTo('App\Plan', 'plan_id', 'id');
    }

    public function additional()
    {
        return $this->hasMany(AdditionalSubscribe::class, 'subscribe_id', 'id');
    }
}
