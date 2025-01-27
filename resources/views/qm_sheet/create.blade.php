@extends('layouts.master')

@section('sh-title')
QM - Sheet
@endsection

@section('sh-detail')
Create New
@endsection

@section('content')

<div class="row">
  <div class="col-lg-12">
  <div class="card">
      <div class="card-header">
          <strong>Create Sheet</strong>
      </div>
      <div class="card-body card-block">

      <!--begin::Form-->
      
        {!! Form::open(
                  array(
                    'route' => 'qm_sheet.store', 
                    'class' => 'form-horizontal',
                    'role'=>'form',
                    'data-toggle'=>"validator")
                  ) !!}
        <div class="row">
          <div class="col-md-3 form-group">
            <label>Lob Name*</label>
            <select name="lob" class="form-control">
              <option value="">Choose Lob Name</option>
              <option value="collection">Collection</option>
              <option value="commercial_vehicle">Commercial Vehicle</option>
              <option value="rural">Rural</option>
              <option value="alliance">Alliance</option>
              <option value="credit_card">Credit Card</option>
            </select>
          </div>
          <div class="col-md-3 form-group">
            <label>Sheet Name*</label>
            <input type="text" name="name" class="form-control" required>
          </div>
          <div class="col-md-3 form-group">
            <label>Sheet Type*</label>
            <select name="type" class="form-control">
              <option value="">Choose Sheet Type</option>
              <option value="branch">Branch</option>
              <option value="agency">Agency</option>
	      <option value="branch_repo">Branch Repo</option>
              <option value="agency_repo">Agency Repo</option>
              <option value="repo_yard">Repo and Yard</option>
            </select>
          </div>
          {{-- <div class="col-md-3 form-group">
            <label>Sheet Code*</label>
            <input type="text" name="code" class="form-control" required>
          </div>
          <div class="col-md-3 form-group">
            <label>Sheet Verion*</label>
            <input type="number" name="version" class="form-control" required>
          </div> --}}
          <!-- <div class="col-md-3 form-group">
            <label>Sheet Banner*</label>
            <input type="file" name="banner" class="form-control">
          </div> -->
          <div class="col-md-3 form-group">
            <label>Details</label>
            <textarea class="form-control" name="details"></textarea>
          </div>
          
        </div>
        <div class="card-footer">
          <button type="submit" class="btn btn-primary btn-sm">
              <i class="fa fa-dot-circle-o"></i> Create
          </button>
          <button type="reset" class="btn btn-danger btn-sm">
              <i class="fa fa-ban"></i> Reset
          </button>
      </div>

      </form>

      <!--end::Form-->
    </div>

    <!--end::Portlet-->
  </div>
</div>

@endsection
@section('js')
{{-- @include('shared.form_js') --}}
@endsection