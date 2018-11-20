<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    protected $fillable = [
        'plan_id', 'name', 'description', 'price', 'active', 'start_at', 'end_at', 'deleted_at'
    ];
}
