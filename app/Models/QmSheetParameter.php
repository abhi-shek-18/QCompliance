<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QmSheetParameter extends Model
{
    public function qm_sheet_sub_parameter()
    {
        return $this->hasMany('App\QmSheetSubParameter');
    }
}
