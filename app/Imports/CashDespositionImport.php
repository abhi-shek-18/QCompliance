<?php

namespace App\Imports;

use App\Model\CashDepositionData;
use App\Agency;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Facades\DB;
use Auth;

class CashDespositionImport implements ToModel, WithHeadingRow
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
         // echo "<pre>"; print_r($row); die;
        $getAgency=Agency::where(['branch_id'=>$branch_id[1]])->where('name','LIKE', $row['agencyname'].'%')->first();
      
        if($getAgency) {
            $arr=array();
            $arr['branch_id']=$branch_id[1];
            $arr['agency_id']=$getAgency->agency_id;
            $arr['agent_name']=$row['agentname'];
            $arr['agent_id']=$row['agent_id'];
            $arr['receipt_no']=$row['receiptno'];
            $arr['date']=date('Y-m-d');
            $arr['uploaded_by']=$this->data['uploaded_by'];
            $arr['receipt_date']=date('Y-m-d',strtotime($row['receiptdate']));
            $arr['month']=$row['month'];
            $arr['reference_no']=$row['referenceno'];
            $arr['product_1']=$row['product_1'];
            $arr['total_rec_amt']=$row['totalreceiptamount'];
            $arr['deposite_date']=date('Y-m-d',strtotime($row['depositdate']));
            $arr['bb_pay_batch_date']=date('Y-m-d',strtotime($row['bbpaybatchackdate']));
            $arr['delay_deposite_bucket']=$row['delay_deposit_buket'];
            $arr['payment_id']=$row['paymentid'];
            $arr['location']=$row['location'];
            $arr['state']=$row['state'];
            $arr['physical_receipt_no']=$row['physicalreceiptno'];
            $arr['customer_name']=$row['customername'];
            $arr['current_bucket_1']=$row['current_bucket_1'];
            $arr['payment_toward']=$row['paymenttowards'];
            $arr['emi_amt']=$row['emiamt'];
            $arr['payment_mode']=$row['paymentmode'];
            $arr['instrument_date']=date('Y-m-d',strtotime($row['instrumentdate']));
            $arr['instrument_amount']=$row['instrumentamount'];
            $arr['pan_card_no']=$row['pancardno'];
            $arr['batch_id']=$row['batchid'];
            $arr['batch_id_created_date']=date('Y-m-d',strtotime($row['batchidcreateddate']));
            $arr['en_pay_in_slip_id']=$row['encollect_pay_in_slip_id'];
            $arr['cms_pay_in_slip_id']=$row['cms_pay_in_slip_id'];
            $arr['deposite_acc_no']=$row['depositaccountnumber'];
            $arr['deposite_amt']=$row['depositamount'];
            $arr['payment_status']=$row['paymentstatus'];
            $arr['amount']=$row['amount'];
            $arr['product_group']=$row['productgroup'];
            $arr['merchant_id']=$row['merchantid'];
            $arr['merchant_tran_id']=$row['merchanttransactionid'];
            $arr['bbpay_partner_agent_code']=$row['bbpaypartneragentcode'];
            $arr['bbpay_partner_branch_code']=$row['bbpaypartnerbranchcode'];
            $arr['deposite_bank']=$row['depositebankname'];
            $arr['bbpay_ack_without_time']=$row['bbpaybatchackdate_without_time'];
            $arr['delay_deposite']=$row['delay_deposit'];
            $arr['receipt_date_1']=$row['receiptdate_1'];
            $arr['receipt_time_1']=$row['receipt_time_1'];
            $arr['time_bkt']=$row['time_bkt'];
            $arr['recpt_sunday']=$row['recpt_sunday'];
            $arr['deposite_day']=$row['deposit_day'];
            $arr['bbpay_day']=$row['bbpay_day'];
            $arr['week_day']=$row['week_day'];
            $arr['product']=$row['product'];
            $arr['current_bucket']=$row['current_bucket'];
            $arr['date_new']=date('Y-m-d',strtotime($row['date_new']));
            $arr['batch_id_blank']=$row['batch_id_blank'];
            $arr['dac_source']=$row['dac_source'];
            
            CashDepositionData::create($arr);
        }   
    }

    public function headingRow(): int
    {
        return 1;
    }
}