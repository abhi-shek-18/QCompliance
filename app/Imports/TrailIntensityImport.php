<?php

namespace App\Imports;

use App\Uploads\TrailIntensity;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Illuminate\Contracts\Queue\ShouldQueue;
use Maatwebsite\Excel\Concerns\WithChunkReading;

class TrailIntensityImport implements ToModel, WithBatchInserts,WithChunkReading, ShouldQueue
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
        if($row[1]=='AGREEMENTNO'){
            return null;
        }   
        return new TrailIntensity([
            'agreement_id'=>$row[0],
            'agreement_no'=>$row[1],
            'npa_stage_id'=>$row[2],
            'bom_bucket'=>$row[3],
            'product_flag_1'=>$row[4],
            'bom_pos'=>$row[5],
            'branch'=>$row[6],
            'mailing_state'=>$row[7],
            'region'=>$row[8],
            'collection_manager_name'=>$row[9],
            'agency_code'=>$row[10],
            'agency_name'=>$row[11],
            'status'=>$row[12],
            'date_stamp_1'=>$this->transformDate($row[13]),
            'customer_name'=>$row[14],
            'customer_met'=>$row[15],
            'last_payment_date'=>$row[16],
            'ptp_date'=>$this->transformDate($row[17]),
            'ptp_amount'=>$row[18],
            'collection_name'=>$row[19],
            'feetback_date'=>$this->transformDate($row[20]),
            'disposition_code'=>$row[21],
            'trail_status'=>$row[22],
            'date_stamp'=>$this->transformDate($row[23]),
            'remarks'=>$row[24],
            'attempts'=>$row[25],
            'agent_id'=>$row[26],
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
