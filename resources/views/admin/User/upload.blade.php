@extends('layouts.app')

@section('title', '| Upload')

@section('sh-detail')
    Create New
@endsection

@section('content')
<div class="row">
    <div class="col-md-12">
    <div class="card ">
        <div class="card-header">
            <strong>Users Upload</strong>
            <a class="btn btn-primary btn-sm float-right" href="{{route('downloadUser')}}">Download Sample</a>
        </div>
        <div class="card-body card-block">
        <form method="post" action="{{ route('userImport') }}" class="form-horizontal">
        @csrf
          
            <div class="row">
                <!-- <div class="col-sm-6">
                    <div class=" form-group">
                        <label for="text-input" class=" form-control-label">Role</label>
                        <select name="role[]" multiple="" class="form-control">
                            {{-- <option>Please Choose Role first</option> --}}
                            @foreach ($roles as $item)
                                <option value="{{$item->id}}">{{$item->name}}</option>
                            @endforeach
                        </select>
                    </div>
                </div> -->
                <div class="col-sm-6">
                    <div class=" form-group">
                        <label for="user_excel" class=" form-control-label">User Excel</label>
                        <input type="file" id="user_excel" name="user_excel" class="form-control-file">
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    @if($errors->any())
                    <div class="text-danger">
                        {{-- <u>There are {{count($errors->all())}} {{count($errors->all())>1?'errors':'error'}} in your excel</u>  --}}
                        Errors
                    </div>
                    <div>
                        <ol class="ml-5">
                            @foreach ($errors->all() as $error)
                                <li class="text-danger">{{$error}}</li>
                            @endforeach
                        </ol>
                    </div>    
                    @endif
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

@endsection
@section('js')
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.10/css/select2.min.css" rel="stylesheet"/>
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