@extends('admin.layout')
@section('title', 'Payemnt Methods')
@section('content')
<div class="row mb-4 mt-20">
    <div class="col-md-12">
	    <div class="card card-small">
	         <div class="card-header border-bottom">
	            <h6 class="m-0 pull-left">Pyament Method</h6>
                <button id="newLink" type="button" class="btn btn-success pull-right"><i class="fa fa-plus"></i> Add new Payment Method</button>
	        </div>
	        <div class="card-body" style="overflow-y: auto;">
	        	<table class="display table table-striped table-hover">
				    <thead>
				      	<tr>
				        	<th>Id</th>
				        	<th>name</th>
				        	<th>icon</th>
				        	<th class="text-center">Action</th>
				      	</tr>
				    </thead>
				    <tbody>
                        @if ($paymentMethods)
                         	@foreach ($paymentMethods as $paymentMethod)
                                <tr>
                                    <td>{{$paymentMethod->id}}</td>
                                    <td>{{$paymentMethod->name}}</td>
                                    <td>
                                        <a href={{$paymentMethod->icon}} data-fancybox="images" data-caption="{!! $paymentMethod->icon !!}">
                                                <img style="width:50px; height:50px" src={{$paymentMethod->icon}} alt="" />
                                        </a>
                                    </td>
                                    <td class="text-center">
                                        <div class="input-group-btn action_group">
                                            <li class="action_icon">
                                                    <button type="button" class="btn btn-info btn-block " data-toggle="dropdown" aria-expanded="false"><i class="fa fa-ellipsis-v" aria-hidden="true" title="View"></i></button>
                                                    <ul class="dropdown-menu">
                                                        <li>
                                                            <a href="{{route('admin.payment-method.destroy', $paymentMethod->id)}}"
                                                                onclick="return confirm('Are you sure you want to delete this Method?');"
                                                            >
                                                                <i class="material-icons">delete</i>Delete
                                                            </a>
                                                        </li>
                                                        <li>
                                                            <a href="javascript:;" data-toggle="modal" data-target="#edit_model"
                                                            class="edit" data-id="{{ $paymentMethod->id }}"
                                                            data-name="{{ $paymentMethod->name }}" data-terms="{{ $paymentMethod->terms }}"
                                                            data-icon="{{ $paymentMethod->icon }}"> <i class="material-icons">edit</i> Edit</a>
                                                        </li>
                                                    </ul>
                                                </li>
                                        </div>
                                    </td>
                                </tr>
                         	@endforeach
                        @endif
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
          <h5 class="modal-title">Add Payment Method</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <form id="form" action="{{route('admin.payment-method.store', ['locale' => request()->query('locale')])}}" method="post" enctype="multipart/form-data">
        <div class="modal-body">
                @csrf
                <div class="form-group">
                    <label for="name">Name</label>
                    <input type="text" name="name" id="name" class="form-control">
                </div>
                <div class="form-group">
                    <div class="custom-file">
                        <input type="file" class="custom-file-input" id="icon" name="icon"
                            accept="image/png,image/gif,image/jpeg,image/svg" aria-describedby="inputGroupFileAddon01">
                        <label class="custom-file-label" for="icon">Choose file</label>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-primary">Save</button>
            </div>
        </form>
      </div>
    </div>
</div>


<script type="text/javascript">

	$('#newLink').on('click', function(){
		$('#form').trigger('reset');
		$('#create_model').modal('show');
	});

    let rules = {
            name: {
                required: true,
                maxlength: 250
            },
            icon: {
                required: false,
                accept: ['image/png','image/gif','image/jpeg']
            }
        };
    form = $('#form');
	$('.edit').on('click', function(){
		$('#create_model').modal('show');
		form.append('<input type="hidden" name="_method" value="PATCH">');
		form.attr('action', `{{route('admin.payment-method.update', "")}}/${$(this).data('id')}?locale={{request()->query('locale')}}`);
		form.find('#name').val($(this).data('name'));
        form.find('button[type=submit]').text('Save Changes');
        rules = {
            name: {
                required: true,
                maxlength: 250
            },
            icon: {
                required: false,
                accept: ['image/png','image/jpg','image/jpeg', 'image/svg']
            }
        }
	});

	$('#create_model').on('hidden.bs.modal', function () {
		form.trigger('reset');
		form.find('input[name="_method"]').remove();
		form.attr('action', "{{route('admin.payment-method.store', ['locale' => request()->query('locale')])}}");
        form.find('button[type=submit]').text('Save');
        rules = {
            name: {
                required: true,
                maxlength: 250
            },
            icon: {
                required: true,
                accept: ['image/png','image/jpg','image/jpeg', 'image/svg']
            }
        }
	});

    $('#form').validate({
        rules: {
            name: {
                required: true,
                maxlength: 250
            },
            icon: {
                required: () => {
                    return form.find('input[name="_method"]').length == 0;
                },
                accept: ['image/png','image/gif','image/jpeg']
            }
        },
        messages: {
            name: {
                required: "Please enter a name"
            },
            icon: {
                required: "please select a icon",
                accept: "only .png, .svg, jpg and .jpeg image allow"
            }
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
			$(element).addClass('is-invalid').removeClass('is-valid');
		},
		unhighlight: function (element, errorClass) {
			// $(element).parent().removeClass('validation-error')
			$(element).removeClass('is-invalid').addClass('is-valid');
		},
        submitHandler: function (form){
            if(!$(form).valid()) return false;
            $(form).find('button[type="submit"]').attr('disabled', true).html('<i class="fa fa-spinner fa-pulse"></i> Please wait...');
            form.submit();
        }
    });

    $('#icon').on('change', function(){
        let fileName = $(this).val().split('\\').pop();
        $(this).next('.custom-file-label').addClass("selected").html(fileName);
    });
</script>

@endsection
