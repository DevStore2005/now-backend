@extends('admin.layout')

@section('title', 'Dashboard')

@section('content')

<div class="grid gap-3">
  <div class="p-4 my-4 md:col-start-1 md:col-end-9 bg-white border border-gray-200 rounded-lg shadow sm:p-6 md:p-8 dark:bg-gray-800 dark:border-gray-700">
    <div class="text-xl font-bold mb-1">
      Member statistics
    </div>
    <div class="flex">
      <div class="flex-auto">
        <div>
          <span class="text-gray-500">Users</span>
          <div class="flex p-1">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6 inline-block">
              <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z" />
            </svg> <span class="text-xl font-medium inline-block px-2"> {!! $totalUser !!}</span>
          </div>
        </div>
      </div>
      <div class="flex-auto">
        <div>
          <span class="text-gray-500">Individual Providers</span>
          <div class="flex p-1">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
              <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
            </svg>
            <span class="text-xl font-medium inline-block px-2"> {!! $individualProvider !!}</span>
          </div>
        </div>
      </div>
      <div class="flex-auto">
        <div>
          <span class="text-gray-500">Companies</span>
          <div class="flex p-1">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
              <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 21h19.5m-18-18v18m10.5-18v18m6-13.5V21M6.75 6.75h.75m-.75 3h.75m-.75 3h.75m3-6h.75m-.75 3h.75m-.75 3h.75M6.75 21v-3.375c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21M3 3h12m-.75 4.5H21m-3.75 3.75h.008v.008h-.008v-.008zm0 3h.008v.008h-.008v-.008zm0 3h.008v.008h-.008v-.008z" />
            </svg>
            <span class="text-xl font-medium inline-block px-2"> {!! $companyProvider !!}</span>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="p-4 my-4 md:col-start-9 md:col-end-12 bg-white border border-gray-200 rounded-lg shadow sm:p-6 md:p-8 dark:bg-gray-800 dark:border-gray-700">
    <div class="text-xl font-bold mb-1">
      Services statistics
    </div>
    <div class="flex">
      <div class="flex-auto">
        <div>
          <span class="text-gray-500">Total Jobs</span>
          <div class="flex">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
              <path stroke-linecap="round" stroke-linejoin="round" d="M6.429 9.75L2.25 12l4.179 2.25m0-4.5l5.571 3 5.571-3m-11.142 0L2.25 7.5 12 2.25l9.75 5.25-4.179 2.25m0 0L21.75 12l-4.179 2.25m0 0l4.179 2.25L12 21.75 2.25 16.5l4.179-2.25m11.142 0l-5.571 3-5.571-3" />
            </svg> <span class="text-xl font-medium inline-block px-2"> {!! $totalServiceRequest !!}</span>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<div class="grid sm:grid-cols-1 md:grid-cols-5 gap-4">
  <div class="p-4 mt-4 col-span-3 bg-white border content-center border-gray-200 rounded-lg shadow sm:p-6 md:p-8 dark:bg-gray-800 dark:border-gray-700">
    <div class="text-xl font-bold mb-1">
      Service Request
    </div>
    <canvas id="my-chart"></canvas>
  </div>
  <div class="p-4 mt-4 col-span-2 bg-white justify-content border border-gray-200 rounded-lg shadow sm:p-6 md:p-8 dark:bg-gray-800 dark:border-gray-700">
    <div class="text-xl font-bold mb-1">
      Service Request
    </div>
    <canvas id="service-request"></canvas>
  </div>
</div>
<div class="grid sm:grid-cols-1 md:grid-cols-2 gap-4">
  <div class="p-4 mt-4 col-span-1 bg-white border content-center border-gray-200 rounded-lg shadow sm:p-6 md:p-8 dark:bg-gray-800 dark:border-gray-700">
    <div class="text-xl font-bold mb-1">
      Transaction
    </div>
    <canvas id="transaction"></canvas>
  </div>
  <div class="p-4 mt-4 col-span-1 bg-white justify-content border border-gray-200 rounded-lg shadow sm:p-6 md:p-8 dark:bg-gray-800 dark:border-gray-700">
    <div class="text-xl font-bold mb-1">
      Users, Providers and Companies
    </div>
    <canvas id="users"></canvas>
  </div>
