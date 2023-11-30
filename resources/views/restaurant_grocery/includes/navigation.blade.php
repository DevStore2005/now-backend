@if($routes)
<div class="breadcrumb-bar">
    <div class="container-fluid">
        <div class="row align-items-center">
            <div class="col-md-12 col-12">
                <nav aria-label="breadcrumb" class="page-breadcrumb">
                    <ol class="breadcrumb">
                        @php
                            $last = end($routes);
                        @endphp
                        @foreach($routes as $key => $route)
                            @if($key != $last)
                                @foreach($route as $name => $value)
                                    <li class="breadcrumb-item"><a href="{!! $value !!}">{!! $name !!}</a></li>
                                @endforeach
                            @else
                                @foreach($route as $name => $value)
                                    <li class="breadcrumb-itema ctive" aria-current="page"><a href="{!! $value !!}">{!! $name !!}</a></li>
                                @endforeach
                            @endif
                        @endforeach
                        {{-- <li class="breadcrumb-item active" aria-current="page">Product Upload</li> --}}
                    </ol>
                </nav>
                <h2 class="breadcrumb-title">Product Upload</h2>
            </div>
        </div>
    </div>
</div>
@endif