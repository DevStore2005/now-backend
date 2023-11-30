@extends('admin.layout')
@section('title', 'Create Blog')
@section('content')
    <div class="row mb-4 mt-20">
        <div class="col-md-12">
            <div class="card card-small">
                <div class="card-header border-bottom">
                    {{$action == 'store' ? 'Create' : 'Edit'}} Article
                </div>
                <div class="card-body">
                    <form id="blog" action="{!! route('admin.blog.store', ['locale' => request()->query('locale')]) !!}"
                          method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="row">
                            <div class="form-group col-md-12">
                                <label for="title">Title</label> <strong class="text-danger"> *</strong>
                                <input type="text" name="title" id="title"
                                       value="{{$action == 'store' ? "" : $blog->title}}" class="form-control"
                                       placeholder="Title...">
                            </div>
                            <div class="form-group col-md-6">
                                <label for="featured_image" class="mt-3">Featured Image</label> @if($action != 'edit')
                                    <strong class="text-danger"> *</strong>
                                @endif
                                <input type="file" accept=".gif,.png,.jpg,.jpeg" name="featured_image"
                                       id="featured_image" class="form-control" placeholder="Image">
                            </div>
                            <div class="form-group col-md-6">
                                <label for="category" class="mt-3">Category</label> <strong class="text-danger">
                                    *</strong>
                                <select name="category_id" id="category" class="form-control">
                                    <option value="">Select Category</option>
                                    @foreach($categories as $category)
                                        <option
                                            value="{{$category->id}}" {{$action == 'store' ? "" : ($blog->category_id == $category->id ? "selected" : "")}}>{{$category->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="default">
                            <label for="editor1">Content</label> <strong class="text-danger">*</strong>
                            <div id="editor1"></div>
                            <div class="text-danger contentError mb-4"></div>
                        </div>

                        <hr>

                        <h5>Meta Information's</h5>
                        <div class="row">
                            <div class="form-group col-12">
                                <label for="og_title">OG Title</label>
                                <input type="text" name="og_title" id="og_title"
                                       value="{{$action == 'store' ? "" : $blog->og_title}}" class="form-control"
                                       placeholder="Title...">
                            </div>
                            <div class="form-group col-12">
                                <label for="og_image">OG Image</label>
                                <div class="custom-file">
                                    <input type="file" accept="image/png,image/gif,image/jpeg"
                                           name="og_image" id="og_image">
                                    <label class="custom-file-label" for="og_image">Choose file</label>
                                </div>
                            </div>
                            <div class="form-group col-12">
                                <label for="og_description">OG Description</label>
                                <textarea class="form-control" name="og_description" id="og_description"
                                          rows="3">{{@$blog->og_description}}</textarea>
                            </div>
                        </div>
                        <button type="button" @if($action == 'edit') hidden @endif id="addImageOrEditor"
                                class="btn btn-outline-secondary add-image">Add Image
                        </button>
                        <button type="submit" class="btn btn-primary float-right submit"
                                id="save">{{$action == 'edit' ? "Update" : "Save"}}</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script type="module">

        const option = {
            placeholder: 'Compose a Blog...',
            theme: 'snow',
            modules: {
                toolbar: [
                    ['bold', 'italic', 'underline', 'strike'],        // toggled buttons
                    ['blockquote', 'code-block'],

                    [{'header': 1}, {'header': 2}],               // custom button values
                    [{'list': 'ordered'}, {'list': 'bullet'}],
                    [{'script': 'sub'}, {'script': 'super'}],      // superscript/subscript
                    [{'indent': '-1'}, {'indent': '+1'}],          // outdent/indent
                    [{'direction': 'rtl'}],                         // text direction

                    [{'size': ['small', false, 'large', 'huge']}],  // custom dropdown
                    [{'header': [1, 2, 3, 4, 5, 6, false]}],

                    [{'color': []}, {'background': []}],          // dropdown with defaults from theme
                    [{'font': []}],
                    [{'align': []}],

                    ['clean'],                                         // remove formatting button

                    ['link', 'video'],                         // link and image, video
                ],
                imageResize: {
                    modules: ['Resize', 'DisplaySize', 'Toolbar'],
                },
            }
        }
        let type = 'Editor';
        let editor = [];
        const ACTION = "{{ $action }}";
        const url = "{{ $action == 'edit' ? route('admin.blog.update', ['blog' =>$blog->slug, 'locale' => request()->query('locale')]) : route('admin.blog.store', ['locale' => request()->query('locale')]) }}";

        const createQuill = (idx) => {
            editor[idx - 1] = new Quill(`#editor${idx}`, option);
        }

        const isQuillEmpty = (value) => (("<p><br></p>" == value) ? true : false);

        const blog = @json(isset($blog) ? $blog : null);

        if (blog) {
            const contents = blog?.contents;

            if (contents) contents.forEach(({content, image}, idx) => {
                if (idx == 1) $('#addImageOrEditor').before(`<div id="editor${idx + 1}" class="mb-4"></div>`);
                if (image) $('#addImageOrEditor').before(`<input type="file" accept="image/gif,image/png,image/jpg,image/jpeg" name="image[${idx + 1}]" value="" class="form-control mb-4" placeholder="Title..."/>`);
                createQuill(idx + 1);
                editor[idx].setContents(JSON.parse(content));
            });
        } else {
            createQuill(1);
        }


        $('#addImageOrEditor').on('click', function (e) {
            let btn = $(this);
            let idx = editor.length;
            if (type == 'Image') {
                if ($(`input[name="image[${idx}]"]`).prop('files')[0]) {
                    type = 'Editor';
                    btn.text('Add Image');
                    idx++;
                    btn.before(`<div id="editor${idx}" class="mb-4"></div>`);
                    createQuill(idx)
                } else {
                    alert('Please select image');
                }
            } else {
                if (editor[idx - 1].getLength() > 1) {
                    type = 'Image';
                    btn.text('Add Editor');
                    btn.before(`<input type="file" accept="image/gif,image/png,image/jpg,image/jpeg" name="image[${idx}]" value="" class="form-control mb-4" placeholder="Title..."/>`);
                    addRule();
                } else {
                    alert('Please enter content');
                }
            }
        });

        $('#blog').validate({
            rules: {
                title: {
                    required: true,
                },
                featured_image: {
                    required: ACTION == 'edit' ? false : true,
                    accept: ["image/gif", "image/jpeg", "image/png", "image/jpg"],
                },
                category_id: {
                    required: true,
                },
            },
            messages: {
                title: {
                    required: "Please enter title",
                },
                featured_image: {
                    required: "Please select featured image",
                },
                category_id: {
                    required: "Please select category",
                },
            },
            ignore: ['input[type=hidden]', '.ql-editor', 'ql-toolbar'],
            errorElement: 'span',
            errorPlacement: function (error, element) {
                error.addClass('invalid-feedback');
                element.closest('.form-group').append(error);
            },
            highlight: function (element, errorClass, validClass) {
                $(element).addClass('is-invalid').removeClass('is-valid');
            },
            unhighlight: function (element, errorClass, validClass) {
                $(element).removeClass('is-invalid').addClass('is-valid');
            },
            submitHandler: function (form) {
                let formData = new FormData(form);
                if (ACTION == 'edit') formData.append('_method', 'PATCH');
                for (let idx = 1; idx <= editor.length; idx++) {
                    let edt = editor[idx - 1];
                    let edtLength = edt?.getLength();
                    if (edtLength == 1) {
                        alert('Please enter content');
                        return false;
                    }
                    edtLength && formData.append(`content[${idx}]`, `${JSON.stringify(edt?.getContents())}`);
                    let image = $(`input[name="image[${idx}]"]`)?.prop('files');
                    image && formData.append(`image[${idx}]`, image[0] ?? '');
                }
                $('.add-image').attr('disabled', true).text('Uploading...');
                $('.submit').attr('disabled', true).text('Uploading...');
                $.ajax({
                    // xhr: function () {
                    //     var xhr = new window.XMLHttpRequest();
                    //     xhr.upload.addEventListener("progress", function (evt) {
                    //         if (evt.lengthComputable) {
                    //             var percentComplete = evt.loaded / evt.total;
                    //             percentComplete = parseInt(percentComplete * 100);
                    //             if (percentComplete === 100) {
                    //             }
                    //         }
                    //     }, false);
                    //     return xhr;
                    // },
                    url,
                    type: "POST",
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function (data) {
                        if (!data?.error) {
                            console.log(data);
                            window.location.href = "{{ route('admin.blog.index', ['locale' => request()->query('locale')]) }}";
                        } else {
                            $('.add-image').attr('disabled', false).text('Add image');
                            $('.submit').attr('disabled', false).text('Submit');
                        }
                    },
                    error: function (data) {
                        window.location.reload();
                    }
                });
                return false;
            }
        });

        const addRule = () => {
            $(`input[type="file"]`).last().rules("add", {
                required: true,
                accept: ["image/gif", "image/jpeg", "image/png", "image/jpg"],
                messages: {
                    required: "Please select image",
                    accept: "Please select valid image format (gif, jpeg, png, jpg)",
                }
            });
            $('#blog').validate();
        }

    </script>

@endsection
@push('plugin-scripts')
    <script src="{{asset('admin/assets/plugins/text-editor/js/quill.min.js')}}"></script>
    <script src="https://cdn.jsdelivr.net/npm/quill-image-resize-module@3.0.0/image-resize.min.js"></script>
    {{-- <script src="{{asset('restaurant/assets/plugins/select2/js/select2.min.js')}}"></script> --}}
    {{-- <script src="{{asset('admin/assets/plugins/text-editor/js/text_editor.js')}}"></script> --}}
@endpush
@push('plugin-stylesheet')
    <link rel="stylesheet" href="{{asset('admin/assets/plugins/text-editor/css/quill.snow.css')}}">
{{-- <link rel="stylesheet" href="{{asset('restaurant/assets/plugins/select2/css/select2.min.css')}}"> --}}
{{-- <link rel="stylesheet" href="{{asset('admin/assets/plugins/text-editor/css/text_editor.css')}}"> --}}
@endpush
