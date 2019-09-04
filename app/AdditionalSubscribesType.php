<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AdditionalSubscribesType extends Model
{
    protected $fillable = [
        'name',
        'price',
        'created_at',
        'updated_at',
        'deleted_at',
    ];
}
