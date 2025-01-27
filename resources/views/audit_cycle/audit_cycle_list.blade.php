@extends('layouts.app')



@section('title', '| Audit Cycle')



<!-- @section('sh-detail')

Users

@endsection -->


<style>
    #flash-message {
        padding: 10px 20px;
        border-radius: 5px;
        color: #fff;
        font-weight: bold;
    }

    .alert-success {
        background-color: green;
    }

    .alert-danger {
        background-color: red;
    }
</style>

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

					<strong class="card-title">Audit Cycle List</strong>

					<a class="btn btn-primary btn-sm float-right" href="{{route('createCycle')}}">Create Audit
						Cycle</a>

				</div>

				<div class="card-body">

					<table class="table table-striped- table-bordered table-hover table-checkable" id="kt_table_1">
						<thead>
							<tr>
								<th scope="col">#</th>
								<th scope="col">Name</th>
								<th scope="col">Created At</th>
								<th scope="col">Status</th>
								<th scope="col">Actions</th>
							</tr>
						</thead>
						<tbody>
							@foreach($data as $row)
								<tr>
									<td>{{ $loop->iteration }}</td>
									<td>{{ $row->name }}</td>
									<td>{{ $row->created_at->format('Y-m-d h:i:s') }}</td>
									<td>
										<span id="status-container-{{ $row->id }}">
											@if($row->status == '0' || $row->status == '2')
												<button class="toggle-status btn btn-success" data-id="{{ $row->id }}"
													data-status="1">Activate</button>
											@else
												<button class="toggle-status btn btn-danger" data-id="{{ $row->id }}"
													data-status="2">Deactivate</button>
											@endif
										</span>
									</td>
									<td>
                                    <a href="{{ route('edit-audit-cycle', ['id' => $row->id]) }}"class="btn btn-sm btn-clean btn-icon btn-icon-md" title="Edit">
											<i class="fa fa-edit"></i>
										</a>
									</td>
								</tr>
							@endforeach
						</tbody>
					</table>

					<div id="flash-message" class="alert" style="display: none; position: fixed; top: 10px; right: 10px; z-index: 9999;"></div>


				</div>

			</div>

		</div>

	</div>

</div>

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

<!-- <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script> -->
<script>
   $(document).ready(function () {
   
    $(document).on('click', '.toggle-status', function () {
        var button = $(this); 
        var id = button.data('id');
        var status = button.data('status');

        
        $.ajax({
            url: '{{ url("toggle-status") }}',
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                id: id,
                status: status
            },
            success: function (response) {
                if (response.success) {
                    
                    $('#status-container-' + id).html(response.buttonHtml);
                }
                showFlashMessage(response.message, response.success ? 'alert-success' : 'alert-danger');
            },
            error: function (xhr) {
                console.error('Error:', xhr);
            }
        });
    });

    function showFlashMessage(message, alertClass) {
        var flashMessage = $('#flash-message');
        flashMessage.removeClass('alert-success alert-danger').addClass(alertClass).text(message).fadeIn();

        setTimeout(function () {
            flashMessage.fadeOut();
        }, 3500);
    }
});

</script>

<!-- <script>
    $(document).on('click', '.toggle-status', function() {
        const button = $(this);
        const id = button.data('id');
        const newStatus = button.data('status');

        $.ajax({
            url: '{{ route('toggle.status') }}',
            type: 'POST',
            data: {
                id: id,
                status: newStatus,
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                if (response.success) {
                    const container = $(`#status-container-${id}`);
                    container.html(response.buttonHtml);
					
                } else {
                    alert('Failed to update status.');
                }
            },
            error: function() {
                alert('An error occurred.');
            }
        });
    });
</script> -->

@endsection