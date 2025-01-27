@extends('layouts.app')
@section('title', '| Users')

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
                    <strong class="card-title">Audit Agency List</strong>
                </div>

                <div class="card-body">

                    <table class="table table-striped- table-bordered table-hover table-checkable" id="kt_table_1">

                        <thead>
                            <tr>
                                <th scope="col">Sr.No.</th>
                                <th class="font-weight-bold" scope="col">Name</th>
                                <th scope="col">Phone</th>
                                <th scope="col">Email</th>
                                <!-- <th scope="col">Admin Name</th>
                                <th scope="col">Admin Email</th> -->
                                <th scope="col">Action</th>
                            </tr>
                        </thead>

                        <tbody>

                        @foreach($auditagency as $row)
                            <tr scope="row">
                                <td>{{$loop->iteration}}</td>
                                <td class="font-weight-bold">
                                    {{$row->name}}
                                </td>
                                <td>
                                    {{$row->mobile}}
                                </td>
                                <td>
                                    {{$row->email}}
                                </td>
                                <!-- <td>
                                    {{$row->agency_admin}}
                                </td>
                                <td>
                                    {{$row->agency_admin_email_one}} , {{$row->agency_admin_email_two}}
                                </td> -->
                                <td nowrap>
                                    <div class="btn-group">
                                        <a href="{{url('audit_agency/'.Crypt::encrypt($row->id).'/edit')}}" class="btn btn-xs btn-info mr-1" title="View">
                                        <i class="fa fa-edit"></i>
                                        </a>
                                        
                                       <!-- <form action="{{ route('audit_agency.destroy', Crypt::encrypt($row->id)) }}" method="POST" style="display:inline;" onsubmit="return confirmDelete();">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-xs btn-danger mr-1" title="Delete">
                                                <i class="fa fa-trash"></i>
                                            </button>
                                        </form> -->

                        
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

@endsection

@section('js')

<script src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.min.js"></script>

<script>

    jQuery(document).on('ready',function(){

        jQuery('#kt_table_1').DataTable();

    })

</script>

<script type="text/javascript">
   function block_user(id) {
        if (confirm("Are you sure you want to block?")) {

            var link  = '/user/'+id+'/disable'
            location.href = link;
        }
    }
</script>

<script>
    function confirmDelete() {
        return confirm('Are you sure you want to delete this item?');
    }
</script>

<script>
    function delete_confirm() {
        return confirm("Are you sure you want to delete this audit agency?");
    }
</script>

@endsection