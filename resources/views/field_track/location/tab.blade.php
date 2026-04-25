@php
    $user = auth()->user();
@endphp
<div class="row">
    <div class="col-12">
        <div class="d-flex align-items-center gap-2 setting_tab">

            <!-- Country -->
            @if ($user->hasRole('administrator') || ($user->can('settings') && $user->can('company_setting_admin')))
                <a class="text-decoration-none flex-grow-1 flex-sm-grow-0"
                   href="{{ route('fieldTrack.location.countries.index') }}">
                    <div
                        class="d-flex justify-content-center align-items-center gap-2 border_box p-2 {{ request()->routeIs('fieldTrack.location.countries*') ? 'active_box' : '' }}">
                        <div>
                            <img src="{{ asset('assets/assestsnew/Cuntires.svg') }}" alt="Administrator"
                                 class="img-fluid">
                        </div>
                        <div>
                            <h6 class="mb-0 text-dark">Countries</h6>
                        </div>
                    </div>
                </a>
            @endif

            <!-- State -->
            @if ($user->hasRole('administrator') || ($user->can('settings') && $user->can('company_setting_break')))
                <a class="text-decoration-none flex-grow-1 flex-sm-grow-0"
                   href="{{ route('fieldTrack.location.states.index') }}">
                    <div
                        class="d-flex justify-content-center align-items-center gap-2 border_box p-2 {{ request()->routeIs('fieldTrack.location.states*') ? 'active_box' : '' }}">
                        <div>
                            <img src="{{ asset('assets/assestsnew/State.svg') }}" alt="Break" class="img-fluid">
                        </div>
                        <div>
                            <h6 class="mb-0 text-dark">States</h6>
                        </div>
                    </div>
                </a>
            @endif

            <!-- City -->
            @if ($user->hasRole('administrator') || ($user->can('settings') && $user->can('company_setting_designation')))
                <a class="text-decoration-none flex-grow-1 flex-sm-grow-0"
                   href="{{ route('fieldTrack.location.cities.index') }}">
                    <div
                        class="d-flex justify-content-center align-items-center gap-2 border_box p-2 {{ request()->routeIs('fieldTrack.location.cities*') ? 'active_box' : '' }}">
                        <div>
                            <img src="{{ asset('assets/assestsnew/City.svg') }}" alt="Designation"
                                 class="img-fluid">
                        </div>
                        <div>
                            <h6 class="mb-0 text-dark">Cities</h6>
                        </div>
                    </div>
                </a>
            @endif

            <!-- Area -->
            @if ($user->hasRole('administrator') || ($user->can('settings') && $user->can('company_setting_roles')))
                <a class="text-decoration-none flex-grow-1 flex-sm-grow-0"
                   href="{{ route('fieldTrack.location.areas.index') }}">
                    <div
                        class="d-flex justify-content-center align-items-center gap-2 border_box p-2 {{ request()->routeIs('fieldTrack.location.areas*') ? 'active_box' : '' }}">
                        <div>
                            <img src="{{ asset('assets/assestsnew/Beat.svg') }}" alt="Roles"
                                 class="img-fluid">
                        </div>
                        <div>
                            <h6 class="mb-0 text-dark">Areas</h6>
                        </div>
                    </div>
                </a>
            @endif

            <!-- Shift -->
            @if ($user->hasRole('administrator') || ($user->can('settings') && $user->can('company_setting_shifts')))
                <a class="text-decoration-none flex-grow-1 flex-sm-grow-0"
                   href="{{ route('fieldTrack.location.beats.index') }}">
                    <div
                        class="d-flex justify-content-center align-items-center gap-2 border_box p-2 {{ request()->routeIs('fieldTrack.location.beats*') ? 'active_box' : '' }}">
                        <div>
                            <img src="{{ asset('assets/assestsnew/user-account.svg') }}" alt="Shift" class="img-fluid">
                        </div>
                        <div>
                            <h6 class="mb-0 text-dark">Beats</h6>
                        </div>
                    </div>
                </a>
            @endif

        </div>
    </div>
</div>
