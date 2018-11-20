<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CPLog extends Model
{
    protected $fillable = [
        'invoice_id',
        'transaction_id',
        'currency',
        'cardFirstSix',
        'cardLastFour',
        'cardType',
        'name',
        'email',
        'issuer',
        'token',
    ];
}
