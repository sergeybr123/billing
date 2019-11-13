<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Invoice extends Model
{
//    protected $connection = 'mysql';
//    protected $table = 'invoices';

    use SoftDeletes;

    protected $fillable = [
        'manager_id',
        'user_id',
        'amount',
        'type_id',
        'plan_id',
        'period',
        'ref_options',
        'service_id',
        'description',
        'paid',
        'options',
        'status',
    ];

    protected $casts = [
        'ref_options' => 'array',
        'options' => 'array',
    ];

    protected $dates = [
        'start_subscribe',
        'paid_at',
        'created_at',
        'updated_at',
        'deleted_at',
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
}
