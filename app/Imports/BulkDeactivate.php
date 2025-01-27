<?php

namespace App\Imports;

use App\User;
use App\Allocation;
use Hash;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Facades\DB;
// class UsersImport implements ToModel,WithChunkReading, WithHeadingRow, WithValidation,WithBatchInserts, ShouldQueue
class BulkDeactivate implements ToModel, WithHeadingRow
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
        $is_user_exist = User::where('email',$row['email'])->first();
        
        
        if($is_user_exist){
            try {
                /* if($row['email'] == '118949'){
                    print_r($is_user_exist);
                    die;
                } */
                $data=User::where('email',(string)$row['email'])->update(['active_status'=>1,'password'=>'fgfgfggfgdgdfgfdgdffhgfhreterfghh5y55']);
       
                //code...
            } catch(Exception $e) {

                echo 'Message: ' .$e->getMessage();
                echo "   \n   ". $row['email'];
                die;
            }
        } else {
            
            
        }
        
        
        //return 1;
        
    }
    public function headingRow(): int
    {
        return 1;
    }
    
}
