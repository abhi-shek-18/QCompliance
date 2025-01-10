<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Products extends Model
{
    protected $fillable = ['name','type','bucket','is_recovery','capacity'];
    //
    public function productUser()
    {
        return $this->hasMany('App\Model\ProductUser','product_id', 'id')->with('user');
    }


    public function attributes()
    {
        return $this->hasMany(Productattribute::class, 'product_id', 'id');
    }
    

    
}
