@extends('admin.layout')
@section('title', 'Services')
@section('content')
    <div class="row mb-4 mt-20">
        <div class="col-md-12">
            <div class="card card-small">
                <div class="card-header border-bottom">
                    <h6 class="m-0 pull-left">Main Services</h6>
                    <button type="button" class="btn btn-success pull-right" data-toggle="modal"
                            data-target="#create_model"><i class="fa fa-plus"></i> Add Service
                    </button>
                </div>
                <div class="card-body" style="overflow-y: auto;">
                    <table class="display table table-striped table-hover">
                        <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Image</th>
                            <th>Is Active</th>
                            <th class="text-center">Action</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($data as $d)
                            <tr>
                                <td>{{$d->id}}</td>
                                <td>{{$d->name}}</td>
                                <td>
                                    <a href={{$d->image}} data-fancybox="images" data-caption="{{ $d->name }}">
                                        <img style="width:40px; height:40px" src={{$d->image}} alt=""/>
                                    </a>
                                </td>
                                {{-- <td><img  src={{$d->image}}></td> --}}
                                <td>{{$d->status ? 'Yes' : 'No'}}</td>
                                <td>
                                    <div class="input-group-btn action_group">
                                        <li class="action_icon">
                                            <button type="button" class="btn btn-info btn-block " data-toggle="dropdown"
                                                    aria-expanded="false"><i class="fa fa-ellipsis-v" aria-hidden="true"
                                                                             title="View"></i></button>
                                            <ul class="dropdown-menu">
                                                <li>
                                                    <a href="javascript:;" data-toggle="modal"
                                                       data-target="#update_model" class="update-obj"
                                                       data-id="{{$d->id}}"
                                                       data-image={{$d->image !== null ? $d->image : 'Empty'}}
                                                    > Update Image</a>
                                                </li>
                                                <li>
                                                    <a href="javascript:;" data-toggle="modal" data-target="#edit_model"
                                                       class="edit-obj"
                                                       data-id="{{$d->id}}"
                                                       data-name="{{$d->name}}"
                                                       data-og_title="{{$d->og_title}}"
                                                       data-og_description="{{$d->og_description}}"
                                                    > Edit</a>
                                                </li>
                                                <li>
                                                    @if($d->status)
                                                        <a href="{{route('admin.services_update_status', ['inactive', $d->id])}}"
                                                           onclick="return confirm('Are you sure? you want to inactive this service?')">Inactive</a>
                                                    @else
                                                        <a href="{{route('admin.services_update_status', ['active', $d->id])}}"
                                                           onclick="return confirm('Are you sure? you want to activate this service?')">Active</a>
                                                    @endif

                                                </li>
                                                <li>
                                                    <a href="{{route('admin.service_delete', ['id' => $d->id, 'type' => 'service'])}}"
                                                       onclick="return confirm('Are you sure? you want to delete this service?')">Delete</a>
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
                <form method="post" id="create-new-service" action="{{route('admin.services_create', ['locale' => request()->query('locale')])}}"
                      enctype="multipart/form-data">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Add Service</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                aria-hidden="true">&times;</span></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-12 form-group">
                                <label>Name</label>
                                <input type="text" class="form-control" placeholder="Service Name" name="name"
                                       required="">
                            </div>
                            <div class="col-md-12 input-group">
                                <div class="custom-file">
                                    <input type="file" class="custom-file-input" onchange="handleImageUpload(this)"
                                           id="image" name="image" accept="image/png,image/gif,image/jpeg"
                                           aria-describedby="inputGroupFileAddon01" required="">
                                    <label class="custom-file-label" for="image">Choose file</label>
                                </div>
                            </div>
                            <div class="col-md-12 mt-1 text">Recommended Image size 281 * 304 max 4MB</div>
                            <br>
                            <br>
                            <div class="form-group col-12">
                                <h5>Meta Information's</h5>
                                <label for="title">OG Title</label>
                                <input type="text" name="og_title" id="title"
                                       class="form-control"
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
                                          rows="3"></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary"> Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>


    <div class="modal fade" id="edit_model" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
         aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form method="post" action="{{route('admin.services_create', ['locale' => request()->query('locale')])}}" id="edit-form">
                    @csrf
                    <input type="hidden" name="id">
                    <input type="hidden" name="locale" value="{{ request()->query('locale') }}">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Update Service</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                aria-hidden="true">&times;</span></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-12 form-group">
                                <label>Name</label>
                                <input type="text" class="form-control" name="name" required="">
                            </div>
                            <br>
                            <div class="form-group col-12">
                                <h5>Meta Information's</h5>
                                <label for="title">OG Title</label>
                                <input type="text" name="og_title" id="title"
                                       class="form-control"
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
                                          rows="3"></textarea>
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

    <div class="modal fade" id="update_model" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
         aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form method="post"
                      action="{{route('admin.services_create', ['locale' => request()->query('locale')])}}" id="update-form"
                      enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="id">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Update Image</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                aria-hidden="true">&times;</span></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-12 mb-3">
                                <img id="image" src="" class="img-fluid">
                            </div>
                            <div class="col-md-12 input-group mb-3">
                                <div class="custom-file">
                                    <input type="file" class="custom-file-input" onchange="handleImageUpload(this)"
                                           id="updateImage" name="image" accept="image/png,image/gif,image/jpeg"
                                           aria-describedby="inputGroupFileAddon01">
                                    <label class="custom-file-label" for="image">Update Image</label>
                                </div>
                            </div>
                            <div class="col-md-12 mt-1 text">Recommended Image size 281 * 304 max 4MB</div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Update</button>
                    </div>
                </form>
            </div>
        </div>
    </div>


    <script type="text/javascript">

        /**
         * Validate Add New Service Form
         *
         * @returns {Boolean}
         */
        $('#create-new-service').validate({
            rules: {
                name: {
                    required: true
                },
                image: {
                    required: true,
                    accept: "image/png,image/gif,image/jpeg"
                }
            },
            messages: {
                name: {
                    required: 'Please enter name'
                },
                image: {
                    required: 'Please select image',
                    accept: 'Please select image only jpg,png,gif'
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
            highlight: function (element, errorClass) {
                $(element).parent().addClass('is-invalid');
                $(element).addClass('is-invalid');
            },
            unhighlight: function (element, errorClass) {
                $(element).parent().removeClass('is-invalid');
                $(element).removeClass('is-invalid');
            },
            submitHandler: function (form) {
                if (!$(form).valid()) return false;
                $('#create-new-service').find('button[type="submit"]').html('<i class="fa fa-spinner fa-pulse"></i> Processing...').attr('disabled', true);
                form.submit();
            }
        });

        /**
         * Validate Edit Service Form
         *
         * @returns {Boolean}
         */
        $('#edit-form').validate({
            rules: {
                name: {
                    required: true
                }
            },
            messages: {
                name: {
                    required: 'Please enter name'
                }
            },
            errorElement: 'div',
            errorPlacement: function (error, element) {
                error.addClass('invalid-feedback');
                error.insertAfter(element);
            },
            highlight: function (element, errorClass) {
                $(element).parent().addClass('is-invalid');
                $(element).addClass('is-invalid');
            },
            unhighlight: function (element, errorClass) {
                $(element).parent().removeClass('is-invalid');
                $(element).removeClass('is-invalid');
            },
            submitHandler: function (form) {
                if (!$(form).valid()) return false;
                $('#edit-form').find('button[type="submit"]').attr('disabled', true).html('<i class="fa fa-spinner fa-pulse"></i> Processing...');
                form.submit();
            }
        });

        /**
         * Validate Update Service Image Form
         *
         * @returns {Boolean}
         */
        $('#update-form').validate({
            rules: {
                image: {
                    required: true,
                    accept: "image/png,image/gif,image/jpeg"
                }
            },
            messages: {
                image: {
                    required: 'Please select image',
                    accept: 'Please select image only jpg,png,gif'
                }
            },
            errorElement: 'div',
            errorPlacement: function (error, element) {
                error.addClass('invalid-feedback ml-3');
                error.insertAfter(element);
            },
            highlight: function (element, errorClass) {
                $(element).parent().addClass('is-invalid');
                $(element).addClass('is-invalid');
            },
            unhighlight: function (element, errorClass) {
                $(element).parent().removeClass('is-invalid');
                $(element).removeClass('is-invalid');
            },
            submitHandler: function (form) {
                if (!$(form).valid()) return false;
                $('#update-form').find('button[type="submit"]').attr('disabled', true).html('<i class="fa fa-spinner fa-pulse"></i> Processing...');
                form.submit();
            }
        });

        function handleImageUpload(element) {
            let xDimension = 281;
            let yDimension = 304;
            let file = $(element).prop('files')[0];

            if (!file) return false;
            if (file.size / 1024 > 4096) {
                alert('Image size should be less than 4MB');
                $(element).prop('files', null);
                $(element).val('');
                $(element).closest('.custom-file').find('.custom-file-label').html('Choose file');
                return false;
            }
            let reader = new FileReader();
            reader.readAsDataURL(file);
            reader.onload = function (e) {
                let image = new Image();
                image.src = e.target.result;
                image.onload = function () {
                    let height = this.height;
                    let width = this.width;
                    if (height < yDimension || width < xDimension) {
                        alert(`Image Recommended dimension ${xDimension} x ${yDimension}`);
                        $(element).prop('files', null);
                        $(element).val('');
                        $(element).closest('.custom-file').find('.custom-file-label').html('Choose file');
                        return false;
                    }
                    return true;
                }
                return true;
            };
            return true;
        }

        /**
         * Set value in edit modal
         *
         * @returns void
         */
        $('.edit-obj').on('click', function () {
            $('#edit-form input[name=name]').val($(this).data('name'));
            $('#edit-form input[name=id]').val($(this).data('id'));
            $('#edit-form input[name=og_title]').val($(this).data('og_title'));
            $('#edit-form textarea[name=og_description]').val($(this).data('og_description'));
        });

        /**
         * Set image in update modal
         *
         * @returns void
         */
        $('.update-obj').on('click', function () {
            $('#image').attr("src", $(this).data('image'));
            $('#update-form input[name=id]').val($(this).data('id'));
        });

        /**
         * Handle image and file upload
         *
         * @param object {e}
         *
         * @returns void
         */
        const imageHandleChange = function (e) {
            if (e.target.files.length) {
                $(this).next('.custom-file-label').html(e.target.files[0].name);
            }
        };

        /**
         * Handle image and file upload on chnage
         *
         * @callback imageHandleChange
         *
         * @returns void
         */
        $('#image').change(imageHandleChange);
        $('#updateImage').change(imageHandleChange);
    </script>
@endsection
