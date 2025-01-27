<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use App\Model\AgencyRepo;
class AgencyRepoExport implements FromArray,WithHeadings
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function array(): array
    {
        //
        $data=AgencyRepo::with(['branch.branchable'=>function($q){
            $q->with('product')->where('type','Collection_Manager');
        },'branch.city.state.region'])->get();
    $final=[];
        foreach($data as $item){
            foreach($item->branch->branchable as $val){
                if($val->product_id==$item->product_id){
                    $final[]=[
                        'LOB'=>$item->branch->lob ?? '',
                        'Zone'=>$item->branch->city->state->region->name ?? '',
                        'Agency_Code'=>$item->agency_id ?? 'N\A',
                        'Agency_Name'=>$item->name ?? 'N\A',
                        'Agency_Location'=>$item->location ?? 'N\A',
                        'Agency_Address'=>$item->addresss ?? 'N\A',
                        'Branch'=>$item->branch->name ?? 'N\A',
                        'Product'=>$val->product->name ?? '',
                        'Collection_manager_name'=>$val->user->name ?? '', 
                    ];
                }
            }
        }
        return $final;
    }
    public function headings(): array
    {
        return [
            'LOB',
            'Zone',
            'Agency_Code',
            'Agency_Name',
            'Agency_Location',
            'Agency_Address',
            'Branch',
            'Product',
            'Collection_manager_name',
        ];
    }
}
