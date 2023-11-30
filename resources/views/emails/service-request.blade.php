@component('mail::message')
@if ($to_user == "user")
# Your booking request.
@endif
@if ($to_user == "provider")
# New booking request.
@endif

Hi **{!! $serviceRequest->{$to_user}->first_name !!}**,

@if ($to_user == "user")
You have booked a service request, you’ll be notified if the provider accept or cancel request.
@endif
@if ($to_user == "provider")
You have a new booking request, please accept or cancel request.
@endif

## Order Summary
@if(!$serviceRequest->is_quotation)
@component('mail::table')
<table class="custom-table">
    <tr>
        <th>Service Request ID</th>
        <td>{!! $serviceRequest->id ?? "" !!}</td>
    </tr>
    <tr>
        <th>Service Name</th>
        <td>{!! $serviceRequest->sub_service ?? "" !!}</td>
    </tr>
    @if ($serviceRequest->date)
        <tr>
            <th>Date</th>
            <td>{!! $serviceRequest->date ?? "" !!}</td>
        </tr>
    @endif
    @if ($serviceRequest->book_time_slots->count() > 0)
        <tr>
            <th>Time Slot</th>
            <td>{!! $serviceRequest->book_time_slots->first() ? $serviceRequest->book_time_slots->first()->start." - ".$serviceRequest->book_time_slots->first()->end :  "" !!}</td>
        </tr>
    @endif
    @if ($to_user == "provider")
        <tr>
            <th>Customer</th>
            <td>{!! $serviceRequest->user->first_name." ".$serviceRequest->user->last_name ?? "" !!}</td>
        </tr>
    @endif
    @if ($to_user == "user")
        <tr>
            <th>Provider</th>
            <td>{!! $serviceRequest->provider->first_name." ".$serviceRequest->provider->last_name ?? "" !!}</td>
        </tr>
    @endif
    <tr>
        <th>Hours</th>
        <td>{!! $serviceRequest->hours ?? "" !!}</td>
    </tr>
    <tr>
        <th>Booking Status</th>
        <td>{!! $serviceRequest->status ?? "" !!}</td>
    </tr>
    <tr>
        <th>Address</th>
        <td>{!! $serviceRequest->address ?? "" !!}</td>
    </tr>
    <tr>
        <th>Amount</th>
        <td>{!! $serviceRequest->paid_amount ? "$".$serviceRequest->paid_amount : ""!!}</td>
    </tr>
</table>
@endcomponent

@if ($serviceRequest->providers_subscription)
### Plan Details
@component('mail::table')
    <table class="custom-table">
        <tr>
            <th>type</th>
            <td>{!! $serviceRequest->providers_subscription->type ?? "" !!}</td>
        </tr>
        <tr>
            <th>Off</th>
            <td>%{!! $serviceRequest->providers_subscription->off ?? "" !!}</td>
        </tr>
        <tr class="without">
            <th>Duration</th>
            <td>{!! $serviceRequest->providers_subscription->duration ?? "" !!}</td>
        </tr>
    </table>
@endcomponent


@if ($serviceRequest->providers_subscription)
### Plan Schadules
@component('mail::table')
    <table class="custom-table">
        @foreach ($serviceRequest->providers_subscription->subscription_histories as $h)
            <tr @if ($loop->last)
                    class="without"
                @endif>
                <th>{{ $loop->index+1 }} deduction</th>
                <td>{!! $h->status ?? "Pending" !!}</td>
                <td>{!! $h->deduction_date ?? "" !!}</td>
            </tr>
        @endforeach
    </table>
