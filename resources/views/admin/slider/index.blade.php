@extends('admin.layout')
@section('title', 'Sliders')
@section('content')
    <div class="row mb-4 mt-20">
        <div class="col-md-12">
            <div class="card card-small">
                <div class="card-header border-bottom">
                    <h6 class="m-0 pull-left">Sliders</h6>
                    <a href="{{route('admin.sliders.create', ['locale' => request()->query('locale')])}}"
                       class="btn btn-success pull-right"><i class="fa fa-plus"></i> Add new Slider</a>
                </div>
                <div class="card-body" style="overflow-y: auto;">
                    <table class="display table table-striped table-hover">
                        <thead>
                        <tr>
                            <th>Id</th>
                            <th>Description</th>
                            <th>Front Image</th>
                            <th>Bg Image</th>
                            <th>Is Publish</th>
                            <th class="text-center" width="200">Action</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($sliders as $slider)
                            <tr>
                                <td>{{$slider->id}}</td>
                                <td>
                                    {!! $slider->description !!}
                                </td>
                                <td>
                                    @if(@$slider->front_image)
                                        <img width="100" src="{{ @$slider->front_image }}" alt="front image">
                                    @else
                                        <span>No Image</span>
                                    @endif
                                </td>
                                <td>
                                    @if(@$slider->bg_image)
                                        <img width="100" src="{{ @$slider->bg_image }}" alt="background image">
                                    @else
                                        <span>No Image</span>
                                    @endif
                                </td>
                                <td>
                                    {{ @$slider->status ? 'Yes': 'No' }}
                                </td>
                                <td class="text-center">
                                    <a href="{{route('admin.sliders.status.change', ['slider' => $slider, 'locale' => request()->query('locale')])}}"
                                       class="btn btn-primary btn-sm mb-2">Status Change</a>
                                    <a href="{{route('admin.sliders.edit', ['slider' => $slider, 'locale' => request()->query('locale')])}}"
                                       class="btn btn-primary btn-sm mb-2"><i
                                            class="fa fa-edit"></i></a>
                                    <a href="javascript:void(0)"
                                       class="btn btn-danger btn-sm delete-slider mb-2"
                                       data-id="{{@$slider->id}}"
                                    >
                                        <i
                                            class="fa fa-trash"></i>
                                    </a>
                                    <form id="row-delete-form{{ @$slider->id }}" method="POST"
                                          class="d-none"
                                          action="{{ route('admin.sliders.destroy', ['slider' => $slider, 'locale' => request()->query('locale')]) }}">
                                        @method('DELETE')
                                        @csrf()
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script type="text/javascript">
        $('.delete-slider').on('click', function () {
            const id = $(this).data('id');
            $("#row-delete-form" + id).submit();
        });
    </script>
@endsection
