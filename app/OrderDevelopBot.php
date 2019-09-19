<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class OrderDevelopBot extends Model
{
    protected $fillable = [
        'subscribe_id',
        'invoice_id',
        'amount',
        'paid_at',
    ];

    protected $dates = [
        'paid_at',
        'created_at',
        'updated_at',
    ];
}
