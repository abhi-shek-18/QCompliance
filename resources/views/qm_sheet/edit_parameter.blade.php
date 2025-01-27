@extends('layouts.master')



@section('sh-title')

{{$qm_sheet_data->name}}

@endsection



@section('sh-detail')

Update Parameter

@endsection



@section('sh-toolbar')

							<div class="kt-subheader__toolbar">

								<div class="kt-subheader__wrapper">



								<a href="/qm_sheet/{{Crypt::encrypt($qm_sheet_data->id)}}/list_parameter" class="btn btn-label-success btn-bold">

									List All Parameter

								</a>

								

								</div>

							</div> 

@endsection



@section('content')



<div class="row">

	<div class="col-md-12">

		<div class="text-right" style="margin-bottom:10px;">

			<div class="kt-subheader__wrapper">

				<a href="{{url('/qm_sheet/'.Crypt::encrypt($qm_sheet_data->id).'/list_parameter')}}" class="btn btn-success btn-bold">

					List All Parameter

				</a>

			</div>

		</div>

	  <!--begin::Portlet-->

	  <div class="card">

		  <div class="card-header">

			  <strong>Edit Parameter</strong>

		  </div>

		  <div class="card-body card-block">

  



      <!--begin::Form-->

      

        {!! Form::open(

                  array(

                    'route' => 'update_parameter', 

                    'class' => 'kt-form',

                    'role'=>'form',

                    'data-toggle'=>"validator")

                  ) !!}

        {{-- <input type="hidden" name="company_id" value="{{Auth::user()->company_id}}"> --}}

        <input type="hidden" name="qm_sheet_id" value="{{$qm_sheet_data->id}}">

        <input type="hidden" name="parameter_id" value="{{$param_data->id}}">

        <div class="kt-portlet__body">



          <div class="form-group">

            <label>Parameter*</label>

            <input type="text" name="parameter" class="form-control col-lg-6" required value="{{$param_data->parameter}}">

          </div>

          {{-- <div class="form-group">

            <div class="kt-form__group--inline">

				<div class="kt-form__control">

					<label>Is non scoring ?</label>

					<input type="checkbox" name="non_scoring" value="1" {{($param_data->is_non_scoring)?'checked':''}}> 

				</div>

			</div>

          </div> --}}



          <div class="kt-separator kt-separator--border-dashed kt-separator--space-lg"></div>

          <h6>Add Sub - Parameters</h6>

          <br/>

          <div id="kt_repeater_qm_sheet">

												<div class="form-group  row" id="kt_repeater_qm_sheet">

													<div data-repeater-list="subs" class="col-lg-12">



														@foreach($param_data->qm_sheet_sub_parameter as $kksp=>$vvsp)

														<div data-repeater-item class="form-group align-items-center">

															<input type="hidden" name="sp_pm_id" value="{{$vvsp->id}}">

															<div class="row">

															<div class="col-md-4">

																<div class="kt-form__group--inline">

																	<div class="kt-form__control">

																		<input type="text" class="form-control" placeholder="Sub - Parameter name" name="sub_parameter" value="{{$vvsp->sub_parameter}}">

																	</div>

																</div>

																<div class="d-md-none kt-margin-b-10"></div>

															</div>



															<div class="col-md-2">

																<div class="kt-form__group--inline">

																	<div class="kt-form__control">

																		<input type="number" class="form-control" placeholder="Weightage" name="weight" value="{{$vvsp->weight}}">

																	</div>

																</div>

																<div class="d-md-none kt-margin-b-10"></div>

															</div>



															<div class="col-md-4">

																<div class="kt-form__group--inline">

																	<div class="kt-form__control">

																		<textarea class="form-control" placeholder="Sub - Parameter details" name="details">{{$vvsp->details}}</textarea>

																	</div>

																</div>

																<div class="d-md-none kt-margin-b-10"></div>

															</div>

															

															<div class="col-md-2">

																<div data-repeater-delete="" class="btn-sm btn btn-danger btn-pill" @click="onQmSheetSubParameterDelete({{$vvsp->id}})">

																	<span>

																		<i class="la la-trash-o"></i>

																		<span>Delete</span>

																	</span>

																</div>

															</div>

															</div>



															<br/>

															{{-- <span class="form-text text-muted">This will only applicable when "Is non scoring" is un-checked!</span><br/> --}}

															<div class="row">

																<div class="col-md-2">

																	<label class="kt-checkbox kt-checkbox--state-success">

																		<input type="checkbox" name="s_pass" value="1" {{($vvsp->pass)?'checked':''}}> Satisfactory

																		<span></span>

																	</label>

																</div>

																{{-- <div class="col-md-3">

																	{{ Form::select('s_pass_alert_box_id',$all_alert_box_list,$vvsp->pass_alert_box_id,['class'=>'form-control','placeholder'=>"Select alert box if any!"]) }}

																</div> --}}

															{{-- </div> --}}



															{{-- <br/> --}}

															{{-- <div class="row"> --}}

																<div class="col-md-2">

																	<label class="kt-checkbox kt-checkbox--state-success">

																		<input type="checkbox" name="s_fail" value="1" {{($vvsp->fail)?'checked':''}}> Unsatisfactory

																		<span></span>

																	</label>

																</div>

																{{-- <div class="col-md-3">

																	{{ Form::select('s_fail_alert_box_id',$all_alert_box_list,$vvsp->fail_alert_box_id,['class'=>'form-control','placeholder'=>"Select alert box if any!"]) }}

																</div>

																<div class="col-md-4">

																	{{ Form::select('s_fail_reason_type_box_id',$all_reason_types,explode(',',$vvsp->fail_reason_types),['class'=>'form-control','multiple'=>'multiple','style'=>"height:80px;"]) }}

																</div> --}}

															{{-- </div> --}}



															{{-- <br/> --}}

															{{-- <div class="row"> --}}

																<div class="col-md-2">

																	<label class="kt-checkbox kt-checkbox--state-success">

																		<input type="checkbox" name="s_critical" value="1" {{($vvsp->critical)?'checked':''}}> Critical

																		<span></span>

																	</label>

																</div>

																{{-- <div class="col-md-3">

																	{{ Form::select('s_critical_alert_box_id',$all_alert_box_list,$vvsp->critical_alert_box_id,['class'=>'form-control','placeholder'=>"Select alert box if any!"]) }}

																</div> --}}

																{{-- <div class="col-md-4">

																	{{ Form::select('s_critical_reason_type_box_id',$all_reason_types,explode(',',$vvsp->critical_reason_types),['class'=>'form-control','multiple'=>'multiple','style'=>"height:80px;"]) }}

																</div> --}}

															{{-- </div> --}}



															{{-- <br/> --}}

															{{-- <div class="row"> --}}

																<div class="col-md-2">

																	<label class="kt-checkbox kt-checkbox--state-success">

																		<input type="checkbox" name="s_na" value="1" {{($vvsp->na)?'checked':''}}> N/A

																		<span></span>

																	</label>

																</div>

																{{-- <div class="col-md-3">

																	{{ Form::select('s_na_alert_box_id',$all_alert_box_list,$vvsp->na_alert_box_id,['class'=>'form-control','placeholder'=>"Select alert box if any!"]) }}

																</div> --}}

																

															{{-- </div> --}}



															{{-- <br/> --}}

															{{-- <div class="row"> --}}

																<div class="col-md-2">

																	<label class="kt-checkbox kt-checkbox--state-success">

																		<input type="checkbox" name="s_pwd" value="1" {{($vvsp->pwd)?'checked':''}}> PWD

																		<span></span>

																	</label>

																</div>

																<div class="col-md-2">

																	<label class="kt-checkbox kt-checkbox--state-success">

																		<input type="checkbox" name="s_per" value="1" {{($vvsp->per)?'checked':''}}> Percentage

																		<span></span>

																	</label>

																</div>

																{{-- <div class="col-md-3">

																	{{ Form::select('s_pwd_alert_box_id',$all_alert_box_list,$vvsp->pwd_alert_box_id,['class'=>'form-control','placeholder'=>"Select alert box if any!"]) }}

																</div> --}}

															</div>



															{{-- <div class="kt-separator kt-separator--border-dashed kt-separator--space-lg"></div>



															<h6>Non Scoring Observation Option Group</h6>



															<div class="form-group col-md-5">

															<label>Please select a group*</label>

															<select class="form-control" name="non_scoring_option_group">

												            	<option value=""> select an option</option>

												            	<option value="1" {{($vvsp->non_scoring_option_group=="1")?'selected':''}}> Yes / No</option>

												            	<option value="2" {{($vvsp->non_scoring_option_group=="2")?'selected':''}}> 3 Pointer (Promoter, Distractor, Passive)</option>

												            	<option value="3" {{($vvsp->non_scoring_option_group=="3")?'selected':''}}> 4 Pointer (Good, Bad, Excellent, Poor)</option>

												            	<option value="4" {{($vvsp->non_scoring_option_group=="4")?'selected':''}}> 5 Pointer (Excellent, Good, Average, Bad, Poor)</option>

												            </select>

												            {{-- {{ Form::select('non_scoring_option_group',list_non_scoring_obs_options(),$vvsp->non_scoring_option_group,['class'=>'form-control','placeholder'=>"Select an option!"]) }} 

												            <span class="form-text text-muted">This will only applicable when "Is non scoring" is checked!</span>

												          </div> --}}



												          <div class="kt-separator kt-separator--border-dashed kt-separator--space-lg"></div>

														</div>

														@endforeach



														<div data-repeater-item class="form-group align-items-center">

															<input type="hidden" name="sp_pm_id">

															<div class="row">

															<div class="col-md-4">

																<div class="kt-form__group--inline">

																	<div class="kt-form__control">

																		<input type="text" class="form-control" placeholder="Sub - Parameter name" name="sub_parameter" >

																	</div>

																</div>

																<div class="d-md-none kt-margin-b-10"></div>

															</div>



															<div class="col-md-2">

																<div class="kt-form__group--inline">

																	<div class="kt-form__control">

																		<input type="number" class="form-control" placeholder="Weightage" name="weight" >

																	</div>

																</div>

																<div class="d-md-none kt-margin-b-10"></div>

															</div>



															<div class="col-md-4">

																<div class="kt-form__group--inline">

																	<div class="kt-form__control">

																		<textarea class="form-control" placeholder="Sub - Parameter details" name="details"></textarea>

																	</div>

																</div>

																<div class="d-md-none kt-margin-b-10"></div>

															</div>

															

															<div class="col-md-2">

																<div data-repeater-delete="" class="btn-sm btn btn-danger btn-pill">

																	<span>

																		<i class="la la-trash-o"></i>

																		<span>Delete</span>

																	</span>

																</div>

															</div>

															</div>



															<br/>

															{{-- <span class="form-text text-muted">This will only applicable when "Is non scoring" is un-checked!</span><br/> --}}

															<div class="row">

																<div class="col-md-2">

																	<label class="kt-checkbox kt-checkbox--state-success">

																		<input type="checkbox" name="s_pass" value="1"> Pass

																		<span></span>

																	</label>

																</div>

																{{-- <div class="col-md-3">

																	{{ Form::select('s_pass_alert_box_id',$all_alert_box_list,null,['class'=>'form-control','placeholder'=>"Select alert box if any!"]) }}

																</div>

															</div> --}}



															{{-- <br/> --}}

															{{-- <div class="row"> --}}

																<div class="col-md-2">

																	<label class="kt-checkbox kt-checkbox--state-success">

																		<input type="checkbox" name="s_fail" value="1"> Fail

																		<span></span>

																	</label>

																</div>

																{{-- <div class="col-md-3">

																	{{ Form::select('s_fail_alert_box_id',$all_alert_box_list,null,['class'=>'form-control','placeholder'=>"Select alert box if any!"]) }}

																</div>

																<div class="col-md-4">

																	{{ Form::select('s_fail_reason_type_box_id',$all_reason_types,null,['class'=>'form-control','multiple'=>'multiple','style'=>"height:80px;"]) }}

																</div>

															</div>



															<br/> --}}

															{{-- <div class="row"> --}}

																<div class="col-md-2">

																	<label class="kt-checkbox kt-checkbox--state-success">

																		<input type="checkbox" name="s_critical" value="1"> Critical

																		<span></span>

																	</label>

																</div>

																{{-- <div class="col-md-3">

																	{{ Form::select('s_critical_alert_box_id',$all_alert_box_list,null,['class'=>'form-control','placeholder'=>"Select alert box if any!"]) }}

																</div>

																<div class="col-md-4">

																	{{ Form::select('s_critical_reason_type_box_id',$all_reason_types,null,['class'=>'form-control','multiple'=>'multiple','style'=>"height:80px;"]) }}

																</div> --}}

															{{-- </div> --}}



															{{-- <br/> --}}

															{{-- <div class="row"> --}}

																<div class="col-md-2">

																	<label class="kt-checkbox kt-checkbox--state-success">

																		<input type="checkbox" name="s_na" value="1"> N/A

																		<span></span>

																	</label>

																</div>

																{{-- <div class="col-md-3">

																	{{ Form::select('s_na_alert_box_id',$all_alert_box_list,null,['class'=>'form-control','placeholder'=>"Select alert box if any!"]) }}

																</div> --}}

																

															{{-- </div> --}}



															{{-- <br/> --}}

															{{-- <div class="row"> --}}

																<div class="col-md-2">

																	<label class="kt-checkbox kt-checkbox--state-success">

																		<input type="checkbox" name="s_pwd" value="1"> PWD

																		<span></span>

																	</label>

																</div>

																<div class="col-md-2">

																	<label class="kt-checkbox kt-checkbox--state-success">

																		<input type="checkbox" name="s_per" value="1"> Percentage

																		<span></span>

																	</label>

																</div>

																{{-- <div class="col-md-3">

																	{{ Form::select('s_pwd_alert_box_id',$all_alert_box_list,null,['class'=>'form-control','placeholder'=>"Select alert box if any!"]) }}

																</div> --}}

															</div>



															{{-- <div class="kt-separator kt-separator--border-dashed kt-separator--space-lg"></div>



															<h6>Non Scoring Observation Option Group</h6>



															<div class="form-group col-md-5">

															<label>Please select a group*</label>

															<select class="form-control" name="non_scoring_option_group">

												            	<option value=""> select an option</option>

												            	<option value="1" > Yes / No</option>

												            	<option value="2" > 3 Pointer (Promoter, Distractor, Passive)</option>

												            	<option value="3" > 4 Pointer (Good, Bad, Excellent, Poor)</option>

												            	<option value="4" > 5 Pointer (Excellent, Good, Average, Bad, Poor)</option>

												            </select>

												            {{-- {{ Form::select('non_scoring_option_group',list_non_scoring_obs_options(),null,['class'=>'form-control','placeholder'=>"Select an option!"]) }} 

												            <span class="form-text text-muted">This will only applicable when "Is non scoring" is checked!</span>

												          </div> --}}



												          <div class="kt-separator kt-separator--border-dashed kt-separator--space-lg"></div>

														</div>



													</div>



												</div>

												<div class="form-group row">

													<div class="col-lg-4">

														<div data-repeater-create="" class="btn btn-primary btn-sm">

															<span>

																<i class="fa fa-plus"></i>

																<span>Add</span>

															</span>

														</div>

													</div>

												</div>

											</div>

          

        </div>

        <div class="card-footer">

			<button type="submit" class="btn btn-primary btn-sm">

				<i class="fa fa-dot-circle-o"></i> Create

			</button>

			<button type="reset" class="btn btn-danger btn-sm">

				<i class="fa fa-ban"></i> Reset

			</button>

		</div>

      </form>



      <!--end::Form-->

    </div>



    <!--end::Portlet-->

  </div>

</div>





@endsection

@section('js')

@include('shared.form_js')

@endsection