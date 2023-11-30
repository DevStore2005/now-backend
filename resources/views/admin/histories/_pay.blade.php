@php
    $head = ['Id', 'provider', 'Amount', 'Created At'];
    $body = $data->map(function($history) {
        return [
            $history->id,
            $history->provider->first_name.' '.$history->provider->last_name,
            $history->amount,
            $history->created_at,
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
<x-pagination :data="$pagingInfo" />