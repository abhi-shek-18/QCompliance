@extends('layouts.app')

@section('title', '| Audit Cycle')

@section('sh-detail')
Create New
@endsection

@section('content')
<div class="card">
    <div class="card-header">
        <strong>Update Audit Cycle</strong>
    </div>

    <div class="card-body card-block">
        <form method="put" action="{{ route('edit-audit-cycle', ['id' => $cycle->id]) }}" class="kt-form">
            @csrf
            @method('PUT')

            <div class="row">
                <div class="col col-md-4">
                    <div class="form-group">
                        <label for="cycle_name">Cycle Name:</label>
                        <input type="text" id="cycle_name" name="cycle_name" value="{{ $cycle->name }}">
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
