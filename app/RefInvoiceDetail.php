<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RefInvoiceDetail extends Model
{
    protected $fillable = [
        'ref_invoice_id',
        'type',
        'paid_id',
        'paid_type',
        'details',
        'price',
        'quantity',
        'discount',
        'amount',
    ];

    protected $casts = [
        'details' => 'array'
    ];

    protected $dates = [
        'created_at',
        'updated_at',
    ];
}
