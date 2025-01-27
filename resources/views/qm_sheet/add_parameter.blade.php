@extends('layouts.master')



@section('sh-title')

{{$qm_sheet_data->name}}

@endsection



@section('sh-detail')

Create New Parameter

@endsection



@section('sh-toolbar')

<div class="kt-subheader__toolbar">

    <div class="kt-subheader__wrapper">



        <a href="{{url('/qm_sheet/'.Crypt::encrypt($qm_sheet_data->id).'/list_parameter')}}"
            class="btn btn-label-success btn-bold">

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

                <a href="{{url('/qm_sheet/'.Crypt::encrypt($qm_sheet_data->id).'/list_parameter')}}"
                    class="btn btn-success btn-bold">

                    List All Parameter

                </a>

            </div>

        </div>

        <!--begin::Portlet-->

        <div class="card">

            <div class="card-header">

                <strong>Create New Parameter</strong>

            </div>

            <div class="card-body card-block">



                <!--begin::Form-->



                {!! Form::open(

                array(

                'route' => 'store_parameters',

                'class' => 'kt-form',

                'role'=>'form',

                'data-toggle'=>"validator")

                ) !!}

                {{-- <input type="hidden" name="company_id" value="{{Auth::user()->company_id}}"> --}}

                <input type="hidden" name="qm_sheet_id" value="{{$qm_sheet_data->id}}">

                <div class="row">



                    <div class="col-md-12 form-group">

                        <label>Parameter*</label>

                        <input type="text" name="parameter" class="form-control col-lg-6" required>

                    </div>

                    {{-- <div class="col-md-12 form-group">

            <div class="kt-form__group--inline">

				<div class="kt-form__control">

					<label>Is non scoring ?</label>

					<input type="checkbox" name="non_scoring" value="1"> 

				</div>

			</div>

          </div> --}}

                </div>



                <div class="kt-separator kt-separator--border-dashed kt-separator--space-lg"></div>

                <h6>Add Sub - Parameters</h6>

                <br />



                <div id="kt_repeater_1">

                    <div class="form-group  row" id="kt_repeater_1">

                        <div data-repeater-list="subs" class="col-lg-12">

                            <div data-repeater-item class="form-group align-items-center">

                                <div class="row">

                                    <div class="col-md-4">

                                        <div class="kt-form__group--inline">

                                            <div class="kt-form__control">

                                                <input type="text" class="form-control"
                                                    placeholder="Sub - Parameter name" name="sub_parameter">

                                            </div>

                                        </div>

                                        <div class="d-md-none kt-margin-b-10"></div>

                                    </div>



                                    <div class="col-md-2">

                                        <div class="kt-form__group--inline">

                                            <div class="kt-form__control">

                                                <input type="number" class="form-control" placeholder="Weightage"
                                                    name="weight">

                                            </div>

                                        </div>

                                        <div class="d-md-none kt-margin-b-10"></div>

                                    </div>



                                    <div class="col-md-4">

                                        <div class="kt-form__group--inline">

                                            <div class="kt-form__control">

                                                <textarea class="form-control" placeholder="Sub - Parameter details"
                                                    name="details"></textarea>

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



                                <br />

                                {{-- <span class="form-text text-muted">This will only applicable when "Is non scoring" is un-checked!</span><br/> --}}

                                <div class="row">

                                    <div class="col-md-2">

                                        <label class="kt-checkbox kt-checkbox--state-success">

                                            <input type="checkbox" name="s_pass" value="1"> Satisfactory

                                            <span></span>

                                        </label>

                                    </div>

                                    {{-- <div class="col-md-3">

																	{{ Form::select('s_pass_alert_box_id',$all_alert_box_list,null,['class'=>'form-control','placeholder'=>"Select alert box if any!"]) }}

                                </div> --}}

                                {{-- </div> --}}



                                {{-- <br/> --}}

                                {{-- <div class="row"> --}}

                                <div class="col-md-2">

                                    <label class="kt-checkbox kt-checkbox--state-success">

                                        <input type="checkbox" name="s_fail" value="1"> Unsatisfactory

                                        <span></span>

                                    </label>

                                </div>

                                {{-- <div class="col-md-3">

																	{{ Form::select('s_fail_alert_box_id',$all_alert_box_list,null,['class'=>'form-control','placeholder'=>"Select alert box if any!"]) }}

                            </div> --}}

                            {{-- <div class="col-md-4">

																	{{ Form::select('s_fail_reason_type_box_id',$all_reason_types,null,['class'=>'form-control','multiple'=>'multiple','style'=>"height:80px;"]) }}

                        </div> --}}

                        {{-- </div> --}}



                        {{-- <br/> --}}

                        {{-- <div class="row"> --}}

                        <div class="col-md-2">

                            <label class="kt-checkbox kt-checkbox--state-success">

                                <input type="checkbox" name="s_critical" value="1"> Critical

                                <span></span>

                            </label>

                        </div>

                        {{-- <div class="col-md-3">

																	{{ Form::select('s_critical_alert_box_id',$all_alert_box_list,null,['class'=>'form-control','placeholder'=>"Select alert box if any!"]) }}

                    </div> --}}

                    {{-- <div class="col-md-4">

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



    <div class="kt-separator kt-separator--border-dashed kt-separator--space-lg"></div>



    {{-- <h6>Non Scoring Observation Option Group</h6> --}}



    {{-- <div class="form-group col-md-5">

												            <label>Please select a group*</label>

												            <select class="form-control" name="non_scoring_option_group">

												            	<option value=""> select an option</option>

												            	<option value="1"> Yes / No</option>

												            	<option value="2"> 3 Pointer (Promoter, Distractor, Passive)</option>

												            	<option value="3"> 4 Pointer (Good, Bad, Excellent, Poor)</option>

												            	<option value="4"> 5 Pointer (Excellent, Good, Average, Bad, Poor)</option>

												            </select>

												            <span class="form-text text-muted">This will only applicable when "Is non scoring" is checked!</span>

												          </div> --}}





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

<script src="https://cdn.jsdelivr.net/npm/jquery@2.2.4/dist/jquery.min.js"></script>

@include('shared.form_js')

@endsection