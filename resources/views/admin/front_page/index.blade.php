@extends('admin.layout')
@section('title', 'Front Page')
@section('content')
    <div class="row mb-4 mt-20">
        <div class="col-md-12">
            <div class="card card-small">
                <div class="card-header border-bottom">
                    <h6 class="m-0 pull-left">Front page</h6>
                    <div class="btn-group pull-right">
                        <div class="dropdown mr-2">
                            <button class="btn btn-secondary dropdown-toggle" type="button"
                                    @if (!$country && $countries->count() < 1)
                                        disabled
                                    @endif data-toggle="dropdown" aria-expanded="false">
                                {{ $country ? $country->name : 'Please select country' }}
                            </button>
                            <div class="dropdown-menu">
                                @foreach ($countries as $country)
                                    <a class="dropdown-item"
                                       href="{{ route('admin.front-pages.index', ['country' => $country->iso2, 'locale' => request()->query('locale')]) }}">{{ $country->name }}</a>
                                @endforeach
                            </div>
                        </div>
                        <button type="button" class="btn btn-success" data-toggle="modal" @if (!$country)
                            disabled
                                @endif
                                data-target="#create_model"><i class="fa fa-plus"></i> Add Section
                        </button>
                    </div>
                </div>
                <div class="card-body" style="overflow-y: auto;">
                    <table class="display table table-striped table-hover">
                        <thead>
                        <tr>
                            <th>id</th>
                            <th>title</th>
                            <th>description</th>
                            <th>Section</th>
                            <th>Image</th>
                            <th class="text-center">Action</th>
                        </tr>
                        </thead>
                        <tbody>
                        @if (!$country)
                            <tr>
                                <td colspan="5" class="text-center">
                                    @if($currencies->count() < 1)
                                        Please add country and currency from side bar
                                    @else
                                        Please select country
                                    @endif
                                </td>
                            </tr>
                        @else
                            @foreach($frontPages as $frontPage)
                                <tr>
                                    <td>{{ $frontPage->id }}</td>
                                    <td>{{ $frontPage->title }}</td>
                                    <td>{{ $frontPage->description }}</td>
                                    <td>{{ $frontPage->type }}</td>
                                    <td>
                                        @if ($frontPage->image)
                                            <a href={{$frontPage->image}} data-fancybox="images"
                                               data-caption="{!! $frontPage->name !!}">
                                                <img style="width:50px; height:50px" src={{$frontPage->image}} alt=""/>
                                            </a>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <div class="input-group-btn action_group">
                                            <li class="action_icon">
                                                <button type="button" class="btn btn-info btn-block "
                                                        data-toggle="dropdown" aria-expanded="false"><i
                                                        class="fa fa-ellipsis-v" aria-hidden="true" title="View"></i>
                                                </button>
                                                <ul class="dropdown-menu">
                                                    <li>
                                                        <a href="javascript:;" data-toggle="modal"
                                                           data-target="#edit_model"
                                                           class="btn btn-primary btn-sm edit-obj"
                                                           data-id="{{$frontPage->id}}"
                                                           data-title="{{$frontPage->title}}"
                                                           data-description="{{$frontPage->description}}"
                                                           data-type="{{$frontPage->type}}"
                                                           data-image="{{$frontPage->image}}"
                                                           data-country_id="{{$frontPage->country_id}}"
                                                        > <i class="material-icons">edit</i> Edit</a>
                                                    </li>
                                                    @if ($frontPage->type == 'App')
                                                        <li>
                                                            <a href="javascript:;" data-toggle="modal"
                                                               data-target="#app_model"
                                                               class="btn btn-primary btn-sm app-obj"
                                                               @foreach ($frontPage->app_urls as $url)
                                                                   data-{{ $url->name }}="{{ $url->url }}"
                                                                @endforeach
                                                            > <i class="material-icons">more</i> Add App</a>
                                                        </li>
                                                    @elseif($frontPage->type == "Info")
                                                        <li>
                                                            <a href="javascript:;" data-toggle="modal"
                                                               data-target="#info_model"
                                                               class="btn btn-primary btn-sm info-obj"
                                                               @if ($frontPage->extra_info)
                                                                   data-title_1="{!! $frontPage->extra_info->title_1 !!}"
                                                               data-title_2="{!! $frontPage->extra_info->title_2 !!}"
                                                               data-description_1="{!! $frontPage->extra_info->description_1 !!}"
                                                               data-description_2="{!! $frontPage->extra_info->description_2 !!}"
                                                               data-image="{!! $frontPage->extra_info->image !!}"
                                                                @endif
                                                            > <i class="material-icons">more</i> Add Info</a>
                                                        </li>
                                                    @elseif($frontPage->type == "Partner")
                                                        <li>
                                                            <a href="{{ route('admin.front-pages.partner') }}"
                                                               class="btn btn-primary btn-sm partner-obj"
                                                            > <i class="material-icons">more</i> view Partner</a>
                                                        </li>
                                                    @endif
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

    <div class="modal fade" id="create_model" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
         aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form method="post" action="{{ route('admin.front-pages.store') }}" id="create-Section"
                      enctype="multipart/form-data">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Add Section</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                aria-hidden="true">&times;</span></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-12 form-group">
                                <label for="title">Title</label>
                                <input id="title" type="text" class="form-control" name="title" required="">
                            </div>
                            <div class="col-md-12 form-group">
                                <label for="country">Country</label>
                                <select class="form-control" name="country_id" id="country">
                                    <option value="">Select Type</option>
                                    @foreach ($countries as $country)
                                        <option value="{{ $country->id }}">{{ $country->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-12 form-group">
                                <label for="type">Type</label>
                                <select class="form-control" name="type" id="type">
                                    <option value="">Select Type</option>
                                    <option value="Header">Head Section</option>
                                    <option value="Services">Services Section</option>
                                    <option value="Info">Info Section</option>
                                    <option value="App">App Section</option>
                                    <option value="Partner">Partner Section</option>
                                    <option value="Professional">Professional Section</option>
                                </select>
                            </div>
                            <div class="col-md-12 form-group">
                                <label for="description">Description</label>
                                <textarea class="form-control" rows="4" name="description"></textarea>
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

    {{-- update page section--}}

    <div class="modal fade" id="edit_model" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
         aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form method="post" action="{{ route('admin.front-pages.update', "") }}" id="update-Section"
                      enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Update Section</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                aria-hidden="true">&times;</span></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-12 form-group">
                                <label for="title">Title</label>
                                <input id="title" type="text" class="form-control" name="title" required="">
                            </div>
                            <div class="col-md-12 form-group">
                                <label for="country">Country</label>
                                <select class="form-control" name="country_id" id="country">
                                    <option value="">Select Country</option>
                                    @foreach ($countries as $country)
                                        <option value="{{ $country->id }}">{{ $country->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-12 form-group">
                                <label for="type">Type</label>
                                <select class="form-control" disabled name="type" id="type">
                                    <option value="">Select Type</option>
                                    <option value="Header">Head Section</option>
                                    <option value="Services">Services Section</option>
                                    <option value="Info">Info Section</option>
                                    <option value="App">App Section</option>
                                    <option value="Partner">Partner Section</option>
                                    <option value="Professional">Professional Section</option>
                                </select>
                            </div>
                            <div class="col-md-12 form-group">
                                <label for="description">Description</label>
                                <textarea class="form-control" rows="4" name="description" id="description"></textarea>
                            </div>
                            <div class="col-12 mb-3">
                                <img id="show-image" src="" class="img-fluid">
                            </div>
                            <div class="col-md-12 input-group">
                                <div class="custom-file">
                                    <input type="file" class="custom-file-input" id="updateImage1" name="image"
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

    <div class="modal fade" id="app_model" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
         aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form method="post" action="{{ route('admin.front-pages.appUrls') }}" id="app-Section">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">App Url</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                aria-hidden="true">&times;</span></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-12 form-group">
                                <label for="user_android">Android User App</label>
                                <input id="user_android" type="url" class="form-control" name="user_android"
                                       required="">
                            </div>
                            <div class="col-md-12 form-group">
                                <label for="provider_android">Android Provider App</label>
                                <input id="provider_android" type="url" class="form-control" name="provider_android"
                                       required="">
                            </div>
                            <div class="col-md-12 form-group">
                                <label for="user_ios">User IOS App</label>
                                <input id="user_ios" type="url" class="form-control" name="user_ios" required="">
                            </div>
                            <div class="col-md-12 form-group">
                                <label for="provider_ios">Provider IOS App</label>
                                <input id="provider_ios" type="url" class="form-control" name="provider_ios"
                                       required="">
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

    <div class="modal fade" id="info_model" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
         aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form method="post" action="{{ route('admin.front-pages.appInfo') }}" id="info-Section"
                      enctype="multipart/form-data">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Info Details Section</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                aria-hidden="true">&times;</span></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-12 form-group">
                                <label for="title_1">Title</label>
                                <input id="title_1" type="text" class="form-control" name="title_1" required="">
                            </div>
                            <div class="col-md-12 form-group">
                                <label for="description_1">Description</label>
                                <textarea id="description_1" type="text" class="form-control" name="description_1"
                                          required=""></textarea>
                            </div>
                            <div class="col-md-12 form-group">
                                <label for="title_2">Title</label>
                                <input id="title_2" type="text" class="form-control" name="title_2" required="">
                            </div>
                            <div class="col-md-12 form-group">
                                <label for="description_2">Description</label>
                                <textarea id="description_2" type="text" class="form-control" name="description_2"
                                          required=""></textarea>
                            </div>

                            <div class="col-12 mb-3">
                                <img id="show-image" src="" class="img-fluid">
                            </div>
                            <div class="col-md-12 input-group">
                                <div class="custom-file">
                                    <input type="file" class="custom-file-input" id="updateImage3" name="image"
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


    <div class="modal fade" id="partner_model" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
         aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form method="post" action="{{ route('admin.front-pages.appInfo') }}" id="info-Section"
                      enctype="multipart/form-data">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Info Details Section</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                aria-hidden="true">&times;</span></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-12 form-group">
                                <label for="title_1">Title</label>
                                <input id="title_1" type="text" class="form-control" name="title_1" required="">
                            </div>
                            <div class="col-12 mb-3">
                                <img id="show-image" src="" class="img-fluid">
                            </div>
                            <div class="col-md-12 input-group">
                                <div class="custom-file">
                                    <input type="file" class="custom-file-input" id="updateImage3" name="image"
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

        const handleImageInput = (idnt) => {
            let val = $(`${idnt} [name=type]`).val();
            if (val == 'Professional' || val == "Services" || val == "Partner" || val == "Info") {
                $(`${idnt} .custom-file`).hide();
                return false;
            }
            $(`${idnt} .custom-file`).show();
            return true;
        }

        $("#create_model [name=type]").on('change', function () {
            handleImageInput('#create_model');
        });

        $("#edit_model [name=type]").on('change', function () {
            handleImageInput('#edit_model');
        });

        $('#create-Section').validate({
            rules: {
                title: {
                    required: true,
                    minlength: 3,
                    maxlength: 255
                },
                country_id: {
                    required: true,
                },
                type: {
                    required: true,
                },
                description: {
                    minlength: 3,
                    maxlength: 1000
                },
                image: {
                    required: handleImageInput('#create_model'),
                    accept: 'image/png,image/jpg,image/jpeg'
                }
            },
            messages: {
                title: {
                    required: "Please enter title",
                    minlength: "Title must be at least 3 characters long",
                    maxlength: "Title must be at most 50 characters long"
                },
                country_id: {
                    required: "Please select country",
                },
                type: {
                    required: "Please select type",
                },
                description: {
                    required: "Please enter description",
                    minlength: "Description must be at least 3 characters long",
                    maxlength: "Description must be at most 1000 characters long"
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
            var title = $(this).data('title');
            var type = $(this).data('type');
            var description = $(this).data('description');
            var image = $(this).data('image');
            var country_id = $(this).data('country_id');
            var url = "{{ route('admin.front-pages.update', ":id") }}";
            url = url.replace(':id', id);
            handleImageInput('#edit_model');
            $('#edit_model form').attr('action', url);
            $('#edit_model #title').val(title);
            $('#edit_model #type').val(type);
            $('#edit_model #description').val(description);
            $('#edit_model #country').val(country_id);
            $('#show-image').attr('src', image);
        });

        $('#update-Section').validate({
            rules: {
                title: {
                    required: true,
                    minlength: 3,
                    maxlength: 255
                },
                country_id: {
                    required: true,
                },
                type: {
                    required: true,
                },
                description: {
                    minlength: 3,
                    maxlength: 1000
                },
                image: {
                    required: handleImageInput('#edit_model'),
                    accept: 'image/png,image/jpg,image/jpeg'
                }
            },
            messages: {
                title: {
                    required: "Please enter title",
                    minlength: "Title must be at least 3 characters long",
                    maxlength: "Title must be at most 50 characters long"
                },
                country_id: {
                    required: "Please select country",
                },
                type: {
                    required: "Please select type",
                },
                description: {
                    required: "Please enter description",
                    minlength: "Description must be at least 3 characters long",
                    maxlength: "Description must be at most 1000 characters long"
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

        $('#app-Section').validate({
            rules: {
                user_android: {
                    required: true,
                    url: true
                },
                provider_android: {
                    required: true,
                    url: true
                },
                user_ios: {
                    required: true,
                    url: true
                },
                provider_ios: {
                    required: true,
                    url: true
                }
            },
            messages: {
                user_android: {
                    required: "please enter url",
                    url: "it must be url"
                },
                provider_android: {
                    required: "please enter url",
                    url: "it must be url"
                },
                user_ios: {
                    required: "please enter url",
                    url: "it must be url"
                },
                provider_ios: {
                    required: "please enter url",
                    url: "it must be url"
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
        })

        $('#updateImage1').on('change', function (e) {
            var fileName = $(this).val().split('\\').pop();
            $('#show-image').attr('src', URL.createObjectURL(e.target.files[0]));
            $(this).next('.custom-file-label').addClass("selected").html(fileName);
        });

        $('#updateImage').on('change', function () {
            var fileName = $(this).val().split('\\').pop();
            $(this).next('.custom-file-label').addClass("selected").html(fileName);
        });

        $('.info-obj').on('click', function () {
            var title_1 = $(this).data('title_1');
            var title_2 = $(this).data('title_2');
            var description_1 = $(this).data('description_1');
            var description_2 = $(this).data('description_2');
            $('#info-Section #title_1').val(title_1 ?? '');
            $('#info-Section #title_2').val(title_2 ?? '');
            $('#info-Section #description_1').val(description_1 ?? '');
            $('#info-Section #description_2').val(description_2 ?? '');
            $('#info-Section #show-image').attr('src', $(this).data('image'));

        });

        $('#updateImage3').on('change', function () {
            var fileName = $(this).val().split('\\').pop();
            $(this).next('.custom-file-label').addClass("selected").html(fileName);
        });

        $('.app-obj').on('click', function () {
            var user_android = $(this).data('user_android');
            var provider_android = $(this).data('provider_android');
            var user_ios = $(this).data('user_ios');
            var provider_ios = $(this).data('provider_ios');
            $('#app-Section #user_android').val(user_android);
            $('#app-Section #provider_android').val(provider_android);
            $('#app-Section #user_ios').val(user_ios);
            $('#app-Section #provider_ios').val(provider_ios);
        });

        $('#info-Section').validate({
            rules: {
                title_1: {
                    required: true,
                    minlength: 3,
                    maxlength: 255
                },
                title_2: {
                    required: true,
                    minlength: 3,
                    maxlength: 255
                },
                description_1: {
                    required: true,
                    minlength: 10,
                    maxlength: 1000
                },
                description_2: {
                    required: true,
                    minlength: 10,
                    maxlength: 1000
                },
                image: {
                    extension: "jpg|jpeg|png"
                }
            },
            messages: {
                title_1: {
                    required: "please enter title",
                    minlength: "title must be at least 3 characters long",
                    maxlength: "title cannot exceed 255 characters"
                },
                title_2: {
                    required: "please enter title",
                    minlength: "title must be at least 3 characters long",
                    maxlength: "title cannot exceed 255 characters"
                },
                description_1: {
                    required: "please enter description",
                    minlength: "description must be at least 10 characters long",
                    maxlength: "description cannot exceed 1000 characters"
                },
                description_2: {
                    required: "please enter description",
                    minlength: "description must be at least 10 characters long",
                    maxlength: "description cannot exceed 1000 characters"
                },
                image: {
                    extension: "please select jpg, jpeg or png file"
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
        })

        // modal events open and close
        $('.modal').on('show.bs.modal', function (e) {
            handleImageInput('#create_model');
        });
        $('.modal').on('hidden.bs.modal', function (e) {
            handleImageInput('#create_model');
        });

    </script>

@endsection
