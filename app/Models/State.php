<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class State extends Model
{
    protected $fillable = ['name','region_id'];
    //
    public function region()
    {
        return $this->hasOne('App\Model\Region', 'id','region_id');
    }
}
