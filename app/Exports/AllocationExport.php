<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use App\Model\Allocation;
class AllocationExport implements FromArray,WithHeadings
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function array(): array
    {
        $data=Allocation::with(['user','sheet'])->get();
        $final=[];
        foreach($data as $item){
            $final[]=[
                'sheet_name'=>$item->sheet->name,
                'user_name'=>$item->user->name,
            ];
        }
        return $final;
    }
    public function headings(): array
    {
        return [
            'Sheet Name',
            'User_Name',            
        ];
    }
}
