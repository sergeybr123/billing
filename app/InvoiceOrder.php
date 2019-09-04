<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class InvoiceOrder extends Model
{
    protected $fillable = [
        'invoice_id',
        'type',
        'model',
        'paid_id',
        'name',
        'price',
        'quantity',
        'discount',
        'amount',
    ];

    protected $dates = [
        'created_at',
        'updated_at',
    ];

    public function invoice()
    {
        return $this->belongsTo('App\Invoice', 'invoice_id', 'id');
    }
}
