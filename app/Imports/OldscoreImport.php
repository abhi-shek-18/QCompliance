<?php

namespace App\Imports;

use App\OldScore;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Facades\DB;
use Auth;

class OldscoreImport implements ToModel, WithHeadingRow
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
       
        
        if($row['branch_id'] != '' && !empty($row['branch_id'])) 
        {
            $branch_id=explode("_",$row['branch_id']);
            $data=array();
            
            if($row['pro_id'] != '' && !empty($row['pro_id'])) {
                $pro_id=explode("_",$row['pro_id']);
                $q=OldScore::where('branch_id',$branch_id[1])->where('type',1)->where('product_id',$pro_id[1])->first();
                $data['product_id']=$pro_id[1];
                $data['type']=1;
            } else {
                $q=OldScore::where('branch_id',$branch_id[1])->where('type',0)->first();
                $data['type']=0;
                $data['product_id']=0;
            }
            $data['branch_id']=$branch_id[1];
            $data['previous_1']=$row['previous_1'];
            $data['previous_2']=$row['previous_2'];
            $data['previous_3']=$row['previous_3'];
            $data['current_score']=0;
            $data['uploaded_by']=Auth::user()->id;
            
            if($q){
                $q->update($data);
                $q->save();
            }
            else {
                OldScore::create($data);
            }
        }
    }

    public function headingRow(): int
    {
        return 1;
    }
}