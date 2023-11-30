@extends('admin.layout')
@section('title', 'Restaurants')
@section('content')
<div class="row mb-4 mt-20">
    <div class="col-md-12">
	    <div class="card card-small">
	         <div class="card-header border-bottom">
	            <h6 class="m-0 pull-left">Restaurants</h6>
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
					        	<td>
					        		<div class="input-group-btn action_group">
									   <li class="action_icon">
									      	<button type="button" class="btn btn-info btn-block " data-toggle="dropdown" aria-expanded="false"><i class="fa fa-ellipsis-v" aria-hidden="true" title="View"></i></button>
										    <ul class="dropdown-menu">
												 <li>
										        	<a href="{{route('admin.profiles.profile', $d)}}">
									        			<i class="material-icons">visibility</i>  View
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
										        		<a href="{{route('admin.user_update_status', ['inactive', $d->id])}}" onclick="return confirm('Are you sure? you want to inactive this service?')"><i class="material-icons">info</i>Inactive</a>
										        	@else
										        		<a href="{{route('admin.user_update_status', ['active', $d->id])}}" onclick="return confirm('Are you sure? you want to activate this service?')"><i class="material-icons">info</i>Active</a>
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
	        </div>
  		</div>
    </div>
</div>
@endsection
