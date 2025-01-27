@extends('layouts.master')

@section('sh-title')
QM - Sheet
@endsection

@section('sh-detail')
Edit
@endsection

@section('content')
<div class="row">
  <div class="col-lg-12">
  <div class="card">
      <div class="card-header">
          <strong>Edit Sheet</strong>
      </div>
      <div class="card-body card-block">

      <!--begin::Form-->
      
        {!! Form::model($data,
                  array(
                  'method' => 'PATCH',
                    'url' =>'qm_sheet/'.Crypt::encrypt($data->id),
                    'class' => 'kt-form',
                    'data-toggle'=>"validator")
                  ) !!}
        {{-- <input type="hidden" name="company_id" value="{{AUth::User()->company_id}}"> --}}

        <div class="row">
          <div class="col-md-3 form-group">
            <label>Lob Name*</label>
            <select name="lob" class="form-control">
              <option value="">Choose Lob Name</option>
              <option value="collection" {{($data->type=='collection')?'selected':''}}>Collection</option>
              <option value="commercial_vehicle" {{($data->type=='commercial_vehicle')?'selected':''}}>Commercial Vehicle</option>
              <option value="rural" {{($data->type=='rural')?'selected':''}}>Rural</option>
              <option value="alliance" {{($data->type=='alliance')?'selected':''}}>Alliance</option>
              <option value="credit_card" {{($data->type=='credit_card')?'selected':''}}>Credit Card</option>
            </select>
          </div>
          <div class="col-md-3 form-group">
            <label>Sheet Name*</label>
            <input type="text" name="name" class="form-control" required value="{{$data->name}}">
          </div>
          <div class="col-md-3 form-group">
            <label>Sheet Type*</label>
            <select name="type" class="form-control">
              <option value="">Choose Sheet Type</option>
              <option value="branch" {{($data->type=='branch')?'selected':''}}>Branch</option>
              <option value="agency" {{($data->type=='agency')?'selected':''}}>Agency</option>
              <option value="branch_repo" {{($data->type=='branch_repo')?'selected':''}}>Branch Repo</option>
              <option value="agency_repo" {{($data->type=='agency_repo')?'selected':''}}>Agency Repo</option>
              <option value="repo_yard" {{($data->type=='repo_yard')?'selected':''}}>Repo and Yard</option>
            </select>
          </div>
          {{-- <div class="col-md-3 form-group">
            <label>Sheet Code*</label>
            <input type="text" name="code" class="form-control" required value="{{$data->code}}">
          </div>
          <div class="col-md-3 form-group">
            <label>Sheet Verion*</label>
            <input type="number" name="version" class="form-control" required value="{{$data->version}}">
          </div> --}}
          <!-- <div class="col-md-3 form-group">
            <label>Sheet Banner*</label>
            <input type="file" name="banner" class="form-control">
          </div> -->
          <div class="col-md-3 form-group">
            <label>Details</label>
            <textarea class="form-control" name="details">{{$data->details}}</textarea>
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

      <!--end::Form-->
    </div>

    <!--end::Portlet-->
  </div>
</div>

@endsection
@section('js')
@include('shared.form_js')
@endsection