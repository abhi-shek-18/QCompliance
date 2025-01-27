@extends('layouts.app')

@section('title', '| Users')

@section('sh-detail')
Create New
@endsection

@section('content')
<style>
    .row.form-group {
        margin-bottom: 20px;
    }
    .is-invalid {
        border-color: red;
    }
    .invalid-feedback {
        color: red;
    }
</style>

<div class="card">
    <div class="card-header">
        <strong>Create User</strong>
    </div>

    <div class="card-body card-block">
     
        <form method="post" action="{{ route('User.store') }}" class="form-horizontal">
            @csrf

            <div class="row form-group" style="margin-bottom: 20px;">
                <div class="col col-md-3">
                    <label for="text-input" class="form-control-label">Name</label>
                </div>
                <div class="col-12 col-md-9">
                    <input type="text" id="text-input" name="name" placeholder="User Name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" required>
                    @error('name')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <div class="row form-group" style="margin-bottom: 20px;">
                <div class="col col-md-3">
                    <label for="email-input" class="form-control-label">Email</label>
                </div>
                <div class="col-12 col-md-9">
                    <input type="email" id="email-input" value="{{ old('email') }}" name="email" placeholder="Enter Email" class="form-control @error('email') is-invalid @enderror" required>
                    @error('email')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <div class="row form-group" style="margin-bottom: 20px;">
                <div class="col col-md-3">
                    <label for="mobile-input" class="form-control-label">Mobile</label>
                </div>
                <div class="col-12 col-md-9">
                    <input type="text" id="mobile-input" name="mobile" placeholder="Enter Mobile" class="form-control @error('mobile') is-invalid @enderror" value="{{ old('mobile') }}" required>
                    @error('mobile')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <div class="row form-group" style="margin-bottom: 20px;">
                <div class="col col-md-3">
                    <label for="password-type" class="form-control-label">Password Type</label>
                </div>
                <div class="col-12 col-md-9">
                    <input type="radio" id="automatic" name="auto" value="automatic" {{ old('auto') == 'automatic' ? 'checked' : '' }}>
                    <label for="automatic">Automatic</label>
                    <input type="radio" id="manual" name="auto" value="manual" {{ old('auto') == 'manual' ? 'checked' : '' }}>
                    <label for="manual">Manual</label>
                </div>
            </div>

            <div id="passwordDiv" style="display: {{ old('auto') == 'manual' ? 'block' : 'none' }};">
                <div class="row form-group" style="margin-bottom: 20px;">
                    <div class="col col-md-3">
                        <label for="password" class="form-control-label">Password</label>
                    </div>
                    <div class="col-12 col-md-9">
                        <input type="password" id="password" name="password" placeholder="Enter password" class="form-control @error('password') is-invalid @enderror" required>
                        @error('password')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <div class="row form-group" style="margin-bottom: 20px;">
                    <div class="col col-md-3">
                        <label for="password_confirmation" class="form-control-label">Confirm Password</label>
                    </div>
                    <div class="col-12 col-md-9">
                        <input type="password" id="password_confirmation" name="password_confirmation" placeholder="Confirm Password" class="form-control @error('password_confirmation') is-invalid @enderror" required>
                        @error('password_confirmation')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="row form-group" style="margin-bottom: 20px;">
                <div class="col col-md-3">
                    <label for="multiple-select" class="form-control-label">Multiple select</label>
                </div>
                <div class="col col-md-9">
                    <select name="role[]" id="multiple-select" class="form-control @error('role') is-invalid @enderror" multiple required>
                        @foreach ($roles as $id => $name)
                            <option value="{{ $id }}" {{ in_array($id, old('role', [])) ? 'selected' : '' }}>{{ $name }}</option>
                        @endforeach
                    </select>
                    @error('role')
                        <span class="invalid-feedback">{{ $message }}</span>
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
        // Select2 initialization for the .sizes element
        var sizes = document.querySelectorAll('.sizes');
        if (sizes.length > 0) {
            sizes.forEach(function(element) {
                // Assuming you have Select2 implemented, or you can use a basic custom dropdown
                $(element).select2();  // This line still requires jQuery or you can implement a custom solution
            });
        }

        // Toggle the visibility of password div
        var passwordDiv = document.getElementById('passwordDiv');
        var manualRadio = document.getElementById('manual');
        var automaticRadio = document.getElementById('automatic');

        if (manualRadio) {
            manualRadio.addEventListener('click', function() {
                if (passwordDiv) {
                    passwordDiv.style.display = 'block'; // Show password div
                }
            });
        }

        if (automaticRadio) {
            automaticRadio.addEventListener('click', function() {
                if (passwordDiv) {
                    passwordDiv.style.display = 'none'; // Hide password div
                }
            });
        }
    });
</script>


@endsection

@section('js')

<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.10/css/select2.min.css" rel="stylesheet" />

<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.10/js/select2.min.js"></script>

@endsection
