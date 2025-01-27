<?php

namespace App\Imports;

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
// class UsersImport implements ToModel,WithChunkReading, WithHeadingRow, WithValidation,WithBatchInserts, ShouldQueue
class BranchImport implements ToModel, WithHeadingRow,WithValidation,WithChunkReading
{
    
    use Importable;
    private $errors = [];
    // private $data; 

    // public function __construct(array $data = [])
    // {
    //     $this->roles = $data; 
    // }
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
    //    dd($row);
    
        $city=City::where('name',$row['city'])->first();
        $datauser=$this->createUser($row);
        //dd($datauser['Quality_Auditor']->id);
        $auditor_id = User::where('email', $row['auditor_email_id'])->get()->pluck('id');
        
        $product=Products::updateOrCreate(['name'=>$row['product_name']],['name'=>$row['product_name'],'bucket'=>$row['bucket'],'type'=>0]);
        
        $branch=Branch::updateOrCreate(['name'=>$row['branch_name']],[
            'lob'=>$row['lob'],
            'city_id'=>$city->id,
            'name'=>$row['branch_name'],
            'location'=>$row['branch_location'] ?? $row['branch_address'],
            'uuid'=>$row['branch_id'] ?? null,
            'address'=>$row['branch_address'],
            'manager_id'=>$datauser['Collection_Manager']->id
        ]);


        $contant=[];
        if(isset($datauser['Collection_Manager'])){
	$acm_id = isset($datauser['Area_Collection_Manager'])?$datauser['Area_Collection_Manager']->id:null;
        $contant[]=['branch_id'=>$branch->id,'product_id'=>$product->id,'manager_id'=>$datauser['Collection_Manager']->id,'type'=>'Collection_Manager','bucket'=>$row['bucket'],'auditor_id'=> $auditor_id[0],'acm_id'=>$acm_id];
        }
        if(isset($datauser['Area_Collection_Manager'])){
        $contant[]=['branch_id'=>$branch->id,'product_id'=>$product->id,'manager_id'=>$datauser['Area_Collection_Manager']->id,'type'=>'Area_Collection_Manager','bucket'=>$row['bucket'],'auditor_id'=> $auditor_id[0]];
        }
        if(isset($datauser['Regional_Collection_Manager'])){
        $contant[]=['branch_id'=>$branch->id,'product_id'=>$product->id,'manager_id'=>$datauser['Regional_Collection_Manager']->id,'type'=>'Regional_Collection_Manager','bucket'=>$row['bucket'],'auditor_id'=> $auditor_id[0]];
        }
        if(isset($datauser['Zonal_Collection_Manager'])){
        $contant[]=['branch_id'=>$branch->id,'product_id'=>$product->id,'manager_id'=>$datauser['Zonal_Collection_Manager']->id,'type'=>'Zonal_Collection_Manager','bucket'=>$row['bucket'],'auditor_id'=> $auditor_id[0]];
        }
        if(isset($datauser['National_Collection_Manager'])){
        $contant[]=['branch_id'=>$branch->id,'product_id'=>$product->id,'manager_id'=>$datauser['National_Collection_Manager']->id,'type'=>'National_Collection_Manager','bucket'=>$row['bucket'],'auditor_id'=> $auditor_id[0]];
        }
        if(isset($datauser['Group_Product_Head'])){
        $contant[]=['branch_id'=>$branch->id,'product_id'=>$product->id,'manager_id'=>$datauser['Group_Product_Head']->id,'type'=>'Group_Product_Head','bucket'=>$row['bucket'],'auditor_id'=> $auditor_id[0]];
        }



       foreach($contant as $item){
        $product_user=Branchable::updateOrCreate(['branch_id'=>$item['branch_id'],'product_id'=>$item['product_id'],'manager_id'=>$item['manager_id'],'type'=>$item['type']],$item);
            if($product_user)
            {
                $data=Branchable::where('id',$product_user->id)->update(['auditor_id'=>$item['auditor_id']]);
            }
        
       }
       if($row['agency_name'] != 'NA'){
            $agency=Agency::updateOrCreate(
                [ 'branch_id'=>$branch->id,
                'name'=>$row['agency_name']
                ],[
                'branch_id'=>$branch->id,
                'name'=>$row['agency_name'],
                'agency_id'=>$row['agency_id'] ?? '',
                'agency_manager'=>$datauser['Collection_Manager']->id,
                'location'=>$row['agency_location'] ?? '',
                'address'=>$row['agency_address'] ?? '',
                'product_id'=>$product->id
                
            ]);
            }
            if($row['yard_name'] != 'NA'){
                $yard=Yard::updateOrCreate([
                    'branch_id'=>$branch->id,
                    // 'agency_id'=>$agency->id,
                    'name'=>$row['yard_name']
                ],
                [
                    'branch_id'=>$branch->id,
                    // 'agency_id'=>$agency->id,
                    'name'=>$row['yard_name'],
                    'yard_id'=>$row['yard_id'] ?? '',
                    'agency_manager'=>(isset($row['yard_manager']))?($row['yard_manager'] ?? ''):($datauser['Collection_Manager']->name ?? ''),
                    // 'agency_manager'=>$row['yard_manager'] ?? '',
                    'location'=>$row['yard_location'] ?? '',
                    'address'=>$row['yard_address'] ?? '',
                    'product_id'=>$product->id
                ]);
            }
        
