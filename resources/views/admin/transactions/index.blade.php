@extends('admin.layout')
@section('title', 'Links')
@section('content')
    <div class="row mb-4 mt-20">
        <div class="col-md-12">
            <div class="card card-small">
                <div class="card-header border-bottom">
                    <h6 class="m-0 pull-left">Transactions</h6>
                    <button id="newLink" type="button"
                            class="btn btn-success pull-right">{{ isset($balance->available[0]) ? '$'.$balance->available[0]->amount : null}} </button>
                </div>

                <div class="card-body" style="overflow-y: auto;">
                    <table class="display table table-striped table-hover">
                        <thead>
                        <tr>
                            <th>Id</th>
                            <th>User Type</th>
                            <th>Amount</th>
                            <th>Amount Captured</th>
                            <th>Payment Method</th>
                            <th>Status</th>
                            <th>Payable</th>
                            <th>
                                Transaction Id
                            </th>
                            {{-- <th class="text-center">Action</th> --}}
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($transactions as $transaction)
                            <tr>
                                <td>{{ $transaction->id }}</td>
                                @if ($transaction->user_id)
                                    <td>User</td>
                                @elseif ($transaction->provider_id)
                                    <td>Provider</td>
                                @else
                                    <td></td>
                                @endif
                                <td>{{ $transaction->amount }}</td>
                                <td>{{ $transaction->amount_captured }}</td>
                                <td>{{ $transaction->payment_method }}</td>
                                <td>{{ $transaction->status }}</td>
                                <td>{{ $transaction->is_payable == 1 ? "Yes" : "No" }}</td>
                                <td>{{ $transaction->fw_transaction_id }}</td>
                                {{-- <td>
                                    <div class="input-group-btn action_group">
                                        <li class="action_icon">
                                           <button type="button" class="btn btn-info btn-block " data-toggle="dropdown" aria-expanded="false"><i class="fa fa-ellipsis-v" aria-hidden="true" title="View"></i></button>
                                                <ul class="dropdown-menu">
                                                    sdlkjfkjsdk
                                                </ul>
                                        </li>
                                    </div>
                                </td> --}}
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

@endsection
