<?php

namespace App\Http\Controllers;

ini_set('max_execution_time', 380);

use App\Models\Agency;
use App\Artifact;
use App\Audit;
use App\AuditAlertBox;
use App\Models\AuditCycle;
use App\AuditParameterResult;
use App\AuditQc;
use App\AuditResult;
use App\Exports\QcAndQaChangesExport;
use App\Imports\AcrImport;
use App\Imports\CashDespositionImport;
use App\Imports\OldscoreImport;
use App\Model\AcrReportData;
use App\Model\AgencyMobileEmail;
use App\Model\AgencyRepo;
use App\Model\Branch;
use App\Model\Branchable;
use App\Model\BranchRepo;
use App\Model\CashDepositionData;
use App\Model\DelaySeconAllocData;
use App\Model\Productattribute;
use App\Model\Products;
use App\Model\ReceiptCutData;
use App\OldScore;
use App\Partner;
use App\Qc;
use App\QcParameterResult;
use App\QcResult;
use App\QmSheet;
use App\QmSheetParameter;
use App\QmSheetSubParameter;
use App\RawData;
use App\RcaMode;
use App\RcaType;
use App\Reason;
use App\ReasonType;
use App\RedAlert;
use App\SavedAudit;
use App\SavedQcAudit;
use App\TypeBScoringOption;
use App\Models\User;
use App\Yard;
ini_set('memory_limit', '-1');

use Auth;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Crypt;
use DB;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Mail;

class AuditController extends Controller
{

    public function sendTestMail($val)
    {

        // $email=array("abhilasha.kenge@qdegrees.com");
        // $subject="Qdegrees Test Mail";
        // $data=array();
        // Mail::send('audit.test_mail',["data1"=>$data], function ($message) use($email,$subject){
        //     $message->from('noreply@qdegrees.com', 'RBL Bank')->to($email)->subject($subject);
        // });
        // echo "mail sent";
        // die;
        $audit = Audit::with('branchnew', 'branchnew.city', 'branchnew.city.state', 'branchnew.city.state.region', 'yard', 'agency', 'branchRepo', 'agencyRepo', 'collectionManagerData', 'qmsheet', 'productnew', 'qa_qtl_detail')->find($val);
        //dd($audit);
        $audit_data = AuditResult::with('parameter_detail', 'sub_parameter_detail')->where(["audit_id" => $val, "is_alert" => 1])->get();

        //echo "jdfjd"; die;
        $branchables = Branchable::with('acm', 'rcm')->where(['branch_id' => $audit->parent_branch_id, 'product_id' => $audit->product_id, 'manager_id' => $audit->branchnew->manager_id])->first();

        $nameAudit = "";

        $tl_email = "";
        //North
        if ($audit->branchnew->city && $audit->branchnew->city->state && $audit->branchnew->city->state->region && $audit->branchnew->city->state->region->id == 1) {
            $tl_email = "pradeep.singh@qdegrees.com";
        }
        //East
        if ($audit->branchnew->city && $audit->branchnew->city->state && $audit->branchnew->city->state->region && $audit->branchnew->city->state->region->id == 2) {
            $tl_email = "haider.ali@qdegrees.com";
        }
        //West
        if ($audit->branchnew->city && $audit->branchnew->city->state && $audit->branchnew->city->state->region && $audit->branchnew->city->state->region->id == 3) {
            $tl_email = "abdul.kadir@qdegrees.com";
        }
        //South
        if ($audit->branchnew->city && $audit->branchnew->city->state && $audit->branchnew->city->state->region && $audit->branchnew->city->state->region->id == 4) {
            $tl_email = "g.gopi@qdegrees.com";
        }

        if ($audit->branchRepo && $audit->branchRepo->name != "" && !is_null($audit->branchRepo->name)) {
            $nameAudit = $audit->branchRepo->name;
        } elseif ($audit->agency && $audit->agency->name != "" && !is_null($audit->agency->name)) {
            $nameAudit = $audit->agency->name;
        } elseif ($audit->agencyRepo && $audit->agencyRepo->name != "" && !is_null($audit->agencyRepo->name)) {
            $nameAudit = $audit->agencyRepo->name;
        } elseif ($audit->yard && $audit->yard->name != "" && !is_null($audit->yard->name)) {
            $nameAudit = $audit->yard->name;
        } else {
            $nameAudit = $audit->branchnew->name;
        }

        $email = array('abhishek.gupta@qdegrees.com');

        $cc = array('rachit.bansal@qdegrees.com', 'devendra.saini@qdegrees.com');

        $bcc = array('devendra.saini@qdegrees.com');

        if ($audit->collectionManagerData->email != "") {
            //$email[]=$audit->collectionManagerData->email;
        }

        if ($audit->qa_qtl_detail->email != "") {
            $email[] = $audit->qa_qtl_detail->email;
        }

        if ($branchables && $branchables->acm && $branchables->acm->email != "") {
            // $cc[]=$branchables->acm->email;
        }
        if ($branchables && $branchables->rcm && $branchables->rcm->email != "") {
            // $cc[]=$branchables->rcm->email;
        }

        if ($tl_email != "") {
            $cc[] = $tl_email;
        }

        $newDate = date("m/Y", strtotime($audit->audit_date_by_aud));
        $subject = $audit->branchnew->name . "_" . $audit->qmsheet->type . "_" . $nameAudit . "_" . $audit->collectionManagerData->name . "_" . $audit->productnew->name . "_" . $newDate;

        $errLevel = error_reporting(E_ALL ^ E_NOTICE);
        Mail::send('audit.test_mail', ["audit" => $audit, "audit_data" => $audit_data], function ($message) use ($email, $cc, $bcc, $subject) {
            $message->from(env('MAIL_FROM_ADDRESS'), 'RBL Bank Mail')->to($email)->cc($cc)->bcc($bcc)->subject($subject);
        });
        error_reporting($errLevel);
        //return response()->json(['status'=>200,'message'=>"Audit saved successfully.",'audit_id'=>$val], 200);

        echo "Mail sent successfully.";die;
        //return view("audit.test_mail",compact('audit','audit_data'));
    }

    public function render_audit_sheet($qm_sheet_id)
    {
        $users = User::with('roles')
        ->where('users.active_status', 0)
        ->whereDoesntHave('roles', function ($query) {
            $query->whereIn('name', ['Admin', 'Client', 'Quality Auditor']);
        })
        ->get()
        ->toArray();

        $formattedUsers = [];
        $Level_5 = [];

        if (!empty($users)) {
            foreach ($users as $user) {
                if (!empty($user['roles'])) {
                    $roleNamesArray = array_column($user['roles'], 'name');
                    $roleNames = implode(', ', $roleNamesArray);

                    // Check if the user has any of the specified roles for Level 5
                    $level5Roles = [
                        'National Collection Manager',
                        'Group Product Head',
                        'Head - Credit Card Collection',
                        'Head - Tele-Calling - Credit Card Collection',
                        'Head - Credit Card Collection - RBL Supercard',
                        'Head of the Collections',
                    ];

                    if (array_intersect($level5Roles, $roleNamesArray)) {
                        $Level_5[$user['id']] = $user['name'] . ' - ' . $user['employee_id'] . ' - ' . $roleNames;
                    } else {
                        $formattedUsers[$user['id']] = $user['name'] . ' - ' . $user['employee_id'] . ' - ' . $roleNames;
                    }
                } else {
                    $formattedUsers[$user['id']] = $user['name'] . ' - ' . $user['employee_id'] . ' - No Role';
                }
            }
        } else {
            $formattedUsers = [];
        }

        //    echo '<pre>'; print_r($formattedUsers); die;

        $data = QmSheet::with('parameter.qm_sheet_sub_parameter')->find(Crypt::decrypt($qm_sheet_id));

        $cycle = AuditCycle::orderBy('id', 'desc')->where('status', 1)->limit(3)->get();

        // $branch=Branch::all();

        // $agency=Agency::all();

        // $yard=Yard::all();

        /*if($user_role = Auth::user()->roles()->first()->name == 'Quality Auditor'){

        $brancid_data =Branchable::distinct()->where('auditor_id',Auth::user()->id)->where('status',1)->get()->pluck('branch_id');

        $branch=Branch::where('lob',$data->lob)->whereIn('id',$brancid_data)->get();

        }

        else{

        $branch=Branch::where('lob',$data->lob)->get();

        }*/

        $branch = Branch::where('lob', $data->lob)->orderBy('name', 'ASC')->get();

        // $agency=Agency::whereIn('branch_id',$branch->pluck('id'))->orderBy('name', 'ASC')->get();

        if ($user_role = Auth::user()->roles()->first()->name == 'Admin') {
            $assign_agency_ids = DB::table('audit_allocation')->where('process_review_agency_email', Auth::user()->email)->pluck('agency_id')->toArray();
            $agency = Agency::orderBy('name', 'ASC')->whereIn('id', $assign_agency_ids)->get();
        } elseif ($user_role = Auth::user()->roles()->first()->name == 'Quality Auditor') {
            $assign_agency_ids = DB::table('auditor_assigns')->where('auditor_email', Auth::user()->email)->pluck('agency_id')->toArray();
            $agency = Agency::orderBy('name', 'ASC')->whereIn('id', $assign_agency_ids)->get();
        } else {
            $agency = Agency::orderBy('name', 'ASC')->get();
        }

        $yard = Yard::whereIn('branch_id', $branch->pluck('id'))->orderBy('name', 'ASC')->get();

        $branchRepo = BranchRepo::whereIn('branch_id', $branch->pluck('id'))->orderBy('name', 'ASC')->get();

        $agencyRepo = AgencyRepo::whereIn('branch_id', $branch->pluck('id'))->orderBy('name', 'ASC')->get();

        $Products = Products::where('status', 0)->pluck('name', 'id')->toArray();

        // echo 'fdsf'; die;
        return view('audit.render_sheet', compact('qm_sheet_id', 'data', 'branch', 'agency', 'yard', 'branchRepo', 'agencyRepo', 'cycle', 'formattedUsers', 'Level_5', 'Products'));

        // return view('audit.render_sheet',compact('qm_sheet_id','data','branch'));

    }

    public function render_audit_sheet_new($qm_sheet_id)
    {

        // echo 'fdsf'; die;
        //dd(all_non_scoring_obs_options(1));

        $data = QmSheet::with('parameter.qm_sheet_sub_parameter')->find(Crypt::decrypt($qm_sheet_id));

        $cycle = AuditCycle::orderBy('id', 'desc')->where('status', 1)->limit(3)->get();

        // $branch=Branch::all();

        // $agency=Agency::all();

        // $yard=Yard::all();

        /*if($user_role = Auth::user()->roles()->first()->name == 'Quality Auditor'){

        $brancid_data =Branchable::distinct()->where('auditor_id',Auth::user()->id)->where('status',1)->get()->pluck('branch_id');

        $branch=Branch::where('lob',$data->lob)->whereIn('id',$brancid_data)->get();

        }

        else{

        $branch=Branch::where('lob',$data->lob)->get();

        }*/

        $branch = Branch::where('lob', $data->lob)->orderBy('name', 'ASC')->get();

        $agency = Agency::whereIn('branch_id', $branch->pluck('id'))->orderBy('name', 'ASC')->get();

        $yard = Yard::whereIn('branch_id', $branch->pluck('id'))->orderBy('name', 'ASC')->get();

        $branchRepo = BranchRepo::whereIn('branch_id', $branch->pluck('id'))->orderBy('name', 'ASC')->get();

        $agencyRepo = AgencyRepo::whereIn('branch_id', $branch->pluck('id'))->orderBy('name', 'ASC')->get();

        return view('audit.render_sheet_new', compact('qm_sheet_id', 'data', 'branch', 'agency', 'yard', 'branchRepo', 'agencyRepo', 'cycle'));

        // return view('audit.render_sheet',compact('qm_sheet_id','data','branch'));

    }
    public function getProductId($agencyId, $type)
    {
        if ($type === 'agency') {
            // Fetch the agency record to get the product_id
            $agency = Agency::find($agencyId);

            if ($agency && $agency->product_id) {
                // Return the product_id for the agency
                return response()->json(['data' => ['product_id' => $agency->product_id]]);
            }
        }

        // If no product_id is found, return an empty response
        return response()->json(['data' => null]);
    }
    public function getProduct($id, $type)
    {

        if ($type == 'branch') {

            // $branchable=Branch::with('branchable','city')->where('id',$id)->first();

            //$productIds=Branchable::where('branch_id',$id)->where('auditor_id',Auth::user()->id)->where('status',1)->get()->pluck('product_id')->toArray();

            $productIds = Branchable::where('branch_id', $id)->get()->pluck('product_id')->toArray();

            $branchable = Products::whereIn('id', array_unique($productIds))->get();

        } else if ($type == 'agency') {

            $agency = Agency::with('user')->find($id);

            // $branchable=Branch::with('branchable','city')->where('id',$agency->branch_id)->first();

            // $branchable=Branchable::with('product')->where('branch_id',$agency->branch_id)->get();

            $productIds = Branchable::where('agency_id', $agency->id)->get()->pluck('product_id')->toArray();

            $branchable = Products::whereIn('id', array_unique($productIds))->get();

        } else if ($type == 'yard') {

            $yard = Yard::with('user')->find($id);

            // $agency=Agency::with('user')->find($yard->agency_id);

            // $branchable=Branch::with('branchable','city')->where('id',$yard->branch_id)->first();

            // $branchable=Branchable::with('product')->where('branch_id',$yard->branch_id)->get();

            $productIds = Branchable::where('branch_id', $yard->branch_id)->get()->pluck('product_id')->toArray();

            $branchable = Products::whereIn('id', array_unique($productIds))->get();

        } else if ($type == 'branch_repo') {

            $branch_repo = BranchRepo::find($id);

            $productIds = Branchable::where('branch_id', $branch_repo->branch_id)->get()->pluck('product_id')->toArray();

            $branchable = Products::whereIn('id', array_unique($productIds))->get();

        } else if ($type == 'agency_repo') {

            $agency_repo = AgencyRepo::find($id);

            $productIds = Branchable::where('branch_id', $agency_repo->branch_id)->get()->pluck('product_id')->toArray();

            $branchable = Products::whereIn('id', array_unique($productIds))->get();

        }

        // echo '<pre>'; print_r($branchable);  die;
        return response()->json(['data' => $branchable]);

    }

    public function renderBranch($id, $type, $product_id)
    {

        $subProducts = Productattribute::where('product_id', $product_id)
            ->pluck('product_attribute_name', 'id')->toArray();
        if (!empty($subProducts)) {
            $subProducts = $subProducts;
        } else {
            $subProducts = [];
        }

        $agency_mobile_number = [];
        $agency_email = [];
        $agency = [];

        // dd($type);

        $agency = Agency::with('user')->find($id);

        $branchable = Agency::with([
            'city',
            'branchable.ncm', // Keeping branchable relationships if needed
            'branchable.rcm',
            'branchable.ghead',
            'branchable.zcm',
            'branchable.acm',
        ])
            ->where('id', $agency->id)
            ->first();

        $agency_mobile_number = AgencyMobileEmail::where('agency_id', $agency->id)
            ->whereNull('email') // Add this line to ensure only rows with null email are fetched
            ->pluck('mobile_number', 'mobile_number')
            ->toArray();

        // Fetch emails where mobile number is null
        $agency_email = AgencyMobileEmail::where('agency_id', $agency->id)
            ->whereNull('mobile_number') // Add this line to ensure only rows with null mobile_number are fetched
            ->pluck('email', 'email')
            ->toArray();

        // echo '<pre>'; print_r($branchable);die;

        // // dd($agency);
        //  echo "<pre>";
        // print_r($branchable);
        // die;
        return view('audit.branch', compact('branchable', 'type', 'agency', 'agency_email', 'agency_mobile_number', 'subProducts'));

    }

    public function renderBranchQc($id, $type, $auditid, $product_id)
    {
        $subProducts = Productattribute::where('product_id', $product_id)
            ->pluck('product_attribute_name', 'id')->toArray();
        if (!empty($subProducts)) {
            $subProducts = $subProducts;
        } else {
            $subProducts = [];
        }

        $agency_mobile_number = [];
        $agency_email = [];
        $userData = [];
        if ((!empty($auditid)) && ($auditid != 'null') && ($auditid != 'undefined')) {
            $manager_id = Audit::with('user')->where('id', $auditid)->orderby('id', 'desc')->first();
            $userData = ['id' => $manager_id->user->id, 'name' => $manager_id->user->name];
        }

        $agency = Agency::with('user')->find($id);

        $branchable = Agency::with(['city.state.region', 'emails', 'mobileNumbers'])
            ->where('id', $agency->id)
            ->first();

        // echo '<pre>'; print_r($branchable); die;

        $agency_mobile_number = AgencyMobileEmail::where('agency_id', $agency->id)
            ->whereNull('email') // Add this line to ensure only rows with null email are fetched
            ->pluck('mobile_number', 'mobile_number')
            ->toArray();

        // Fetch emails where mobile number is null
        $agency_email = AgencyMobileEmail::where('agency_id', $agency->id)
            ->whereNull('mobile_number') // Add this line to ensure only rows with null mobile_number are fetched
            ->pluck('email', 'email')
            ->toArray();

        $formattedUsers = [];
        $Level_5 = [];

        if (!empty($users)) {
            foreach ($users as $user) {
                if (!empty($user['roles'])) {
                    $roleNamesArray = array_column($user['roles'], 'name');
                    $roleNames = implode(', ', $roleNamesArray);

                    // Check if the user has any of the specified roles for Level 5
                    $level5Roles = [
                        'National Collection Manager',
                        'Group Product Head',
                        'Head - Credit Card Collection',
                        'Head - Tele-Calling - Credit Card Collection',
                        'Head - Credit Card Collection - RBL Supercard',
                        'Head of the Collections',
                    ];

                    if (array_intersect($level5Roles, $roleNamesArray)) {
                        $Level_5[$user['id']] = $user['name'] . ' - ' . $user['employee_id'] . ' - ' . $roleNames;
                    } else {
                        $formattedUsers[$user['id']] = $user['name'] . ' - ' . $user['employee_id'] . ' - ' . $roleNames;
                    }
                } else {
                    $formattedUsers[$user['id']] = $user['name'] . ' - ' . $user['employee_id'] . ' - No Role';
                }
            }
        } else {
            $formattedUsers = [];
        }

        //echo '<pre>'; print_r($manager_id); die;
        return view('audit.branchQc', compact('branchable', 'type', 'agency', 'userData', 'agency_email', 'agency_mobile_number', 'subProducts', 'formattedUsers', 'Level_5', 'manager_id'));

    }

