<?php

namespace App\Exports;
use DB;
use App\Model\City;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\FromArray;
class CityExport implements FromArray
{
    /**
    * @return \Illuminate\Support\Collection
    */
    // public function collection()
    // {
    //     $data=City::With('state.region')->get();
    //     $final=[];
    //     foreach($data as $k=>$item){
    //         $final=[
    //             'region'=>$item->state->region->name,
    //             'state'=>$item->state->name,
    //             'name'=>$item->name,
    //     ];
    //     }
    //     return new Collection($final);
    // }
    public function array(): array
    {
        $data=City::With('state.region')->get();
        $final=[];
        foreach($data as $k=>$item){
            $final[]=[
                'region'=>$item->state->region->name,
                'state'=>$item->state->name,
                'name'=>$item->name,
        ];
        }
        return $final;
    }
}
