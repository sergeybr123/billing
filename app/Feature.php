<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Feature extends Model
{
    protected $fillable = [
        'code', 'name', 'description', 'interval', 'interval_count', 'sort_order', 'active'
    ];

    public function plans()
    {
        return $this->belongsToMany('App\Plan', 'plans_features');
    }

    public function children(){
        return $this->hasMany( 'App\Feature', 'parent_id', 'id' );
    }

    public function parents(){
        return $this->hasOne( 'App\Feature', 'id', 'parent_id' );
    }
}