    public function render_audit_sheet_edit($qm_sheet_id)
    {
        $cycle = AuditCycle::orderBy('id', 'desc')->where('status', 1)->limit(3)->get();

        $result = Audit::with(['audit_parameter_result', 'audit_results'])->where('id', Crypt::decrypt($qm_sheet_id))->first();
        // echo '<pre>'; print_r($result); die;

        $subProducts = Productattribute::where('product_id', $result->product_id)
            ->pluck('product_attribute_name', 'id')->toArray();
        //   echo '<pre>'; print_r($subProducts); die;
        if (!empty($subProducts)) {
            $subProducts = $subProducts;
        } else {
            $subProducts = [];
        }

        $formattedUsers = [];
        $Level_5 = [];
        $users = User::with('roles')
            ->where('users.active_status', 0)
            ->whereDoesntHave('roles', function ($query) {
                $query->whereIn('name', ['Admin', 'Client', 'Quality Auditor']);
            })
            ->get()
            ->toArray();
        if (!empty($users)) {
            foreach ($users as $user) {
                if (!empty($user['roles'])) {
                    $roleNamesArray = array_column($user['roles'], 'name');
                    $roleNames = implode(', ', $roleNamesArray);

                    // Check if the user has any of the specified roles for Level 5
                    $level5Roles = [
                        'National Collection Manager',
                        'Group Product Head',
                        'Head - Credit Card Collection',
                        'Head - Tele-Calling - Credit Card Collection',
                        'Head - Credit Card Collection - RBL Supercard',
                        'Head of the Collections',
                    ];

                    if (array_intersect($level5Roles, $roleNamesArray)) {
                        $Level_5[$user['id']] = $user['name'] . ' - ' . $user['employee_id'] . ' - ' . $roleNames;
                    } else {
                        $formattedUsers[$user['id']] = $user['name'] . ' - ' . $user['employee_id'] . ' - ' . $roleNames;
                    }
                } else {
                    $formattedUsers[$user['id']] = $user['name'] . ' - ' . $user['employee_id'] . ' - No Role';
                }
            }
        } else {
            $formattedUsers = [];
        }

        $agency_mobile_number = [];
        $agency_email = [];
        //dd(all_non_scoring_obs_options(1));

        $resultPar = AuditParameterResult::where('audit_id', $result->id)->get()->keyBy('parameter_id');

        $resultSubPar = AuditResult::where('audit_id', $result->id)->get()->keyBy('sub_parameter_id');

        $data = QmSheet::with('parameter.qm_sheet_sub_parameter.artifact')->find($result->qm_sheet_id);

        $branch = Branch::where('lob', $data->lob)->get();

        if ($user_role = Auth::user()->roles()->first()->name == 'Admin') {
            $assign_agency_ids = DB::table('audit_allocation')->where('process_review_agency_email', Auth::user()->email)->pluck('agency_id')->toArray();
            $agency = Agency::orderBy('name', 'ASC')->whereIn('id', $assign_agency_ids)->get();
        } elseif ($user_role = Auth::user()->roles()->first()->name == 'Quality Auditor') {
            $assign_agency_ids = DB::table('auditor_assigns')->where('auditor_email', Auth::user()->email)->pluck('agency_id')->toArray();
            $agency = Agency::orderBy('name', 'ASC')->whereIn('id', $assign_agency_ids)->get();
        } else {
            $agency = Agency::orderBy('name', 'ASC')->get();
        }

        $yard = Yard::whereIn('branch_id', $branch->pluck('id'))->get();

        $branchRepo = BranchRepo::whereIn('branch_id', $branch->pluck('id'))->get();

        $agencyRepo = AgencyRepo::whereIn('branch_id', $branch->pluck('id'))->get();

        $artifactIds = Artifact::where('audit_id', $result->id)->get()->pluck('id')->toArray();

        $redalertIds = RedAlert::where('audit_id', $result->id)->get()->pluck('sub_parameter_id')->toArray();

        // dd($data,$artifactIds,$result->id);

        $agency_mobile_number = AgencyMobileEmail::where('agency_id', $result->agency_id)
            ->whereNull('email') // Add this line to ensure only rows with null email are fetched
            ->pluck('mobile_number', 'mobile_number')
            ->toArray();

        // Fetch emails where mobile number is null
        $agency_email = AgencyMobileEmail::where('agency_id', $result->agency_id)
            ->whereNull('mobile_number') // Add this line to ensure only rows with null mobile_number are fetched
            ->pluck('email', 'email')
            ->toArray();

        $Products = Products::where('status', 0)->pluck('name', 'id')->toArray();

        //  echo '<pre>'; print_r($result); die;
        return view('audit.render_sheet_edit', compact('qm_sheet_id', 'data', 'result', 'resultPar', 'resultSubPar', 'branch', 'agency', 'yard', 'branchRepo', 'agencyRepo', 'artifactIds', 'redalertIds', 'formattedUsers', 'subProducts', 'agency_mobile_number', 'agency_email', 'Level_5', 'Products', 'cycle'));

    }

    public function render_audit_sheet_View($qm_sheet_id)
    {

        ini_set('memory_limit', '-1');
        $cycle = AuditCycle::orderBy('id', 'desc')->where('status', 1)->limit(3)->get();

        //dd(all_non_scoring_obs_options(1));

        $result = Audit::with(['audit_parameter_result', 'audit_results'])->where('id', Crypt::decrypt($qm_sheet_id))->first();
        //  echo '<pre>'; print_r($result); die;
        $resultPar = AuditParameterResult::where('audit_id', $result->id)->get()->keyBy('parameter_id');

        $resultSubPar = AuditResult::where('audit_id', $result->id)->get()->keyBy('sub_parameter_id');

        $data = QmSheet::with('parameter.qm_sheet_sub_parameter.artifact')->find($result->qm_sheet_id);

        $branch = Branch::where('lob', $data->lob)->get();
        $agency = Agency::orderBy('name', 'ASC')->get();

        //    echo '<pre>'; print_r($agency); die;
        $yard = Yard::whereIn('branch_id', $branch->pluck('id'))->get();

        $branchRepo = BranchRepo::whereIn('branch_id', $branch->pluck('id'))->get();

        $agencyRepo = AgencyRepo::whereIn('branch_id', $branch->pluck('id'))->get();

        $artifactIds = Artifact::where('audit_id', $result->id)->get()->pluck('id')->toArray();

        $redalertIds = RedAlert::where('audit_id', $result->id)->get()->pluck('sub_parameter_id')->toArray();

        $formattedUsers = [];
        $Level_5 = [];
        $users = User::with('roles')
        ->where('users.active_status', 0)
        ->whereDoesntHave('roles', function ($query) {
            $query->whereIn('name', ['Admin', 'Client', 'Quality Auditor']);
        })
        ->get()
        ->toArray();
        if (!empty($users)) {
            foreach ($users as $user) {
                if (!empty($user['roles'])) {
                    $roleNamesArray = array_column($user['roles'], 'name');
                    $roleNames = implode(', ', $roleNamesArray);

                    // Check if the user has any of the specified roles for Level 5
                    $level5Roles = [
                        'National Collection Manager',
                        'Group Product Head',
                        'Head - Credit Card Collection',
                        'Head - Tele-Calling - Credit Card Collection',
                        'Head - Credit Card Collection - RBL Supercard',
                        'Head of the Collections',
                    ];

                    if (array_intersect($level5Roles, $roleNamesArray)) {
                        $Level_5[$user['id']] = $user['name'] . ' - ' . $user['employee_id'] . ' - ' . $roleNames;
                    } else {
                        $formattedUsers[$user['id']] = $user['name'] . ' - ' . $user['employee_id'] . ' - ' . $roleNames;
                    }
                } else {
                    $formattedUsers[$user['id']] = $user['name'] . ' - ' . $user['employee_id'] . ' - No Role';
                }
            }
        } else {
            $formattedUsers = [];
        }
        $Products = Products::where('status', 0)->pluck('name', 'id')->toArray();
        //  echo '<pre>'; print_r($result); die;
        return view('audit.view_sheet', compact('qm_sheet_id', 'data', 'result', 'resultPar', 'resultSubPar', 'branch', 'agency', 'yard', 'branchRepo', 'agencyRepo', 'artifactIds', 'redalertIds', 'Level_5', 'formattedUsers', 'cycle', 'Products'));

    }
    public function render_audit_sheet_View_QC($qm_sheet_id)
    {

        //dd(all_non_scoring_obs_options(1));

        $result = Audit::with(['audit_parameter_result', 'audit_results'])->where('id', Crypt::decrypt($qm_sheet_id))->first();

        $resultPar = AuditParameterResult::where('audit_id', $result->id)->get()->keyBy('parameter_id');

        $resultSubPar = AuditResult::where('audit_id', $result->id)->get()->keyBy('sub_parameter_id');

        $data = QmSheet::with('parameter.qm_sheet_sub_parameter.artifact')->find($result->qm_sheet_id);

        $branch = Branch::where('lob', $data->lob)->get();

        $agency = Agency::whereIn('branch_id', $branch->pluck('id'))->get();

        $yard = Yard::whereIn('branch_id', $branch->pluck('id'))->get();

        $branchRepo = BranchRepo::whereIn('branch_id', $branch->pluck('id'))->get();

        $agencyRepo = AgencyRepo::whereIn('branch_id', $branch->pluck('id'))->get();

        // dd($data,$resultPar,$resultSubPar);

        $qc = Qc::where('audit_id', $result->id)->first();

        $artifactIds = Artifact::where('audit_id', $result->id)->get()->pluck('id')->toArray();

        $redalertIds = RedAlert::where('audit_id', $result->id)->get()->pluck('sub_parameter_id')->toArray();

        return view('audit.view_sheet_qc', compact('qm_sheet_id', 'data', 'result', 'resultPar', 'resultSubPar', 'branch', 'agency', 'yard', 'branchRepo', 'agencyRepo', 'qc', 'artifactIds', 'redalertIds'));

    }

    public function detail_audit_sheet_edit($qm_sheet_id)
    {

        //dd(all_non_scoring_obs_options(1));

        $result = Audit::with(['audit_parameter_result', 'audit_results', 'user'])->where('id', Crypt::decrypt($qm_sheet_id))->first();

        $resultPar = AuditParameterResult::where('audit_id', $result->id)->get()->keyBy('parameter_id');

        $resultSubPar = AuditResult::where('audit_id', $result->id)->get()->keyBy('sub_parameter_id');
        // DB::enableQueryLog();
        $data = QmSheet::with('parameter.qm_sheet_sub_parameter')->find($result->qm_sheet_id);
        //dd(DB::getQueryLog());
        //echo $result->qm_sheet_id;
        // dd($data);

        $branch = Branch::where('lob', $data->lob)->get();

        $agency = Agency::whereIn('branch_id', $branch->pluck('id'))->get();

        $yard = Yard::whereIn('branch_id', $branch->pluck('id'))->get();

        $branchRepo = BranchRepo::whereIn('branch_id', $branch->pluck('id'))->get();

        $agencyRepo = AgencyRepo::whereIn('branch_id', $branch->pluck('id'))->get();

        $products = Products::get();

        $artifactIds = Artifact::where('audit_id', $result->id)->get()->pluck('id')->toArray();

        $qc = Qc::where('audit_id', $result->id)->first();

        $redalertIds = RedAlert::where('audit_id', $result->id)->get()->pluck('sub_parameter_id')->toArray();

        // dd($data,$resultPar,$resultSubPar);

        $preview_redalert = DB::table('qm_sheet_parameters as parameter')

            ->join('qm_sheet_sub_parameters as subp', 'subp.qm_sheet_parameter_id', '=', 'parameter.id')

            ->join('red_alerts as ra', 'ra.sub_parameter_id', '=', 'subp.id')

            ->select('ra.*', 'subp.sub_parameter', 'parameter.parameter')

            ->where('ra.audit_id', $result->id)

            ->get()->toArray();

        return view('audit.detail_sheet_edit', compact('qm_sheet_id', 'data', 'result', 'resultPar', 'resultSubPar', 'branch', 'agency', 'yard', 'branchRepo', 'agencyRepo', 'artifactIds', 'qc', 'redalertIds', 'preview_redalert', 'products'));

    }
    public function save_qc_status(Request $request)
    {

        // dd($request->all());

        if ($request->type == 'save') {

            SavedQcAudit::updateOrCreate(['audit_id' => $request->audit_id], ['audit_id' => $request->audit_id, 'status' => 1]);

        } else if ($request->type == 'submit' || $request->type == 'savebyqc') {

            SavedQcAudit::where(['audit_id' => $request->audit_id])->delete();

        }

        if ($request->qc_id != '') {

            $data = Qc::where('id', $request->qc_id)->update(['qm_sheet_id' => $request->qm_sheet_id, 'audit_id' => $request->audit_id, 'status' => $request->status, 'feedback' => $request->feedback, 'qc_by_id' => Auth::user()->id]);

        } else {

            $data = Qc::create(['qm_sheet_id' => $request->qm_sheet_id, 'audit_id' => $request->audit_id, 'status' => $request->status, 'feedback' => $request->feedback, 'qc_by_id' => Auth::user()->id]);

        }

        if ($data && $request->type == 'submit') {

            $ids = [];

            $audit_id = $request->audit_id;

            $otherDetails = [];

            $audit = Audit::with(['qmsheet', 'redAlert.parameter', 'redAlert.subParameter', 'product'])->where('id', $audit_id)->first();

            $auditResult = AuditResult::where('audit_id', $audit_id)->get()->pluck('remark', 'sub_parameter_id');

            // dd($auditResult);

            switch ($audit->qmsheet->type) {

                case 'branch':

                    $branch = Branch::with('city.state.region')->find($audit->branch_id);

                    $otherDetails['region'] = $branch->city->state->region->name ?? '';

                    $otherDetails['state'] = $branch->city->state->name ?? '';

                    $otherDetails['city'] = $branch->city->name ?? '';

                    $otherDetails['name'] = $branch->name ?? '';

                    $ids = Branchable::where('branch_id', $audit->branch_id)->get(['id', 'manager_id'])->pluck('manager_id');

                    break;

                case 'agency':

                    $agency = Agency::find($audit->agency_id);

                    $branch = Branch::with('city.state.region')->find($agency->branch_id);

                    $otherDetails['region'] = $branch->city->state->region->name ?? '';

                    $otherDetails['state'] = $branch->city->state->name ?? '';

                    $otherDetails['city'] = $branch->city->name ?? '';

                    $otherDetails['name'] = $agency->name ?? '';

                    $ids = Branchable::where('agency_id', $audit->agency_id)->get(['id', 'manager_id'])->pluck('manager_id');

                    break;

                case 'yard':

                    $agency = Yard::find($audit->yard_id);

                    $branch = Branch::with('city.state.region')->find($agency->branch_id);

                    $otherDetails['region'] = $branch->city->state->region->name ?? '';

                    $otherDetails['state'] = $branch->city->state->name ?? '';

                    $otherDetails['city'] = $branch->city->name ?? '';

                    $otherDetails['name'] = $agency->name ?? '';

                    $ids = Branchable::where('branch_id', $agency->branch_id)->get(['id', 'manager_id'])->pluck('manager_id');

                    break;

                case 'branch_repo':

                    $BranchRepo = BranchRepo::find($audit->branch_repo_id);

                    $branch = Branch::with('city.state.region')->find($BranchRepo->branch_id);

                    $otherDetails['region'] = $branch->city->state->region->name ?? '';

                    $otherDetails['state'] = $branch->city->state->name ?? '';

                    $otherDetails['city'] = $branch->city->name ?? '';

                    $otherDetails['name'] = $BranchRepo->name ?? '';

                    $ids = Branchable::where('branch_id', $BranchRepo->branch_id)->get(['id', 'manager_id'])->pluck('manager_id');

                    break;

                case 'agency_repo':

                    $AgencyRepo = AgencyRepo::find($audit->agency_repo_id);

                    $branch = Branch::with('city.state.region')->find($AgencyRepo->branch_id);

                    $otherDetails['region'] = $branch->city->state->region->name ?? '';

                    $otherDetails['state'] = $branch->city->state->name ?? '';

                    $otherDetails['city'] = $branch->city->name ?? '';

                    $otherDetails['name'] = $AgencyRepo->name ?? '';

                    $ids = Branchable::where('branch_id', $AgencyRepo->branch_id)->get(['id', 'manager_id'])->pluck('manager_id');

                    break;

            }

            $attach = [];

            foreach ($audit->redAlert as $item) {

                if ($item->file != null) {

                    $attach[] = $item->file;

                }

            }

            $emails = User::whereIn('id', $ids)->role('Area Collection Manager')->get(['id', 'email'])->pluck('email')->toArray();

            $otherDetails['collection'] = User::whereIn('id', $ids)->role('Collection Manager')->first(['id', 'name'])->name;

            //$emails[]='ravindra.swami9@gmail.com';

            $url = url('red-alert/') . '/' . Crypt::encrypt($audit->id);

            /* Mail::send('emails.alert', ['data' => $audit,'otherDetails'=>$otherDetails,'auditResult'=>$auditResult,'url'=>$url], function ($m) use ($emails,$attach) {

            //$m->from('hello@app.com', 'Your Application');

            $m->to($emails)->subject('Alert');

            foreach($attach as $item){

            $m->attach($item);

            }

            });*/

            return response()->json(['status' => true, 'msg' => 'status saved ']);

        } else {

            return response()->json(['status' => false, 'msg' => 'status not saved ']);

        }

    }

