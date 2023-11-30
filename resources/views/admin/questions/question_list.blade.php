@extends('admin.layout')
@section('title', 'Services')
@section('content')
{{-- {{ dd( $subService) }} --}}
<div class="row mb-4 mt-20">
    <div class="col-md-12">
	    <div class="card card-small">
	         <div class="card-header border-bottom">
	            <h6 class="m-0 pull-left">Services Question: {{ $subService->name }}</h6>
	            <button 
					type="button" 
					class="btn btn-success pull-right" 
					data-target="#create_model" 
					data-toggle="modal" 
					data-backdrop="static" 
					data-keyboard="false"
					id="create_model_btn"
				 ><i class="fa fa-plus"></i> Add Question</button>
	        </div>
	        <div class="card-body" style="overflow-y: auto;">
	        	<table class="display table table-striped table-hover">
				    <thead>
				      	<tr>
				        	<th>ID</th>
				        	<th>Question</th>
				        	<th class="text-center">Action</th>
				      	</tr>
				    </thead>
				    <tbody>
				      	@foreach($questions as $question)
				      		<tr>
					        	<td>{{$question->id}}</td>
					        	<td>{{$question->question}}</td>
					        	<td>
					        		<div class="input-group-btn action_group">
									   <li class="action_icon">
									      	<button type="button" class="btn btn-info btn-block " data-toggle="dropdown" aria-expanded="false"><i class="fa fa-ellipsis-v" aria-hidden="true" title="View"></i></button>
										    <ul class="dropdown-menu">
										        <li>
										        	<a href="javascript:;" data-toggle="modal" data-target="#update_model" class="update-obj"
													data-id="{{$question->id}}"
													data-is_multiple="{{$question->is_multiple}}"
													data-question="{{$question->question}}"
													data-options="{{$question->options}}"
										        	>	<i class="material-icons">visibility</i>  View and Edit</a> 
										        </li>
												<li>
													<a href="{{route('admin.destroy', ['question'=>$question])}}"
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
	<div class="modal-dialog modal-dialog-scrollable modal-lg" role="document">
	    <div class="modal-content">
			<form method="post" action="{{route('admin.question_store')}}" id="question" enctype="multipart/form-data">
				@csrf
				<input hidden class="form-control" name="sub_service_id" value="{{$subService->id}}" required=""/>
	      		<div class="modal-header">
			        <h5 class="modal-title" id="exampleModalLabel">Add Question</h5>
			        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
			    </div>
			    <div class="modal-body">
			        <div class="row">
					<div class="col-md-12 form-group">
			        	</div>
			        	<div class="col-md-12 form-group">
			        		<label>Question</label>
			        		<div class="input-group">
			        		<input type="text" class="form-control" placeholder="Add question" name="question" value="{{ @old('question') }}" required=""/>
								<div class="input-group-append">
									<span class="input-group-text">
										Is Multiple Choice?
										<input type="checkbox" name="is_multiple" class="mb-2 ml-1" >
									</span>
								</div>
							</div>
							{{-- @error('question')
								<div class="text-danger">{{ $message }}</div>
							@enderror --}}
			        	</div>
			        </div>
					<div class="row" id='option_list'>
						<div class="col-md-12 form-group">
			        		<div class="input-group">
								<input type="text" class="form-control" name="options[1]" id="add_option_1"  placeholder="Add option" required=""/>
								<div class="input-group-append">
									<button class="btn btn-danger" disabled id="remove_1" type="button" ><i class="fa fa-times"></i></button>
								</div>
							</div>
			        	</div>
						@error('options.*')
							<div class="alert alert-danger">Option should be at least 1 and at most 255 character</div>
						@enderror
						<div class="col-md-12">
							<button class="btn btn-primary" role="button" id="add_option">Add option</button>
						</div>
					</div>
			    </div>
			    <div class="modal-footer">
			        <button type="button" class="btn btn-secondary remove-options" data-dismiss="modal">Close</button>
			        <button type="submit" class="btn btn-primary">Submit</button>
			    </div>
	      	</form>
	    </div>
	</div>
</div>

