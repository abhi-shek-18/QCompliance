<?php

namespace App\Imports;

use App\Model\DelaySeconAllocData;
use App\Agency;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Facades\DB;
use Auth;

class SecondaryAllocationImport implements ToModel, WithHeadingRow
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
            $arr['allocation']=$row['allocation'];
            $arr['amount']=$row['amount'];
            $arr['bucket_name']=$row['bucket_name'];
            $arr['date']=date('Y-m-d',strtotime($row['date']));
            $arr['uploaded_by']=$this->data['uploaded_by'];
            DelaySeconAllocData::create($arr);
        }  
    }

    public function headingRow(): int
    {
        return 1;
    }
}