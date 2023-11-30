@php
    $head = ['Id', 'Service Request Id', 'Commission', 'Created At', 'Updated At'];
    $body = $data->map(function($history) {
        return [
            $history->id,
            $history->service_request_id,
            $history->amount,
            $history->created_at,
            $history->updated_at
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