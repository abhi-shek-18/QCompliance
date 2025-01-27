<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Response;
use App\Imports\DacImport;
use App\Model\Branch;
use App\Imports\AllocationImport;
use App\Imports\TrailIntensityImport;
use App\Imports\SetlementImport;
use App\Imports\AdverseImport;
use App\Imports\UsersImport;
use App\Imports\BulkDeactivate;
use App\Exports\InternalDump;
use App\Exports\RawDump;
use App\Exports\QcAndQaChangesExport;
use Maatwebsite\Excel\Facades\Excel;
use App\Uploads\Allocation;
use App\Uploads\Dacs;
use App\Uploads\TrailIntensity;
use App\Uploads\Setlement;
use App\Uploads\AdverseBulk;
use Carbon\Carbon;
use Validator;
use Spatie\Permission\Models\Role;
error_reporting(E_ALL & ~E_NOTICE);
class UploadController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('bulkUpload.upload');
    }
    public function userUpload()
    {
        $roles = Role::all();
        return view('admin.User.upload',compact('roles'));
    }

    public function bulkDeactivate()
    {
        return view('admin.User.deactivate');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
     
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    function checkExcelFile($file_ext){
        $valid=array(
            // 'csv',
            'xls','xlsx' // add your extensions here.
        );        
        return in_array($file_ext,$valid) ? true : false;
    }
    public function store(Request $request)
    {
        ini_set('max_execution_time', 300);
        $validator = Validator::make($request->all(), [
            // 'dac_report' => 'required',
            // 'allocation_report' => 'required',
            // 'trail_intensity' => 'required',
            // 'settlement_mis' => 'required',
            // 'adverse' => 'required',
            'lob' => 'required',
         ]);
         $validator->after(function ($validator) use ($request){
            if($request->hasFile('dac_report') && $this->checkExcelFile($request->file('dac_report')->getClientOriginalExtension()) == false) {
                //return validator with error by file input name
                $validator->errors()->add('file', 'The file must be a file of type: xlsx, xls');
            }
            if($request->hasFile('allocation_report') && $this->checkExcelFile($request->file('allocation_report')->getClientOriginalExtension()) == false) {
                //return validator with error by file input name
                $validator->errors()->add('file', 'The file must be a file of type: xlsx, xls');
            }
            if($request->hasFile('trail_intensity') && $this->checkExcelFile($request->file('trail_intensity')->getClientOriginalExtension()) == false) {
                //return validator with error by file input name
                $validator->errors()->add('file', 'The file must be a file of type: xlsx, xls');
            }
            if($request->hasFile('settlement_mis') && $this->checkExcelFile($request->file('settlement_mis')->getClientOriginalExtension()) == false) {
                //return validator with error by file input name
                $validator->errors()->add('file', 'The file must be a file of type: xlsx, xls');
            }
            if($request->hasFile('adverse') && $this->checkExcelFile($request->file('adverse')->getClientOriginalExtension()) == false) {
                //return validator with error by file input name
                $validator->errors()->add('file', 'The file must be a file of type: xlsx, xls');
            }
        });
        if ($validator->fails()) {

            return redirect()->back()->with('error', [$validator->errors()->all()])->withInput();
        }
        
       if($request->hasFile('dac_report')){
            $path1 = $request->file('dac_report')->store('temp'); 
            $dacpath=storage_path('app').'/'.$path1;
            $dacImport = new DacImport($request->lob);
            Excel::import($dacImport, $dacpath);
        }
        if($request->hasFile('allocation_report')){
        $path2 = $request->file('allocation_report')->store('temp'); 
        $allopath=storage_path('app').'/'.$path2;
        $allocationImport = new AllocationImport($request->lob);
        Excel::import($allocationImport, $allopath);

        }
        if($request->hasFile('trail_intensity')){
        $path3 = $request->file('trail_intensity')->store('temp'); 
        $trailpath=storage_path('app').'/'.$path3;
        $trailIntensityImport = new TrailIntensityImport($request->lob);
        Excel::import($trailIntensityImport, $trailpath);

        }
        if($request->hasFile('settlement_mis')){
        $path4 = $request->file('settlement_mis')->store('temp'); 
        $setpath=storage_path('app').'/'.$path4;
        $setlementImport = new SetlementImport($request->lob);
        Excel::import($setlementImport, $setpath);

        }
        if($request->hasFile('adverse')){
        $path5 = $request->file('adverse')->store('temp'); 
        $adverpath=storage_path('app').'/'.$path5;
        // dd( $adverpath);
        $adverseImport = new AdverseImport($request->lob);
        Excel::import($adverseImport, $adverpath);
        // return redirect('upload/gap-show');
        }
        return redirect('upload/gap-show')->with('success', ['data uploaded successfully.']);
        // dd($request->all(),$request->file('adverse'));
       }
       
       public function downloadUser(){
        $file= public_path(). "/download/user.xlsx";
        $headers = array(
                  'Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                );
        return Response::download($file, 'user.xlsx',$headers);
       }
       public function userImport(Request $request){
            $validator = Validator::make($request->all(), [
                'user_excel' => 'required',
            //    'role' => 'required',
             ]);
             $validator->after(function ($validator) use ($request){
                if($request->hasFile('user_excel') && $this->checkExcelFile($request->file('user_excel')->getClientOriginalExtension()) == false) {
                    //return validator with error by file input name
                    $validator->errors()->add('file', 'The file must be a file of type: xlsx, xls');
                }
            });
            if ($validator->fails()) {
    
                return redirect()->back()->with('error', [$validator->errors()->all()])->withInput();
            }
            if($request->hasFile('user_excel')){
                $path1 = $request->file('user_excel')->store('temp'); 
                $dacpath=storage_path('app').'/'.$path1;
                // dd($request->all());
                // Excel::import(new UsersImport($request->role), $dacpath);
                $exampleImport = new UsersImport($request->role);
                try{
                    Excel::import( $exampleImport, $dacpath);
                }catch ( \Maatwebsite\Excel\Validators\ValidationException $e){
                    $failures = $e->failures();
                    return redirect()->back()->withErrors($failures)->withInput();
                }
            }
            return redirect('user');     
       }

    public function bulk_user_deactivate(Request $request){
        $validator = Validator::make($request->all(), [
            'user_excel' => 'required',
         ]);
         $validator->after(function ($validator) use ($request){
            if($request->hasFile('user_excel') && $this->checkExcelFile($request->file('user_excel')->getClientOriginalExtension()) == false) {
                //return validator with error by file input name
                $validator->errors()->add('file', 'The file must be a file of type: xlsx, xls');
            }
        });
        if ($validator->fails()) {

            return redirect()->back()->with('error', [$validator->errors()->all()])->withInput();
        }
        if($request->hasFile('user_excel')){
            $path1 = $request->file('user_excel')->store('temp'); 
            $dacpath=storage_path('app').'/'.$path1;
            // dd($request->all());
            // Excel::import(new UsersImport($request->role), $dacpath);
            $exampleImport = new BulkDeactivate(["2","3"]);
            try{
                //Excel::import(new BulkDeactivate, $dacpath);
                Excel::import( $exampleImport, $dacpath);
            }catch ( \Maatwebsite\Excel\Validators\ValidationException $e){
                $failures = $e->failures();
                return redirect()->back()->withErrors($failures)->withInput();
            }
        }
        return redirect('user')->with('success', ['User De-activated successfully.']);;     
   }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $this->dac();
        $this->allocation();
        $this->trail();
        $this->settlement();
    }
    public function getBranch($lob){
        ini_set('memory_limit', '1024M');
        $TrailIntensity=TrailIntensity::where('lob',$lob)->get(['lob','branch'])->pluck('branch')->toArray();
        // dd($TrailIntensity);
        $AllocationBranch=Allocation::where('lob',$lob)->get(['lob','branch'])->pluck('branch')->toArray();
        $dacBranch=Dacs::where('lob',$lob)->get(['lob','BranchName'])->pluck('BranchName')->toArray();
        $setBranch=Setlement::where('lob',$lob)->get(['lob','BRANCH'])->pluck('BRANCH')->toArray();
        $addBranch=AdverseBulk::where('lob',$lob)->get(['lob','BRANCH'])->pluck('BRANCH')->toArray();
        $data=array_merge($AllocationBranch,$dacBranch,$TrailIntensity,$setBranch,$addBranch);
        $branchs=array_unique($data);
        return $branchs;
    }
    public function getAgencies($branch_name){
            $TrailIntensity=TrailIntensity::where('branch',$branch_name)->get()->pluck('agency_name')->toArray();
            $AllocationBranch=Allocation::where('branch',$branch_name)->get()->pluck('agency_name')->toArray();
            $dacBranch=Dacs::where('BranchName',$branch_name)->get()->pluck('AgencyName')->toArray();
            $setBranch=Setlement::where('BRANCH',$branch_name)->get()->pluck('Agency_Name')->toArray();
            $addBranch=AdverseBulk::where('BRANCH',$branch_name)->get()->pluck('month_Agency_Name')->toArray();
            $data=array_merge($AllocationBranch,$dacBranch,$TrailIntensity,$setBranch,$addBranch);
            $branchs=array_unique($data);
            return response()->json(['status'=>true,'data'=>(array)$branchs]);
    }
    public function gapShow(Request $request){
        ini_set('memory_limit', '1024M');
        $lob=$request->lob;
        $data=explode('-',$request->date);
        $to=Carbon::parse($data[0])->format('d-m-Y');
        $from=Carbon::parse($data[1])->format('d-m-Y');
        $dac=$this->dac($request->branch,$lob,$request->agency,$to,$from);
        $allocation=$this->allocation($request->branch,$lob,$request->agency,$to,$from);
        $trail=$this->trail($request->branch,$lob,$request->agency,$to,$from);
        $settlement=$this->settlement($request->branch,$lob,$request->agency,$to,$from);
        $adverseBulk=$this->adverseBulk($request->branch,$lob,$request->agency,$to,$from);
        $branchs=[];
        // $branchs=$this->getBranch($lob);
        $branchId=$request->branch;
        $agencyId=$request->agency;
        $bucket=$request->bucket;
        $date=$request->date;
        return view('bulkUpload.gap',compact('branchId','bucket','date','agencyId','branchs','trail','allocation','dac','settlement','adverseBulk','lob'));
    }
    public function gapView(){
          // $branchs=$this->getBranch();
          $branchs=[];
        $branchId='';
        $lob='';
        return view('bulkUpload.gap',compact('branchs','branchId','lob'));
    }
    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
    public function trail($branch,$lob,$agency,$to,$from){
        $allocation=TrailIntensity::where(['branch'=>$branch,'lob'=>$lob,'agency_name'=>$agency])->get();
        $total=$allocation->count();
        $allocationGap=[];
        $con=['Attempt > 5','Attempt 1','Attempt 2','Attempt 3','Attempt 4','Attempt 5'];
        foreach($allocation as $data){
            // $allocationGap[$data->id]=[];
            // $allocationGap[$data->id]['GAP']=[];
            // $allocationGap[$data->id]['intensity']=[];
            if($data->remarks=='Trail GAP'){
                if(!isset($allocationGap[$data->id])){
                    $allocationGap[$data->id]=$data;    
                }
                $allocationGap[$data->id]['GAP']=true;
            }
            if(in_array($data->attempts,$con)){
                if(!isset($allocationGap[$data->id])){
                    $allocationGap[$data->id]=$data;    
                }
                $allocationGap[$data->id]['intensity']=false;
            }
        }
        return ['data'=>$allocationGap,'total'=>$total];
    }
    public function allocation($branch,$lob,$agency,$to,$from){
        $allocation=Allocation::where(['branch'=>$branch,'lob'=>$lob,'agency_name'=>$agency])
        ->WhereBetween('agent_allocation_date_stamp', [$to,$from])
        ->WhereBetween('date_stamp', [$to,$from])->get();
        $total=$allocation->count();
        $allocationGap=[];
        foreach($allocation as $data){
            // diffInDays
            if($data->agent_allocation_status=='GAP'){
                // dd($data);
                if($data->date_stamp!=null){
                    $date_stamp = Carbon::parse($data->date_stamp);
                }
                else{
                    $date_stamp=null;
                }
                if($data->agent_allocation_date_stamp!=null){
                    $agent_allocation_date_stamp =Carbon::parse($data->agent_allocation_date_stamp);
                }
                else{
                    $agent_allocation_date_stamp=null;
                }
                if($date_stamp!=null && $agent_allocation_date_stamp!=null){
                    $allocationGap[$data->id]=$data;
                    $allocationGap[$data->id]['gap1']=$agent_allocation_date_stamp->diffInDaysFiltered(function(Carbon $date) {
                        $mdate=$date->format('Y-m-d');
                        $secondSat=$date->nthOfMonth(2, Carbon::SATURDAY);
                        $fourSat=$date->nthOfMonth(4, Carbon::SATURDAY);
                        if($date->isWeekend()){
                           return  true;
                        }
                        if($date->nthOfMonth(2, Carbon::SATURDAY)->format('Y-m-d') == $mdate){
                           return  true;
                        }
                        if($date->nthOfMonth(4, Carbon::SATURDAY)->format('Y-m-d') == $mdate){
                           return  true;
                        }
                        return false;
                     },$date_stamp);
                }
                else{
                    $allocationGap[$data->id]=$data;
                }
            }
        }
        return ['data'=>$allocationGap,'total'=>$total];
    }
    public function dac($branch,$lob,$agency,$to,$from){
        Carbon::setWeekendDays([
            Carbon::SUNDAY,
            // Carbon::MONDAY,
        ]);
        $allocation=Dacs::where(['BranchName'=>$branch,'lob'=>$lob,'AgencyName'=>$agency])
        ->whereBetween('DepositDate', [$to,$from])
        ->WhereBetween('ReceiptDate', [$to,$from])
        ->WhereBetween('BBPayBatchAckDate', [$to,$from])
        ->get();
        // dd($allocation[0]);
        $total=$allocation->count();
        $allocationGap=[];
        foreach($allocation as $data){
            // diffInDays
            $DepositDate = Carbon::parse($data->DepositDate);
            $ReceiptDate =Carbon::parse($data->ReceiptDate);
            $BBPayBatchAckDate = Carbon::parse($data->BBPayBatchAckDate);
            $allocationGap[$data->id]=$data;
            // $allocationGap[$data->id]['gap1']=$DepositDate->diffInDays($ReceiptDate);
            // $allocationGap[$data->id]['gap2']=$BBPayBatchAckDate->diffInDays($ReceiptDate);
            $allocationGap[$data->id]['gap1']=$DepositDate->diffInDaysFiltered(function(Carbon $date) {
                $mdate=$date->format('Y-m-d');
                $secondSat=$date->nthOfMonth(2, Carbon::SATURDAY);
                $fourSat=$date->nthOfMonth(4, Carbon::SATURDAY);
                if($date->isWeekend()){
                   return  true;
                }
                if($date->nthOfMonth(2, Carbon::SATURDAY)->format('Y-m-d') == $mdate){
                   return  true;
                }
                if($date->nthOfMonth(4, Carbon::SATURDAY)->format('Y-m-d') == $mdate){
                   return  true;
                }
                return false;
                // return ($date->isWeekend() || $date->diffInDays($secondSat)==0 || $data->diffInDays($fourSat) == 0);
             },$ReceiptDate);

            $allocationGap[$data->id]['gap2']=$BBPayBatchAckDate->diffInDaysFiltered(function(Carbon $date) {
                $mdate=$date->format('Y-m-d');
                $secondSat=$date->nthOfMonth(2, Carbon::SATURDAY);
                $fourSat=$date->nthOfMonth(4, Carbon::SATURDAY);
                if($date->isWeekend()){
                   return  true;
                }
                if($date->nthOfMonth(2, Carbon::SATURDAY)->format('Y-m-d') == $mdate){
                   return  true;
                }
                if($date->nthOfMonth(4, Carbon::SATURDAY)->format('Y-m-d') == $mdate){
                   return  true;
                }
                return false;
             },$ReceiptDate);
        }
        // dd($allocationGap);
        return ['data'=>$allocationGap,'total'=>$total];
    }
    public function settlement($branch,$lob,$agency,$to,$from){
        $allocation=Setlement::where(['BRANCH'=>$branch,'lob'=>$lob,'Agency_Name'=>$agency])
        ->WhereBetween('SETTLEMENTEND_DATE', [$to,$from])
        ->WhereBetween('Received_Date', [$to,$from])
        ->get();
        $total=$allocation->count();
        $allocationGap=[];
        foreach($allocation as $data){
            // diffInDays
            $SETTLEMENTEND_DATE = Carbon::parse($data->SETTLEMENTEND_DATE);
            $Received_Date =Carbon::parse($data->Received_Date);
            if($SETTLEMENTEND_DATE->diffInDaysFiltered(function(Carbon $date) {
                $mdate=$date->format('Y-m-d');
                $secondSat=$date->nthOfMonth(2, Carbon::SATURDAY);
                $fourSat=$date->nthOfMonth(4, Carbon::SATURDAY);
                if($date->isWeekend()){
                   return  true;
                }
                if($date->nthOfMonth(2, Carbon::SATURDAY)->format('Y-m-d') == $mdate){
                   return  true;
                }
                if($date->nthOfMonth(4, Carbon::SATURDAY)->format('Y-m-d') == $mdate){
                   return  true;
                }
                return false;
             },$Received_Date)>7){
                $allocationGap[$data->id]=$data;
                // $allocationGap[$data->id]['gap1']=$DepositDate->diffInDays($ReceiptDate);
                // dd($allocationGap);
            }
        }
        return ['data'=>$allocationGap,'total'=>$total];
    }
    public function adverseBulk($branch,$lob,$agency,$to,$from){
        $allocation=AdverseBulk::where(['BRANCH'=>$branch,'lob'=>$lob,'month_Agency_Name'=>$agency])->get();
        $total=$allocation->count();
        $allocationGap=[];
        foreach($allocation as $data){
            $arrayPos=[];
            $arrayAgency=[];
            $arrayPos[]=$data->prev_month2_BOM_POS;
            $arrayPos[]=$data->prev_month1_BOM_POS;
            $arrayPos[]=$data->month_BOM_POS;
            $arrayAgency[]=$data->prev_month2_Agency_Name;
            $arrayAgency[]=$data->prev_month1_Agency_Name;
            $arrayAgency[]=$data->month_Agency_Name;
            if((count(array_unique($arrayPos))==1) && (count(array_unique($arrayAgency))==1) ){
                $allocationGap[$data->id]=$data;
            }
        }
        return ['data'=>$allocationGap,'total'=>$total];
    }
    /* public function rawDumpAudit(){
        
        ini_set('memory_limit', '-1');
        ini_set('max_execution_time', 600);
        return Excel::download(new RawDump, 'dump.xlsx');
    }*/

    public function rawDumpAudit(Request $request){

        

        ini_set('memory_limit', '-1');

        ini_set('max_execution_time', 600);
        $filter_data = $request->all();
        return Excel::download(new RawDump($filter_data), 'dump.xlsx');

    }
    public function rawDumpTest(){
        
        ini_set('memory_limit', '-1');
        ini_set('max_execution_time', 600);
        return Excel::download(new QcAndQaChangesExport, 'dump.xlsx');
    }
      //added by kratika jain
     public function reportindex(){
        $branch=Branch::get(['id', 'name']);
        return view('bulkUpload.reports',compact('branch'));

    }
    public function internalRawDump(Request $request){
    //dd($request->all());
      ini_set('memory_limit', '-1');

        ini_set('max_execution_time', 600);
        $dumpdata = $request->all();
        return Excel::download(new InternalDump($dumpdata), 'dumpData.xls');
   }
}
