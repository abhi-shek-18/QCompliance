<?php

namespace App\Imports;

use App\Model\AgencyRepo;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class AgencyRepoImport implements ToModel, WithHeadingRow
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        dd($row);
        return new AgencyRepo([
            'branch_id' => isset($row['branch_id']) ? $row['branch_id'] : null,
            'name' => isset($row['name']) ? $row['name'] : null,
            'product_id' => isset($row['product_id']) ? $row['product_id'] : null,
            'location' => isset($row['location']) ? $row['location'] : null,
            'agency_repo_id' => isset($row['agency_repo_id']) ? $row['agency_repo_id'] : null,
        ]);
    }

}