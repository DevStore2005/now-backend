@php
    $head = [
        'Id',
        'Provider Name',
        'User Name',
        'Service Name',
        'Service Type',
        'Status',
        'Working Status',
        'Completed',
        'Payment Status',
        'Payable Amount',
        'Created At'
    ];
    $body = $data->map(function($history) {
        return [
            $history->id,
            @$history->provider->first_name. ' ' .@$history->provider->last_name,
            @$history->user->first_name. ' ' .@$history->user->last_name,
            $history->requested_sub_service ? @$history->requested_sub_service->name : 'N/A',
            $history->is_quotation ? 'Quotation' : 'Hourly',
            $history->status,
            $history->working_status,
            $history->is_completed ? 'Yes' : 'No',
            $history->payment_status === 0 ? 'Pending' : 'Paid',
            $history->payable_amount ? $history->payable_amount : 0,
            $history->created_at->format('d-m-Y H:i:s'),
        ];
    });
    $pagingInfo = [
        'currentPage' => $data->currentPage(),
        'hasMore' => $data->hasMorePages(),
        'previousPage' => $data->previousPageUrl(),
        'nextPage' => $data->nextPageUrl()
    ];
@endphp

@include('admin.histories._table', ['head' => $head, 'body' => $body])
<x-pagination :data="$pagingInfo"/>
