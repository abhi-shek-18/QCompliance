@extends('layouts.master')

@section('title', '| View')

<!-- @section('sh-detail')
Users
@endsection -->

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
					<strong class="card-title">Product Hierarchy View</strong>
					<a class="btn btn-primary btn-sm float-right" style="margin-right: 5px" href="{{route('excelDownloadProduct')}}" target="_blank">Export Product</a>
				</div>
				<div class="card-body">
					<table class="table table-striped- table-bordered table-hover table-checkable" id="kt_table_1">
						<thead>
							<tr>
								<th scope="col">#</th>
								<th scope="col">Branch Name</th>
								<th scope="col">Product Name</th>
								<th class="font-weight-bold" scope="col">Collection Manager</th>
								<th scope="col">Area Collection Manager</th>
								<th scope="col">Regional Collection Manager</th>
								<th scope="col">Zonal Collection Manager</th>
								<th scope="col">National Collection Manager</th>
								<th scope="col">Group Product Head</th>
								<th scope="col">Actions</th>
							</tr>
						</thead>
						<tbody>
							@foreach($data as $row)
								<tr scope="row">
									<td>{{$loop->iteration}}</td>
									<td>
										@if(isset($row->br_name))
											{{$row->br_name }}
										@else
										---
										@endif
									</td>
									<td>
										@if (isset($row->pr_name))
											{{$row->pr_name}}
										@else
											---
										@endif
									</td>

									<td class="font-weight-bold">
										@if (isset($row->cm_name))
											{{$row->cm_name}}
										@else
											---
										@endif
									</td>
									<td>
										@if (isset($row->acm_name))
											{{$row->acm_name}}
										@else
											-
										@endif
									</td>
									<td>
										@if (isset($row->rcm_name))
											{{$row->rcm_name}}
										@else
											-
										@endif
									</td>
									<td>
										@if (isset($row->zcm_name))
											{{$row->zcm_name}}
										@else
											-
										@endif
									</td>
									<td>
										@if (isset($row->ncm_name))
											{{$row->ncm_name}}
										@else
											-
										@endif
									</td>
									<td>
										@if (isset($row->gph_name))
											{{$row->gph_name}}
										@else
											-
										@endif
									</td>
									<td nowrap>
										<a href="{{ route('hierarchyEdit', ['id' => $row->id]) }}" class="btn btn-xs btn-info" title="View">
											<i class="fa fa-edit"></i>
										</a>
										
										<div class="btn-group">										
											<form action="{{ route('producthierarchy.destroy', ['id' => $row->id]) }}" method="POST">
												@csrf
												@method('DELETE')
												<button class="btn btn-xs btn-danger" type="submit"><i class="fa fa-trash"></i></button>
											</form>
										</div>
									</td>
								</tr>
							@endforeach

						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>
</div>
@endsection
@section('css')
<link rel="stylesheet" href="https://cdn.datatables.net/1.10.21/css/jquery.dataTables.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/1.6.2/css/buttons.dataTables.min.css">
@endsection
@section('js')
<script src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.min.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/buttons/1.6.2/js/dataTables.buttons.min.js"></script> 
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/buttons/1.6.2/js/buttons.html5.min.js"></script>
<script>
	jQuery(document).on('ready',function(){
		jQuery('#kt_table_1').DataTable();
		// {
		// 	dom: 'Bfrtip',
        // buttons: [
        //     'excelHtml5',
        // ]
    	// }
	})
</script>
@endsection