<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use App\Yard;
class YardExport implements FromArray,WithHeadings
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function array(): array
    {
        //
        $data=Yard::with(['branch.branchable'=>function($q){
            $q->with('product')->where('type','Collection_Manager');
        },'branch.city.state.region'])->get();
    $final=[];
        foreach($data as $item){
            foreach($item->branch->branchable as $val){
                    $final[]=[
                        'LOB'=>$item->branch->lob ?? '',
                        'Zone'=>$item->branch->city->state->region->name ?? '',
                        'Yard_Code'=>$item->agency_id ?? 'N\A',
                        'Yard_Name'=>$item->name ?? 'N\A',
                        'Yard_Location'=>$item->location ?? 'N\A',
                        'Yard_Address'=>$item->addresss ?? 'N\A',
                        'Branch'=>$item->branch->name ?? 'N\A',
                        'Product'=>$val->product->name ?? '',
                        'Collection_manager_name'=>$val->user->name ?? '', 
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
            'Yard_Code',
            'Yard_Name',
            'Yard_Location',
            'Yard_Address',
            'Branch',
            'Product',
            'Collection_manager_name',
        ];
    }
}
