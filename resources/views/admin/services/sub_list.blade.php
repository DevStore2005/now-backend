@extends('admin.layout')
@section('title', 'Services')
@section('content')
    <div class="row mb-4 mt-20">
        <div class="col-md-12">
            <div class="card card-small">
                <div class="card-header border-bottom">
                    <h6 class="m-0 pull-left">Sub Services</h6>
                    <button type="button" class="btn btn-success pull-right" data-toggle="modal"
                            data-target="#create_model">
                        <i class="fa fa-plus"></i> Add Service
                    </button>
                </div>
                <div class="card-body" style="overflow-y: auto;">
                    <table class="display table table-striped table-hover">
                        <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Image</th>
                            <th>Service</th>
                            <th>View Type</th>
                            <th>Is Footer</th>
                            <th>Is Active</th>
                            <th class="text-center">Action</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach ($data as $d)
                            <tr>
                                <td>{{ $d->id }}</td>
                                <td>{{ $d->name }}</td>
                                <td>
                                    <a href={{$d->image}} data-fancybox="images" data-caption="{!! $d->name !!}">
                                        <img style="width:40px; height:40px" src={{$d->image}} alt=""/>
                                    </a>
                                </td>
                                <td>{{ $d->service->name }}</td>
                                <td>
                                    @if($d->view_type === 'provider')
                                        <span class='badge badge-outline-info'>{{ $d->view_type}}</span>
                                    @else
                                        <span class='badge badge-outline-secondary '>{{ $d->view_type}}</span>
                                    @endif
                                </td>
                                <td>
                                    @if($d->show_in_the_footer)
                                        <span class='badge badge-success'>Yes</span>
                                    @else
                                        <span class='badge badge-danger'>No</span>
                                    @endif
                                </td>
                                <td>
                                    @if($d->status)
                                        <span class='badge badge-success'>Yes</span>
                                    @else
                                        <span class='badge badge-danger'>No</span>
                                    @endif
                                </td>
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
                                                       data-id="{{ $d->id }}" data-image="{{ $d->image }}">
                                                        Update Image</a>
                                                </li>
                                                <li>
                                                    <a href="javascript:;" data-toggle="modal" data-target="#edit_model"
                                                       class="edit-obj"
                                                       data-id="{{ $d->id }}"
                                                       data-name="{{ $d->name }}"
                                                       data-terms="{{ $d->terms }}"
                                                       data-credit="{!! $d->credit !!}"
                                                       data-service_id="{{ $d->service_id }}"
                                                       data-view_type="{{ $d->view_type }}"
                                                       data-show_in_the_footer="{{ $d->show_in_the_footer }}"
                                                    > Edit</a>
                                                </li>
                                                <li>
                                                    <a href="{{ route('admin.question_list', $d->id) }}"> Service
                                                        Question</a>
                                                </li>
                                                <li>
                                                    <a
                                                        href="{{ route('admin.sub_service_content_list', ['SUB_SERVICE', $d->id]) }}">
                                                        service
                                                        Content</a>
                                                </li>
                                                <li>
                                                    @if ($d->status)
                                                        <a href="{{ route('admin.sub_services_update_status', ['inactive', $d->id]) }}"
                                                           onclick="return confirm('Are you sure? you want to inactive this service?')">Inactive</a>
                                                    @else
                                                        <a href="{{ route('admin.sub_services_update_status', ['active', $d->id]) }}"
                                                           onclick="return confirm('Are you sure? you want to activate this service?')">Active</a>
                                                    @endif
                                                </li>
                                                <li>
                                                    <a href="{{route('admin.service_delete', ['id' => $d->id, 'type' => 'sub-service'])}}"
                                                       onclick="return confirm('Are you sure? you want to delete this sub service?')">Delete</a>
                                                </li>
                                            </ul>
                                        </li>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                    <x-pagination :data="[
					'currentPage' => $data->currentPage(),
					'hasMore' => $data->hasMorePages(),
					'previousPage' => $data->previousPageUrl(),
					'nextPage' => $data->nextPageUrl(),
				]"/>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="create_model" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
         aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form method="post" id="add_new_sub_service" action="{{ route('admin.sub_services_create') }}"
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
                                <input type="text" class="form-control" name="name" required="">
                            </div>
                            <div class="col-md-12 form-group">
                                <label>Credits</label>
                                <input type="number" min="1" max="99999999" step="0.01" value="1.00"
                                       class="form-control"
                                       name="credit" required="">
                            </div>
                            <div class="col-md-12 form-group">
                                <label>Service</label>
                                <select name="service_id" required="" class="form-control">
                                    <option value="" disabled="" selected="">Choose Service</option>
                                    @foreach ($services as $ser)
                                        <option value="{{ $ser->id }}">{{ $ser->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-12 form-group">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="view_type"
                                           id="view_type_stander" value="standard" checked>
                                    <label class="form-check-label" for="view_type_stander">
                                        Standard View
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="view_type"
                                           id="view_type_provider" value="provider">
                                    <label class="form-check-label" for="view_type_provider">
                                        Provider View
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="show_in_the_footer"
                                           id="show_in_the_footer" value="1">
                                    <label class="form-check-label" for="show_in_the_footer">
                                        Show in the footer
                                    </label>
                                </div>
                            </div>

                            <div class="col-md-12 form-group">
                                <label>Terms</label>
                                <textarea name="terms" rows="5" class="form-control" required=""></textarea>
                            </div>
                            <div class="col-md-12 input-group">
                                <div class="custom-file">
                                    <input type="file" class="custom-file-input" id="image" name="image"
                                           accept="image/png,image/gif,image/jpeg"
                                           aria-describedby="inputGroupFileAddon01"
                                           required="" onchange="handleImageUpload(this)">
                                    <label class="custom-file-label" for="image">Choose file</label>
                                </div>
                            </div>
                            <div class="col-md-12 mt-1 text">Recommended Image size 281 * 304 max 4MB</div>
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
                <form method="post" action="{{ route('admin.sub_services_create') }}" id="edit-form"
                      enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="id">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Edit Service</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                aria-hidden="true">&times;</span></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-12 form-group">
                                <label>Name</label>
                                <input type="text" class="form-control" name="name" required="">
                            </div>
                            <div class="col-md-12 form-group">
                                <label>Credits</label>
                                <input type="number" min="1" max="99999999" step="0.01" value="1.00"
                                       class="form-control"
                                       name="credit" required="">
                            </div>
                            <div class="col-md-12 form-group">
                                <label>Service</label>
                                <select name="service_id" id="edit_service_id" required="" class="form-control">
                                    <option value="" disabled="" selected="">Choose Service</option>
                                    @foreach ($services as $ser)
                                        <option value="{{ $ser->id }}">{{ $ser->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-12 form-group">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="view_type"
                                           id="view_type_stander" value="standard">
                                    <label class="form-check-label" for="view_type_stander">
                                        Standard View
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="view_type"
                                           id="view_type_provider" value="provider">
                                    <label class="form-check-label" for="view_type_provider">
                                        Provider View
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="show_in_the_footer"
                                           id="show_in_the_footer_edit" value="1">
                                    <label class="form-check-label" for="show_in_the_footer_edit">
                                        Show in the footer
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-12 form-group">
                                <label>Terms</label>
                                <textarea name="terms" rows="5" class="form-control" required=""></textarea>
                                @error('terms')
                                <div class="text-danger">{{ $message }}</div>
                                @enderror
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
                <form method="post" action="{{ route('admin.sub_services_create') }}" id="update-form"
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
                            <div class="col-12 mb-3 text-center">
                                <img id="subServiceImage" src="" class="img-fluid">
                            </div>
                            <div class="col-md-12 input-group">
                                <div class="custom-file">
                                    <input type="file" class="custom-file-input" id="updateImage" name="image"
                                           accept="image/png,image/gif,image/jpeg" onchange="handleImageUpload(this)"
                                           aria-describedby="inputGroupFileAddon01">
                                    <label class="custom-file-label" for="image">Update Image</label>
                                </div>
                            </div>
                            <div class="col-md-12 mt-1 mb-3 text">Recommended Image size 281 * 304</div>
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

        /**
         * Validate add sub service form
         *
         *  @returns {boolean}
         */
        $('#add_new_sub_service').validate({
            rules: {
                name: {
                    required: true,
                    minlength: 3,
                    maxlength: 255
                },
                credit: {
                    required: true,
                    number: true,
                    min: 1,
                    max: 99999999
                },
                service_id: {
                    required: true
                },
                terms: {
                    required: true,
                    minlength: 3,
                    maxlength: 1500
                },
                image: {
                    required: true,
                    accept: "image/png,image/gif,image/jpeg"
                }
            },
            messages: {
                name: {
                    required: "Please enter name",
                    minlength: "Name must be at least 3 characters long",
                    maxlength: "Name must be at least 255 characters long"
                },
                credit: {
                    required: "Please enter credit",
                    number: "Please enter a valid number",
                    min: "Credit must be at least 1",
                    max: "Credit may not greater than 99999999"
                },
                service_id: {
                    required: "Please select service"
                },
                terms: {
                    required: "Please enter terms",
                    minlength: "Terms must be at least 3 characters long",
                    maxlength: "Terms may not be greater than 1500 characters"
                },
                image: {
                    required: "Please select image",
                    accept: "Please select image in png, gif or jpeg format"
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
                element = $(form).find('input[name=image]');
                if (!$(form).valid() || !handleImageUpload(element)) return false;
                $(form).find('button[type="submit"]').html('<i class="fa fa-spinner fa-pulse"></i> Processing...').attr('disabled', true);
                form.submit();
            }
        });

        /**
         * Validate update sub service form
         *
         *  @returns {boolean}
         */
        $('#edit-form').validate({
            rules: {
                name: {
                    required: true,
                    minlength: 3,
                    maxlength: 255
                },
                credit: {
                    required: true,
                    number: true,
                    min: 1,
                    max: 99999999
                },
                service_id: {
                    required: true
                },
                terms: {
                    required: true,
                    minlength: 3,
                    maxlength: 1500
                }
            },
            messages: {
                name: {
                    required: "Please enter name",
                    minlength: "Name must be at least 3 characters long",
                    maxlength: "Name must be at most 255 characters long"
                },
                credit: {
                    required: "Please enter credit",
                    number: "Please enter a valid number",
                    min: "Credit must be at least 1",
                    max: "Credit may not greater than 99999999"
                },
                service_id: {
                    required: "Please select service"
                },
                terms: {
                    required: "Please entr e terms",
                    minlength: "Terms must be at least 3 characters long",
                    maxlength: "Terms may not be greater than 1500 characters"
                }
            },
            errorElement: 'div',
            errorPlacement: function (error, element) {
                error.addClass('invalid-feedback');
                error.insertAfter(element);
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

        /**
         * Validate update sub service form image
         *
         * @returns {boolean}
         */
        $('#update-form').validate({
            rules: {
                image: {
                    required: true,
                    accept: 'image/png,image/jpg,image/jpeg'
                }
            },
            messages: {
                image: {
                    required: "Please select image",
                    accept: "Please select image in png, jpg or jpeg format"
                }
            },
            errorElement: 'div',
            errorPlacement: function (error, element) {
                error.addClass('invalid-feedback ml-3');
                error.insertAfter(element.closest('.input-group'));
            },
            highlight: function (element, errorClass, validClass) {
                $(element).addClass('is-invalid').removeClass('is-valid');
            },
            unhighlight: function (element, errorClass, validClass) {
                $(element).removeClass('is-invalid').addClass('is-valid');
            },
            submitHandler: function (form) {
                let element = $(form).find('input[name=image]');
                element = handleImageUpload(element);
                if (!element) return false;
                $(form).find('button[type="submit"]').html('<i class="fa fa-spinner fa-pulse"></i> Processing...').attr('disabled', true);
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
         * Set data in edit modal
         *
         *  @returns {void}
         */
        $('.edit-obj').on('click', function () {
            var view_type = $(this).data('view_type')
            var show_in_the_footer = $(this).data('show_in_the_footer')
            $('#edit-form input[name=name]').val($(this).data('name'));
            $('#edit-form input[name=credit]').val($(this).data('credit'));
            $('#edit-form textarea[name=terms]').text($(this).data('terms'));
            $('#edit-form input[name=id]').val($(this).data('id'));
            if (view_type === 'provider') {
                $('#edit-form #view_type_provider').val(view_type).attr('checked', true);
            } else {
                $('#edit-form #view_type_stander').val(view_type).attr('checked', true);
            }

            if (show_in_the_footer == 1) {
                $('#edit-form input[name=show_in_the_footer]').attr('checked', true);
            } else {
                $('#edit-form input[name=show_in_the_footer]').attr('checked', false);
            }
            $(`#edit_service_id option[value="${$(this).data('service_id')}"]`).attr('selected', 'selected');
        });

        /**
         * Set data in update image model
         *
         *  @returns {void}
         */
        $('.update-obj').on('click', function () {
            $('#subServiceImage').attr("src", $(this).data('image'));
            $('#update-form input[name=id]').val($(this).data('id'));
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
        $('#updateImage').change(imageHandleChange);
    </script>
@endsection
