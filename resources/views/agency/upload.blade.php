@extends('layouts.master')

@section('title', '| Upload')

@section('sh-detail')
Create New
@endsection

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card ">
            <div class="card-header">
                <strong>Agency Bulk Upload</strong>
                <a class="btn btn-primary btn-sm float-right" href="{{route('downloadAgencySample')}}">Download Sample</a>

                <a class="btn btn-info btn-sm float-right" style="margin-right: 5px"
                    href="{{route('agency.index')}}">Agency List</a>
            </div>
            <div class="card-body card-block">

                <form action="{{ route('agencyimport') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="row">
                        <div class="col-sm-3">
                            <div class=" form-group">
                                <label for="text-input" class=" form-control-label">Select Excel File</label>
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class=" form-group">
                                <input type="file" name="file" accept=".xlsx, .xls, .csv">
                            </div>
                        </div>
                    </div>


                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary btn-sm">
                            <i class="fa fa-dot-circle-o"></i> Submit
                        </button>
                        <button type="reset" class="btn btn-danger btn-sm">
                            <i class="fa fa-ban"></i> Reset
                        </button>
                    </div>
                </form>
            </div>

        </div>
    </div>
</div>

@endsection
@section('js')
<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.10/css/select2.min.css" rel="stylesheet" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.10/js/select2.min.js"></script>
<script>
jQuery(document).ready(function() {
    jQuery('.datepicker').datepicker({
        format: "mm-yyyy",
        viewMode: "months",
        minViewMode: "months"
    });
})
</script>
@endsection