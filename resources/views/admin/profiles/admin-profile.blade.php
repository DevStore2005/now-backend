@extends('admin.layout')
@section('title', 'Profile')
@section('content')
    <div class="row mb-4 mt-20">
        <div class="col-md-12">
            <div class="card card-small">
                <div class="card-header border-bottom">
                    <h6 class="m-0 pull-left ml-2"> Profile </h6>
                </div>
                <div class="card-body">
                    <form class="row" action="{!! route('admin.profile') !!}" method="POST">
                        @csrf
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="name">First Name</label>
                                <input 
                                    type="text"
                                    class="form-control"
                                    name="first_name"
                                    id="first_name"
                                    placeholder="First Name"
                                    value="{{ $admin->first_name }}"
                                    required
                                />
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="name">Last Name</label>
                                <input 
                                    type="text"
                                    class="form-control"
                                    name="last_name"
                                    id="last_name"
                                    placeholder="Last Name"
                                    value="{{ $admin->last_name }}"
                                    required
                                />
                            </div>
                        </div>
                        <div class="col-md-12">
                            <button type="submit" class="btn btn-primary float-right">Update Profile</button>
                        </div>
                    </form>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="name">Email</label>
                                <input 
                                    type="email"
                                    class="form-control"
                                    name="email"
                                    id="email"
                                    placeholder="email" 
                                    value="{{ $admin->email }}"
                                    disabled
                                    required
                                />
                            </div>
                                <button disabled type="button" class="btn btn-secondary float-right" data-toggle="modal" data-target="#email-modal">Change Email</button>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="name">Phone</label>
                                <input 
                                    type="tel"
                                    class="form-control"
                                    name="phone"
                                    id="phone"
                                    placeholder="Phone" 
                                    value="{{ $admin->phone }}"
                                    required
                                    disabled
                                />
                            </div>
                                <button disabled type="button" class="btn btn-secondary float-right">Change Phone</button>
                        </div>
                    </div>
                    <h4>Change you password</h4>
                    <form id="changePassword" class="row" method="POST" action="{!! route('admin.changePassword') !!}">
                        @csrf
                        <div class="col-md-6 mx-auto">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="name">Old Password</label>
                                    <input 
                                        type="password"
                                        class="form-control @error('old_password') is-invalid @enderror"
                                        name="old_password"
                                        id="old_password"
                                        placeholder="Old Password" 
                                        required
                                    />
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="name">New Password</label>
                                    <input 
                                        type="password"
                                        class="form-control @error('password') is-invalid @enderror"
                                        name="password"
                                        id="password"
                                        placeholder="New Password" 
                                        required
                                    />
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="name">Confirm Password</label>
                                    <input 
                                        type="password"
                                        class="form-control @error('email') is-invalid @enderror"
                                        name="password_confirmation"
                                        id="password_confirmation"
                                        placeholder="Confirm Password" 
                                        required
                                    />
                                </div>
                            </div>
                            <button type="submit" class="btn btn-primary float-right">Change Password</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="email-modal" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <form id="changeEmail" method="POST" action="{!! "route('admin.changeEmail')" !!}">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-body">
                        @csrf
                        <div class="form-group">
                            <label for="name">New Email</label>
                            <input 
                                type="email"
                                class="form-control"
                                name="email"
                                id="email"
                                placeholder="New Email" 
                                required
                            />
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-primary">Change</button>
                    </div>
                </div>
            </div>
        </form>
    </div>
<script type="text/javascript">
    $('#changePassword').validate({
        rules: {
            old_password: {
                required: true,
                minlength: 8
            },
            password: {
                required: true,
                minlength: 8,
                pattern: /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[$@$!%*?&])[A-Za-z\d$@$!%*?&]{8,}/
            },
            password_confirmation: {
                required: true,
                minlength: 8,
                equalTo: "#password"
            }
        },
        messages: {
            old_password: {
                required: "please enter your old password",
                minlength: "password must be at least 8 characters long"
            },
            password: {
                required: "please enter your new password",
                minlength: "password must be at least 8 characters long",
                pattern: "password must contain at least one lowercase letter, one uppercase letter, one number and one special character"
            },
            password_confirmation: {
                required: "please confirm your new password",
                minlength: "password must be at least 8 characters long",
                equalTo: "password does not match"
            }
        },
        errorElement: "span",
        errorPlacement: function (elm, element, errorClass, validClass) {
			elm.addClass('invalid-feedback');
			elm.insertAfter(element);
		},
		highlight: function (element, errorClass) {
			$(element).addClass('is-invalid').removeClass('is-valid');
		},
		unhighlight: function (element, errorClass) {
			$(element).removeClass('is-invalid').addClass('is-valid');
		},
        submitHandler: function (form){
            if(!$(form).valid()) return false;
            $(form).find('button[type="submit"]').attr('disabled', true).html('<i class="fa fa-spinner fa-pulse"></i> Please wait...');
            form.submit();
        }
    });
    $('#changeEmail').validate({
        rules: {
            email: {
                required: true,
                email: true
            }
        },
        messages: {
            email: {
                required: "please enter your new email",
                email: "please enter a valid email"
            }
        },
        errorElement: "span",
        errorPlacement: function (elm, element, errorClass, validClass) {
			elm.addClass('invalid-feedback');
			elm.insertAfter(element);
		},
		highlight: function (element, errorClass) {
			$(element).addClass('is-invalid').removeClass('is-valid');
		},
		unhighlight: function (element, errorClass) {
			$(element).removeClass('is-invalid').addClass('is-valid');
		},
        submitHandler: function (form){
            if(!$(form).valid()) return false;
            $(form).find('button[type="submit"]').attr('disabled', true).html('<i class="fa fa-spinner fa-pulse"></i> Please wait...');
            form.submit();
        }
    });
</script>
@endsection