@extends('admin.layout')
@section('title', 'Article')
@section('content')
<div class="row mb-4 mt-20">
    <div class="col-md-12">
	    <div class="card card-small">
            <div class="card-header border-bottom">
                {{$action == 'store' ? 'Create' : 'Edit'}} Article
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-12">
                        <div id="success">
                        </div>
                    </div>
                </div>
                <form id="article" action="{{ $action == 'store' ? route('admin.article.store') : route('admin.article.update', $article->slug) }}" method="POST">
                    @csrf
                    <div class="row">
                        <div class="form-group col-md-12">
                            <label for="title">Title</label> <strong class="text-danger"> *</strong>
                            <input type="text" name="title" id="title" value="{{$action == 'store' ? "" : $article->title}}" class="form-control" placeholder="Title...">
                        </div>
                        <div class="form-group col-md-6">
                            <label for="for_role">For User Type</label>
                            <select name="for_role" class="form-control" id="for_role">
                                <option value="">Both</option>
                                @foreach (["USER", "PROVIDER"] as $role)
                                    <option value="{!!$role!!}" {{$action == 'store' ? "" : ($article->for_role == $role ? "selected" : "")}}>{{Str::of($role)->title()}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group col-md-6">
                            <label for="sub_service_id">Sub Service</label>
                            <select name="sub_service_id" class="form-control" id="sub_service_id">
                                <option value="">Please select Sub Service</option>
                                @foreach ($sub_services as $sub_service)
                                    <option value="{{$sub_service->id}}" {{$action == 'store' ? "" : ($article->sub_service_id == $sub_service->id ? "selected" : "")}}>{{$sub_service->name}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <label for="editor">Content</label> <strong class="text-danger">*</strong>
                    <div id="editor"></div>
                    <div class="text-danger contentError"></div>
                    <button type="submit" class="btn btn-primary mt-3" id="save">{{$action == 'edit' ? "Update" : "Save"}}</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
$('#title').on('input', function () {
    $('#success').html('');
})

const ACTION = "{{ $action }}";

let content = {!! @json_encode($article->content) !!}

let error = false;

var quill = new Quill('#editor', {
    placeholder: 'Compose an artic...',
    theme: 'snow'
});

if(content){
    quill.setContents(JSON.parse(content));
}

let form = $('#article');

$('input[name="name"]').on('change', function() {
    $('#success').html('');
    $('#error').html('');
});

$('#editor').on('input', function() {
    if(quill?.getText() && quill?.getText()?.replace(/<(.|\n)*?>/g, '')?.trim()?.length > 10 ){
        $('.contentError').html('');
    } else {
        $('.contentError').html('Please enter at least 10 characters');
    }
});

$('#article').validate({
    rules: {
        title: {
            required: true,
            minlength: 3,
            maxlength: 255
        }
    },
    messages: {
        title: {
            required: "Please enter a title",
            minlength: "Title must be at least 3 characters long",
            maxlength: "Title can not be more than 255 characters long"
        }
    },
    ignore: [
        '#editor'
    ],
    errorElement: 'span',
    errorPlacement: function (error, element) {
        error.addClass('invalid-feedback');
        element.closest('.form-group').append(error);
    },
    highlight: function (element, errorClass, validClass) {
        $(element).addClass('is-invalid');
    },
    unhighlight: function (element, errorClass, validClass) {
        $(element).removeClass('is-invalid');
    },
    submitHandler: function(thisForm) {
        if(quill?.getText()?.replace(/<(.|\n)*?>/g, '')?.trim()?.length < 10) {
            $('.contentError').html('Please enter at least 10 characters');
            return false
        }
        let data = {};
        data.content = JSON.stringify(quill.getContents());
        data.title = form.find('input[name="title"]').val();
        data.sub_service_id = form.find('select[name="sub_service_id"]').val();
        data.for_role = form.find('select[name="for_role"]').val();
        $('button[type="submit"]').attr('disabled', true);
        $('button[type="submit"]').html(`${ACTION == 'store' ? 'Saving' : "Updating"}... <i class="fa fa-spinner fa-pulse" id="loading"></i>`);
        $.ajax({
            url: form.attr('action'),
            type: ACTION == 'store' ? 'post' : 'put',
            data: {...data, _token: "{{ csrf_token() }}"},
            success: function (response) {
                console.log(response);
                $('#success').html(
                    `<div class="alert alert-success" role="alert">
                        <strong>Success!</strong> article ${ACTION == 'store' ? "created" : "updated"} successfully.
                    </div>`
                )
                ACTION == 'store' && quill.setContents(JSON.parse('{"ops":[{"insert":"\\n"}]}'));
                ACTION == 'store' && thisForm.reset();
                $('button[type="submit"]').attr('disabled', false);
                $('button[type="submit"]').html(ACTION == 'edit' ? "update" : "Save");
            },
            error: function (error) {
                $('#article').validate();
                $('button[type="submit"]').attr('disabled', false);
                $('button[type="submit"]').html(ACTION == 'edit' ? "update" : "Save");
                if(error.status === 422){
                    let errors = error.responseJSON.errors;
                    ['name', 'title'].forEach(function (item) {
                    if (errors[item]) {
                        form.find(`input[name="${item}"]`).addClass('is-invalid');
                            form.find(`input[name="${item}"]`).after(`<div class="invalid-feedback">${errors[item][0]}</div>`);
                        }
                    });
                } else if(error.status === 409 || error.status === 500) {
                    $('#success').html(
                        `<div class="text-danger" role="alert">
                            <strong>Error!</strong> ${error.responseJSON.message}.
                        </div>`
                    )
                }
            }
        });
        return false;
    }
});

</script>

@endsection
@push('plugin-scripts')
<script src="{{asset('admin/assets/plugins/text-editor/js/quill.min.js')}}"></script>
{{-- <script src="{{asset('restaurant/assets/plugins/select2/js/select2.min.js')}}"></script> --}}
{{-- <script src="{{asset('admin/assets/plugins/text-editor/js/text_editor.js')}}"></script> --}}
@endpush
@push('plugin-stylesheet')
<link rel="stylesheet" href="{{asset('admin/assets/plugins/text-editor/css/quill.snow.css')}}">
{{-- <link rel="stylesheet" href="{{asset('restaurant/assets/plugins/select2/css/select2.min.css')}}"> --}}
{{-- <link rel="stylesheet" href="{{asset('admin/assets/plugins/text-editor/css/text_editor.css')}}"> --}}
@endpush
