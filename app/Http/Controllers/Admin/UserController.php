<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Language;

use App\Mail\UserCreated;

use App\Process;

use App\Region;

use Illuminate\Support\Facades\Crypt;

use App\Models\User;
// use App\Models\Role;

use Carbon\Carbon;

use App\UsersMaster;

use Auth;

//use Crypt;

use Illuminate\Http\Request;

use Spatie\Permission\Models\Role;

use Illuminate\Support\Facades\Mail;

use Illuminate\Support\Facades\Storage;

use Validator;

use Maatwebsite\Excel\Facades\Excel;

use App\Exports\UsersExport;

use App\Exports\QcAndQaChangesExport;
use App\Imports\UsersImport;
use Illuminate\Support\Str;


class UserController extends Controller
{
    public function index()
    {
        $userRole = auth()->user()->roles->first();
        // dd($userRole);
        $authUserEmail = auth()->user()->email; 
        $check=User::select('is_approved')->get();
        $query = User::with('roles')
                ->where('active_status', 0)->paginate(10);
                // ->whereDoesntHave('roles', function ($query) {
                //     $query->where('name', 'Client');
                // });

        // dd($userRole);
        // if ($userRole->name == 'Admin') {
        //     $query->where("created_by", $authUserEmail);
        // }
    
        // $query->where(function ($subQuery) {
        //     $subQuery->whereDoesntHave("roles", function ($roleQuery) {
        //         $roleQuery->where("name", "Quality Auditor")->where("is_approved", 0);
        //     })->orWhereHas("roles", function ($roleQuery) {
        //         $roleQuery->where("name", "Quality Auditor")->where("is_approved", 1);
        //     });
        // });   
        
        $data = $query;
        // dd($data);
        return view("admin.User.index", ["data" => $data]);
    }

    public function showClient(){
        $query = User::with('roles') // Load the related roles
        ->where('active_status', 0) // Filter by active_status
        ->whereHas('roles', function ($query) {
            $query->where('name', 'Client'); // Filter by role name
        })
        ->paginate(10); 
        $data=$query;

        return view("Client.User.index", ["data" => $data]);
    }
    

