@extends('layouts.app')



@section('title', '| Audit Cycle')



@section('sh-detail')

    Create New

@endsection



@section('content')

    <div class="card">

        <div class="card-header">

            <strong>Create Audit Cycle</strong> 

        </div>

        <div class="card-body card-block">
        <form method="post" action="{{ route('createCycle') }}" class="form-horizontal">
                @csrf

            <div class="row">

            

            <div class="col col-md-4">

                <div class=" form-group">

                        <label for="text-input" class=" form-control-label">Cycle Name</label>

                        <input type="text" id="text-input" name="cycle_name" placeholder="cycle_name" class="form-control" value="" tabindex="1" required>

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
