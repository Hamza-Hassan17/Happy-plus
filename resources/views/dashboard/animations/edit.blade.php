@extends('layouts.master')

@section('title', __('Edit Animation'))

@section('css')
@endsection


@section('breadcrumb-items')
    <li class="breadcrumb-item"><a href="{{ route('dashboard.animations.index') }}">{{ __('Animations') }}</a></li>
    <li class="breadcrumb-item active">{{ __('Edit') }}</li>
@endsection
@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="card mb-6">
            <div class="card-body pt-4">
                <form method="POST" action="{{ route('dashboard.animations.update', $animation->id) }}" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="row p-5">
                        <h3>{{ __('Edit Animation') }}</h3>
                        <div class="mb-4 col-md-12">
                            <label for="name" class="form-label">{{ __('Animation Name') }}</label><span
                                class="text-danger">*</span>
                            <input class="form-control @error('name') is-invalid @enderror" type="text" id="name"
                                name="name" required placeholder="{{ __('Enter name') }}" autofocus
                                value="{{ old('name', $animation->name) }}" />
                            @error('name')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                        <div class="mb-4 col-md-6">
                            <label for="thumbnail" class="form-label">{{ __('Thumbnail') }}</label>
                            <input class="form-control @error('thumbnail') is-invalid @enderror" type="file"
                                id="thumbnail" name="thumbnail" accept="image/*" />
                            @error('thumbnail')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                            @if($animation->thumbnail)
                                <img src="{{ asset($animation->thumbnail) }}" alt="thumbnail" class="mt-2" width="120">
                            @endif
                        </div>
                        <div class="mb-4 col-md-6">
                            <label for="video" class="form-label">{{ __('Video') }} <small class="text-muted">(MP4/MOV, max 250 MB)</small></label>
                            <input class="form-control @error('video') is-invalid @enderror" type="file"
                                id="video" name="video" accept="video/mp4,video/quicktime,video/webm,.mp4,.mov,.m4v,.webm" />
                            @error('video')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                            @if($animation->video)
                                <a class="btn btn-link" href="{{ asset($animation->video) }}" target="_blank">{{ __('View Video') }}</a>
                            @endif
                        </div>
                        @include('dashboard.animations._translations')
                    </div>
                    <div class="mt-2">
                        <button type="submit" class="btn btn-primary me-3">{{ __('Update Animation') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('script')
@endsection
