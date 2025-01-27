<?php

namespace App\Imports;

use App\Uploads\AdverseBulk;
use Maatwebsite\Excel\Concerns\ToModel;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Illuminate\Contracts\Queue\ShouldQueue;
use Maatwebsite\Excel\Concerns\WithChunkReading;

class AdverseImport implements ToModel, WithBatchInserts
{
    
    public function __construct(string $data = '')
    {
        $this->lob = $data; 
    }
    // /**
    // * @param array $row
    // *
    // * @return \Illuminate\Database\Eloquent\Model|null
    // */
    public function model(array $row)
    {
        if($row[0]=='AGRMNTID'){
            return null;
        }
        return new AdverseBulk([
            'AGRMNTID'=>$row[0],
            'PRODUCTFLAG'=>$row[1],
            'PRODUCTFLAG_Q'=>$row[2],
            'BRANCH'=>$row[3],
            'prev_month2_BOM_BUCKET'=>$row[4],
            'prev_month1_BOM_BUCKET'=>$row[5],
            'month_BOM_BUCKET'=>$row[6],
            'prev_month2_BOM_POS'=>$row[7],
            'prev_month1_BOM_POS'=>$row[8],
            'month_BOM_POS'=>$row[9],
            'prev_month2_Agency_Name'=>$row[10],
            'prev_month1_Agency_Name'=>$row[11],
            'month_Agency_Name'=>$row[12],
            'prev_month2_Agent_Code'=>$row[13],
            'prev_month1_Agent_Code'=>$row[14],
            'month_Agent_Code'=>$row[15],
            'Repeat_Agency'=>$row[16],
            'Buket_Match_Status'=>$row[17],
            'POS_Status'=>$row[18],
            'Formula1'=>$row[19],
            'Formula2'=>$row[20],
            'Formula3'=>$row[21],
            'Formula4'=>$row[22],
            'Formula5'=>$row[23],
            'Formula6'=>$row[24],
            'Formula7'=>$row[25],
            'Formula8'=>$row[26],
            'Formula9'=>$row[27],
            'lob'=>$this->lob
                ]);
    }
    public function batchSize(): int
    {
        return 50;
    }
    public function chunkSize(): int
    {
        return 50;
    }
}
