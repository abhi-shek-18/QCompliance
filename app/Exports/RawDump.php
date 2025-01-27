<?php



namespace App\Exports;

use App\Audit;

use App\Qc;

use App\SavedQcAudit;

use App\Model\Branchable;

use App\User;

use Maatwebsite\Excel\Concerns\FromCollection;

use Maatwebsite\Excel\Concerns\FromArray;

use Maatwebsite\Excel\Concerns\WithHeadings;
use Illuminate\Support\Facades\DB;

class RawDump implements FromArray,WithHeadings

{

    public function __construct(array $filterdata)

    {

        $this->filterdata = $filterdata;

    }



    /*public function query()

    {

        return Post::query()->select('id', 'title', 'body')->whereYear('created_at', $this->year);

    }*/



    /**

    * @return \Illuminate\Support\Collection

    */

    public function collection()

    {

        //

    }

    public function headings(): array

    {

        return [
            'LOB',

            'NAME OF Agency',

            'ZONE',

            'BRANCH',

            'NAME OF QA',

            'NAME OF QC',

            'NAME OF Collection Manager',
            
            'NAME OF ACM',
            
            'NAME OF ZCM',
            
            'NAME OF RCM',
            
            'NAME OF NCM',

            'Emp Code',

            'Reporting Manager',

            'Designation',

            'Product',

            'MOBILE NUMBER',

            'AUDIT DATE',

            // 'AUDIT MODE',

            'AUDIT TYPES',

            'AUDIT STATUS',

            'MAIN PARAMETER',

            'SUB PARAMETER',

            // 'SCORING',

            'WEIGHTAGE',

            'SCORABLE',

            // 'SCORING PATTERN',

            'SCORED',

            'AUDIT OBSERVATION',

            'REMARK',
            
            'AUDIT DATE BY QA',
            
            'AUDIT CYCLE',

        ];

    }

    public function getReportingManager($branch_id,$product){

        // $ids=Branchable::where(['branch_id'=>$branch_id,'product_id'=>$product])->get()->pluck('manager_id');

        $ids=Branchable::with('user')->where(['branch_id'=>$branch_id,'product_id'=>$product])->get();

        $email='';

        $degination='';

        if($ids->where('type','Area_Collection_Manager')->count()>0){

            $user=$ids->where('type','Area_Collection_Manager')->first();

            $email=$user->user->name ?? '';

            $degination='Area Collection Manager';

        }

        elseif($ids->where('type','Regional_Collection_Manager')->count()>0){

            $user=$ids->where('type','Regional_Collection_Manager')->first();

            $email=$user->user->name ?? '';

            $degination='Regional Collection Manager';

        }

        elseif($ids->where('type','Zonal_Collection_Manager')->count()>0){

            $user=$ids->where('type','Zonal_Collection_Manager')->first();

            $email=$user->user->name ?? '';

            $degination='Zonal Collection Manager';

        }

        elseif($ids->where('type','National_Collection_Manager')->count()>0){

            $user=$ids->where('type','National_Collection_Manager')->first();

            $email=$user->user->name ?? '';

            $degination='National Collection Manager';

        }

        elseif($ids->where('type','Group_Product_Head')->count()>0){

            $user=$ids->where('type','Group_Product_Head')->first();

            $email=$user->user->name ?? '';

            $degination='Group Product Head';

        }

        else{

            $user=$ids->where('type','Head_of_the_Collections')->first();

            $email=$user->user->name ?? '';

            $degination='Head of the Collections';

        }

        // dd($degination,$email);

        return ['name'=>$email,'degination'=>$degination];

    }

    public function array(): array

