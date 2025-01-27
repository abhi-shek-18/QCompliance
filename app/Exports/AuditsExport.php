<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Illuminate\Support\Facades\DB;
use App\Audit;
use App\User;
use App\QmSheetSubParameter;

class AuditsExport implements FromQuery, WithHeadings, WithMapping, WithEvents
{
    protected $auditAgencyId;
    protected $agencyId;
    protected $startDate;
    protected $endDate;
    protected $dynamicHeadings = [];  

    public function __construct($auditAgencyId, $agencyId, $startDate, $endDate)
    {
        $this->auditAgencyId = $auditAgencyId;
        $this->agencyId = $agencyId;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
    }

    public function query()
    {
            
        $query = Audit::query()
            ->join('agencies', 'audits.agency_id', '=', 'agencies.id')
            ->join('states', 'agencies.state', '=', 'states.id')
            ->join('regions', 'agencies.region_id', '=', 'regions.id')
            ->join('products', 'audits.product_id', '=', 'products.id')
            ->join('audit_cycles', 'audits.audit_cycle_id', '=', 'audit_cycles.id')
            ->leftJoin('audit_results', 'audits.id', '=', 'audit_results.audit_id')
            ->join('users', 'audits.audit_agency_id', '=', 'users.id');

        // Filter by audit agency ID
        if (!empty($this->auditAgencyId) && $this->auditAgencyId !== 'all') {
            $query->where('audits.audit_agency_id', $this->auditAgencyId);
        }

        // Optional: If you want to handle the collection agency as well
        if ($this->agencyId !== 'all' && !empty($this->agencyId)) {
            $query->where('audits.agency_id', $this->agencyId);
        }

        // Apply date filter if both dates are provided
        if ($this->startDate && $this->endDate) {
            $query->whereBetween(DB::raw('DATE(audits.created_at)'), [$this->startDate, $this->endDate]);
        }
        
        return $query->select(
            'audits.id',
            'audits.overall_score',
            'audits.grade',
            'agencies.name as agency_name',
            'agencies.location',
            'agencies.address',
            'agencies.agency_phone',
            'agencies.email',
            'states.name as state_name',
            'regions.name as region_name',
            'products.name as product_name',
            'audit_cycles.name as audit_cycle_month',
            'audits.created_at as process_review_date',
            DB::raw('GROUP_CONCAT(CASE WHEN audit_results.option_selected = "Unsatisfactory" THEN audit_results.remark END SEPARATOR ", ") as unsatisfactory_remarks'),
            'audits.lavel_4', 'audits.lavel_5'
        )
        ->groupBy(
            'audits.id',
            'audits.overall_score',
            'audits.grade',
            'agencies.name',
            'agencies.location',
            'agencies.address',
            'agencies.agency_phone',
            'agencies.email',
            'states.name',
            'regions.name',
            'products.name',
            'audit_cycles.name',
            'audits.created_at',
            'audits.lavel_4', 
            'audits.lavel_5' 
        )
        ->orderBy('audits.id', 'asc');
    }

    public function headings(): array
    {
        
        $baseHeadings = [
            'AGENCY NAME',
            'TYPE OF AGENCY',
            'PRODUCT',
            'LOCATION',
            'Con',
            'AGENCY NAME WITH LOCATION',
            'STATE',
            'REGION',
            'PROCESS REVIEW AGENCY',
            'PROCESS REVIEW PERIOD',
            'ADDRESS',
            'CONTACT NO.',
            'EMAIL',
            'LEVEL 4A',
            'LEVEL 5A',
            'Level 4B',
            'Level 5B',
            'Level 4C',
            'Level 5C',
            'Level 4D',
            'Level 5D',
            'Level 4E',
            'Level 5E',
            'Level 4F',
            'Level 5F',
            'Level 4G',
            'Level 5G',
            'PROCESS REVIEW DATE',
            'Scores',
            'Grade',
        ];

         
        $this->dynamicHeadings = QmSheetSubParameter::join('audits', 'audits.qm_sheet_id', '=', 'qm_sheet_sub_parameters.qm_sheet_id')
        ->when($this->agencyId !== 'all', function ($query) {
            $query->where('audits.agency_id', $this->agencyId);
        })
        ->when($this->startDate && $this->endDate, function ($query) {
            // Apply date filter only if both start and end date are provided
            $query->whereBetween(DB::raw('DATE(audits.created_at)'), [$this->startDate, $this->endDate]);
        })
        ->pluck('qm_sheet_sub_parameters.sub_parameter')
        ->unique()
        ->toArray();

        //return array_merge($baseHeadings, $this->dynamicHeadings); 

        return array_merge($baseHeadings, $this->dynamicHeadings, [
        'PROCESS REVIEW REMARKS',
        'Audit Month ',
        ]);
    }

