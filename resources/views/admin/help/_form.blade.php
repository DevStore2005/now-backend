@extends('admin.layout')
@section('title', 'Help Pages')
@section('content')
    <div class="row mb-4 mt-20">
        <div class="col-md-12">
            <div class="card card-small">
                <div class="card-header border-bottom">
                    {{@$action == 'store' ? 'Create' : 'Edit'}} Help Page
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div id="success">
                            </div>
                        </div>
                    </div>
                    <form id="page"
                          action="{{ @$action == 'store' ? route('admin.help-pages.store', ['locale' => request()->query('locale')]) : route('admin.help-pages.update', ['help_page' => $helpPage->id,'locale' => request()->query('locale')]) }}"
                          method="POST">
                        @csrf
                        @if(@$helpPage)
                            @method('PUT')
                        @endif
                        <div class="default">
                            <label>Description</label>
                            <textarea id="mytextarea" name="description">{{@$helpPage->description}}</textarea>
                        </div>

                        <button type="submit" class="btn btn-primary mt-3"
                                id="save">{{@$action == 'edit' ? "Update" : "Save"}}</button>
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
