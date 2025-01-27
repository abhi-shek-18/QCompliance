<?php

namespace App\Imports;

use App\Model\ReceiptCutData;
use App\Agency;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Facades\DB;
use Auth;

class ReceiptCutImport implements ToModel, WithHeadingRow
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
        $getAgency=Agency::where(['branch_id'=>$branch_id[1],'agency_id'=>$row['agency_id']])->first();
        if($getAgency) {
            $arr=array();
            $arr['branch_id']=$branch_id[1];
            $arr['agency_id']=$row['agency_id'];
            $arr['agent_name']=$row['agent_name'];
            $arr['agent_id']=$row['agent_id'];
            $arr['receipt_no']=$row['receipt_no'];
            $arr['date']=date('Y-m-d',strtotime($row['date']));
            $arr['uploaded_by']=$this->data['uploaded_by'];
            $arr['receipt_date']=$row['receipt_date'];
            $arr['month']=$row['month'];
            $arr['reference_no']=$row['reference_no'];
            $arr['product_1']=$row['product_1'];
            $arr['total_rec_amt']=$row['total_rec_amt'];
            $arr['deposite_date']=$row['deposite_date'];
            $arr['bb_pay_batch_date']=$row['bb_pay_batch_date'];
            $arr['delay_deposite_bucket']=$row['delay_deposite_bucket'];
            $arr['receipt_time_1']=$row['receipt_time_1'];
            $arr['time_bkt']=$row['time_bkt'];
            ReceiptCutData::create($arr);
        }   
    }

    public function headingRow(): int
    {
        return 1;
    }
}