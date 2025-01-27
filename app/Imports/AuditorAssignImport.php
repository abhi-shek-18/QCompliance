<?php

namespace App\Imports;

use PhpOffice\PhpSpreadsheet\Shared\Date;
use Carbon\Carbon;
use App\Model\Agency;
use App\Model\AuditorAssign;
use App\User;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithUpserts;

class AuditorAssignImport implements ToModel, WithHeadingRow, WithUpserts
{
    
    protected $userId;
    protected $duplicates = [];

   
    public function __construct($userId)
    {
        $this->userId = $userId;
    }

    public function model(array $row)
    {
        
        // V Validate that process_review_agency_email is not blank
        if (empty($row['process_review_agency_email'])) {
            // Optionally log or track rows with missing process_review_agency_email
            \Log::warning('Missing process_review_agency_email for row: ', $row);
            return null;
        }

        if (!isset($row['final_agency_name']) || empty($row['agency_code'])) {
            return null;
        }

        // Check if process_review_agency_email exists in the users table and get user ID
        $processReviewAgencyEmail = $row['process_review_agency_email'];
        $user = User::where('email', $processReviewAgencyEmail)->first();
        $processReviewAgencyId = $user ? $user->id : null;


        $currentMonth = Carbon::now()->format('m');
        $currentYear = Carbon::now()->format('Y');

        $auditorAssign = AuditorAssign::where('agency_code', $row['agency_code'])
        ->whereYear('created_at', $currentYear)
        ->whereMonth('created_at', $currentMonth)
        ->where('status', 1)
        ->first();

        $auditDate = isset($row['audit_date']) ? $this->transformDate($row['audit_date']) : null;
        

        if ($auditorAssign) {
        $auditorAssign->update([
            'user_id' => $this->userId,
            'agency_id' => $row['agency_id'] ?? null,
            'final_agency_name' => $row['final_agency_name'] ?? null,
            'agency_code' => $row['agency_code'] ?? null,
            'type_of_agency' => $row['type_of_agency'] ?? null,
            'sub_product_id' => $row['sub_product_id'] ?? null,
            'sub_product' => $row['sub_product'] ?? null,
            'product_id' => $row['product_id'] ?? null,
            'product' => $row['product'] ?? null,
            'location' => $row['location'] ?? null,
            'state' => $row['state'] ?? null,
            'region' => $row['region'] ?? null,
            'process_review_agency' => $row['process_review_agency'] ?? null,
            'process_review_agency_id' => $processReviewAgencyId,
            'process_review_agency_email' => $row['process_review_agency_email'] ?? null,
            'process_review_period' => $row['process_review_period'] ?? null,
            'audit_cycle_id' => $row['audit_cycle_id'] ?? null,
            'agency_address' => $row['agency_address'] ?? null,
            'contact' => $row['contact'] ?? null,
            'agency_email' => $row['agency_email'] ?? null,
            'auditor_name' => $row['auditor_name'] ?? null,
            'auditor_email' => $row['auditor_email'] ?? null,
            //'audit_date' => $row['audit_date'] ?? null,
            'audit_date' => $auditDate,
        ]);
        return $auditorAssign;
        } else {
            // If no existing record, create a new one
            return AuditorAssign::create([
                'user_id' => $this->userId,
                'agency_id' => $row['agency_id'] ?? null,
                'final_agency_name' => $row['final_agency_name'] ?? null,
                'agency_code' => $row['agency_code'] ?? null,
                'type_of_agency' => $row['type_of_agency'] ?? null,
                'sub_product_id' => $row['sub_product_id'] ?? null,
                'sub_product' => $row['sub_product'] ?? null,
                'product_id' => $row['product_id'] ?? null,
                'product' => $row['product'] ?? null,
                'location' => $row['location'] ?? null,
                'state' => $row['state'] ?? null,
                'region' => $row['region'] ?? null,
                'process_review_agency' => $row['process_review_agency'] ?? null,
                'process_review_agency_id' => $processReviewAgencyId,
                'process_review_agency_email' => $row['process_review_agency_email'] ?? null,
                'process_review_period' => $row['process_review_period'] ?? null,
                'audit_cycle_id' => $row['audit_cycle_id'] ?? null,
                'agency_address' => $row['agency_address'] ?? null,
                'contact' => $row['contact'] ?? null,
                'agency_email' => $row['agency_email'] ?? null,
                'auditor_name' => $row['auditor_name'] ?? null,
                'auditor_email' => $row['auditor_email'] ?? null,
                //'audit_date' => $row['audit_date'] ?? null,
                'audit_date' => $auditDate,
            ]);
        }
    }

    private function transformDate($value)
    {
        if (is_numeric($value)) {
            return Date::excelToDateTimeObject($value)->format('Y-m-d');
        }

        try {
            return Carbon::parse($value)->format('Y-m-d');
        } catch (\Exception $e) {
            return null;
        }
    }

    public function uniqueBy()
    {
        return 'agency_code';
    }

}

