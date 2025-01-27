<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Branch extends Model
{

    protected $fillable = ['manager_id', 'owner_id', 'city_id', 'name', 'uuid', 'location', 'lob','address'];

    public function city()
    {
        return $this->hasOne('App\Model\City', 'id','city_id');
    }


    public function user()
    {
        return $this->hasOne('App\User', 'id','manager_id')->latest('id');
    }



    public function branchable()
    {
        return $this->hasMany('App\Model\Branchable', 'branch_id','id')->with('user','product.productUser');
    }
    
    public function branchableCollection()
    {
        return $this->hasMany('App\Model\Branchable', 'branch_id','id')->where('status',2);
    }

    public function yard()
    {
        return $this->hasMany('App\Yard', 'branch_id','id');
    }

    public function branchRepo()
    {
        return $this->hasMany('App\Model\BranchRepo', 'branch_id','id');
    }

    public function agencyRepo()
    {
        return $this->hasMany('App\Model\AgencyRepo', 'branch_id','id');
    }

    public function agency()
    {
        return $this->hasMany('App\Agency', 'branch_id','id');
    }



}