</div>

 {{-- <div class="page-header row  py-4">
   <div class="col-12  text-center text-sm-left mb-0">
     <span class="text-uppercase page-subtitle">Dashboard</span>
     <h3 class="page-title">Dashboard Overview</h3>
     <div class="col-md-12">
      <div class="note_txt"></div>
     </div>
   </div>
 </div>

 <div class="row">
   <div class="col-lg-3 col-md-6 col-sm-6">
     <div class="card dashboard_card">
       <div class="card-header card-header-warning card-header-icon">
         <div class="card-icon">
          <i class="material-icons">person</i>
         </div>
         <p class="card-category stats-small__label text-uppercase">No. of Users</p>
         <h3 class="card-title user_data">{!! $totalUser !!}</h3>
       </div>
     </div>
   </div>
   <div class="col-lg-3 col-md-6 col-sm-6">
     <div class="card dashboard_card">
       <div class="card-header card-header-success card-header-icon">
         <div class="card-icon">
          <i class="material-icons">person</i>
         </div>
         <p class="card-category stats-small__label text-uppercase"><b>No. of Providers</b></p>
         <h3 class="card-title provider_data">{!! $totalProvider !!}</h3>
       </div>
     </div>
   </div>
   <div class="col-lg-3 col-md-6 col-sm-6">
     <div class="card dashboard_card">
       <div class="card-header card-header-success card-header-icon">
         <div class="card-icon">
          <i class="material-icons">money</i>
         </div>
         <p class="card-category stats-small__label text-uppercase"><b>Total Balance</b></p>
         <h3 class="card-title provider_data"> <a href="{{ route('admin.transaction.index') }}"> ${{ isset($balance->available[0]) ? $balance->available[0]->amount : null}} </a></h3>
       </div>
     </div>
   </div>
 </div> --}}
 
 <div class="row">
   {{-- <div class="col-lg-6 col-md-12 col-sm-12 mb-4">
     <div class="card card-small">
        <div class="card-header border-bottom">
          <h6 class="m-0">Total Transports</h6>
        </div>
       <div class="card-body pt-0">
        <canvas id="canvas"></canvas>
       </div>
     </div>
   </div> --}}

   {{-- <div class="col-lg-6 col-md-12 col-sm-12 mb-4">
     <a href="{{ route('admin.transaction.index') }}" class="card card-small">
       <div class="card-header border-bottom">
        <h6 class="m-0">Total Profit</h6>
       </div>
       <div class="card-body p-0">
         <ul class="list-group list-group-small list-group-flush">
           <li class="list-group-item d-flex px-3">
             <span class="text-semibold text-fiord-blue">Profit</span>
             <span class="ml-auto text-right text-semibold text-reagent-gray admin_credit">${{ isset($balance->available[0]) ? $balance->available[0]->amount : null}}</span>
           </li>
         </ul>
       </div>
     </a>
   </div> --}}
 </div>
<script>
$(document).ready(function(){
  
  
  const ctx = document.querySelector('#my-chart');
  new Chart(ctx, {
    type: 'bar',
    data: {
      labels: ['3rd Last Week', '2nd Lsat Week', 'Last Week', 'Current Week'],
      datasets: [{
        label: '# of Services Requested',
        data: [
          "{{ $week4 }}",
          "{{ $week3 }}",
          "{{ $week2 }}",
          "{{ $week1 }}",
        ],
        backgroundColor: [
          'rgb(54, 162, 235)',
          'rgb(75, 192, 192)',
          'rgb(255, 99, 132)',
          'rgb(255, 205, 86)'
        ],
        borderColor: [
          'rgb(54, 162, 235)',
          'rgb(75, 192, 192)',
          'rgb(255, 99, 132)',
          'rgb(255, 205, 86)'
        ],
        borderWidth: 2
      }]
    },
    options: {
      scales: {
        y: {
          beginAtZero: true
        }
      }
    }
  });

  const serviceRequest = document.querySelector('#service-request');
  new Chart(serviceRequest, {
    type: 'doughnut',
    data: {
      labels: [
        'Total '+ "{{ $totalServiceRequest }}",
        'Accepted '+ "{{ $acceptedRequest }}",
        'Rejected '+ "{{ $rejectedRequest }}",
        'Pending '+ "{{ $pendingRequest }}",
        'Cancelled '+ "{{ $cancelledRequest }}",
        'Completed ' + "{{ $completedRequest }}",
      ],
      datasets: [{
        label: 'Service Reequests',
        data: [
          "{{ $totalServiceRequest }}",
          "{{ $acceptedRequest }}",
          "{{ $rejectedRequest }}",
          "{{ $pendingRequest }}",
          "{{ $cancelledRequest }}",
          "{{ $completedRequest }}"
        ],
        backgroundColor: [
          'rgb(54, 162, 235)',
          'rgb(75, 192, 192)',
          'rgb(255, 99, 132)',
          'rgb(255, 205, 86)',
          'rgb(201, 203, 207)',
          'rgb(75, 150, 100)'
        ],
        hoverOffset: 4,
        cutout: '75%'
      }],
    }
  })


  const transaction = document.querySelector('#transaction');
  new Chart(transaction, {
    type: 'doughnut',
    data: {
      labels: [
        'Bonus',
        'Commission',
        'Credit',
        'Refund'
      ],
      datasets: [{
        label: 'Transaction',
        data: [
          "{{ $bonus }}",
          "{{ $commission }}",
          "{{ $credit }}",
          "{{ $refund }}"
        ],
        backgroundColor: [
          'rgb(54, 162, 235)',
          'rgb(75, 192, 192)',
          'rgb(255, 99, 132)',
          'rgb(75, 150, 100)'
        ],
        hoverOffset: 4,
        cutout: '75%'
      }],
    }
  });

  const users = document.querySelector('#users');
  new Chart(users, {
    type: 'pie',
    data: {
      labels: [
        'Total Users',
        'Total Providers',
        'Total Companies'
      ],
      datasets: [{
        label: 'Users',
        data: [
          "{{ $totalUser }}",
          "{{ $individualProvider }}",
          "{{ $companyProvider }}"
        ],
        backgroundColor: [
          'rgb(54, 162, 235)',
          'rgb(75, 192, 192)',
          'rgb(255, 99, 132)'
        ]
      }],
    }
  });
});
</script>
@endsection
@push('custom-scripts')
  <script src="https://cdn.tailwindcss.com?plugins=forms,typography,aspect-ratio,line-clamp"></script>
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
@endpush
