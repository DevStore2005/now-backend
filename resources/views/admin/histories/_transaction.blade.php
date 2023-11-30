@php
    $head = ['Id', 'Payment Id', 'Amount','Payment Method', 'Type' ,'Created At'];
    $body = $data->map(function($history) {
        return [
            $history->id,
            $history->payment_id,
            $history->amount,
            $history->payment_method,
            $history->type,
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