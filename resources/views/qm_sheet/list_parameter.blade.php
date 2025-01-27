@extends('layouts.master')

@section('sh-title')
{{$qm_sheet_data->name}}
@endsection

@section('sh-detail')
Parameters
@endsection

@section('sh-toolbar')
							<div class="kt-subheader__toolbar">
								<div class="kt-subheader__wrapper">

								<a href="{{url('qm_sheet/'.Crypt::encrypt($qm_sheet_data->id).'/add_parameter')}}" class="btn btn-label-success btn-bold">
									Create New Parameter
								</a>
								
								</div>
							</div> 
@endsection

@section('content')

<div class="row">
	<div class="col-lg-12" style="margin-top:10x">
	</div>
</div>
<div class="animated fadeIn">
	<div class="row">
		<div class="col-lg-12">
			<div class="text-right" style="margin-bottom:10px;">
				<div class="kt-subheader__wrapper">

				<a href="{{url('qm_sheet/'.Crypt::encrypt($qm_sheet_data->id).'/add_parameter')}}" class="btn btn-success btn-bold">
					Create New Parameter
				</a>
				
				</div>
			</div>
			<div class="card">
				<div class="card-header">
					<strong class="card-title">Sheet parameter List</strong>
				</div>
				<div class="card-body">		<!--begin: Datatable -->
		<table class="table table-striped- table-bordered table-hover table-checkable" id="kt_table_1">
			<thead>
				<tr>
						<th title="Field #1">#</th>
						<th title="Field #2">
							Parameter
						</th>
						<th title="Field #2">
							Sub Parameter - Weightage
						</th>
						<th title="Field #2">
							Weightage
						</th>
						<th title="Field #2">
							Type
						</th>
						<th title="Field #7">
							Action
						</th>
				</tr>
			</thead>
			<tbody>
				@foreach($data as $row)
				<tr>
					<td>{{$loop->iteration}}</td>
											<td>
												{{$row->parameter}}
											</td>
											<td>
												<ol>
												<?php $total_weightage=0; ?>
												@foreach($row->qm_sheet_sub_parameter as $ksp=>$vsp)
												<li>{{$vsp->sub_parameter}} - <strong>{{$vsp->weight}}</strong></li>
												<?php $total_weightage += $vsp->weight;?>
												@endforeach
												</ul>
											</td>
											<td>
												{{$total_weightage}}
											</td>
											<td>
												{{($row->is_non_scoring)?"Non-Scoring":"Scoring"}}
											</td>
											
					<td nowrap>
                        <div style="display: flex;">
                        	{{Form::open([ 'method'  => 'delete', 'route' => [ 'delete_parameter', Crypt::encrypt($row->id) ],'onsubmit'=>"delete_confirm()"])}}
                        	<button class="btn btn-sm btn-clean btn-icon btn-icon-md" title="View">
                        		<i class="fa fa-trash"></i>
                        	</button>
                        </form>
                        <a href="{{url('parameter/'.Crypt::encrypt($row->id).'/edit')}}" class="btn btn-sm btn-clean btn-icon btn-icon-md" title="Edit">
                        	<i class="fa fa-edit"></i>
                        </a>
                    </div>

                </td>
            	</tr>
            @endforeach

        </tbody>
    </table>

    <!--end: Datatable -->
</div>
</div>
</div>
</div>
</div>
@endsection
@section('css')
{{-- @include('shared.table_css'); --}}
@endsection
@section('js')
{{-- @include('shared.table_js'); --}}
@endsection