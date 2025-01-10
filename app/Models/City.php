<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class City extends Model
{
    protected $fillable = ['name','state_id'];
    //
    public function state()
    {
        return $this->hasOne('App\Model\State', 'id','state_id');
    }
}
