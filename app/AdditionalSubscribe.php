<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AdditionalSubscribe extends Model
{
    protected $fillable = [
        'subscribe_id', 'additional_subscribe_type_id', 'trial_ends_at', 'start_at', 'end_at', 'deleted_at'
    ];
}
