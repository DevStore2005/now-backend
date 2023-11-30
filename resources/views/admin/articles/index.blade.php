@extends('admin.layout')
@section('title', 'Articles')
@section('content')
<div class="row mb-4 mt-20">
    <div class="col-md-12">
	    <div class="card card-small">
	         <div class="card-header border-bottom">
	            <h6 class="m-0 pull-left">article links</h6>
                <a class="btn btn-success pull-right" href="{{route('admin.article.create')}}"><i class="fa fa-plus"></i> Add new article</a>
	        </div>
	        <div class="card-body" style="overflow-y: auto;">
	        	<table class="display table table-striped table-hover">
				    <thead>
				      	<tr>
				        	<th>Id</th>
				        	<th>title</th>
				        	<th>content</th>
				        	<th class="text-center">Action</th>
				      	</tr>
				    </thead>
				    <tbody>
                        @foreach($articles as $article)
				      	<tr>
				        	<td>{{ $article->id }}</td>
				        	<td>{{ $article->title }}</td>
				        	<td>{{ $article->content }}</td>
				        	<td class="text-center">
				        		<div class="input-group-btn action_group">
									   <li class="action_icon">
									      	<button type="button" class="btn btn-info btn-block " data-toggle="dropdown" aria-expanded="false"><i class="fa fa-ellipsis-v" aria-hidden="true" title="View"></i></button>
										    <ul class="dropdown-menu">
										        <li>
										        	<a href="{{route('admin.article.destroy', $article->slug)}}"
										        		onclick="return confirm('Are you sure you want to delete this Article?');"
									        		> 
									        			<i class="material-icons">delete</i>Delete
									        		</a> 
										        </li>
										        <li>
										        	<a href="{{route('admin.article.edit', $article->slug)}}"
									        		> 
									        			<i class="material-icons">edit</i>Edit
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

<script type="text/javascript">
//
</script>

@endsection