    public function get_qm_sheet_details_for_audit($qm_sheet_id)
    {

        $data = QmSheet::with(['client', 'process', 'parameter', 'parameter.qm_sheet_sub_parameter'])->find(Crypt::decrypt($qm_sheet_id));

        $partners_list = Partner::where('client_id', $data->client_id)->pluck('name', 'id');

        $final_data['partners_list'] = $partners_list;

        $temp_my_alloted_call_list = RawData::where('qtl_id', Auth::user()->id)->orWhere('qa_id', Auth::user()->id)->where('status', 0)->pluck('call_id', 'id');

        foreach ($temp_my_alloted_call_list as $key => $value) {

            $my_alloted_call_list[] = ['key' => $key, "value" => $value];

        }

        $final_data['my_alloted_call_list'] = $my_alloted_call_list;

        $final_data['sheet_details'] = $data->toArray();

        //$final_data['type_b_scoring_option'] = TypeBScoringOption::all();

        $all_type_b_scoring_option = TypeBScoringOption::where('company_id', $data->company_id)->pluck('name', 'id');

        //process data

        $pds = [];

        foreach ($data->parameter as $key => $value_p) {

            $pds[$value_p->id]['name'] = $value_p->parameter;

            $pds[$value_p->id]['is_non_scoring'] = $value_p->is_non_scoring;

            $total_parameter_weight = 0;

            $pds[$value_p->id]['is_fatal'] = 0;

            $pds[$value_p->id]['score'] = 0;

            $pds[$value_p->id]['score_with_fatal'] = 0;

            $pds[$value_p->id]['score_without_fatal'] = 0;

            $pds[$value_p->id]['temp_total_weightage'] = 0;

            foreach ($value_p->qm_sheet_sub_parameter as $key => $value_s) {

                $pds[$value_p->id]['subs'][$value_s->id]['name'] = $value_s->sub_parameter;

                $pds[$value_p->id]['subs'][$value_s->id]['details'] = $value_s->details;

                $pds[$value_p->id]['subs'][$value_s->id]['is_fatal'] = 0;

                $pds[$value_p->id]['subs'][$value_s->id]['is_non_scoring'] = $value_p->is_non_scoring;

                $pds[$value_p->id]['subs'][$value_s->id]['failure_reason'] = '';

                $pds[$value_p->id]['subs'][$value_s->id]['remark'] = '';

                $pds[$value_p->id]['subs'][$value_s->id]['orignal_weight'] = $value_s->weight;

                $pds[$value_p->id]['subs'][$value_s->id]['temp_weight'] = 0;

                $scoring_opts = [];

                if ($value_p->is_non_scoring) {

                    //total weight

                    $total_parameter_weight += 0;

                    if ($value_s->non_scoring_option_group) {

                        foreach (all_non_scoring_obs_options($value_s->non_scoring_option_group) as $key_ns => $value_ns) {

                            $scoring_opts[$value_p->id . "_" . $value_s->id . "_" . $value_ns . "_" . $key_ns . "_0"] = ["key" => $value_p->id . "_" . $value_s->id . "_" . $value_ns . "_" . $key_ns . "_0", "value" => $value_ns, "alert_box" => null];

                        }

                    } else {

                        $scoring_opts = null;

                    }

                } else {

                    //total weight

                    $total_parameter_weight += $value_s->weight;

                    //total weight

                    $alert_box = null;

                    $all_reason_type_fail = null;

                    $all_reason_type_cric = null;

                    if ($value_s->pass) {

                        if ($value_s->pass_alert_box_id) {
                            $alert_box = AuditAlertBox::find($value_s->pass_alert_box_id);
                        } else {
                            $alert_box = null;
                        }

                        $scoring_opts[$value_p->id . "_" . $value_s->id . "_" . $value_s->weight . "_1_0"] = ["key" => $value_p->id . "_" . $value_s->id . "_" . $value_s->weight . '_1_0', "value" => "Pass", "alert_box" => $alert_box];

                    }

                    if ($value_s->fail) {

                        if ($value_s->fail_alert_box_id) {
                            $alert_box = AuditAlertBox::find($value_s->fail_alert_box_id);
                        } else {
                            $alert_box = null;
                        }

                        if ($value_s->fail_reason_types) {

                            $temp_index_f = $value_p->id . "_" . $value_s->id . "_" . "0" . "_2_1";

                            $temp_r_fail = ReasonType::find(explode(',', $value_s->fail_reason_types))->pluck('name', 'id');

                            foreach ($temp_r_fail as $keycc => $valuecc) {

                                $all_reason_type_fail[] = ["key" => $value_p->id . "_" . $value_s->id . "_" . $keycc, "value" => $valuecc];

                            }

                        } else {

                            $temp_index_f = $value_p->id . "_" . $value_s->id . "_" . "0" . "_2_0";

                            $all_reason_type_fail = null;

                        }

                        $scoring_opts[$temp_index_f] = ["key" => $temp_index_f, "value" => "Fail", "alert_box" => $alert_box];

                    }

                    if ($value_s->critical) {

                        if ($value_s->critical_alert_box_id) {
                            $alert_box = AuditAlertBox::find($value_s->critical_alert_box_id);
                        } else {
                            $alert_box = null;
                        }

                        if ($value_s->critical_reason_types) {

                            $temp_index_cri = $value_p->id . "_" . $value_s->id . "_" . "Critical" . "_3_1";

                            $temp_cric = ReasonType::find(explode(',', $value_s->critical_reason_types))->pluck('name', 'id');

                            foreach ($temp_cric as $keycc => $valuecc) {

                                $all_reason_type_cric[] = ["key" => $value_p->id . "_" . $value_s->id . "_" . $keycc, "value" => $valuecc];

                            }

                        } else {

                            $temp_index_cri = $value_p->id . "_" . $value_s->id . "_" . "Critical" . "_3_0";

                            $all_reason_type_cric = null;

                        }

                        $scoring_opts[$temp_index_cri] = ["key" => $temp_index_cri, "value" => "Critical", "alert_box" => $alert_box];

                    }

                    if ($value_s->na) {

                        if ($value_s->na_alert_box_id) {
                            $alert_box = AuditAlertBox::find($value_s->na_alert_box_id);
                        } else {
                            $alert_box = null;
                        }

                        $scoring_opts[$value_p->id . "_" . $value_s->id . "_" . "N/A" . "_4_0"] = ["key" => $value_p->id . "_" . $value_s->id . "_" . "N/A" . "_4_0", "value" => "N/A", "alert_box" => $alert_box];

                    }

                    if ($value_s->pwd) {

                        if ($value_s->pwd_alert_box_id) {
                            $alert_box = AuditAlertBox::find($value_s->pwd_alert_box_id);
                        } else {
                            $alert_box = null;
                        }

                        $scoring_opts[$value_p->id . "_" . $value_s->id . "_" . ($value_s->weight / 2) . "_5_0"] = ["key" => $value_p->id . "_" . $value_s->id . "_" . ($value_s->weight / 2) . "_5_0", "value" => "PWD", "alert_box" => $alert_box];

                    }

                }

                $pds[$value_p->id]['subs'][$value_s->id]['options'] = $scoring_opts;

                $pds[$value_p->id]['subs'][$value_s->id]['score'] = 0;

                $pds[$value_p->id]['subs'][$value_s->id]['selected_options'] = null;

                $pds[$value_p->id]['subs'][$value_s->id]['selected_option_model'] = '';

                $pds[$value_p->id]['subs'][$value_s->id]['all_reason_type_fail'] = $all_reason_type_fail;

                $pds[$value_p->id]['subs'][$value_s->id]['all_reason_type_cric'] = $all_reason_type_cric;

                $pds[$value_p->id]['subs'][$value_s->id]['all_reason_type'] = null;

                $pds[$value_p->id]['subs'][$value_s->id]['selected_reason_type'] = '';

                $pds[$value_p->id]['subs'][$value_s->id]['all_reasons'] = null;

                $pds[$value_p->id]['subs'][$value_s->id]['selected_reason'] = '';

            }

            $pds[$value_p->id]['parameter_weight'] = $total_parameter_weight;

        }

        $final_data['simple_data'] = $pds;

        // rca starts

        $rca_type = RcaType::where('company_id', $data->company_id)->where('process_id', $data->process_id)->pluck('name', 'id');

        $rca_mode = $data = RcaMode::where('company_id', $data->company_id)->where('process_id', $data->process_id)->pluck('name', 'id');

        // rca end

        $final_data['rca_type'] = $rca_type;

        $final_data['rca_mode'] = $rca_mode;

        return response()->json(['status' => 200, 'message' => "Success", 'data' => $final_data], 200);

    }

    public function get_raw_data_for_audit($comm_instance_id)
    {

        $data = RawData::with('partner_detail')->find($comm_instance_id);

        if ($data) {

            return response()->json(['status' => 200, 'message' => "Call found.", 'data' => $data], 200);

        } else {

            return response()->json(['status' => 404, 'message' => "Call not found."], 404);

        }

    }

    public function audited_list(Request $request)
    {

        // die('Under Maintenance');
        $savedQcIds = SavedQcAudit::all()->pluck('audit_id')->toArray();

        $ids = Qc::with('user')->whereNotIn('audit_id', $savedQcIds)->get()->keyBy('audit_id');

        $savedIds = SavedAudit::all()->pluck('audit_id')->toArray();

        if ($ids->count() > 0) {
            if (isset($request->search)) {

                /*  echo "in";
                die; */
                $data = Audit::with(['qmsheet', 'product', 'branch.city.state', 'branch.branchable', 'yard.branch.city.state', 'agency.branch.city.state', 'qa_qtl_detail', 'user', 'branchRepo.branch.city.state', 'agencyRepo.branch.city.state'])->withCount('artifact')->whereNotIn('id', $ids->pluck('audit_id'))->whereNotIn('id', $savedIds)

                    ->orderby('id', 'desc')->paginate(10);
            } else {
                $data = Audit::with(['qmsheet', 'product', 'branch.city.state', 'branch.branchable', 'yard.branch.city.state', 'agency.branch.city.state', 'qa_qtl_detail', 'user', 'branchRepo.branch.city.state', 'agencyRepo.branch.city.state'])->withCount('artifact')->whereNotIn('id', $ids->pluck('audit_id'))->whereNotIn('id', $savedIds)

                    ->orderby('id', 'desc')->paginate(10);
            }

            //  $data= Audit::select('id','latitude','longitude','created_at','qm_sheet_id','branch_id','product_id','agency_id','collection_manager_id','agency_repo_id','branch_repo_id','audited_by_id','qm_sheet_id')->with(array('qmsheet'=>function($query){
            //   $query->select('id','name','type','lob');
            // }))->with(array('product'))->with(array('product'=>function($query){
            //  $query->select('id','name');
            // }))->with(array('branch.branchable'))->with(array('branch'=>function($query){
            //  $query->select('name','city_id');
            // }))->with(array('branch.city.state'))->with(array('user'=>function($query){
            //  $query->select('id','name','email');
            // }))->with(array('yard'=>function($query){
            //  $query->select('name');
            // }))->with(array('yard.branch.city.state'))->with(array('agency'=>function($query){
            //  $query->select('id','name','branch_id');
            // }))->with(array('agency.branch.city.state'))->with(array('qa_qtl_detail'=>function($query){
            //  $query->select('name');
            // }))->with(array('branchRepo'=>function($query){
            //  $query->select('name');
            // }))->with(array('agencyRepo'=>function($query){
            //  $query->select('name');
            // }))->withCount('artifact')->whereNotIn('id',$ids->pluck('audit_id'))->whereNotIn('id',$savedIds)->orderby('id','desc')->get();

        } else {

            $data = Audit::with(['qmsheet', 'product', 'branch.city.state', 'branch.branchable', 'yard.branch.city.state', 'agency.branch.city.state', 'qa_qtl_detail', 'user', 'branchRepo.branch.city.state', 'agencyRepo.branch.city.state'])->withCount('artifact')->whereNotIn('id', $savedIds)->orderby('id', 'desc')->paginate(10);

            /*     $data= Audit::select('id','latitude','longitude','created_at','qm_sheet_id','branch_id','product_id','agency_id','collection_manager_id','agency_repo_id','branch_repo_id','audited_by_id','qm_sheet_id')->with(array('qmsheet'=>function($query){
        $query->select('id','name','type','lob');
        }))->with(array('product'))->with(array('product'=>function($query){
        $query->select('id','name');
        }))->with(array('branch.branchable'))->with(array('branch'=>function($query){
        $query->select('name','city_id');
        }))->with(array('branch.city.state'))->with(array('user'=>function($query){
        $query->select('id','name','email');
        }))->with(array('yard'=>function($query){
        $query->select('name');
        }))->with(array('yard.branch.city.state'))->with(array('agency'=>function($query){
        $query->select('id','name','branch_id');
        }))->with(array('agency.branch.city.state'))->with(array('qa_qtl_detail'=>function($query){
        $query->select('name');
        }))->with(array('branchRepo'=>function($query){
        $query->select('name');
        }))->with(array('agencyRepo'=>function($query){
        $query->select('name');
        }))->withCount('artifact')->whereNotIn('id',$savedIds)->orderby('id','desc')->get(); */

        }

        // dd($data);

        return view('audit.audit_list', compact('data', 'ids', 'savedQcIds'));

    }

    public function audited_list_new(Request $request)
    {
        // echo 'ravi'; die;
        /* if(isset($request->start_date)){
        $start_date = date('Y-m-01');

        } else {

        } */

        $savedQcIds = SavedQcAudit::all()->pluck('audit_id')->toArray();

        $ids = Qc::with('user')->whereNotIn('audit_id', $savedQcIds)->get()->keyBy('audit_id');

        $savedIds = SavedAudit::all()->pluck('audit_id')->toArray();

        if ($ids->count() > 0) {

            $data = Audit::with(['qmsheet', 'product', 'audit_cycle', 'branch.city.state', 'branch.branchable', 'yard.branch.city.state', 'agency.branch.city.state', 'qa_qtl_detail', 'user', 'branchRepo.branch.city.state', 'agencyRepo.branch.city.state'])->withCount('artifact')->whereNotIn('id', $ids->pluck('audit_id'))->whereNotIn('id', $savedIds)->orderby('id', 'desc')->get();

        } else {

            $data = Audit::with(['qmsheet', 'product', 'audit_cycle', 'branch.city.state', 'branch.branchable', 'yard.branch.city.state', 'agency.branch.city.state', 'qa_qtl_detail', 'user', 'branchRepo.branch.city.state', 'agencyRepo.branch.city.state'])->withCount('artifact')->whereNotIn('id', $savedIds)->orderby('id', 'desc')->get();

        }

        return view('audit.audit_list_new', compact('data', 'ids', 'savedQcIds'));

    }

    public function done_audited_list(Request $request)
    {

        //    echo 'fdsf'; die;
        if ($request->start_date) {

            // $ids=Qc::with('user')->get()->keyBy('audit_id');

            $savedQcIds = SavedQcAudit::all()->pluck('audit_id')->toArray();

            // if($ids->count()>0){

            //     $data = Audit::with(['qmsheet','product','audit_cycle','branch.city.state','branch.branchable','yard.branch.city.state','agency.branch.city.state','qa_qtl_detail','branchRepo.branch.city.state','agencyRepo.branch.city.state'])->withCount('artifact')->whereNotIn('id',$savedQcIds)
            //     ->whereDate('created_at','>',$request->start_date)
            //     ->whereDate('created_at','<=',$request->end_date)
            //     ->orderby('id','desc')->get();

            // }

            // else{

            $data = Audit::with(['qmsheet', 'product', 'audit_cycle', 'branch.city.state', 'branch.branchable', 'yard.branch.city.state', 'agency.branch.city.state', 'qa_qtl_detail', 'branchRepo.branch.city.state', 'agencyRepo.branch.city.state'])->withCount('artifact')->whereNotIn('id', $savedQcIds)
                ->whereDate('created_at', '>', $request->start_date)
                ->whereDate('created_at', '<=', $request->end_date)
                ->where('status', 'approved')
                ->orderby('id', 'desc')->get();

            // }

            // echo '<pre>'; print_r( count( $data) ); die;
            return view('audit.audit_list_new', compact('data'));
        } else {
            return view('audit.audit_list_new');
        }

        // dd($data,$ids);

        // return view('audit.audit_list_new',compact('data','ids'));

    }

    public function audited_list_Post(Request $request)
    {

        $sheetids = [];

        $savedQcIds = SavedQcAudit::all()->pluck('audit_id')->toArray();

        $ids = Qc::with('user')->whereNotIn('audit_id', $savedQcIds)->get()->keyBy('audit_id');

        $savedIds = SavedAudit::all()->pluck('audit_id')->toArray();

        if ($ids->count() > 0) {

            $query = Audit::with(['qmsheet', 'product', 'branch.city.state', 'branch.branchable', 'yard.branch.city.state', 'agency.branch.city.state', 'qa_qtl_detail', 'branchRepo.branch.city.state', 'agencyRepo.branch.city.state'])->withCount('artifact')->whereIn('id', $ids->pluck('audit_id'))->whereNotIn('id', $savedQcIds);

        } else {

            $query = Audit::with(['qmsheet', 'product', 'branch.city.state', 'branch.branchable', 'yard.branch.city.state', 'agency.branch.city.state', 'qa_qtl_detail', 'branchRepo.branch.city.state', 'agencyRepo.branch.city.state'])->withCount('artifact')->whereNotIn('id', $savedQcIds);

        }

        // $query = Audit::with(['qmsheet','product','branch','yard','agency'])->whereNotIn('id',$ids);

        if (!empty($request->lob)) {

            $sheetids = QmSheet::where('lob', $request->lob)->get(['id'])->pluck('id');

            $query->whereIn('qm_sheet_id', $sheetids);

        }

        if ($request->start_date) {

            $start = Carbon::parse($request->start_date)->format('y-m-d 00:00:00');

            $query->whereDate('created_at', '>=', $start);

        }

        if ($request->end_date) {

            $end = Carbon::parse($request->end_date)->format('y-m-d 23:59:59');

            $query->whereDate('created_at', '<=', $end);

        }

        $data = $query->get();

        // dd($request->all(),$start,$data,$query->toSql());

        return view('audit.audit_list', compact('data', 'ids', 'savedQcIds'));

    }

