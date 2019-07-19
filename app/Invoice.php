<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
//    protected $connection = 'mysql';
//    protected $table = 'invoices';

    protected $fillable = [
        'manager_id',
        'user_id',
        'amount',
        'type_id',
        'plan_id',
        'period',
        'service_id',
        'description',
        'paid',
        'paid_at',
        'status',
    ];

    protected $casts = [
        'options' => 'array',
    ];

    public function types()
    {
        return $this->hasOne('App\TypeInvoice', 'id', 'type_id');
    }

    public function plan()
    {
        return $this->hasOne('App\Plan', 'id', 'plan_id');
    }

    public function service()
    {
        return $this->hasOne('App\Service', 'id', 'service_id');
    }

//    public function additional()
//    {
//        return $this->hasOne('App\AdditionalSubscribe', 'id', 'subscribe_id');
//    }
}
