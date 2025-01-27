<?php

namespace App\Imports;

use App\Uploads\Allocation;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Illuminate\Contracts\Queue\ShouldQueue;
use Maatwebsite\Excel\Concerns\WithChunkReading;

class AllocationImport implements ToModel, WithBatchInserts,WithChunkReading, ShouldQueue
{
    public function __construct(string $data = '')
    {
        $this->lob = $data; 
    }
    public function transformDate($value, $format = 'Y-m-d')
    {
        try {
            if($value!=null){
                return \Carbon\Carbon::instance(\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($value))->format($format);
            }
            else{
                return null;
            }
        } catch (\ErrorException $e) {
            // return \Carbon\Carbon::createFromFormat($format, $value);
            return $value;
        }
    }
    public function model(array $row)
    {
        if($row[0]=='AGRMNTID'){
            return null;
        }
        return new Allocation([
            'agrmnt_id'=>$row[0],
            'agreement_no'=>$row[1],
            'npa_stage_id'=>$row[2],
            'bom_bucket'=>$row[3],
            'product_flag'=>$row[4],
            'bom_pos'=>$row[5],
            'branch'=>$row[6],
            'mailing_state'=>$row[7],
            'region'=>$row[8],
            'collection_manager'=>$row[9],
            'agency_code'=>$row[10],
            'agency_name'=>$row[11],
            'status'=>$row[12],
            'date_stamp'=>$this->transformDate($row[13]),
            'agent_code'=>$row[14],
            'agent_name'=>$row[15],
            'agent_allocation_status'=>$row[16],
            'agent_allocation_date_stamp'=>$this->transformDate($row[17]),
            'remarks'=>$row[18],  
            'lob'=>$this->lob,
                ]);
    }
    public function batchSize(): int
    {
        return 100;
    }
    public function chunkSize(): int
    {
        return 100;
    }
}