    public function store_audit(Request $request)
    {

        $user_role = Auth::user()->roles()->first()->name; // Get the user's role name
        $audit_agency_id = null; // Initialize variable
        $process_review_agency_email = null; // Initialize variable
        // Determine audit_agency_id based on the user role
        if ($user_role === 'Quality Auditor') {
            // Get the ID of the agency associated with the 'created_by' field of the authenticated user
            $audit_agency_id = User::where('email', Auth::user()->created_by)->pluck('id')->first();
            $process_review_agency_email = Auth::user()->created_by;
        } elseif ($user_role === 'Admin') {
            // If the user is an Admin, set audit_agency_id to the authenticated user's ID
            $audit_agency_id = Auth::user()->id;
            $process_review_agency_email = Auth::user()->email;
        }

        $email = User::where('email', Auth::user()->created_by)->pluck('id')->first();

        //dd("fefef");
        //dd($request);
        DB::beginTransaction();
        try {
            logger($request);

            $latlong = explode(" ", $request->submission_data[0]['geotag']);

            //return response()->json(['status'=>200,'message'=>"Audit saved successfully.",'data'=>$request], 200);

            //create audit record

            $new_ar = new Audit;

            $parent_br_id = 0;

            if (isset($request->submission_data[0]['agency_id'])) {
                $fi = Agency::find($request->submission_data[0]['agency_id']);
                $parent_br_id = $fi->branch_id;
            }

            if (isset($request->submission_data[0]['yard_id'])) {
                $fi = Yard::find($request->submission_data[0]['yard_id']);
                $parent_br_id = $fi->branch_id;
            }

            if (isset($request->submission_data[0]['branch_repo_id'])) {
                $fi = BranchRepo::find($request->submission_data[0]['branch_repo_id']);
                $parent_br_id = $fi->branch_id;
            }

            if (isset($request->submission_data[0]['agency_repo_id'])) {
                $fi = AgencyRepo::find($request->submission_data[0]['agency_repo_id']);
                $parent_br_id = $fi->branch_id;
            }

            if (isset($request->submission_data[0]['branch_id'])) {
                $parent_br_id = $request->submission_data[0]['branch_id'];
            }
            $new_ar->process_review_agency_email = $process_review_agency_email;
            $new_ar->parent_branch_id = $parent_br_id;
            $new_ar->audit_cycle_id = $request->submission_data[0]['audit_cycle'];
            $new_ar->audit_date_by_aud = date('Y-m-d', strtotime($request->submission_data[0]['audit_date']));
            $new_ar->latitude = $latlong[0];
            $new_ar->longitude = $latlong[1];
            $new_ar->qm_sheet_id = $request->submission_data[0]['qm_sheet_id'];
            $new_ar->lavel_3 = $request->submission_data[0]['collection_manager_id'] ?? null;

            $new_ar->agency_email =$request->submission_data[0]['agency_email'];
            $new_ar->agency_mobile = $request->submission_data[0]['agency_phone'];

            $new_ar->lavel_4 = isset($request->submission_data[0]['lavel_4'])
            ? implode(',', $request->submission_data[0]['lavel_4'])
            : null;

            $new_ar->lavel_5 = isset($request->submission_data[0]['lavel_5'])
            ? implode(',', $request->submission_data[0]['lavel_5'])
            : null;
            $new_ar->sub_product_ids = isset($request->submission_data[0]['sub_product'])
            ? implode(',', $request->submission_data[0]['sub_product'])
            : null;

            $new_ar->grade = $request->submission_data[0]['grade'] ?? null;
            $new_ar->score_percentage = $request->submission_data[0]['with_fatal_score_per'] ?? null;
            $new_ar->audited_by_id = Auth::user()->id;
            $new_ar->audit_agency_id = $audit_agency_id;

            $new_ar->is_critical = isset($request->submission_data[0]['is_critical']) ? ($request->submission_data[0]['is_critical']) : 0;

            $new_ar->overall_score = $request->submission_data[0]['overall_score'];

            // $new_ar->audit_date = Carbon::now()->format('Y-m-d');

            // $new_ar->with_fatal_score_per = $request->submission_data[0]['overall_score'];

            $new_ar->branch_id = (isset($request->submission_data[0]['branch_id'])) ? $request->submission_data[0]['branch_id'] : null;

            $new_ar->agency_id = (isset($request->submission_data[0]['agency_id'])) ? $request->submission_data[0]['agency_id'] : null;

            $new_ar->yard_id = (isset($request->submission_data[0]['yard_id'])) ? $request->submission_data[0]['yard_id'] : null;

            $new_ar->branch_repo_id = (isset($request->submission_data[0]['branch_repo_id'])) ? $request->submission_data[0]['branch_repo_id'] : null;

            $new_ar->agency_repo_id = (isset($request->submission_data[0]['agency_repo_id'])) ? $request->submission_data[0]['agency_repo_id'] : null;

            $new_ar->product_id = (isset($request->submission_data[0]['product_id'])) ? $request->submission_data[0]['product_id'] : null;

            $new_ar->collection_manager_email = (isset($request->submission_data[0]['collection_manager_email'])) ? $request->submission_data[0]['collection_manager_email'] : null;

            $new_ar->agency_manager_email = (isset($request->submission_data[0]['agency_manager_email'])) ? $request->submission_data[0]['agency_manager_email'] : null;

            $new_ar->yard_manager_email = (isset($request->submission_data[0]['yard_manager_email'])) ? $request->submission_data[0]['yard_manager_email'] : null;

            $new_ar->collection_manager_id = (isset($request->submission_data[0]['collection_manager_id'])) ? $request->submission_data[0]['collection_manager_id'] : null;
            $get_parameters = DB::table('qm_sheet_parameters')->select('id')->where('qm_sheet_id', $request->submission_data[0]['qm_sheet_id'])->get()->toArray();
            $db_parameterids = array_column($get_parameters, 'id');

            // added for qc audit records

            $audit_qc = new AuditQc;

            $audit_qc->qm_sheet_id = $new_ar->qm_sheet_id;

            $audit_qc->audited_by_id = Auth::user()->id;

            $audit_qc->is_critical = $new_ar->is_critical;

            $audit_qc->overall_score = $new_ar->overall_score;

            $audit_qc->branch_id = $new_ar->branch_id;

            $audit_qc->agency_id = $new_ar->agency_id;

            $audit_qc->yard_id = $new_ar->yard_id;

            $audit_qc->branch_repo_id = $new_ar->branch_repo_id;
            $audit_qc->agency_repo_id = $new_ar->agency_repo_id;

            $audit_qc->product_id = $new_ar->product_id;

            $audit_qc->collection_manager_email = $new_ar->collection_manager_email;

            $audit_qc->agency_manager_email = $new_ar->agency_manager_email;

            $audit_qc->yard_manager_email = $new_ar->yard_manager_email;

            $audit_qc->collection_manager_id = $new_ar->collection_manager_id;

            $new_ar->save();
            if (isset($request->submission_data[0]['agency_id'])) {

                Agency::where('id', $new_ar->agency_id)->update([
                    'agency_manager' => $request->submission_data[0]['agency_manager'],
                    'agency_phone' => $request->submission_data[0]['agency_phone'],
                    'email' => $request->submission_data[0]['agency_email'],
                ]);

            }

            if ($request->submission_data[0]['status'] == 'submit') {
                $agency_email = $request->submission_data[0]['agency_email'];
                $toEmails = []; // Initialize the 'To' email array

                // Add the agency email as the primary recipient
                if ($agency_email) {
                    $toEmails[] = $agency_email;
                }

                // Add the email from lavel_3 to 'To' emails if it exists
                $user = User::find($new_ar->lavel_3);
                if ($user && $user->email) {
                    $toEmails[] = $user->email; // Add lavel_3 email
                }

                // Fetch emails for lavel_4 and lavel_5
                $lavel4Emails = [];
                $lavel5Emails = [];

                // Fetch users based on lavel_4 if it exists
                if (!empty($new_ar->lavel_4)) {
                    $lavel4Ids = explode(',', $new_ar->lavel_4);
                    $lavel4Users = User::whereIn('id', $lavel4Ids)->get(['email']);
                    $lavel4Emails = $lavel4Users->pluck('email')->toArray();
                }

                // Fetch users based on lavel_5 if it exists
                if (!empty($new_ar->lavel_5)) {
                    $lavel5Ids = explode(',', $new_ar->lavel_5);
                    $lavel5Users = User::whereIn('id', $lavel5Ids)->get(['email']);
                    $lavel5Emails = $lavel5Users->pluck('email')->toArray();
                }

                // Merge lavel_4 and lavel_5 emails
                $ccEmails = array_merge($lavel4Emails, $lavel5Emails);

                // Prepare data for the PDF
                $agency = DB::table('agencies')->where('id', $request->submission_data[0]['agency_id'])->first();
                $product = DB::table('products')->where('id', $request->submission_data[0]['product_id'])->first();
                $audit_cycles = DB::table('audit_cycles')->where('id', $request->submission_data[0]['audit_cycle'])->first();

                $pdfData = [
                    'Audit_agency' => 'QDegrees',
                    'grade' => $request->submission_data[0]['grade'] ?? null,
                    'score_percentage' => $request->submission_data[0]['with_fatal_score_per'] ?? null,
                    'overall_score' => $request->submission_data[0]['overall_score'] ?? null,
                    'agency_name' => $agency->name ?? null,
                    'location' => $agency->location ?? null,
                    'product_name' => $product->name ?? null,
                    'auditor_id' => Auth::user()->name,
                    'audit_cycle' => $audit_cycles->name,
                    'audit_date' => date('F Y', strtotime($request->submission_data[0]['audit_date'])),
                ];

                // Generate PDF
                $pdf = Pdf::loadView('audit.audit_result_pdf', $pdfData);
                $pdfContent = $pdf->output();

                $subject = "RBL Bank Process Review Rating Period for " . $pdfData['audit_cycle'] . " (" . $pdfData['agency_name'] . " - " . $pdfData['product_name'] . ")";

                // Send the email with CC
                Mail::send([], [], function ($message) use ($toEmails, $ccEmails, $subject, $pdfContent) {
                    $message->from('noreplyall@qdegrees.org', 'Audit Team')
                        ->to($toEmails) // Add all 'To' recipients
                        ->cc($ccEmails) // Add CC recipients
                        ->subject($subject)
                        ->attachData($pdfContent, 'Audit_Result.pdf', [
                            'mime' => 'application/pdf',
                        ])
                        ->setBody(
                            '<p>Dear Associate,</p>
                             <p>Greetings from RBL Bank!</p>
                             <p>Thank you for your co-operation & assistance extended to the Process Reviewer in conducting the process review successfully.</p>
                             <p>Please find attached the audit result for your agency.</p>',
                            'text/html'
                        );
                });

                $audit_qc->save();
            }

            // if(isset($request->submission_data[0]['agency_id']) && $request->submission_data[0]['agency_manager'] != '')

            // {

            //     Agency::where('id',$new_ar->agency_id)->update(['agency_manager'=>$request->submission_data[0]['agency_manager'],'agency_phone'=>$request->submission_data[0]['agency_phone'],'email'=>$request->submission_data[0]['agency_email']]);

            // }

            //added by  nisha for change status in branchable if audit submitting

            if (!empty($request->submission_data[0]['branch_id'])) {

                $idupdate = $request->submission_data[0]['branch_id'];

            }

            if (!empty($request->submission_data[0]['agency_id'])) {

                $branch_idfromagency = Agency::where('id', $request->submission_data[0]['agency_id'])->pluck('branch_id');

                $idupdate = $branch_idfromagency[0];

            }

            if (!empty($request->submission_data[0]['yard_id'])) {

                $branch_idfromyard = Yard::where('id', $request->submission_data[0]['yard_id'])->pluck('branch_id');

                $idupdate = $branch_idfromyard[0];

            }

            if (!empty($request->submission_data[0]['branch_repo_id'])) {

                $branch_idfrombranchrepo = BranchRepo::where('id', $request->submission_data[0]['branch_repo_id'])->pluck('branch_id');

                $idupdate = $branch_idfrombranchrepo[0];

            }

            if (!empty($request->submission_data[0]['agency_repo_id'])) {

                $branch_idfromagencyrepo = AgencyRepo::where('id', $request->submission_data[0]['agency_repo_id'])->pluck('branch_id');

                $idupdate = $branch_idfromagencyrepo[0];

            }

            if ($request->submission_data[0]['status'] == 'save') {

                SavedAudit::create(['audit_id' => $new_ar->id, 'status' => 1]);

            }

            // DB::enableQueryLog();

            $id4update_branchable_status = DB::table('branchables')

                ->where('branch_id', $idupdate)->where('status', 1)->where('product_id', $request->submission_data[0]['product_id'])

                ->where('type', 'Collection_Manager')

                ->where('manager_id', $request->submission_data[0]['collection_manager_id'])

                ->update(['status' => 2]);

            if (isset($request->submission_data[0]['artifactIds'])) {

                $artifactIds = json_decode($request->submission_data[0]['artifactIds']);

                foreach ($artifactIds as $item) {

                    Artifact::where('id', $item)->update(['audit_id' => $new_ar->id]);

                }

            }

            if ($new_ar->id) {
                $getparameterids = array_keys($request->parameters);
                $notmatched_param = array();
                $notmatched_param = array_diff($db_parameterids, $getparameterids);
                if (!empty($notmatched_param)) {
                    foreach ($notmatched_param as $parm_values) {
                        $new_arb = new AuditParameterResult;
                        $new_arb->audit_id = $new_ar->id;
                        $new_arb->parameter_id = $parm_values;
                        $new_arb->qm_sheet_id = $request->submission_data[0]['qm_sheet_id'];
                        $new_arb->orignal_weight = 0;
                        $new_arb->temp_weight = 0;
                        $new_arb->with_fatal_score = 0;
                        $new_arb->without_fatal_score = 0;
                        $new_arb->with_fatal_score_per = 0;
                        $new_arb->without_fatal_score_pre = 0;

                        if ($request->submission_data[0]['status'] == 'submit') {
                            $qc_arb = new QcParameterResult;
                            $qc_arb->audit_id = $new_arb->audit_id;
                            $qc_arb->parameter_id = $new_arb->parameter_id;
                            $qc_arb->qm_sheet_id = $new_arb->qm_sheet_id;
                            $qc_arb->orignal_weight = $new_arb->orignal_weight;
                            $qc_arb->temp_weight = $new_arb->temp_weight;
                            $qc_arb->with_fatal_score = $new_arb->with_fatal_score;
                            $qc_arb->without_fatal_score = $new_arb->without_fatal_score;

                            // if($value['temp_total_weightage']!=0)
                            // {
                            //     $qc_arb->with_fatal_score_per = $new_arb->with_fatal_score_per;
                            //     $qc_arb->without_fatal_score_pre = $new_arb->without_fatal_score_pre;
                            // }
                        }
                        $new_arb->save();

                        if ($request->submission_data[0]['status'] == 'submit') {
                            $qc_arb->save();
                        }

                    }
                }
                // store parameter wise data

                foreach ($request->parameters as $key => $value) {
                    if (in_array($key, $db_parameterids)) {
                        $new_arb = new AuditParameterResult;

                        $new_arb->audit_id = $new_ar->id;

                        $new_arb->parameter_id = $key;

                        $new_arb->qm_sheet_id = $request->submission_data[0]['qm_sheet_id'];

                        $new_arb->orignal_weight = ($value['parameter_weight'] != null) ? $value['parameter_weight'] : 0;

                        $new_arb->temp_weight = $value['temp_total_weightage'];

                        $new_arb->with_fatal_score = $value['score_with_fatal'];

                        $new_arb->without_fatal_score = $value['score_with_fatal'];

                        // $new_arb->without_fatal_score = $value['score_without_fatal'];

                        if ($value['temp_total_weightage'] != 0) {

                            $new_arb->with_fatal_score_per = ($value['score_with_fatal'] / $value['temp_total_weightage']) * 100;

                            // $new_arb->without_fatal_score_pre = ($value['score_without_fatal'] / $value['temp_total_weightage'])*100;

                            $new_arb->without_fatal_score_pre = ($value['score_with_fatal'] / $value['temp_total_weightage']) * 100;

                        }
                    }

                    // $new_arb->is_critical = $value['is_fatal'];

                    if ($request->submission_data[0]['status'] == 'submit') {

                        $qc_arb = new QcParameterResult;

                        $qc_arb->audit_id = $new_arb->audit_id;

                        $qc_arb->parameter_id = $new_arb->parameter_id;

                        $qc_arb->qm_sheet_id = $new_arb->qm_sheet_id;

                        $qc_arb->orignal_weight = $new_arb->orignal_weight;

                        $qc_arb->temp_weight = $new_arb->temp_weight;

                        $qc_arb->with_fatal_score = $new_arb->with_fatal_score;

                        $qc_arb->without_fatal_score = $new_arb->without_fatal_score;

                        if ($value['temp_total_weightage'] != 0) {

                            $qc_arb->with_fatal_score_per = $new_arb->with_fatal_score_per;

                            $qc_arb->without_fatal_score_pre = $new_arb->without_fatal_score_pre;

                        }

                    }

                    $new_arb->save();

                    if ($request->submission_data[0]['status'] == 'submit') {

                        $qc_arb->save();

                    }

                    if (isset($value['subs']) && count($value['subs']) > 0)
                    // store sub parameter wise data
                    {
                        foreach ($value['subs'] as $key_sb => $value_sb) {

                            if ($value_sb['temp_weight']);

                            {

                                $new_arc = new AuditResult;

                                $new_arc->audit_id = $new_ar->id;

                                $new_arc->parameter_id = $key;

                                $new_arc->sub_parameter_id = $key_sb;

                                $new_arc->selected_option = ($value_sb['temp_weight'] != 'Critical') ? $value_sb['temp_weight'] : 0;

                                $new_arc->option_selected = (isset($value_sb['option'])) ? $value_sb['option'] : null;

                                $new_arc->is_critical = ($value_sb['temp_weight'] != 'Critical') ? 0 : 1;

                                $new_arc->is_alert = (array_key_exists('ackalert', $value_sb) && $value_sb['ackalert'] == 1) ? 1 : 0;

                                if ($value_sb['score'] != 'rating') {

                                    $new_arc->score = ($value_sb['score'] != 'Critical') ? $value_sb['score'] : 0;

                                    $new_arc->is_percentage = $value_sb['is_percentage'];

                                    $new_arc->selected_per = ($value_sb['selected_per'] != 'select percentage') ? $value_sb['selected_per'] : null;

                                } else {

                                    $new_arc->score = ($value_sb['score'] != 'Critical') ? $value_sb['temp_weight'] : 0;

                                    $new_arc->is_percentage = $value_sb['is_percentage'];

                                    $new_arc->selected_per = ($value_sb['selected_per'] != 'select percentage') ? $value_sb['selected_per'] : null;

                                }

                                $new_arc->remark = $value_sb['remark'];

                                if ($request->submission_data[0]['status'] == 'submit') {

                                    $qc_arc = new QcResult;

                                    $qc_arc->audit_id = $new_arc->audit_id;

                                    $qc_arc->parameter_id = $new_arc->parameter_id;

                                    $qc_arc->sub_parameter_id = $new_arc->sub_parameter_id;

                                    $qc_arc->selected_option = $new_arc->selected_option;

                                    $qc_arc->option_selected = $new_arc->option_selected;

                                    $qc_arc->is_critical = $new_arc->is_critical;

                                    $qc_arc->score = $new_arc->score;

                                    $qc_arc->is_percentage = $new_arc->is_percentage;

                                    $qc_arc->selected_per = $new_arc->selected_per;

                                    $qc_arc->is_alert = ($new_arc->is_alert == 1) ? 1 : 0;

                                    $qc_arc->remark = $new_arc->remark;

                                    $qc_arc->save();
                                }

                                $new_arc->save();

                            }

                        }

                    }

                }

            }
            DB::commit();
            // if($request->submission_data[0]['status'] == 'submit'){
            //     $callMail=$this->sendTestMail($new_ar->id);
            // }

            //closure mail work
            if ($request->submission_data[0]['status'] == 'submit') {
                $auditId = $new_ar->id;
                $unsetParams = DB::table('audit_results')
                    ->join('qm_sheet_sub_parameters', 'audit_results.sub_parameter_id', '=', 'qm_sheet_sub_parameters.id')
                    ->where('audit_results.audit_id', $auditId)
                    ->where('audit_results.option_selected', 'Unsatisfactory')
                    ->select('qm_sheet_sub_parameters.sub_parameter as parameter_name', 'audit_results.*', 'audit_results.remark as remarks') // Select relevant fields
                    ->get();
                $agency_id = DB::table('audits')->where('id', $auditId)->pluck('agency_id')->first();
                if ($unsetParams->isNotEmpty()) {

                    $agency_details = DB::table('agencies')->where('id', $agency_id)->first();
                    $audit_details = DB::table('audits')->where('id', $auditId)->first();
                    $process_review_month = DB::table('audit_cycles')->where('id', $audit_details->audit_cycle_id)->pluck('name')->first();
                    if (!empty($unsetParams)) {
                        // Check if audit already exists in closure_audits
                        $auditExists = DB::table('closure_audits')
                            ->where('audit_id', $auditId)
                            ->first(); // Use first() instead of exists()

                        if (!$auditExists) { // If no record is found, $auditExists will be null
                            $closureId = DB::table('closure_audits')->insertGetId([
                                'audit_id' => $auditId,
                                'agency_id' => $agency_id, // Save agency_id
                                'remarks' => '',
                                'created_at' => now(),
                                'audit_agency_id' => $audit_agency_id,
                            ]);
                        } else {
                            $closureId = $auditExists->id;
                        }
                        // Insert the audit into closure_audits and get the closure_id

                        // Assuming you have the agency email stored somewhere
                        $agencyEmail = $request->submission_data[0]['agency_email']; // Replace this with the actual agency email

                        // Prepare data for the PDF
                        $pdfData = [
                            'unsetParams' => $unsetParams,
                            'audit_id' => $auditId,
                            'agency_details' => $agency_details, // Replace with actual agency name if available
                            'audit_details' => $audit_details, // Replace with actual agency name if available
                            'process_review_month' => $process_review_month,
                            'audit_agency_id' => $audit_agency_id, // Save audit_agency_id
                        ];

                        // Generate PDF for unset parameters
                        $pdf = PDF::loadView('closure.audit_closure_pdf', $pdfData);
                        $pdfContent = $pdf->output();

                        // Prepare the closure form link with closure_id
                        $closureFormLink = url("audit-closure-justification/{$closureId}/{$auditId}");

                        // Prepare email subject
                        $subject = "Audit Closure Form for Audit Agency: {$agency_details->name}";
                        $agency_name = $agency_details->name;
                        // Send email with the PDF attached using a view for the email body
                        Mail::send('closure.audit_closure_mail', ['closureFormLink' => $closureFormLink, 'process_review_month' => $process_review_month, 'agency_name' => $agency_name], function ($message) use ($agencyEmail, $subject, $pdfContent) {
                            $message->from('noreplyall@qdegrees.org', 'Audit Team')
                                ->to($agencyEmail)
                                ->subject($subject)
                                ->attachData($pdfContent, 'UNSATISFACTORY_PARAMETERS.pdf', [
                                    'mime' => 'application/pdf',
                                ]);
                        });
                    }
                } else {
                    $closureId = DB::table('closure_audits')
                        ->insertGetId([
                            'audit_id' => $auditId,
                            'agency_id' => $agency_id, // Save agency_id
                            'audit_agency_id' => $audit_agency_id, // Save audit_agency_id
                            'remarks' => '',
                            'status' => '1',
                            'created_at' => now(),
                        ]);

                }
            }

            //closure report work

            return response()->json(['status' => 200, 'message' => "Audit saved successfully.", 'audit_id' => $new_ar->id], 200);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['status' => 500, 'message' => "Audit saved unsuccessfully.", 'audit_id' => $e->getMessage()], 500);
        }
    }

    public function store_audit_new(Request $request)
    { //dd("fefef");
        //dd($request);
        DB::beginTransaction();
        try {
            logger($request);

            $user_role = Auth::user()->roles()->first()->name;

            $latlong = explode(" ", $request->submission_data[0]['geotag']);

            //return response()->json(['status'=>200,'message'=>"Audit saved successfully.",'data'=>$request], 200);

            //create audit record

            $new_ar = new Audit;

            $parent_br_id = 0;

            if (isset($request->submission_data[0]['agency_id'])) {
                $fi = Agency::find($request->submission_data[0]['agency_id']);
                $parent_br_id = $fi->branch_id;
            }

            if (isset($request->submission_data[0]['yard_id'])) {
                $fi = Yard::find($request->submission_data[0]['yard_id']);
                $parent_br_id = $fi->branch_id;
            }

            if (isset($request->submission_data[0]['branch_repo_id'])) {
                $fi = BranchRepo::find($request->submission_data[0]['branch_repo_id']);
                $parent_br_id = $fi->branch_id;
            }

            if (isset($request->submission_data[0]['agency_repo_id'])) {
                $fi = AgencyRepo::find($request->submission_data[0]['agency_repo_id']);
                $parent_br_id = $fi->branch_id;
            }

            if (isset($request->submission_data[0]['branch_id'])) {
                $parent_br_id = $request->submission_data[0]['branch_id'];
            }

            $new_ar->parent_branch_id = $parent_br_id;
            $new_ar->audit_cycle_id = $request->submission_data[0]['audit_cycle'];
            $new_ar->audit_date_by_aud = date('Y-m-d', strtotime($request->submission_data[0]['audit_date']));
            $new_ar->latitude = $latlong[0];
            $new_ar->longitude = $latlong[1];
            $new_ar->qm_sheet_id = $request->submission_data[0]['qm_sheet_id'];

            $new_ar->audited_by_id = Auth::user()->id;

            $new_ar->is_critical = isset($request->submission_data[0]['is_critical']) ? ($request->submission_data[0]['is_critical']) : 0;

            $new_ar->overall_score = $request->submission_data[0]['overall_score'];

            // $new_ar->audit_date = Carbon::now()->format('Y-m-d');

            // $new_ar->with_fatal_score_per = $request->submission_data[0]['overall_score'];

            $new_ar->branch_id = (isset($request->submission_data[0]['branch_id'])) ? $request->submission_data[0]['branch_id'] : null;

            $new_ar->agency_id = (isset($request->submission_data[0]['agency_id'])) ? $request->submission_data[0]['agency_id'] : null;

            $new_ar->yard_id = (isset($request->submission_data[0]['yard_id'])) ? $request->submission_data[0]['yard_id'] : null;

            $new_ar->branch_repo_id = (isset($request->submission_data[0]['branch_repo_id'])) ? $request->submission_data[0]['branch_repo_id'] : null;

            $new_ar->agency_repo_id = (isset($request->submission_data[0]['agency_repo_id'])) ? $request->submission_data[0]['agency_repo_id'] : null;

            $new_ar->product_id = (isset($request->submission_data[0]['product_id'])) ? $request->submission_data[0]['product_id'] : null;

            $new_ar->collection_manager_email = (isset($request->submission_data[0]['collection_manager_email'])) ? $request->submission_data[0]['collection_manager_email'] : null;

            $new_ar->agency_manager_email = (isset($request->submission_data[0]['agency_manager_email'])) ? $request->submission_data[0]['agency_manager_email'] : null;

            $new_ar->yard_manager_email = (isset($request->submission_data[0]['yard_manager_email'])) ? $request->submission_data[0]['yard_manager_email'] : null;

            $new_ar->collection_manager_id = (isset($request->submission_data[0]['collection_manager_id'])) ? $request->submission_data[0]['collection_manager_id'] : null;
            $get_parameters = DB::table('qm_sheet_parameters')->select('id')->where('qm_sheet_id', $request->submission_data[0]['qm_sheet_id'])->get()->toArray();
            $db_parameterids = array_column($get_parameters, 'id');

            // added for qc audit records

            $audit_qc = new AuditQc;

            $audit_qc->qm_sheet_id = $new_ar->qm_sheet_id;

            $audit_qc->audited_by_id = Auth::user()->id;

            $audit_qc->is_critical = $new_ar->is_critical;

            $audit_qc->overall_score = $new_ar->overall_score;

            $audit_qc->branch_id = $new_ar->branch_id;

            $audit_qc->agency_id = $new_ar->agency_id;

            $audit_qc->yard_id = $new_ar->yard_id;

            $audit_qc->branch_repo_id = $new_ar->branch_repo_id;
            $audit_qc->agency_repo_id = $new_ar->agency_repo_id;

            $audit_qc->product_id = $new_ar->product_id;

            $audit_qc->collection_manager_email = $new_ar->collection_manager_email;

            $audit_qc->agency_manager_email = $new_ar->agency_manager_email;

            $audit_qc->yard_manager_email = $new_ar->yard_manager_email;

            $audit_qc->collection_manager_id = $new_ar->collection_manager_id;

            $new_ar->save();

            if ($request->submission_data[0]['status'] == 'submit') {

                $audit_qc->save();

            }

            if (isset($request->submission_data[0]['agency_id']) && $request->submission_data[0]['agency_manager'] != '') {

                Agency::where('id', $new_ar->agency_id)->update(['agency_manager' => $request->submission_data[0]['agency_manager'], 'agency_phone' => $request->submission_data[0]['agency_phone']]);

            }

            //added by  nisha for change status in branchable if audit submitting

            if (!empty($request->submission_data[0]['branch_id'])) {

                $idupdate = $request->submission_data[0]['branch_id'];

            }

            if (!empty($request->submission_data[0]['agency_id'])) {

                $branch_idfromagency = Agency::where('id', $request->submission_data[0]['agency_id'])->pluck('branch_id');

                $idupdate = $branch_idfromagency[0];

            }

            if (!empty($request->submission_data[0]['yard_id'])) {

                $branch_idfromyard = Yard::where('id', $request->submission_data[0]['yard_id'])->pluck('branch_id');

                $idupdate = $branch_idfromyard[0];

            }

            if (!empty($request->submission_data[0]['branch_repo_id'])) {

                $branch_idfrombranchrepo = BranchRepo::where('id', $request->submission_data[0]['branch_repo_id'])->pluck('branch_id');

                $idupdate = $branch_idfrombranchrepo[0];

            }

            if (!empty($request->submission_data[0]['agency_repo_id'])) {

                $branch_idfromagencyrepo = AgencyRepo::where('id', $request->submission_data[0]['agency_repo_id'])->pluck('branch_id');

                $idupdate = $branch_idfromagencyrepo[0];

            }

            if ($request->submission_data[0]['status'] == 'save') {

                SavedAudit::create(['audit_id' => $new_ar->id, 'status' => 1]);

            }

            // DB::enableQueryLog();

            $id4update_branchable_status = DB::table('branchables')

                ->where('branch_id', $idupdate)->where('status', 1)->where('product_id', $request->submission_data[0]['product_id'])

                ->where('type', 'Collection_Manager')

                ->where('manager_id', $request->submission_data[0]['collection_manager_id'])

                ->update(['status' => 2]);

            if (isset($request->submission_data[0]['artifactIds'])) {

                $artifactIds = json_decode($request->submission_data[0]['artifactIds']);

                foreach ($artifactIds as $item) {

                    Artifact::where('id', $item)->update(['audit_id' => $new_ar->id]);

                }

            }

            if ($new_ar->id) {
                $getparameterids = array_keys($request->parameters);
                $notmatched_param = array();
                $notmatched_param = array_diff($db_parameterids, $getparameterids);
                if (!empty($notmatched_param)) {
                    foreach ($notmatched_param as $parm_values) {
                        $new_arb = new AuditParameterResult;
                        $new_arb->audit_id = $new_ar->id;
                        $new_arb->parameter_id = $parm_values;
                        $new_arb->qm_sheet_id = $request->submission_data[0]['qm_sheet_id'];
                        $new_arb->orignal_weight = 0;
                        $new_arb->temp_weight = 0;
                        $new_arb->with_fatal_score = 0;
                        $new_arb->without_fatal_score = 0;
                        $new_arb->with_fatal_score_per = 0;
                        $new_arb->without_fatal_score_pre = 0;

                        if ($request->submission_data[0]['status'] == 'submit') {
                            $qc_arb = new QcParameterResult;
                            $qc_arb->audit_id = $new_arb->audit_id;
                            $qc_arb->parameter_id = $new_arb->parameter_id;
                            $qc_arb->qm_sheet_id = $new_arb->qm_sheet_id;
                            $qc_arb->orignal_weight = $new_arb->orignal_weight;
                            $qc_arb->temp_weight = $new_arb->temp_weight;
                            $qc_arb->with_fatal_score = $new_arb->with_fatal_score;
                            $qc_arb->without_fatal_score = $new_arb->without_fatal_score;

                            // if($value['temp_total_weightage']!=0)
                            // {
                            //     $qc_arb->with_fatal_score_per = $new_arb->with_fatal_score_per;
                            //     $qc_arb->without_fatal_score_pre = $new_arb->without_fatal_score_pre;
                            // }
                        }
                        $new_arb->save();

                        if ($request->submission_data[0]['status'] == 'submit') {
                            $qc_arb->save();
                        }

                    }
                }
                // store parameter wise data

                foreach ($request->parameters as $key => $value) {
                    if (in_array($key, $db_parameterids)) {
                        $new_arb = new AuditParameterResult;

                        $new_arb->audit_id = $new_ar->id;

                        $new_arb->parameter_id = $key;

                        $new_arb->qm_sheet_id = $request->submission_data[0]['qm_sheet_id'];

                        $new_arb->orignal_weight = ($value['parameter_weight'] != null) ? $value['parameter_weight'] : 0;

                        $new_arb->temp_weight = $value['temp_total_weightage'];

                        $new_arb->with_fatal_score = $value['score_with_fatal'];

                        $new_arb->without_fatal_score = $value['score_with_fatal'];

                        // $new_arb->without_fatal_score = $value['score_without_fatal'];

                        if ($value['temp_total_weightage'] != 0) {

                            $new_arb->with_fatal_score_per = ($value['score_with_fatal'] / $value['temp_total_weightage']) * 100;

                            // $new_arb->without_fatal_score_pre = ($value['score_without_fatal'] / $value['temp_total_weightage'])*100;

                            $new_arb->without_fatal_score_pre = ($value['score_with_fatal'] / $value['temp_total_weightage']) * 100;

                        }
                    }

                    // $new_arb->is_critical = $value['is_fatal'];

                    if ($request->submission_data[0]['status'] == 'submit') {

                        $qc_arb = new QcParameterResult;

                        $qc_arb->audit_id = $new_arb->audit_id;

                        $qc_arb->parameter_id = $new_arb->parameter_id;

                        $qc_arb->qm_sheet_id = $new_arb->qm_sheet_id;

                        $qc_arb->orignal_weight = $new_arb->orignal_weight;

                        $qc_arb->temp_weight = $new_arb->temp_weight;

                        $qc_arb->with_fatal_score = $new_arb->with_fatal_score;

                        $qc_arb->without_fatal_score = $new_arb->without_fatal_score;

                        if ($value['temp_total_weightage'] != 0) {

                            $qc_arb->with_fatal_score_per = $new_arb->with_fatal_score_per;

                            $qc_arb->without_fatal_score_pre = $new_arb->without_fatal_score_pre;

                        }

                    }

                    $new_arb->save();

                    if ($request->submission_data[0]['status'] == 'submit') {

                        $qc_arb->save();

                    }

                    if (isset($value['subs']) && count($value['subs']) > 0)
                    // store sub parameter wise data
                    {
                        foreach ($value['subs'] as $key_sb => $value_sb) {

                            if ($value_sb['temp_weight']);

                            {

                                $new_arc = new AuditResult;

                                $new_arc->audit_id = $new_ar->id;

                                $new_arc->parameter_id = $key;

                                $new_arc->sub_parameter_id = $key_sb;

                                $new_arc->selected_option = ($value_sb['temp_weight'] != 'Critical') ? $value_sb['temp_weight'] : 0;

                                $new_arc->option_selected = (isset($value_sb['option'])) ? $value_sb['option'] : null;

                                $new_arc->is_critical = ($value_sb['temp_weight'] != 'Critical') ? 0 : 1;

                                $new_arc->is_alert = (array_key_exists('ackalert', $value_sb) && $value_sb['ackalert'] == 1) ? 1 : 0;

                                if ($value_sb['score'] != 'rating') {

                                    $new_arc->score = ($value_sb['score'] != 'Critical') ? $value_sb['score'] : 0;

                                    $new_arc->is_percentage = $value_sb['is_percentage'];

                                    $new_arc->selected_per = ($value_sb['selected_per'] != 'select percentage') ? $value_sb['selected_per'] : null;

                                } else {

                                    $new_arc->score = ($value_sb['score'] != 'Critical') ? $value_sb['temp_weight'] : 0;

                                    $new_arc->is_percentage = $value_sb['is_percentage'];

                                    $new_arc->selected_per = ($value_sb['selected_per'] != 'select percentage') ? $value_sb['selected_per'] : null;

                                }

                                $new_arc->remark = $value_sb['remark'];

                                if ($request->submission_data[0]['status'] == 'submit') {

                                    $qc_arc = new QcResult;

                                    $qc_arc->audit_id = $new_arc->audit_id;

                                    $qc_arc->parameter_id = $new_arc->parameter_id;

                                    $qc_arc->sub_parameter_id = $new_arc->sub_parameter_id;

                                    $qc_arc->selected_option = $new_arc->selected_option;

                                    $qc_arc->option_selected = $new_arc->option_selected;

                                    $qc_arc->is_critical = $new_arc->is_critical;

                                    $qc_arc->score = $new_arc->score;

                                    $qc_arc->is_percentage = $new_arc->is_percentage;

                                    $qc_arc->selected_per = $new_arc->selected_per;

                                    $qc_arc->is_alert = ($new_arc->is_alert == 1) ? 1 : 0;

                                    $qc_arc->remark = $new_arc->remark;

                                    $qc_arc->save();
                                }

                                $new_arc->save();

                            }

                        }

                    }

                }

            }
            DB::commit();
            // if($request->submission_data[0]['status'] == 'submit'){
            //     $callMail=$this->sendTestMail($new_ar->id);
            // }
            return response()->json(['status' => 200, 'message' => "Audit saved successfully.", 'audit_id' => $new_ar->id], 200);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['status' => 500, 'message' => "Audit saved unsuccessfully.", 'audit_id' => $e->getMessage()], 500);
        }
    }

    public function update_audit(Request $request) {

        $user_role = Auth::user()->roles()->first()->name; // Get the user's role name
        $audit_agency_id = null; // Initialize variable
        $process_review_agency_email = null; // Initialize variable
        // Determine audit_agency_id based on the user role
        if ($user_role == 'Quality Auditor') {
            // Get the ID of the agency associated with the 'created_by' field of the authenticated user
            $audit_agency_id = User::where('email', Auth::user()->created_by)->pluck('id')->first();
            $process_review_agency_email = Auth::user()->created_by;
        } elseif ($user_role == 'Admin') {
            // If the user is an Admin, set audit_agency_id to the authenticated user's ID
            $audit_agency_id = Auth::user()->id;
            $process_review_agency_email = Auth::user()->email;
        }

            //  echo '<pre>'; print_r($request->all()); die;
        DB::beginTransaction();
        try {
            logger($request);

            // echo '<pre>'; print_r($request->all()); die;
            /*  echo $request->submission_data[0]['id'];
        die; */

            //return response()->json(['status'=>200,'message'=>"Audit saved successfully.",'data'=>$request], 200);

            //create audit record

            $new_ar = Audit::find($request->submission_data[0]['id']);

            /* $new_ar->qm_sheet_id = $request->submission_data[0]['qm_sheet_id']; */

            // $new_ar->audited_by_id = Auth::user()->id;

            // $new_ar->is_critical = isset($request->submission_data[0]['is_critical'])?($request->submission_data[0]['is_critical']):0;

            $new_ar->overall_score = $request->submission_data[0]['overall_score'];

            // $new_ar->lavel_3 =$request->submission_data[0]['collection_manager_id'] ?? null;
            // $new_ar->lavel_4 = isset($request->submission_data[0]['lavel_4']) 
            // ? implode(',', $request->submission_data[0]['lavel_4']) 
            // : null;

            // $new_ar->lavel_5 = isset($request->submission_data[0]['lavel_5']) 
            //     ? implode(',', $request->submission_data[0]['lavel_5']) 
            //     : null;


            // $new_ar->audit_date = Carbon::now()->format('Y-m-d');

            $new_ar->score_percentage = $request->submission_data[0]['with_fatal_score_per'] ?? null;
           // $new_ar->audited_by_id = Auth::user()->id;
            /* $new_ar->branch_id = (isset($request->submission_data[0]['branch_id']))?$request->submission_data[0]['branch_id']:null;

        $new_ar->agency_id = (isset($request->submission_data[0]['agency_id']))?$request->submission_data[0]['agency_id']:null;

        $new_ar->yard_id = (isset($request->submission_data[0]['yard_id']))?$request->submission_data[0]['yard_id']:null;

        $new_ar->product_id = (isset($request->submission_data[0]['product_id']))?$request->submission_data[0]['product_id']:null;

        $new_ar->branch_repo_id = (isset($request->submission_data[0]['branch_repo_id']))?$request->submission_data[0]['branch_repo_id']:null;

        $new_ar->agency_repo_id = (isset($request->submission_data[0]['agency_repo_id']))?$request->submission_data[0]['agency_repo_id']:null;

        $new_ar->collection_manager_id = (isset($request->submission_data[0]['collection_manager_id']))?$request->submission_data[0]['collection_manager_id']:null; */

            $new_ar->update();



            // if(isset($request->submission_data[0]['agency_id']) && isset($request->submission_data[0]['agency_manager']) && $request->submission_data[0]['agency_manager'] != '')

            // {

            //     Agency::where('id',$new_ar->agency_id)->update(['agency_manager'=>$request->submission_data[0]['agency_manager'] ,'agency_phone'=>$request->submission_data[0]['agency_phone'],'email'=>$request->submission_data[0]['agency_email']]);

            // }


            if ($new_ar->id) {

                // store parameter wise data

                foreach ($request->parameters as $key => $value) {



                    $new_arb = AuditParameterResult::find($value['id']);

                    $new_arb->audit_id =  $new_ar->id;

                    $new_arb->parameter_id = $key;

                    $new_arb->qm_sheet_id = $request->submission_data[0]['qm_sheet_id'];

                    $new_arb->orignal_weight = $value['parameter_weight'];

                    $new_arb->temp_weight = $value['temp_total_weightage'];

                    $new_arb->with_fatal_score = $value['score_with_fatal'];

                    // $new_arb->without_fatal_score = $value['score_without_fatal'];

                    $new_arb->without_fatal_score = $value['score_with_fatal'];



                    if ($value['temp_total_weightage'] != 0) {

                        $new_arb->with_fatal_score_per = ($value['score_with_fatal'] / $value['temp_total_weightage']) * 100;

                        // $new_arb->without_fatal_score_pre = ($value['score_without_fatal'] / $value['temp_total_weightage'])*100;

                        $new_arb->without_fatal_score_pre = ($value['score_with_fatal'] / $value['temp_total_weightage']) * 100;
                    }

                    // $new_arb->is_critical = $value['is_fatal'];



                    $new_arb->update();



                    // store sub parameter wise data

                    if (isset($value['subs'])) {

                        foreach ($value['subs'] as $key_sb => $value_sb) {

                            if ($value_sb['temp_weight']); {

                                if (isset($value_sb['id'])) {

                                    $new_arc = AuditResult::find($value_sb['id']);

                                    $new_arc->audit_id =  $new_ar->id;

                                    $new_arc->parameter_id = $key;

                                    $new_arc->sub_parameter_id = $key_sb;

                                    // $new_arc->is_critical = $value_sb['is_fatal'];

                                    // $new_arc->is_non_scoring = $value_sb['is_non_scoring'];

                                    // $temp_selected_opt = explode("_",$value_sb['selected_option_model']);

                                    $new_arc->selected_option = ($value_sb['temp_weight'] != 'Critical') ? $value_sb['temp_weight'] : 0;

                                    $new_arc->option_selected = (isset($value_sb['option'])) ? $value_sb['option'] : null;

                                    $new_arc->is_critical = ($value_sb['temp_weight'] != 'Critical') ? 0 : 1;

                                    // $new_arc->is_alert = (array_key_exists('ackalert',$value_sb) && $value_sb['ackalert'] == 1) ? 1 : 0;

                                    // $new_arc->score = ($value_sb['score']!='Critical')?$value_sb['score']:0;

                                    if ($value_sb['score'] != 'rating') {

                                        $new_arc->score = ($value_sb['score'] != 'Critical') ? $value_sb['score'] : 0;

                                        $new_arc->is_percentage = $value_sb['is_percentage'];

                                        $new_arc->selected_per = (isset($value_sb['selected_per']) && $value_sb['selected_per'] != 'select percentage') ? $value_sb['selected_per'] : null;
                                    } else {

                                        $new_arc->score = ($value_sb['score'] != 'Critical') ? $value_sb['temp_weight'] : 0;

                                        $new_arc->is_percentage = $value_sb['is_percentage'];

                                        $new_arc->selected_per = ($value_sb['selected_per'] != 'select percentage') ? $value_sb['selected_per'] : null;
                                    }

                                    // $new_arc->after_audit_weight = $value_sb['temp_weight'];



                                    // if($temp_selected_opt[3]==2||$temp_selected_opt[3]==3)

                                    // if(isset($value_sb['selected_reason_type'])==1&&$value_sb['selected_reason_type']!='')

                                    // {

                                    //     $temp_selected_reason_type = explode("_",$value_sb['selected_reason_type']);

                                    //     $new_arc->reason_type_id = $temp_selected_reason_type[2];

                                    //     $new_arc->reason_id = $value_sb['selected_reason'];

                                    // }

                                    $new_arc->remark = $value_sb['remark'];

                                    $new_arc->update();
                                } else {

                                    $new_arc = new AuditResult;

                                    $new_arc->audit_id =  $new_ar->id;

                                    $new_arc->parameter_id = $key;

                                    $new_arc->sub_parameter_id = $key_sb;

                                    $new_arc->selected_option = ($value_sb['temp_weight'] != 'Critical') ? $value_sb['temp_weight'] : 0;

                                    $new_arc->option_selected = (isset($value_sb['option'])) ? $value_sb['option'] : null;

                                    $new_arc->is_critical = ($value_sb['temp_weight'] != 'Critical') ? 0 : 1;

                                    // $new_arc->is_alert = (array_key_exists('ackalert',$value_sb) && $value_sb['ackalert'] == 1) ? 1 : 0;

                                    if ($value_sb['score'] != 'rating') {

                                        $new_arc->score = ($value_sb['score'] != 'Critical') ? $value_sb['score'] : 0;

                                        $new_arc->is_percentage = $value_sb['is_percentage'];

                                        $new_arc->selected_per = (isset($value_sb['selected_per']) && $value_sb['selected_per'] != 'select percentage') ? $value_sb['selected_per'] : null;
                                    } else {

                                        $new_arc->score = ($value_sb['score'] != 'Critical') ? $value_sb['temp_weight'] : 0;

                                        $new_arc->is_percentage = $value_sb['is_percentage'];

                                        $new_arc->selected_per = ($value_sb['selected_per'] != 'select percentage') ? $value_sb['selected_per'] : null;
                                    }

                                    $new_arc->remark = $value_sb['remark'];

                                    $new_arc->save();
                                }
                            }
                        }
                    }
                }
            }




            
            if (isset($request->submission_data[0]['status']) && $request->submission_data[0]['status'] == 'submit') {
             
                //Audit_Result mail 
                 $agency_email = $request->submission_data[0]['agency_email'];
                 $toEmails = []; // Initialize the 'To' email array
 
                 // Add the agency email as the primary recipient
                 if ($agency_email) {
                     $toEmails[] = $agency_email;
                 }
 
                 // Add the email from lavel_3 to 'To' emails if it exists
                 $user = User::find($new_ar->lavel_3);
                 if ($user && $user->email) {
                     $toEmails[] = $user->email; // Add lavel_3 email
                 }
 
                 // Fetch emails for lavel_4 and lavel_5
                 $lavel4Emails = [];
                 $lavel5Emails = [];
 
                 // Fetch users based on lavel_4 if it exists
                 if (!empty($new_ar->lavel_4)) {
                     $lavel4Ids = explode(',', $new_ar->lavel_4);
                     $lavel4Users = User::whereIn('id', $lavel4Ids)->get(['email']);
                     $lavel4Emails = $lavel4Users->pluck('email')->toArray();
                 }
 
                 // Fetch users based on lavel_5 if it exists
                 if (!empty($new_ar->lavel_5)) {
                     $lavel5Ids = explode(',', $new_ar->lavel_5);
                     $lavel5Users = User::whereIn('id', $lavel5Ids)->get(['email']);
                     $lavel5Emails = $lavel5Users->pluck('email')->toArray();
                 }
 
                 // Merge lavel_4 and lavel_5 emails
                 $ccEmails = array_merge($lavel4Emails, $lavel5Emails);
 
                 // Prepare data for the PDF
                 $agency = DB::table('agencies')->where('id', $request->submission_data[0]['agency_id'])->first();
                 $product = DB::table('products')->where('id', $request->submission_data[0]['product_id'])->first();
                 $audit_cycles = DB::table('audit_cycles')->where('id', $new_ar->audit_cycle_id)->first();
 
                   // echo '<pre>'; print_r($audit_cycles); die;
                 $pdfData = [
                     'Audit_agency' => 'QDegrees',
                     'grade' => $request->submission_data[0]['grade'] ?? null,
                     'score_percentage' => $request->submission_data[0]['with_fatal_score_per'] ?? null,
                     'overall_score' => $request->submission_data[0]['overall_score'] ?? null,
                     'agency_name' => $agency->name ?? null,
                     'location' => $agency->location ?? null,
                     'product_name' => $product->name ?? null,
                     'auditor_id' => Auth::user()->name,
                     'audit_cycle' => $audit_cycles->name,
                     'audit_date' => date('F Y', strtotime($new_ar->audit_date_by_aud)),
                 ];
 
                 // Generate PDF
                 $pdf = Pdf::loadView('audit.audit_result_pdf', $pdfData);
                 $pdfContent = $pdf->output();
 
                 $subject = "RBL Bank Process Review Rating Period for " . $pdfData['audit_cycle'] . " (" . $pdfData['agency_name'] . " - " . $pdfData['product_name'] . ")";
 
                 // Send the email with CC
                 Mail::send([], [], function ($message) use ($toEmails, $ccEmails, $subject, $pdfContent) {
                     $message->from('noreplyall@qdegrees.org', 'Audit Team')
                         ->to($toEmails) // Add all 'To' recipients
                         ->cc($ccEmails) // Add CC recipients
                         ->subject($subject)
                         ->attachData($pdfContent, 'Audit_Result.pdf', [
                             'mime' => 'application/pdf',
                         ])
                         ->setBody(
                             '<p>Dear Associate,</p>
                                  <p>Greetings from RBL Bank!</p>
                                  <p>Thank you for your co-operation & assistance extended to the Process Reviewer in conducting the process review successfully.</p>
                                  <p>Please find attached the audit result for your agency.</p>',
                             'text/html'
                         );
                 });
 
 
 
 
                 //closure mail work
                 $auditId = $new_ar->id;
                 $unsetParams = DB::table('audit_results')
                     ->join('qm_sheet_sub_parameters', 'audit_results.sub_parameter_id', '=', 'qm_sheet_sub_parameters.id')
                     ->where('audit_results.audit_id', $auditId)
                     ->where('audit_results.option_selected', 'Unsatisfactory')
                     ->select('qm_sheet_sub_parameters.sub_parameter as parameter_name', 'audit_results.*', 'audit_results.remark as remarks') // Select relevant fields
                     ->get();
                 $agency_id = DB::table('audits')->where('id', $auditId)->pluck('agency_id')->first();
                 if ($unsetParams->isNotEmpty()) {
 
                     $agency_details = DB::table('agencies')->where('id', $agency_id)->first();
                     $audit_details = DB::table('audits')->where('id', $auditId)->first();
                     $process_review_month = DB::table('audit_cycles')->where('id', $audit_details->audit_cycle_id)->pluck('name')->first();
                     if (!empty($unsetParams)) {
                         // Check if audit already exists in closure_audits
                         $auditExists = DB::table('closure_audits')
                             ->where('audit_id', $auditId)
                             ->first(); // Use first() instead of exists()
 
                         if (!$auditExists) {  // If no record is found, $auditExists will be null
                             $closureId = DB::table('closure_audits')->insertGetId([
                                 'audit_id' => $auditId,
                                 'agency_id' => $agency_id, // Save agency_id
                                 'remarks' => '',
                                 'created_at' => now(),
                                 'audit_agency_id' => $audit_agency_id,
                             ]);
                         } else {
                             $closureId = $auditExists->id;
                         }
                         // Insert the audit into closure_audits and get the closure_id
 
                         // Assuming you have the agency email stored somewhere
                         $agencyEmail = $request->submission_data[0]['agency_email']; // Replace this with the actual agency email
 
                         // Prepare data for the PDF
                         $pdfData = [
                             'unsetParams' => $unsetParams,
                             'audit_id' => $auditId,
                             'agency_details' => $agency_details, // Replace with actual agency name if available
                             'audit_details' => $audit_details, // Replace with actual agency name if available
                             'process_review_month' => $process_review_month,
                             'audit_agency_id' => $audit_agency_id,  // Save audit_agency_id
                         ];
 
                         // Generate PDF for unset parameters
                         $pdf = PDF::loadView('closure.audit_closure_pdf', $pdfData);
                         $pdfContent = $pdf->output();
 
                         // Prepare the closure form link with closure_id
                         $closureFormLink = url("audit-closure-justification/{$closureId}/{$auditId}");
 
                         // Prepare email subject
                         $subject = "Audit Closure Form for Audit Agency: {$agency_details->name}";
                         $agency_name = $agency_details->name;
                         // Send email with the PDF attached using a view for the email body
                         Mail::send('closure.audit_closure_mail', ['closureFormLink' => $closureFormLink, 'process_review_month' => $process_review_month, 'agency_name' => $agency_name], function ($message) use ($agencyEmail, $subject, $pdfContent) {
                             $message->from('noreplyall@qdegrees.org', 'Audit Team')
                                 ->to($agencyEmail)
                                 ->subject($subject)
                                 ->attachData($pdfContent, 'UNSATISFACTORY_PARAMETERS.pdf', [
                                     'mime' => 'application/pdf',
                                 ]);
                         });
                     }
                 } else {
                     $closureId = DB::table('closure_audits')
                         ->insertGetId([
                             'audit_id' => $auditId,
                             'agency_id' => $agency_id,  // Save agency_id
                             'audit_agency_id' => $audit_agency_id,  // Save audit_agency_id
                             'remarks' => '',
                             'status' => '1',
                             'created_at' => now(),
                         ]);
                 }
 
                 SavedAudit::where('audit_id', $new_ar->id)->delete();
             }

            DB::commit();

            return response()->json(['status' => 200, 'message' => "Audit saved successfully."], 200);
        } catch (\Exception $e) {
           // echo '<pre>'; print_r($e->getMessage()); die;
            DB::rollback();
            return response()->json(['status' => 500, 'message' => "Audit saved unsuccessfully.", 'audit_id' => ''], 500);
        }
    }

    public function get_reasons_by_type($type_id)
    {

        $all_reasons = Reason::where('reason_type_id', $type_id)->pluck('name', 'id');

        return response()->json(['status' => 200, 'message' => ".", 'data' => $all_reasons], 200);

    }

    public function getUsers($val, $type)
    {

        $user = User::role('Collection Manager')->where('name', 'like', "%$val%")->get();

        return response()->json(['data' => $user]);

    }

    public function rejectUsers($email, $auditId, $type)
    {

        $audit = Audit::find($auditId);

        switch ($type) {

            case 'collection':

                $audit->collection_manager_email = null;

                break;

            case 'agency':

                $audit->agency_manager_email = null;

                break;

            case 'yard':

                $audit->yard_manager_email = null;

                break;

        }

        $audit->save();

        return response()->json(['status' => true]);

    }

    public function saveUsers($email, $auditId, $type, $userid)
    {

        $audit = Audit::find($auditId);

        if ($audit->branch_id != null) {

            $branch = $audit->branch_id;

        } else if ($audit->agency_id != null) {

            $branch = Agency::find($audit->agency_id)->branch_id;

        } else {

            $branch = Yard::find($audit->yard_id)->branch_id;

        }

        switch ($type) {

            case 'collection':

                $audit->collection_manager_email = null;

                if ($branch) {

                    $user = User::where('email', $email)->first();

                    $branchableID = Branchable::where(['branch_id' => $branch, 'manager_id' => $userid])->update(['manager_id' => $user->id]);

                }

                break;

            case 'agency':

                $audit->agency_manager_email = null;

                $user = User::where('email', $email)->first();

                $agency = Agency::where('id', $audit->agency_id)->update(['agency_manager' => $user->id]);

                break;

            case 'yard':

                $audit->yard_manager_email = null;

                $user = User::where('email', $email)->first();

                $yard = Yard::where('id', $audit->yard_id)->update(['agency_manager' => $user->id]);

                break;

        }

        $audit->save();

        return response()->json(['status' => true]);

    }

    public function excelDownloadQaChanges(Request $request)
    {

        ini_set('memory_limit', '-1');

        ini_set('max_execution_time', 3000);
        $filter_data = $request->all();
        return Excel::download(new QcAndQaChangesExport($filter_data), 'Qa_changes.xlsx');

    }

    public function reportAutomation(Request $request)
    {

        $branchList = Branch::select('id', 'name')->get();
        $has_data = 0;
        return view('audit.report_automate', compact('branchList', 'has_data'));
    }

    public function reportAutomationData(Request $request)
    {

        $branchList = Branch::select('id', 'name')->get();
        if ($request->isMethod('get')) {

            $ids = Qc::with('user')->get()->keyBy('audit_id');
            $savedQcIds = SavedQcAudit::all()->pluck('audit_id')->toArray();
            //echo "<pre>"; print_r($ids); die;
            $getBranch = Branch::with('city', 'city.state', 'branchable', 'yard', 'branchRepo', 'agencyRepo', 'agency', 'branchable.user')->find($request->branch);

            $yardID = array();
            $agencyID = array();
            $branchRepoID = array();
            $agencyRepoID = array();
            $collectionManagerID = array();

            if ($getBranch) {
                foreach ($getBranch->yard as $key => $value) {
                    $yardID[] = $value->id;
                }
                foreach ($getBranch->agency as $key => $value) {
                    $agencyID[] = $value->id;
                }
                foreach ($getBranch->branchRepo as $key => $value) {
                    $branchRepoID[] = $value->id;
                }
                foreach ($getBranch->agencyRepo as $key => $value) {
                    $agencyRepoID[] = $value->id;
                }
                foreach ($getBranch->branchable as $key => $value) {
                    if (trim($value->type) == "Collection_Manager") {
                        $collectionManagerID[] = $value->manager_id;
                    }
                }
            }

            $brID = $request->branch;
            $start_date = $request->start_date;
            $end_date = $request->end_date;

            //$start_date=date('Y-m-d', strtotime("-3 month", strtotime($request->start_date)));

            if ($ids->count() > 0) {
                $data = Audit::with(['qmsheet', 'qmsheet.qm_sheet_sub_parameter', 'branch.branchableCollection', 'productnew', 'branch.yard', 'branch.agency', 'branch.city.state', 'branch.branchable', 'yard.branch.city.state', 'agency.branch.city.state', 'qa_qtl_detail', 'branchRepo.branch.city.state', 'agencyRepo.branch.city.state', 'audit_parameter_result', 'audit_results', 'qc', 'qc.user', 'collectionManagerData'])
                    ->whereIn('id', $ids->pluck('audit_id'))->where('parent_branch_id', $request->branch)->whereNotIn('id', $savedQcIds)->whereDate('audit_date_by_aud', '>=', $request->start_date)->whereDate('audit_date_by_aud', '<=', $request->end_date)->orderBy('id', 'desc')->get();
            } else {
                $data = Audit::with(['qmsheet', 'qmsheet.qm_sheet_sub_parameter', 'productnew', 'branch.branchableCollection', 'branch.yard', 'branch.agency', 'branch.city.state', 'branch.branchable', 'yard.branch.city.state', 'agency.branch.city.state', 'qa_qtl_detail', 'branchRepo.branch.city.state', 'agencyRepo.branch.city.state', 'audit_parameter_result', 'audit_results', 'qc', 'qc.user', 'collectionManagerData'])
                    ->whereNotIn('id', $savedQcIds)->where('parent_branch_id', $request->branch)->whereDate('audit_date_by_aud', '>=', $request->start_date)->whereDate('audit_date_by_aud', '<=', $request->end_date)->orderBy('id', 'desc')->get();
            }

            $oldSco = OldScore::where('branch_id', $request->branch)->where('type', 0)->first();

            $proWiseSco = OldScore::where('branch_id', $request->branch)->where('type', 1)->get();

            $has_data = 1;

            $auditCycle = AuditCycle::orderBy('id', 'desc')->limit(3)->get()->toArray();

            $getAcr = $this->getAcrReportData($getBranch->id, $request->start_date);

            return view('audit.report_automate_data', compact('data', 'oldSco', 'proWiseSco', 'auditCycle', 'branchList', 'getBranch', 'has_data', 'start_date', 'end_date', 'brID', 'collectionManagerID', 'agencyRepoID', 'branchRepoID', 'agencyID', 'yardID', 'getAcr'));
        }

    }

    public function reportAutomationDataColl(Request $request)
    {

        $ids = Qc::with('user')->get()->keyBy('audit_id');
        $savedQcIds = SavedQcAudit::all()->pluck('audit_id')->toArray();
        //echo "<pre>"; print_r($ids); die;
        $getBranch = Branch::with('branchable', 'yard', 'branchRepo', 'agencyRepo', 'agency', 'branchable.user')->find($request->branch);

        $yardID = array();
        $agencyID = array();
        $branchRepoID = array();
        $agencyRepoID = array();
        $collectionManagerID = array();

        if ($getBranch) {
            foreach ($getBranch->yard as $key => $value) {
                $yardID[] = $value->id;
            }
            foreach ($getBranch->agency as $key => $value) {
                $agencyID[] = $value->id;
            }
            foreach ($getBranch->branchRepo as $key => $value) {
                $branchRepoID[] = $value->id;
            }
            foreach ($getBranch->agencyRepo as $key => $value) {
                $agencyRepoID[] = $value->id;
            }
            foreach ($getBranch->branchable as $key => $value) {
                if (trim($value->type) == "Collection_Manager" && $value->status == 2) {
                    $collectionManagerID[] = $value->manager_id;
                }
            }
        }
        $brID = $request->branch;
        if ($ids->count() > 0) {
            $data = Audit::with(['qmsheet', 'qmsheet.qm_sheet_sub_parameter', 'productnew', 'yard.branch.city.state', 'agency.branch.city.state', 'qa_qtl_detail', 'branchRepo.branch.city.state', 'agencyRepo.branch.city.state', 'audit_parameter_result', 'audit_results', 'qc', 'qc.user', 'collectionManagerData', 'audit_results.parameter_detail', 'audit_results.sub_parameter_detail'])
                ->whereIn('id', $ids->pluck('audit_id'))->where('parent_branch_id', $brID)->whereIn('collection_manager_id', $collectionManagerID)->whereNotIn('id', $savedQcIds)->whereDate('audit_date_by_aud', '>=', $request->start_date)->whereDate('audit_date_by_aud', '<=', $request->end_date)->get();
        } else {
            $data = Audit::with(['qmsheet', 'qmsheet.qm_sheet_sub_parameter', 'productnew', 'yard.branch.city.state', 'agency.branch.city.state', 'qa_qtl_detail', 'branchRepo.branch.city.state', 'agencyRepo.branch.city.state', 'audit_parameter_result', 'audit_results', 'qc', 'qc.user', 'collectionManagerData', 'audit_results.parameter_detail', 'audit_results.sub_parameter_detail'])
                ->whereNotIn('id', $savedQcIds)->whereIn('collection_manager_id', $collectionManagerID)->where('parent_branch_id', $brID)->whereDate('audit_date_by_aud', '>=', $request->start_date)->whereDate('audit_date_by_aud', '<=', $request->end_date)->get();
        }

        $calculation = DB::select(DB::raw("Select * from 6040_calculation"));

        $start_date = $request->start_date;
        $end_date = $request->end_date;
        $selecollMan = ($request->cmid) ? $request->cmid : $collectionManagerID[0];
        $has_data = 1;

        return view('audit.report_automate_collec', compact('data', 'calculation', 'getBranch', 'has_data', 'start_date', 'end_date', 'collectionManagerID', 'selecollMan'));

    }

    public function reportAutomationDatagap(Request $request)
    {

        $ids = Qc::with('user')->get()->keyBy('audit_id');
        $savedQcIds = SavedQcAudit::all()->pluck('audit_id')->toArray();
        //echo "<pre>"; print_r($ids); die;
        $getBranch = Branch::with('branchable', 'yard', 'branchRepo', 'agencyRepo', 'agency', 'branchable.user')->find($request->branch);

        $yardID = array();
        $agencyID = array();
        $branchRepoID = array();
        $agencyRepoID = array();
        $collectionManagerID = array();

        if ($getBranch) {
            foreach ($getBranch->yard as $key => $value) {
                $yardID[] = $value->id;
            }
            foreach ($getBranch->agency as $key => $value) {
                $agencyID[] = $value->id;
            }
            foreach ($getBranch->branchRepo as $key => $value) {
                $branchRepoID[] = $value->id;
            }
            foreach ($getBranch->agencyRepo as $key => $value) {
                $agencyRepoID[] = $value->id;
            }
            foreach ($getBranch->branchable as $key => $value) {
                if (trim($value->type) == "Collection_Manager" && $value->status == 2) {
                    $collectionManagerID[] = $value->manager_id;
                }
            }
        }

        $brID = $request->branch;

        if ($ids->count() > 0) {
            $data = Audit::whereIn('id', $ids->pluck('audit_id'))->whereIn('agency_id', $agencyID)->where('parent_branch_id', $brID)->whereNotIn('id', $savedQcIds)->whereDate('audit_date_by_aud', '>=', $request->start_date)->whereDate('audit_date_by_aud', '<=', $request->end_date)->get();
        } else {
            $data = Audit::whereNotIn('id', $savedQcIds)->whereIn('agency_id', $agencyID)->where('parent_branch_id', $brID)->whereDate('audit_date_by_aud', '>=', $request->start_date)->whereDate('audit_date_by_aud', '<=', $request->end_date)->get();
        }

        $start_date = $request->start_date;
        $end_date = $request->end_date;
        $selecollMan = ($request->cmid) ? $request->cmid : 'all';
        $has_data = 1;

        $getAgency = Agency::find($selecollMan);
        $auditCycle = AuditCycle::orderBy('id', 'desc')->limit(3)->get()->toArray();

        $depositionData = CashDepositionData::with('agency', 'branch')->whereDate('date', '>=', $start_date)->whereDate('date', '<=', $end_date)->get();
        $receiptData = ReceiptCutData::with('agency', 'branch')->whereDate('date', '>=', $start_date)->whereDate('date', '<=', $end_date)->get();
        $secData = DelaySeconAllocData::with('agency', 'branch')->whereDate('date', '>=', $start_date)->whereDate('date', '<=', $end_date)->get();

        return view('audit.report_automate_gap', compact('data', 'getBranch', 'auditCycle', 'has_data', 'start_date', 'end_date', 'agencyID', 'selecollMan', 'getAgency', 'depositionData', 'receiptData', 'secData'));

    }

    public function reportDataUploader(Request $request)
    {

        if ($request->hasFile('acr_report')) {
            $data = Excel::import(new AcrImport([
                'uploaded_by' => Auth::User()->id,
            ]), $request->acr_report);
        }

        if ($request->hasFile('dac_uploader')) {
            $data = Excel::import(new CashDespositionImport([
                'uploaded_by' => Auth::User()->id,
            ]), $request->dac_uploader);
        }

        if ($request->hasFile('score_upload')) {
            $data = Excel::import(new OldscoreImport([
                'uploaded_by' => Auth::User()->id,
            ]), $request->score_upload);
        }

        return redirect()->route('reportAutomation')->withStatus(__('Data Uploaded Successfully.'));
    }

    public function getMonths($quarter, $year)
    {
        switch ($quarter) {
            case 1:return array('Jan_' . $year, 'Feb_' . $year, 'Mar_' . $year);
            case 2:return array('Apr_' . $year, 'May_' . $year, 'Jun_' . $year);
            case 3:return array('Jul_' . $year, 'Aug_' . $year, 'Sep_' . $year);
            case 4:return array('Oct_' . $year, 'Nov_' . $year, 'Dec_' . $year);
        }
    }

    public function getAcrReportData($branch_id, $start_date)
    {

        $final_data = array();

        $pre_mon = array();

        $pre_mon[0] = date('Y-M', strtotime("-1 month", strtotime(date("Y-m-d", strtotime($start_date)))));
        $pre_mon[1] = date('Y-M', strtotime("-2 month", strtotime(date("Y-m-d", strtotime($start_date)))));
        $pre_mon[2] = date('Y-M', strtotime("-3 month", strtotime(date("Y-m-d", strtotime($start_date)))));

        $get = AcrReportData::with('agency', 'product')->where('branch_id', $branch_id)->whereIn('month', $pre_mon)->get();

        $agency_arr_id = array();
        $product_arr_id = array();
        foreach ($get as $g) {
            if (!in_array($g->agency_id, $agency_arr_id)) {
                $agency_arr_id[] = $g->agency_id;
            }
            if (!in_array($g->product_group, $product_arr_id)) {
                $product_arr_id[] = $g->product_group;
            }
        }
        $allocation_capacity = array();
        foreach ($agency_arr_id as $a) {
            $data = array();
            $data['agency_code'] = $a;
            $data['agency_name'] = "";
            $data['fos_count'][0] = 0;
            $data['fos_count'][1] = 0;
            $data['fos_count'][2] = 0;
            $data['alloc_count'][0] = 0;
            $data['alloc_count'][1] = 0;
            $data['alloc_count'][2] = 0;
            $data['capacity'] = 0;
            $data['avg_alloc_count'] = 0;
            $data['avg_fos_count'] = 0;
            $data['gap'] = 0;

            $uniqFos[0] = array();
            $uniqFos[1] = array();
            $uniqFos[2] = array();
            $is_flow = 0;
            $recovery = array();
            $pro = array();
            foreach ($get as $g) {
                if ($g->agency_id == $a) {

                    $data['agency_name'] = $g->agency->name;
                    if ($g->month == $pre_mon[0]) {
                        if (!in_array($g->agent_id, $uniqFos[0])) {
                            $data['fos_count'][0] += 1;
                            $uniqFos[0][] = $g->agent_id;
                        }
                        $data['alloc_count'][0] += 1;
                    }
                    if ($g->month == $pre_mon[1]) {
                        if (!in_array($g->agent_id, $uniqFos[1])) {
                            $data['fos_count'][1] += 1;
                            $uniqFos[1][] = $g->agent_id;
                        }
                        $data['alloc_count'][1] += 1;
                    }
                    if ($g->month == $pre_mon[2]) {
                        if (!in_array($g->agent_id, $uniqFos[2])) {
                            $data['fos_count'][2] += 1;
                            $uniqFos[2][] = $g->agent_id;
                        }
                        $data['alloc_count'][2] += 1;
                    }

                    if ($g->product) {
                        if (!in_array($g->product->id, $pro)) {
                            if ($g->product->is_recovery == 0) {
                                $is_flow += 1;
                            } else {
                                $recovery[] = $g->product->capacity;
                            }
                        }
                    }
                }
            }

            $flow_capacity = $is_flow * 80;
            $data['capacity'] = $flow_capacity * array_sum($data['fos_count']);
            if (count($recovery) > 0) {
                foreach ($recovery as $r) {
                    $data['capacity'] += (array_sum($data['fos_count']) * $r);
                }
            }
            $data['avg_fos_count'] = (array_sum($data['fos_count']) != 0) ? round(array_sum($data['fos_count']) / 3) : 0;
            $data['avg_alloc_count'] = (array_sum($data['alloc_count']) != 0) ? round(array_sum($data['alloc_count']) / 3) : 0;
            $data['gap'] = $data['avg_alloc_count'] - $data['capacity'];
            $allocation_capacity[] = $data;
        }
        $pro_detail = array();
        foreach ($product_arr_id as $p) {
            $data = array();
            $data['pro_name'] = $p;
            $pro_cap = 0;
            $uni_agen[0] = array();
            $uni_agen[1] = array();
            $uni_agen[2] = array();
            $data['main'][0] = 0;
            $data['main'][1] = 0;
            $data['main'][2] = 0;
            $data['crossed'][0] = 0;
            $data['crossed'][1] = 0;
            $data['crossed'][2] = 0;

            foreach ($get as $g) {
                if ($g->product_group == $p) {
                    $pro_cap = ($g->product) ? $g->product->capacity : 0;
                    if ($g->month == $pre_mon[0]) {
                        if (!array_key_exists($g->agent_id, $uni_agen[0])) {
                            $uni_agen[0][$g->agent_id] = 0;
                        }
                        if (array_key_exists($g->agent_id, $uni_agen[0])) {
                            $uni_agen[0][$g->agent_id] += 1;
                        }
                    }
                    if ($g->month == $pre_mon[1]) {
                        if (!array_key_exists($g->agent_id, $uni_agen[1])) {
                            $uni_agen[1][$g->agent_id] = 0;
                        }
                        if (array_key_exists($g->agent_id, $uni_agen[1])) {
                            $uni_agen[1][$g->agent_id] += 1;
                        }
                    }
                    if ($g->month == $pre_mon[2]) {
                        if (!array_key_exists($g->agent_id, $uni_agen[2])) {
                            $uni_agen[2][$g->agent_id] = 0;
                        }
                        if (array_key_exists($g->agent_id, $uni_agen[2])) {
                            $uni_agen[2][$g->agent_id] += 1;
                        }
                    }
                }
            }

            foreach ($uni_agen[2] as $a) {
                if ($a == $pro_cap) {
                    $data['main'][2] += 1;
                } else {
                    if ($a > $pro_cap) {
                        $data['crossed'][2] += 1;
                    }
                }
            }

            foreach ($uni_agen[0] as $a) {
                if ($a == $pro_cap) {
                    $data['main'][0] += 1;
                } else {
                    if ($a > $pro_cap) {
                        $data['crossed'][0] += 1;
                    }
                }
            }

            foreach ($uni_agen[1] as $a) {
                if ($a == $pro_cap) {
                    $data['main'][1] += 1;
                } else {
                    if ($a > $pro_cap) {
                        $data['crossed'][1] += 1;
                    }
                }
            }

            $pro_detail[] = $data;
        }

        $final_data['pre_mon'] = $pre_mon;
        $final_data['allocation_capacity'] = $allocation_capacity;
        $final_data['pro_detail'] = $pro_detail;

        return $final_data;
    }

    public function createCycle(Request $request)
    {
        if ($request->isMethod('post')) {
            if ($request->cycle_name) {
                $data = array();
                $data['created_by'] = Auth::user()->id;
                $data['name'] = $request->cycle_name;
                $has = AuditCycle::where('name', $data['name'])->first();
                if (!$has) {
                    AuditCycle::create($data);
                    return redirect('list-audit-cycle')->withStatus(__('Cycle created successfully.'));
                } else {
                    return redirect('list-audit-cycle')->withStatus(__('Cycle already available.'));
                }
            } else {
                return redirect('list-audit-cycle')->withStatus(__('Cycle name not available.'));
            }

        }
        return view('audit_cycle.create_audit_cycle');
    }

    public function listCycle(Request $request)
    {
        $data = AuditCycle::orderBy("id", "desc")->get();
        return view('audit_cycle.audit_cycle_list', compact('data'));
    }

    public function editCycle(Request $request, $id)
    {
        if ($request->isMethod('put')) {
            // Validation
            $request->validate([
                'cycle_name' => 'required|string|max:255|unique:audit_cycles,name,' . $id,
            ]);
    
            // Find the cycle
            $cycle = AuditCycle::find($id);
    
            if ($cycle) {
                $cycle->name = $request->cycle_name;
                $cycle->created_by = Auth::id();
                $cycle->save();
    
                return redirect()->route('list-audit-cycle')->with('status', __('Cycle updated successfully.'));
            } else {
                return redirect()->route('list-audit-cycle')->with('status', __('Cycle not found.'));
            }
        }
    
        // Handle GET request to show the form
        $cycle = AuditCycle::find($id);
        if (!$cycle) {
            return redirect()->route('list-audit-cycle')->with('status', __('Cycle not found.'));
        }
    
        return view('audit_cycle.audit_cycle_edit', compact('cycle'));
    }
    

    public function toggleStatus(Request $request)
    {
        $row = AuditCycle::find($request->id);
    
        if ($row) {
            if ($request->status == '1') {
                $activeCount = AuditCycle::where('status', '1')->count();
    
    
                if ($activeCount >= 3) {
                    return response()->json([
                        'success' => false,
                        'message' => 'You cannot activate more than 3 audit cycles.',
                    ]);
                }
            }
    
            // Update the status
            $row->status = $request->status;
            $row->save();
    
            $buttonHtml = ($row->status == '0' || $row->status == '2')
                ? '<button class="toggle-status btn btn-success" data-id="' . $row->id . '" data-status="1">Activate</button>'
                : '<button class="toggle-status btn btn-danger" data-id="' . $row->id . '" data-status="2">Deactivate</button>';
    
            return response()->json([
                'success' => true,
                'buttonHtml' => $buttonHtml,
                'message' => 'Status updated successfully!',
            ]);
        }
    
        return response()->json(['success' => false, 'message' => 'Failed to update status.']);
    }

    // public function sendAgencyOtp(Request $request) {

    //    // echo '<pre>'; print_r($request->all()); die;
    //     // Get email from the request
    //   //  $email = $request->input('email');
    //   $email = 'ravi.prajapat@qdegrees.org';
    //     // Generate a 6-digit OTP
    //     $otp = rand(100000, 999999);

    //     // Save the OTP in the database
    //     $otpData = [
    //         'audit_id' => 1, // Set audit_id if available
    //         'agency_id' => 1, // Set agency_id if available
    //         'mobile_number' => null, // Set mobile_number if available
    //         'otp' => $otp,
    //         'type' => 'agency', // Assuming type to differentiate between email/mobile OTPs
    //         'email' => $email,
    //         'created_at' => Carbon::now(),
    //         'updated_at' => Carbon::now()
    //     ];

    //     DB::table('otp_verifications')->insert($otpData);

    //     // Send OTP via email
    //     $subject = "Your OTP for Audit Confirmation";
    //     Mail::send('audit.agency_otp_mail', ["otp" => $otp], function ($message) use($email, $subject) {
    //         $message->from(env('MAIL_FROM_ADDRESS'), 'RBL Bank Test Mail')
    //                 ->to($email)
    //                 ->subject($subject);
    //     });

    //     return response()->json(['message' => 'OTP sent successfully']);
    // }

    // public function confirmAgencyOtp(Request $request) {
    //     // Get the email addresses from the request

    //   //  echo '<pre>'; print_r($request->all()); die;
    //     $agencyEmail = $request->input('agency_email');
    //     $cmEmail = 'ravi.prajapat@qdegrees.org';

    //     // Generate a 6-digit OTP for the agency
    //     $agencyOtp = rand(100000, 999999);

    //     // Generate a different 6-digit OTP for the collection manager
    //     $cmOtp = rand(100000, 999999);

    //     // Save the OTP for the agency in the database
    //     $agencyOtpData = [
    //         'audit_id' => 1, // Set audit_id if available
    //         'agency_id' => 1, // Set agency_id if available
    //         'mobile_number' => null,
    //         'otp' => $agencyOtp,
    //         'type' => 'agency',
    //         'email' => $agencyEmail,
    //         'created_at' => Carbon::now(),
    //         'updated_at' => Carbon::now()
    //     ];
    //     DB::table('otp_verifications')->insert($agencyOtpData);

    //     // Save the OTP for the collection manager in the database
    //     $cmOtpData = [
    //         'audit_id' => 1, // Set audit_id if available
    //         'agency_id' => 1, // Set agency_id if available
    //         'mobile_number' => null,
    //         'otp' => $cmOtp,
    //         'type' => 'cm',
    //         'email' => $cmEmail,
    //         'created_at' => Carbon::now(),
    //         'updated_at' => Carbon::now()
    //     ];
    //     DB::table('otp_verifications')->insert($cmOtpData);

    //     // Send OTP via email to the agency
    //     $agencySubject = "Your OTP for Audit Confirmation";
    //     Mail::send('audit.agency_otp_mail', ["otp" => $agencyOtp], function ($message) use($agencyEmail, $agencySubject) {
    //         $message->from(env('MAIL_FROM_ADDRESS'), 'RBL Bank Test Mail')
    //                 ->to($agencyEmail)
    //                 ->subject($agencySubject);
    //     });

    //     // Send OTP via email to the collection manager
    //     $cmSubject = "Your OTP for Audit Confirmation";
    //     Mail::send('audit.agency_otp_mail', ["otp" => $cmOtp], function ($message) use($cmEmail, $cmSubject) {
    //         $message->from(env('MAIL_FROM_ADDRESS'), 'RBL Bank Test Mail')
    //                 ->to($cmEmail)
    //                 ->subject($cmSubject);
    //     });

    //     return response()->json(['message' => 'OTPs sent successfully']);
    // }

    public function verifyAgencyOtp(Request $request)
    {
        // dd($request);
        $request->validate([
            'otp' => 'required|numeric',
            'agency_id' => 'required|integer', // Validate agency_id
            'agency_email' => 'required|email', // Validate agency email
            'type' => 'required', // Include type check
        ]);

        $otp = $request->input('otp');
        $agencyId = $request->input('agency_id');
        $agencyEmail = $request->input('agency_email');
        $type = $request->input('type');

        if ($type == 'collection_manager') {
            $cmUser = DB::table('users')->where('id', $request->cm_manager_id)->first();
            $collectionManagerEmail = $cmUser->email;
            $otpRecord = DB::table('otp_verifications')
                ->where('otp', $otp)
                ->where('type', $type)
                ->where('created_at', '>', Carbon::now()->subMinutes(10)) // Optional: OTP expiry check
                ->where('agency_id', $agencyId)
                ->where('email', $collectionManagerEmail)
                ->first();
        } else {
            // Check if OTP exists in the database and is valid for agency
            $otpRecord = DB::table('otp_verifications')
                ->where('otp', $otp)
                ->where('type', $type)
                ->where('created_at', '>', Carbon::now()->subMinutes(10)) // Optional: OTP expiry check
                ->where('agency_id', $agencyId)
                ->where('email', $agencyEmail)
                ->first();
        }

        if ($otpRecord) {
            // OTP is valid
            return response()->json('valid');
        } else {
            // OTP is invalid
            return response()->json('invalid');
        }

    }

    public function sendAgencyOtp(Request $request)
    {

        // echo '<pre>'; print_r($request->all()); die;
        // Extract email from request
        $email = $request->agency_email;
        $agency_details = DB::table('agencies')->where('id', $request->agency_id)->first();
        $product_details = DB::table('products')->where('id', $request->product_id)->first();
        $audit_cycle = DB::table('audit_cycles')->where('id', $request->audit_cycle)->first();

        $audit_date = Carbon::now()->toDateString();
        //  echo '<pre>'; print_r($request->product_id); die;
        //   echo '<pre>'; print_r($email); die;
        // Generate OTP
        $otp = rand(100000, 999999);

        // Prepare OTP data
        $otpData = [
            'otp' => $otp,
            'mobile_number' => null, // Set mobile_number if available
            'type' => 'agency', // Assuming type to differentiate between email/mobile OTPs
            'email' => $email,
            'created_at' => Carbon::now(), // Only relevant for inserts
            'updated_at' => Carbon::now(), // Always updated
        ];

        // Use updateOrInsert to avoid duplicates
        DB::table('otp_verifications')->updateOrInsert(
            [
                'audit_id' => 1, // Set audit_id if available
                'agency_id' => $agency_details->id, // Set agency_id if available
                'type' => 'agency', // Matching condition
                'email' => $email, // Matching condition
            ],
            $otpData
        );

        // Fetch parameters with status
        $parameters = $this->fetchParametersWithStatus($request->parameters);

        // echo '<pre>'; print_r($parameters); die;
        // Prepare data for PDF
        $pdfData = [
            'agency_name' => 'Sample Agency',
            'audit_date' => Carbon::now()->toDateString(),
            'parameters' => $parameters,
            'agency_details' => $agency_details,
            'product_details' => $product_details,
            'audit_cycle' => $audit_cycle->name,

            'score' => $request->input('overall_score', 0),
        ];

        // Generate PDF
        $pdf = Pdf::loadView('audit.checksheet_pdf', $pdfData);
        $pdfContent = $pdf->output();
        // dd(env('MAIL_FROM_ADDRESS'),$email);
        // Send email with OTP and PDF attached
        $subject = "Your OTP for Audit Confirmation";
        // Check if collection manager is selected
        $collectionManagerOtpSent = false;
        if ($request->collection_manager) {

            $cmUser = DB::table('users')->where('id', $request->collection_manager)->first();
            $collectionManagerEmail = $cmUser->email;

            // Generate OTP for collection manager
            $collectionManagerOtp = rand(100000, 999999);

            // Insert OTP data for collection manager
            DB::table('otp_verifications')->updateOrInsert(
                // Conditions to check for an existing record
                [
                    'audit_id' => 1,
                    'agency_id' => $agency_details->id,
                    'type' => 'collection_manager',
                    'email' => $collectionManagerEmail,
                ],
                // Data to insert or update
                [
                    'otp' => $collectionManagerOtp,
                    'mobile_number' => null, // Set mobile_number if available
                    'created_at' => Carbon::now(), // This will only be used if inserting
                    'updated_at' => Carbon::now(), // Always updated
                ]
            );

            // Set flag that collection manager OTP has been sent
            $collectionManagerOtpSent = true;

            // Send OTP to collection manager email (similar to agency OTP logic)
            Mail::send('audit.collection_manager_otp_mail', ["otp" => $collectionManagerOtp, "agency_details" => $agency_details, "audit_date" => $audit_date], function ($message) use ($email, $subject, $pdfContent, $collectionManagerEmail) {
                $message->from('noreplyall@qdegrees.org', 'Audit Team')
                    ->to($collectionManagerEmail)
                    ->subject($subject)
                    ->attachData($pdfContent, 'Audit_Checksheet.pdf', [
                        'mime' => 'application/pdf',
                    ]);
            });
        }

        Mail::send('audit.agency_otp_mail', ["otp" => $otp, "agency_details" => $agency_details, "audit_date" => $audit_date], function ($message) use ($email, $subject, $pdfContent) {
            $message->from('noreplyall@qdegrees.org', 'Audit Team')
                ->to($email)
                ->subject($subject)
                ->attachData($pdfContent, 'Audit_Checksheet.pdf', [
                    'mime' => 'application/pdf',
                ]);
        });
        // Return response indicating success
        return response()->json([
            'message' => 'OTP and PDF sent successfully',
            'collection_manager_otp_sent' => $collectionManagerOtpSent,
        ]);
    }
    public function sendCollectionManagerOtp(Request $request)
    {

        $user = DB::table('users')->where('id', $request->level3_user_id)->first();
        $email = $user->email;
        $agency_details = DB::table('agencies')->where('id', $request->agency_id)->first();
        $product_details = DB::table('products')->where('id', $request->product_id)->first();
        $audit_cycle = DB::table('audit_cycles')->where('id', $request->audit_cycle)->first();

        $audit_date = Carbon::now()->toDateString();
        //  echo '<pre>'; print_r($request->product_id); die;
        //   echo '<pre>'; print_r($email); die;
        // Generate OTP
        $otp = rand(100000, 999999);

        // Insert OTP data into the database
        $otpData = [
            'audit_id' => 1, // Set audit_id if available
            'agency_id' => $agency_details->id, // Set agency_id if available
            'mobile_number' => null, // Set mobile_number if available
            'otp' => $otp,
            'type' => 'collection-manager', // Assuming type to differentiate between email/mobile OTPs
            'email' => $email,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ];
        DB::table('otp_verifications')->insert($otpData);
        // Fetch parameters with status
        $parameters = $this->fetchParametersWithStatus($request->parameters);

        // echo '<pre>'; print_r($parameters); die;
        // Prepare data for PDF
        $pdfData = [
            'agency_name' => 'Sample Agency',
            'audit_date' => Carbon::now()->toDateString(),
            'parameters' => $parameters,
            'agency_details' => $agency_details,
            'product_details' => $product_details,
            'audit_cycle' => $audit_cycle->name,

            'score' => $request->input('overall_score', 0),
        ];

        // Generate PDF
        $pdf = Pdf::loadView('audit.checksheet_pdf', $pdfData);
        $pdfContent = $pdf->output();
        // dd(env('MAIL_FROM_ADDRESS'),$email);
        // Send email with OTP and PDF attached
        $subject = "Your OTP for Audit Confirmation";
        Mail::send('audit.agency_otp_mail', ["otp" => $otp, "agency_details" => $agency_details, "audit_date" => $audit_date], function ($message) use ($email, $subject, $pdfContent) {
            $message->from('noreplyall@qdegrees.org', 'Audit Team')
                ->to($email)
                ->subject($subject)
                ->attachData($pdfContent, 'Audit_Checksheet.pdf', [
                    'mime' => 'application/pdf',
                ]);
        });

        return response()->json(['message' => 'OTP and PDF sent successfully']);
    }

    public function resendAgencyOtp(Request $request)
    {
        $request->validate([
            'agency_email' => 'required|email',
            'agency_id' => 'required|numeric',
        ]);

        // Extract email and agency ID from request
        $email = $request->agency_email;
        $agency_details = DB::table('agencies')->where('id', $request->agency_id)->first();

        if (!$agency_details) {
            return response()->json(['message' => 'Agency not found'], 404);
        }
        $product_details = DB::table('products')->where('id', $request->product_id)->first();
        $audit_cycle = DB::table('audit_cycles')->where('id', $request->audit_cycle)->first();

        $audit_date = Carbon::now()->toDateString();
        //  echo '<pre>'; print_r($request->product_id); die;
        //   echo '<pre>'; print_r($email); die;
        // Generate OTP
        $otp = rand(100000, 999999);
        // Fetch parameters with status
        $parameters = $this->fetchParametersWithStatus($request->parameters);

        // echo '<pre>'; print_r($parameters); die;
        // Prepare data for PDF
        $pdfData = [
            'agency_name' => 'Sample Agency',
            'audit_date' => Carbon::now()->toDateString(),
            'parameters' => $parameters,
            'agency_details' => $agency_details,
            'product_details' => $product_details,
            'audit_cycle' => $audit_cycle->name,

            'score' => $request->input('overall_score', 0),
        ];

        // Generate PDF
        $pdf = Pdf::loadView('audit.checksheet_pdf', $pdfData);
        $pdfContent = $pdf->output();
        // Update existing OTP record or insert a new one
        DB::table('otp_verifications')
            ->updateOrInsert(
                ['agency_id' => $agency_details->id, 'type' => 'agency', 'email' => $email],
                [
                    'otp' => $otp,
                    'email' => $email,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ]
            );

        // Send email with OTP
        $subject = "Your New OTP for Audit Confirmation";
        Mail::send('audit.agency_otp_mail', ["otp" => $otp, "agency_details" => $agency_details, "audit_date" => $audit_date], function ($message) use ($email, $subject, $pdfContent) {
            $message->from('noreplyall@qdegrees.org', 'Audit Team')
                ->to($email)
                ->subject($subject)
                ->attachData($pdfContent, 'Audit_Checksheet.pdf', [
                    'mime' => 'application/pdf',
                ]);
        });

        return response()->json(['message' => 'New OTP sent successfully']);
    }
    public function resendCollectionManagerOtp(Request $request)
    {
        $request->validate([
            'agency_email' => 'required|email',
            'agency_id' => 'required|numeric',
            'type' => 'required',
        ]);
        // Extract email and agency ID from request
        $managerId = $request->manager_id;
        $managerDetail = DB::table('users')->where('id', $managerId)->first();
        $email = $managerDetail->email;
        if (!$managerDetail) {
            return response()->json(['message' => 'Collection manager not found'], 404);
        }

        $agency_details = DB::table('agencies')->where('id', $request->agency_id)->first();

        if (!$agency_details) {
            return response()->json(['message' => 'Agency not found'], 404);
        }
        $product_details = DB::table('products')->where('id', $request->product_id)->first();
        $audit_cycle = DB::table('audit_cycles')->where('id', $request->audit_cycle)->first();

        $audit_date = Carbon::now()->toDateString();
        //  echo '<pre>'; print_r($request->product_id); die;
        //   echo '<pre>'; print_r($email); die;
        // Generate OTP
        $otp = rand(100000, 999999);
        // Fetch parameters with status
        $parameters = $this->fetchParametersWithStatus($request->parameters);

        // echo '<pre>'; print_r($parameters); die;
        // Prepare data for PDF
        $pdfData = [
            'agency_name' => 'Sample Agency',
            'audit_date' => Carbon::now()->toDateString(),
            'parameters' => $parameters,
            'agency_details' => $agency_details,
            'product_details' => $product_details,
            'audit_cycle' => $audit_cycle->name,

            'score' => $request->input('overall_score', 0),
        ];

        // Generate PDF
        $pdf = Pdf::loadView('audit.checksheet_pdf', $pdfData);
        $pdfContent = $pdf->output();
        // Update existing OTP record or insert a new one
        DB::table('otp_verifications')
            ->updateOrInsert(
                ['agency_id' => $request->agency_id, 'type' => 'collection_manager', 'email' => $email],
                [
                    'otp' => $otp,
                    'email' => $email,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ]
            );

        // Send email with OTP
        $subject = "Your New OTP for Audit Confirmation";

        Mail::send('audit.collection_manager_otp_mail', ["otp" => $otp, "agency_details" => $agency_details, "audit_date" => $audit_date], function ($message) use ($email, $subject, $pdfContent) {
            $message->from('noreplyall@qdegrees.org', 'Audit Team')
                ->to($email)
                ->subject($subject)
                ->attachData($pdfContent, 'Audit_Checksheet.pdf', [
                    'mime' => 'application/pdf',
                ]);
        });

        return response()->json(['message' => 'New OTP sent successfully']);
    }

    public function fetchParametersWithStatus($parametersData)
    {
        $parameters = [];

        foreach ($parametersData as $parameterId => $parameterInfo) {
            // Fetch the parameter by ID
            $parameter = QmSheetParameter::find($parameterId);

            if (!$parameter) {
                continue; // Skip if the parameter is not found
            }

            // Prepare the parameter array
            $parameterArray = [
                'name' => $parameter->parameter, // Name of the parameter
                'subparameters' => [], // Initialize sub-parameters array
            ];

            // Fetch sub-parameters
            foreach ($parameterInfo['subs'] as $subId => $subInfo) {
                $subParameter = QmSheetSubParameter::find($subId);

                if (!$subParameter) {
                    continue; // Skip if sub-parameter is not found
                }

                // Add sub-parameter data to array
                $parameterArray['subparameters'][] = [
                    'name' => $subParameter->sub_parameter, // Name of the sub-parameter
                    'status' => $subInfo['option'], // Status (Satisfactory/Unsatisfactory)
                    'remarks' => $subInfo['remark'],
                ];
            }

            // Add the parameter to the final array
            $parameters[] = $parameterArray;
        }

        // Return the structured parameter data
        return $parameters;
    }
    // public function fetchParametersWithStatus($parametersData) {
    //     $parameters = [];

    //     foreach ($parametersData as $parameterId => $parameterInfo) {
    //         // Fetch the parameter by ID
    //         $parameter = QmSheetParameter::find($parameterId);

    //         if (!$parameter) {
    //             continue; // Skip if the parameter is not found
    //         }

    //         // Prepare the parameter array
    //         $parameterArray = [
    //             'name' => $parameter->parameter, // Name of the parameter
    //             'subparameters' => [] // Initialize sub-parameters array
    //         ];

    //         // Fetch sub-parameters
    //         foreach ($parameterInfo['subs'] as $subId => $subInfo) {
    //             $subParameter = QmSheetSubParameter::find($subId);

    //             if (!$subParameter) {
    //                 continue; // Skip if sub-parameter is not found
    //             }

    //             // Add sub-parameter data to array
    //             $parameterArray['subparameters'][] = [
    //                 'name' => $subParameter->sub_parameter, // Name of the sub-parameter
    //                 'status' => $subInfo['option'] ,// Status (Satisfactory/Unsatisfactory)
    //                 'remarks' => $subInfo['remark']
    //             ];
    //         }

    //         // Add the parameter to the final array
    //         $parameters[] = $parameterArray;
    //     }

    //     // Return the structured parameter data
    //     return $parameters;
    // }

}
