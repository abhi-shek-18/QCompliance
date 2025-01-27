<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\FromArray;
use App\QcResult;
use App\Qc;
use App\Audit;
use App\AuditQc;
use App\AuditResult;
use App\Model\Branchable;

use Maatwebsite\Excel\Concerns\WithHeadings;
class QaAndQcChangesSheet2 implements FromArray,WithHeadings
{
    /**
    * @return \Illuminate\Support\Collection
    */
     public function __construct(array $filterdata)
    {

        $this->filterdata = $filterdata;

    }
    public function array(): array
    {
        $data=Audit::with(['qmsheet.qm_sheet_sub_parameter','audit_results.parameter_detail','audit_results.sub_parameter_detail'])->whereBetween('created_at', [$this->filterdata['start_date'], $this->filterdata['end_date']])->limit(50)->get();
        $parameterId=0;
        foreach($data as $k=>$row){
            $qcdata=QcResult::where('audit_id',$row->id)->get()->keyBy('sub_parameter_id');
            $parameter=[];
            foreach($row->qmsheet->qm_sheet_sub_parameter as $k=>$val){
                if(isset($parameter[$val->qm_sheet_parameter_id]['weight'])){
                    if($val->weight!=null){
                        $parameter[$val->qm_sheet_parameter_id]['weight']=$parameter[$val->qm_sheet_parameter_id]['weight']+$val->weight;
                    }
                    else{
                        $parameter[$val->qm_sheet_parameter_id]['weight']=$parameter[$val->qm_sheet_parameter_id]['weight']+0;
                    }
                }
                else{
                    $parameter[$val->qm_sheet_parameter_id]['weight']=($val->weight!=null)?$val->weight:0;
                }
            }
            foreach($row->audit_results as $k=>$value){
                // dd($value,$qcdata,$row->audit_results[8]);
                    $parameter[$value->parameter_id]['name']=$value->parameter_detail->parameter;
                    if(isset($parameter[$value->parameter_id]['score'])){
                        if($value->score!=null){
                            $parameter[$value->parameter_id]['score']=$parameter[$value->parameter_id]['score']+((int)$value->score);
                        }
                        else{
                            $parameter[$value->parameter_id]['score']=$parameter[$value->parameter_id]['score']+0;
                        }
                    }
                    else{
                        $parameter[$value->parameter_id]['score']=($value->score!=null)?(int)$value->score:0;
                    }
                    
                    if(isset($parameter[$value->parameter_id]['qcscore'])){
                        if(isset($qcdata[$value->sub_parameter_id]) && $qcdata[$value->sub_parameter_id]->score!=null){
                            $parameter[$value->parameter_id]['qcscore']=$parameter[$value->parameter_id]['qcscore']+((int)$qcdata[$value->sub_parameter_id]->score ?? 0);
                        }
                        else{
                            $parameter[$value->parameter_id]['qcscore']=$parameter[$value->parameter_id]['qcscore']+0;
                        }
                    }
                    else{
                        $parameter[$value->parameter_id]['qcscore']=(isset($qcdata[$value->sub_parameter_id]) && $qcdata[$value->sub_parameter_id]->score!=null)?(int)$qcdata[$value->sub_parameter_id]->score:0;
                    }
                }
                // dd($parameter);
                foreach($parameter as $item){
                    $qaPer='0';
                    $qcPer='0';
                    // if(!isset($item['score'])){
                    //     dd($item,$row->qmsheet->qm_sheet_sub_parameter,$parameter);
                    // }
                    if($item['weight']!=0 && isset($item['score'])){
                        $qaPer=($item['score']/$item['weight'])*100;
                        $qcPer=($item['qcscore']/$item['weight'])*100;
                    }
                    $final[]=[
                        'Parameter'=>$item['name'] ?? '',
                        'Qa_Scored'=>isset($item['score']) ? (string)$item['score']: '0',
                        'Qc_Scored'=>isset($item['qcscore']) ? (string)$item['qcscore']: '0',
                        'Qa_scorable'=>(string)$item['weight'] ?? '0',
                        'Qc_scorable'=>(string)$item['weight'] ?? '0',
                        'Qa_Percentage'=>($qaPer)?$qaPer: '0',
                        'Qc_Percentage'=>($qcPer)?$qcPer: '0',  
                    ];
                }
        }
        // dd($final);
        return $final;
    }
    
    public function headings(): array
    {
        return [
            'Parameter',
            'Qa_Scored',
            'Qc_Scored',
            'Qa_scorable',
            'Qc_scorable',
            'Qa_Percentage',
            'Qc_Percentage',
        ];
    }
    
}
