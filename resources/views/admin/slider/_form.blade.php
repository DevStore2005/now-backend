@extends('admin.layout')
@section('title', 'Create Slider')
@section('content')
    <div class="row">
        <div class="col-12">
            @if(session()->has('success'))
                <div class="alert alert-success">
                    {{ session()->get('success') }}
                </div>
            @endif
            @if(session()->has('error'))
                <div class="alert alert-danger">
                    {{ session()->get('error') }}
                </div>
            @endif
        </div>
    </div>
    <div class="row mb-4 mt-20">
        <div class="col-md-12">
            <div class="card card-small">
                <div class="card-header border-bottom">
                    {{$action == 'store' ? 'Create' : 'Edit'}} Article
                </div>
                <div class="card-body">
                    <form id="blog"
                          action="{{ $action == 'store' ? route('admin.sliders.store', ['locale' => request()->query('locale')]) : route('admin.sliders.update', ['slider' => $slider->id,'locale' => request()->query('locale')]) }}"
                          method="POST" enctype="multipart/form-data">
                        @csrf
                        @if(@$slider)
                            @method('PUT')
                        @endif
                        <div class="row">
                            <div class="form-group col-md-6">
                                <label for="front_image" class="mt-3">Front Image</label> @if($action != 'edit')
                                    <strong class="text-danger"> *</strong>
                                @endif
                                <input type="file" accept=".gif,.png,.jpg,.jpeg" name="front_image"
                                       id="front_image" class="form-control" placeholder="Front Image">
                            </div>
                            <div class="form-group col-md-6">
                                <label for="bg_image" class="mt-3">BG Image</label> @if($action != 'edit')
                                @endif
                                <input type="file" accept=".gif,.png,.jpg,.jpeg" name="bg_image"
                                       id="bg_image" class="form-control" placeholder="BG Image">
                            </div>

                        </div>
                        <div class="default">
                            <label>Content</label>
                            <textarea id="mytextarea" name="description">{{@$slider->description}}</textarea>
                        </div>

                        <div class="form-group form-check">
                            <input type="checkbox" name="status"
                                   {{@$slider->status==1 ? 'checked': ''}}
                                   class="form-check-input" id="status">
                            <label class="form-check-label" for="status">Is Publish</label>
                        </div>
                        <hr>
                        <button type="submit" class="btn btn-primary float-right submit"
                                id="save">{{$action == 'edit' ? "Update" : "Save"}}</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

@endsection
@push('plugin-scripts')
    <script src="https://cdn.tiny.cloud/1/kzs2eo8g06ytwd6kqz5k0oxjn0dhicra68tisyajx418on7u/tinymce/6/tinymce.min.js"
            referrerpolicy="origin"></script>
    <script>
        tinymce.init({
            selector: '#mytextarea',
            height: 500,
        });
    </script>
@endpush
