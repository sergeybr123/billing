<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AdditionalSubscribesType extends Model
{
    protected $fillable = [
        'name', 'deleted_at'
    ];
}
