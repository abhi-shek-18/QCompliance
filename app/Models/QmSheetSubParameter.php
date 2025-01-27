<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QmSheetSubParameter extends Model
{
    public function audit_alert_box()
    {
        return $this->belongsTo('App\AuditAlertBox');
    }
    public function artifact()
    {
        return $this->hasMany('App\Artifact','sub_parameter_id','id');
    }
}
