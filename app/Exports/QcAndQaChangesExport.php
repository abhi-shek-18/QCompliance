<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use App\QcResult;
use App\Qc;
use App\Audit;
use App\AuditQc;
use App\AuditResult;
use App\Model\Branchable;
use App\Exports\QaAndQcChangesSheet1;
use App\Exports\QaAndQcChangesSheet2;
use Maatwebsite\Excel\Concerns\WithHeadings;
class QcAndQaChangesExport implements WithMultipleSheets
{

     public function __construct(array $filterdata)

    {

        $this->filterdata = $filterdata;

    }
    public function sheets(): array
    {
        $sheets = [];
        $sheets[] = new QaAndQcChangesSheet1($this->filterdata);
        $sheets[] = new QaAndQcChangesSheet2($this->filterdata);

    return $sheets;
    }
}
