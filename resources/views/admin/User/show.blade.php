@extends('layouts.app')

@section('content')
<div class="container">
    <h1>User Details</h1>
<hr>
    <br>

    <table class="table table-bordered">
    <thead>
        <tr>
            <th>Field</th>
            <th>Details</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td><strong>Name:</strong></td>
            <td>{{ $data->name }}</td>
        </tr>
        <tr>
            <td><strong>Email:</strong></td>
            <td>{{ $data->email }}</td>
        </tr>
        <tr>
            <td><strong>Mobile:</strong></td>
            <td>{{ $data->mobile }}</td>
        </tr>
        <tr>
            <td><strong>Role:</strong></td>
            <td>
                @foreach ($rdata as $roles)
                    {{ $roles }}{{ !$loop->last ? ', ' : '' }}
                @endforeach
            </td>
        </tr>
    </tbody>
</table>


    <a href="{{ route('User.index') }}" class="btn btn-secondary">Back to List</a>
</div>
@endsection
