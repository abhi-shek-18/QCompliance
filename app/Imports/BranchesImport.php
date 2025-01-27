<?php

namespace App\Imports;

use App\Model\Branch;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class BranchesImport implements ToModel, WithHeadingRow
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        return new Branch([
            'manager_id' => isset($row['manager_id']) ? $row['manager_id'] : null,
            'owner_id' => isset($row['owner_id']) ? $row['owner_id'] : null,
            'city_id' => isset($row['city_id']) ? $row['city_id'] : null,
            'name' => isset($row['name']) ? $row['name'] : null,
            'uuid' => isset($row['uuid']) ? $row['uuid'] : null,
            'location' => isset($row['location']) ? $row['location'] : null,
            'lob' => isset($row['lob']) ? $row['lob'] : null,
            'address' => isset($row['address']) ? $row['address'] : null,

        ]);
    }
   
}
