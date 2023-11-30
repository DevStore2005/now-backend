@extends('admin.layout')
@section('title', 'Create Seo')
@section('content')
    <div class="row mb-4 mt-20">
        <div class="col-md-12">
            <div class="card card-small">
                <div class="card-header border-bottom">
                    {{@$action == 'store' ? 'Create' : 'Edit'}} Seo
                </div>
                <div class="card-body">
                    <form id="blog"
                          action="{!! @$action === 'store' ?  route('admin.seos.store') : route('admin.seos.update', @$seo->id)  !!}"
                          method="POST"
                          enctype="multipart/form-data">
                        @if(@$action !== 'store')
                            @method('PUT')
                        @endif
                        @csrf
                        <div class="row">
                            <div class="form-group col-md-6">
                                <label for="title">OG Title</label> <strong class="text-danger"> *</strong>
                                <input type="text" name="og_title" id="title"
                                       value="{{@$action == 'store' ? "" : @$seo->og_title}}" class="form-control"
                                       placeholder="Title...">
                            </div>
                            <div class="form-group col-md-6">
                                <label for="page_name">Page Name</label>
                                <input type="text" name="page_name" id="page_name"
                                       value="{{@$action == 'store' ? "" : @$seo->og_title}}" class="form-control"
                                       placeholder="Page Name">
                            </div>
                            <div class="form-group col-md-6">
                                <label for="og_image" class="mt-3">OG Image</label> @if(@$action != 'edit')
                                @endif
                                <input type="file" accept=".gif,.png,.jpg,.jpeg" name="og_image"
                                       id="og_image" class="form-control" placeholder="Image">
                            </div>
                            <div class="form-group col-md-6">
                                <label for="og_description">OG Description</label>
                                <textarea class="form-control" name="og_description" id="og_description"
                                          rows="3">{{@$seo->og_description}}</textarea>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary float-right submit"
                                id="save">{{@$action == 'edit' ? "Update" : "Save"}}</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
