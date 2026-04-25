@extends('admin.layouts.admin')
@php
    // $profile=asset(Storage::url('uploads/avatar/'));
    $profile = \App\Models\Utility::get_file('uploads/avatar');
@endphp
@section('page-title')
        {{ __('Manage User') }}
@endsection
@section('page-icon')
    @if (\Auth::user()->type == 'super admin')
    {{ asset('assets/assestsnew/company.svg') }}
    @else
    
    @endif
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item">
        <a href="{{ route('admin.dashboard') }}">{{ __('Dashboard') }}</a>
    </li>
    <li class="breadcrumb-item">{{ __('User') }}</li>
@endsection
@section('action-btn')
    <div class="float-end">
        @can('create user')
            <a href="{{ route('admin.roles.index') }}" data-bs-toggle="tooltip" data-title="{{  __('Roles') }}" data-bs-original-title="{{  __('Roles') }}" class="btn btn-sm btn-primary me-1">
                <i class="ti ti-shield	"></i>
            </a>
            <a href="#" data-size="lg" data-url="{{ route('admin.other-users.create') }}" data-ajax-popup="true"
                data-bs-toggle="tooltip" data-title="{{  __('Create User') }}" data-bs-original-title="{{  __('Create User') }}" class="btn btn-sm btn-primary me-1">
                <i class="ti ti-plus"></i>
            </a>
        @endcan
    </div>
@endsection
@section('content')

<div class="row">
    
    @include('admin.layouts.partials.nav')

    <div class="col-md-12 mt-5">
        <div class="card">
            <div class="card-body table-border-style">
                <div class="table-responsive company_order_table">
                    <table class="table datatable">
                        <thead>
                            <tr>
                                <th style="position: unset; background-color: transparent">{{ __('Name') }}</th>
                                <th>{{ __('Email') }}</th>
                                <th>{{ __('Role') }}</th>
                                <th>{{ __('Action') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($users as $user)
                                <tr>
                                    <td class="tex_fix"  style="position: unset;">{{ $user->name }}</td>
                                    <td>{{ $user->email }}</td>
                                    <td>
                                        <span class="me-5 badge p-2 px-3 rounded bg-primary status_badge">{{ $user->roles->pluck('name')->join(', ') }}</span>
                                    </td>
                                    <td>
                                        @if (Gate::check('edit user') || Gate::check('delete user'))
                                            <div class="card-header-right">
                                                <div class="btn-group card-option">
                                                    @if ($user->is_active == 1 && $user->is_disable == 1)
                                                        <button type="button" class="btn dropdown-toggle" data-bs-toggle="dropdown"
                                                            aria-haspopup="true" aria-expanded="false">
                                                            <i class="ti ti-dots-vertical"></i>
                                                        </button>
        
                                                        <div class="dropdown-menu dropdown-menu-end">
        
                                                            @can('edit user')
                                                                <a href="#!" data-size="lg"
                                                                    data-url="{{ route('admin.other-users.edit', $user->id) }}"
                                                                    data-ajax-popup="true" class="dropdown-item"
                                                                    data-bs-original-title="{{ __('Edit User') }}">
                                                                    <i class="ti ti-pencil"></i>
                                                                    <span>{{ __('Edit') }}</span>
                                                                </a>
                                                            @endcan
        
                                                            @can('delete user')
                                                                {!! Form::open([
                                                                    'method' => 'DELETE',
                                                                    'route' => ['admin.other-users.destroy', $user['id']],
                                                                    'id' => 'delete-form-' . $user['id'],
                                                                ]) !!}
                                                                <a href="#!" class="dropdown-item bs-pass-para">
                                                                    <i class="ti ti-archive"></i>
                                                                    <span>
                                                                            {{ __('Delete') }}
                                                                    </span>
                                                                </a>
                                                                {!! Form::close() !!}
                                                            @endcan
        
                                                            <a href="#!"
                                                                data-url="{{ route('admin.otheruser.reset', \Crypt::encrypt($user->id)) }}"
                                                                data-ajax-popup="true" data-size="md" class="dropdown-item"
                                                                data-bs-original-title="{{ __('Reset Password') }}">
                                                                <i class="ti ti-adjustments"></i>
                                                                <span> {{ __('Reset Password') }}</span>
                                                            </a>
        
                                                            @if ($user->is_enable_login == 1)
                                                                <a href="{{ route('admin.otheruser.login', \Crypt::encrypt($user->id)) }}"
                                                                    class="dropdown-item">
                                                                    <i class="ti ti-road-sign"></i>
                                                                    <span class="text-danger"> {{ __('Login Disable') }}</span>
                                                                </a>
                                                            @elseif ($user->is_enable_login == 0 && $user->password == null)
                                                                <a href="#" data-url="{{ route('admin.otheruser.reset', \Crypt::encrypt($user->id)) }}"
                                                                    data-ajax-popup="true" data-size="md" class="dropdown-item login_enable"
                                                                    data-title="{{ __('New Password') }}" class="dropdown-item">
                                                                    <i class="ti ti-road-sign"></i>
                                                                    <span class="text-success"> {{ __('Login Enable') }}</span>
                                                                </a>
                                                            @else
                                                                <a href="{{ route('admin.otheruser.login', \Crypt::encrypt($user->id)) }}"
                                                                    class="dropdown-item">
                                                                    <i class="ti ti-road-sign"></i>
                                                                    <span class="text-success"> {{ __('Login Enable') }}</span>
                                                                </a>
                                                            @endif
                                                        </div>
                                                    @else
                                                        <a href="#" class="action-item text-lg"><i class="ti ti-lock"></i></a>
                                                    @endif
        
                                                </div>
                                            </div>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection

@push('script-page')
    <script>
        $(document).on('change', '#password_switch', function() {
            if ($(this).is(':checked')) {
                $('.ps_div').removeClass('d-none');
                $('#password').attr("required", true);

            } else {
                $('.ps_div').addClass('d-none');
                $('#password').val(null);
                $('#password').removeAttr("required");
            }
        });
        $(document).on('click', '.login_enable', function() {
            setTimeout(function() {
                $('.modal-body').append($('<input>', {
                    type: 'hidden',
                    val: 'true',
                    name: 'login_enable'
                }));
            }, 2000);
        });
    </script>
@endpush
