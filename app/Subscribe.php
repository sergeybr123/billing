<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\AdditionalSubscribe;

class Subscribe extends Model
{
    protected $fillable = [
        'user_id',
        'plan_id',
        'interval',
        'bot_count',
        'trial_ends_at',
        'start_at',
        'end_at',
        'active',
        'last_invoice',
    ];

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    public function plan()
    {
        return $this->belongsTo(Plan::class, 'plan_id', 'id');
    }

    public function additionals()
    {
        return $this->hasMany(AdditionalSubscribe::class, 'subscribe_id', 'id')->with('type');
    }

    public function invoices()
    {
        return $this->hasMany(Invoice::class, 'user_id', 'user_id')->orderByDesc('id');
    }

    public function last_invoice()
    {
        return $this->hasOne(Invoice::class, 'id', 'last_invoice');
    }
}
