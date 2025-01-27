<?php



namespace App\Models;



use Illuminate\Database\Eloquent\Model;



class Agency extends Model

{

    protected $fillable=['name','branch_id','agency_id','agency_manager','location','address','city_id','state','email','mobile_number','region_id','agency_phone','product_id','sub_product_id'];

    public function branch()

    {

        return $this->hasOne('App\Model\Branch', 'id','branch_id');

    }

    public function user()

    {

        return $this->hasOne('App\User', 'id','agency_manager');

    }

    public function city()
    {
        return $this->hasOne('App\Model\City', 'id','city_id');
    }

    public function branchable()
    {
        return $this->hasMany('App\Model\Branchable', 'agency_id','id')->with('user','product.productUser');
    }
    
    public function branchableCollection()
    {
        return $this->hasMany('App\Model\Branchable', 'agency_id','id')->where('status',2);
    }

    public function emails()
    {
        return $this->hasMany('App\Model\AgencyMobileEmail','agency_id')->whereNotNull('email');  // Assuming the Email model exists
    }

    public function mobileNumbers()
    {
        return $this->hasMany('App\Model\AgencyMobileEmail','agency_id')->whereNotNull('mobile_number');  // Assuming the Email model exists
    }
}

