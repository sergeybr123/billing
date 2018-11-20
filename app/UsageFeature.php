<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UsageFeature extends Model
{
    protected $fillable = [
        'user_id', 'feature_id', 'usage_count',
    ];
}
