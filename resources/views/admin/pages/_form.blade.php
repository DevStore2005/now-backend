@extends('admin.layout')
@section('title', 'Pages')
@section('content')
    <div class="row mb-4 mt-20">
        <div class="col-md-12">
            <div class="card card-small">
                <div class="card-header border-bottom">
                    {{$action == 'store' ? 'Create' : 'Edit'}} Page
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div id="success">
                            </div>
                        </div>
                    </div>
                    <form id="page"
                          action="{{ $action == 'store' ? route('admin.page.store', ['locale' => request()->query('locale')]) : route('admin.page.update', ['page' => $page->id,'locale' => request()->query('locale')]) }}"
                          method="POST">
                        @csrf
                        <div class="row">
                            <div class="form-group col-md-3">
                                <label for="name">Name</label> <strong class="text-danger">*</strong>
                                <input type="text" name="name" value="{{$action == 'store' ? "" : $page->name}}"
                                       id="name" class="form-control" placeholder="Name">
                            </div>
                            <div class="form-group col-md-9">
                                <label for="title">Title</label> <strong class="text-danger"> *</strong>
                                <input type="text" name="title" id="title"
                                       value="{{$action == 'store' ? "" : $page->title}}" class="form-control"
                                       placeholder="Title...">
                            </div>
                        </div>
                        <label for="editor">Contant</label> <strong class="text-danger">*</strong>
                        <div id="editor"></div>
                        <div class="text-danger content"></div>
                        <hr>

                        <h5>Meta Information's</h5>
                        <div class="row">
                            <div class="form-group col-12">
                                <label for="title">OG Title</label>
                                <input type="text" name="og_title" id="title"
                                       value="{{$action == 'store' ? "" : $page->og_title}}" class="form-control"
                                       placeholder="Title...">
                            </div>
                            <div class="form-group col-12">
                                <label for="title">OG Image</label>
                                <div class="custom-file">
                                    <input type="file" accept="image/png,image/gif,image/jpeg"
                                           name="og_image" id="og_image">
                                    <label class="custom-file-label" for="og_image">Choose file</label>
                                </div>
                            </div>
                            <div class="form-group col-12">
                                <label for="og_description">OG Description</label>
                                <textarea class="form-control" name="og_description" id="og_description"
                                          rows="3">{{@$page->og_description}}</textarea>
                            </div>
                        </div>

                        {{-- {{dd((!$foundTerms || !$foundPrivacy))}} --}}
                        @if ( $action == 'store' && (!$foundTerms || !$foundPrivacy))
                            <label for="type" class="mt-4">Page Type <span>(Optional)</span></label>
                            <select name="type" id="type" class="form-control col-6">
                                <option value="">Please select Type</option>
                                @if (!$foundTerms)
                                    <option value="1">Terms & Conditions</option>
                                @endif
                                @if (!$foundPrivacy)
                                    <option value="2">Privecy</option>
                                @endif
                            </select>
                        @endif
                        <button type="submit" class="btn btn-primary mt-3"
                                id="save">{{$action == 'edit' ? "Update" : "Save"}}</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script type="text/javascript">

        const ACTION = "{{ $action }}";

        let content = {!! @json_encode($page->content) !!}

            let
        error = false;

        var quill = new Quill('#editor', {
            placeholder: 'Compose an epic...',
            theme: 'snow'
        });

        if (content != '') {
            quill.setContents(JSON.parse(content));
        }


        let form = $('#page');

        $('input[name="name"]').on('change', function () {
            $('success').html('');
            $('error').html('');
        });

        $('#editor').on('input', function () {
            if (quill.getText().replace(/<(.|\n)*?>/g, '').trim().length > 10) {
                $('.contentError').html('');
            } else {
                $('.contentError').html('Please enter at least 10 characters');
            }
        });

        $('#page').validate({
            rules: {
                name: {
                    required: true,
                    minlength: 3,
                    maxlength: 50
                },
                title: {
                    required: true,
                    minlength: 3,
                    maxlength: 255
                },
            },
            messages: {
                name: {
                    required: "Please enter a name",
                    minlength: "Name must be at least 3 characters long",
                    maxlength: "Name can not be more than 50 characters long"
                },
                title: {
                    required: "Please enter a title",
                    minlength: "Title must be at least 3 characters long",
                    maxlength: "Title can not be more than 255 characters long"
                },
            },
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
            submitHandler: function (thisForm) {
                if (quill.getText().replace(/<(.|\n)*?>/g, '').trim().length < 10) {
                    $('.contentError').html('Please enter at least 10 characters');
                    return false
                }
                let form = new FormData(thisForm);
                form.append('content', JSON.stringify(quill.getContents()));
                $('button[type="submit"]').attr('disabled', true);
                $('button[type="submit"]').html(`${ACTION == 'store' ? 'Saving' : "Updating"}... <i class="fa fa-spinner fa-pulse" id="loading"></i>`);
                try {

                    $.ajax({
                        url: "{!! $action == 'store' ? route('admin.page.store', ['locale' => request()->query('locale')]) : route('admin.page.update', $page->id, ['page' => $page->id, 'locale' => request()->query('locale')]) !!}",
                        type: 'post',
                        cache: false,
                        contentType: false,
                        processData: false,
                        data: form,
                        success: function (response) {
                            $('#success').html(
                                `<div class="alert alert-success" role="alert">
                        <strong>Success!</strong> Page ${ACTION == 'store' ? "created" : "updated"} successfully.
                    </div>`
                            )
                            ACTION == 'store' && quill.setContents(JSON.parse('{"ops":[{"insert":"\\n"}]}'));
                            ACTION == 'store' && thisForm.reset();
                            $('button[type="submit"]').attr('disabled', false);
                            $('button[type="submit"]').html(ACTION == 'edit' ? "update" : "Save");
                        },
                        error: function (error) {
                            $('#page').validate();
                            $('button[type="submit"]').attr('disabled', false);
                            $('button[type="submit"]').html(ACTION == 'edit' ? "update" : "Save");
                            if (error.status === 422) {
                                let errors = error.responseJSON.errors;
                                ['name', 'title'].forEach(function (item) {
                                    if (errors[item]) {
                                        form.find(`input[name="${item}"]`).addClass('is-invalid');
                                        form.find(`input[name="${item}"]`).after(`<div class="invalid-feedback">${errors[item][0]}</div>`);
                                    }
                                });
                            } else if (error.status === 409 || error.status === 500) {
                                $('#success').html(
                                    `<div class="text-danger" role="alert">
                            <strong>Error!</strong> ${error.responseJSON.message}.
                        </div>`
                                )
                            }
                        }
                    });
                } catch (error) {
                    console.log(error.message);
                }
                return false;
            }
        });

        /**
         * select and show image
         * @param {*} event
         *
         * @returns {void}
         */
        const imageHandleChange = function (e) {
            if (e.target.files.length) {
                $(this).next('.custom-file-label').html(e.target.files[0].name.replace(/ /g, '_'));
            }
        };

        /**
         * handle image change
         *
         *  @returns {void}
         */
        $('#image').change(imageHandleChange);
    </script>

@endsection
@push('plugin-scripts')
    <script src="{{asset('admin/assets/plugins/text-editor/js/quill.min.js')}}"></script>
    <script src="{{asset('admin/assets/plugins/text-editor/js/text_editor.js')}}"></script>
@endpush
@push('plugin-stylesheet')
    <link rel="stylesheet" href="{{asset('admin/assets/plugins/text-editor/css/quill.snow.css')}}">
    <link rel="stylesheet" href="{{asset('admin/assets/plugins/text-editor/css/text_editor.css')}}">
@endpush
