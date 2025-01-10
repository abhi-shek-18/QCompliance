@extends('layouts.app')

@section('title', '| Sub Product')

@section('sh-detail')
    Create New
@endsection

@section('content')
<div class="row">
    <div class="col-lg-12">
    <div class="card">
        <div class="card-header">
            <strong>Create Product Attributes</strong>
            <a class="btn btn-primary btn-sm float-right" style="margin-right: 5px" href="{{route('productattribute.index')}}">Product Attributes List</a>
        </div>
        <div class="card-body card-block">
        <form method="post" action="{{ route('productattribute.store') }}" class="form-horizontal">
        @csrf
     
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label class="form-control-label">Product</label>
                        <select name="product_id" class="form-control" required>
                            <option value="">Select Product</option>
                            @foreach($products as $data)
                            <option value="{{$data->id}}">{{$data->name}}</option>
                            @endforeach
                          </select>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class=" form-group">
                        <label for="text-input" class=" form-control-label">Product Attributes</label>
                        <input type="text" id="text-input" name="product_attribute_name" placeholder="Product Attributes Name" class="form-control" value="{{old('product_attribute_name')}}" tabindex="1">
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
    <script>

        
    </script>
@endsection