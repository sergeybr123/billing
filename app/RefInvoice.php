<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RefInvoice extends Model
{
    protected $fillable = [
        'invoice_id',
        'manager_id',
        'user_id',
        'amount',
        'type_id',
        'description',
    ];

    protected $dates = [
        'created_at',
        'updated_at',
    ];

    public function details()
    {
        return $this->hasMany('App\RefInvoiceDetail', 'ref_invoice_id', 'id');
    }
}