    {
        //DB::enableQueryLog();

        //dd($this->filterdata);

        $ids= Qc::with('user')->where('status','!=',3)->get()->keyBy('audit_id');
        //dd($ids);

        $savedQcIds=SavedQcAudit::all()->pluck('audit_id')->toArray();

        if($ids->count()>0){
            //  print_r( str_replace(" ", ',' ,implode(' ',json_decode(json_encode($ids->pluck('audit_id')), true))));
            // echo "-------".'<br/>';
            // print_r(implode(',', $savedQcIds));
         // DB::enableQueryLog();

            $data = Audit::with(['branchnew','audit_cycle','qmsheet' => function($q){$q->where('lob', $this->filterdata['lob']);}])->whereBetween('created_at', [$this->filterdata['start_date'], $this->filterdata['end_date']])->with(['audit_results.parameter_detail','audit_results.sub_parameter_detail','product','branch.city.state.region','branch.branchable','yard.branch.city.state.region','agency.branch.city.state.region','qa_qtl_detail','branchRepo.branch.city.state.region','agencyRepo.branch.city.state.region','user'])->withCount('artifact')->whereIn('id',$ids->pluck('audit_id'))->whereNotIn('id',$savedQcIds)->get();

       // dd(DB::getQueryLog());

            

        }

        else{

            /*$data = Audit::orWhere('branch_id', $this->filterdata['branch_name'])->*/

            $data = Audit::with(['branchnew','audit_cycle','qmsheet' => function($q){$q->where('lob', $this->filterdata['lob']);}])->whereBetween('created_at', [$this->filterdata['start_date'], $this->filterdata['end_date']])->with(['audit_results.parameter_detail','audit_results.sub_parameter_detail','product','branch.city.state.region','branch.branchable','yard.branch.city.state.region','agency.branch.city.state.region','qa_qtl_detail','branchRepo.branch.city.state.region','agencyRepo.branch.city.state.region','user'])->withCount('artifact')->whereNotIn('id',$savedQcIds)->get();

        }



        // dd($data,$ids);

        if ($this->filterdata['branch_name'] != '')

        {

                $final=[];

                foreach($data as $k=>$row){
                    $name='';

                    $branch='';

                    $state='';

                    $region='';

                    $report=[];
                   if(!empty($row->qmsheet)){

                    switch ($row->qmsheet->type) {

                        case 'agency':

                            $name=$row->agency->name ?? '';

                            $branch=$row->agency->branch->name ?? '';

                            $branchid=$row->agency->branch->id ?? '';

                            $state=$row->agency->branch->city->state->name ?? '';

                            $region=$row->agency->branch->city->state->region->name ?? '';

                            $report=$this->getReportingManager($branchid,$row->product_id);

                            break;

                        case 'branch':

                            $branch=$row->branch->name ?? '';

                            $name=$branch;

                            $branchid=$row->branch->id ?? '';

                            $state=$row->branch->city->state->name ?? '';

                            $region=$row->branch->city->state->region->name ?? '';

                            $report=$this->getReportingManager($branchid,$row->product_id);

                            break;

                        case 'repo_yard':

                            $name=$row->yard->name ?? '';

                            $branch=$row->yard->branch->name ?? '';

                            $branchid=$row->yard->branch->id ?? '';

                            $state=$row->yard->branch->city->state->name ?? '';

                            $region=$row->yard->branch->city->state->region->name ?? '';

                            $report=$this->getReportingManager($branchid,$row->product_id);

                            break;

                        case 'branch_repo':

                            $name=$row->branchRepo->name ?? '';

                            $branch=$row->branchRepo->branch->name ?? '';

                            $branchid=$row->branchRepo->branch->id ?? '';

                            $state=$row->branchRepo->branch->city->state->name ?? '';

                            $region=$row->branchRepo->branch->city->state->region->name ?? '';

                            $report=$this->getReportingManager($branchid,$row->product_id);

                            break;

                        case 'agency_repo':

                            $name=$row->agencyRepo->name ?? '';

                            $branch=$row->agencyRepo->branch->name ?? '';

                            $branchid=$row->agencyRepo->branch->id ?? '';

                            $state=$row->agencyRepo->branch->city->state->name ?? '';

                            $region=$row->agencyRepo->branch->city->state->region->name ?? '';

                            $report=$this->getReportingManager($branchid,$row->product_id);

                            break;

                        

                    
                 } 

                    switch($ids[$row->id]->status ?? ''){

                        case 1:

                            $status='Pass with edit';

                        break;

                        case 2:

                            $status='Pass';

                        break;

                        case 3:

                            $status='Failed';

                        break;

                        default:

                            $status=(in_array($row->id,$savedQcIds))?'Saved':'Pending';

                        break;

                    }

                   

                    if (!empty($this->filterdata['branch_name']) && ($branchid == $this->filterdata['branch_name'])){

                        foreach($row->audit_results as $k=>$value){

                            // dd($value);
                            
                            $branchables=Branchable::with('acm','rcm','ncm','zcm')->where(['branch_id'=>$row->parent_branch_id,'product_id'=>$row->product_id,'manager_id'=>$row->branchnew->manager_id])->first();

                            $final[]=[
                                'lob'=>$row->qmsheet->lob ?? '',
                                'name'=>$name,

                                'zone'=>$region,

                                'branch'=>$branch,

                                'qa'=>$row->qa_qtl_detail->name ?? '',

                                'qc'=>$ids[$row->id]->user->name  ?? '',

                                'cm'=>$row->user->name ?? '',
                                
                                'acm'=>($branchables->acm && $branchables->acm->name) ?  $branchables->acm->name : '',
                                
                                'zcm'=>($branchables->zcm && $branchables->zcm->name) ? $branchables->zcm->name : '',
                                
                                'rcm'=>($branchables->rcm && $branchables->rcm->name) ? $branchables->rcm->name : '',
                                
                                'ncm'=>($branchables->ncm && $branchables->ncm->name) ? $branchables->ncm->name : '',

                                'emp_code'=>$row->user->employee_id ?? '',

                                'reporting_manager'=>$report['name'] ?? '',

                                'designation'=>$report['degination'] ?? '',

                                'product'=>$row->product->name ?? '',

                                'mobile'=>$row->user->mobile ?? '',

                                'audit_date'=>$row->created_at,

                                'audit_type'=>$row->qmsheet->type ?? '',

                                'audit_status'=>$status,

                                'main_parameter'=>$value->parameter_detail->parameter ?? '',

                                'sub_parameter'=>$value->sub_parameter_detail->sub_parameter ?? '',

                                'weightage'=>$value->sub_parameter_detail->weight ?? '0',

                                // 'scorable'=>$value->selected_option ?? '',

                                'scorable'=>($value->score!='N/A')?($value->sub_parameter_detail->weight ?? '') : ($value->score ?? ''),

                                'scored'=>$value->score ?? '',

                                // 'observation'=>$value->option_selected ?? '',

                                'observation'=>(($value->option_selected=='Percentage')?$value->option_selected.'('.$value->selected_per.')':$value->option_selected) ?? '',

                                'remark'=>$value->remark ?? '',
                                
                                'audit_date_by_aud'=>$row->audit_date_by_aud,

                                'audit_cycle_id'=>$row->audit_cycle->name ?? '',

                        ];

                        }  

                    }

                  }  

                
              }

                //dd($final);

                return $final;
            

        }

        else

        {

                $final=[];
             
                foreach($data as $k=>$row){

                    $name='';

                    $branch='';

                    $state='';

                    $region='';

                    $report=[];
                    if(!empty($row->qmsheet)){

                    switch ($row->qmsheet->type) {

                        case 'agency':

                            $name=$row->agency->name ?? '';

                            $branch=$row->agency->branch->name ?? '';

                            $branchid=$row->agency->branch->id ?? '';

                            $state=$row->agency->branch->city->state->name ?? '';

                            $region=$row->agency->branch->city->state->region->name ?? '';

                            $report=$this->getReportingManager($branchid,$row->product_id);

                            break;

                        case 'branch':

                            $branch=$row->branch->name ?? '';

                            $name=$branch;

                            $branchid=$row->branch->id ?? '';

                            $state=$row->branch->city->state->name ?? '';

                            $region=$row->branch->city->state->region->name ?? '';

                            $report=$this->getReportingManager($branchid,$row->product_id);

                            break;

                        case 'repo_yard':

                            $name=$row->yard->name ?? '';

                            $branch=$row->yard->branch->name ?? '';

                            $branchid=$row->yard->branch->id ?? '';

                            $state=$row->yard->branch->city->state->name ?? '';

                            $region=$row->yard->branch->city->state->region->name ?? '';

                            $report=$this->getReportingManager($branchid,$row->product_id);

                            break;

                        case 'branch_repo':

                            $name=$row->branchRepo->name ?? '';

                            $branch=$row->branchRepo->branch->name ?? '';

                            $branchid=$row->branchRepo->branch->id ?? '';

                            $state=$row->branchRepo->branch->city->state->name ?? '';

                            $region=$row->branchRepo->branch->city->state->region->name ?? '';

                            $report=$this->getReportingManager($branchid,$row->product_id);

                            break;

                        case 'agency_repo':

                            $name=$row->agencyRepo->name ?? '';

                            $branch=$row->agencyRepo->branch->name ?? '';

                            $branchid=$row->agencyRepo->branch->id ?? '';

                            $state=$row->agencyRepo->branch->city->state->name ?? '';

                            $region=$row->agencyRepo->branch->city->state->region->name ?? '';

                            $report=$this->getReportingManager($branchid,$row->product_id);

                            break;

                        

                    }
                   
                    switch($ids[$row->id]->status ?? ''){

                        case 1:

                            $status='Pass with edit';

                        break;

                        case 2:

                            $status='Pass';

                        break;

                        case 3:

                            $status='Failed';

                        break;

                        default:

                            $status=(in_array($row->id,$savedQcIds))?'Saved':'Pending';

                        break;

                    }


                    // dd($row->audit_results);
                    
                       
                        foreach($row->audit_results as $k=>$value){
                          $score_val=$obs='';
                        if(strtolower($value->option_selected) == 'percentage')
                                {
                                    if($value->selected_per == 100)
                                    {
                                      $score_val= $value->sub_parameter_detail->weight;  
                                    }elseif(($value->selected_per >= 1) && ($value->selected_per < 100))
                                    {
                                        $score_val= round(($value->sub_parameter_detail->weight * $value->selected_per)/100,1);
                                    }else
                                    {
                                        $score_val='0';
                                    }
                                }
                                else
                                {
                                    $score_val= $value->score;
                                }
                       if($value->selected_option == 'N/A')
                                {
                                    $obs='N/A';
                                    $score_val='N/A';
                                }else{
                                    $obs=$value->option_selected;
                                }
                                
                            $branchables=Branchable::with('acm','rcm','ncm','zcm')->where(['branch_id'=>$row->parent_branch_id,'product_id'=>$row->product_id,'manager_id'=>$row->branchnew->manager_id])->first();

                            $final[]=[
                                'lob'=>$row->qmsheet->lob ?? '',

                                'name'=>$name,

                                'zone'=>$region,

                                'branch'=>$branch,

                                'qa'=>$row->qa_qtl_detail->name ?? '',

                                'qc'=>$ids[$row->id]->user->name  ?? '',

                                'cm'=>$row->user->name ?? '',
                                
                                'acm'=>($branchables->acm && $branchables->acm->name) ?  $branchables->acm->name : '',
                                
                                'zcm'=>($branchables->zcm && $branchables->zcm->name) ? $branchables->zcm->name : '',
                                
                                'rcm'=>($branchables->rcm && $branchables->rcm->name) ? $branchables->rcm->name : '',
                                
                                'ncm'=>($branchables->ncm && $branchables->ncm->name) ? $branchables->ncm->name : '',

                                'emp_code'=>$row->user->employee_id ?? '',

                                'reporting_manager'=>$report['name'] ?? '',

                                'designation'=>$report['degination'] ?? '',

                                'product'=>$row->product->name ?? '',

                                'mobile'=>$row->user->mobile ?? '',

                                'audit_date'=>$row->created_at,

                                'audit_type'=>$row->qmsheet->type ?? '',

                                'audit_status'=>$status,

                                'main_parameter'=>$value->parameter_detail->parameter ?? '',

                                'sub_parameter'=>$value->sub_parameter_detail->sub_parameter ?? '',

                                'weightage'=>$value->sub_parameter_detail->weight ?? '0',

                                // 'scorable'=>$value->selected_option ?? '',

                                'scorable'=>($value->selected_option!='N/A')?($value->sub_parameter_detail->weight ?? '') : ($score_val ?? ''),

                                 'scored'=>isset($score_val) ? $score_val : '',

                                // 'observation'=>$value->option_selected ?? '',

                                 'observation'=>(($value->option_selected=='Percentage')?$value->option_selected.'('.$value->selected_per.')':$obs) ?? '',

                                'remark'=>$value->remark ?? '',
                                
                                'audit_date_by_aud'=>$row->audit_date_by_aud,
                                
                                'audit_cycle_id'=>$row->audit_cycle->name ?? '',

                                

                        ];

                        }  
                
                    }

                }

                //dd($final);

                return $final;
            

        }

    }

}