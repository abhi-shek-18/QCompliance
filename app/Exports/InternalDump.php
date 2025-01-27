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

class InternalDump implements FromArray,WithHeadings

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

       return ['Month','LOB','Zone','State','City','Branch Name','Agency Name','Yard Name','Agency Repo','Branch Repo','Product Name','Bucket','Collection Manager','CM Email Id','CM EMP Code','CM Mobile No','Area Collection Manager','ACM Email ID','ACM EMP Code','ACM Mobile No','RCM','RCM Email ID','RCM Emp Code','RCM Mobile NO','ZCM','ZCM Email ID','ZCM Emp Code','ZCM Mobile No','National Head','NH Email ID','NH Emp Code','NH Mobile No','Group Head','GH Email ID','GH Emp Code','GH Mobile No','Branch Location','Branch Address','Branch ID','Agency ID','Agency Manager','Agency Location','Agency Address','Yard ID','Yard Location','Yard Address','Branch Repo Address','Branch Repo ID','Agency Repo Address','Agency Repo ID','Auditior Email ID','Audit Type','Audit Assign Date','Audit Latitude','Audit Longitude','Submitted / Approved','Parameter','Sub Parameter','QA Name','Audit Submitted Date','Score','QA Scorable','QA Scored','QA Remark','QC_name','Audit Approved Date','Audit Status','Audit_Observation','QC Scorable','QC Scored','QC Remark'
            

        ];

    }

    public function getReportingManager($branch_id,$product){

        // $ids=Branchable::where(['branch_id'=>$branch_id,'product_id'=>$product])->get()->pluck('manager_id');

        $ids=Branchable::where(['branch_id'=>$branch_id,'product_id'=>$product])->select('manager_id','acm_id','zcm_id','rcm_id','ncm_id','gph_id')->first();
        $email='';

        $degination='';
        //echo "<pre/>";
       // print_r($ids);die;
        $users['collection_manager_name']=$users['collection_manager_email']=$users['collection_manager_emp_id']=$users['collection_manager_mobile']=$users['area_manager_name']=$users['area_manager_email']=$users['area_manager_emp_id']=$users['area_manager_mobile']=$users['zone_manager_name']=$users['zone_manager_email']=$users['zone_manager_emp_id']=$users['zone_manager_mobile']=$users['regional_manager_name']=$users['regional_manager_email']=$users['regional_manager_emp_id']=$users['regional_manager_mobile']=$users['national_manager_name']=$users['national_manager_email']=$users['national_manager_emp_id']=$users['national_manager_mobile']=$users['group_head_name']=$users['group_head_email']=$users['group_head_emp_id']=$users['group_head_mobile']='NA';
        if(!empty($ids['manager_id'])){
            $users_data=User::where('id',$ids['manager_id'])->first();
            $users['collection_manager_name']=isset($users_data['name'])? $users_data['name'] :'NA';
            $users['collection_manager_email']=isset($users_data['email'])? $users_data['email'] :'NA';
            $users['collection_manager_emp_id']=isset($users_data['employee_id'])? $users_data['employee_id'] : 'NA';
            $users['collection_manager_mobile']=isset($users_data['mobile'])?$users_data['mobile']: 'NA';

        }
        if(!empty($ids['acm_id'])){
            $users_data=User::where('id',$ids['acm_id'])->first();
            $users['area_manager_name']=isset($users_data['name'])? $users_data['name'] :'NA';
            $users['area_manager_email']=isset($users_data['email'])? $users_data['email'] :'NA';
            $users['area_manager_emp_id']=isset($users_data['employee_id'])? $users_data['employee_id'] : 'NA';
            $users['area_manager_mobile']=isset($users_data['mobile'])?$users_data['mobile']: 'NA';

        }
        if(!empty($ids['zcm_id'])){
            $users_data=User::where('id',$ids['zcm_id'])->first();
            $users['zone_manager_name']=isset($users_data['name'])? $users_data['name'] :'NA';
            $users['zone_manager_email']=isset($users_data['email'])? $users_data['email'] :'NA';
            $users['zone_manager_emp_id']=isset($users_data['employee_id'])? $users_data['employee_id'] : 'NA';
            $users['zone_manager_mobile']=isset($users_data['mobile'])?$users_data['mobile']: 'NA';

        }
        if(!empty($ids['rcm_id'])){
            $users_data=User::where('id',$ids['rcm_id'])->first();
            $users['regional_manager_name']=isset($users_data['name'])? $users_data['name'] :'NA';
            $users['regional_manager_email']=isset($users_data['email'])? $users_data['email'] :'NA';
            $users['regional_manager_emp_id']=isset($users_data['employee_id'])? $users_data['employee_id'] : 'NA';
            $users['regional_manager_mobile']=isset($users_data['mobile'])?$users_data['mobile']: 'NA';

        }
        if(!empty($ids['ncm_id'])){
            $users_data=User::where('id',$ids['ncm_id'])->first();
            $users['national_manager_name']=isset($users_data['name'])? $users_data['name'] :'NA';
            $users['national_manager_email']=isset($users_data['email'])? $users_data['email'] :'NA';
            $users['national_manager_emp_id']=isset($users_data['employee_id'])? $users_data['employee_id'] : 'NA';
            $users['national_manager_mobile']=isset($users_data['mobile'])?$users_data['mobile']: 'NA';

        }
         if(!empty($ids['gph_id'])){
            $users_data=User::where('id',$ids['gph_id'])->first();
            $users['group_head_name']=isset($users_data['name'])? $users_data['name'] :'NA';
            $users['group_head_email']=isset($users_data['email'])? $users_data['email'] :'NA';
            $users['group_head_emp_id']=isset($users_data['employee_id'])? $users_data['employee_id'] : 'NA';
            $users['group_head_mobile']=isset($users_data['mobile'])?$users_data['mobile']: 'NA';

        }

        return $users;

    }
    public function qcdata($auditid,$parameterid,$subparameter_id){
        $qcData=DB::table('qc_results')->select('id','selected_option','score','remark','created_at','selected_per','option_selected')->where('audit_id',$auditid)->where('parameter_id',$parameterid)->where('sub_parameter_id',$subparameter_id)->orderby('id','desc')->first();

        return $qcData;

    }

    public function array(): array

    {

        //DB::enableQueryLog();

          //dd($this->filterdata);

        $ids= Qc::with('user')->get()->keyBy('audit_id');

        $savedQcIds=SavedQcAudit::all()->pluck('audit_id')->toArray();

        if($ids->count()>0){

            $data = Audit::with(['qmsheet' => function($q){$q->orWhere('lob', $this->filterdata['lob']);}])->whereBetween('created_at', [$this->filterdata['start_date'], $this->filterdata['end_date']])->with(['audit_results.parameter_detail','audit_results.sub_parameter_detail','product','branch.city.state.region','branch.branchable','yard.branch.city.state.region','agency.branch.city.state.region','qa_qtl_detail','branchRepo.branch.city.state.region','agencyRepo.branch.city.state.region','user'])->withCount('artifact')->whereIn('id',$ids->pluck('audit_id'))->whereNotIn('id',$savedQcIds)->get();

        //dd(DB::getQueryLog());

            

        }

        else{

            /*$data = Audit::orWhere('branch_id', $this->filterdata['branch_name'])->*/

            $data = Audit::with(['qmsheet' => function($q){$q->orWhere('lob', $this->filterdata['lob']);}])->whereBetween('created_at', [$this->filterdata['start_date'], $this->filterdata['end_date']])->with(['audit_results.parameter_detail','audit_results.sub_parameter_detail','product','branch.city.state.region','branch.branchable','yard.branch.city.state.region','agency.branch.city.state.region','qa_qtl_detail','branchRepo.branch.city.state.region','agencyRepo.branch.city.state.region','user'])->withCount('artifact')->whereNotIn('id',$savedQcIds)->get();

        }


        if ($this->filterdata['branch_name'] != '')

        {

                $final=[];

                foreach($data as $k=>$row){

                    $name='';

                    $branch='';

                    $state='';

                    $region='';

                    $report=[];

        // if(!isset($row->qmsheet->type)){


        // }

                    switch ($row->qmsheet->type) {

                        case 'agency':

                            $agency_name=$row->agency->name ?? '';

                            $branch=$row->agency->branch->name ?? '';
                            $agency_location=$row->agency->location ?? '';
                            $agency_address=$row->agency->address ?? '';
                            $agency_id=$row->agency->agency_id;
                            $agency_mananger=$row->agency_mananger;

                            $branchid=$row->agency->branch->id ?? '';

                            $state=$row->agency->branch->city->state->name ?? '';

                            $region=$row->agency->branch->city->state->region->name ?? '';
                            $city=$row->agency->branch->city->name ?? '';

                            $report=$this->getReportingManager($branchid,$row->product_id);


                            break;

                        case 'branch':

                          $branch=$row->branch->name ?? '';

                            $branch_name=$branch;
                            $address=$row->branch->location ?? '';

                            $branchid=$row->branch->id ?? '';

                            $state=$row->branch->city->state->name ?? '';

                            $region=$row->branch->city->state->region->name ?? '';
                             $city=$row->branch->city->name ?? '';

                            $report=$this->getReportingManager($branchid,$row->product_id);



                            break;

                        case 'repo_yard':

                           $repo_yard_name=$row->yard->name ?? '';

                            $branch=$row->yard->branch->name ?? '';
                            $yard_location=$row->yard->location ?? '';
                            $yard_id=$row->yard->yard_id ?? '';
                            $yard_address=$row->yard->address ?? '';
                            $branchid=$row->yard->branch->id ?? '';

                            $state=$row->yard->branch->city->state->name ?? '';

                            $region=$row->yard->branch->city->state->region->name ?? '';
                            $city=$row->yard->branch->city->name ?? '';

                            $report=$this->getReportingManager($branchid,$row->product_id);

                            break;

                        case 'branch_repo':

                           $branch_repo_name=$row->branchRepo->name ?? '';
                            $branch_repo_address=$row->branchRepo->location ?? '';
                            $branch_repo_id=$row->branchRepo->branch_repo_id ?? '';

                            $branch=$row->branchRepo->branch->name ?? '';

                            $branchid=$row->branchRepo->branch->id ?? '';

                            $state=$row->branchRepo->branch->city->state->name ?? '';

                            $region=$row->branchRepo->branch->city->state->region->name ?? '';
                            $city=$row->branchRepo->branch->city->name ?? '';

                            $report=$this->getReportingManager($branchid,$row->product_id);

                            break;

                        case 'agency_repo':

                            $agency_repo_name=$row->agencyRepo->name ?? '';
                            $agency_repo_address=$row->agencyRepo->location ?? '';
                            $agency_repo_id=$row->agencyRepo->agency_repo_id ?? '';

                            $branch=$row->agencyRepo->branch->name ?? '';

                            $branchid=$row->agencyRepo->branch->id ?? '';

                            $state=$row->agencyRepo->branch->city->state->name ?? '';

                            $region=$row->agencyRepo->branch->city->state->region->name ?? '';
                            $city=$row->agencyRepo->branch->city->name ?? '';

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

                           $qcresult= $this->qcdata($value->audit_id,$value->parameter_id,$value->sub_parameter_id);
                            $month=\Carbon\Carbon::parse($row->created_at)->formatLocalized("%b'%y");

                            $final[]=[
                                'month'=>$month ?? '',
                                'lob'=>$row->qmsheet->lob ?? '',
                                'zone'=>$region,
                                'state'=>$state ?? '',
                                'city'=>$city ?? '',
                                'branch_name'=>$branch_name ?? '',
                                'agency_name'=>$agency_name ?? '',
                                'yard_name'=>$repo_yard_name ?? '',
                                'repo_agency'=>$agency_repo_name ?? '',
                                'branch_repo'=>$branch_repo_name ?? '',
                                'product'=>$row->product->name ?? '',
                                'bucket'=>$row->product->bucket ?? '',
                                'collection_manager'=>$report['collection_manager_name'],
                                'CM_email_id'=>$report['collection_manager_email'],
                                'CM_emp_code'=>$report['collection_manager_emp_id'],
                                'CM_mbl_no'=>$report['collection_manager_mobile'],
                                'area_manager'=>$report['area_manager_name'],
                                'ACM_email_id'=>$report['area_manager_email'],
                                'ACM_emp_code'=>$report['area_manager_emp_id'],
                                'ACM_mbl_no'=>$report['area_manager_mobile'],
                                'zone_manager'=>$report['zone_manager_name'],
                                'ZCM_email_id'=>$report['zone_manager_email'],
                                'ZCM_emp_code'=>$report['zone_manager_emp_id'],
                                'ZCM_mbl_no'=>$report['zone_manager_mobile'],
                                'regional_manager'=>$report['regional_manager_name'],
                                'RCM_email_id'=>$report['regional_manager_email'],
                                'RCM_emp_code'=>$report['regional_manager_emp_id'],
                                'RCM_mbl_no'=>$report['regional_manager_mobile'],
                                'national_manager'=>$report['national_manager_name'],
                                'NCM_email_id'=>$report['national_manager_email'],
                                'NCM_emp_code'=>$report['national_manager_emp_id'],
                                'NCM_mbl_no'=>$report['national_manager_mobile'],
                                'group_head_manager'=>$report['group_head_name'],
                                'GH_email_id'=>$report['group_head_email'],
                                'GH_emp_code'=>$report['group_head_emp_id'],
                                'GH_mbl_no'=>$report['group_head_mobile'],
                                'branch_location'=>$branch ?? '',
                                'branch_address'=>$address ?? '',
                                'branch_id'=>$branchid ?? '',
                                'agency_id'=>$agency_id ?? '',
                                'agency_manager'=>$agency_mananger ?? '',
                                'agency_location'=>$agency_location ?? '',
                                'agency_address'=>$agency_address ?? '',
                                'yard_id'=>$yard_id ?? '',
                                'yard_location'=>$yard_location ?? '',
                                'yard_address'=>$yard_address ?? '',
                                 'branch_repo_address'=>$branch_repo_address ?? '',
                                'branch_repo_id'=>$branch_repo_id ?? '',
                                'agency_repo_address'=>$agency_repo_address ?? '',
                                'agency_repo_id'=>$agency_repo_id ?? '',
                                'auditor_email_id'=>$ids[$row->id]->user->email ?? '',
                                'audit_type'=>$row->qmsheet->type ?? '',
                                'audit_assign_date'=>$row->created_at ?? '',
                                'audit_latitute'=>$row->latitute ?? '',
                                'audit_longitude'=>$row->longitute ?? '',
                                'audit_submit_approved_on'=>$row->created_at ?? '',
                                'main_parameter'=>$value->parameter_detail->parameter ?? '',
                                'sub_parameter'=>$value->sub_parameter_detail->sub_parameter ?? '',
                                'qa_name'=>$row->qa_qtl_detail->name ?? '',
                                'audit_submit_date'=>$row->created_at,
                                'weightage'=>$value->sub_parameter_detail->weight ?? '0',
                                'qa_scorable'=>($value->score!='N/A')?($value->sub_parameter_detail->weight ?? '') : ($value->score ?? ''),
                                'qa_scored'=>$value->score ?? '',
                                'qa_remark'=>$value->remark ?? '',
                                'qc_name'=>$ids[$row->id]->user->name  ?? '',
                                'audit_approved_on'=>$row->created_at,
                                'audit_status'=>$status,
                                'audit_observation'=>(($value->option_selected=='Percentage')?$value->option_selected.'('.$value->selected_per.')':$obs) ?? '',
                               'qc_scorable'=>($qcresult->selected_option!='N/A')?($qcresult->sub_parameter_detail->weight ?? '') : ($qcresult->score ?? ''),
                               'qc_scored'=>$qcresult->score ?? '',
                               'qc_remark'=>$qcresult->remark ?? '',       

                        ];

                        }  

                    }

                    

                }
               //echo "nooo";
               // dd($final);

                return $final;

        }

        else

        { 

                $final=[];//echo "-----";
            
                foreach($data as $k=>$row){
                    //dd($row);

                    $name=$branch=$state=$region=$city='';
                     $report=[];
                     //$report=$this->getReportingManager(6,2);
                     //dd($report);

                    switch ($row->qmsheet->type) {

                        case 'agency':

                            $agency_name=$row->agency->name ?? '';

                            $branch=$row->agency->branch->name ?? '';
                            $agency_location=$row->agency->location ?? '';
                            $agency_address=$row->agency->address ?? '';
                            $agency_id=$row->agency->agency_id;
                            $agency_mananger=$row->agency_mananger;

                            $branchid=$row->agency->branch->id ?? '';

                            $state=$row->agency->branch->city->state->name ?? '';

                            $region=$row->agency->branch->city->state->region->name ?? '';
                            $city=$row->agency->branch->city->name ?? '';

                            $report=$this->getReportingManager($branchid,$row->product_id);

                            break;

                        case 'branch':

                            $branch=$row->branch->name ?? '';

                            $branch_name=$branch;
                            $address=$row->branch->location ?? '';

                            $branchid=$row->branch->id ?? '';

                            $state=$row->branch->city->state->name ?? '';

                            $region=$row->branch->city->state->region->name ?? '';
                            $city=$row->branch->city->name ?? '';

                            $report=$this->getReportingManager($branchid,$row->product_id);

                            break;

                        case 'repo_yard':

                            $repo_yard_name=$row->yard->name ?? '';

                            $branch=$row->yard->branch->name ?? '';
                            $yard_location=$row->yard->location ?? '';
                            $yard_id=$row->yard->yard_id ?? '';
                            $yard_address=$row->yard->address ?? '';
                            $branchid=$row->yard->branch->id ?? '';

                            $state=$row->yard->branch->city->state->name ?? '';
                            $city=$row->yard->branch->city->name ?? '';

                            $region=$row->yard->branch->city->state->region->name ?? '';

                            $report=$this->getReportingManager($branchid,$row->product_id);

                            break;

                        case 'branch_repo':

                            $branch_repo_name=$row->branchRepo->name ?? '';
                            $branch_repo_address=$row->branchRepo->location ?? '';
                            $branch_repo_id=$row->branchRepo->location ?? '';

                            $branch=$row->branchRepo->branch->name ?? '';

                            $branchid=$row->branchRepo->branch->id ?? '';

                            $state=$row->branchRepo->branch->city->state->name ?? '';
                            $city=$row->branchRepo->branch->city->name ?? '';

                            $region=$row->branchRepo->branch->city->state->region->name ?? '';

                            $report=$this->getReportingManager($branchid,$row->product_id);

                            break;

                        case 'agency_repo':

                            $agency_repo_name=$row->agencyRepo->name ?? '';
                            $agency_repo_address=$row->agencyRepo->location ?? '';
                            $agency_repo_id=$row->agencyRepo->agency_repo_id ?? '';

                            $branch=$row->agencyRepo->branch->name ?? '';

                            $branchid=$row->agencyRepo->branch->id ?? '';

                            $state=$row->agencyRepo->branch->city->state->name ?? '';
                            $city=$row->agencyRepo->branch->city->name ?? '';

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
                    //dd($row->audit_results);
                    
                         $score_val=$obs=$qc_score_val='';
                        foreach($row->audit_results as $k=>$value){
                             $qcresult= $this->qcdata($value->audit_id,$value->parameter_id,$value->sub_parameter_id);
                             // $qc_score_val='';
                             // if(strtolower($qcresult->option_selected) == 'percentage')
                             //    { //echo "####".$value->selected_per."*****<br>";
                             //        if($qcresult->selected_per == 100)
                             //        {
                             //          $qc_score_val= $qcresult->sub_parameter_detail->weight;  
                             //        }elseif($qcresult->selected_per == 0)
                             //        {
                             //            //echo "sxd";
                             //            $qc_score_val= '0';
                             //        }else
                             //        {
                             //            $qc_score_val=round(($qcresult->sub_parameter_detail->weight * $qcresult->selected_per)/100,1);
                             //        }
                             //    }else
                             //    {
                             //        $qc_score_val= $qcresult->score;
                             //    }
                             //    if($qcresult->selected_option == 'N/A')
                             //    {
                             //     $qc_score_val='N/A';
                             //    }
                            //print_r($value->option_selected);
                           
                            if(strtolower($value->option_selected) == 'percentage')
                                { //echo "####".$value->selected_per."*****<br>";
                                    if($value->selected_per == 100)
                                    {
                                      $score_val= $value->sub_parameter_detail->weight;  
                                    }elseif($value->selected_per == 0)
                                    {
                                        //echo "sxd";
                                        $score_val= '0';
                                    }else
                                    {
                                        $score_val=round(($value->sub_parameter_detail->weight * $value->selected_per)/100,1);
                                    }
                                }else
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

                                $month=\Carbon\Carbon::parse($row->created_at)->formatLocalized("%b'%y");
                            // echo "here: ".$score_val."  end<br>";

                            $final[]=[

                                'month'=>$month ?? '',
                                'lob'=>$row->qmsheet->lob ?? '',
                                'zone'=>$region ?? '',
                                'state'=>$state ?? '',
                                'city'=>$city ?? '',
                                'branch_name'=>$branch_name ?? '',
                                'agency_name'=>$agency_name ?? '',
                                'yard_name'=>$repo_yard_name ?? '',
                                'repo_agency'=>$agency_repo_name ?? '',
                                'branch_repo'=>$branch_repo_name ?? '',
                                'product'=>$row->product->name ?? '',
                                'bucket'=>$row->product->bucket ?? '',
                                'collection_manager'=>$report['collection_manager_name'],
                                'CM_email_id'=>$report['collection_manager_email'],
                                'CM_emp_code'=>$report['collection_manager_emp_id'],
                                'CM_mbl_no'=>$report['collection_manager_mobile'],
                                'area_manager'=>$report['area_manager_name'],
                                'ACM_email_id'=>$report['area_manager_email'],
                                'ACM_emp_code'=>$report['area_manager_emp_id'],
                                'ACM_mbl_no'=>$report['area_manager_mobile'],
                                'regional_manager'=>$report['regional_manager_name'],
                                'RCM_email_id'=>$report['regional_manager_email'],
                                'RCM_emp_code'=>$report['regional_manager_emp_id'],
                                'RCM_mbl_no'=>$report['regional_manager_mobile'],
                                'zone_manager'=>$report['zone_manager_name'],
                                'ZCM_email_id'=>$report['zone_manager_email'],
                                'ZCM_emp_code'=>$report['zone_manager_emp_id'],
                                'ZCM_mbl_no'=>$report['zone_manager_mobile'],
                                'national_manager'=>$report['national_manager_name'],
                                'NCM_email_id'=>$report['national_manager_email'],
                                'NCM_emp_code'=>$report['national_manager_emp_id'],
                                'NCM_mbl_no'=>$report['national_manager_mobile'],
                                'group_head_manager'=>$report['group_head_name'],
                                'GH_email_id'=>$report['group_head_email'],
                                'GH_emp_code'=>$report['group_head_emp_id'],
                                'GH_mbl_no'=>$report['group_head_mobile'],
                                'branch_location'=>$branch ?? '',
                                'branch_address'=>$address ?? '',
                                'branch_id'=>$branchid ?? '',
                                'agency_id'=>$agency_id ?? '',
                                'agency_manager'=>$agency_mananger ?? '',
                                'agency_location'=>$agency_location ?? '',
                                'agency_address'=>$agency_address ?? '',
                                'yard_id'=>$yard_id ?? '',
                                'yard_location'=>$yard_location ?? '',
                                'yard_address'=>$yard_address ?? '',
                                'branch_repo_address'=>$branch_repo_address ?? '',
                                'branch_repo_id'=>$branch_repo_id ?? '',
                                'agency_repo_address'=>$agency_repo_address ?? '',
                                'agency_repo_id'=>$agency_repo_id ?? '',
                                'auditor_email_id'=>$ids[$row->id]->user->email ?? '',
                                'audit_type'=>$row->qmsheet->type ?? '',
                                'audit_assign_date'=>$row->created_at ?? '',
                                'audit_latitute'=>$row->latitute ?? '',
                                'audit_longitude'=>$row->longitute ?? '',
                                'audit_submit_approved_on'=>$row->created_at ?? '',
                                'main_parameter'=>$value->parameter_detail->parameter ?? '',
                                'sub_parameter'=>$value->sub_parameter_detail->sub_parameter ?? '',
                                'qa_name'=>$row->qa_qtl_detail->name ?? '',
                                'audit_submit_date'=>$row->created_at,
                                'weightage'=>$value->sub_parameter_detail->weight ?? '0',
                                'qa_scorable'=>($value->selected_option!='N/A')?($value->sub_parameter_detail->weight ?? '') : ($score_val ?? ''),
                                'qa_scored'=>isset($score_val) ? $score_val : '',
                                'qa_remark'=>$value->remark ?? '',
                                'qc_name'=>$ids[$row->id]->user->name  ?? '',
                                'audit_approved_on'=>$row->created_at,
                                'audit_status'=>$status,
                                'audit_observation'=>(($value->option_selected=='Percentage')?$value->option_selected.'('.$value->selected_per.')':$obs) ?? '',
                               'qc_scorable'=>($qcresult->selected_option!='N/A')?($qcresult->sub_parameter_detail->weight ?? '') : ($qcresult->score ?? ''),
                               'qc_scored'=>isset($qcresult->score) ? $qcresult->score : '',
                               'qc_remark'=>$qcresult->remark ?? '',      

                        ];
                       
                        }     

                }
                //dd($final);
                return $final;

        }

    }

}