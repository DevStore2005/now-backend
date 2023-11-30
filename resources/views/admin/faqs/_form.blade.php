@extends('admin.layout')
@section('title', 'FAQ')
@section('content')
<div class="row mb-4 mt-20">
    <div class="col-md-12">
	    <div class="card card-small">
            <div class="card-header border-bottom">
                {{$action == 'store' ? 'Create' : 'Edit'}} FAQ
            </div>
            <div class="card-body">
				<form id="submit-form" action="{!! $action == 'store' ? route('admin.faq.store', ['locale' => request()->query('locale')]) : route('admin.faq.update', $faq->id) !!}" method="POST">
					@csrf
					@if($action == 'edit')@method('PUT')@endif
					<div class="row">
						<div class="col-md-8 mx-auto">
							<div class="form-group mb-2">
									<label for="sub_service_id">Sub Service</label>
									<select class="form-control" {!! $action == 'show' ? 'disabled' : "" !!} name="sub_service_id" id="sub_service_id">
										<option value="">Select Sub Service</option>
										@foreach($subServices as $subService)
											<option value="{{$subService->id}}" {{ ($action == 'edit' || $action == 'show') && $subService->id == $faq->sub_service_id ? 'selected' : '' }}>{{$subService->name}}</option>
										@endforeach
									</select>
									{!! $errors->first('sub_service_id', '<label class="invalid-feedback" for="question">:message</label>') !!}
							</div>
							<div class="form-group mb-2">
								<label for="question">Question</label> <strong class="text-danger"> *</strong>
								<div class="input-group">
									<textarea type="text" {!! $action == 'show' ? 'disabled' : "" !!} class="form-control {!! $errors->first('question', "is-invalid") !!}" value="{!! old('question', ($action == 'edit' || $action == 'show') ? $faq->question : "") !!}" name="question" placeholder="Question">{!! old('question', ($action == 'edit' || $action == 'show') ? $faq->question : "") !!}</textarea>
									{!! $errors->first('question', '<label class="invalid-feedback" for="question">:message</label>') !!}

								</div>
							</div>
							{{-- @if ($errors->has('answers.*'))
								@foreach ($errors->get('answers.*') as $key => $error)
									{{ dd($key, $error) }}
									<div class="form-group mb-2">
										<label for="answers">Answers</label> <strong class="text-danger"> *</strong>
										<div class="input-group">
											<textarea type="text" {!! $action == 'show' ? 'disabled' : "" !!} class="form-control is-invalid" value="{!! old('answers.*') !!}" name="answers[{!! $key+1 !!}]" id="answer_{!! $key+1 !!}"  placeholder="Enter answer 1"/>{!! old('answers.*') !!}</textarea>
											<div class="input-group-append">
												<button class="btn btn-danger" disabled id="remove_1" type="button" ><i class="fa fa-times"></i></button>
											</div>
											<label class="invalid-feedback" for="question">{!! $errors->first('answers.*') !!}</label>
										</div>
									</div>
								@endforeach
							@else  --}}
								@if ($action == 'edit' || $action == 'show')
									@foreach ($faq->answers as $key => $answer)
										<div class="input-group mb-2">
											<textarea type="text" {!! $action == 'show' ? 'disabled' : "" !!} class="form-control @error('answers.'.($key+1)) is-invalid @enderror" value="{!! $answer->answer !!}" name="answers[{!! $key+1 !!}]" id="answer_{!! $key+1 !!}"  placeholder="Enter answer">{!! $answer->answer !!}</textarea>
											<div class="input-group-append">
												<button class="btn btn-danger" {!! ($key == 0 || $action == 'show') ? 'disabled' : "" !!} id="remove_{!! $key+1 !!}" onclick="handleRemoveClick(this)" type="button" ><i class="fa fa-times"></i></button>
											</div>
											@error('answers.'.($key+1))
												<label class="invalid-feedback" for="question">{!! $message !!}</label>
											@enderror
										</div>
									@endforeach
								@else
									<div class="input-group mb-2">
										<textarea type="text" class="form-control" name="answers[1]" id="answer_1"  placeholder="Enter answer 1"/></textarea>
										<div class="input-group-append">
											<button class="btn btn-danger" disabled id="remove_1" type="button" ><i class="fa fa-times"></i></button>
										</div>
									</div>
								@endif
							{{-- @endif --}}
							@if ($action != 'show')
								<button type="button" class="btn btn-primary" id="add_answer">
								<i class="fa fa-plus"></i> Add answer
								</button>
								<button type="submit" class="btn btn-primary float-right">
									{!!$action == 'edit' ?
										"<i class='fa fa-edit'></i> Update" :
										"<i class='fa fa-save'></i> Save"
									!!}
								</button>
							@endif
						</div>
					</div>
				</form>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">

	const ACTION = "{!! $action !!}";
	let answer = 2;

	$(document).ready(function() {
		if(ACTION == 'edit'){
			answer = {!! isset($faq) ?  count($faq->answers)+1 : 2 !!};
			$('#faq').validate();
			$('[name^="answers"]').each(function () {
				$(this).rules('add', {
					required: true,
					minlength: 1,
					maxlength: 1200,
					messages: {
						required: "Please enter answer",
						minlength: "Answer must be at least 1 characters long",
						maxlength: "Answer cannot be more than 1200 characters long",
					}
				});
			});
		}
	});

	$('#submit-form').validate({
		rules: {
			question: {
				required: true,
				minlength: 3,
				maxlength: 1200
			},
			"answers[1]" : {
				required: true,
				minlength: 1,
				maxlength: 1200
			}
		},
		messages: {
			question: {
				required: "Please enter question",
				minlength: "Question must be at least 3 characters long",
				maxlength: "Question must be less than 1200 characters long"
			},
			"answers[1]" : {
				required: "Please enter answer",
				minlength: "Answer must be at least 1 characters long",
				maxlength: "Answer must be less than 1200 characters long"
			}
		},
		errorPlacement: function (label, element, errorClass, validClass) {
			label.addClass('invalid-feedback');
			let id =`${element.attr('id')}`;
			if(element.attr('name') == `answers[${id.split('_').pop()}]`){
				label.insertAfter(element.next());
			} else {
				label.insertAfter(element);
			}
		},
		highlight: function (element, errorClass) {
			$(element).addClass('is-invalid').removeClass('is-valid');
		},
		unhighlight: function (element, errorClass) {
			$(element).removeClass('is-invalid').addClass('is-valid');
		}
	});

    const handleRemoveClick = function({id}) {
		$(`#${id}`).parent().parent().remove();
	}

	$('#add_answer').on('click', function () {
		$(`<div class="input-group mb-2">
				<textarea type="text" class="form-control" name="answers[${answer}]" id="answer_${answer}" placeholder="Enter answer" autocomplete="off"></textarea>
				<div class="input-group-append">
					<button class="btn btn-danger" id="remove_${answer}" onclick="handleRemoveClick(this)" type="button" ><i class="fa fa-times"></i></button>
				</div>
			</div>`).insertBefore($(this));
		answer++;
		$('#faq').validate()
		$('[name^="answers"]').each(function () {
			$(this).rules('add', {
				required: true,
				minlength: 1,
				maxlength: 1200,
				messages: {
					required: "Please enter answer",
					minlength: "Answer must be at least 1 characters long",
					maxlength: "Answer cannot be more than 1200 characters long",
				}
			});
		});
	});


</script>
@endsection
