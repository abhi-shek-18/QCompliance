@extends('layouts.app')

@section('title', '| Agency')

@section('sh-detail')
Edit Agency
@endsection

@section('content')
<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                <strong>Edit Agency</strong> form
            </div>
            <div class="card-body card-block">
                @if(!$data)
                    <div class="alert alert-danger">
                        The agency you are trying to edit does not exist.
                    </div>
                @else
                <form method="post" action="{{ route('agency/' . Crypt::encrypt($data->id)) }}" class="kt-form">
                @csrf
                @method('put')
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="text-input" class="form-control-label">Name</label>
                            <input type="text" id="text-input" name="name" placeholder="Agency Name" class="form-control"
                                value="{{ $data->name }}">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="multiple-select" class="form-control-label">Select Product</label>
                            <select id="product_id" name="product_id" class="form-control">
                                <option value="" disabled>Select a product</option>
                                @foreach($products as $key => $value)
                                    <option value="{{ $key }}" {{ $key == old('product_id', $data->product_id) ? 'selected' : '' }}>
                                        {{ $value }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="agency_id" class="form-control-label">Agency ID</label>
                            <input type="text" id="agency_id" name="agency_id" placeholder="Agency ID"
                                class="form-control" value="{{ $data->agency_id }}">
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="email" class="form-control-label">Email</label>
                            <div id="email-group">
                                @foreach ($data->emails as $email)
                                    <div class="input-group mb-3">
                                        <input type="text" name="emails[]" class="form-control" placeholder="Email"
                                            value="{{ $email->email }}">
                                        <div class="input-group-append">
                                            <button class="btn btn-danger remove-email" type="button">Remove</button>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            <button id="add-more-email" type="button" class="btn btn-success">Add More Email</button>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="mobile_number" class="form-control-label">Mobile Number</label>
                            <div id="mobile-number-group">
                                @foreach ($data->mobileNumbers as $mobile)
                                    <div class="input-group mb-3">
                                        <input type="text" name="mobile_numbers[]" class="form-control" placeholder="Mobile Number"
                                            value="{{ $mobile->mobile_number }}">
                                        <div class="input-group-append">
                                            <button class="btn btn-danger remove-mobile" type="button">Remove</button>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            <button id="add-more-mobile" type="button" class="btn btn-success">Add More Mobile</button>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="agency_manager" class="form-control-label">Agency Manager</label>
                            <select name="agency_manager" data-placeholder="Choose an Agency Manager..."
                                class="form-control" tabindex="3">
                                <option value="" label="Agency Manager"></option>
                                @foreach($user as $k=>$item)
                                    <option value="{{ $item->id }}" {{ ($data->agency_manager == $item->id) ? 'selected' : '' }}>
                                        {{ $item->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="multiple-select" class="form-control-label">Regions</label>
                            <select class="form-control" name="region_id" id="country">
                                <option value="">Choose Region</option>
                                @foreach ($regions as $country)
                                    <option @if($data->region_id == $country->id) selected @endif value="{{ $country->id }}">
                                        {{ $country->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="multiple-select" class="form-control-label">State</label>
                            <select class="form-control" name="state" id="state">
                                <!-- States will be populated dynamically -->
                            </select>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="multiple-select" class="form-control-label">Location (City)</label>
                            <select class="form-control" name="city_id" id="city">
                                <!-- Cities will be populated dynamically -->
                            </select>
                        </div>
                    </div>

                    <div class="col-md-6" style="display: none;">
                        <div class="form-group">
                            <label for="location" class="form-control-label">Location</label>
                            <input type="text" id="location" name="location" placeholder="Location" class="form-control" value="{{ $data->location }}">
                        </div>
                    </div>

                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="address" class="form-control-label">Address</label>
                            <input type="text" id="address" name="address" placeholder="Address" class="form-control"
                                value="{{ $data->address }}">
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
                {!! Form::close() !!}
                @endif
            </div>
        </div>
    </div>
</div>



@endsection

@section('js')
<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.10/css/select2.min.css" rel="stylesheet" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.10/js/select2.min.js"></script>


<!-- V Region State City Start------>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
<script>
    $(document).ready(function () {
        var stateId = {{ $data->city->state->id ?? 'null' }};
        var cityId = {{ $data->city->id ?? 'null' }};

        $('#country').change(function () {
            var countryId = $(this).val();
            if (countryId) {
                $.ajax({
                    type: "GET",
                    url: "{{ url('/getStates') }}/" + countryId,
                    success: function (res) {
                        $("#state").empty().append('<option value="">Select State</option>');
                        $("#city").empty().append('<option value="">Select City</option>');
                        $.each(res, function (key, value) {
                            $("#state").append('<option value="' + key + '">' + value + '</option>');
                        });
                        if (stateId) {
                            $("#state").val(stateId).change(); // Load cities
                        }
                    }
                });
            }
        });

        $('#state').change(function () {
            var stateId = $(this).val();
            if (stateId) {
                $.ajax({
                    type: "GET",
                    url: "{{ url('/getCities') }}/" + stateId,
                    success: function (res) {
                        $("#city").empty().append('<option value="">Select City</option>');
                        $.each(res, function (key, value) {
                            $("#city").append('<option value="' + key + '">' + value + '</option>');
                        });
                        if (cityId) {
                            $("#city").val(cityId);
                        }
                    }
                });
            }
        });
        $('#country').trigger('change');
    });
</script>
<!-- V Region State City End----->

<script>
    $(function () {
        $(".sizes").select2();
    });

    // Add your existing code for adding email and mobile inputs here

    document.getElementById('add-more-email').addEventListener('click', function() {
        let emailGroup = document.getElementById('email-group');
        let emailInput = `<div class="input-group mb-3">
                            <input type="text" name="emails[]" class="form-control" placeholder="Email">
                            <div class="input-group-append">
                                <button class="btn btn-danger remove-email" type="button">Remove</button>
                            </div>
                          </div>`;
        emailGroup.insertAdjacentHTML('beforeend', emailInput);
    });

    document.getElementById('add-more-mobile').addEventListener('click', function() {
        let mobileGroup = document.getElementById('mobile-number-group');
        let mobileInput = `<div class="input-group mb-3">
                            <input type="text" name="mobile_numbers[]" class="form-control" placeholder="Mobile Number">
                            <div class="input-group-append">
                                <button class="btn btn-danger remove-mobile" type="button">Remove</button>
                            </div>
                          </div>`;
        mobileGroup.insertAdjacentHTML('beforeend', mobileInput);
    });

    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('remove-email')) {
            e.target.closest('.input-group').remove();
        }
        if (e.target.classList.contains('remove-mobile')) {
            e.target.closest('.input-group').remove();
        }
    });
</script>
@endsection
