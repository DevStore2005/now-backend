@extends('admin.layout')
@section('title', 'Social Links')
@section('content')
    <div class="row mb-4 mt-20">
        <div class="col-md-12">
            <div class="card card-small">
                <div class="card-header border-bottom">
                    <h6 class="m-0 pull-left">Social links</h6>
                    <button id="newLink" type="button" class="btn btn-success pull-right"><i class="fa fa-plus"></i> Add
                        new social link
                    </button>
                </div>
                <div class="card-body" style="overflow-y: auto;">
                    <table class="display table table-striped table-hover">
                        <thead>
                        <tr>
                            <th>Id</th>
                            <th>Type</th>
                            <th>Name</th>
                            <th>Url</th>
                            <th class="text-center">Action</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($links as $link)
                            <tr>
                                <td>{{$link->id}}</td>
                                <td>{{$link->type}}</td>
                                <td>{{$link->name}}</td>
                                <td style="max-width: 20rem;"><a href="{{$link->url}}"
                                                                 target="_blank">{{$link->url}}</a></td>
                                <td>
                                    <div class="input-group-btn action_group">
                                        <li class="action_icon">
                                            <button type="button" class="btn btn-info btn-block " data-toggle="dropdown"
                                                    aria-expanded="false"><i class="fa fa-ellipsis-v" aria-hidden="true"
                                                                             title="View"></i></button>
                                            <ul class="dropdown-menu">
                                                <li>
                                                    <a
                                                        href="javascript:void(0)"
                                                        class="edit"
                                                        data-id="{!! $link->id !!}"
                                                        data-type="{!! $link->type !!}"
                                                        data-name="{!! $link->name !!}"
                                                        data-url="{!! $link->url !!}"
                                                        {{-- data-description="{!! $link->description !!}" --}}
                                                    >
                                                        <i class="material-icons">edit</i> Edit
                                                    </a>
                                                </li>
                                                <li>
                                                    <a href="{{route('admin.link.delete', $link->id)}}"
                                                       onclick="return confirm('Are you sure you want to delete this link?');"
                                                    >
                                                        <i class="material-icons">delete</i> Delete
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

    <div class="modal" id="create_model" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add new Link</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="linkForm" action="{{route('admin.link.store', ['locale' => request()->query('locale') ])}}"
                      method="post">
                    <div class="modal-body">
                        @csrf
                        <div class="form-group">
                            <label for="type">Social Type</label>
                            <select name="type" id="type" class="form-control">
                                <option value="">Select Social Type</option>
                                <option value="FACEBOOK">Facebook</option>
                                <option value="INSTAGRAM">Instagram</option>
                                <option value="TWITTER">Twitter</option>
                                <option value="LINKEDIN">Linkedin</option>
                                <option value="WHATS_APP">Whats App</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="name">Name</label>
                            <input type="text" placeholder="Name" name="name" id="name" class="form-control">
                        </div>
                        <div class="form-group">
                            <label for="url">Url</label>
                            <input type="url" placeholder="Url" name="url" id="url" class="form-control">
                        </div>
                        {{-- <div class="form-group">
                            <label for="description">Description</label>
                            <textarea required name="description" id="description" cols="30" rows="10" class="form-control"></textarea>
                        </div> --}}
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Save changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>


    <script type="text/javascript">

        const validator = $('#linkForm').validate({
            rules: {
                type: {
                    required: true
                },
                name: {
                    maxlength: 250
                },
                url: {
                    required: true
                }
            },
            messages: {
                page: {
                    required: "Please select a page"
                },
                name: {
                    required: "Please enter a name"
                },
                url: {
                    required: "Please enter a url"
                }
            },
            errorPlacement: function (label, element, errorClass, validClass) {
                label.addClass('invalid-feedback');
                // let id =`${element.attr('id')}`;
                // if(element.attr('name') == `options[${id.substr(id.length - 1)}]`) {
                // 	label.insertAfter(element.parent(this.currentElements));
                // } else {
                label.insertAfter(element);
                // }
            },
            highlight: function (element, errorClass) {
                // $(element).parent().addClass('validation-error')
                $(element).addClass('is-invalid').removeClass('is-valid');
            },
            unhighlight: function (element, errorClass) {
                // $(element).parent().removeClass('validation-error')
                $(element).removeClass('is-invalid').addClass('is-valid');
            },
            submitHandler: function (form) {
                if (!$(form).valid()) return false;
                $(form).find('button[type="submit"]').attr('disabled', true).html('<i class="fa fa-spinner fa-pulse"></i> Please wait...');
                form.submit();
            }
        });

        $('#newLink').on('click', function () {
            validator.resetForm();
            $('#linkForm').trigger('reset');
            $('#create_model').modal('show');
        });

        $('.edit').on('click', function () {
            $('#create_model').modal('show');
            form = $('#linkForm');
            form.append('<input type="hidden" name="_method" value="PUT">');
            form.attr('action', `{{route('admin.link.update', "")}}/${$(this).data('id')}`);
            form.find('#type').val($(this).data('type')).trigger("change");
            form.find('#name').val($(this).data('name'));
            form.find('#url').val($(this).data('url'));
        });

        $('#create_model').on('hidden.bs.modal', function () {
            $('#linkForm').trigger('reset');
            $('#linkForm').find('input[name="_method"]').remove();
            $('#linkForm').attr('action', "{{route('admin.link.store')}}");
        });
    </script>

@endsection
