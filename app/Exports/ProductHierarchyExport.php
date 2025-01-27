<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use App\Model\Branchable;
class ProductHierarchyExport implements FromArray,WithHeadings
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function array(): array
    {
        //
        $data=Branchable::with(['branch','product','user'])->where('type','!=','Collection_Manager')->get();
        $productUserType=$data->pluck(['type'])->unique();
        $finaldata=[];
        $productUser=[];
        $productBucket=[];
        foreach ($data as $key => $value) {
            if($value->branch!=null){
                $productUser[$value->branch->name ?? $value->branch_id][$value->product->name ?? $value->product_id][$value->type]=$value; 
                $productBucket[$value->branch->name ?? $value->branch_id][$value->product->name ?? $value->product_id]['bucket']=$value->bucket;
            }
            // dd($value);
        }
        foreach($productUser as $k=>$val){
            foreach($val as $key=>$row){
                $finaldata[]=[
                    'Product_name'=>$key ?? '',
                    'Product_Bucket'=>$productBucket[$k][$key]['bucket'] ?? '',
                    'Branch_Name'=>$k ?? '',
                    'Area_Collection_Manager'=>$row['Area_Collection_Manager']->user->name ?? 'N/A',
                    'Regional_Collection_Manager'=>$row['Regional_Collection_Manager']->user->name ?? 'N/A',
                    'Zonal_Collection_Manager'=>$row['Zonal_Collection_Manager']->user->name ?? 'N/A',
                    'National_Collection_Manager'=>$row['National_Collection_Manager']->user->name ?? 'N/A',
                    'Group_Product_Head'=>$row['Group_Product_Head']->user->name ?? 'N/A',
                    'Head_of_the_Collections'=>$row['Head_of_the_Collections']->user->name ?? 'N/A'
                ];
            }
        }
        return $finaldata;
    }
    public function headings(): array
    {
        return [
            'Product_name',
            'Product_Bucket',
            'Branch_Name',
            'Area_Collection_Manager',
            'Regional_Collection_Manager',
            'Zonal_Collection_Manager',
            'National_Collection_Manager',
            'Group_Product_Head',
            'Head_of_the_Collections'
        ];
    }
}
