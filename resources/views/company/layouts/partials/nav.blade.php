@php
    $logo = Utility::get_file('uploads/logo');
    $company_favicon = $setting['company_favicon'] ?? '';
@endphp
<div class="col-12" style="padding-top: 100px;">
    <div class="row align-items-center justify-content-center">
        <div class="header_fixedshow">
            <div class="row align-items-center">
                <div class="col-6">
                    <div class="d-flex justify-content-start align-items-center gap-2">
                        <img class=" header_icons"
                             src="{{ $logo . '/' . (!empty($company_favicon) ? $company_favicon : 'favicon.png') . '?' . time() }}"
                             alt="Header Icon" width="60" height="60"/>
                        <img class="header_icons_1"
                             src="@yield('page-icon', asset('assets/assestsnew/download-logo.svg'))"
                             alt="Header Icon" width="32" height="32"/>

                        <h5 class="fw-semibold let_1 mb-0">@yield('page-title')</h5>
                    </div>

                </div>

                <div class="col-6 d-flex justify-content-end align-items-center mt-0">
                    <button class="menu_icon" id="mobileMenuButton">
                        <img src="{{ asset('assets/assestsnew/menu-icon.svg') }}">
                    </button>

                    <div
                        class="d-md-flex justify-content-end align-items-center flex-md-nowrap flex-wrap gap-3 mt-3 mt-md-0"
                        id="menuItems">
                        <div>
                            @if(\Auth::user()->type == 'company')
                                @impersonating($guard = null)
                                <a class="btn btn-danger btn-sm text-nowrap"
                                   href="{{ route('organization.exit.company') }}"><i
                                        class="ti ti-ban"></i>
                                    {{ __('Exit Login') }}
                                </a>
                                @endImpersonating
                            @endif
                            @if(\Auth::user()->type == 'Employee')
                                @impersonating
                                <a class="btn btn-danger btn-sm" href="{{ route('organization.exit.employee') }}">
                                    <i class="ti ti-ban"></i>
                                    {{ __('Exit From Employee') }}
                                </a>
                                @endImpersonating
                            @endif
                        </div>

                        <!-- Help Dropdown -->
                        <div class="help">
                            <div class="dropdown text-center text-nowrap position-relative">
                                <img src="{{ asset('assets/assestsnew/help-circle.svg') }}" alt="">
                                <a class="dropdown-toggle fw-semibold text-decoration-none text-primary" href="#"
                                   role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    Help
                                </a>
                                <ul class="dropdown-menu dropdown-menu-center bg-transparent border-0 p-0 rounded-0 shadow-none">
                                    <div class="container-center">
                                        <div class="account-card">
                                            <a href="{{ route('blogs.index') }}" target="_blank"
                                               class="menu-item self_hov1 px-2" style="color: #6B6B6B;">
                                                <div class="menu-icon">
                                                    <img src="{{ asset('assets/assestsnew/docs.svg') }}" alt="">
                                                </div>
                                                Documentation
                                            </a>
                                            <a href="{{ env('APP_MAIN_URL') }}/contact" target="_blank"
                                               class="menu-item self_hov1 px-2" style="color: #6B6B6B;">
                                                <div class="menu-icon">
                                                    <img src="{{ asset('assets/assestsnew/take-care.svg') }}" alt="">
                                                </div>
                                                Onboarding Support
                                            </a>
                                            <a href="{{ rtrim(env('APP_MAIN_URL', 'https://loov.in'), '/') . '/contact' }}"
                                               target="_blank"
                                               class="menu-item self_hov1 px-2" style="color: #6B6B6B;">
                                                <div class="menu-icon">
                                                    <img src="{{ asset('assets/assestsnew/problem.svg') }}" alt="">
                                                </div>
                                                Report Issue
                                            </a>
                                        </div>
                                    </div>
                                </ul>
                            </div>
                        </div>

                        <!-- Download Button -->
                        <button class="fw-normal but_1 text-white d-flex align-items-center gap-2 text-nowrap px-4 py-2"
                                data-bs-toggle="modal" data-bs-target="#downloadModal">
                            <img src="{{ asset('assets/assestsnew/download-logo.svg') }}" alt="Download Icon"/>
                            Download LOOV
                        </button>

                        <!-- User Profile Dropdown -->
                        <div class="d-flex align-items-center gap-2 menu_drop">
                            @php
                                $user = \Illuminate\Support\Facades\Auth::user();
                                $profile=\App\Models\Utility::get_file($user->avatar);
                                $avatar = $user->avatar?$profile : asset('assets/assestsnew/menimg.png');
                            @endphp

                            <img src="{{ $avatar }}" alt="User Image" class="rounded-circle" width="38" height="38"/>

                            <div class="dropdown">
                                <a class="dropdown-toggle fw-semibold text-decoration-none text-dark" type="button"
                                   data-bs-toggle="dropdown" aria-expanded="false">
                                    {{ \Illuminate\Support\Facades\Auth::user()->company_name ?? \Illuminate\Support\Facades\Auth::user()->name}}
                                </a>
                                <ul class="dropdown-menu border-0 p-0 bg-transparent dropdown-menu-end">
                                    <div class="container-center">
                                        <div class="account-card">
                                            <div class="profile-section">
                                                <div class="profile-pic">
                                                    <img src="{{ $avatar }}" alt="User Image" class="rounded-circle"
                                                         width="38" height="38"/>
                                                </div>
                                                <div>
                                                    <h6 class="mb-0 text-black fw-semibold fs-6">{{ \Illuminate\Support\Facades\Auth::user()->company_name ?? \Illuminate\Support\Facades\Auth::user()->name}}</h6>
                                                    <small
                                                        style="color: #A2A2A2;">{{ \Illuminate\Support\Facades\Auth::user()->email }}</small>
                                                </div>
                                            </div>

                                            @if ($user->hasRole(['administrator']))
                                                <a href="{{ route('general.plans.index') }}"
                                                   class="menu-item self_hov1 px-2" style="color: #6B6B6B;">
                                                    <div class="menu-icon">
                                                        <img src="{{ asset('assets/assestsnew/bill.svg') }}" alt="">
                                                    </div>
                                                    Billing
                                                </a>

                                                <a href="{{ route('organization.settings.user') }}"
                                                   class="menu-item self_hov1 px-2" style="color: #6B6B6B;">
                                                    <div class="menu-icon">
                                                        <i class="ti ti-settings"
                                                           style="font-size: 24px; width: 24px; height: 24px;"></i>
                                                    </div>
                                                    Settings
                                                </a>
                                            @endif

                                            <form id="logout-form" action="{{ route('auth.logout') }}" method="POST"
                                                  style="display: none;">
                                                @csrf
                                            </form>

                                            <a href="#" class="menu-item self_hov1 px-2" style="color: #6B6B6B;"
                                               onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                                <div class="menu-icon">
                                                    <img src="{{ asset('assets/assestsnew/log-out.svg') }}"
                                                         alt="Logout">
                                                </div>
                                                Logout
                                            </a>
                                        </div>
                                    </div>
                                </ul>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
            <!-- Mobile Menu Dropdown -->
            <div class="mobile-menu-dropdown p-0" id="mobileMenuDropdown" style="display: none;">
                <div class="mobile-menu-container">
                    <!-- Help Dropdown -->
                    <div class="mobile-menu-item">
                        <div class="mobile-menu-header" onclick="toggleMobileSubmenu('helpSubmenu')">
                            <img src="{{ asset('assets/assestsnew/help-circle.svg') }}" alt="">
                            <span>Help</span>
                            <i class="ti ti-chevron-down"></i>
                        </div>
                        <div class="mobile-submenu" id="helpSubmenu">
                            <a href="#" class="mobile-submenu-item">
                                <img src="{{ asset('assets/assestsnew/docs.svg') }}" alt="">
                                Documentation
                            </a>
                            <a href="#" class="mobile-submenu-item">
                                <img src="{{ asset('assets/assestsnew/take-care.svg') }}" alt="">
                                Onboarding Support
                            </a>
                            <a href="#" class="mobile-submenu-item">
                                <img src="{{ asset('assets/assestsnew/problem.svg') }}" alt="">
                                Report Issue
                            </a>
                        </div>
                    </div>

                    <!-- Download Button -->
                    <button class="mobile-menu-item download-btn" data-bs-toggle="modal"
                            data-bs-target="#downloadModal">
                        <img src="{{ asset('assets/assestsnew/download-logo.svg') }}" alt="Download Icon"/>
                        Download LOOV
                    </button>

                    <!-- User Profile Dropdown -->
                    <div class="mobile-menu-item">
                        <div class="mobile-menu-header" onclick="toggleMobileSubmenu('profileSubmenu')">
                            <img src="{{ $avatar }}" alt="User Image" class="rounded-circle" width="24"
                                 height="24">
                            <span>{{ \Illuminate\Support\Facades\Auth::user()->company_name ?? \Illuminate\Support\Facades\Auth::user()->name}}</span>
                            <i class="ti ti-chevron-down"></i>
                        </div>
                        <div class="mobile-submenu" id="profileSubmenu">
                            <div class="profile-info">
                                <img src="{{ $avatar }}" alt="User Image" class="rounded-circle" width="38"
                                     height="38">
                                <div>
                                    <h6>{{ \Illuminate\Support\Facades\Auth::user()->company_name ?? \Illuminate\Support\Facades\Auth::user()->name}}</h6>
                                    <small>{{ \Illuminate\Support\Facades\Auth::user()->email }}</small>
                                </div>
                            </div>
                            @if ($user->hasRole(['administrator']))
                                <a href="{{ route('general.plans.index') }}" class="mobile-submenu-item">
                                    <img src="{{ asset('assets/assestsnew/bill.svg') }}" alt="">
                                    Billing
                                </a>
                            @endif
                            <a href="{{ route('organization.settings.user') }}" class="mobile-submenu-item">
                                <i class="ti ti-settings" style="font-size: 24px; width: 24px; height: 24px;"></i>
                                Settings
                            </a>
                            <a href="#" class="mobile-submenu-item"
                               onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                <img src="{{ asset('assets/assestsnew/log-out.svg') }}" alt="Logout">
                                Logout
                            </a>
                        </div>
                    </div>

                    <div class="p-3">
                        @if(\Auth::user()->type == 'company')
                            @impersonating($guard = null)
                            <a class="btn btn-danger btn-sm" href="{{ route('organization.exit.company') }}"><i
                                    class="ti ti-ban"></i>
                                {{ __('Exit Login') }}
                            </a>
                            @endImpersonating
                        @endif
                    </div>
                </div>
            </div>


            @php
                use App\Models\Setting;

                $settings = Setting::whereIn('name', [
                'window_stel_user',
                'window_stand_user',
                'mac_stel_user',
                'mac_stand_user',
                'field_track_loov'
                ])->pluck('value', 'name');
            @endphp
        </div>

        <div class="col-md-12">
            @include('company.layouts.partials.header')
        </div>

        <div class="col-md-12">
            <div class="page-header">
                <div class="page-block">
                    <div class="d-flex flex-wrap align-items-center justify-content-between gap-2">
                        <div>
                            <div class="page-header-title">
                                <!--<h4 class="mb-2">@yield('page-title')</h4>-->
                            </div>
                            <ul class="breadcrumb">
                                @yield('breadcrumb')
                            </ul>
                        </div>
                        <div class="action-btn-col">
                            @yield('action-btn')
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!--Model For Download Button -->
    <div class="modal fade" id="downloadModal" tabindex="-1" aria-labelledby="downloadModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">

                <!-- Modal Header -->
                <div class="modal-header border-0">
                    <h5 class="modal-title fw-bold" id="downloadModalLabel">Download LOOV</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <!-- Modal Body -->
                <div class="modal-body">

                    <!-- Nav Tabs -->
                    <div class="custom_tabs_container">
                        <ul class="custom_tabs nav" id="myTab" role="tablist">
                            <li class="custom_tab_item" role="presentation">
                                <button class="custom_tab_link active" id="system-tab" data-bs-toggle="tab"
                                        data-bs-target="#system" type="button" role="tab" aria-controls="system"
                                        aria-selected="true">
                                    <i class="fa fa-desktop me-1"></i> System
                                </button>
                            </li>
                            <li class="custom_tab_item" role="presentation">
                                <button class="custom_tab_link" id="field-tab" data-bs-toggle="tab"
                                        data-bs-target="#field" type="button" role="tab" aria-controls="field"
                                        aria-selected="false">
                                    <i class="fa fa-map-marker-alt me-1"></i> Field
                                </button>
                            </li>
                            <li class="custom_tab_item" role="presentation">
                                <button class="custom_tab_link" id="call-tab" data-bs-toggle="tab"
                                        data-bs-target="#call" type="button" role="tab" aria-controls="call"
                                        aria-selected="false">
                                    <i class="fa fa-phone me-1"></i> Call
                                </button>
                            </li>
                        </ul>
                    </div>


                    <!-- Tab Content -->
                    <div class="tab-content mt-3" id="myTabContent">

                        <!-- System Tab -->
                        <div class="tab-pane fade show active" id="system" role="tabpanel" aria-labelledby="system-tab">
                            <h3 class="text-center mb-4">System</h3>

                            <!-- Windows Stealth -->
                            <div class="d-flex align-items-center justify-content-center mb-3">
                                <span class="fw-bold me-3" style="width: 180px;">Windows (Stealth User)</span>
                                <a href="{{ $settings['window_stel_user'] ?? '#' }}" target="_blank"
                                   class="btn but_1 text-white me-2">Download LOOV</a>
                                <button class="btn btn-outline-secondary btn-sm copy-btn"
                                        data-url="{{ $settings['window_stel_user'] ?? '#' }}">Copy
                                </button>
                            </div>

                            <!-- Windows Standard -->
                            <div class="d-flex align-items-center justify-content-center mb-3">
                                <span class="fw-bold me-3" style="width: 180px;">Windows (Standard User)</span>
                                <a href="{{ $settings['window_stand_user'] ?? '#' }}" target="_blank"
                                   class="btn but_1 text-white me-2">Download LOOV</a>
                                <button class="btn btn-outline-secondary btn-sm copy-btn"
                                        data-url="{{ $settings['window_stand_user'] ?? '#' }}">Copy
                                </button>
                            </div>

                            <!-- Mac Stealth -->
                            <div class="d-flex align-items-center justify-content-center mb-3">
                                <span class="fw-bold me-3" style="width: 180px;">Mac (Stealth User)</span>
                                <a href="{{ $settings['mac_stel_user'] ?? '#' }}" target="_blank"
                                   class="btn but_1 text-white me-2">Download LOOV</a>
                                <button class="btn btn-outline-secondary btn-sm copy-btn"
                                        data-url="{{ $settings['mac_stel_user'] ?? '#' }}">Copy
                                </button>
                            </div>

                            <!-- Mac Standard -->
                            <div class="d-flex align-items-center justify-content-center mb-3">
                                <span class="fw-bold me-3" style="width: 180px;">Mac (Standard User)</span>
                                <a href="{{ $settings['mac_stand_user'] ?? '#' }}" target="_blank"
                                   class="btn but_1 text-white me-2">Download LOOV</a>
                                <button class="btn btn-outline-secondary btn-sm copy-btn"
                                        data-url="{{ $settings['mac_stand_user'] ?? '#' }}">Copy
                                </button>
                            </div>

                        </div>

                        <!-- Field Tab -->
                        <div class="tab-pane fade" id="field" role="tabpanel" aria-labelledby="field-tab">
                            <h3 class="text-center mb-4">Field Track</h3>
                            <!-- Windows Stealth -->
                            <div class="d-flex align-items-center justify-content-center mb-3">
                                <span class="fw-bold me-3" style="width: 180px;">Android</span>
                                <a href="{{ $settings['field_track_loov'] ?? '#' }}" target="_blank"
                                   class="btn but_1 text-white me-2">Download Field Track</a>
                                <button class="btn btn-outline-secondary btn-sm copy-btn"
                                        data-url="{{ $settings['field_track_loov'] ?? '#' }}">Copy
                                </button>
                            </div>
                        </div>

                        <!-- Call Tab -->
                        <div class="tab-pane fade" id="call" role="tabpanel" aria-labelledby="call-tab">
                            <!--<h3>Call Track</h3>-->
                            <!--<p>Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque-->
                            <!--    laudantium, totam rem aperiam.</p>-->
                            <div class="d-flex justify-content-center align-items-center">

                                <img class="w-50" src="{{ asset('assets/assestsnew/coming_soon.svg') }}">
                            </div>
                        </div>

                    </div> <!-- End tab-content -->

                </div> <!-- End modal-body -->
            </div> <!-- End modal-content -->
        </div> <!-- End modal-dialog -->
    </div> <!-- End modal -->
