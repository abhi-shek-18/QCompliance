<?php

namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{

    use Notifiable,HasRoles;

   

    protected $fillable = [
        'user_role_id', 'name', 'email', 'password', 'employee_id', 'mobile', 'status', 'team', 'audit_agency'
    ];
   

    protected $hidden = [

        'password', 'remember_token'

    ];



    /**

     * The attributes that should be cast to native types.

     *

     * @var array

     */

    protected $casts = [

        'email_verified_at' => 'datetime',

    ];

}