    public function map($audit): array
    {
        
        $level4Ids = array_filter(array_map('trim', explode(',', $audit->lavel_4)));
        $level4Names = [];

        foreach ($level4Ids as $id) {
            $user = User::find($id);
            if ($user) {
                $level4Names[] = $user->name;
            }
        }

        $level5Ids = array_filter(array_map('trim', explode(',', $audit->lavel_5)));
        $level5Names = [];

        foreach ($level5Ids as $id) {
            $user = User::find($id);
            if ($user) {
                $level5Names[] = $user->name;
            }
        }



        $row = [
            $audit->agency_name ?? '-',
            $audit->agency_type ?? '-',
            $audit->product_name ?? '-',
            $audit->location ?? '-',
            $audit->con ?? '-',
            ($audit->agency_name ?? '-') . ' - ' . ($audit->location ?? '-'),
            $audit->state_name ?? '-',
            $audit->region_name ?? '-',
            $audit->process_review_agency ?? 'Qdegreesv',
            $audit->process_review_period ?? 'Aug-2024',
            $audit->address ?? '-',
            $audit->agency_phone ?? '-',
            $audit->email ?? '-',
        ];
            $row[] = $level4Names[0] ?? '-'; // Level 4A
            $row[] = $level5Names[0] ?? '-'; // Level 5A
            $row[] = $level4Names[1] ?? '-'; // Level 4B
            $row[] = $level5Names[1] ?? '-'; // Level 5B
            $row[] = $level4Names[2] ?? '-'; // Level 4C
            $row[] = $level5Names[2] ?? '-'; // Level 5C
            $row[] = $level4Names[3] ?? '-'; // Level 4D
            $row[] = $level5Names[3] ?? '-'; // Level 5D
            $row[] = $level4Names[4] ?? '-'; // Level 4E
            $row[] = $level5Names[4] ?? '-'; // Level 5E
            $row[] = $level4Names[5] ?? '-'; // Level 4F
            $row[] = $level5Names[5] ?? '-'; // Level 5F
            $row[] = $level4Names[6] ?? '-'; // Level 4G
            $row[] = $level5Names[6] ?? '-'; // Level 5G

            $row[] = $audit->process_review_date ?? '-';
            $row[] = $audit->overall_score ?? '-';
            $row[] = $audit->grade ?? '-';
            
      
            $dynamicData = QmSheetSubParameter::join('audit_results', 'audit_results.sub_parameter_id', '=', 'qm_sheet_sub_parameters.id')
            ->where('audit_results.audit_id', $audit->id)
            ->pluck('audit_results.option_selected', 'qm_sheet_sub_parameters.sub_parameter')
            ->toArray();

            
            foreach ($this->dynamicHeadings as $subParameter) {
                $row[] = $dynamicData[$subParameter] ?? '-';
            }

            //$row[] = $audit->unsatisfactory_remarks ?? 'NIL';  // PROCESS REVIEW REMARKS

            $unsatisfactoryRemarks = $audit->unsatisfactory_remarks ?? 'NIL';  // Default if not present
            $remarksArray = array_filter(array_map('trim', explode(',', $unsatisfactoryRemarks)));

            $numberedRemarks = [];
            foreach ($remarksArray as $index => $remark) {
                $numberedRemarks[] = ($index + 1) . ". " . $remark;  // Indexing starts from 1
            }

            $row[] = implode(', ', $numberedRemarks);
            $row[] = $audit->audit_cycle_month ?? '-';        // Audit Month

            return $row;

    }

    


    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $sheet->getStyle('A1:BL1')->getFont()->setBold(true);

                // Set background color for the header row (A1:AF1)
                $sheet->getStyle('A1:AD1')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                      ->getStartColor()->setARGB('D6DCE4'); // Background color for A1:AD1

                // Set background color for the header row (AG1:BL1)
                $sheet->getStyle('AE1:BJ1')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                      ->getStartColor()->setARGB('FFD966'); // Background color for AG1:CR1

                 // Set background color Blue in the last 2 column
                $sheet->getStyle('BK1:BL1')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                      ->getStartColor()->setARGB('D6DCE4'); // Background color for A1:AD1

                // Set column width for AG:BL
                for ($col = 'AE'; $col <= 'BJ'; $col++) {
                    $sheet->getColumnDimension($col)->setWidth(34);
                }

                $sheet->getRowDimension(1)->setRowHeight(80);
                $sheet->getStyle('A1:BL1')->getAlignment()->setWrapText(true);
                $sheet->getStyle('A1:BL1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER); // Center text horizontally
                $sheet->getStyle('A1:BL1')->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER); // Center text vertically

                // Set borders for all cells
                $sheet->getStyle('A1:BL' . $sheet->getHighestRow())->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                            'color' => ['argb' => 'FF000000'], // Black color
                        ],
                    ],
                ]);

                foreach ($sheet->getColumnIterator() as $column) {
                    $colIndex = $column->getColumnIndex();
                    // Skip AG:CR columns for auto-size
                    if ($colIndex < 'AG' || $colIndex > 'BJ') {
                        $sheet->getColumnDimension($colIndex)->setAutoSize(true);
                    }
                }
            },
        ];
    }


}
