<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use App\Agency;
class AgencyExport implements FromArray,WithHeadings
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function array(): array
    {
        //
        // $data=Agency::with(['branch.branchable'=>function($q){
        //     $q->with('product')->where('type','Collection_Manager');
        // },'branch.city.state.region'])->get();
        $data=Agency::with('User')->where('status',0)->get();

        // dd($data->first());
        $final=[];
        foreach($data as $item){
            //foreach($item->branch->branchable as $val){
                $final[]=[
                    'LOB'=>$item->lob ?? '',
                    'Zone'=>$item->region ?? '',
                    'Agency_Code'=>$item->agency_id,
                    'Agency_Name'=>$item->name,
                    'Agency_Location'=>$item->location,
                    'Agency_Address'=>$item->addresss,
                    'Collection_manager_name'=>$item->user->name ?? '', 
                ];
           // }
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
