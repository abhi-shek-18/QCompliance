<?php

namespace App\Imports;

use App\User;
use App\Allocation;
use Hash;
use Illuminate\Support\Facades\Mail;
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
class UsersImport implements ToModel, WithHeadingRow, WithValidation,WithChunkReading
{
    
    use Importable;
    private $errors = [];
    private $data; 

    public function __construct(array $data = [])
    {
        $this->roles = $data; 
    }
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        $role_r = Role::whereIn('id', $this->roles)->get()->pluck('name');
        $data=User::create(['name'=>$row['name'],'email'=>$row['email'],'mobile'=>$row['mobile'],'employee_id'=>$row['employee_id'],'password'=>Hash::make($row['email'])])->assignRole($role_r);
        $url=url('login');
        $password=$row['email'];
            Mail::send('emails.createUser', ['user' => $data,'password'=>$password,'url'=>$url], function ($m) use ($data) {
                $m->from('hello@app.com', 'Your Application');
                $m->to($data->email, $data->name)->subject('Welcome Audit');
            });
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
            'email' => 'required|unique:users,email',
            'employee_id' => 'required|unique:users,employee_id',
            // 'manager_id' => 'required|exists:users,id',
        ];
    }
}
