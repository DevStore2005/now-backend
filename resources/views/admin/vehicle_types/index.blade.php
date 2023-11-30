@extends('admin.layout')
@section('title', 'Vehicle Types')
@section('content')
<div class="row mb-4 mt-20">
    <div class="col-md-12">
	    <div class="card card-small">
	         <div class="card-header border-bottom">
	            <h6 class="m-0 ml-2 pull-left">Vehicle Types</h6>
	            <button type="button" class="btn btn-success pull-right" data-toggle="modal" data-target="#create_model"><i class="fa fa-plus"></i> Add Vehicle Type</button>
	        </div>
	        <div class="card-body" style="overflow-y: auto;">
	        	<table class="display table table-striped table-hover">
				    <thead>
				      	<tr>
				        	<th>ID</th>
				        	<th>Title</th>
				        	<th>Image</th>
				        	{{-- <th>Type</th> --}}
				        	<th class="text-center">Action</th>
				      	</tr>
				    </thead>
				    <tbody>
				      	@foreach($data as $d)
				      		<tr>
					        	<td>{{$d->id}}</td>
					        	<td>{{$d->title}}</td>
					        	<td>
									<a href={{$d->image}} data-fancybox="images" data-caption="{{ $d->title }}">
										<img style="width:40px; height:40px" src={{$d->image}} alt="" />
									</a>
								</td>
					        	{{-- <td>{{$d->type ? $d->type : "//"}}</td> --}}
					        	<td>
					        		<div class="input-group-btn action_group">
									   <li class="action_icon">
									      	<button type="button" class="btn btn-info btn-block " data-toggle="dropdown" aria-expanded="false"><i class="fa fa-ellipsis-v" aria-hidden="true" title="View"></i></button>
										    <ul class="dropdown-menu">
										        {{-- <li> --}}
										        	{{-- <a href="{{route('admin.profiles.profile', $d)}}">  --}}
									        			{{-- <i class="material-icons">visibility</i>  View --}}
									        		{{-- </a>  --}}
										        {{-- </li> --}}
												<li>
										        	<a href="javascript:;" data-backdrop="static" data-keyboard="false" data-toggle="modal" data-target="#update_model" class="edit-obj"
										        		data-id="{{$d->id}}"
										        		data-title="{{$d->title}}"
										        		data-image="{{$d->image}}"
														data-route="{{route('admin.vehicle.type.update', $d->id)}}"
										        	><i class="material-icons">edit</i>  Edit</a> 
										        </li>
										        <li>
										        	<a href="{{route('admin.vehicle.type.destroy', $d->id)}}"
										        		onclick="return confirm('Are you sure you want to delete this vehicle type?');"
									        		> 
									        			<i class="material-icons">delete</i>  Delete
									        		</a> 
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
	      	<form method="post" action="{{route('admin.vehicle.type.store')}}" enctype="multipart/form-data" id="create-cehicle-type">
	      		@csrf
	      		<div class="modal-header">
			        <h5 class="modal-title" id="exampleModalLabel">Add Vehicle Type</h5>
			        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
			    </div>
			    <div class="modal-body">
			        <div class="row">
			        	<div class="col-md-12 form-group">
			        		<label>Title</label>
			        		<input type="text" class="form-control" placeholder="Title" name="title" required="">
			        	</div>
						<div class="col-md-12 input-group">
							<div class="custom-file">
								<input type="file" class="custom-file-input" id="image1" name="image" accept="image/png,image/gif,image/jpeg" aria-describedby="inputGroupFileAddon01" required="">
								<label class="custom-file-label" for="image1">Choose file</label>
							</div>
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
	      	<form method="post" action="" id="edit-from" enctype="multipart/form-data">
	      		@csrf
				{{ method_field('PATCH') }}
	      		<div class="modal-header">
			        <h5 class="modal-title" id="exampleModalLabel">Add Vehicle Type</h5>
			        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
			    </div>
			    <div class="modal-body">
			        <div class="row">
			        	<div class="col-md-12 form-group">
			        		<label>Title</label>
			        		<input type="text" class="form-control" name="title" required="">
			        	</div>
						<input type="hidden" name="isChange" id="isChange" value="0">
						<div class="col-md-12 input-group">
							<div class="custom-file">
								<input type="file" class="image custom-file-input" id="image" name="image" accept="image/png,image/gif,image/jpeg" aria-describedby="inputGroupFileAddon01">
								<label class="custom-file-label" for="image">Change image</label>
							</div>
						</div>
						<div class="col-12 my-3">
							<img src="" id="previewImage" class="img-fluid" style="width: 100%; height: 200px">
						</div>
			        </div>
			    </div>
			    <div class="modal-footer">
			        <button type="button" class="btn btn-secondary" data-dismiss="modal" id="handleCloseClick">Close</button>
			        <button type="submit" class="btn btn-primary">Submit</button>
			    </div>
	      	</form>
	    </div>
	</div>
</div>

<script type="text/javascript">

	let editForm = $('#edit-from');
	let createForm = $('#create-cehicle-type');

	const initValidate = {
		rules: {
			title: {
				required: true,
				minlength: 3,
				maxlength: 100
			},
			image: {
				required: {
					depends: function(e) {
						return $(e).closest('form').find('#isChange').val() != undefined ? false : true;
					}
				},
				accept: "image/png, image/jpeg, image/jpg"
			},
		},
		messages: {
			title: {
				title: "Please enter title",
				minlength: "Title must be at least 3 characters long",
				maxlength: "Title must be at most 100 characters long"
			},
			image: {
				required: "Please select image",
				accept: "Please select image in png, jpeg, jpg, jpg format"
			}
		},
		errorElement: 'div',
		errorPlacement: function(error, element){
			error.addClass('invalid-feedback');
			if(element.hasClass('custom-file-input')) {
				error.addClass('ml-3');
				error.insertAfter(element.closest('.input-group'));
			} else {
				error.insertAfter(element);
			}
		},
		highlight: function(element){
			$(element).addClass('is-invalid').removeClass('is-valid');
		},
		unhighlight: function(element){
			$(element).addClass('is-valid').removeClass('is-invalid');
		},
		submitHandler: function(form){
			if(!$(form).valid()) return false;
			$(form).find('button[type="submit"]').html('<i class="fa fa-spinner fa-pulse"></i> Processing...').attr('disabled', true);
			form.submit();
		}
	};

	editForm.validate(initValidate);
	createForm.validate(initValidate);

	$('#handleCloseClick').click(function(){
		$('#isChange').val(0);
	});

	$('.edit-obj').on('click', function(){
		editForm.attr('action', $(this).data('route'));
		$('#edit-from input[name=title]').val($(this).data('title'));
		$('#edit-from img[class=img-fluid]').attr('src',$(this).data('image'));
	});
	
	const imageHandleChange = function (e) { 
		if (e.target.files.length) {
			$(this).next('.custom-file-label').html(e.target.files[0].name);
			$('#previewImage').attr('src', URL.createObjectURL(e.target.files[0]));
			$('#edit-from input[name=isChange]').val(1);
        }
	};
	$('#image1').change(imageHandleChange);
	$('#image').change(imageHandleChange);
</script>
@endsection