</div>

<!-- Copy to Clipboard JS -->
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const copyButtons = document.querySelectorAll('.copy-btn');

        copyButtons.forEach(button => {
            button.addEventListener('click', function () {
                const url = this.getAttribute('data-url');
                navigator.clipboard.writeText(url).then(() => {
                    this.textContent = 'Copied!';
                    setTimeout(() => {
                        this.textContent = 'Copy';
                    }, 1500);
                }).catch(err => {
                    console.error('Failed to copy!', err);
                });
            });
        });
    });
</script>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const menuButton = document.getElementById('mobileMenuButton');
        const mobileMenu = document.getElementById('mobileMenuDropdown');

        menuButton.addEventListener('click', function () {
            if (mobileMenu.style.display === 'none') {
                mobileMenu.style.display = 'block';
            } else {
                mobileMenu.style.display = 'none';
// Also close all submenus when closing main menu
                document.querySelectorAll('.mobile-submenu').forEach(submenu => {
                    submenu.classList.remove('show');
                });
            }
        });

// Close menu when clicking outside
        document.addEventListener('click', function (event) {
            if (!menuButton.contains(event.target) && !mobileMenu.contains(event.target)) {
                mobileMenu.style.display = 'none';
            }
        });
    });

    function toggleMobileSubmenu(id) {
        const submenu = document.getElementById(id);
        submenu.classList.toggle('show');
    }
</script>

