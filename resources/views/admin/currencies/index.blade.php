@extends('admin.layout')
@section('title', 'Services')
@section('content')
<div class="row mb-4 mt-20">
    <div class="col-md-12">
	    <div class="card card-small">
	         <div class="card-header border-bottom">
	            <h6 class="m-0 pull-left">Currencies</h6>
	            <button type="button" class="btn btn-success pull-right" data-toggle="modal" data-target="#create_model"><i class="fa fa-plus"></i> Add new currency</button>
	        </div>
	        <div class="card-body" style="overflow-y: auto;">
	        	<table class="display table table-striped table-hover">
				    <thead>
				      	<tr>
				        	<th>ID</th>
				        	<th>Country and currency</th>
				        	<th>Currency Code</th>
				        	<th class="text-center">Action</th>
				      	</tr>
				    </thead>
				    <tbody>
				      	@foreach($data as $d)
				      		<tr>
					        	<td>{{$d->id}}</td>
					        	<td>{{$d->country_currency}}</td>
					        	<td>{{$d->code}}</td>
								<td>
									<div class="input-group-btn action_group">
									   <li class="action_icon">
									      	<button type="button" class="btn btn-info btn-block " data-toggle="dropdown" aria-expanded="false"><i class="fa fa-ellipsis-v" aria-hidden="true" title="View"></i></button>
										    <ul class="dropdown-menu">
										        <li>
                                                    <a href="javascript:;" data-toggle="modal" data-target="#update_model" class="btn btn-primary btn-sm edit-obj"
                                                        data-id="{{$d->id}}"
                                                        data-country_currency="{{$d->country_currency}}"
                                                        data-code="{{$d->code}}"
                                                    > <i class="material-icons">edit</i> Edit</a>
										        </li>
										        <li>
													<a href="{{route('admin.currency.delete', $d)}}" onclick="return confirm('Are you sure! you want to delete this?')"><i class="material-icons">delete</i> Delete</a></a> 
												</li>
											</ul>
										</li>
									</div>
								</td>
					      	</tr>
				      	@endforeach
				    </tbody>
			    </table>
	        </div>
  		</div>
    </div>
</div>


<div class="modal fade" id="create_model" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
	<div class="modal-dialog" role="document">
	    <div class="modal-content">
	      	<form method="post" action={{route('admin.currency.store')}} id="create-currency" enctype=" multipart/form-data">
	      		@csrf
	      		<div class="modal-header">
			        <h5 class="modal-title" id="exampleModalLabel">Add Service</h5>
			        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
			    </div>
			    <div class="modal-body">
			        <div class="row">
			        	<div class="col-md-12 form-group">
			        		<label>Country Currency</label>
			        		<input type="text" class="form-control" name="country_currency" required="">
			        	</div>
			        	<div class="col-md-12 form-group">
			        		<label>Currency Code</label>
			        		<input type="text" class="form-control" name="code" required="">
			        	</div>
			        </div>
			    </div>
			    <div class="modal-footer">
			        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
			        <button type="submit" class="btn btn-primary">Submit</button>
			    </div>
	      	</form>
	    </div>
	</div>
</div>

<div class="modal fade" id="update_model" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
	<div class="modal-dialog" role="document">
	    <div class="modal-content">
	      	<form method="post" action={{route('admin.currency.update', ':id')}} id="update-currency" enctype="multipart/form-data">
	      		@csrf
				@method('put')
	      		<div class="modal-header">
			        <h5 class="modal-title" id="exampleModalLabel">Add Service</h5>
			        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
			    </div>
			    <div class="modal-body">
			        <div class="row">
			        	<div class="col-md-12 form-group">
			        		<label>Country Currency</label>
			        		<input type="text" class="form-control" name="country_currency" required="">
			        	</div>
			        	<div class="col-md-12 form-group">
			        		<label>Currency Code</label>
			        		<input type="text" class="form-control" name="code" required="">
			        	</div>
			        </div>
			    </div>
			    <div class="modal-footer">
			        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
			        <button type="submit" class="btn btn-primary">Submit</button>
			    </div>
	      	</form>
	    </div>
	</div>
</div>

<script type="text/javascript">
	$('#create-currency').validate({
            rules: {
                country_currency: {
                    required: true,
                    maxlength: 50
                },
                code: {
                    required: true,
					maxlength: 50
                }
            },
            messages: {
                country_currency: {
                    required: "Please enter name",
                    maxlength: "Name should be less than 50 characters"
                },
				code: {
					required: "Please enter code",
					maxlength: "Code should be less than 50 characters"
				}
            },
            errorElement: 'div',
            errorPlacement: function (error, element) {
                error.addClass('invalid-feedback');
                error.insertAfter(element);
            },
            highlight: function (element) {
                $(element).addClass('is-invalid').removeClass('is-valid');
            },
            unhighlight: function (element) {
                $(element).addClass('is-valid').removeClass('is-invalid')
            },
            submitHandler: function (form){
                if(!$(form).valid()) return false;
                $(form).find('button[type="submit"]').attr('disabled', true).html('<i class="fa fa-spinner fa-pulse"></i> processing...')
                form.submit()
            }
        });
	$('#update-currency').validate({
            rules: {
                country_currency: {
                    required: true,
                    maxlength: 50
                },
                code: {
                    required: true,
					maxlength: 50
                }
            },
            messages: {
                country_currency: {
                    required: "Please enter name",
                    maxlength: "Name should be less than 50 characters"
                },
				code: {
					required: "Please enter code",
					maxlength: "Code should be less than 50 characters"
				}
            },
            errorElement: 'div',
            errorPlacement: function (error, element) {
                error.addClass('invalid-feedback');
                error.insertAfter(element);
            },
            highlight: function (element) {
                $(element).addClass('is-invalid').removeClass('is-valid');
            },
            unhighlight: function (element) {
                $(element).addClass('is-valid').removeClass('is-invalid')
            },
            submitHandler: function (form){
                if(!$(form).valid()) return false;
                $(form).find('button[type="submit"]').attr('disabled', true).html('<i class="fa fa-spinner fa-pulse"></i> processing...')
                form.submit()
            }
        });

		$('.edit-obj').on('click', function () {
            var id = $(this).data('id');
            var country_currency = $(this).data('country_currency');
            var code = $(this).data('code');
            $('#update_model').find('form').attr('action', $('#update_model').find('form').attr('action').replace(':id', id));
			$('#update_model').find('input[name="country_currency"]').val(country_currency);
			$('#update_model').find('input[name="code"]').val(code);
		})
</script>
@endsection