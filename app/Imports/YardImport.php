<?php

namespace App\Imports;

use App\Yard;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class YardImport implements ToModel, WithHeadingRow
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        return new Yard([
            'branch_id' => isset($row['branch_id']) ? $row['branch_id'] : null,
            'agency_id' => isset($row['agency_id']) ? $row['agency_id'] : null,
            'name' => isset($row['name']) ? $row['name'] : null,
            'yard_id' => isset($row['yard_id']) ? $row['yard_id'] : null,
            'agency_manager' => isset($row['agency_manager']) ? $row['agency_manager'] : null,
            'location' => isset($row['location']) ? $row['location'] : null,
            'addresss' => isset($row['addresss']) ? $row['addresss'] : null,
            'product_id' => isset($row['product_id']) ? $row['product_id'] : null,
        ]);
    }

}