    public function create()
    {
        // dd(auth()->user()->roles);
        // if (auth()->check()) {
        //     $userRole = auth()->user()->roles->first();
        // } else {
        //     // Handle the case where no user is logged in
        //     $userRole = null;
        // }
        
    
        // if ($userRole->name == 'Admin') {
        //     $roles = Role::where('name', 'Quality Auditor')->pluck('name', 'id')->toArray();
        // } else {
        //     $roles = Role::where('name', '!=', 'Quality Auditor')->where('name', '!=', 'Client')->pluck('name', 'id')->toArray();
        // }
        $roles=Role::get()->pluck( 'name','id');
    
        return view("admin.User.create", compact("roles"));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "name" => "required",

            "email" => "required|unique:users,email",

            // 'password' => 'required|confirmed|min:8',

            "mobile" => "required|numeric|digits:10",

            "role" => "required",
        ]);

        $validator->sometimes("password", "required|confirmed", function (
            $input
        ) {
            return $input->auto != "automatic";
        });

        if ($validator->fails()) {
            return redirect()
                ->back()
                ->withErrors($validator) 
                ->withInput(); 
        } else {
            $data = new User();

            $data->name = $request->name;

            $data->email = $request->email;

            $data->mobile = $request->mobile;
            $data->created_by = Auth::user()->email;
            $data->parent_id= Auth::user()->id;
            $data->audit_agency_id = Auth::user()->id;
            if ($request->auto == "automatic") {
                $password = Str::random(8);
                $data->password = bcrypt($password);
            } else {
                $password = $request->password;
                $data->password = bcrypt($password);
            }
            // dd($data);

            $data->save();

            $roles = $request["role"]; 
            // dd($roles);

            if (isset($roles)) {
                foreach ($roles as $role) {
                    $role_r = Role::where("id", "=", $role)->firstOrFail();
                    // dd($role_r);

                    $data->assignRole($role_r); //Assigning role to user
                    // dd($data);
                }
            }

            $url = url("login");

            // Mail::send(
            //     "emails.createUser",
            //     ["user" => $data, "password" => $password, "url" => $url],
            //     function ($m) use ($data) {
            //         // $m->from('hello@app.com', 'Your Application');

            //         $m->to($data->email, $data->name)->subject("Welcome Audit");
            //     }
            // );

            
            $errLevel = error_reporting(E_ALL ^ E_NOTICE);
                // try {
                //     Mail::send('emails.createUser', ['user' => $data, 'password' => $password, 'url' => $url], function ($m) use ($data) {
                //         $m->from('auditemail.noreply@qdegrees.org', 'Welcome Audit')
                //           ->to($data->email, $data->name)
                //           ->subject('Welcome to the Audit');
                //     });
                // } catch (Exception $e) {
                //     error_log('Error sending email: ' . $e->getMessage());
                // }
            error_reporting($errLevel);
        }

        return redirect("User")->with("success", [
            "User created successfully.",
        ]);

        //        return redirect('user')->with('success', 'User created successfully');
    }

    // for disable USER
    public function disable($id)
    {
        if (
            Auth::user()
                ->roles()
                ->first()->name == "Admin"
        ) {
            $user = User::find(Crypt::decrypt($id));
            $user->active_status = 1;
            $user->password = "fgfgfggfgdgdfgfdgdffhgfhreterfghh5y55";
            $user->disable_date = now(); 
            $user->update();
            return back();
        }
        return "Unauthorised";
    }

    public function edit($id)
    {
        $userRole = auth()->user()->roles->first();
    
        if ($userRole->name == 'Admin') {
            $roles = Role::where('name', 'Quality Auditor')->pluck('name', 'id')->toArray();
        } else {
            $roles = Role::where('name', '!=', 'Quality Auditor')->where('name', '!=', 'Client')->pluck('name', 'id')->toArray();
        }

        $data = User::find(Crypt::decrypt($id));

        $rdata = User::find(Crypt::decrypt($id))
            ->roles->pluck("id")
            ->toArray();

        return view("admin.User.edit", compact("roles", "data", "rdata"));
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            "name" => "required",

            "email" => "required",

            "mobile" => "required|numeric|digits:10",

            "role" => "required",
        ]);

        if ($validator->fails()) {
            return redirect()
                ->back()
                ->with("error", [$validator->errors()->all()])
                ->withInput();
        } else {
            $user = User::find(Crypt::decrypt($id));

            $user->name = $request->name;

            // $data->email = $request->email;

            $user->mobile = $request->mobile;

            $user->save();

            $roles = $request["role"]; //Retreive all roles

            if (isset($roles)) {
                $user->roles()->sync($roles); //If one or more role is selected associate user to roles
            } else {
                $user->roles()->detach(); //If no role is selected remove exisiting role associated to a user
            }

            //return redirect('user')->with('success', ['User Updated successfully.']);

            return redirect("User")->with(
                "success",
                "User Updated Successfully"
            );
        }
    }

    public function change_user_status($user_id, $status)
    {
        $user = User::find(Crypt::decrypt($user_id));

        $user->status = $status;

        $user->save();

        return redirect("User")->with(
            "success",
            "User status updated successfully"
        );
    }

    public function customer_profile()
    {
        $user = Auth::User();

        if ($user->avatar) {
            $final_data["user"] = Storage::url($user->avatar);
        } else {
            $final_data["user"] = "http://via.placeholder.com/150x150";
        }

        return response()->json(
            ["status" => 200, "message" => "Success", "data" => $user],
            200
        );
    }

    // public function profile()
    // {
    //     $id = Auth::user()->id;

    //     $roles = "";

    //     $data = User::find($id);

    //     $rdata = User::find($id);

    //     // echo '<pre>'; print_r( $rdata); die;

    //     //print_r($rdata);

    //     return view("acl.users.profile", [
    //         "data" => $data,
    //         "roles" => $roles,
    //         "rdata" => $rdata,
    //     ]);

    //     //return view('acl.users.profile',compact($rdata));
    // }

    public function delImage($filePath)
    {
        $url =
            "https://" .
            env("AWS_BUCKET") .
            ".s3." .
            env("AWS_DEFAULT_REGION") .
            ".amazonaws.com" .
            "/";

        $filePath = str_replace($url, "", $filePath);

        Storage::disk("s3")->delete($filePath);
    }

    public function updateprofile(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            "name" => "required",

            "email" => "required",

            "mobile" => "required",
        ]);

        if ($validator->fails()) {
            return redirect()
                ->back()

                ->withErrors($validator)

                ->withInput();
        } else {
            $data = User::find(Crypt::decrypt($id));

            $data->name = $request->name;

            $data->email = $request->email;

            $data->mobile = $request->mobile;

            if ($request->avatar) {
                if ($data->avatar) {
                    Storage::delete(
                        "company/_" .
                            Auth::user()->company_id .
                            "/user/_" .
                            Auth::Id() .
                            "/avatar/" .
                            $data->avatar
                    );
                }

                $request->avatar->store(
                    "company/_" .
                        Auth::user()->company_id .
                        "/user/_" .
                        Auth::Id() .
                        "/avatar"
                );

                $data->avatar = $request->avatar->hashName();
            }

            $data->save();

            return redirect("profile")->with(
                "success",
                "User Updated Successfully"
            );
        }
    }

    public function destroy($id)
    {
        //Find a user with a given id and delete

        $user = User::findOrFail(Crypt::decrypt($id));

        $user->delete();

        return redirect()
            ->route("Users.index")

            ->with(
                "success",

                "User successfully deleted."
            );
    }

    public function excelDownloadUser()
    {
        ini_set("memory_limit", "-1");

        ini_set("max_execution_time", 3000);

        return Excel::download(new UsersExport(), "users.xlsx");

        // return Excel::download(new QcAndQaChangesExport, 'users.xlsx');
    }

    public function userImport(Request $request)
    {
        // Validate the request to ensure the file is present
        $validator = Validator::make($request->all(), [
            'user_excel' => 'required|file|mimes:xlsx,xls',
        ]);
    
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }
    
        if ($request->hasFile('user_excel')) {
            $path1 = $request->file('user_excel')->store('temp');
            $dacpath = storage_path('app/' . $path1);
    
            // Create an instance of UsersImport without the role
            $exampleImport = new UsersImport();
    
            try {
                Excel::import($exampleImport, $dacpath);
            } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
                $failures = $e->failures();
                return redirect()->back()->withErrors($failures)->withInput();
            }
        }
    
        return redirect('User');
    }
    

    public function auditor_status($user_id, Request $request)
{
    $user = User::find(Crypt::decrypt($user_id));
    
    $user->is_approved = $request->input('is_approved'); 
    if ($user->is_approved == 1) {
        $user->approval_date = Carbon::now(); 
    }
    $user->update();

    return redirect("user")->with(
        "success",
        "Auditor status updated successfully"
    );
}

public function show($user_id)
{
    $userRole = auth()->user()->roles->first();
    $authUserEmail = auth()->user()->email;
    
    // $data = User::whereHas('roles', function ($query) {
    //     $query->where('name', 'Quality Auditor');
    // })->where('is_approved', 0)
    //   ->get();
    $data = User::find(Crypt::decrypt($user_id));

    $rdata = User::find(Crypt::decrypt($user_id))
        ->roles->pluck("name",'id');
    
 
    return view("admin.User.show", ["data" => $data, 'rdata' => $rdata]);
}



    
}
