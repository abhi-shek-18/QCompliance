<?php

namespace App\Exports;

use App\Models\User;
use Auth;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use Maatwebsite\Excel\Events\AfterSheet;

class UsersExport implements FromArray, WithEvents, WithStyles
{
    /**
     * @return \Illuminate\Support\Collection
     */
    public function array(): array
    {
        // $data = User::with('roles')->get();

        $userRole = auth()->user()->roles->first();

        // dd(auth()->user()->roles->first());
        if ($userRole->name == 'Client' || $userRole->name =='Super Admin') {
            $data = User::with('roles')->get();
        } else {
            $data = User::with('roles')
                ->where('created_by', auth()->user()->email)
                ->get();
        }


        $final = [
            ['Name', 'Role', 'Email id', 'Phone Number', 'Employee ID', 'Active Status', 'Created At', 'Created By','Audit Agency', 'disable_date', 'auditor_approval_date']
        ];

        foreach ($data as $item) {
            $status = $item->active_status == '0' ? 'Activate' : 'Deactivate';
            $audit_agency= User::where('email',$item->created_by)->pluck('name')->first();
            $final[] = [
                'name' => $item->name,
                'role' => implode(',', $item->roles->pluck('name')->toArray()),
                'email' => $item->email,
                'phone_number' => $item->mobile,
                'employee_id' => $item->employee_id,
                'active_status' => $status,
                'created_at' => $item->created_at,
                'created_by' => $item->created_by,
                'audit_agency' => $audit_agency,
                'disable_date' => $item->disable_date ? date("d-m-Y", strtotime($item->disable_date)) : null,
                'auditor_approval_date' => $item->approval_date ? date("d-m-Y", strtotime($item->approval_date)) : null,

            ];
        }

        return $final;
    }

    /**
     * @return array
     */
    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $cellRange = 'A1:G1'; // All headers
                $sheet->getStyle($cellRange)->getFont()->setBold(true);

                $highestRow = $sheet->getHighestRow();
                for ($row = 2; $row <= $highestRow; $row++) {
                    $statusCell = 'F' . $row;
                    $status = $sheet->getCell($statusCell)->getValue();

                    if ($status == 'Activate') {
                        $sheet->getStyle($statusCell)->applyFromArray([
                            'fill' => [
                                'fillType' => Fill::FILL_SOLID,
                                'startColor' => ['rgb' => '00FF00'], // Green
                            ],
                        ]);
                    } elseif ($status == 'Deactivate') {
                        $sheet->getStyle($statusCell)->applyFromArray([
                            'fill' => [
                                'fillType' => Fill::FILL_SOLID,
                                'startColor' => ['rgb' => 'FF0000'], // Red
                            ],
                        ]);
                    }
                }
            }
        ];
    }

    /**
     * @param Worksheet $sheet
     * @return array
     */
    public function styles(Worksheet $sheet)
    {
        return [
            // Style the first row as bold text.
            1 => ['font' => ['bold' => true]],
        ];
    }
}