<div class="modal fade" id="update_model" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-dialog-scrollable modal-lg" role="document">
	    <div class="modal-content">
			<form method="post" action="" id="update_question" enctype="multipart/form-data">
				@csrf
				@method('put')
				<input hidden class="form-control" name="sub_service_id" value="{{$subService->id}}" required=""/>
	      		<div class="modal-header">
			        <h5 class="modal-title" id="exampleModalLabel">Update Question</h5>
			        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
			    </div>
			    <div class="modal-body">
			        <div class="row">
					<div class="col-md-12 form-group">
			        	</div>
			        	<div class="col-md-12 form-group">
			        		<label>Question</label>
			        		<div class="input-group">
			        		<input type="text" class="form-control" placeholder="Add question" name="question" value="{{ @old('question') }}" required=""/>
								<div class="input-group-append">
									<span class="input-group-text">
										Is Multiple Choice?
										<input type="checkbox" name="is_multiple" class="mb-2 ml-1" >
									</span>
								</div>
							</div>
							{{-- @error('question')
								<div class="text-danger">{{ $message }}</div>
							@enderror --}}
			        	</div>
			        </div>
					<div class="row" id='option_list'>
						<div class="col-md-12">
							<button class="btn btn-primary" role="button" id="add_option">Add option</button>
						</div>
					</div>
			    </div>
			    <div class="modal-footer">
			        <button type="button" class="btn btn-secondary remove-options" data-dismiss="modal">Close</button>
			        <button type="submit" class="btn btn-primary">Submit</button>
			    </div>
	      	</form>
	    </div>
	</div>
</div>

