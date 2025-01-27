<?php

namespace App\Imports;

use App\Model\AuditAllocation;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Carbon\Carbon;
use App\Agency;
use App\Model\Products;
use App\Model\Productattribute;
use App\User;
use App\Model\AgencyMobileEmail;
use App\Model\Region;
use App\Model\State;
use App\Model\City;
use App\AuditCycle;

class AuditAllocationImport implements ToModel, WithHeadingRow
{
    protected $userId;

    public function __construct($userId)
    {
        $this->userId = $userId;
    }

    public function model(array $row)
    {
        // Check required fields
        if (empty($row['final_agency_name']) || empty($row['agency_code']) || empty($row['product']) || empty($row['sub_product']) || empty($row['process_review_agency_email'])) {
            return null;
        }

        // Validate process_review_period
        if (isset($row['process_review_period']) && !preg_match("/^[A-Za-z]{3}'\d{2}$/", $row['process_review_period'])) {
            throw new \Exception("Error: Invalid process review period format: {$row['process_review_period']}. Expected format: Mon'YY (e.g., Oct'24).");
        }

        
        // Step 1: Check if the product exists. If not, create it.
        $product = Products::firstOrCreate(
            ['name' => $row['product']],
            [
                'type' => 1,
                'bucket' => 'default',
                'is_recovery' => 0,
                'capacity' => 'standard',
                'status' => 0,
            ]
        );

        // Step 2: Handle sub-products, linking them to the product
        $subProductNames = explode(',', $row['sub_product']); // Split sub-products by comma if multiple
        $subProductIds = [];

        foreach ($subProductNames as $subProductName) {
            $subProduct = Productattribute::firstOrCreate(
                [
                    'product_id' => $product->id,
                    'product_attribute_name' => trim($subProductName),
                ],
                [
                    'bucket' => 'default',
                    'type' => 1,
                    'is_recovery' => 0,
                    'status' => 0,
                ]
            );
            $subProductIds[] = $subProduct->id;
        }

        $subProductIdsString = implode(',', $subProductIds); // Join IDs as a comma-separated string

        // Step 3: Check if the agency exists, and create it if it doesn't, associating it with the product and sub-product IDs
        $agency = Agency::where('agency_id', $row['agency_code'])->first();

        if ($agency) {
            // Update agency status if it already exists
            if ($agency->status == 1) {
                $agency->status = 0;
            }
            $agency->sub_product_id = $subProductIdsString;
            $agency->save();
        } else {
            // Create new agency if not found, associating it with the product and sub-product IDs
            $agency = Agency::create([
                'name' => $row['final_agency_name'],
                'agency_id' => $row['agency_code'],
                'product_id' => $product->id, // Link the product ID
                'sub_product_id' => $subProductIdsString, // Save sub-product IDs as comma-separated values
                'location' => $row['location'] ?? null,
                'region_id' => $this->getRegionId($row['region']),
                'state' => $this->getStateId($row['state']),
                'city_id' => $this->getCityId($row['location']),
                'address' => $row['agency_address'] ?? null,
                'status' => 0,
            ]);
        }

        // Step 4: Handle agency contacts
        $this->handleContacts($agency->id, $row['contact'] ?? null, $row['agency_email'] ?? null);

        // Step 5: Get user ID for process review
        $user_id = User::where('email', $row['process_review_agency_email'])->first();

        // Current month and year
        $currentMonth = Carbon::now()->format('m');
        $currentYear = Carbon::now()->format('Y');

       
        
        // Check audit cycle match
        $auditCycle = AuditCycle::where('name', $row['process_review_period'] ?? null)->first();

        if (!$auditCycle) {
            throw new \Exception("Error: No matching audit cycle found for process review period: {$row['process_review_period']}");
        }

        // Step 6: Check if audit allocation exists for the current month and process_review_period -- okay
        $allocation = AuditAllocation::where('agency_code', $agency->agency_id)
        ->where('process_review_period', $row['process_review_period'] ?? null)
        ->first();

        $regionId = $this->getRegionId($row['region']);
        $stateId = $this->getStateId($row['state']);
        $cityId = $this->getCityId($row['location']);

        // Step 7: Update or create audit allocation record
        if ($allocation) {
            // Update existing allocation
            $allocation->update([
                'user_id' => $this->userId,
                'agency_id' => $agency->id,
                'final_agency_name' => $row['final_agency_name'] ?? null,
                'agency_code' => $agency->agency_id,
                'type_of_agency' => $row['type_of_agency'] ?? null,
                'sub_product_id' => $subProductIdsString,
                'sub_product' => $row['sub_product'] ?? null,
                'product_id' => $product->id,
                'product' => $row['product'] ?? null,
                'location' => $row['location'] ?? null,
                'city_id' => $cityId,
                'state' => $row['state'] ?? null,
                'state_id' => $stateId,
                'region' => $row['region'] ?? null,
                'region_id' => $regionId,
                'process_review_agency' => $row['process_review_agency'] ?? null,
                'process_review_agency_email' => $row['process_review_agency_email'] ?? null,
                'process_review_agency_id' => $user_id->id ?? null,
                'process_review_period' => $row['process_review_period'] ?? null,
                'audit_cycle_id' => $auditCycle->id,
                'agency_address' => $row['agency_address'] ?? null,
                'contact' => $row['contact'] ?? null,
                'agency_email' => $row['agency_email'] ?? null,
            ]);
        } else {
            // Create new audit allocation
            return new AuditAllocation([
                'user_id' => $this->userId,
                'agency_id' => $agency->id,
                'final_agency_name' => $row['final_agency_name'] ?? null,
                'agency_code' => $agency->agency_id,
                'type_of_agency' => $row['type_of_agency'] ?? null,
                'sub_product_id' => $subProductIdsString,
                'sub_product' => $row['sub_product'] ?? null,
                'product_id' => $product->id,
                'product' => $row['product'] ?? null,
                'location' => $row['location'] ?? null,
                'city_id' => $cityId,
                'state' => $row['state'] ?? null,
                'state_id' => $stateId,
                'region' => $row['region'] ?? null,
                'region_id' => $regionId,
                'process_review_agency' => $row['process_review_agency'] ?? null,
                'process_review_agency_email' => $row['process_review_agency_email'] ?? null,
                'process_review_agency_id' => $user_id->id ?? null,
                'process_review_period' => $row['process_review_period'] ?? null,
                'audit_cycle_id' => $auditCycle->id,
                'agency_address' => $row['agency_address'] ?? null,
                'contact' => $row['contact'] ?? null,
                'agency_email' => $row['agency_email'] ?? null,
            ]);
        }
    }
    protected function handleContacts($agencyId, $contact, $agencyEmail)
    {
        if (isset($contact) && !empty($contact)) {
            $mobileNumbers = explode(',', $contact);
            AgencyMobileEmail::where('agency_id', $agencyId)->whereNotIn('mobile_number', $mobileNumbers)->delete();
            
            foreach ($mobileNumbers as $mobileNumber) {
                AgencyMobileEmail::updateOrCreate(
                    ['agency_id' => $agencyId, 'mobile_number' => trim($mobileNumber)]
                );
            }
        }

        if (isset($agencyEmail) && !empty($agencyEmail)) {
            $emails = explode(',', $agencyEmail);
            AgencyMobileEmail::where('agency_id', $agencyId)->whereNotIn('email', $emails)->delete();
            
            foreach ($emails as $email) {
                AgencyMobileEmail::updateOrCreate(
                    ['agency_id' => $agencyId, 'email' => trim($email)]
                );
            }
        }
    }



    protected function getRegionId($regionName)
    {
        if (!empty($regionName)) {
            $region = Region::where('name', $regionName)->first();
            return $region ? $region->id : null;
        }
        return null;
    }

    protected function getStateId($stateName)
    {
        if (!empty($stateName)) {
            $state = State::where('name', $stateName)->first();
            return $state ? $state->id : null;
        }
        return null;
    }

    protected function getCityId($locationName)
    {
        if (!empty($locationName)) {
            $location = City::where('name', $locationName)->first();
            return $location ? $location->id : null;
        }
        return null;
    }
}
