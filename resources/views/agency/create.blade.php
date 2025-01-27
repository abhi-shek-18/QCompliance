@extends('layouts.app')

@section('title', '| Agency')

@section('sh-detail')
Create New
@endsection

@section('content')
<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                <strong>Create Agency</strong> form
            </div>

            <div class="card-body card-block">
            <form method="post" action="{{ route('agency.store') }}" class="form-horizontal">
            @csrf
                <div class="row">
                    <div class="col-md-6">
                        <div class=" form-group">
                            <label for="text-input" class=" form-control-label">Agency Name</label>
                            <input type="text" id="text-input" name="name" placeholder="Agency Name"
                                class="form-control" value="{{old('name')}}">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="multiple-select" class="form-control-label">Select Product</label>
                            <select id="product_id" name="product_id" class="form-control">
                                <option value="" disabled selected>Select a product</option>
                                @foreach($products as $key => $value)
                                    <option value="{{ $key }}">{{ $value }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class=" form-group">
                            <label for="agency_id" class=" form-control-label">Agency id</label>
                            <input type="text" id="agency_id" name="agency_id" placeholder="Agency id"
                                class="form-control" value="{{old('agency_id')}}">
                        </div>
                    </div>
                    <div class="col-md-6">
            <div class="form-group">
                <label for="email" class="form-control-label">Email</label>
                <div id="email_container">
                    <input type="text" id="email" name="emails[]" placeholder="email" class="form-control" value="{{old('email')}}">
                </div>
                <button type="button" class="btn btn-success mt-2" id="addEmailBtn">Add More Email</button>
            </div>
        </div>
        
        <div class="col-md-6">
            <div class="form-group">
                <label for="mobile_number" class="form-control-label">Mobile Number</label>
                <div id="mobile_container">
                    <input type="text" id="mobile_number" name="mobile_numbers[]" placeholder="Mobile Number"
                        class="form-control" value="{{old('mobile_number')}}">
                </div>
                <button type="button" class="btn btn-success mt-2" id="addMobileBtn">Add More Mobile</button>
            </div>
        </div>  
                    <div class="col-md-6">
                        <div class=" form-group">
                            <label for="agency_manager" class=" form-control-label">Agency Manager</label>
                            <!-- <input type="text" id="agency_manager" name="agency_manager" placeholder="Agency Manger" class="form-control" value="{{old('agency_manager')}}"> -->
                            <select name="agency_manager" data-placeholder="Choose a Agency Manager..."
                                class="standardSelect form-control" tabindex="3">
                                <option value="" label="Agency Manger"></option>
                                @foreach($user as $k=>$item)
                                <option value="{{$item->id}}">{{$item->name}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                  
                    <div class="col col-md-6">
                        <div class="form-group">
                            <label for="multiple-select" class=" form-control-label">Regions
                                    </label>
                            <select class="form-control" name="region_id" id="country">
                                <option value="">Choose Region</option>
                                @foreach ($regions as $country)
                                    <option value="{{$country->id}}">
                                        {{$country->name}}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col col-md-6">
                        <div class="form-group">
                            <label for="multiple-select" class=" form-control-label">State
                                    </label>
                                <select class="form-control" name="state" id="state">
                                </select>

                        </div>
                    </div>
                    <div class="col col-md-6">
                        <div class="form-group">
                            <label for="multiple-select" class=" form-control-label">City
                                    </label>

                                <select class="form-control" name="city_id" id="city">
                                </select>

                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class=" form-group">
                            <label for="location" class=" form-control-label">Location</label>
                            <input type="text" id="location" name="location" placeholder="Location" class="form-control"
                                value="{{old('location')}}">
                        </div>
                    </div>
                
                    <div class="col-md-6">
                        <div class=" form-group">
                            <label for="address" class=" form-control-label">Address</label>
                            <input type="text" id="address" name="address" placeholder="Address" class="form-control"
                                value="{{old('address')}}">
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

            <script>
                document.addEventListener('DOMContentLoaded', function () {
                    // Add email field
                    document.getElementById('addEmailBtn').addEventListener('click', function () {
                        var emailContainer = document.getElementById('email_container');
                        var inputGroup = document.createElement('div');
                        inputGroup.className = 'input-group mt-2';

                        var newInput = document.createElement('input');
                        newInput.type = 'text';
                        newInput.name = 'emails[]';
                        newInput.placeholder = 'Email';
                        newInput.className = 'form-control';

                        var removeButton = document.createElement('button');
                        removeButton.type = 'button';
                        removeButton.className = 'btn btn-danger ml-2';
                        removeButton.innerText = 'Remove';
                        removeButton.addEventListener('click', function () {
                            emailContainer.removeChild(inputGroup);
                        });

                        inputGroup.appendChild(newInput);
                        inputGroup.appendChild(removeButton);
                        emailContainer.appendChild(inputGroup);
                    });

                    // Add mobile field
                    document.getElementById('addMobileBtn').addEventListener('click', function () {
                        var mobileContainer = document.getElementById('mobile_container');
                        var inputGroup = document.createElement('div');
                        inputGroup.className = 'input-group mt-2';

                        var newInput = document.createElement('input');
                        newInput.type = 'text';
                        newInput.name = 'mobile_numbers[]';
                        newInput.placeholder = 'Mobile Number';
                        newInput.className = 'form-control';

                        var removeButton = document.createElement('button');
                        removeButton.type = 'button';
                        removeButton.className = 'btn btn-danger ml-2';
                        removeButton.innerText = 'Remove';
                        removeButton.addEventListener('click', function () {
                            mobileContainer.removeChild(inputGroup);
                        });

                        inputGroup.appendChild(newInput);
                        inputGroup.appendChild(removeButton);
                        mobileContainer.appendChild(inputGroup);
                    });
                });

            </script>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const countrySelect = document.getElementById('country');
        const stateSelect = document.getElementById('state');
        const citySelect = document.getElementById('city');

        // Event listener for country dropdown
        countrySelect.addEventListener('change', function () {
            const countryId = this.value;
            console.log('Country ID:', countryId); // Debugging
            if (countryId) {
                fetch(`/getStates/${countryId}`)
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('Network response was not ok');
                        }
                        return response.json();
                    })
                    .then(states => {
                        console.log('States:', states); // Debugging
                        stateSelect.innerHTML = '<option>Select State</option>';
                        citySelect.innerHTML = '<option>Select City</option>'; // Clear city dropdown
                        for (const [id, name] of Object.entries(states)) {
                            stateSelect.innerHTML += `<option value="${id}">${name}</option>`;
                        }
                    })
                    .catch(error => console.error('Error fetching states:', error));
            } else {
                stateSelect.innerHTML = '<option>Select State</option>';
                citySelect.innerHTML = '<option>Select City</option>';
            }
        });

        // Event listener for state dropdown
        stateSelect.addEventListener('change', function () {
            const stateId = this.value;
            console.log('State ID:', stateId); // Debugging
            if (stateId) {
                fetch(`/getCities/${stateId}`)
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('Network response was not ok');
                        }
                        return response.json();
                    })
                    .then(cities => {
                        console.log('Cities:', cities); // Debugging
                        citySelect.innerHTML = '<option>Select City</option>';
                        for (const [id, name] of Object.entries(cities)) {
                            citySelect.innerHTML += `<option value="${id}">${name}</option>`;
                        }
                    })
                    .catch(error => console.error('Error fetching cities:', error));
            } else {
                citySelect.innerHTML = '<option>Select City</option>';
            }
        });
    });
</script>



@endsection
@section('js')
<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.10/css/select2.min.css" rel="stylesheet" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.10/js/select2.min.js"></script>

<script>
jQuery(function() {
    jQuery(".sizes").select2();

});
jQuery(document).ready(function() {
    jQuery(".standardSelect").chosen({
        disable_search_threshold: 10,
        no_results_text: "Oops, nothing found!",
        width: "100%"
    });
})
</script>

@endsection