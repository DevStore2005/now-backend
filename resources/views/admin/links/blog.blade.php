@extends('admin.layout')
@section('title', 'Links')
@section('content')
<div class="row mb-4 mt-20">
    <div class="col-md-12">
	    <div class="card card-small">
	         <div class="card-header border-bottom">
	            <h6 class="m-0 pull-left">Blog links</h6>
                <button id="newLink" type="button" class="btn btn-success pull-right"><i class="fa fa-plus"></i> Add new link</button>
	        </div>
	        <div class="card-body" style="overflow-y: auto;">
	        	<table class="display table table-striped table-hover">
				    <thead>
				      	<tr>
				        	<th>Id</th>
				        	<th>Name</th>
				        	<th>Url</th>
				        	<th class="text-center">Action</th>
				      	</tr>
				    </thead>
				    <tbody>
				      	@foreach($links as $link)
				      		<tr>
					        	<td>{{$link->id}}</td>
					        	<td>{{$link->name}}</td>
					        	<td style="max-width: 20rem;"><a href="{{$link->url}}" target="_blank">{{$link->url}}</a></td>
					        	<td>
					        		<div class="input-group-btn action_group">
									   <li class="action_icon">
									      	<button type="button" class="btn btn-info btn-block " data-toggle="dropdown" aria-expanded="false"><i class="fa fa-ellipsis-v" aria-hidden="true" title="View"></i></button>
										    <ul class="dropdown-menu">
										        <li>
										        	<a 
														href="javascript:void(0)"
														class="edit"
														data-id="{!! $link->id !!}"
														data-name="{!! $link->name !!}"
														data-url="{!! $link->url !!}"
													> 
									        			<i class="material-icons">edit</i>  Edit
									        		</a> 
										        </li>
										        <li>
										        	<a href="{{route('admin.link.delete', $link->id)}}"
										        		onclick="return confirm('Are you sure you want to delete this link?');"
									        		> 
									        			<i class="material-icons">delete</i>  Delete
									        		</a> 
										        </li>
												<li>
													<li>
														{{-- @if($d->status == 'ACTIVE')
															<a href="{{route('admin.user_update_status', ['suspended', $d->id])}}" onclick="return confirm('Are you sure? you want to suspend this user?')">
																<i class="material-icons">block</i>  Suspend
															</a> 
														@elseif($d->status == 'SUSPENDED')
															<a href="{{route('admin.user_update_status', ['active', $d->id])}}" onclick="return confirm('Are you sure? you want to activate this user?')">
																<i class="material-icons">check_circle</i>  Active
															</a> 
														@endif --}}
													</li>
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
          <h5 class="modal-title">Add new Link</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <form id="linkForm" action="{{route('admin.link.store')}}" method="post">
        <div class="modal-body">
                @csrf
                <input type="text" value="1" name="is_blog" hidden>
                <div class="form-group">
                    <label for="name">Name</label>
                    <input type="text" placeholder="Name" name="name" id="name" class="form-control">
                </div>
                <div class="form-group">
                    <label for="url">Url</label>
                    <input type="url" placeholder="Url" name="url" id="url" class="form-control">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-primary">Save changes</button>
            </div>
        </form>
      </div>
    </div>
</div>


<script type="text/javascript">

	$('#newLink').on('click', function(){
		$('#linkForm').trigger('reset');
		$('#create_model').modal('show');
	});

	$('.edit').on('click', function(){
		$('#create_model').modal('show');
		$('#create_model').find('.modal-title').text('Edit Link');
		form = $('#linkForm');
		form.append('<input type="hidden" name="_method" value="PUT">');
		form.attr('action', `{{route('admin.link.update', "")}}/${$(this).data('id')}`);
		form.find('#page').val($(this).data('page')).trigger("change");
		form.find('#name').val($(this).data('name'));
		form.find('#url').val($(this).data('url'));
		form.find('#description').val($(this).data('description'));
	});

	$('#create_model').on('hidden.bs.modal', function () {
		$('#create_model').find('.modal-title').text('Add new link');
		$('#linkForm').trigger('reset');
		$('#linkForm').find('input[name="_method"]').remove();
		$('#linkForm').attr('action', "{{route('admin.link.store')}}");
	});

    $('#linkForm').validate({
        rules: {
            name: {
                required: true,
                maxlength: 250
            },
            url: {
                required: true
            },
        },
        messages: {
            name: {
                required: "Please enter a name",
                maxlength: "Name must be less than 250 characters"
            },
            url: {
                required: "Please enter a url"
            },
        },
        errorPlacement: function (label, element, errorClass, validClass) {
			label.addClass('invalid-feedback');
			// let id =`${element.attr('id')}`;
			// if(element.attr('name') == `options[${id.substr(id.length - 1)}]`) {
			// 	label.insertAfter(element.parent(this.currentElements));
			// } else {
			label.insertAfter(element);
			// }
		},
		highlight: function (element, errorClass) {
			// $(element).parent().addClass('validation-error')
			$(element).addClass('is-invalid').removeClass('is-valid')
		},
		unhighlight: function (element, errorClass) {
			// $(element).parent().removeClass('validation-error')
			$(element).removeClass('is-invalid').addClass('is-valid')
		},
		submitHandler: function (form) {
			if(!$(form).valid()) return false;
            $(form).find('button[type="submit"]').attr('disabled', true).html('<i class="fa fa-spinner fa-pulse"></i> Please wait...');
            form.submit();
		}
    });
</script>

@endsection