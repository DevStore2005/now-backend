@extends('admin.layout')
@section('title', 'Providers')
@section('content')
    <div class="row mb-4 mt-20">
        <div class="col-md-12">
            <div class="card card-small">
                <div class="card-header border-bottom">
                    <h6 class="m-0 pull-left">Providers</h6>
                    <a href="{{ route('admin.users.download', ['role' => 'provider']) }}">
                        <button type="button" class="btn btn-primary pull-right">Download Profiles</button>
                    </a>
                </div>
                <div class="row">
                    <div class="col-12">
                        <div class="btn-group pull-right m-2 dropleft">
                            <button class="btn btn-secondary btn-sm dropdown-toggle" type="button"
                                    data-toggle="dropdown" aria-expanded="false">
                                {{ Request::get('type') ? Str::title(Request::get('type')). " Providers" : 'All Providers' }}
                            </button>
                            <div class="dropdown-menu">
                                <a class="dropdown-item"
                                   href="{!! route('admin.providers', ['page' => $data->currentPage(), 'type' => 'PENDING', 'locale' => request()->query('locale')]) !!}">Pending
                                    Providers</a>
                                <a class="dropdown-item"
                                   href="{!! route('admin.providers', ['page' => $data->currentPage(), 'type' => 'ACTIVE', 'locale' => request()->query('locale')]) !!}">Active
                                    Providers</a>
                                <a class="dropdown-item"
                                   href="{!! route('admin.providers', ['page' => $data->currentPage(), 'type' => 'SUSPENDED', 'locale' => request()->query('locale')]) !!}">Suspended
                                    Providers</a>
                                <a class="dropdown-item"
                                   href="{!! route('admin.providers', ['page' => $data->currentPage(), 'type' => 'VERIFIED', 'locale' => request()->query('locale')]) !!}">Verified
                                    Providers</a>
                                @if(Request::get('type') == 'OLDEST')
                                    <a class="dropdown-item"
                                       href="{!! route('admin.providers', ['page' => $data->currentPage(), 'locale' => request()->query('locale')]) !!}">Newest
                                        Providers</a>
                                @else
                                    <a class="dropdown-item"
                                       href="{!! route('admin.providers', ['page' => $data->currentPage(), 'type' => 'OLDEST', 'locale' => request()->query('locale')]) !!}">Oldest
                                        Providers</a>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-body" style="overflow-y: auto;">
                    <table class="display table table-striped table-hover">
                        <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Type</th>
                            <th>Status</th>
                            <th>Created At</th>
                            <th>Verified</th>
                            <th class="text-center">Action</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($data as $d)
                            <tr>
                                <td>{{$d->id}}</td>
                                <td>{{$d->first_name}} {{$d->last_name}}</td>
                                <td>{{$d->email}}</td>
                                <td>{{$d->phone}}</td>
                                <td>{{$d->provider_type}}</td>
                                <td>{{$d->status}}</td>
                                <td>{{ $d->created_at->format('y-m-d h:m:i') }}</td>
                                <td>
                                    <div class="custom-control custom-toggle custom-toggle-md mb-1">
                                        <input type="checkbox" id="customToggle_{{$d->id}}" name="customToggle"
                                               class="custom-control-input {!! !$d->verified_at ? 'is-invalid': "" !!}" {{$d->verified_at ? 'checked' : ""}}>
                                        <label class="custom-control-label" for="customToggle_{{$d->id}}"></label>
                                    </div>
                                </td>
                                <td>
                                    <div class="input-group-btn action_group">
                                        <li class="action_icon">
                                            <button type="button" class="btn btn-info btn-block " data-toggle="dropdown"
                                                    aria-expanded="false"><i class="fa fa-ellipsis-v" aria-hidden="true"
                                                                             title="View"></i></button>
                                            <ul class="dropdown-menu">
                                                <li>
                                                    <a href="{{route('admin.profiles.profile', $d)}}">
                                                        <i class="material-icons">visibility</i> View
                                                    </a>
                                                </li>
                                                <li>
                                                    <a href="{{route('admin.users.delete', $d->id)}}"
                                                       onclick="return confirm('Are you sure you want to delete this user?');"
                                                    >
                                                        <i class="material-icons">delete</i>Delete
                                                    </a>
                                                </li>
                                                <li>
                                                    @if($d->status == 'ACTIVE')
                                                        <a href="{{route('admin.user_update_status', ['suspended', $d->id])}}"
                                                           onclick="return confirm('Are you sure? you want to inactive this provider?')">
                                                            <i class="material-icons">block</i> Suspend
                                                        </a>
                                                    @else
                                                        <a href="{{route('admin.user_update_status', ['active', $d->id])}}"
                                                           onclick="return confirm('Are you sure? you want to activate this provider?')">
                                                            <i class="material-icons">check_circle</i> Active
                                                        </a>
                                                    @endif
                                                </li>
                                            </ul>
                                        </li>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                    <div class="text-center">
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
    </div>
    <script type="text/javascript">
        let _token = "{{csrf_token()}}";
        $('.custom-control-input').on('change', function () {
            let btn = $(this);
            let id = btn.attr('id').split('_')[1];
            let verified = btn.is(':checked') ? 1 : 0;
            var data = {
                verified,
                id,
                _token
            };
            $.ajax({
                url: "{{route('admin.user_update_verified')}}",
                type: "POST",
                data,
                success: function (res) {
                },
                error: function (err) {
                    if (btn.is(':checked')) {
                        btn.prop('checked', false);
                    } else {
                        btn.prop('checked', true);
                    }
                }
            });
        })
    </script>
@endsection
