@extends('admin.layout')
@section('title', 'Partners')
@section('content')
    <div class="row mb-4 mt-20">
        <div class="col-md-12">
            <div class="card card-small">
                <div class="card-header border-bottom">
                    <h6 class="m-0 pull-left">Partners</h6>
                    <button type="button" class="btn btn-success pull-right" data-toggle="modal"
                            data-target="#create_model"><i class="fa fa-plus"></i> Add Partner
                    </button>
                </div>
                <div class="card-body" style="overflow-y: auto;">
                    <table class="display table table-striped table-hover">
                        <thead>
                        <tr>
                            <th>Id</th>
                            <th>name</th>
                            <th>url</th>
                            <th>Image</th>
                            <th class="text-center">Action</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($partners as $partner)
                            <tr>
                                <td>{{ $partner->id }}</td>
                                <td>{{ $partner->name }}</td>
                                <td>{{ $partner->url }}</td>
                                <td>
                                    @if ($partner->image)
                                        <a href={{$partner->image}} data-fancybox="images"
                                           data-caption="{!! $partner->name !!}">
                                            <img style="width:50px; height:50px" src={{$partner->image}} alt=""/>
                                        </a>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <div class="input-group-btn action_group">
                                        <li class="action_icon">
                                            <button type="button" class="btn btn-info btn-block " data-toggle="dropdown"
                                                    aria-expanded="false"><i class="fa fa-ellipsis-v" aria-hidden="true"
                                                                             title="View"></i></button>
                                            <ul class="dropdown-menu">
                                                <li>
                                                    <a href="javascript:;" data-toggle="modal" data-target="#edit_model"
                                                       class="btn btn-primary btn-sm edit-obj"
                                                       data-id="{{$partner->id}}"
                                                       data-name="{{$partner->name}}"
                                                       data-url="{{$partner->url}}"
                                                       data-image="{{$partner->image}}"
                                                    > <i class="material-icons">edit</i> Edit</a>
                                                </li>
                                                <li>
                                                    <a href="{{ route('admin.front-pages.partner.delete', $partner->id) }}"
                                                       class="btn btn-primary btn-sm"
                                                    > <i class="material-icons">delete</i> Delete</a>
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

    <div class="modal fade" id="create_model" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
         aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form method="post" action="{{ route('admin.front-pages.partner.store') }}" id="create-Section"
                      enctype="multipart/form-data">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Add Partner</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                aria-hidden="true">&times;</span></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-12 form-group">
                                <label for="name">Name</label>
                                <input id="name" type="text" class="form-control" name="name" required="">
                            </div>
                            <div class="col-md-12 form-group">
                                <label for="url">Url</label>
                                <input id="url" type="url" class="form-control" name="url">
                            </div>
                            <div class="col-md-12 input-group">
                                <div class="custom-file">
                                    <input type="file" class="custom-file-input" id="updateImage" name="image"
                                           accept="image/png,image/gif,image/jpeg"
                                           aria-describedby="inputGroupFileAddon01">
                                    <label class="custom-file-label" for="image">Select Image</label>
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

    <div class="modal fade" id="edit_model" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
         aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form method="post" action="{{ route('admin.front-pages.partner.store') }}" id="edit-Section"
                      enctype="multipart/form-data">
                    @method('PUT')
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Edit Partner</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                aria-hidden="true">&times;</span></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-12 form-group">
                                <label for="name">Name</label>
                                <input id="name" type="text" class="form-control" name="name" required="">
                            </div>
                            <div class="col-md-12 form-group">
                                <label for="url">Url</label>
                                <input id="url" type="url" class="form-control" name="url">
                            </div>
                            <div class="col-12 mb-3">
                                <img id="show-image" src="" class="img-fluid">
                            </div>
                            <div class="col-md-12 input-group">
                                <div class="custom-file">
                                    <input type="file" class="custom-file-input" id="updateImage" name="image"
                                           accept="image/png,image/gif,image/jpeg"
                                           aria-describedby="inputGroupFileAddon01">
                                    <label class="custom-file-label" for="image">Select Image</label>
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

    <script type="text/javascript">

        $('#create-Section').validate({
            rules: {
                name: {
                    required: true,
                    minlength: 3,
                    maxlength: 255
                },
                url: {
                    url: true,
                },
                image: {
                    required: true,
                    accept: 'image/png,image/jpg,image/jpeg'
                }
            },
            messages: {
                name: {
                    required: "Please enter name",
                    minlength: "Name must be at least 3 characters long",
                    maxlength: "Name must be at most 50 characters long"
                },
                url: {
                    url: "Please enter valid url"
                },
                image: {
                    required: "Please select image",
                    accept: "Please select image in png, jpg or jpeg format"
                }
            },
            errorElement: 'div',
            errorPlacement: function (error, element) {
                error.addClass('invalid-feedback');
                if (element.hasClass('custom-file-input')) {
                    error.addClass('ml-3');
                    error.insertAfter(element.closest('.input-group'));
                } else {
                    error.insertAfter(element);
                }
            },
            highlight: function (element, errorClass, validClass) {
                $(element).addClass('is-invalid').removeClass('is-valid');
            },
            unhighlight: function (element, errorClass, validClass) {
                $(element).removeClass('is-invalid').addClass('is-valid');
            },
            submitHandler: function (form) {
                if (!$(form).valid()) return false;
                $(form).find('button[type="submit"]').html('<i class="fa fa-spinner fa-pulse"></i> Processing...').attr('disabled', true);
                form.submit();
            }
        });

        $('#edit-Section').validate({
            rules: {
                name: {
                    required: true,
                    minlength: 3,
                    maxlength: 255
                },
                url: {
                    url: true,
                },
                image: {
                    required: false,
                    accept: 'image/png,image/jpg,image/jpeg'
                }
            },
            messages: {
                name: {
                    required: "Please enter name",
                    minlength: "Name must be at least 3 characters long",
                    maxlength: "Name must be at most 50 characters long"
                },
                url: {
                    url: "Please enter valid url"
                },
                image: {
                    required: "Please select image",
                    accept: "Please select image in png, jpg or jpeg format"
                }
            },
            errorElement: 'div',
            errorPlacement: function (error, element) {
                error.addClass('invalid-feedback');
                if (element.hasClass('custom-file-input')) {
                    error.addClass('ml-3');
                    error.insertAfter(element.closest('.input-group'));
                } else {
                    error.insertAfter(element);
                }
            },
            highlight: function (element, errorClass, validClass) {
                $(element).addClass('is-invalid').removeClass('is-valid');
            },
            unhighlight: function (element, errorClass, validClass) {
                $(element).removeClass('is-invalid').addClass('is-valid');
            },
            submitHandler: function (form) {
                if (!$(form).valid()) return false;
                $(form).find('button[type="submit"]').html('<i class="fa fa-spinner fa-pulse"></i> Processing...').attr('disabled', true);
                form.submit();
            }
        });

        $('.edit-obj').on('click', function () {
            var id = $(this).data('id');
            var name = $(this).data('name');
            var url = $(this).data('url');
            var image = $(this).data('image');
            var actionUrl = "{{ route('admin.front-pages.partner.update', ":id") }}";
            actionUrl = actionUrl.replace(':id', id);
            // handleImageInput('#edit_model');
            $('#edit_model form').attr('action', actionUrl);
            $('#edit_model #name').val(name);
            $('#edit_model #url').val(url);
            $('#show-image').attr('src', image);
        });

    </script>

@endsection
