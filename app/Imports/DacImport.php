<?php
namespace App\Imports;

use App\Uploads\Dacs;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Illuminate\Contracts\Queue\ShouldQueue;
use Maatwebsite\Excel\Concerns\WithChunkReading;

class DacImport implements ToModel, WithBatchInserts,WithChunkReading, ShouldQueue
{
    public function __construct(string $data = '')
    {
        $this->lob = $data; 
    }
    
    public function model(array $row)
    {
      
        if($row[1]=='PaymentId'){
            return null;
        }
        return new Dacs([
            'si'=>$row[0],
            'PaymentId'=>$row[1],
            'Location'=>$row[2],
            'BranchId'=>$row[3],
            'BranchName'=>$row[4],
            'AgencyId'=>$row[5],
            'AgencyName'=>$row[6],
            'AgentEmail'=>$row[7],
            'AgentName'=>$row[8],
            'Agent_Id'=>$row[9],
            'ReceiptBookNo'=>$row[10],
            'ReceiptNo'=>$row[11],
            'PhysicalReceiptNo_online_transaction_ID'=>$row[12],
            'ReceiptDate'=>$this->transformDate($row[13]),
            'Month'=>$row[14],
            'ReferenceNo'=>$row[15],
            'CUSTOMERNAME'=>$row[16],
            'PRODUCT'=>$row[17],
            'CURRENT_BUCKET'=>$row[18],
            'PaymentTowards'=>$row[19],
            'EMIAMT'=>$row[20],
            'LatePaymentPenalty'=>$row[21],
            'BounceChargesAmt'=>$row[22],
            'Excess'=>$row[23],
            'IMD'=>$row[24],
            'ProcFee'=>$row[25],
            'Swap'=>$row[26],
            'EBCCharge'=>$row[27],
            'CollectionPickupCharge'=>$row[28],
            'ForeclosureAmount'=>$row[29],
            'TotalReceiptAmount'=>$row[30],
            'PaymentMode'=>$row[31],
            'InstrumentDate'=>$row[32],
            'InstrumentNo'=>$row[33],
            'InstrumentAmount'=>$row[34],
            'MICRCode'=>$row[35],
            'PANCardNo'=>$row[36],
            'BatchID'=>$row[37],
            'BatchIDCreatedDate'=>$this->transformDate($row[38]),
            'DepositDate'=>$this->transformDate($row[39]),
            'ENCollect_Pay_in_slip_ID'=>$row[40],
            'CMS_Pay_In_Slip_ID'=>$row[41],
            'DepositAccountNumber'=>$row[42],
            'Rectified_Depslip_number'=>$row[43],
            'DepositAmount'=>$row[44],
            'PaymentStatus'=>$row[45],
            'DepositSlipNo_Status'=>$row[46],
            'Finnone_Update'=>$row[47],
            'Vintage'=>$row[48],
            'a'=>$row[49],
            's'=>$row[50],
            'MerchantReferenceNumber'=>$row[51],
            'MID'=>$row[52],
            'BankTransactionId'=>$row[53],
            'BankTId'=>$row[54],
            'Amount'=>$row[55],
            'StatusCode'=>$row[56],
            'CreatedDate'=>$this->transformDate($row[57]),
            'RRN'=>$row[58],
            'AuthCode'=>$row[59],
            'CardNumber'=>$row[60],
            'CardType'=>$row[61],
            'CardHolderName'=>$row[62],
            'ProductGroup'=>$row[63],
            'MerchantId'=>$row[64],
            'MerchantTransactionId'=>$row[65],
            'BBPayPartnerAgentCode'=>$row[66],
            'BBPayPartnerAgentEmailId'=>$row[67],
            'BBPayPartnerAgentMobileNumber'=>$row[68],
            'BBPayPartnerBranchCode'=>$row[69],
            'BBPayBatchAckDate'=>$this->transformDate($row[70]),
            'DepositeBankName'=>$row[71],
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
        return \Carbon\Carbon::createFromFormat($format, $value);
    }
}
}