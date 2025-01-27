@extends('layouts.app')



@section('title', '| Users')



<!-- @section('sh-detail')

Users

@endsection -->


@php
	use Illuminate\Support\Facades\Crypt;
@endphp

@section('content')

<div class="row">

	<div class="col-lg-12" style="margin-top:10x">

	</div>

</div>

<div class="animated fadeIn">

	<div class="row">

		<div class="col-lg-12">

			<div class="card">

				<divspan class="card-header">

					<strong class="card-title">
						<h3>All Users List</h3>
					</strong>
					<hr>
			
					<span class="d-flex justify-content-end">
					
					</span>

				</divspan>

				<div class="card-body">
				<div class="d-flex justify-content-end">
							<a class="btn btn-primary btn-xs float-right" style="margin-right: 10px"
							href="{{route('userUpload')}}">Import Users (Create bulk user)</a>

						<a class="btn btn-primary btn-xs float-right" style="margin-right: 10px"
							href="{{route('excelDownloadUser')}}" target="_blank">Export Users</a>
						<a class="btn btn-primary btn-xs float-right" style="margin-right: 10px"
							href="{{route('bulkDeactivate')}}">De-Activate
							user(Bulk)</a>
						<a href="{{ route('User.create') }}" class="btn btn-primary btn-xs float-right"style="margin-right: 10px">
							<i class="fa fa-plus"></i> Add New User
						</a>
						</div>
				<span class="d-flex justify-content-between mb-3">
						<div id="table-search-container">
							<label>
								Search:
								<input type="text" id="table-search" class="form-control form-control-sm"
									placeholder="Search users...">
							</label>
							</div>
					</span>
					<table class="table table-striped table-bordered table-hover table-checkable" id="kt_table_1">
						<thead>
							<tr>
								<th scope="col">#</th>
								<th scope="col">Name</th>
								<th scope="col">Role</th>
								<th scope="col">Email</th>
								<th scope="col">Phone</th>
								<th scope="col">Status</th>
								<th scope="col">Actions</th>
								<th scope="col">Disable User</th>
							</tr>
						</thead>
						<tbody>
							@foreach($data as $row)
								<tr scope="row">
									<td>{{ $data->firstItem() + $loop->index }}</td>
									<!-- Adjusted row numbering for pagination -->
									<td>{{ $row->name }}</td>
									<td>
										@if($row->roles->isNotEmpty())
											{{ $row->roles->first()->name }}
										@else
											No Role Assigned
										@endif
									</td>
									<td>{{ $row->email }}</td>
									<td>{{ $row->mobile }}</td>
									<td>
										@if($row->active_status == 0)
											Activated
										@else
											De-Activated
										@endif
									</td>
									<td nowrap>
										<a href="{{ url('User/' . Crypt::encrypt($row->id) . '/edit') }}"
											class="btn btn-xs btn-info" title="Edit">
											<i class="fa fa-edit"></i>
										</a>
										<a href="{{ url('User/' . Crypt::encrypt($row->id)) }}" class="btn btn-xs btn-info"
											title="View">
											<i class="fa fa-eye"></i>
										</a>
									</td>
									<td nowrap>
										<a class="btn btn-xs btn-info"
											onclick="block_user('{{ Crypt::encrypt($row->id) }}')" title="Disable">
											<i class="fa fa-ban"></i>
										</a>
									</td>
								</tr>
							@endforeach
						</tbody>
					</table>

					<!-- Pagination Links -->
					<div class="d-flex justify-content-center">
						{{ $data->links('pagination::bootstrap-4') }}
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const searchInput = document.getElementById('table-search');
        const table = document.getElementById('kt_table_1');

        searchInput.addEventListener('keyup', function () {
            const filter = searchInput.value.toLowerCase();
            const rows = table.getElementsByTagName('tr');

            // Loop through table rows and hide/show based on search
            for (let i = 1; i < rows.length; i++) { // Start from 1 to skip the header row
                const cells = rows[i].getElementsByTagName('td');
                let match = false;

                // Check each cell in the row
                for (let j = 0; j < cells.length; j++) {
                    const cellValue = cells[j].textContent || cells[j].innerText;
                    if (cellValue.toLowerCase().includes(filter)) {
                        match = true;
                        break;
                    }
                }

                // Show the row if there's a match, otherwise hide it
                rows[i].style.display = match ? '' : 'none';
            }
        });
    });
</script>



@endsection

@section('css')

<link rel="stylesheet" href="https://cdn.datatables.net/1.10.21/css/jquery.dataTables.min.css">

@endsection

@section('js')

<script src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.min.js"></script>

<script>

	jQuery(document).on('ready', function () {

		jQuery('#kt_table_1').DataTable();

	})

</script>


<script type="text/javascript">
	function block_user(id) {
		if (confirm("Are you sure you want to block?")) {

			var link = '/user/' + id + '/disable'
			location.href = link;
		}
	}
</script>

@endsection