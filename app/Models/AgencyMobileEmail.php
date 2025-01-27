<?php



namespace App\Models;



use Illuminate\Database\Eloquent\Model;



class AgencyMobileEmail extends Model

{

    protected $fillable = ['agency_id','mobile_number','email'];

    //

    public function state()

    {

        return $this->hasOne('App\Model\State', 'id','state_id');

    }

}

