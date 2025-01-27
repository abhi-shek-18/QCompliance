<?php

namespace App\Imports;

use App\Uploads\Setlement;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Illuminate\Contracts\Queue\ShouldQueue;
use Maatwebsite\Excel\Concerns\WithChunkReading;

class SetlementImport implements ToModel, WithBatchInserts,WithChunkReading, ShouldQueue
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
   
   
   
   
   
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        if($row[0]=='Month'){
            return null;
        }
        return new Setlement([
            'Month'=>$row[0],
            'REQUEST_NO'=>$row[1],
            'LOAN_NO'=>$row[2],
            'CUSTOMERNAME'=>$row[3],
            'BRANCH'=>$row[4],
            'STATE'=>$row[5],
            'PRODUCT_1'=>$row[6],
            'SCHEMEDESC'=>$row[7],
            'PENALTY'=>$row[8],
            'LOANAMT'=>$row[9],
            'EMI'=>$row[10],
            'SETTLEMENTAMT'=>$row[11],
            'REQUEST_DATE'=>$this->transformDate($row[12]),
            'REQUESTED_BY'=>$row[13],
            'SETTLEMENTSTART_DATE'=>$this->transformDate($row[14]),
            'SETTLEMENTEND_DATE'=>$this->transformDate($row[15]),
            'SETTLEMENT_STROKES'=>$row[16],
            'MAKER_REMARKS'=>$row[17],
            'VERIFIER_REMARKS'=>$row[18],
            'VERIFIED_DATE'=>$this->transformDate($row[19]),
            'VERIFIER'=>$row[20],
            'APPROVER_REAMRKS'=>$row[21],
            'APPROVED_DATE'=>$this->transformDate($row[22]),
            'APPROVER'=>$row[23],
            'STATUS1'=>$row[24],
            'APPROVED_BY'=>$row[25],
            'STATUS_DATE'=>$this->transformDate($row[26]),
            'APPROVER_EMAIL'=>$row[27],
            'TOTAL_POS'=>$row[28],
            'CURRENT_MONTH_INTEREST'=>$row[29],
            'INSTALLMENT_OVERDUE'=>$row[30],
            'TOTAL_OVERDUE_PRINCIPAL'=>$row[31],
            'TOTAL_OVERDUE_INTEREST'=>$row[32],
            'PENALTYCHARGES'=>$row[33],
            'ST_ON_PENALTY'=>$row[34],
            'BOUNCE_CHARGES'=>$row[35],
            'PENAL_CHARGES'=>$row[36],
            'OTHER_CHARGES'=>$row[37],
            'TOTAL_DUES'=>$row[38],
            'TOTAL_POSCOLL'=>$row[39],
            'CURRENT_MONTH_INTERESTCOLL'=>$row[40],
            'INSTALLMENT_OVERDUECOLL'=>$row[41],
            'TOTAL_OVERDUE_PRINCIPALCOLL'=>$row[42],
            'TOTAL_OVERDUE_INTERESTCOLL'=>$row[43],
            'PENALTYCOLLECTED'=>$row[44],
            'ST_ON_FC_CHARGESCOLL'=>$row[45],
            'BOUNCE_CHARGESCOLL'=>$row[46],
            'PENAL_CHARGESCOLL'=>$row[47],
            'OTHER_CHARGESCOLL'=>$row[48],
            'TOTAL_AMOUNT_COLLECTED'=>$row[49],
            'TOTAL_POSWAIVER'=>$row[50],
            'CURRENT_MONTH_INTERESTWAIVER'=>$row[51],
            'INSTALLMENT_OVERDUEWAIVER'=>$row[52],
            'TOTAL_OVERDUE_PRINCIPALWAIVER'=>$row[53],
            'TOTAL_OVERDUE_INTERESTWAIVER'=>$row[54],
            'PENALTYWIAVER'=>$row[55],
            'ST_ON_FC_CHARGESWAIVER'=>$row[56],
            'BOUNCE_CHARGESWAIVER'=>$row[57],
            'PENAL_CHARGESWAIVER'=>$row[58],
            'OTHER_CHARGESWAIVER'=>$row[59],
            'TOTAL_WAIVER'=>$row[60],
            'TOTAL_CHARGES_WAIVER'=>$row[61],
            'per_of_POS_Waiver'=>$row[62],
            'per_of_Charges_Waiver'=>$row[63],
            'BUCKET'=>$row[64],
            'DPD'=>$row[65],
            'DPDSTRING'=>$row[66],
            'STAGE'=>$row[67],
            'LOAN_STATUS'=>$row[68],
            'PAYMENT_RECEIVED'=>$row[69],
            'LAST_PAYMENT_DATE'=>$this->transformDate($row[70]),
            'SYSTEM'=>$row[71],
            'Product1'=>$row[72],
            'Status2'=>$row[73],
            'Hold_Category'=>$row[74],
            'Remark'=>$row[75],
            'Received_Date'=>$this->transformDate($row[76]),
            '1st_action_date'=>$this->transformDate($row[77]),
            'Hold_Date'=>$this->transformDate($row[78]),
            'Resolution_Date'=>$this->transformDate($row[79]),
            'Action_Date'=>$this->transformDate($row[80]),
            'Status3'=>$row[81],
            'Request_received_month'=>$row[82],
            'TOTAL_SETTLEMENT_AMT'=>$row[83],
            'Agency_Name'=>$row[84],
            'SETTLEMENT_Close_day'=>$row[85],
            'SETTLEMENT_Status'=>$row[86],
            'DAC_Amount'=>$row[87],
            'Online_Amount'=>$row[88],
            'Total_Payment_Reacive'=>$row[89],
            'SETTLEMENTAMT_Amount_Status'=>$row[90],
            'Amount_Deffrance'=>$row[91],
            'PRODUCT'=>$row[92],
            'BUCKET_Q'=>$row[93],
            'DAC_Received'=>$row[94],
            'Online_pay_date'=>$this->transformDate($row[95]),
            'Actual_Date'=>$this->transformDate($row[96]),
            'Date_GAP'=>$this->transformDate($row[97]),
            'Date_GAP_BKT'=>$this->transformDate($row[98]),
            // "Sep'19"=>$row[99],
            // "Oct'19"=>$row[100],
            // "Nov'19"=>$row[101],
            'Scheme'=>$row[102],
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