<script type="text/javascript">

	$('#question').validate({
		degub:true,
		rule: {
			question: {
				required: true,
				minlength: 5,
				maxlength: 255
			},
			"options[1]" : {
				required: true,
				minlength: 1,
				maxlength: 255
			}
		},
		messages: {
			question: {
				required: "Please enter question",
				minlength: "Question must be at least 1 characters long",
				maxlength: "Question cannot be more than 255 characters long",
			},
			"options[]" : {
				required: "Please enter option",
				minlength: "Option must be at least 1 characters long",
				maxlength: "Option cannot be more than 255 characters long",
			}
		},
		errorPlacement: function (label, element, errorClass, validClass) {
			label.addClass('invalid-feedback');
			let id =`${element.attr('id')}`;
			if(element.attr('name') == `options[${id.substr(id.length - 1)}]` || element.attr('name') == 'question'){
				label.insertAfter(element.parent(this.currentElements));
			} else {
				label.insertAfter(element);
			}
		},
		highlight: function (element, errorClass) {
			// $(element).parent().addClass('validation-error')
			// $(element).addClass('is-invalid').removeClass('is-valid');
			$(element).addClass('is-invalid').removeClass('is-valid');
		},
		unhighlight: function (element, errorClass) {
			// $(element).parent().removeClass('validation-error')
			// $(element).removeClass('is-valid').addClass('is-invalid')
			$(element).removeClass('is-invalid').addClass('is-valid');
		}
	});

	$('#update_question').validate({
		degub:true,
		rule: {
			question: {
				required: true,
				minlength: 5,
				maxlength: 255
			},
			"options[1]" : {
				required: true,
				minlength: 1,
				maxlength: 255
			}
		},
		messages: {
			question: {
				required: "Please enter question",
				minlength: "Question must be at least 1 characters long",
				maxlength: "Question cannot be more than 255 characters long",
			},
			"options[]" : {
				required: "Please enter option",
				minlength: "Option must be at least 1 characters long",
				maxlength: "Option cannot be more than 255 characters long",
			}
		},
		errorPlacement: function (label, element, errorClass, validClass) {
			label.addClass('invalid-feedback');
			let id =`${element.attr('id')}`;
			if(element.attr('name') == `options[${id.substr(id.length - 1)}]` || element.attr('name') == 'question'){
				label.insertAfter(element.parent(this.currentElements));
			} else {
				label.insertAfter(element);
			}
		},
		highlight: function (element, errorClass) {
			// $(element).parent().addClass('validation-error')
			// $(element).addClass('is-invalid').removeClass('is-valid');
			$(element).addClass('is-invalid').removeClass('is-valid');
		},
		unhighlight: function (element, errorClass) {
			// $(element).parent().removeClass('validation-error')
			// $(element).removeClass('is-valid').addClass('is-invalid')
			$(element).removeClass('is-invalid').addClass('is-valid');
		}
	});

	$('.update-obj').on('click', function () {
		$('#update_question').validate();
		let id = $(this).data('id');
		let question = $(this).data('question');
		let options = $(this).data('options');
		let is_multiple = $(this).data('is_multiple');

		$('#update_question input[name=question]').val(question);
		if(is_multiple){
			$('#update_question input[name=is_multiple]').prop('checked', true);
		}
		
		$('#update_question').attr('action', "{!! route('admin.question_update', ':id') !!}");
		$('#update_question').attr('action', $('#update_question').attr('action').replace(':id', id));

		let optionSection = $('#update_question #option_list');
		optionSection.empty();
		let opt = options?.map((option, index) => {
			return `<div class="col-md-12 form-group">
						<div class="input-group">
							<input type="text" class="form-control" name="options[${option.id}]" id="add_option_${option.id}" placeholder="Add option" required="" autocomplete="off" value="${option.option}"/>
							<div class="input-group-append">
								<button class="btn btn-danger" disabled id="remove_${option.id}" onclick="handleRemoveClick(this)" type="button" ><i class="fa fa-times"></i></button>
							</div>
						</div>
					</div>`
		}).join('');
		optionSection.append(opt);
		// optionSection.append(`<div class="col-md-12">
		// 					<button type="button" class="btn btn-primary" role="button" id="add_option">Add option</button>
		// 				</div>`);
	});

	$('#update_question #add_option').on('click', function () {
		console.log('add option');
		$(
			`<div class="col-md-12 form-group">
				<div class="input-group">
					<input type="text" class="form-control" name="options[${option}]" id="add_option_${option}" placeholder="Add option" required="" autocomplete="off"/>
					<div class="input-group-append">
						<button class="btn btn-danger" id="remove_${option}" onclick="handleRemoveClick(this)" disabled type="button" ><i class="fa fa-times"></i></button>
					</div>
				</div>
			</div>`
		).insertBefore($(this).parent());
		option++;
		$(this).validate();
		$('[name^="options"]').each(function () {
			$(this).rules('add', {
				required: true,
				minlength: 1,
				maxlength: 255,
				messages: {
					required: "Please enter option",
					minlength: "Option must be at least 1 characters long",
					maxlength: "Option cannot be more than 255 characters long",
				}
			});
		});
	});

	const handleRemoveClick = function({id}) {
		$(`#${id}`).parents()?.eq(2)?.remove();
	}

	let option = 2;
	$('#add_option').on('click', function () {
		$(
			`<div class="col-md-12 form-group">
				<div class="input-group">
					<input type="text" class="form-control" name="options[${option}]" id="add_option_${option}" placeholder="Add option" required="" autocomplete="off"/>
					<div class="input-group-append">
						<button class="btn btn-danger" id="remove_${option}" onclick="handleRemoveClick(this)" type="button" ><i class="fa fa-times"></i></button>
					</div>
				</div>
			</div>`
		).insertBefore($(this).parent());
		option++;
		$('#question').validate()
		$('[name^="options"]').each(function () {
			$(this).rules('add', {
				required: true,
				minlength: 1,
				maxlength: 255,
				messages: {
					required: "Please enter option",
					minlength: "Option must be at least 1 characters long",
					maxlength: "Option cannot be more than 255 characters long",
				}
			});
		});
	});

	$('#create_model_btn').on('click', function () {
		$('#question').validate()
		$('[name^="options"]').each(function () {
			$(this).rules('add', {
				required: true,
				minlength: 1,
				maxlength: 255,
				messages: {
					required: "Please enter option",
					minlength: "Option must be at least 1 characters long",
					maxlength: "Option cannot be more than 255 characters long",
				}
			});
		});
	})

</script>
@endsection