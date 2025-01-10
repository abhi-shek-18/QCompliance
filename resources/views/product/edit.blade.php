@extends('layouts.app')

@section('title', '| Yard')

@section('sh-detail')
    Edit New
@endsection

@section('content')
<div class="row">
    <div class="col-lg-12">
    <div class="card">
        <div class="card-header">
            <strong>Edit Product</strong> form
        </div>
        <div class="card-body card-block">
        <form method="PUT" action="{{ route('product/'.Crypt::encrypt($data->id)) }}" class="kt-form">
        @csrf
            <div class="row">
                <div class="col-md-4">
                    <div class=" form-group">
                        <label for="text-input" class=" form-control-label">Name</label>
                        <input type="text" id="text-input" name="name" placeholder="Yard Name" class="form-control" value="{{$data->name}}">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class=" form-group">
                        <label for="text-input" class=" form-control-label">Bucket</label>
                        <input type="text" id="text-input" name="bucket" placeholder="Bucket Name" class="form-control" value="{{$data->bucket}}" tabindex="2">
                    </div>
                </div>
                {{-- <div class="col-md-4">
                    <div class=" form-group">
                        <label for="text-input" class=" form-control-label">Product Type</label>
                        <select name="type"  id="type" data-placeholder="Choose a Product Type..." class="standardSelect form-control" tabindex="1">
                            <option>Choose a Product Type...</option>
                            <option value="0" {{($data->type==0)?'selected':''}}>first</option>
                            <option value="1" {{($data->type==1)?'selected':''}}>second</option>
                        </select>
                    </div>
                </div> --}}
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
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.10/css/select2.min.css" rel="stylesheet"/>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.10/js/select2.min.js"></script>
    <script>


    </script>
@endsection