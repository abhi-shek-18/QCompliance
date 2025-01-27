<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use App\Model\Branch;
use App\Model\BranchRepo;
use App\Model\Branchable;

class BranchRepoExport implements FromArray,WithHeadings
{
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
        //
        $data=BranchRepo::with(['branch.branchable'=>function($q){
            $q->with('product')->where('type','Collection_Manager');
        },'branch.city.state.region'])->get();
        // dd($data->first());
        $final=[];
        foreach($data as $item){
            foreach($item->branch->branchable as $val){
                $report=$this->getReportingManager($item->branch_id,$val->product_id);
                $final[]=[
                    'LOB'=>$item->branch->lob ?? '',
                    'Zone'=>$item->branch->city->state->region->name ?? '',
                    'Branch_repo'=>$item->name,
                    'Branch'=>$item->branch->name ?? '',
                    'Product'=>$val->product->name ?? '',
                    'Bucket'=>$val->bucket,
                    'Collection_Manager_Name'=>$val->user->name ?? '',
                    'Branch_Location'=>$item->location,
                    'Branch_Address'=>$item->location,
                    'Reporting_manager'=>$report['name'],
                    'Designation'=>$report['degination']
                ];
            }
        }
        return $final;
    }
    public function headings(): array
    {
        return [
            'LOB',
            'Zone',
            'Branch_repo',
            'Branch',
            'Product',
            'Bucket',
            'Collection_Manager_Name',
            'Branch_Location',
            'Branch_Address',
            'Reporting_manager',
            'Designation',
        ];
    }
}
