@extends('layouts.master')

@section('sh-title')
QM - Sheet
@endsection

@section('sh-detail')
All Client
@endsection

@section('content')
<div class="row">
	<div class="col-lg-12" style="margin-top:10x">
	</div>
</div>
<div class="animated fadeIn">
	<div class="row">
		<div class="col-lg-12">
			<div class="card">
				<div class="card-header">
					<strong class="card-title">Sheet List</strong>
				</div>
				<div class="card-body">

					<!--begin: Datatable -->
					<table class="table table-striped- table-bordered table-hover table-checkable" id="kt_table_1">
						<thead>
							<tr>
									<th title="Field #1">#</th>
									{{-- <th title="Field #2">
										Client
									</th> --}}
									{{-- <th title="Field #2">
										Process
									</th> --}}
									{{-- <th title="Field #2">
										Version
									</th> --}}
									<th title="Field #2">
										Name
									</th>
									<th title="Field #2">
										Lob
									</th>
									{{-- <th title="Field #2">
										Code
									</th> --}}
									<th title="Field #2">
										Type
									</th>
									<th title="Field #2">
										Total Parameters
									</th>
									<th title="Field #7">
										Actions
									</th>
							</tr>
						</thead>
						<tbody>
							@foreach($data as $row)
							<tr>
								<td>{{$loop->iteration}}</td>
														{{-- <td>
															{{$row->client->name}}
														</td> --}}
														{{-- <td>
															{{$row->process->name}}
														</td> --}}
														{{-- <td>
															{{$row->version}}
														</td> --}}
														<td>
															{{$row->name}}
														</td>
														<td>
															{{$row->lob}}
														</td>
														{{-- <td>
															{{$row->code}}
														</td> --}}

														<td>
															{{ucfirst($row->type)}}
														</td>
														<td>{{$row->parameter->count()}}</td>
								<td nowrap>
									<div style="display: flex;">
										<!-- {{Form::open([ 'method'  => 'delete', 'route' => [ 'qm_sheet.destroy', Crypt::encrypt($row->id) ],'onsubmit'=>"delete_confirm()"])}}
										<button class="btn btn-sm btn-clean btn-icon btn-icon-md" title="Delete">
											<i class="fa fa-trash"></i>
										</button>
									</form>
									<a href="{{url('qm_sheet/'.Crypt::encrypt($row->id).'/edit')}}" class="btn btn-sm btn-clean btn-icon btn-icon-md" title="Edit">
										<i class="fa fa-edit"></i>
									</a> -->
									<a href="{{url('qm_sheet/'.Crypt::encrypt($row->id).'/parameter')}}" class="btn btn-sm btn-clean btn-icon btn-icon-md" title="Manage Parameters">
										<i class="fa fa-list"></i>
									</a>
									<a href="{{url('audit_sheet/'.Crypt::encrypt($row->id))}}" class="btn btn-sm btn-clean btn-icon btn-icon-md" title="Audit Sheet">
										<i class="fa fa-eye"></i>
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
<script>
function delete_confirm() {
	var x = confirm("Are you sure you want to delete?");
	if (x) {
		return true;
	}
	else {
		event.preventDefault();
		return false;
	}
}
</script>
{{-- @include('shared.table_js'); --}}
@endsection