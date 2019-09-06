<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SubscriptionHistory extends Model
{
    protected $fillable = [
        'subscribe_id',
        'type',
        'plan_id',
    ];

    protected $dates = [
        'start',
        'end',
        'created_at',
        'updated_at',
    ];
}
