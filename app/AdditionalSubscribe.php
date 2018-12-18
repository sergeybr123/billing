<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Http\Resources\AdditionalSubscribeCollection;

class AdditionalSubscribe extends Model
{
    protected $fillable = [
        'subscribe_id',
        'additional_subscribe_type_id',
        'quantity',
        'price',
    ];

    protected $dates = [
        'trial_ends_at',
        'start_at',
        'end_at',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    public function type()
    {
        return $this->belongsTo('App\AdditionalSubscribesType', 'additional_subscribe_type_id', 'id');
    }
}
