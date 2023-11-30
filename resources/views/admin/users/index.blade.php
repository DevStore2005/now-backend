@extends('admin.layout')
@section('title', 'Users')
@section('content')
<div class="row mb-4 mt-20">
    <div class="col-md-12">
	    <div class="card card-small">
	         <div class="card-header border-bottom">
	            <h6 class="m-0 pull-left">Users</h6>
				<a href="{{ route('admin.users.download', ['role' => 'user', 'locale' => request()->query('locale')]) }}">
					<button type="button" class="btn btn-primary pull-right">Download Profiles</button>
				</a>
	        </div>
			<div class="row">
				<div class="col-12">
					<div class="btn-group pull-right m-2 dropleft">
						<button class="btn btn-secondary btn-sm dropdown-toggle" type="button" data-toggle="dropdown" aria-expanded="false">
							{{ Request::get('type') ? Str::title(Request::get('type')). " Users" : 'All Users' }}
						</button>
						<div class="dropdown-menu">
							<a class="dropdown-item" href="{!! route('admin.users', ['page' => $data->currentPage(), 'type' => 'PENDING', 'locale' => request()->query('locale')]) !!}">Pending Users</a>
							<a class="dropdown-item" href="{!! route('admin.users', ['page' => $data->currentPage(), 'type' => 'ACTIVE', 'locale' => request()->query('locale')]) !!}">Active Users</a>
							<a class="dropdown-item" href="{!! route('admin.users', ['page' => $data->currentPage(), 'type' => 'SUSPENDED', 'locale' => request()->query('locale')]) !!}">Suspended Users</a>
							<a class="dropdown-item" href="{!! route('admin.users', ['page' => $data->currentPage(), 'type' => 'VERIFIED', 'locale' => request()->query('locale')]) !!}">Verified Users</a>
							@if(Request::get('type') == 'OLDEST')
								<a class="dropdown-item" href="{!! route('admin.users', ['page' => $data->currentPage(), 'locale' => request()->query('locale')]) !!}">Newest Users</a>
							@else
								<a class="dropdown-item" href="{!! route('admin.users', ['page' => $data->currentPage(), 'type' => 'OLDEST', 'locale' => request()->query('locale')]) !!}">Oldest Users</a>
							@endif
						</div>
					</div>
				</div>
			</div>
	        <div class="card-body" style="overflow-y: auto;">
				<div class="row">
				<div class="col-12">
	        	<table id="user-table" class="display table table-striped table-hover">
				    <thead>
				      	<tr>
				        	<th>Id</th>
				        	<th>Name</th>
				        	<th>Email</th>
				        	<th>Phone</th>
				        	<th>Zip Code</th>
				        	<th>Status</th>
							<th>Created At</th>
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
					        	<td>{{$d->zip_code}}</td>
					        	<td>{{$d->status}}</td>
								<td>{{$d->created_at->format('y-m-d h:m:i')}}</td>
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
									        			<i class="material-icons">delete</i>  Delete
									        		</a>
										        </li>
												<li>
													<li>
														@if($d->status == 'ACTIVE')
															<a href="{{route('admin.user_update_status', ['suspended', $d->id])}}" onclick="return confirm('Are you sure? you want to suspend this user?')">
																<i class="material-icons">block</i>  Suspend
															</a>
														@elseif($d->status == 'SUSPENDED' || $d->status == 'PENDING')
															<a href="{{route('admin.user_update_status', ['active', $d->id])}}" onclick="return confirm('Are you sure? you want to activate this user?')">
																<i class="material-icons">check_circle</i>  Active
															</a>
														@endif
													</li>
													{{-- <a href="#" id="changeStatus" data-urls='{"active": "{{ route("admin.user_update_status", ["ACTIVE", $d->id]) }}","inactive":"{{ route("admin.user_update_status", ["INACTIVE", $d->id]) }}"}'>
														<i class="material-icons">edit</i>  Change Status
													</a> --}}
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
				]" />
				</div>
				</div>
	        </div>
  		</div>
    </div>
</div>

<script type="text/javascript">
	// $('#changeStatus').click(function(e){
	// 	e.preventDefault();
	// 	var url = $(this).data('urls');
	// 	swal.fire({
	// 		title: 'Change Status',
	// 		html: `<div class="form-group">
	// 				<label for="status">Status</label>
	// 				<select class="form-control" id="status">
	// 					<option value="${url.active}">Active</option>
	// 					<option value="${url.inactive}">Inactive</option>
	// 				</select>
	// 			</div>`,
	// 		showCancelButton: true,
	// 		confirmButtonText: 'Submit',
	// 		cancelButtonText: 'Cancel',
	// 		showLoaderOnConfirm: true,
	// 		allowOutsideClick: false
	// 	}).then(function (result) {
	// 		if(result.value){
	// 			var status = $('#status').val();
	// 			let form = new FormData();
	// 			form.append('_token', '{{csrf_token()}}');
	// 			form.append('status', status);
	// 			form.submit('POST');
	// 		}
	// 	});
	// });
</script>

@endsection
@push('plugin-scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@endpush
@push('custom-scripts')
<script src="{{ asset('/js/sweet-alert.js') }}"></script>
@endpush
