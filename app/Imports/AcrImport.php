<?php

namespace App\Imports;

use App\Model\AcrReportData;
use App\Agency;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Facades\DB;
use Auth;

class AcrImport implements ToModel, WithHeadingRow
{  
    public $data;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Array $data)
    {
        $this->data = $data;
    }

    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {   
        $branch_id=explode("_",$row['branch']);
        $getAgency=Agency::with('branch','branch.city','branch.city.state')->where(['branch_id'=>$branch_id[1],'agency_id'=>$row['agency_code']])->first();
        if($getAgency) {
            $arr=array();
            $arr['branch_id']=$branch_id[1];
            $arr['agency_id']=$row['agency_code'];
            $arr['month']=$row['month'];
            $arr['loan_acc_no']=$row['loan_account_no'];
            $arr['product_flag']=$row['product_flag'];
            $arr['product_group']=$row['product_group'];
            $arr['p_out_orig']=$row['principal_outstanding_original'];
            $arr['branch']=$getAgency->branch->name;
            $arr['b_state']=$getAgency->branch->city->state->name;
            $arr['b_city']=$getAgency->branch->city->name;
            $arr['region']=$row['region'];
            $arr['agency_name']=$getAgency->name;
            $arr['agent_id']=$row['agent_id'];
            $arr['agent_name']=$row['agent_name'];
            $arr['recovery_npa_stage']=$row['recovery_npa_stage_id'];
            $arr['collection_manager']=$row['collection_manager'];
            $arr['input_1']=$row['input_1'];
            $arr['input_2']=$row['input_2'];
            $arr['input_3']=$row['input_3'];
            $arr['input_4']=$row['input_4'];
            $arr['input_5']=$row['input_5'];
            $arr['input_6']=$row['input_6'];
            $arr['input_7']=$row['input_7'];
            $arr['input_8']=$row['input_8'];
            $arr['input_9']=$row['input_9'];
            $arr['input_10']=$row['input_10'];
            $arr['bombucket']=$row['bombucket'];
            $arr['date_stamp']=date('Y-m-d',strtotime($row['date_stamp']));
            $arr['uploaded_by']=$this->data['uploaded_by'];
            AcrReportData::create($arr);
        }        
    }

    public function headingRow(): int
    {
        return 1;
    }
}