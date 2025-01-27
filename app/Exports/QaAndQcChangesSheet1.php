<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Cell\Hyperlink;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use App\QcResult;
use App\Qc;
use App\Audit;
use App\AuditQc;
use App\AuditResult;
use App\Model\Branchable;
use URL;
use Maatwebsite\Excel\Concerns\WithHeadings;
class QaAndQcChangesSheet1 implements FromArray,WithHeadings,WithEvents
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function __construct(array $filterdata)
    {

        $this->filterdata = $filterdata;

    }
    public function registerEvents(): array
    {
        return [
            AfterSheet::class    => function(AfterSheet $event) {
                /** @var Worksheet $sheet */
                foreach ($event->sheet->getColumnIterator('H') as $row) {
                    foreach ($row->getCellIterator() as $cell) {
                        if (str_contains($cell->getValue(), '://')) {
                            $array=explode(',',$cell->getValue());
                            $data=[];
                            foreach($array as $item){
                                $data=new Hyperlink($item, 'Read');
                                $cell->setHyperlink($data);
                            }
                             // Upd: Link styling added
                             $event->sheet->getStyle($cell->getCoordinate())->applyFromArray([
                                'font' => [
                                    'color' => ['rgb' => '0000FF'],
                                    'underline' => 'single'
                                ]
                            ]);
                        }
                    }
                }
            },
        ];
    }
    public function array(): array
    {
        $data=Audit::with(['qmsheet','audit_results.parameter_detail','audit_results.artifact','product','branch.city.state.region','branch.branchable','yard.branch.city.state.region','agency.branch.city.state.region','qa_qtl_detail','branchRepo.branch.city.state.region','agencyRepo.branch.city.state.region','user'])->whereBetween('created_at', [$this->filterdata['start_date'], $this->filterdata['end_date']])->limit(50)->get();
        $final=[];
        foreach($data as $k=>$row){
            $qcdata=AuditResult::where('audit_id',$row->id)->get()->keyBy('sub_parameter_id');
            $qc=Qc::with('user')->where('audit_id',$row->id)->first();
            // dd($qc);
            $name='';
            $branch='';
            $state='';
            $region='';
            $city='';
            $report=[];
            $agency_name='N/A';
            $agency_manager='N/A';
            $agency_address='N/A';
            $agency_phone='N/A';
            $yard_name='N/A';
            $yard_manager='N/A';
            $yard_address='N/A';
            $branch_repo_name='N/A';
            $branch_repo_address='N/A';
            $agency_repo_name='N/A';
            $agency_repo_address='N/A';
            switch ($row->qmsheet->type) {
                case 'agency':
                    $agency_name=$row->agency->name ?? '';
                    $agency_manager=$row->agency->agency_manager ?? '';
                    $agency_phone=$row->agency->agency_phone ?? '';
                    $agency_address=$row->agency->addresss ?? '';
                    $branch=$row->agency->branch->name ?? '';
                    $branchid=$row->agency->branch->id ?? '';
                    $state=$row->agency->branch->city->state->name ?? '';
                    $city=$row->agency->branch->city->name ?? '';
                    $region=$row->agency->branch->city->state->region->name ?? '';
                    $report=$this->getReportingManager($branchid,$row->product_id);
                    break;
                case 'branch':
                    $branch=$row->branch->name ?? '';
                    $name=$branch;
                    $branchid=$row->branch->id ?? '';
                    $state=$row->branch->city->state->name ?? '';
                    $city=$row->branch->city->name ?? '';
                    $region=$row->branch->city->state->region->name ?? '';
                    $report=$this->getReportingManager($branchid,$row->product_id);
                    break;
                case 'repo_yard':
                    $yard_name=$row->yard->name ?? '';
                    $yard_address=$row->yard->addresss ?? '';
                    $yard_manager=$row->yard->agency_manager ?? '';
                    $branch=$row->yard->branch->name ?? '';
                    $branchid=$row->yard->branch->id ?? '';
                    $state=$row->yard->branch->city->state->name ?? '';
                    $city=$row->yard->branch->city->name ?? '';
                    $region=$row->yard->branch->city->state->region->name ?? '';
                    $report=$this->getReportingManager($branchid,$row->product_id);
                    break;
                case 'branch_repo':
                    $branch_repo_name=$row->branchRepo->name ?? '';
                    $branch_repo_address=$row->branchRepo->location ?? '';
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
                    $branch=$row->agencyRepo->branch->name ?? '';
                    $branchid=$row->agencyRepo->branch->id ?? '';
                    $state=$row->agencyRepo->branch->city->state->name ?? '';
                    $city=$row->agencyRepo->branch->city->name ?? '';
                    $region=$row->agencyRepo->branch->city->state->region->name ?? '';
                    $report=$this->getReportingManager($branchid,$row->product_id);
                    break;
                
            }
            switch($qc->status ?? ''){
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
                    $status='Pending';
                break;
            }
            foreach($row->audit_results as $k=>$value){
                $url=[];
               /*foreach($value->artifact as $item){
                    if($item->audit_id==$row->id){
                        $url[]=URL::asset('storage/app/'.$item->file); 
                        // dd($url);
                    }
                }*/
                // URL::asset('storage/app/'.$art->file)
                    $final[]=[
                        'Month'=>\Carbon\Carbon::parse($row->created_at)->formatLocalized("%b'%y"),
                        'Audit_Date'=>$row->created_at,
                        'Audit_type'=>$row->qmsheet->type,
                        'LOB'=>$row->qmsheet->lob,
                        'State'=>$state,
                        'Branch_Name'=>$branch,
                        'Agency_Name'=>$agency_name,
                        'Agency_Manager'=>$agency_manager,
                        'Agency_Address'=>$agency_address,
                        'Agency_Phone'=>$agency_phone,
                        'yard_name'=>$yard_name,
                        'yard_address'=>$yard_address,
                        'yard_manager'=>$yard_manager,
                        'branch_repo_name'=>$branch_repo_name,
                        'branch_repo_address'=>$branch_repo_address,
                        'agency_repo_name'=>$agency_repo_name,
                        'agency_repo_address'=>$agency_repo_address,
                        'Product_Name'=>$row->product->name ?? '',
                        'City_Name'=>$city,
                        'Collection_Manager'=>$report['Collection_Manager']['name'] ?? '',
                        'Collection_Manager_Emp_Code'=>$report['Collection_Manager']['employee_id'] ?? '',
                        'Area_Collection_Manager'=>$report['Area_Collection_Manager']['name'] ?? '',
                        'Regional_Collection_Manager'=>$report['Regional_Collection_Manager']['name'] ?? '',
                        'National_Collection_Manager'=>$report['National_Collection_Manager']['name'] ?? '',
                        'Group_Product_Head'=>$report['Group_Product_Head']['name'] ?? '',
                        'Zonal_Collection_Manager'=>$report['Zonal_Collection_Manager']['name'] ?? '',
                        'Head_of_Collection'=>$report['Head_of_the_Collections']['name'] ?? '',
                        'Audit_Latitude'=>$row->latitude,
                        'Audit_Longitude'=>$row->longitude,
                        'Auditor_Name'=>$row->qa_qtl_detail->name ?? '',
                        'Visited_Date_and_Time'=>$row->created_at ??'',
                        'Audit_Approved_By'=>$qc->user->name ?? '',
                        'Audit_Approved_Date'=>$qc->created_at ?? '',
                        'Status'=>$status,
                        'Parameter'=>$value->parameter_detail->parameter ?? ''
                        //'attachment'=>implode(',',$url)
                        
                        
                ];
            }
        }
        return $final;
    }
    
    public function headings(): array
    {
        return [
            'Month',
            'Audit_Date',
            'Audit_type',
            'LOB',
            'State',
            'Branch_Name',
            'Agency_Name',
            'Agency_Manager',
            'Agency_Address',
            'Agency_Phone',
            'yard_name',
            'yard_address',
            'yard_manager',
            'branch_repo_name',
            'branch_repo_address',
            'agency_repo_name',
            'agency_repo_address',
            'Product_Name',
            'City_Name',
            'Collection_Manager',
            'Collection_Manager_Emp_Code',
            'Area_Collection_Manager',
            'Regional_Collection_Manager',
            'National_Collection_Manager',
            'Group_Product_Head',
            'Zonal_Collection_Manager',
            'Head_of_Collection',
            'Audit_Latitude',
            'Audit_Longitude',
            'Auditor_Name',
            'Visited_Date_and_Time',
            'Audit_Approved_By',
            'Audit_Approved_Date',
            'Status',
            'Parameter'
           // 'Attachment'
        ];
    }
    public function getReportingManager($branch_id,$product){
        // $ids=Branchable::where(['branch_id'=>$branch_id,'product_id'=>$product])->get()->pluck('manager_id');
        $ids=Branchable::with('user')->where(['branch_id'=>$branch_id,'product_id'=>$product])->get();
        $email='';
        $degination='';
        $data=[];
        if($ids->where('type','Collection_Manager')->count()>0){
            $user=$ids->where('type','Collection_Manager')->first();
            $email=$user->user->name ?? '';
            $degination='Collection Manager';
            $data['Collection_Manager']=['name'=>$email,'degination'=>$degination,'employee_id'=>$user->user->employee_id ?? ''];
        }
        if($ids->where('type','Area_Collection_Manager')->count()>0){
            $user=$ids->where('type','Area_Collection_Manager')->first();
            $email=$user->user->name ?? '';
            $degination='Area Collection Manager';
            $data['Area_Collection_Manager']=['name'=>$email,'degination'=>$degination];
        }
        if($ids->where('type','Regional_Collection_Manager')->count()>0){
            $user=$ids->where('type','Regional_Collection_Manager')->first();
            $email=$user->user->name ?? '';
            $degination='Regional Collection Manager';
            $data['Regional_Collection_Manager']=['name'=>$email,'degination'=>$degination];
        }
        if($ids->where('type','Zonal_Collection_Manager')->count()>0){
            $user=$ids->where('type','Zonal_Collection_Manager')->first();
            $email=$user->user->name ?? '';
            $degination='Zonal Collection Manager';
            $data['Zonal_Collection_Manager']=['name'=>$email,'degination'=>$degination];
        }
        if($ids->where('type','National_Collection_Manager')->count()>0){
            $user=$ids->where('type','National_Collection_Manager')->first();
            $email=$user->user->name ?? '';
            $degination='National Collection Manager';
            $data['National_Collection_Manager']=['name'=>$email,'degination'=>$degination];
        }
        if($ids->where('type','Group_Product_Head')->count()>0){
            $user=$ids->where('type','Group_Product_Head')->first();
            $email=$user->user->name ?? '';
            $degination='Group Product Head';
            $data['Group_Product_Head']=['name'=>$email,'degination'=>$degination];
        }
        if($ids->where('type','Head_of_the_Collections')->count()>0){
            $user=$ids->where('type','Head_of_the_Collections')->first();
            $email=$user->user->name ?? '';
            $degination='Head of the Collections';
            $data['Head_of_the_Collections']=['name'=>$email,'degination'=>$degination];
        }
        // dd($degination,$email);
        // return ['name'=>$email,'degination'=>$degination];
        return $data;
    }
}
