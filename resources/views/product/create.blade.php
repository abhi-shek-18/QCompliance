@extends('layouts.app')

@section('title', '| Product')

@section('sh-detail')
    Create New
@endsection


@section('content')
<div class="row">
    <div class="col-lg-12">
    <div class="card">
        <div class="card-header">
            <strong>Create Product</strong>
            <a class="btn btn-success btn-sm float-right" style="margin-right: 5px" href="{{route('productattribute.create')}}">Create Product Attributes</a>
            <a class="btn btn-primary btn-sm float-right" style="margin-right: 5px" href="{{route('product.index')}}">Product List</a>
        </div>
        <div class="card-body card-block">
        <form method="post" action="{{ route('product.store') }}" class="form-horizontal">
        @csrf

            <div class="row">
                <div class="col-md-4">
                    <div class=" form-group">
                        <label for="text-input" class="form-control-label">Product Name</label>
                        <input type="text" id="text-input" name="name" placeholder="Product Name" class="form-control" value="{{old('name')}}" tabindex="1">
                    </div>
                </div>
                                
                <div class="col-md-4">
                    <div class=" form-group">
                        <label for="text-input" class=" form-control-label">Bucket</label>
                        <input type="text" id="text-input" name="bucket" placeholder="Bucket Name" class="form-control" value="{{old('bucket')}}" tabindex="2">
                    </div>
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
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.10/css/select2.min.css" rel="stylesheet"/>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.10/js/select2.min.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.1/jquery.min.js"></script>  
    
@endsection