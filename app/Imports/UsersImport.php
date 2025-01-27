<?php

namespace App\Imports;

use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Illuminate\Support\Facades\Mail;

class UsersImport implements ToCollection, WithHeadingRow, WithValidation
{
    /**
     * 
     * @param Collection $rows
     */


     private $errors = [];
 
     private $data; 
 
 
 
     public function __construct(array $data = [])
 
     {
 
         $this->roles = $data; 
 
     }

    public function collection(Collection $rows)
    {
        ini_set("memory_limit", "-1");

        ini_set("max_execution_time", 5000);
        
        foreach ($rows as $row) {
            // Find role ID based on the role name
            $role = Role::where('name', $row['role'])->first();

            // Check if the user already exists
            $user = User::where('email', $row['email'])->first();

            $userData = [
                'employee_id' => $row['employeeid'],
                'name' => $row['fullname'],
                'email' => $row['email'],
                'team' => $row['team'],
                'user_role_id' => $role ? $role->id : null,
                'mobile' =>  '1234567890',
                'audit_agency' =>  'Qdegrees',
                'password' => bcrypt('Admin@123'),
            ];

            if ($user) {
                // Update existing user
                $user->update($userData);
            } else {
                // Create new user
                $user = User::create($userData);
            }
            $user->assignRole($row['role']);
            // Send email
          //  $this->sendEmail($user, $row['password']);
        }
    }

    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            '*.employeeid' => 'required|unique:users,employee_id',
            '*.fullname' => 'required',
         //   '*.team' => 'required',
            '*.email' => 'required|unique:users,email',
            '*.role' => 'required|exists:roles,name',
         //   '*.mobilenumber' => 'required',
          //  '*.auditagency' => 'required',
          //  '*.password' => 'required|string|min:6',
        ];
    }

    /**
     * Send email to user with login details.
     *
     * @param User $user
     * @param string $password
     */
    protected function sendEmail(User $user, string $password)
    {
        $url = url('login');

        Mail::send('emails.createUser', ['user' => $user, 'password' => $password, 'url' => $url], function ($m) use ($user) {
            $m->to($user->email, $user->name)->subject('Welcome Audit');
        });
    }
}
