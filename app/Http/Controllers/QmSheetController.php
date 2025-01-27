<?php

namespace App\Http\Controllers;

use App\AuditAlertBox;
use App\Models\QmSheet;
use App\Models\QmSheetParameter;
use App\Models\QmSheetSubParameter;
// use App\ReasonType;
use Auth;
use Crypt;
use Illuminate\Http\Request;
use Validator;

class QmSheetController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data = QmSheet::with('parameter')->get();
        return view('qm_sheet.list',compact('data'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('qm_sheet.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            // 'company_id' => 'required',
            // 'client_id' => 'required',
            // 'process_id' => 'required',
            'name' => 'required',
            // 'code' => 'required',
            // 'version' => 'required|integer',
            'type' => 'required',
            'lob' => 'required',
        ]);

        if($validator->fails())
        {
            return redirect('qm_sheet/create')
                        ->withErrors($validator)
                        ->withInput();
        }else
        {
            $new_rc = new QmSheet;
            $new_rc->fill($request->all());
            $new_rc->save();
            return redirect('qm_sheet/'.Crypt::encrypt($new_rc->id).'/add_parameter')->with('success', 'QM Sheet created successfully, now please add all parameters and sub-parameters.'); 
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $data = QmSheet::find(Crypt::decrypt($id));
        // $all_client = Client::where('company_id',Auth::user()->company_id)->pluck('name','id');
        // $all_process = Process::where('company_id',Auth::user()->company_id)->pluck('name','id');
        // return view('qm_sheet.edit',compact('data','all_client','all_process'));
        return view('qm_sheet.edit',compact('data'));
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
        $validator = Validator::make($request->all(), [
            // 'company_id' => 'required',
            // 'client_id' => 'required',
            // 'process_id' => 'required',
            'name' => 'required',
            'type' => 'required',
            'lob' => 'required',
            // 'code' => 'required',
            // 'version' => 'required|integer',
        ]);

        if($validator->fails())
        {
            return redirect('qm_sheet/create')
                        ->withErrors($validator)
                        ->withInput();
        }else
        {
            $new_rc =  QmSheet::find(Crypt::decrypt($id));
            $new_rc->fill($request->all());
            $new_rc->save();
            return redirect('qm_sheet')->with('success','QM - Sheet updated successfully.');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $new_rc =  QmSheet::where('id',Crypt::decrypt($id))->delete();
        if($new_rc)
            return redirect('qm_sheet')->with('success','QM - Sheet deleted successfully.');
        else
        return redirect('qm_sheet')->with('error','QM - Sheet not deleted.');
    }

    public function add_parameter($sheet_id)
    {
        $qm_sheet_data = QmSheet::find(Crypt::decrypt($sheet_id));
        $all_alert_box_list = AuditAlertBox::all()->pluck('name','id');
        // $all_reason_types = ReasonType::where('company_id',Auth::user()->company_id)->where('process_id',$qm_sheet_data->process_id)->pluck('label','id');
        // $all_alert_box_list = [];
        $all_reason_types = [];
        return view('qm_sheet.add_parameter',compact('qm_sheet_data','all_alert_box_list','all_reason_types'));
    }

    public function list_parameter($sheet_id)
    {
        $qm_sheet_data = QmSheet::find(Crypt::decrypt($sheet_id));
        $data = QmSheetParameter::where('qm_sheet_id',$qm_sheet_data->id)->with('qm_sheet_sub_parameter')->get();
        
        // dd($data);
        return view('qm_sheet.list_parameter',compact('data','qm_sheet_data'));
    }

    public function store_parameters(Request $request)
    {
        // dd($request->subs);
        // return "a";
        
        $validator = Validator::make($request->all(), [
            // 'company_id' => 'required',
            'qm_sheet_id' => 'required',
            'parameter' => 'required'
        ]);

        if($validator->fails())
        {
            return redirect('qm_sheet/'.Crypt::encrypt($request->qm_sheet_id).'/add_parameter')
                        ->withErrors($validator)
                        ->withInput();
        }else
        {
            // dd($request->all());
            $new_rc = new QmSheetParameter;
            $new_rc->qm_sheet_id = $request->qm_sheet_id;
            $new_rc->parameter = $request->parameter;
            $new_rc->is_non_scoring = (isset($request->non_scoring))?1:0;
            $new_rc->save();

            if($new_rc->id)
            {
                foreach ($request->subs as $key => $value) {
                    if($value['sub_parameter']){

                    $new_sub_rc = new QmSheetSubParameter;
                    $new_sub_rc->qm_sheet_id = $request->qm_sheet_id;
                    $new_sub_rc->qm_sheet_parameter_id = $new_rc->id;

                    $new_sub_rc->sub_parameter = $value['sub_parameter'];
                    $new_sub_rc->weight = $value['weight'];
                    $new_sub_rc->details = $value['details'];

                    if(isset($request->non_scoring))
                    {
                        $new_sub_rc->non_scoring_option_group = $value['non_scoring_option_group'];
                    }else
                    {

                    $new_sub_rc->pass = (isset($value['s_pass']))?1:0;
                    $new_sub_rc->fail = (isset($value['s_fail']))?1:0;
                    $new_sub_rc->critical = (isset($value['s_critical']))?1:0;
                    $new_sub_rc->na = (isset($value['s_na']))?1:0;
                    $new_sub_rc->pwd = (isset($value['s_pwd']))?1:0;
                    $new_sub_rc->per = (isset($value['s_per']))?1:0;

                    // $new_sub_rc->pass_alert_box_id = $value['s_pass_alert_box_id'];
                    // $new_sub_rc->fail_alert_box_id = $value['s_fail_alert_box_id'];
                    // $new_sub_rc->critical_alert_box_id = $value['s_critical_alert_box_id'];
                    // $new_sub_rc->na_alert_box_id = $value['s_na_alert_box_id'];
                    // $new_sub_rc->pwd_alert_box_id = $value['s_pwd_alert_box_id'];

                    if(isset($value['s_fail_reason_type_box_id']))
                        $new_sub_rc->fail_reason_types = implode(",", $value['s_fail_reason_type_box_id']);

                    if(isset($value['s_critical_reason_type_box_id']))
                        $new_sub_rc->critical_reason_types = implode(",", $value['s_critical_reason_type_box_id']);

                    }

                    $new_sub_rc->save();
                }}
            }
            return redirect('qm_sheet/'.Crypt::encrypt($request->qm_sheet_id).'/add_parameter')->with('success','QM - Sheet parameter created successfully.');
        }
    }
    public function delete_parameter($id)
    {
        $temp = QmSheetParameter::with('qm_sheet_sub_parameter')->find(Crypt::decrypt($id));
        QmSheetSubParameter::where('qm_sheet_parameter_id',$temp->id)->delete();
        $sheet_id = $temp->qm_sheet_id;
        $temp->delete();
        return redirect('qm_sheet/'.Crypt::encrypt($sheet_id).'/list_parameter')->with('success','QM - Sheet parameter deleted successfully.');

    }
    public function edit_parameter($parameter_id)
    {
        $param_data = QmSheetParameter::find(Crypt::decrypt($parameter_id));

        $qm_sheet_data = QmSheet::find($param_data->qm_sheet_id);
        $all_alert_box_list = AuditAlertBox::all()->pluck('name','id');
        // $all_reason_types = ReasonType::where('company_id',Auth::user()->company_id)->pluck('label','id');
        $all_reason_types = [];

        return view('qm_sheet.edit_parameter',compact('qm_sheet_data','all_alert_box_list','all_reason_types','param_data'));
    }
    public function update_parameter(Request $request)
    {
         // dd($request->subs);
        $validator = Validator::make($request->all(), [
            // 'company_id' => 'required',
            'qm_sheet_id' => 'required',
            'parameter' => 'required'
        ]);

        if($validator->fails())
        {
            return redirect('parameter/'.Crypt::encrypt($request->parameter_id).'/edit')
                        ->withErrors($validator)
                        ->withInput();
        }else
        {
            $new_rc = QmSheetParameter::find($request->parameter_id);
            $new_rc->qm_sheet_id = $request->qm_sheet_id;
            $new_rc->parameter = $request->parameter;
            $new_rc->is_non_scoring = (isset($request->non_scoring))?1:0;
            $new_rc->save();

            if($new_rc->id)
            {
                     foreach ($request->subs as $key => $value) {
                        if($value['sub_parameter']){

                        if($value['sp_pm_id'])
                            $new_sub_rc = QmSheetSubParameter::find($value['sp_pm_id']);
                        else
                            $new_sub_rc = new QmSheetSubParameter;
                            

                    $new_sub_rc->qm_sheet_id = $request->qm_sheet_id;
                    $new_sub_rc->qm_sheet_parameter_id = $new_rc->id;

                    $new_sub_rc->sub_parameter = $value['sub_parameter'];
                    $new_sub_rc->weight = $value['weight'];
                    $new_sub_rc->details = $value['details'];

                    if(isset($request->non_scoring))
                    {
                        $new_sub_rc->non_scoring_option_group = $value['non_scoring_option_group'];

                        $new_sub_rc->pass = 0;
                        $new_sub_rc->fail = 0;
                        $new_sub_rc->critical = 0;
                        $new_sub_rc->na = 0;
                        $new_sub_rc->pwd = 0;
                        $new_sub_rc->per = 0;
                        $new_sub_rc->pass_alert_box_id = null;
                        $new_sub_rc->fail_alert_box_id = null;
                        $new_sub_rc->critical_alert_box_id = null;
                        $new_sub_rc->na_alert_box_id = null;
                        $new_sub_rc->pwd_alert_box_id = null;
                        
                        // $new_sub_rc->fail_reason_types = null;
                        
                        // $new_sub_rc->critical_reason_types = null;

                    }else
                    {

                    $new_sub_rc->pass = (isset($value['s_pass']))?1:0;
                    $new_sub_rc->fail = (isset($value['s_fail']))?1:0;
                    $new_sub_rc->critical = (isset($value['s_critical']))?1:0;
                    $new_sub_rc->na = (isset($value['s_na']))?1:0;
                    $new_sub_rc->pwd = (isset($value['s_pwd']))?1:0;
                    $new_sub_rc->per = (isset($value['s_per']))?1:0;
                    
                    // $new_sub_rc->pass_alert_box_id = $value['s_pass_alert_box_id'];
                    // $new_sub_rc->fail_alert_box_id = $value['s_fail_alert_box_id'];
                    // $new_sub_rc->critical_alert_box_id = $value['s_critical_alert_box_id'];
                    // $new_sub_rc->na_alert_box_id = $value['s_na_alert_box_id'];
                    // $new_sub_rc->pwd_alert_box_id = $value['s_pwd_alert_box_id'];

                    // if(isset($value['s_fail_reason_type_box_id']))
                    //     $new_sub_rc->fail_reason_types = implode(",", $value['s_fail_reason_type_box_id']);
                    // else
                    //     $new_sub_rc->fail_reason_types = null;

                    // if(isset($value['s_critical_reason_type_box_id']))
                    //     $new_sub_rc->critical_reason_types = implode(",", $value['s_critical_reason_type_box_id']);
                    // else
                    //     $new_sub_rc->critical_reason_types = null;

                    }
                    
                    $new_sub_rc->save();

                    }
                }
            } 
            return redirect('qm_sheet/'.Crypt::encrypt($request->qm_sheet_id).'/list_parameter')->with('success', 'Parameter and sub parameter updated successfully.'); 
        }
    }
    public function delete_sub_parameter($id)
    {
        QmSheetSubParameter::find($id)->delete();
        return response()->json(['status'=>200,'message'=>"Success, sub parameter deleted successfully."], 200);
    }
    public function get_client_process_based_qm_sheet(Request $request)
    {
        $all_sheet = QmSheet::where('client_id',$request->client_id)->where('process_id',$request->process_id)->orderBy('id','desc')->get();
        return response()->json(['status'=>200,'message'=>"Success",'data'=>$all_sheet], 200);
    }
}
