@extends('admin.layouts.admin')
@php
    $profile = \App\Models\Utility::get_file('uploads/avatar');
@endphp
@section('page-title')
    @if (\Auth::user()->type == 'super admin')
        {{ __('Manage Companies') }}
    @else
        {{ __('Manage User') }}
    @endif
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
    @if (\Auth::user()->type == 'super admin')
        <li class="breadcrumb-item">{{ __('Companies') }}</li>
    @else
        <li class="breadcrumb-item">{{ __('User') }}</li>
    @endif
@endsection

@section('content')

    <div class="row">

        @include('admin.layouts.partials.nav')

        <div class="row">
            <div class="col-sm-12">
                <div class=" mt-2 " id="multiCollapseExample1">
                    <div class="card">
                        <div class="card-body">
                            {{ Form::open(array('route' => array('admin.users.index'),'method' => 'GET','id'=>'frm_submit')) }}
                            <div class="row align-items-center justify-content-end">
                                <div class="col-xl-10">
                                    <div class="row align-items-center justify-content-end">

                                        {{-- ── Status Filter ── --}}
                                        <div class="col-xl-3 col-lg-3 col-md-4 col-sm-12 col-12">
                                            <div class="btn-box">
                                                {{ Form::label('status', __('Status'), ['class' => 'form-label']) }}
                                                <select name="status" id="status_filter" class="form-control select2">
                                                    <option
                                                        value="all" {{ request('status', 'active') == 'all'      ? 'selected' : '' }}>{{ __('All') }}</option>
                                                    <option
                                                        value="active" {{ request('status', 'active') == 'active'   ? 'selected' : '' }}>{{ __('Active') }}</option>
                                                    <option
                                                        value="inactive" {{ request('status', 'active') == 'inactive' ? 'selected' : '' }}>{{ __('Inactive') }}</option>
                                                </select>
                                            </div>
                                        </div>

                                        {{-- ── Search ── --}}
                                        <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12 col-12">
                                            <div class="btn-box">
                                                {{ Form::label('search', __('Search'), ['class' => 'form-label']) }}
                                                {{ Form::text('search', isset($_GET['search']) ? $_GET['search'] : null, ['class' => 'form-control', 'placeholder' => 'Search by company name, name, or email']) }}
                                            </div>
                                        </div>

                                    </div>
                                </div>
                                <div class="col-auto mt-4">
                                    <div class="row">
                                        <div class="col-auto">
                                            <a href="#" class="btn btn-sm btn-primary me-1"
                                               onclick="document.getElementById('frm_submit').submit(); return false;"
                                               data-bs-toggle="tooltip" title="{{ __('Apply') }}"
                                               data-original-title="{{ __('apply') }}">
                                                <span class="btn-inner--icon"><i class="ti ti-search"></i></span>
                                            </a>
                                            <a href="{{ route('admin.users.index') }}" class="btn btn-sm btn-danger"
                                               data-bs-toggle="tooltip" title="{{ __('Reset') }}"
                                               data-original-title="{{ __('Reset') }}">
                                                <span class="btn-inner--icon"><i
                                                        class="ti ti-refresh text-white-off"></i></span>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            {{ Form::close() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @foreach ($users as $user)
            <div class="col-xxl-3 col-lg-4 col-sm-6 mb-4">
                <div class="user-card d-flex flex-column h-100">
                    <div class="user-card-top d-flex align-items-center justify-content-between flex-1 gap-2 mb-3">
                        {{--@if (\Auth::user()->type == 'super admin')
                            <div class="badge bg-primary p-1 px-2">
                                {{ !empty($user->currentPlan) ? $user->currentPlan->name : '' }}
                            </div>
                        @else
                            <div class="badge bg-primary p-1 px-2">
                                {{ ucfirst($user->type) }}
                            </div>
                        @endif--}}
                    </div>
                    <div class="user-info-wrp d-flex align-items-center gap-3 border-bottom pb-3 mb-3">
                        <div class="user-image rounded-1 border-1 border border-primary">
                            @php
                                $profile=\App\Models\Utility::get_file($user->avatar);
                                $avatar = $user->avatar?$profile : asset('assets/assestsnew/menimg.png');
                            @endphp
                            <img src="{{ $avatar }}"
                                 alt="user-image" height="100%" width="100%">
                        </div>
                        <div class="user-info flex-1">
                            <h5 class="mb-1">{{ $user->company_name }}</h5>
                            <span class="text-sm text-muted text-break">{{ $user->email }}</span>
                        </div>

                        @if (Gate::check('edit user') || Gate::check('delete user'))
                            <div class="btn-group card-option">
                                @if ($user->is_disable == 1)
                                    <button type="button" class="btn p-0 border-0" data-bs-toggle="dropdown"
                                            aria-haspopup="true" aria-expanded="false">
                                        <i class="ti ti-dots-vertical"></i>
                                    </button>

                                    <div class="dropdown-menu icon-dropdown dropdown-menu-end">
                                        @can('edit user')
                                            <a href="#!" data-size="lg"
                                               data-url="{{ route('admin.users.edit', $user->id) }}"
                                               data-ajax-popup="true" class="dropdown-item"
                                               data-bs-original-title="{{ \Auth::user()->type == 'super admin' ? __('Edit Company') : __('Edit User') }}">
                                                <i class="ti ti-pencil"></i>
                                                <span>{{ __('Edit') }}</span>
                                            </a>
                                        @endcan

                                        @can('delete user')
                                            {!! Form::open([
                                                'method' => 'DELETE',
                                                'route' => ['admin.users.destroy', $user['id']],
                                                'id' => 'delete-form-' . $user['id'],
                                            ]) !!}
                                            <a href="#!"
                                               class="dropdown-item confirm-delete"
                                               data-form-id="delete-form-{{ $user['id'] }}"
                                               data-name="{{ $user->company_name }}">
                                                <i class="ti ti-trash"></i>
                                                <span>{{ __('Delete') }}</span>
                                            </a>
                                            {!! Form::close() !!}
                                        @endcan

                                        @if (Auth::user()->type == 'super admin')
                                            <a href="{{ route('admin.login.with.company', $user->id) }}"
                                               class="dropdown-item"
                                               data-bs-original-title="{{ __('Login As Company') }}">
                                                <i class="ti ti-replace"></i>
                                                <span> {{ __('Login As Company') }}</span>
                                            </a>
                                        @endif

                                        <a href="#!"
                                           data-url="{{ route('admin.users.reset', \Crypt::encrypt($user->id)) }}"
                                           data-ajax-popup="true" data-size="md" class="dropdown-item"
                                           data-bs-original-title="{{ __('Reset Password') }}">
                                            <i class="ti ti-adjustments"></i>
                                            <span> {{ __('Reset Password') }}</span>
                                        </a>

                                        @if ($user->is_enable_login == 1)
                                            <a href="{{ route('admin.users.login', \Crypt::encrypt($user->id)) }}"
                                               class="dropdown-item">
                                                <i class="ti ti-road-sign"></i>
                                                <span class="text-danger"> {{ __('Login Disable') }}</span>
                                            </a>
                                        @elseif ($user->is_enable_login == 0 && $user->password == null)
                                            <a href="#"
                                               data-url="{{ route('admin.users.reset', \Crypt::encrypt($user->id)) }}"
                                               data-ajax-popup="true" data-size="md" class="dropdown-item login_enable"
                                               data-title="{{ __('New Password') }}" class="dropdown-item">
                                                <i class="ti ti-road-sign"></i>
                                                <span class="text-success"> {{ __('Login Enable') }}</span>
                                            </a>
                                        @else
                                            <a href="{{ route('admin.users.login', \Crypt::encrypt($user->id)) }}"
                                               class="dropdown-item">
                                                <i class="ti ti-road-sign"></i>
                                                <span class="text-success"> {{ __('Login Enable') }}</span>
                                            </a>
                                        @endif

                                    </div>
                                @endif
                            </div>
                        @endif
                    </div>
                    <div class="date-wrp d-flex align-items-center justify-content-between gap-2">
                        @php
                            $date = \Carbon\Carbon::parse($user->last_login_at)->format('Y-m-d');
                            $time = \Carbon\Carbon::parse($user->last_login_at)->format('H:i:s');
                        @endphp
                        <div class="date d-flex align-items-center gap-2">
                            <div class="date-icon d-flex align-items-center justify-content-center">
                                <i class="f-16 ti ti-calendar text-white"></i>
                            </div>
                            <span class="text-sm">{{ $date }}</span>
                        </div>
                        <div class="time d-flex align-items-center gap-2">
                            <div class="time-icon d-flex align-items-center justify-content-center">
                                <i class="f-16 ti ti-clock text-white"></i>
                            </div>
                            <span class="text-sm">{{ $time }}</span>
                        </div>
                    </div>
                    @if (\Auth::user()->type == 'super admin')
                        <div class="btn-wrp d-flex align-items-center gap-2 border-bottom border-top py-3 my-3">
                            <a href="#" data-url="{{ route('admin.plan.upgrade', $user->id) }}" data-size="lg"
                               data-ajax-popup="true" class="btn btn-primary p-2 px-1 w-100"
                               data-title="{{ __('Upgrade Plan') }}">{{ __('Upgrade Plan') }}</a>

                            <a href="{{ route('admin.debug.index', $user->id) }}"
                               class="btn btn-light-primary p-2 px-1 w-100">
                                {{ __('Developer Hub') }}
                            </a>
                        </div>
                        <div class="text-center pb-3 mb-3 border-bottom">
                        <span class="text-sm">
                            {{ __('Plan Expired : ') }}
                            {{ !empty($user->plan_expire_date) ? \Auth::user()->dateFormat($user->plan_expire_date) : __('Lifetime') }}
                        </span>
                        </div>
                        <div
                            class="user-count-wrp d-flex align-items-center justify-content-center gap-2">
                            <div class="user-count d-flex align-items-center gap-2" data-bs-toggle="tooltip"
                                 title="{{ __('Users') }}">
                                <div class="user-icon d-flex align-items-center justify-content-center">
                                    <i class="f-16 ti ti-users text-white"></i>
                                </div>
                                {{ $user->totalCompanyUser($user->id) }}
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        @endforeach
        <div class="d-flex justify-content-end mt-3">
            {{ $users->links('pagination::bootstrap-5') }}
        </div>

    </div>

@endsection

@push('script-page')
    {{-- SweetAlert2 CDN --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        // ─── Select2 init for Status filter ──────────────────────────────────────
        $(document).ready(function () {
            $('#status_filter').select2({
                minimumResultsForSearch: Infinity, // hides the search box (only 3 options)
                width: '100%',
            });
        });

        // ─── Delete with "type delete" confirmation ───────────────────────────────
        $(document).on('click', '.confirm-delete', function (e) {
            e.preventDefault();
            e.stopPropagation(); // prevent dropdown from closing awkwardly

            const formId = $(this).data('form-id');
            const name = $(this).data('name') || 'this record';

            Swal.fire({
                title: '<span style="color:#e3342f;">Delete Confirmation</span>',
                html: `
                    <p class="text-muted mb-3">
                        You are about to permanently delete<br>
                        <strong>${name}</strong>.
                    </p>
                    <p class="mb-1 text-muted" style="font-size:0.9rem;">
                        This action <strong>cannot be undone</strong>.<br>
                        Type <code style="background:#ffeaea;padding:2px 6px;border-radius:4px;color:#e3342f;">Delete</code> below to confirm:
                    </p>
                    <input
                        type="text"
                        id="swal-confirm-input"
                        class="swal2-input"
                        placeholder="Type delete here"
                        autocomplete="off"
                    >
                `,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: '<i class="ti ti-trash"></i> Yes, Delete',
                cancelButtonText: 'Cancel',
                confirmButtonColor: '#e3342f',
                cancelButtonColor: '#6c757d',
                reverseButtons: true,
                focusCancel: true,
                customClass: {
                    confirmButton: 'btn btn-danger px-4',
                    cancelButton: 'btn btn-secondary px-4 me-2',
                },
                buttonsStyling: false,
                didOpen: () => {
                    // Auto-focus the input field
                    document.getElementById('swal-confirm-input').focus();

                    // Allow pressing Enter to trigger confirm
                    document.getElementById('swal-confirm-input').addEventListener('keydown', function (event) {
                        if (event.key === 'Enter') {
                            Swal.clickConfirm();
                        }
                    });
                },
                preConfirm: () => {
                    const inputVal = document.getElementById('swal-confirm-input').value.trim();
                    if (inputVal !== 'Delete') {
                        Swal.showValidationMessage(
                            '<i class="ti ti-alert-circle"></i> You must type <strong>Delete</strong> exactly (capital D) to confirm.'
                        );
                        return false;
                    }
                    return true;
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById(formId).submit();
                }
            });
        });

        // ─── Password switch toggle ───────────────────────────────────────────────
        $(document).on('change', '#password_switch', function () {
            if ($(this).is(':checked')) {
                $('.ps_div').removeClass('d-none');
                $('#password').attr("required", true);
            } else {
                $('.ps_div').addClass('d-none');
                $('#password').val(null);
                $('#password').removeAttr("required");
            }
        });

        // ─── Login enable append hidden input ────────────────────────────────────
        $(document).on('click', '.login_enable', function () {
            setTimeout(function () {
                $('.modal-body').append($('<input>', {
                    type: 'hidden',
                    val: 'true',
                    name: 'login_enable'
                }));
            }, 2000);
        });
    </script>
@endpush
