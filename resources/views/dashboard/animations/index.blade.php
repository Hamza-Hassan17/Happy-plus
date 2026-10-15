@extends('layouts.master')

@section('title', __('Animations'))

@section('css')
@endsection


@section('breadcrumb-items')
    <li class="breadcrumb-item active">{{ __('Animations') }}</li>
@endsection
@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <!-- Animations List Table -->
        <div class="card">
            <div class="card-header">
                @canany(['create animation'])
                    <a href="{{route('dashboard.animations.create')}}" class="add-new btn btn-primary waves-effect waves-light">
                        <i class="ti ti-plus me-0 me-sm-1 ti-xs"></i><span
                            class="d-none d-sm-inline-block">{{ __('Add New Animation') }}</span>
                    </a>
                @endcan
            </div>
            <div class="card-datatable table-responsive">
                <table class="datatables-users table border-top custom-datatables">
                    <thead>
                        <tr>
                            <th>{{ __('Sr.') }}</th>
                            <th>{{ __('Name') }}</th>
                            <th>{{ __('Thumbnail') }}</th>
                            <th>{{ __('Video') }}</th>
                            <th>{{ __('Size') }}</th>
                            <th>{{ __('Created At') }}</th>
                            <th>{{ __('Status') }}</th>
                            @canany(['delete animation', 'update animation'])<th>{{ __('Action') }}</th>@endcan
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($animations as $index => $animation)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $animation->name }}</td>
                                <td>
                                    @if (isset($animation->thumbnail))
                                        <img src="{{ asset($animation->thumbnail) }}" alt="{{ $animation->name }}"
                                            height="35px" width="35px">
                                    @else
                                        {{ __('No Image') }}
                                    @endif
                                </td>
                                <td>
                                    @if (isset($animation->video))
                                        <a href="{{ asset($animation->video) }}" target="_blank">{{ __('Play') }}</a>
                                    @else
                                        {{ __('No Video') }}
                                    @endif
                                </td>
                                <td>{{ $animation->video_size ? number_format($animation->video_size / 1048576, 1) . ' MB' : '-' }}</td>
                                <td>{{ $animation->created_at->format('d M Y') }}</td>
                                <td>
                                    <span
                                        class="badge me-4 bg-label-{{ $animation->is_active == 'active' ? 'success' : 'danger' }}">{{ ucfirst($animation->is_active) }}</span>
                                </td>
                                @canany(['delete animation', 'update animation'])
                                    <td class="d-flex">
                                        @canany(['delete animation'])
                                            <form action="{{ route('dashboard.animations.destroy', $animation->id) }}" method="POST">
                                                @method('DELETE')
                                                @csrf
                                                <a href="#" type="submit"
                                                    class="btn btn-icon btn-text-danger waves-effect waves-light rounded-pill delete-record delete_confirmation"
                                                    data-bs-toggle="tooltip" data-bs-placement="top"
                                                    title="{{ __('Delete animation') }}">
                                                    <i class="ti ti-trash ti-md"></i>
                                                </a>
                                            </form>
                                        @endcan
                                        @canany(['update animation'])
                                            <span class="text-nowrap">
                                                <a href="{{ route('dashboard.animations.edit', $animation->id) }}"
                                                    class="btn btn-icon btn-text-primary waves-effect waves-light rounded-pill me-1"
                                                    data-bs-toggle="tooltip" data-bs-placement="top"
                                                    title="{{ __('Edit Animation') }}">
                                                    <i class="ti ti-edit ti-md"></i>
                                                </a>
                                            </span>
                                            <span class="text-nowrap">
                                                <a href="{{ route('dashboard.animations.status.update', $animation->id) }}"
                                                    class="btn btn-icon btn-text-primary waves-effect waves-light rounded-pill me-1"
                                                    data-bs-toggle="tooltip" data-bs-placement="top"
                                                    title="{{ $animation->is_active == 'active' ? __('Deactivate Animation') : __('Activate Animation') }}">
                                                    @if ($animation->is_active == 'active')
                                                        <i class="ti ti-toggle-right ti-md text-success"></i>
                                                    @else
                                                        <i class="ti ti-toggle-left ti-md text-danger"></i>
                                                    @endif
                                                </a>
                                            </span>
                                        @endcan
                                    </td>
                                @endcan
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

@section('script')
    {{-- <script src="{{asset('assets/js/app-user-list.js')}}"></script> --}}
    <script>
        $(document).ready(function() {
            //
        });
    </script>
@endsection
