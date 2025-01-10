<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Productattribute extends Model
{
    use HasFactory;

    protected $table = 'productattributes';

    protected $fillable = ['product_id', 'product_attribute_name','type', 'is_recovery','status'];

    // public function productattr()
    // {
    //     return $this->hasOne('App\Model\Products', 'id','product_id');
    // }

    public function product()
    {
        return $this->belongsTo(Products::class, 'product_id', 'id');
    }

    public function productName()
    {
        return $this->hasOne('App\Models\Products', 'id','product_id');
    }
}
