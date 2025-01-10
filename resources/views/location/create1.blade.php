
@extends('layouts.app')



@section('title', '| Users')



@section('sh-detail')

    Create New

@endsection



@section('content')

    <div class="card">

        <div class="card-header">

            <strong>Create State</strong> 

        </div>

        <div class="card-body card-block">

        <form method="post" action="{{ route( 'location.update') }}" class="form-horizontal">
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

                <div class=" form-group">

                        <label for="text-input" class=" form-control-label">State Name</label>

                        <input type="text" id="text-input" name="state" placeholder="State Name" class="form-control" value="" tabindex="1" required>

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





@endsection

@section('js')

    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.10/css/select2.min.css" rel="stylesheet"/>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.10/js/select2.min.js"></script>

    <script>

     

        jQuery('#country').change(function () {

            var cid = jQuery(this).val();

            if (cid) {

                jQuery.ajax({

                    type: "get",

                    url: " {{url('/getStates')}}/" + cid,

                    success: function (res) {

                        if (res) {

                            jQuery("#state").empty();

                            jQuery("#city").empty();

                            jQuery("#state").append('<option>Select State</option>');

                            jQuery.each(res, function (key, value) {

                                jQuery("#state").append('<option value="' + key + '">' + value + '</option>');

                            });

                        }

                    }



                });

            }

        });

        jQuery('#state').change(function () {

            var sid = jQuery(this).val();

            if (sid) {

                jQuery.ajax({

                    type: "get",

                    url: "{{url('/getCities')}}/" + sid,

                    success: function (res) {

                        if (res) {

                            jQuery("#city").empty();

                            jQuery("#city").append('<option>Select City</option>');

                            jQuery.each(res, function (key, value) {

                                jQuery("#city").append('<option value="' + key + '">' + value + '</option>');

                            });

                        }

                    }



                });

            }

        });

    </script>

@endsection