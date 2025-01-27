<?php

namespace App\Imports;

// Import necessary models
use App\User;
use App\Model\Branch;
use App\Model\Branchable;
use App\Model\Products;
use App\Model\ProductUser;
use App\Model\City;
use App\Model\BranchRepo;
use App\Model\AgencyRepo;
use App\Model\YardRepo;
use App\Yard;
use App\Agency;
use App\Model\Region;
use Hash;
use Spatie\Permission\Models\Role;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Illuminate\Contracts\Queue\ShouldQueue;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Validators\Failure;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Illuminate\Validation\Rule;

// Define the BranchImport class that implements several interfaces
class BranchImport implements ToModel, WithHeadingRow, WithChunkReading
{
    use Importable; // Provides functionality to import data easily

    private $errors = []; // To store any errors during import

    /**
     * Function to process each row of the Excel file.
     * 
     * @param array $row
     * 
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        // Get the city by name
        $city = City::where('name', $row['city'])->first();

        // Create users based on the row data
        $datauser = $this->createUser($row);

        // Check if the city exists
        if (!isset($city->id)) {
            echo $row['city'] . " City not added";
            die;
        }

        // Get the auditor ID from the email
        $auditor_id = User::where('email', $row['auditor_email_id'])->get()->pluck('id');

        // Update or create a product
        $product = Products::updateOrCreate(
            ['name' => $row['product_name']],
            ['name' => $row['product_name'], 'bucket' => $row['bucket'], 'type' => 0]
        );
       // echo '<pre>'; print_r($product);  die;
        // Update or create a agency
        $region = Region::where('name', $row['lob'])->first();
        $agency = Agency::updateOrCreate(
            [
                'name' => $row['agency_name'] ?? '', // First array should map keys to values
                'region_id' => $region->id // 'lob' also needs to be in the correct format
            ],
            [
                'state' => $row['state'] ?? '',
                'region_id' => optional($region)->id,
                'city_id' => $city->id,
                'branch_id' => null, // Set this temporarily or if agency doesn't need a branch
                'name' => $row['agency_name'],
                'agency_id' => $row['agency_id'] ?? '',
                'agency_manager' => $datauser['Collection_Manager']->id,
                'location' => $row['agency_location'] ?? '',
                'address' => $row['agency_address'] ?? '',
                'product_id' => $product->id
            ]
        );

       
        // Update or create a branch
        // $branch = Branch::updateOrCreate(
        //     ['name' => $row['branch_name'] ?? '', 'lob' => $row['lob']],
        //     [
        //         'lob' => $row['lob'],
        //         'city_id' => $city->id,
        //         'name' => $row['branch_name'] ?? '',
        //         'location' => $row['branch_location'] ?? $row['branch_address'],
        //         'uuid' => $row['branch_id'] ?? null,
        //         'address' => $row['branch_address'],
        //         'manager_id' => $datauser['Collection_Manager']->id
        //     ]
        // );
       
        // Initialize an array to hold relationships for the branch
        $contant = [];

        // Set the manager IDs based on the available data
        if (isset($datauser['Collection_Manager'])) {
            $acm_id = isset($datauser['Area_Collection_Manager']) ? $datauser['Area_Collection_Manager']->id : null;
            $zcm_id = isset($datauser['Zonal_Collection_Manager']) ? $datauser['Zonal_Collection_Manager']->id : null;
            $rcm_id = isset($datauser['Regional_Collection_Manager']) ? $datauser['Regional_Collection_Manager']->id : null;
            $ncm_id = isset($datauser['National_Collection_Manager']) ? $datauser['National_Collection_Manager']->id : null;
            $gph_id = isset($datauser['Group_Product_Head']) ? $datauser['Group_Product_Head']->id : null;

            $contant[] = [
                //'branch_id' => $branch->id ?? '',
                'agency_id' => $agency->id,
                'product_id' => $product->id,
                'manager_id' => $datauser['Collection_Manager']->id,
                'type' => 'Collection_Manager',
                'bucket' => $row['bucket'],
                'auditor_id' => $auditor_id[0],
                'acm_id' => $acm_id,
                'zcm_id' => $zcm_id,
                'rcm_id' => $rcm_id,
                'ncm_id' => $ncm_id,
                'gph_id' => $gph_id
            ];
        }
       
      
        if (isset($datauser['Area_Collection_Manager'])) {
            $contant[] = [
                'branch_id' => $branch->id ?? '',
                'agency_id' => $agency->id,
                'product_id' => $product->id,
                'manager_id' => $datauser['Area_Collection_Manager']->id,
                'type' => 'Area_Collection_Manager',
                'bucket' => $row['bucket'],
                'auditor_id' => $auditor_id[0]
            ];
        }
        if (isset($datauser['Regional_Collection_Manager'])) {
            $contant[] = [
                'branch_id' => $branch->id ?? '',
                'agency_id' => $agency->id,
                'product_id' => $product->id,
                'manager_id' => $datauser['Regional_Collection_Manager']->id,
                'type' => 'Regional_Collection_Manager',
                'bucket' => $row['bucket'],
                'auditor_id' => $auditor_id[0]
            ];
        }
        if (isset($datauser['Zonal_Collection_Manager'])) {
            $contant[] = [
                'branch_id' => $branch->id ?? '',
                'agency_id' => $agency->id,
                'product_id' => $product->id,
                'manager_id' => $datauser['Zonal_Collection_Manager']->id,
                'type' => 'Zonal_Collection_Manager',
                'bucket' => $row['bucket'],
                'auditor_id' => $auditor_id[0]
            ];
        }
        if (isset($datauser['National_Collection_Manager'])) {
            $contant[] = [
                'branch_id' => $branch->id ?? '',
                'agency_id' => $agency->id,
                'product_id' => $product->id,
                'manager_id' => $datauser['National_Collection_Manager']->id,
                'type' => 'National_Collection_Manager',
                'bucket' => $row['bucket'],
                'auditor_id' => $auditor_id[0]
            ];
        }
        if (isset($datauser['Group_Product_Head'])) {
            $contant[] = [
                'branch_id' => $branch->id ?? '',
                'agency_id' => $agency->id,
                'product_id' => $product->id,
                'manager_id' => $datauser['Group_Product_Head']->id,
                'type' => 'Group_Product_Head',
                'bucket' => $row['bucket'],
                'auditor_id' => $auditor_id[0]
            ];
        }
        echo '<pre>'; print_r( $contant ); die;

        // Update or create branchable relationships
        foreach ($contant as $item) {
            $product_user = Branchable::updateOrCreate(
                [
                    'branch_id' => $item['branch_id'] ?? '',
                    'agency_id' => $item['agency_id'],
                    'product_id' => $item['product_id'],
                    'manager_id' => $item['manager_id'],
                    'type' => $item['type']
                ],
                $item
            );
            if ($product_user) {
                $data = Branchable::where('id', $product_user->id)->update(['auditor_id' => $item['auditor_id']]);
            }
        }
     

        // Update or create agencies, yards, and repositories if applicable
        // if ($row['agency_name'] != 'NA') {
        //     $agency = Agency::updateOrCreate(
        //         [
        //             'branch_id' => $branch->id ?? '',
        //             'name' => $row['agency_name']
        //         ],
        //         [
        //             'branch_id' => $branch->id ?? '',
        //             'name' => $row['agency_name'],
        //             'agency_id' => $row['agency_id'] ?? '',
        //             'agency_manager' => $datauser['Collection_Manager']->id,
        //             'location' => $row['agency_location'] ?? '',
        //             'address' => $row['agency_address'] ?? '',
        //             'product_id' => $product->id
        //         ]
        //     );
        // }

        
        // if ($row['yard_name'] != 'NA') {
        //     $yard = Yard::updateOrCreate(
        //         [
        //             'branch_id' => $branch->id ?? '',
        //             'name' => $row['yard_name']
        //         ],
        //         [
        //             'branch_id' => $branch->id ?? '',
        //             'name' => $row['yard_name'],
        //             'yard_id' => $row['yard_id'] ?? '',
        //             'agency_manager' => (isset($row['yard_manager'])) ? ($row['yard_manager'] ?? '') : ($datauser['Collection_Manager']->name ?? ''),
        //             'location' => $row['yard_location'] ?? '',
        //             'address' => $row['yard_address'] ?? '',
        //             'product_id' => $product->id
        //         ]
        //     );
        // }

        // if ($row['branch_repo'] != 'NA') {
        //     BranchRepo::updateOrCreate(
        //         [
        //             'branch_id' => $branch->id ?? '',
        //             'name' => $row['branch_repo']
        //         ],
        //         [
        //             'branch_id' => $branch->id ?? '',
        //             'name' => $row['branch_repo'],
        //             'location' => $row['branch_repo_address'] ?? '',
        //             'product_id' => $product->id,
        //             'branch_repo_id' => $row['branch_repo_id']
        //         ]
        //     );
        // }
        // if ($row['repo_agency'] != 'NA') {
        //     AgencyRepo::updateOrCreate(
        //         [
        //             'branch_id' => $branch->id ?? '',
        //             'name' => $row['repo_agency']
        //         ],
        //         [
        //             'branch_id' => $branch->id ?? '',
        //             'name' => $row['repo_agency'],
        //             'location' => $row['agency_repo_address'],
        //             'product_id' => $product->id,
        //             'agency_repo_id' => $row['agency_repo_id']
        //         ]
        //     );
        // }
        // if (isset($row['yard_repo']) && !empty($row['yard_repo']) && $row['yard_repo'] != 'NA') {
        //     YardRepo::updateOrCreate(
        //         [
        //             'branch_id' => $branch->id ?? '',
        //             'name' => $row['yard_repo']
        //         ],
        //         [
        //             'branch_id' => $branch->id ?? '',
        //             'name' => $row['yard_repo'],
        //             'location' => $row['location'],
        //             'product_id' => $product->id
        //         ]
        //     );
        // }

        // Return the branch model
       // echo '<pre>'; print_r($branch); die;
        return $agency;

       
    }

    /**
     * Function to create users and assign roles.
     * 
     * @param array $row
     * 
     * @return array
     */
    public function createUser($row)
    {

       
        $datauser = []; // Array to hold created users

        // Create Collection Manager user
        if ($row['collection_manger'] != 'NA' && $row['cm_email_id'] != 'NA') {
            $datauser[] = [
                'user' => [
                    'name' => $row['collection_manger'],
                    'email' => $row['cm_email_id'],
                    'password' => Hash::make($row['cm_email_id']),
                    'employee_id' => ($row['cm_emp_code'] != 'NA' ? $row['cm_emp_code'] : null),
                    'mobile' => ($row['cm_mobile_number'] != 'NA' ? $row['cm_mobile_number'] : null)
                ],
                'role' => ['Collection Manager'],
            ];
        }

        // Create Area Collection Manager user
        if ($row['area_collection_manager'] != 'NA' && $row['acm_email_id'] != 'NA') {
            $datauser[] = [
                'user' => [
                    'name' => $row['area_collection_manager'],
                    'email' => $row['acm_email_id'],
                    'password' => Hash::make($row['acm_email_id']),
                    'employee_id' => ($row['acm_emp_code'] != 'NA' ? $row['acm_emp_code'] : null),
                    'mobile' => ($row['acm_mobile_number'] != 'NA' ? $row['acm_mobile_number'] : null)
                ],
                'role' => ['Area Collection Manager'],
            ];
        }

        // Create Regional Collection Manager user
        if ($row['rcm'] != 'NA' && $row['rcm_email_id'] != 'NA') {
            $datauser[] = [
                'user' => [
                    'name' => $row['rcm'],
                    'email' => $row['rcm_email_id'],
                    'password' => Hash::make($row['rcm_email_id']),
                    'employee_id' => ($row['rcm_emp_code'] != 'NA' ? $row['rcm_emp_code'] : null),
                    'mobile' => ($row['rcm_mobile_number'] != 'NA' ? $row['rcm_mobile_number'] : null)
                ],
                'role' => ['Regional Collection Manager'],
            ];
        }

        // Create Zonal Collection Manager user
        if ($row['zcm'] != 'NA' && $row['zcm_email_id'] != 'NA') {
            $datauser[] = [
                'user' => [
                    'name' => $row['zcm'],
                    'email' => $row['zcm_email_id'],
                    'password' => Hash::make($row['zcm_email_id']),
                    'employee_id' => ($row['zcm_emp_code'] != 'NA' ? $row['zcm_emp_code'] : null),
                    'mobile' => ($row['zcm_mobile_number'] != 'NA' ? $row['zcm_mobile_number'] : null)
                ],
                'role' => ['Zonal Collection Manager'],
            ];
        }

        // Create National Collection Manager user
        if ($row['national_head'] != 'NA' && $row['nh_email_id'] != 'NA') {
            $datauser[] = [
                'user' => [
                    'name' => $row['national_head'],
                    'email' => $row['nh_email_id'],
                    'password' => Hash::make($row['nh_email_id']),
                    'employee_id' => ($row['nh_emp_code'] != 'NA' ? $row['nh_emp_code'] : ''),
                    'mobile' => ($row['nh_mobile_number'] != 'NA' ? $row['nh_mobile_number'] : null)
                ],
                'role' => ['National Collection Manager'],
            ];
        }

        // Create Group Product Head user
        if ($row['group_head'] != 'NA' && $row['gh_email_id'] != 'NA') {
            $datauser[] = [
                'user' => [
                    'name' => $row['group_head'],
                    'email' => $row['gh_email_id'],
                    'password' => Hash::make($row['gh_email_id']),
                    'employee_id' => ($row['gh_emp_code'] != 'NA' ? $row['gh_emp_code'] : ''),
                    'mobile' => ($row['gh_mobile_number'] != 'NA' ? $row['gh_mobile_number'] : null)
                ],
                'role' => ['Group Product Head'],
            ];
        }
      
        // Loop through each user data and create/update user and assign role
        foreach ($datauser as $key => $value) {
            $role_r = Role::whereIn('name', $value['role'])->get()->pluck('name');
            $user = User::updateOrCreate(['email' => $value['user']['email']], $value['user'])->assignRole($role_r);
        
            $data[str_replace(' ', '_', $value['role'][0])] = $user;
        }

        // Return created user data
        return $data;
    }

    /**
     * Define batch size for batch inserts.
     * 
     * @return int
     */
    public function batchSize(): int
    {
        return 100;
    }

    /**
     * Define chunk size for reading the file.
     * 
     * @return int
     */
    public function chunkSize(): int
    {
        return 100;
    }

    // Uncomment this section to add validation rules and messages

    /*
    public function rules(): array
    {
        return [
            'product_name' => 'required|exists:products,name',
            'auditor_email_id' => 'required|exists:users,email',
            'city' => 'required|exists:cities,name',
            'lob' => 'required|in:collection,commercial_vehicle,rural,alliance','credit_card',
        ];
    }

    public function customValidationMessages()
    {
        return [
            'product_name.exists' => 'The product does not exist in the system',
            'auditor_email_id.exists' => 'Auditor does not exist in the system.',
            'city.exists' => 'The city does not exist in the system.',
            'lob.in' => 'The lob must be one of collection, commercial_vehicle, rural, alliance, credit_card',
        ];
    }
    */
}
