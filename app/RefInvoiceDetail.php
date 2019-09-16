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
        'price',
        'quantity',
        'discount',
        'amount',
    ];

    protected $dates = [
        'created_at',
        'updated_at',
    ];
}
