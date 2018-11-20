<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Discount extends Model
{
    protected $fillable = [
        'code', 'name', 'interval', 'percent', 'start_at', 'end_at', 'active'
    ];
}
