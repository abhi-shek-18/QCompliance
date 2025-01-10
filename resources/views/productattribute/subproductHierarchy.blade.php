@extends('layouts.master')

@section('title', '| Product Hierarchy')

@section('sh-detail')
    Create New
@endsection

@section('content')
<div class="row">
    <div class="col-lg-12">
    <div class="card">
        <div class="card-header">
            <strong>Product Hierarchy</strong>
        </div>
        <div class="card-body card-block">
            {!! Form::open(
                     array(
                       'route' => 'doHierarchy',
                       'class' => 'form-horizontal',
                       'role'=>'form',
                       'data-toggle'=>"validator")
                     ) !!}
            <div class="row">
                <div class="col-md-6">
                    <div class=" form-group">
                        <label for="text-input" class=" form-control-label">Branch</label>
                        <select name="branch" id="branch" data-placeholder="Choose a Branch..." class="standardSelect form-control" tabindex="3">
                            <option>Choose a branch...</option>
                            @foreach($branch as $k=>$value)
                                <option value="{{$value->id}}">{{$value->name}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class=" form-group">
                        <label for="text-input" class=" form-control-label">Product Type</label>
                        <select name="type" id="type" data-placeholder="Choose a Product Type..." class="standardSelect form-control" tabindex="3">
                            <option>Choose a Products...</option>
                            @foreach($product as $k=>$value)
                        <option value="{{$value->id}}">{{$value->name}}({{($value->type==1)?'Recovery':'Regular'}})</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
            <div class="row" id="next">
                @php
                    $collection=[
                        'Area Collection Manager',
                        'Regional Collection Manager',
                        'Zonal Collection Manager',
                        'National Collection Manager',
                        'Group Product Head',
                        'Head of the Collections',
                    ];   
                @endphp
            @foreach ($collection as $item)
            
                <div class="col-md-4">
                    <div class=" form-group">
                        <label for="text-input" class=" form-control-label">{{$item}}</label>
                        <select name="{{str_replace(' ','_',$item)}}" id="type" data-placeholder="Choose a {{$item}}..." class="standardSelect form-control" tabindex="3">
                            <option value="">Choose a {{$item}}...</option>
                            @isset($finalUser[$item])
                                @foreach($finalUser[$item] as $k=>$value)
                                    <option value="{{$value->id}}">{{$value->name}}</option>
                                @endforeach
                            @endif
                        </select>
                    </div>
                </div>
            
                @endforeach
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