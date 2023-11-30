@extends('admin.layout')
@section('title', 'Categoies')
@section('content')
    <div class="row mb-4 mt-20">
        <div class="col-md-12">
            <div class="card card-small">
                <div class="card-header border-bottom">
                    <h6 class="m-0 pull-left">{{ request('type') == "whereNull" ? "Blog " : "" }}Categories</h6>
                    <button type="button" class="btn btn-success pull-right" data-toggle="modal"
                        data-target="#create_model"><i class="fa fa-plus"></i> Add New Category</button>
                </div>
                <div class="card-body" style="overflow-y: auto;">
                    <table class="display table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                @if (request('type') != "whereNull")
                                    <th>Type</th>
                                @endif
                                <th>Is Active</th>
                                <th class="text-center">Action</th>
                            </tr>
                        </thead>
                        @if ($data->count() > 0)
                            <tbody>
                                @foreach ($data as $d)
                                    <tr>
                                        <td>{{ $d->id }}</td>
                                        <td>{{ $d->name }}</td>
                                        @if (request('type') != "whereNull")
                                        <td>{{ $d->type }}</td>
                                        @endif
                                        {{-- <td><img style="width:40px; height:40px" src={{$d->image}}></td> --}}
                                        <td>{{ $d->status }}</td>
                                        <td>
                                            <div class="input-group-btn action_group">
                                                <li class="action_icon">
                                                    <button type="button" class="btn btn-info btn-block "
                                                        data-toggle="dropdown" aria-expanded="false"><i
                                                            class="fa fa-ellipsis-v" aria-hidden="true"
                                                            title="View"></i></button>
                                                    <ul class="dropdown-menu">
                                                        {{-- <li>
										        	<a href="javascript:;" data-toggle="modal" data-target="#update_model" class="update-obj"
										        		data-id="{{$d->id}}"
										        		data-image={{$d->image !== null ? $d->image : 'Empty'}}
										        	> Update Image</a>
										        </li> --}}
                                                        {{-- <li>
										        	<a href="javascript:;" data-toggle="modal" data-target="#edit_model" class="edit-obj"
										        		data-id="{{$d->id}}"
										        		data-name="{{$d->name}}"
										        	> Edit</a>
										        </li> --}}
                                                        <li>
                                                            @if ($d->status === App\Utils\AppConst::ACTIVE)
                                                                <a href="{{ route('admin.category.update_status', [App\Utils\AppConst::INACTIVE, $d->id]) }}"
                                                                    onclick="return confirm('Are you sure? you want to inactive category?')"><i
                                                                        class="material-icons">toggle_off</i> Inactive</a>
                                                            @else
                                                                <a href="{{ route('admin.category.update_status', [App\Utils\AppConst::ACTIVE, $d->id]) }}"
                                                                    onclick="return confirm('Are you sure? you want to activate category?')"><i
                                                                        class="material-icons">toggle_on</i> Active</a>
                                                            @endif

                                                        </li>
                                                        <li>
                                                            <a href="{{ route('admin.category.delete', $d) }}"
                                                                onclick="return confirm('Are you sure? you want to delete category?')"><i
                                                                    class="material-icons">delete</i> Delete</a>
                                                        </li>
                                                    </ul>
                                                </li>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        @endif
                    </table>
                </div>
            </div>
        </div>
    </div>


    <div class="modal fade" id="create_model" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form method="post" action="{{ route('admin.category.store') }}" id="create-category" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Add Category</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                aria-hidden="true">&times;</span></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-12 form-group">
                                <label>Name</label>
                                <input type="text" class="form-control" name="name" required="">
                            </div>
                            @if (request('type') != "whereNull")
                            <div class="col-md-12 form-group">
                                <label>Type</label>
                                <select name="type" required="" class="form-control">
                                    <option value="" disabled="" selected="">Please Select Type</option>
                                    <option value={{ App\Utils\ProductType::GROCERY }}>Grocery</option>
                                    <option value="{{ App\Utils\ProductType::FOOD }}">Food</option>
                                </select>
                            </div>
                            @endif
                            {{-- <div class="col-md-12 input-group mb-3">
							<div class="custom-file">
								<input type="file" class="custom-file-input" id="image" name="image" accept="image/png,image/gif,image/jpeg" aria-describedby="inputGroupFileAddon01" required="">
								<label class="custom-file-label" for="image">Choose file</label>
							</div>
						</div> --}}
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


    {{-- <div class="modal fade" id="edit_model" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
	<div class="modal-dialog" role="document">
	    <div class="modal-content">
	      	<form method="post" action="{{route('admin.services_create')}}" id="edit-form">
	      		@csrf
	      		<input type="hidden" name="id">
	      		<div class="modal-header">
			        <h5 class="modal-title" id="exampleModalLabel">Add Service</h5>
			        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
			    </div>
			    <div class="modal-body">
			        <div class="row">
			        	<div class="col-md-12 form-group">
			        		<label>Name</label>
			        		<input type="text" class="form-control" name="name" required="">
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
</div> --}}

    {{-- <div class="modal fade" id="update_model" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
	<div class="modal-dialog" role="document">
	    <div class="modal-content">
	      	<form method="post" action="{{route('admin.services_create')}}" id="update-form" enctype="multipart/form-data">
	      		@csrf
	      		<input type="hidden" name="id">
	      		<div class="modal-header">
			        <h5 class="modal-title" id="exampleModalLabel">Update Image</h5>
			        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
			    </div>
			    <div class="modal-body">
			        <div class="row">
						<div class="col-12 mb-3">
							<img id="serviceImage" src="" class="img-fluid">
						</div>
			        	<div class="col-md-12 input-group mb-3">
							<div class="custom-file">
								<input type="file" class="custom-file-input" id="image" name="image" accept="image/png,image/gif,image/jpeg" aria-describedby="inputGroupFileAddon01">
								<label class="custom-file-label" for="image">Update Image</label>
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
</div> --}}


    <script type="text/javascript">
        $('#create-category').validate({
            rules: {
                name: {
                    required: true,
                    maxlength: 50
                },
                type: {
                    required: true
                }
            },
            messages: {
                name: {
                    required: "Please enter name",
                    maxlength: "Name should be less than 50 characters"
                },
                type: {
                    required: "Please select type"
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
        })
        // $('.edit-obj').on('click', function(){
        // 	$('#edit-form input[name=name]').val($(this).data('name'));
        // 	$('#edit-form input[name=id]').val($(this).data('id'));
        // });

        // $('.update-obj').on('click', function(){
        // 	$('#serviceImage').attr("src", $(this).data('image'));
        // 	$('#update-form input[name=id]').val($(this).data('id'));
        // });

        // $('#image').change(function (e) {
        //     if (e.target.files.length) {
        //         $(this).next('.custom-file-label').html(e.target.files[0].name);
        //     }
        // });
    </script>
@endsection
