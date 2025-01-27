@extends('layouts.app')

@section('title', '| Users')
@section('sh-detail')
    Create New
@endsection


@section('content')
<style>
    .is-invalid {
        border-color: red;
    }
    .invalid-feedback {
        color: red;
    }
</style>
    <div class="card">
        <div class="card-header">
            <strong>Create Audit Agency</strong>
        </div>

        <div class="card-body card-block">
            <form method="post" action="{{ route('audit_agency.store') }}" class="form-horizontal">
                @csrf

                <div class="row form-group">
                    <div class="col col-md-6">
                        <label for="text-input" class="form-control-label font-weight-bold">Name</label>
                        <input type="text" id="text-input" name="name" placeholder="Audit Agency Name" class="form-control" value="{{ old('name') }}" required>
                        @error('name')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="col col-md-6">
                        <label for="email-input" class="form-control-label font-weight-bold">Email</label>
                        <input type="email" id="email-input" value="{{ old('email') }}" name="email" placeholder="Enter Email" class="form-control @error('email') is-invalid @enderror" required>
                        @error('email')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <div class="row form-group">
                    <div class="col col-md-6">
                        <label for="mobile-input" class="form-control-label font-weight-bold">Contact</label>
                        <input type="text" id="mobile-input" name="mobile" placeholder="Enter Mobile" class="form-control @error('mobile') is-invalid @enderror" value="{{ old('mobile') }}" required>
                        @error('mobile')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="col col-md-6">
                        <label for="agency_admin-input" class="form-control-label font-weight-bold">Admin Name</label>
                        <input type="text" id="agency_admin-input" name="agency_admin" placeholder="Agency Admin Name" class="form-control" value="{{ old('agency_admin') }}">
                        @error('agency_admin')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <div class="row form-group">
                    <div class="col col-md-6">
                        <label for="agency_admin_email_one-input" class="form-control-label font-weight-bold">Admin Email - First</label>
                        <input type="text" id="agency_admin_email_one-input" name="agency_admin_email_one" placeholder="Agency Admin Email - First" class="form-control" value="{{ old('agency_admin_email_one') }}">
                        @error('agency_admin_email_one')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="col col-md-6">
                        <label for="agency_admin_email_two-input" class="form-control-label font-weight-bold">Admin Email - Second (If any)</label>
                        <input type="text" id="agency_admin_email_two-input" name="agency_admin_email_two" placeholder="Agency Admin Email - Second" class="form-control" value="{{ old('agency_admin_email_two') }}">
                        @error('agency_admin_email_two')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <div class="col col-md-6" style="display:none;">
                    <label for="multiple-select" class="form-control-label">Select Role</label>
                    <select name="role[]" id="multiple-select" class="form-control">
                        @foreach($roles as $k => $v)
                            <option value="{{ $v->id }}" {{ $v->name === 'Admin' ? 'selected' : '' }}>{{ $v->name }}</option>
                        @endforeach
                    </select>
                    @error('role')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                    <input type="hidden" name="default_role" value="Admin">
                </div>

                <div class="col col-md-6" style="display:none;">
                    <label for="check-input" class="form-control-label">Password Type</label>
                    <input type="hidden" id="check-input2" name="auto" id="manual" value="manual" checked>
                    <span>Manual</span>
                </div>

                <div class="row form-group">
                    <div class="col col-md-6">
                        <label for="password-input" class="form-control-label font-weight-bold">Password</label>
                        <input type="password" id="password-input" name="password" placeholder="Enter Password" class="form-control">
                        @error('password')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="col col-md-6">
                        <label for="password_confirmation-input" class="form-control-label font-weight-bold">Confirm Password</label>
                        <input type="password" id="password_confirmation-input" name="password_confirmation" placeholder="Confirm Password" class="form-control">
                        @error('password_confirmation')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
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
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var sizes = document.querySelectorAll('.sizes');
            if (sizes.length > 0) {
                sizes.forEach(function(element) {
                    $(element).select2(); 
                });
            }

            var passwordDiv = document.getElementById('passwordDiv');
            var manualRadio = document.getElementById('manual');
            var automaticRadio = document.getElementById('automatic');

            if (manualRadio) {
                manualRadio.addEventListener('click', function() {
                    if (passwordDiv) {
                        passwordDiv.style.display = 'block';
                    }
                });
            }

            if (automaticRadio) {
                automaticRadio.addEventListener('click', function() {
                    if (passwordDiv) {
                        passwordDiv.style.display = 'none';
                    }
                });
            }
        });
    </script>
@endsection

@section('js')
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.10/css/select2.min.css" rel="stylesheet"/>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.10/js/select2.min.js"></script>
@endsection
