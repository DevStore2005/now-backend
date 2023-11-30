@extends('admin.layout')
@section('title', 'Create Countries')
@section('content')
    <div class="row mb-4 mt-20">
        <div class="col-md-12">
            <div class="card card-small">
                <div class="card-header border-bottom">
                    {{@$action == 'store' ? 'Create' : 'Edit'}} Country
                </div>
                <div class="card-body">
                    <form id="blog"
                          action="{!! @$action === 'store' ?  route('admin.countries.store') : route('admin.countries.update', @$country->id)  !!}"
                          method="POST"
                          enctype="multipart/form-data">
                        @if(@$action !== 'store')
                            @method('PUT')
                        @endif
                        @csrf
                        <div class="row">
                            <div class="form-group col-md-6">
                                <label for="name">Country Name</label>
                                <input type="text" name="name" id="name"
                                       value="{{@$action == 'store' ? "" : @$country->name}}" class="form-control"
                                       placeholder="Country Name...">
                                @error('name')
                                <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="form-group col-md-6">
                                <label for="iso2">Iso2</label>
                                <input type="text" name="iso2" id="iso2"
                                       value="{{@$action == 'store' ? "" : @$country->iso2}}" class="form-control"
                                       placeholder="Country Iso2">
                                @error('iso')
                                <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="form-group col-md-6">
                                <label for="iso3">Iso3</label>
                                <input type="text" name="iso3" id="iso3"
                                       value="{{@$action == 'store' ? "" : @$country->iso3}}" class="form-control"
                                       placeholder="Country Iso3">
                                @error('iso3')
                                <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="form-group col-md-6">
                                <label for="iso3">Currency</label>
                                <input type="text" name="currency" id="currency"
                                       value="{{@$action == 'store' ? "" : @$country->currency}}" class="form-control"
                                       placeholder="Currency...">
                                @error('currency')
                                <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="form-group col-md-6">
                                <label for="currency_name">Currency Name</label>
                                <input type="text" name="currency_name" id="currency_name"
                                       value="{{@$action == 'store' ? "" : @$country->currency_name}}"
                                       class="form-control"
                                       placeholder="Currency Name...">
                                @error('currency_name')
                                <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="form-group col-12">
                                <div class="form-group form-check">
                                    <input type="checkbox" name="is_default"
                                           {{ @$country->is_default ? 'checked' : '' }} class="form-check-input"
                                           id="is_default">
                                    <label class="form-check-label" for="is_default">Is Default</label>
                                </div>
                                <div class="form-group form-check">
                                    <input type="checkbox" name="is_active"
                                           {{ @$country->is_active ? 'checked' : '' }} class="form-check-input"
                                           class="form-check-input" id="is_active">
                                    <label class="form-check-label" for="is_active">Is Active</label>
                                </div>
                                <div class="form-group form-check">
                                    <input type="checkbox" name="stripe_enable"
                                           {{ @$country->stripe_enable ? 'checked' : '' }} class="form-check-input"
                                           class="form-check-input" id="stripe_enable">
                                    <label class="form-check-label" for="stripe_enable">Is Stripe Enable</label>
                                </div>
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
