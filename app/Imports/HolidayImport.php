<?php

namespace App\Imports;

use App\User;
use App\Holiday;
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
class HolidayImport implements ToModel, WithHeadingRow, WithValidation,WithChunkReading
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
        $data=Holiday::create(['religon'=>$row['religon'],'date'=>$row['date']]);
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
        // return [
        //     'email' => 'required|unique:users,email',
        //     'employee_id' => 'required|unique:users,employee_id',
        //     // 'manager_id' => 'required|exists:users,id',
        // ];
    }
}
