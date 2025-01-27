<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;

use App\Models\Agency;
use App\Models\User;
use DB,Response;
use App\Model\Branch;

use Validator;
use App\Imports\AgencyImport;
use App\Exports\AgencyExport;

use Spatie\Permission\Models\Role;
use App\UsersMaster;
//use Auth;
use Illuminate\Support\Facades\Auth;

use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Hash;

class AuditAgencyController extends Controller

{

    public function index()
    { 
        $roles = Role::all();
        //echo '<pre>'; print_r($roles); die;

        $auditagency = User::role('Admin')->get();
        return view('audit_agency.list', compact('auditagency'));
    }



    public function create()
    {
        $roles = Role::all();
        return view("audit_agency.create", compact("roles"));
    }


    public function store(Request $request)
    {
        // Validate the form data
        $validator = Validator::make($request->all(), [
            "name" => "required",
            "email" => "required|email|unique:users,email",
            "mobile" => "required|numeric|digits:10",
            "role" => "required",
        ]);

        // Conditionally validate the password
        $validator->sometimes("password", "required|confirmed|min:8", function ($input) {
            return $input->auto != "automatic";
        });

        // If validation fails, redirect back with errors
        if ($validator->fails()) {
            return redirect()
                ->back()
                ->with("error", $validator->errors()->all())
                ->withInput();
        }

        // Create new user instance
        $data = new User();
        $data->name = $request->name;
        $data->email = $request->email;
        $data->mobile = $request->mobile;
        
        $data->agency_admin = $request->agency_admin;
        $data->agency_admin_email_one = $request->agency_admin_email_one;
        $data->agency_admin_email_two = $request->agency_admin_email_two;
        
    
        $data->created_by = Auth::user()->email;
        $data ->parent_id = Auth::user()->id;

        // If auto is set to automatic, generate a random password
        if ($request->auto == "automatic") {
            $password = Str::random(8);  // Use Str::random instead of str_random (Laravel 6+)
            $data->password = bcrypt($password);
        } else {
            $password = $request->password;
            $data->password = bcrypt($password);
        }

        // Save user data
        $data->save();

        $roles = $request["role"];
        if (isset($roles)) {
            foreach ($roles as $role) {
                $role_r = Role::findOrFail($role);
                $data->assignRole($role_r);
            }
        }

        $url = url("login");

        // Send the email after the user is created
        try {
            // Mail::send('emails.createUser', ['user' => $data, 'password' => $password, 'url' => $url], function ($m) use ($data) {
            //     $m->from('auditemail.noreply@qdegrees.org', 'Welcome QDegrees')
            //         ->to($data->email, $data->name)
            //         ->subject('Welcome to the Agency');
            // });
            // Mail::send('emails.createUser', ['user' => $data, 'password' => $password, 'url' => $url], function ($m) use ($data) {
            //     $m->from('noreply@example.com', 'Your Application Name')  // Use a valid address
            //         ->to($data->email, $data->name)
            //         ->subject('Welcome to the QDegrees');
            // });
        } catch (Exception $e) {
            error_log('Error sending email: ' . $e->getMessage());
        }

         return redirect("audit_agency")->with("success", [ "Audit Agency created successfully.", ]);
    }




       
 



    public function show($id)
    {
        $data=Agency::where('id',Crypt::decrypt($id))->update(['status'=>1]);

        if($data){

            return redirect('agency')->with('success', ['Agency deleted successfully.']);
        }

        else{
            return redirect()->back()->with('error', ['Agency deletion unsuccessfully.']);

        }

    }



   
    public function edit($id)
    {
        $roles = Role::all();

        $data = User::find(Crypt::decrypt($id));
        return view('audit_agency.edit', compact('data','roles'));
    }



    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . Crypt::decrypt($id),
            'mobile' => 'required|numeric|max:10',
            'password' => 'nullable|string|min:6',
            'agency_admin' => 'required|string|max:255', 
        ]);

        if ($validator->fails()) {
            return redirect()->back()->with('error', $validator->errors()->all())->withInput();
        }

        $userId = Crypt::decrypt($id);
        $user = User::findOrFail($userId);

        $updateData = [
            'name' => $request->name,
            'email' => $request->email,
            'mobile' => $request->mobile,
            'agency_admin' => $request->agency_admin,
            'agency_admin_email_one' => $request->agency_admin_email_one,
            'agency_admin_email_two' => $request->agency_admin_email_two,
        ];

        if ($request->filled('password')) {
            $updateData['password'] = bcrypt($request->password);
        }

        $updated = User::where('id', $userId)->update($updateData);

        if ($updated) {
            return redirect('audit_agency')->with('success', ['Audit Agency updated successfully.']);
        } else {
            return redirect()->back()->with('error', ['Audit Agency update was unsuccessful.']);
        }
    }





  
    // public function update(Request $request, $id)
    // {

    //     $validator = Validator::make($request->all(), [
    //         'name' => 'required',
    //         'email' => 'required',
    //         'mobile' => 'required',
            
    //     ]);
    //     if ($validator->fails()) {
    //         return redirect()->back()->with('error', [$validator->errors()->all()])->withInput();
    //     } else {
    //         $agency=User::where('id',Crypt::decrypt($id))->update(

    //             [
    //                 'name'=>$request->name,
    //                 'email'=>$request->email,
    //                 'mobile'=>$request->mobile,
    //                 'agency_admin'=>$request->agency_admin,
    //                 'agency_admin_email_one'=>$request->agency_admin_email_one,
    //                 'agency_admin_email_two'=>$request->agency_admin_email_two,
    //             ]
    //         );

    //         if($agency){
    //             return redirect('audit_agency')->with('success', ['Audit Agency updated successfully.']);
    //         }
    //         else{
    //                 return redirect()->back()->with('error', ['Audit Agency updation unsuccessfully.']);
    //         }
    //     }
    // }



    /**

     * Remove the specified resource from storage.

     *

     * @param  int  $id

     * @return \Illuminate\Http\Response

     */

    
    public function destroy($id)
    {
        $user = User::findOrFail(Crypt::decrypt($id));
        $user->delete();

        return redirect()->route("audit_agency.index")->with("success", "Audit Agency successfully deleted.");
    }


    public function excelDownloadAgency(){

        ini_set('memory_limit', '-1');

        ini_set('max_execution_time', 3000);

        return Excel::download(new AgencyExport, 'Agency.xlsx');

    }


    //V Upload Page Show
    public function showAgencyImport()
    {
        return view('agency.upload');
    }


    //V Upload Excel Data
    public function agencyImport()
    {
     //  echo 'dfdsf'; die;AgencyImport
        Excel::import(new AgencyImport, request()->file('file'));

        return redirect()->back()->with('success', 'Excel file imported successfully.');
    }

    public function downloadAgencySample(){
      //  echo "jh"; die;
       $file= public_path(). "/download/agency_import.xlsx";
        $headers = array(
                  'Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                );
        return Response::download($file, 'agency_import.xlsx',$headers);
    }

    

}