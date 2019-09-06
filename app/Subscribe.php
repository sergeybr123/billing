<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\AdditionalSubscribe;

class Subscribe extends Model
{
    protected $fillable = [
        'user_id',
        'plan_id',
        'interval',
        'bot_count',
        'trial_ends_at',
        'start_at',
        'end_at',
        'active',
        'last_invoice',
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

    public function additionals()
    {
        return $this->hasMany(AdditionalSubscribe::class, 'subscribe_id', 'id')->with('type');
    }
}
