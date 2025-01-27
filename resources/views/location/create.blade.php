@extends('layouts.app')



@section('title', '| Users')



@section('sh-detail')

Create New

@endsection



@section('content')

<div class="card">

    <div class="card-header">

        <strong>Create City</strong>

    </div>

    <div class="card-body card-block">
        <form method="post" action="{{ route('location.store') }}" class="form-horizontal">
            @csrf

            <div class="row">

                <div class="col col-md-4">

                    <div class="form-group">

                        <label for="multiple-select" class=" form-control-label">Regions

                        </label>

                        <select class="form-control" name="region_id" id="country" required>

                            <option value="">Choose Region</option>

                            @foreach ($regions as $country)

                                <option value="{{$country->id}}">

                                    {{$country->name}}

                                </option>

                            @endforeach

                        </select>

                    </div>

                </div>

                <div class="col col-md-4">

                    <div class="form-group">
                        <label for="multiple-select" class=" form-control-label">State
                        </label>
                        <select class="form-control" name="state" id="state">
                        </select>

                    </div>

                </div>

                <div class="col col-md-4">

                    <div class=" form-group">

                        <label for="text-input" class=" form-control-label">City Name</label>

                        <input type="text" id="text-input" name="city" placeholder="City Name" class="form-control"
                            value="" tabindex="1" required>

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
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>

@endsection