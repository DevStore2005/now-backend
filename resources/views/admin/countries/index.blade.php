@extends('admin.layout')
@section('title', 'Countries Lists')
@section('content')
    <div class="row mb-4 mt-20">
        <div class="col-md-12">
            <div class="card card-small">
                <div class="card-header border-bottom">
                    <h6 class="m-0 pull-left">Title</h6>
                    <button type="button" class="btn btn-success pull-right" data-toggle="modal"
                            data-target="#create_model"><i class="fa fa-plus"></i> Add New Country
                    </button>
                </div>
                <div class="card-body" style="overflow-y: auto;">
                    <table class="display table table-striped table-hover">
                        <thead>
                        <tr>
                            <th>Id</th>
                            <th>Name</th>
                            <th>ISO2</th>
                            <th>ISO3</th>
                            <th>Currency</th>
                            <th>Currency Name</th>
                            <th>Is Default</th>
                            <th>Is Active</th>
                            <th>Is Stripe Enable</th>
                            <th class="text-center">Action</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($countries as $country)
                            <tr>
                                <td>{{@$country->id}}</td>
                                <td>{{@$country->name}}</td>
                                <td>{{@$country->iso2}}</td>
                                <td>{{@$country->iso3}}</td>
                                <td>{{@$country->currency}}</td>
                                <td>{{@$country->currency_name}}</td>
                                <td>{{@$country->is_default ? 'Yes' : 'No'}}</td>
                                <td>{{@$country->is_active ? 'Yes' : 'No'}}</td>
                                <td>{{@$country->stripe_enable ? 'Yes' : 'No'}}</td>
                                <td>
                                    <div class="input-group-btn action_group">
                                        <li class="action_icon">
                                            <button type="button" class="btn btn-info btn-block " data-toggle="dropdown"
                                                    aria-expanded="false"><i class="fa fa-ellipsis-v" aria-hidden="true"
                                                                             title="View"></i></button>
                                            <ul class="dropdown-menu">
                                                <li>
                                                    <a href="javascript:;" data-toggle="modal" data-target="#edit_model"
                                                       class="edit-obj"
                                                       data-id="{{$country->id}}"
                                                       data-name="{{$country->name}}"
                                                       data-iso2="{{$country->iso2}}"
                                                       data-iso3="{{$country->iso3}}"
                                                       data-currency="{{$country->currency}}"
                                                       data-currency_name="{{$country->currency_name}}"
                                                       data-is_default="{{$country->is_default}}"
                                                       data-is_active="{{$country->is_active}}"
                                                       data-stripe_enable="{{$country->stripe_enable}}"
                                                    ><i class="material-icons">edit</i> Edit</a>
                                                </li>
                                                <li>
                                                    <a href="javascript:void(0)"
                                                       class="delete-country"
                                                       data-id="{{@$country->id}}"
                                                    >
                                                        <i class="material-icons">delete</i> Delete
                                                    </a>
                                                    <form id="row-delete-form{{ @$country->id }}" method="POST"
                                                          class="d-none"
                                                          action="{{ route('admin.countries.destroy', @$country->id) }}">
                                                        @method('DELETE')
                                                        @csrf()
                                                    </form>
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
                <form method="post" id="create-new-service"
                      action="{{route('admin.countries.store')}}"
                      enctype="multipart/form-data">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Add Country</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                aria-hidden="true">&times;</span></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-12 form-group">
                                <label for="name">Name</label>
                                <input type="text" class="form-control" placeholder="Country name" name="name"
                                       required="">
                            </div>
                            <div class="col-md-12 form-group">
                                <label for="iso2">Iso2</label>
                                <input type="text" class="form-control" placeholder="Country iso2" name="iso2"
                                       required="">
                            </div>
                            <div class="col-md-12 form-group">
                                <label for="iso3">Iso3</label>
                                <input type="text" class="form-control" placeholder="Country iso3" name="iso3"
                                       required="">
                            </div>
                            <div class="col-md-12 form-group">
                                <label for="currency">Currency</label>
                                <input type="text" class="form-control" placeholder="Currency" name="currency"
                                       required="">
                            </div>
                            <div class="col-md-12 form-group">
                                <label for="currency_name">Currency name</label>
                                <input type="text" class="form-control" placeholder="Currency name" name="currency_name"
                                       required="">
                            </div>

                            <div class="form-group col-12">
                                <div class="form-group form-check">
                                    <input type="checkbox" name="is_default"
                                           class="form-check-input"
                                           id="is_default">
                                    <label class="form-check-label" for="is_default">Is Default</label>
                                </div>
                                <div class="form-group form-check">
                                    <input type="checkbox" name="is_active"
                                           class="form-check-input" id="is_active">
                                    <label class="form-check-label" for="is_active">Is Active</label>
                                </div>
                                <div class="form-group form-check">
                                    <input type="checkbox" name="stripe_enable"
                                           class="form-check-input" id="stripe_enable">
                                    <label class="form-check-label" for="stripe_enable">Is Stripe Enable</label>
                                </div>
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
                <form method="post"
                      action="{{route('admin.countries.update', '')}}"
                      id="edit-form">
                    @method('put')
                    @csrf
                    <input type="hidden" name="id">
                    <input type="hidden" name="locale" value="{{ request()->query('locale') }}">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Update Country</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                aria-hidden="true">&times;</span></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-12 form-group">
                                <label for="name">Name</label>
                                <input type="text" class="form-control" placeholder="Country name" name="name"
                                       required="">
                            </div>
                            <div class="col-md-12 form-group">
                                <label for="iso2">Iso2</label>
                                <input type="text" class="form-control" placeholder="Country iso2" name="iso2"
                                       required="">
                            </div>
                            <div class="col-md-12 form-group">
                                <label for="iso3">Iso3</label>
                                <input type="text" class="form-control" placeholder="Country iso3" name="iso3"
                                       required="">
                            </div>
                            <div class="col-md-12 form-group">
                                <label for="currency">Currency</label>
                                <input type="text" class="form-control" placeholder="Currency" name="currency"
                                       required="">
                            </div>
                            <div class="col-md-12 form-group">
                                <label for="currency_name">Currency name</label>
                                <input type="text" class="form-control" placeholder="Currency name" name="currency_name"
                                       required="">
                            </div>

                            <div class="form-group col-12">
                                <div class="form-group form-check">
                                    <input type="checkbox" name="is_default"
                                           class="form-check-input"
                                           id="is_default">
                                    <label class="form-check-label" for="is_default">Is Default</label>
                                </div>
                                <div class="form-group form-check">
                                    <input type="checkbox" name="is_active"
                                           class="form-check-input" id="is_active">
                                    <label class="form-check-label" for="is_active">Is Active</label>
                                </div>
                                <div class="form-group form-check">
                                    <input type="checkbox" name="stripe_enable"
                                           class="form-check-input" id="stripe_enable">
                                    <label class="form-check-label" for="stripe_enable">Is Stripe Enable</label>
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
        $('.delete-country').on('click', function () {
            const id = $(this).data('id');
            $("#row-delete-form" + id).submit();
        });
    </script>
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
                iso2: {
                    required: true,
                    minlength: 2,
                    maxlength: 2,
                },
                iso3: {
                    required: true,
                    minlength: 3,
                    maxlength: 3,
                },
                currency: {
                    required: true,
                    minlength: 3,
                },
                currency_name: {
                    required: true
                },
            },
            // messages: {
            //     name: {
            //         required: 'The field is required.'
            //     },
            // },
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
                },
                iso2: {
                    required: true,
                    minlength: 2,
                    maxlength: 2,
                },
                iso3: {
                    required: true,
                    minlength: 3,
                    maxlength: 3,
                },
                currency: {
                    required: true,
                    minlength: 3,
                },
                currency_name: {
                    required: true
                },
            },
            // messages: {
            //     name: {
            //         required: 'Please enter name'
            //     }
            // },
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
         * Set value in edit modal
         *
         * @returns void
         */
        $('.edit-obj').on('click', function () {
            $('#edit-form input[name=id]').val($(this).data('id'));
            $('#edit-form input[name=name]').val($(this).data('name'));
            $('#edit-form input[name=iso2]').val($(this).data('iso2'));
            $('#edit-form input[name=iso3]').val($(this).data('iso3'));
            $('#edit-form input[name=currency]').val($(this).data('currency'));
            $('#edit-form input[name=currency_name]').val($(this).data('currency_name'));

            if ($(this).data('is_default') == 1) {
                $('#edit-form input[name=is_default]').attr('checked', true);
            }
            if ($(this).data('is_active') == 1) {
                $('#edit-form input[name=is_active]').attr('checked', true);
            }
            if ($(this).data('stripe_enable') == 1) {
                $('#edit-form input[name=stripe_enable]').attr('checked', true);
            }
            var url = `{{route('admin.countries.update', '')}}/${$(this).data('id')}`
            $('#edit-form').attr('action', url);
        });
    </script>
@endsection



