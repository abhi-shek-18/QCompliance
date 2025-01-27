<?php
namespace App\Imports;

use App\Agency;
use App\Model\Region;
use App\Model\State;
use App\Model\City;
use App\Model\AgencyMobileEmail;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

// class AgencyImport implements ToModel, WithHeadingRow
// {
//     /**
//     * @param array $row
//     *
//     * @return \Illuminate\Database\Eloquent\Model|null
//     */
//     public function model(array $row)
//     {
//         // Ensure that the 'agencyname' field is present
//         if (!isset($row['agencyname']) || empty($row['agencyname'])) {
//             // Skip this row or handle it as per your requirements
//             return null;
//         }
    
//         // Check for region, state, and city from respective tables
//         $region = Region::where('name', $row['region'])->first();
//         $state = State::where('name', $row['state'])->first();
//         $city = City::where('name', $row['city'])->first();
//         return new Agency([
//             'name' => $row['agencyname'], // Now it's guaranteed not to be null
//             'email' => isset($row['email']) ? $row['email'] : null,
//             'mobile_number' => isset($row['mobilenumber']) ? $row['mobilenumber'] : null,
//             'region_id' => optional($region)->id, // Will be null if not found
//             'state' => optional($state)->id,      // Will be null if not found
//             'city_id' =>  optional($city)->id,      // Will be null if not found
//             'agency_id' => isset($row['code']) ? $row['code'] : null,
//             'agency_manager' => isset($row['agency_manager']) ? $row['agency_manager'] : null,
//             'location' => isset($row['location']) ? $row['location'] : null,
//             'address' => isset($row['address']) ? $row['address'] : null,
//             'agency_phone' => isset($row['mobilenumber']) ? $row['mobilenumber'] : null,
//         ]);
//     }
// }


class AgencyImport implements ToModel, WithHeadingRow
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        // Ensure that the 'agencyname' field is present
        if (!isset($row['agencyname']) || empty($row['agencyname'])) {
            // Skip this row or handle it as per your requirements
            return null;
        }

        // Check for region, state, and city from respective tables
        $region = Region::where('name', $row['region'])->first();
        $state = State::where('name', $row['state'])->first();
        $city = City::where('name', $row['city'])->first();

        // First, create the agency and get the ID
        $agency = Agency::create([
            'name' => $row['agencyname'],
            'region_id' => optional($region)->id,
            'state' => optional($state)->id,
            'city_id' => optional($city)->id,
            'agency_id' => isset($row['code']) ? $row['code'] : null,
            // 'agency_manager' => isset($row['agency_manager']) ? $row['agency_manager'] : null,
            'location' => isset($row['location']) ? $row['location'] : null,
            'address' => isset($row['address']) ? $row['address'] : null,
        ]);

        // Handle multiple mobile numbers
        if (isset($row['mobilenumber']) && !empty($row['mobilenumber'])) {
            $mobileNumbers = explode(',', $row['mobilenumber']);
            foreach ($mobileNumbers as $mobileNumber) {
                AgencyMobileEmail::create([
                    'agency_id' => $agency->id,
                    'mobile_number' => trim($mobileNumber),
                    'email' => null, // Set email to null
                ]);
            }
        }

        // Handle multiple emails
        if (isset($row['email']) && !empty($row['email'])) {
            $emails = explode(',', $row['email']);
            foreach ($emails as $email) {
                AgencyMobileEmail::create([
                    'agency_id' => $agency->id,
                    'mobile_number' => null, // Set mobile_number to null
                    'email' => trim($email),
                ]);
            }
        }

        return $agency;
    }
}