        if($row['branch_repo'] != 'NA'){
            BranchRepo::updateOrCreate([
                'branch_id'=>$branch->id,
                'name'=>$row['branch_repo']
            ],
            [
                'branch_id'=>$branch->id,
                'name'=>$row['branch_repo'],
                'location'=>$row['branch_repo_address'] ?? '',
                'product_id'=>$product->id,
                'branch_repo_id'=>$row['branch_repo_id']
            ]);
        }
        if($row['repo_agency'] != 'NA'){
            AgencyRepo::updateOrCreate(
                ['branch_id'=>$branch->id,
                'name'=>$row['repo_agency']],
            [
                'branch_id'=>$branch->id,
                'name'=>$row['repo_agency'],
                'location'=>$row['agency_repo_address'],
                'product_id'=>$product->id,
                'agency_repo_id'=>$row['agency_repo_id']
            ]);
        }
        if(isset($row['yard_repo']) && !empty($row['yard_repo']) &&$row['yard_repo'] != 'NA'){
            YardRepo::updateOrCreate(
                [
                    'branch_id'=>$branch->id,
                    'name'=>$row['yard_repo']
                ],
                [
                    'branch_id'=>$branch->id,
                    'name'=>$row['yard_repo'],
                    'location'=>$row['location'],
                    'product_id'=>$product->id
                ]);
        }
        return $branch;
        
    }
    public function createUser($row){
        $datauser=[];
        if($row['collection_manger'] != 'NA' && $row['cm_email_id'] != 'NA'){    
            $datauser[]= [
                    'user'=>['name'=>$row['collection_manger'],'email'=>$row['cm_email_id'],'password'=>Hash::make($row['cm_email_id']),'employee_id'=>($row['cm_emp_code']!= 'NA'?$row['cm_emp_code']:null),'mobile'=>($row['cm_mobile_number']!= 'NA'?$row['cm_mobile_number']:null)],
                    'role'=>['Collection Manager'],
            ];
        }
        if($row['area_collection_manager'] != 'NA' && $row['acm_email_id'] != 'NA'){
            $datauser[]= [
                    'user'=>['name'=>$row['area_collection_manager'],'email'=>$row['acm_email_id'],'password'=>Hash::make($row['acm_email_id']),'employee_id'=>($row['acm_emp_code']!= 'NA'?$row['acm_emp_code']:null),'mobile'=>($row['acm_mobile_number']!= 'NA'?$row['acm_mobile_number']:null)],
                    'role'=>['Area Collection Manager'],
            ];
        }
        if($row['rcm'] != 'NA' && $row['rcm_email_id'] != 'NA'){
            $datauser[]= [
                    'user'=>['name'=>$row['rcm'],'email'=>$row['rcm_email_id'],'password'=>Hash::make($row['rcm_email_id']),'employee_id'=>($row['rcm_emp_code']!= 'NA'?$row['rcm_emp_code']:null),'mobile'=>($row['rcm_mobile_number']!= 'NA'?$row['rcm_mobile_number']:null)],
                    'role'=>['Regional Collection Manager'],
            ];
        }
        if($row['zcm'] != 'NA' && $row['zcm_email_id'] != 'NA'){
            $datauser[]= [
                    'user'=>['name'=>$row['zcm'],'email'=>$row['zcm_email_id'],'password'=>Hash::make($row['zcm_email_id']),'employee_id'=>($row['zcm_emp_code']!= 'NA'?$row['zcm_emp_code']:null),'mobile'=>($row['zcm_mobile_number']!= 'NA'?$row['zcm_mobile_number']:null)],
                    'role'=>['Zonal Collection Manager'],
            ];
        }
        if($row['national_head'] != 'NA' && $row['nh_email_id'] != 'NA'){
            $datauser[]= [
                    'user'=>['name'=>$row['national_head'],'email'=>$row['nh_email_id'],'password'=>Hash::make($row['nh_email_id']),'employee_id'=>($row['nh_emp_code']!= 'NA'?$row['nh_emp_code']:''),'mobile'=>($row['nh_mobile_number']!= 'NA'?$row['nh_mobile_number']:null)],
                    'role'=>['National Collection Manager'],
            ];
        }
        if($row['group_head'] != 'NA' && $row['gh_email_id'] != 'NA'){
            $datauser[]= [
                    'user'=>['name'=>$row['group_head'],'email'=>$row['gh_email_id'],'password'=>Hash::make($row['gh_email_id']),'employee_id'=>($row['gh_emp_code']!= 'NA'?$row['gh_emp_code']:''),'mobile'=>($row['gh_mobile_number']!= 'NA'?$row['gh_mobile_number']:null)],
                    'role'=>['Group Product Head'],
            ];
        }

        
        
        // dd($datauser);
        foreach ($datauser as $key => $value) {
            // dd(str_replace(' ','_',$value['role'][0]));
            $role_r = Role::whereIn('name', $value['role'])->get()->pluck('name');
            $user=User::updateOrCreate(['email'=>$value['user']['email']],$value['user'])->assignRole($role_r);
            $data[str_replace(' ','_',$value['role'][0])]=$user;
            // dd($data);
        }
        return $data;
    }
    public function batchSize(): int
    {
        return 100;
    }
    public function chunkSize(): int
    {
        return 100;
    }
    public function rules(): array
    {
        return [
            'product_name' => 'required|exists:products,name',
            'auditor_email_id' => 'required|exists:users,email',
            'city' => 'required|exists:cities,name',
            'lob' => 'required|in:collection,commercial_vehicle,rural,alliance',
            // 'manager_id' => 'required|exists:users,id',
        ];
    }
    public function customValidationMessages()
    {
        return [
            'product_name.exists' => 'The product is not exist in system',
            'auditor_email_id.exists' => 'Autidor is not exist in system.',
            'city.exists' => 'The city is not exist in system.',
            'lob.in' => 'The lob must be in collection,commercial_vehicle,rural,alliance',
        ];
    }
}
