@extends('admin.layout')
@section('title', 'Seo lists')
@section('content')
    <div class="row mb-4 mt-20">
        <div class="col-md-12">
            <div class="card card-small">
                <div class="card-header border-bottom">
                    <h6 class="m-0 pull-left">Title</h6>
                    <a href="{{ route('admin.seos.create') }}" type="button" class="btn btn-success pull-right"><i
                            class="fa fa-plus"></i> Add
                        new meta info
                    </a>
                </div>
                <div class="card-body" style="overflow-y: auto;">
                    <table class="display table table-striped table-hover">
                        <thead>
                        <tr>
                            <th>Id</th>
                            <th>Page Name</th>
                            <th>Og Image</th>
                            <th>OG Title</th>
                            <th>Og Description</th>
                            <th class="text-center">Action</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($seos as $seo)
                            <tr>
                                <td>{{@$seo->id}}</td>
                                <td>{{@$seo->page_name}}</td>
                                <td>
                                    <img src="{{ @$seo->og_image }}" width="100" alt="">
                                </td>
                                <td>{{@$seo->og_title}}</td>
                                <td>{{@$seo->og_description}}</td>
                                <td>
                                    <div class="input-group-btn action_group">
                                        <li class="action_icon">
                                            <button type="button" class="btn btn-info btn-block " data-toggle="dropdown"
                                                    aria-expanded="false"><i class="fa fa-ellipsis-v" aria-hidden="true"
                                                                             title="View"></i></button>
                                            <ul class="dropdown-menu">
                                                <li>
                                                    <a
                                                        href="{{ route('admin.seos.edit', $seo->id) }}"
                                                    >
                                                        <i class="material-icons">edit</i> Edit
                                                    </a>
                                                </li>
                                                <li>
                                                    <a href="javascript:void(0)"
                                                       class="delete-seos"
                                                       data-id="{{@$seo->id}}"
                                                    >
                                                        <i class="material-icons">delete</i> Delete
                                                    </a>
                                                    <form id="row-delete-form{{ @$seo->id }}" method="POST"
                                                          class="d-none"
                                                          action="{{ route('admin.seos.destroy', @$seo->id) }}">
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
    <script type="text/javascript">
        $('.delete-seos').on('click', function () {
            const id = $(this).data('id');
            $("#row-delete-form" + id).submit();
        });
    </script>
@endsection
