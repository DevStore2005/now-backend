@extends('admin.layout')
@section('title', 'Blogs')
@section('content')
    <div class="row mb-4 mt-20">
        <div class="col-md-12">
            <div class="card card-small">
                <div class="card-header border-bottom">
                    <h6 class="m-0 pull-left">Blogs</h6>
                    <a href="{{route('admin.blog.create', ['locale' => request()->query('locale')])}}"
                       class="btn btn-success pull-right"><i class="fa fa-plus"></i> Add new Blog</a>
                </div>
                <div class="card-body" style="overflow-y: auto;">
                    <table class="display table table-striped table-hover">
                        <thead>
                        <tr>
                            <th>Id</th>
                            <th>title</th>
                            <th class="text-center">Action</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($blogs as $blog)
                            <tr>
                                <td>{{$blog->id}}</td>
                                <td>{{$blog->title}}</td>
                                <td class="text-center">
                                    <a href="{{route('admin.blog.edit', ['blog' => $blog, 'locale' => request()->query('locale')])}}"
                                       class="btn btn-primary btn-sm"><i
                                            class="fa fa-edit"></i></a>
                                    <a href="{{route('admin.blog.destroy', ['blog' => $blog, 'locale' => request()->query('locale')])}}"
                                       class="btn btn-danger btn-sm"><i
                                            class="fa fa-trash"></i></a>
                                </td>
                            </tr>
                        @endforeach
				    </tbody>
			    </table>
	        </div>
  		</div>
    </div>
</div>
@endsection
