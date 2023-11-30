@extends('admin.layout')
@section('title', $title)
@section('content')
    <div class="row mb-4 mt-20">
        <div class="col-md-12">
            <div class="card card-small">
                <div class="card-header border-bottom">
                    <h6 class="m-0 pull-left">{{ $title }}</h6>
                    <button type="button" class="btn btn-success pull-right" data-toggle="modal"
                        data-target="#create_model"><i class="fa fa-plus"></i> Add {{ $title }}</button>
                </div>
                <div class="card-body" style="overflow-y: auto;">
                    <table class="display table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Title</th>
                                <th>Image</th>
                                <th>Discription</th>
                                <th class="text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if (!$data->isEmpty())
                                @foreach ($data as $d)
                                    <tr>
                                        <td>{{ $d->id }}</td>
                                        <td>{{ $d->title }}</td>
                                        <td>
                                            <a href={{$d->image}} data-fancybox="images" data-caption="{!! $d->title !!}">
                                                <img style="width:40px; height:40px" src={{$d->image}} alt="" />
                                            </a>
                                        </td>
                                        <td>{{ $d->description }}</td>
                                        <td>
                                            <div class="input-group-btn action_group">
                                                <li class="action_icon">
                                                    <button type="button" class="btn btn-info btn-block "
                                                        data-toggle="dropdown" aria-expanded="false"><i
                                                            class="fa fa-ellipsis-v" aria-hidden="true"
                                                            title="View"></i></button>
                                                    <ul class="dropdown-menu">
                                                        <li>
                                                            <a 
                                                                href="javascript:;"
                                                                data-toggle="modal"
                                                                data-target="#update_model"
                                                                class="update-obj" data-id="{{ $d->id }}"
                                                                data-title="{{ $d->title }}"
                                                                data-image="{{ $d->image }}"
                                                                data-description="{{ $d->description }}"
                                                            > <i class="material-icons">edit</i> Edit</a>
                                                        </li>
                                                        <li>
                                                            <a href="{{route('admin.delete_service_content', ['serviceContent'=>$d])}}"
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
                <form id="content" method="post" action="{{ route('admin.sub_service_create_content') }}"
                    enctype="multipart/form-data">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Add {{ $title }}</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                aria-hidden="true">&times;</span></button>
                    </div>
                    <input type="text" name={{ $typeId }} value="{{ $id }}" hidden>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-12 form-group">
                                <label>Title</label>
                                <input type="text" class="form-control" value="{{ @old('title') }}" name="title"
                                    required="">
                                @error('title')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-12 form-group">
                                <label>Description</label>
                                <textarea type="text" class="form-control" rows="5" name="description" required="">{{ @old('description') }}</textarea>
                                @error('description')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-12 input-group">
                                <div class="custom-file">
                                    <input type="file" class="custom-file-input" id="image" name="image"
                                        accept="image/png,image/gif,image/jpeg" aria-describedby="inputGroupFileAddon01"
                                        required="">
                                    <label class="custom-file-label" id="imageName" for="image">Choose file</label>
                                </div>
                            </div>
                            <div class="col-md-12 input-group">
                            <div class="text-danger" id="imageError">image should be at least 682px * 480px or greater</div>
                            @error('image')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" id="submit" disabled class="btn btn-primary">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="update_model" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form id="content" method="post" action="{{ route('admin.sub_service_update_content', ":id") }}"
                    enctype="multipart/form-data">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Add {{ $title }}</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                aria-hidden="true">&times;</span></button>
                    </div>
                    <input type="text" name={{ $typeId }} value="{{ $id }}" hidden>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-12 form-group">
                                <label>Title</label>
                                <input type="text" class="form-control" value="{{ @old('title') }}" name="title"
                                    required="">
                                @error('title')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-12 form-group">
                                <label>Description</label>
                                <textarea type="text" class="form-control" rows="5" name="description" required="">{{ @old('description') }}</textarea>
                                @error('description')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-12 text-center mb-2">
                                <img src="" id="image" alt="" width="100%" height="250px">
                            </div>
                            <div class="col-md-12 input-group">
                                <div class="custom-file">
                                    <input type="file" class="custom-file-input" id="image" name="image"
                                        accept="image/png,image/gif,image/jpeg" aria-describedby="inputGroupFileAddon01">
                                    <label class="custom-file-label" id="imageName" for="image">Choose file</label>
                                </div>
                            </div>
                            <div class="col-md-12 input-group">
                            <div class="text-danger" id="imageError">image should be at least 682px * 480px or greater</div>
                            @error('image')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" id="submit" disabled class="btn btn-primary">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>



    <script type="text/javascript">
        const imageHandleChange = function(e) {
            let target = e.target;
            if (target.files.length) {
                var file = target.files[0];
                var reader = new FileReader();
                reader.readAsDataURL(file);
                reader.onload = function (e) {
                    var image = new Image();
                    image.src = e.target.result;
                    image.onload = function () {
                        var height = this.height;
                        var width = this.width;
                        console.log($(target).closest('.modal-body').next().find('#submit'));
                        if(height>=480 && width>=682){
                            $(target).next().html(file.name);
                            $(target).closest('.modal-body').find('#imageError').text('');
                            $(target).closest('.modal-body').next().find('#submit').attr('disabled', false);
                            return true;
                        } else{
                            $(target).next().html('Choose image');
                            $(target).closest('.modal-body').find('#imageError').text('image should be at least 682px * 480px or greater');
                            $(target).closest('.modal-body').next().find('#submit').attr('disabled', true);
                            return false;
                        }
                    };
                }
            }
        };
        $('#image').change(imageHandleChange);

        $('#content').validate({
            rules: {
                title: {
                    required: true,
                    minlength: 3,
                    maxlength: 100
                },
                description: {
                    required: true,
                    minlength: 3,
                    maxlength: 500
                },
                image: {
                    required: true,
                    extension: "png|jpeg|jpg|gif",
                }
            },
            messages: {
                title: {
                    required: "Please enter title",
                    minlength: "Title must be at least 3 characters long",
                    maxlength: "Title may not be greater 100 characters long"
                },
                description: {
                    required: "Please enter description",
                    minlength: "Description must be at least 3 characters long",
                    maxlength: "Description may not be greater 500 characters long"
                },
                image: {
                    extension: "Please upload file with png, jpeg, jpg or gif extension",
                }
            },
            errorPlacement: function (label, element, errorClass, validClass) {
                label.addClass('mt-1 tx-13 text-danger');
                label.insertAfter(element);
            },
            highlight: function (element, errorClass) {
                $(element).parent().addClass('validation-error')
                $(element).addClass('border-danger')
            },
            unhighlight: function (element, errorClass) {
                $(element).parent().removeClass('validation-error')
                $(element).removeClass('border-danger')
            }
        });

        $('.update-obj').on('click', function () {

            var id = $(this).data('id');
            var title = $(this).data('title');
            var description = $(this).data('description');
            var image = $(this).data('image');
            $('#update_model').find('form').attr('action', $('#update_model').find('form').attr('action').replace(':id', id))
            $('#update_model').find('input[name="title"]').val(title);
            $('#update_model').find('textarea[name="description"]').val(description);
            $('#update_model').find('img').attr('src', image);
            $('#update_model').find('button[type="submit"]').attr('disabled', false);
        });

        $('#update_model #image').change(imageHandleChange);

    </script>
@endsection
