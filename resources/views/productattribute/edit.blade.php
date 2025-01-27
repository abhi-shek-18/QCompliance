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
            <strong>Edit Product Attributes</strong> form
        </div>
        <div class="card-body card-block">
        <form method="POST" action="{{ route('productattribute.update',Crypt::encrypt($data->id)) }}" class="kt-form">
        @csrf
        @method('PUT')
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label class="form-control-label">Product</label>
                        <select name="product_id" class="form-control">
                            <option value="">Select Product</option>
                            @foreach($products as $productdata)
                                <option value="{{$productdata->id}}" 
                                    @if(isset($data->product_id) && $data->product_id == $productdata->id) 
                                        selected 
                                    @endif>
                                    {{$productdata->name}}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class=" form-group">
                        <label for="text-input" class=" form-control-label">Product Attributes Name</label>
                        <input type="text" id="text-input" name="product_attribute_name" placeholder="Product Attributes Name" class="form-control" value="{{$data->product_attribute_name}}">
                    </div>
                </div>
                <!-- <div class="col-md-4">
                    <div class=" form-group">
                        <label for="text-input" class=" form-control-label">Bucket</label>
                        <input type="text" id="text-input" name="bucket" placeholder="Bucket Name" class="form-control" value="{{$data->bucket}}" tabindex="2">
                    </div>
                </div> -->
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