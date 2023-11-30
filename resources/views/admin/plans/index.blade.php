@extends('admin.layout')
@section('title', 'Links')
@section('content')
<div class="row mb-4 mt-20">
    <div class="col-md-12">
	    <div class="card card-small">
	         <div class="card-header border-bottom">
	            <h6 class="m-0 pull-left">Plans</h6>
                <button id="newLink" type="button" class="btn btn-success pull-right"><i class="fa fa-plus"></i> Add new link</button>
	        </div>
	        <div class="card-body" style="overflow-y: auto;">
	        	<table class="display table table-striped table-hover">
				    <thead>
				      	<tr>
				        	<th>Id</th>
				        	<th>Title</th>
				        	<th>Name</th>
				        	<th>price</th>
				        	<th>leads</th>
				        	<th>Description</th>
				        	<th class="text-center">Action</th>
				      	</tr>
				    </thead>
				    <tbody>
				      	@foreach($data as $row)
				      		<tr>
					        	<td>{!! $row->id!!}</td>
					        	<td>{!! $row->title ?? "N/A" !!}</td>
					        	<td>{!! $row->stripe_name ?? "N/A" !!}</td>
					        	<td>{!! $row->price ?? "N/A" !!}</td>
					        	<td>{!! $row->credit ?? "N/A" !!}</td>
					        	<td>{!! $row->description ?? "N/A" !!}</td>
					        	<td>
					        		<div class="input-group-btn action_group">
									   <li class="action_icon">
									      	<button type="button" class="btn btn-info btn-block " data-toggle="dropdown" aria-expanded="false"><i class="fa fa-ellipsis-v" aria-hidden="true" title="View"></i></button>
										    <ul class="dropdown-menu">
										        <li>
										        	<a
                                                        href="javascript:;"
                                                        data-toggle="modal"
                                                        data-target="#edit_model"
                                                        class="edit"
                                                        data-id="{{ $row->id }}"
                                                        data-title="{{ $row->title }}"
                                                        data-stripe_name="{{ $row->stripe_name }}"
                                                        data-price="{!! $row->price !!}"
                                                        data-credit="{{ $row->credit }}"
                                                        data-threshold="{{ $row->threshold }}"
                                                        data-description="{{ $row->description }}"
                                                    >
                                                        <i class="material-icons">edit</i>  Edit
                                                    </a>
										        </li>
										        <li>
										        	<a href="{{route('admin.plan.delete', $row)}}">
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

<div class="modal" id="create_model" tabindex="-1">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 id="mod-title" class="modal-title">Create new Plan</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <form id="planForm" action="{{route('admin.plan.store')}}" method="post">
        <div class="modal-body">
                @csrf
                <div class="form-group">
                    <label for="title">Title</label>
                    <input type="text" name="title" id="title" class="form-control">
                </div>
                <div class="form-group">
                    <label for="stripe_name">Name</label>
                    <input type="text" name="stripe_name" id="stripe_name" class="form-control">
                </div>
                <div class="form-group">
                    <label for="price">Price</label>
                    <input type="number" name="price" id="price" min="1" max="1000" class="form-control">
                </div>
                <div class="form-group">
                    <label for="leads">Leads</label>
                    <input type="number" name="credit" min="1" max="1000" id="leads" class="form-control">
                </div>
                <div class="form-group">
                    <label for="threshold">Alert Threshold</label>
                    <input type="number" name="threshold" min="1" max="999" id="threshold" class="form-control">
                </div>
                <div class="form-group">
                    <label for="description">Description</label>
                    <textarea rows="5" name="description" id="description" class="form-control"></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="submit" id="mod-btn" class="btn btn-primary">Save changes</button>
            </div>
        </form>
      </div>
    </div>
</div>

<script type="text/javascript">

	$('#newLink').on('click', function(){
		$('#planForm').trigger('reset');
        $('#mod-title').html('Create new Plan');
        $('#mod-btn').html('Create');
		$('#create_model').modal('show');
	});

	$('.edit').on('click', function(){
		$('#create_model').modal('show');
        $('#mod-title').html('Update Plan');
        $('#mod-btn').html('Update');
		form = $('#planForm');
		form.append('<input type="hidden" name="_method" value="PUT">');
		form.attr('action', `{{route('admin.plan.update', "")}}/${$(this).data('id')}`);
		form.find('#title').val($(this).data('title')).trigger("change");
		form.find('#stripe_name').val($(this).data('stripe_name'));
		form.find('#price').val($(this).data('price'));
		form.find('#leads').val($(this).data('credit'));
		form.find('#threshold').val($(this).data('threshold'));
		form.find('#description').val($(this).data('description'));
	});

	$('#create_model').on('hidden.bs.modal', function () {
        $('#mod-title').html('Update Plan');
        $('#mod-btn').html('Update');
		$('#planForm').trigger('reset');
		$('#planForm').find('input[name="_method"]').remove();
		$('#planForm').attr('action', "{{route('admin.plan.update',"")}}");
	});

    $('#planForm').validate({
        rules: {
            title: {
                required: true,
                maxlength: 255
            },
            stripe_name: {
                required: true,
                maxlength: 250
            },
            price: {
                required: true,
                min: 1,
                max: 1000
            },
            leads: {
                required: true,
                min: 1,
                max: 1000
            },
            threshold: {
                min: 1,
                max: 1000
            },
            description: {
                maxlength: 1000
            }
        },
        messages: {
            title: {
                required: "Please enter title",
                maxlength: "Title should not be more than 255 characters"
            },
            stripe_name: {
                required: "Please enter name",
                maxlength: "Name should not be more than 250 characters"
            },
            price: {
                required: "Please enter price",
                min: "Price should be greater than 0",
                max: "Price should be less than 1000"
            },
            leads: {
                required: "Please enter leads",
                min: "Leads should be greater than 0",
                max: "Leads should be less than 1000"
            },
            threshold: {
                min: "Threshold should be greater than 0",
                max: "Description should not be more than 20 characters"
            },
            description: {
                maxlength: "Description should not be more than 1000 characters"
            }
        },
        errorPlacement: function (label, element) {
			label.addClass('invalid-feedback');
			label.insertAfter(element);
		},
		highlight: function (element, errorClass) {
			$(element).addClass('is-invalid').removeClass('is-valid');
		},
		unhighlight: function (element, errorClass) {
			$(element).removeClass('is-invalid').addClass('is-valid')
		},
		submitHandler: function (form){
			if(!$(form).valid()) return false;
            $(form).find('button[type="submit"]').attr('disabled', true).html('<i class="fa fa-spinner fa-pulse"></i> Please wait...');
            form.submit();
		}
    });
</script>

@endsection