@endcomponent
@endif
@endif
@else
@component('mail::table')
    <table class="custom-table">
        <tr>
            <th>Service Request ID</th>
            <td>{!! $serviceRequest->id ?? "" !!}</td>
        </tr>
        <tr>
            <th>Service Name</th>
            <td>{!! $serviceRequest->sub_service ?? "" !!}</td>
        </tr>
        @if ($serviceRequest->date)
            <tr>
                <th>Date</th>
                <td>{!! $serviceRequest->date ?? "" !!}</td>
            </tr>
        @endif
        @if ($to_user == "provider")
            <tr>
                <th>Customer</th>
                <td>{!! $serviceRequest->user->first_name." ".$serviceRequest->user->last_name ?? "" !!}</td>
            </tr>
        @endif
        @if ($to_user == "user")
            <tr>
                <th>Provider</th>
                <td>{!! $serviceRequest->provider->first_name." ".$serviceRequest->provider->last_name ?? "" !!}</td>
            </tr>
        @endif
        <tr>
            <th>Booking Status</th>
            <td>{!! $serviceRequest->status ?? "" !!}</td>
        </tr>
        <tr>
            <th>Address</th>
            <td>{!! $serviceRequest->address ?? "" !!}</td>
        </tr>
        @if ($serviceRequest->quotation_info)
            @if ($serviceRequest->quotation_info->name)
                <tr>
                    <th>Name</th>
                    <td>{!! $serviceRequest->quotation_info->name!!}</td>
                </tr>
            @endif
            @if ($serviceRequest->quotation_info->email)
                <tr>
                    <th>Email</th>
                    <td>{!! $serviceRequest->quotation_info->email!!}</td>
                </tr>
            @endif
            @if ($serviceRequest->quotation_info->phone)
                <tr>
                    <th>Phone</th>
                    <td>{!! $serviceRequest->quotation_info->phone!!}</td>
                </tr>
            @endif
            <tr>
                <th>Details</th>
                <td>{!! $serviceRequest->quotation_info->detail!!}</td>
            </tr>
        @endif
    </table>
@endcomponent
@endif
    {{-- <div class="row">
        <div class="col-md-12 bg-dark">
            <h1 class="text-center p-1 text-white">{!!$serviceRequest->user->first_name .", needs ". $serviceRequest->sub_service ." in ".$serviceRequest->address !!}</h1>
        </div>
        <div class="col-md-12 fw-bold fs-1 text-center">
            {!! $serviceRequest->user->first_name ." ". $serviceRequest->user->last_name !!}
        </div>
        <div class="card p-0">
          <img class="card-img-top img-fluid" src="{!! config('app.url')."/admin/assets/img/location.png" !!}" alt="Title">
          <div class="card-body">
            <div class="text-center profile">
                <img src="{!! $serviceRequest->user->image ? config('app.url').$serviceRequest->user->image : config('app.url')."/admin/assets/img/avatar.png" !!}" alt="">
            </div>
            <h5 class="card-title fs-2">{!!@$serviceRequest->user->first_name .", needs ". $serviceRequest->sub_service ." in ".$serviceRequest->address !!}.</h5>
            <div class="card-text fs-4">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-geo-alt" viewBox="0 0 16 16">
                    <path d="M12.166 8.94c-.524 1.062-1.234 2.12-1.96 3.07A31.493 31.493 0 0 1 8 14.58a31.481 31.481 0 0 1-2.206-2.57c-.726-.95-1.436-2.008-1.96-3.07C3.304 7.867 3 6.862 3 6a5 5 0 0 1 10 0c0 .862-.305 1.867-.834 2.94zM8 16s6-5.686 6-10A6 6 0 0 0 2 6c0 4.314 6 10 6 10z"/>
                    <path d="M8 8a2 2 0 1 1 0-4 2 2 0 0 1 0 4zm0 1a3 3 0 1 0 0-6 3 3 0 0 0 0 6z"/>
                </svg> {!! $serviceRequest->address !!}
            </div>
                @if ($serviceRequest->is_quotation && $serviceRequest->quotation_info->date)
                    <div class="card-text fs-4"><i class="bi bi-calendar-plus"></i> 
                    {!! $serviceRequest->quotation_info->date !!}
                    </div>
                @elseif($serviceRequest->time_slots->isNotEmpty())
                    N/A
                @endif
            <div class="text-center">
                <button class="btn btn-primary p-2 fs-4 my-3" type="button">View opportunity details</button>
            </div>
          </div>
        </div>
    </div> --}}
@endcomponent
