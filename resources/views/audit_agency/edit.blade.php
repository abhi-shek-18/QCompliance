@extends('layouts.app')

@section('title', '| Audit Agency Edit')

@section('sh-detail')
    Edit New
@endsection

<style>
    .is-invalid {
        border-color: red;
    }
    .invalid-feedback {
        color: red;
    }
</style>
@section('content')
<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                <strong>Edit Audit Agency</strong> form
            </div>
            <div class="card-body card-block">
                <form method="put" action="{{ route('audit_agency.update',['audit_agency' => $data->id]) }}" class="kt-form">
                    @csrf
                    @method('PUT')

                    <div class="row form-group">
                        <div class="col col-md-6">
                            <label for="text-input" class="form-control-label font-weight-bold">Name</label>
                            <input type="text" id="text-input" name="name" placeholder="Agency Name" class="form-control" value="{{ old('name', $data->name) }}" required>
                            @error('name')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                        <div class="col col-md-6">
                            <label for="email-input" class="form-control-label font-weight-bold">Email</label>
                            <input type="email" id="email-input" value="{{ old('email', $data->email) }}" name="email" placeholder="Enter Email" class="form-control" required>
                            @error('email')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>

                    <div class="row form-group">
                        <div class="col col-md-6">
                            <label for="mobile-input" class="form-control-label font-weight-bold">Contact</label>
                            <input type="text" id="mobile-input" name="mobile" placeholder="Enter Mobile" class="form-control" value="{{ old('mobile', $data->mobile) }}" required>
                            @error('mobile')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                        <div class="col col-md-6">
                            <label for="agency_admin-input" class="form-control-label font-weight-bold">Admin Name</label>
                            <input type="text" id="agency_admin-input" name="agency_admin" placeholder="Agency Admin Name" class="form-control" value="{{ old('agency_admin', $data->agency_admin) }}">
                            @error('agency_admin')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>

                    <div class="row form-group">
                        <div class="col col-md-6">
                            <label for="agency_admin_email_one-input" class="form-control-label font-weight-bold">Admin Email - First</label>
                            <input type="text" id="agency_admin_email_one-input" name="agency_admin_email_one" placeholder="Agency Admin Email - First" class="form-control" value="{{ old('agency_admin_email_one', $data->agency_admin_email_one) }}">
                            @error('agency_admin_email_one')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                        <div class="col col-md-6">
                            <label for="agency_admin_email_two-input" class="form-control-label font-weight-bold">Admin Email - Second (If any)</label>
                            <input type="text" id="agency_admin_email_two-input" name="agency_admin_email_two" placeholder="Agency Admin Email - Second" class="form-control" value="{{ old('agency_admin_email_two', $data->agency_admin_email_two) }}">
                            @error('agency_admin_email_two')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>

                    <input type="hidden" name="old_password" value="{{ $data->password }}">

                    <div class="row form-group">
                        <div class="col col-md-6">
                            <label for="password-input" class="form-control-label font-weight-bold">New Password (Leave blank to keep current)</label>
                            <input type="password" id="password-input" name="password" placeholder="New Password (Min 6 Digits)" class="form-control">
                            <input type="checkbox" id="show-password"> Show New Password
                            @error('password')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
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
<script>
    document.getElementById('show-password').addEventListener('change', function() {
        const newPasswordInput = document.getElementById('password-input');
        newPasswordInput.type = this.checked ? 'text' : 'password';
    });
</script>

@endsection

@section('js')
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.10/css/select2.min.css" rel="stylesheet"/>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.10/js/select2.min.js"></script>
@endsection
