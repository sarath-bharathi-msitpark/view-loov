@php
    $user = auth()->user();
@endphp

<div class="row mb-4">
    <div class="col-12">
        <div class="d-flex align-items-center gap-2 setting_tab flex-wrap">

            {{-- Break --}}
            @if ($user->hasRole('administrator') || ($user->can('settings') && $user->can('company_setting_break')))
                <a class="text-decoration-none flex-grow-1 flex-sm-grow-0"
                   href="{{ route('organization.settings.break') }}">
                    <div class="d-flex justify-content-center align-items-center gap-2 um-tab-btn {{ request()->routeIs('organization.settings.break*') ? 'active' : '' }}">
                        <img src="{{ asset('assets/assestsnew/coffee.svg') }}" alt="Break" class="tab-icon-img">
                        <h6 class="mb-0">Break</h6>
                    </div>
                </a>
            @endif

            {{-- Designation --}}
            @if ($user->hasRole('administrator') || ($user->can('settings') && $user->can('company_setting_designation')))
                <a class="text-decoration-none flex-grow-1 flex-sm-grow-0"
                   href="{{ route('organization.settings.designation') }}">
                    <div class="d-flex justify-content-center align-items-center gap-2 um-tab-btn {{ request()->routeIs('organization.settings.designation*') ? 'active' : '' }}">
                        <img src="{{ asset('assets/assestsnew/briefcase.svg') }}" alt="Designation" class="tab-icon-img">
                        <h6 class="mb-0">Designation</h6>
                    </div>
                </a>
            @endif

            {{-- Roles --}}
            @if ($user->hasRole('administrator') || ($user->can('settings') && $user->can('company_setting_roles')))
                <a class="text-decoration-none flex-grow-1 flex-sm-grow-0"
                   href="{{ route('organization.settings.role') }}">
                    <div class="d-flex justify-content-center align-items-center gap-2 um-tab-btn {{ request()->routeIs('organization.settings.role*') ? 'active' : '' }}">
                        <img src="{{ asset('assets/assestsnew/user-management.svg') }}" alt="Roles" class="tab-icon-img">
                        <h6 class="mb-0">Roles</h6>
                    </div>
                </a>
            @endif

            {{-- Shift --}}
            @if ($user->hasRole('administrator') || ($user->can('settings') && $user->can('company_setting_shifts')))
                <a class="text-decoration-none flex-grow-1 flex-sm-grow-0"
                   href="{{ route('organization.settings.shift') }}">
                    <div class="d-flex justify-content-center align-items-center gap-2 um-tab-btn {{ request()->routeIs('organization.settings.shift*') ? 'active' : '' }}">
                        <img src="{{ asset('assets/assestsnew/user-account.svg') }}" alt="Shift" class="tab-icon-img">
                        <h6 class="mb-0">Shift</h6>
                    </div>
                </a>
            @endif

            {{-- Teams --}}
            @if ($user->hasRole('administrator') || ($user->can('settings') && $user->can('company_setting_teams')))
                <a class="text-decoration-none flex-grow-1 flex-sm-grow-0"
                   href="{{ route('organization.settings.team') }}">
                    <div class="d-flex justify-content-center align-items-center gap-2 um-tab-btn {{ request()->routeIs('organization.settings.team*') ? 'active' : '' }}">
                        <img src="{{ asset('assets/assestsnew/groups.svg') }}" alt="Teams" class="tab-icon-img">
                        <h6 class="mb-0">Teams</h6>
                    </div>
                </a>
            @endif

            {{-- User --}}
            @if ($user->hasRole('administrator') || $user->can('settings'))
                <a class="text-decoration-none flex-grow-1 flex-sm-grow-0"
                   href="{{ route('organization.settings.user') }}">
                    <div class="d-flex justify-content-center align-items-center gap-2 um-tab-btn {{ request()->routeIs('organization.settings.user*') ? 'active' : '' }}">
                        <img src="{{ asset('assets/assestsnew/user.svg') }}" alt="User" class="tab-icon-img">
                        <h6 class="mb-0">User</h6>
                    </div>
                </a>
            @endif

            {{-- Workplace --}}
            @if ($user->hasRole('administrator') || ($user->can('settings') && $user->can('company_setting_workspace')))
                <a class="text-decoration-none flex-grow-1 flex-sm-grow-0"
                   href="{{ route('organization.settings.workplace') }}">
                    <div class="d-flex justify-content-center align-items-center gap-2 um-tab-btn {{ request()->routeIs('organization.settings.workplace*') ? 'active' : '' }}">
                        <img src="{{ asset('assets/assestsnew/workplace.svg') }}" alt="Workplace" class="tab-icon-img">
                        <h6 class="mb-0">Workplace</h6>
                    </div>
                </a>
            @endif

        </div>
    </div>
</div>