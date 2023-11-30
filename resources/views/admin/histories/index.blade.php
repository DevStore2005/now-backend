@extends('admin.layout')
@section('title', 'History')
@section('content')
<div class="row mb-4 mt-20">
    <div class="col-md-12">
	    <div class="card card-small">
	         <div class="card-header border-bottom">
	            <h6 class="m-0 ml-2 pull-left">{!! Str::ucfirst($type) !!}</h6>
	        </div>
	        <div class="card-body" style="overflow-y: auto;">
            @switch($type)
                @case('commission')
                    @include('admin.histories._commission', ['data' => $commissionHistory])
                    @break
                @case('credit')
                    @include('admin.histories._transaction', ['data' => $transactionHistory])
                    @break
                @case('pay')
                    @include('admin.histories._pay', ['data' => $transactionHistory])
                    @break
                @case('Provider History')
                    @include('admin.histories._services', ['data' => $serviceRequest])
                    @break
                @case('User History')
                    @include('admin.histories._services', ['data' => $serviceRequest])
                    @break
                @default
                    <div class="text-center">Not Found</div>
            @endswitch
	        </div>
  		</div>
    </div>
</div>
@endsection