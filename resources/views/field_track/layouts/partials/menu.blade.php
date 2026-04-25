@php
    use App\Models\Utility;

    $setting = Utility::settings();
    $logo = Utility::get_file('uploads/logo');

    $company_logo = $setting['company_logo_dark'] ?? '';
    $company_logos = $setting['company_logo_light'] ?? '';
    $company_small_logo = $setting['company_small_logo'] ?? '';
    $company_favicon = $setting['company_favicon'] ?? '';

    $emailTemplate = \App\Models\EmailTemplate::emailTemplateData();
    $lang = Auth::user()->lang;
    $userPlan = \App\Models\Plan::getPlan(Auth::user()->show_dashboard());
@endphp

@if (isset($setting['cust_theme_bg']) && $setting['cust_theme_bg'] == 'on')
    <nav class="light-sidebar main_hover_setter">
        @else
            <nav class="dash-sidebar light-sidebar" style="height:0px" ;>
                @endif

                <div class="row">

                    <div class="main_sidebar" id="mobileSidebar">
                        <div class="row px-0 h-100">
                            <div class="main_linksbar" style="padding-top:80px;">
                                <div class="row links_content h-100 justify-content-start">

                                    <div class="logo d-none">
                                        @if ($setting['cust_darklayout'] == 'on')
                                            <img
                                                src="{{ $logo . '/' . (!empty($company_logos) ? $company_logos : 'logo-dark.png') . '?' . time() }}"
                                                alt="{{ config('app.name', 'Loov') }}">
                                        @else
                                            <img
                                                src="{{ $logo . '/' . (!empty($company_logo) ? $company_logo : 'logo-light.png') . '?' . time() }}"
                                                alt="{{ config('app.name', 'Loov') }}">
                                        @endif
                                    </div>
                                    <div class="logo1 d-none">
                                        @if ($setting['cust_darklayout'] == 'on')
                                            <img
                                                src="{{ $logo . '/' . (!empty($company_favicon) ? $company_favicon : 'logo-dark.png') . '?' . time() }}"
                                                alt="{{ config('app.name', 'Loov') }}">
                                        @else
                                            <img
                                                src="{{ $logo . '/' . (!empty($company_favicon) ? $company_favicon : 'logo-light.png') . '?' . time() }}"
                                                alt="{{ config('app.name', 'Loov') }}">
                                        @endif
                                    </div>

                                    @php
                                        $user = auth()->user();
                                    @endphp

                                    <div class="text-center main_icononly justify-content-start">

                                        {{--Dashboard--}}
                                        @if ($user->hasRole(['administrator']))
                                            <div
                                                class="sidebar_logo {{ request()->routeIs('fieldTrack.dashboard') ? 'active_logo' : '' }}">
                                                <img
                                                    class="img_side_logo {{ request()->routeIs('fieldTrack.dashboard') ? 'active_side_logo' : '' }}"
                                                    src="{{ asset('assets/assestsnew/dasboard.svg') }}"
                                                    alt="">
                                            </div>
                                        @endif

                                        {{--Live Location--}}
                                        @if ($user->hasRole(['administrator']))
                                            <div
                                                class="sidebar_logo {{ request()->routeIs('fieldTrack.live_location') ? 'active_logo' : '' }}">
                                                <img
                                                    class="img_side_logo {{ request()->routeIs('fieldTrack.live_location') ? 'active_side_logo' : '' }}"
                                                    src="{{ asset('assets/assestsnew/field_live_location.svg') }}"
                                                    alt="">
                                            </div>
                                        @endif

                                        {{--Attendance--}}
                                        @if ($user->hasRole(['administrator']))
                                            <div
                                                class="sidebar_logo {{ request()->routeIs('fieldTrack.attendanceemployee.index') ? 'active_logo' : '' }}">
                                                <img
                                                    class="img_side_logo {{ request()->routeIs('fieldTrack.attendanceemployee.index') ? 'active_side_logo' : '' }}"
                                                    src="{{ asset('assets/assestsnew/field_attendance.svg') }}"
                                                    alt="">
                                            </div>
                                        @endif

                                        {{--Customer Management--}}
                                        @if ($user->hasRole(['administrator']))
                                            <div
                                                class="sidebar_logo {{ request()->routeIs('fieldTrack.customer.*') ? 'active_logo' : '' }}">
                                                <img
                                                    class="img_side_logo {{ request()->routeIs('fieldTrack.customer.*') ? 'active_side_logo' : '' }}"
                                                    src="{{ asset('assets/assestsnew/field_coustomer.svg') }}"
                                                    alt="">
                                            </div>
                                        @endif

                                        {{--Visit Management--}}
                                        @if ($user->hasRole(['administrator']))
                                            <div
                                                class="sidebar_logo {{ request()->routeIs('fieldTrack.visits.*') ? 'active_logo' : '' }}">
                                                <img
                                                    class="img_side_logo {{ request()->routeIs('fieldTrack.visits.*') ? 'active_side_logo' : '' }}"
                                                    src="{{ asset('assets/assestsnew/field_visit.svg') }}"
                                                    alt="">
                                            </div>
                                        @endif

                                        {{--Location--}}
                                        @if ($user->hasRole(['administrator']))
                                            <div
                                                class="sidebar_logo {{ request()->routeIs('fieldTrack.location.*') ? 'active_logo' : '' }}">
                                                <img
                                                    class="img_side_logo {{ request()->routeIs('fieldTrack.location.*') ? 'active_side_logo' : '' }}"
                                                    src="{{ asset('assets/assestsnew/field_location.svg') }}"
                                                    alt="">
                                            </div>
                                        @endif
                                    </div>

                                    <div class="row main_text_link scroller_tabbtnsetbefore">
                                        {{--Dashboard--}}
                                        @if ($user->hasRole(['administrator']))
                                            <div
                                                class="d-flex justify-content-start align-items-center gap-3 hover_linksets {{ request()->routeIs('fieldTrack.dashboard') ? 'active_link' : '' }}">
                                                <img class="img_side_logo mb-0"
                                                     src="{{ asset('assets/assestsnew/dasboard.svg') }}"
                                                     alt="">
                                                <a class="px-0"
                                                   href="{{ route('fieldTrack.dashboard') }}">Dashboard</a>
                                            </div>
                                        @endif

                                        {{--Live Location--}}
                                        @if ($user->hasRole(['administrator']))
                                            <div
                                                class="d-flex justify-content-start align-items-center gap-1 hover_linksets {{ request()->routeIs('fieldTrack.live_location') ? 'active_link' : '' }}">
                                                <img
                                                    src="{{ asset('assets/assestsnew/field_live_location.svg') }}"
                                                    alt="">
                                                <a href="{{ route('fieldTrack.live_location') }}">Live Location</a>
                                            </div>
                                        @endif

                                        {{--Attendance--}}
                                        @if ($user->hasRole(['administrator']))
                                            <div
                                                class="d-flex justify-content-start align-items-center gap-1 hover_linksets {{ request()->routeIs('fieldTrack.attendanceemployee.index') ? 'active_link' : '' }}">
                                                <img
                                                    src="{{ asset('assets/assestsnew/field_attendance.svg') }}"
                                                    alt="">
                                                <a href="{{ route('fieldTrack.attendanceemployee.index') }}">Attendance</a>
                                            </div>
                                        @endif

                                        {{--Customer Management--}}
                                        @if ($user->hasRole(['administrator']))
                                            <div
                                                class="d-flex justify-content-start align-items-center gap-1 hover_linksets {{ request()->routeIs('fieldTrack.customer.index') ? 'active_link' : '' }}">
                                                <img
                                                    src="{{ asset('assets/assestsnew/field_coustomer.svg') }}"
                                                    alt="">
                                                <a href="{{ route('fieldTrack.customer.index') }}">Customer Management</a>
                                            </div>
                                        @endif

                                        {{--Visit Management--}}
                                        @if ($user->hasRole(['administrator']))
                                            <div
                                                class="d-flex justify-content-start align-items-center gap-1 hover_linksets {{ request()->routeIs('fieldTrack.visits.index') ? 'active_link' : '' }}">
                                                <img
                                                    src="{{ asset('assets/assestsnew/field_visit.svg') }}"
                                                    alt="">
                                                <a href="{{ route('fieldTrack.visits.index') }}">Visit Management</a>
                                            </div>
                                        @endif

                                        {{--Location--}}
                                        @if ($user->hasRole(['administrator']))
                                            <div
                                                class="d-flex justify-content-start align-items-center gap-1 hover_linksets {{ request()->routeIs('fieldTrack.location.*') ? 'active_link' : '' }}">
                                                <img src="{{ asset('assets/assestsnew/field_location.svg') }}" alt="">
                                                <a href="{{ route('fieldTrack.location.countries.index') }}">Location</a>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="row justify-content-center align-items-end ">
                                        <div
                                            class="flex-wrap pt-3 justify-content-center p-3 gap-3 btn_sidebartabsmain new_fields">
                                            <a href="{{ route('organization.dashboard') }}"
                                               class="{{ request()->routeIs('organization.*') ? 'active_sidetab' : '' }}">
                                                <img src="{{ asset('assets/assestsnew/monitoring.svg') }}" alt="">
                                            </a>

                                            <a href="{{ route('fieldTrack.dashboard') }}"
                                               class="{{ request()->routeIs('fieldTrack.*') ? 'active_sidetab' : '' }}">
                                                <img src="{{ asset('assets/assestsnew/track.svg') }}" alt="">
                                            </a>

                                            {{--                                            <a href="#">--}}
                                            {{--                                                <img src="{{ asset('assets/assestsnew/callrecord.svg') }}" alt="">--}}
                                            {{--                                            </a>--}}
                                        </div>

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </nav>
    </nav>
    <button class="menu_bar" id="toggleBtn">
        <i class="ti ti-arrow-right" id="toggleIcon"></i>
    </button>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const toggles = document.querySelectorAll(".collapse-toggle");

            toggles.forEach(toggle => {
                const targetSelector = toggle.getAttribute("data-target");
                const targetEl = document.querySelector(targetSelector);

                // Create Bootstrap collapse instance for each
                const bsCollapse = new bootstrap.Collapse(targetEl, {
                    toggle: false
                });

                toggle.addEventListener("click", function () {
                    if (targetEl.classList.contains("show")) {
                        bsCollapse.hide();
                    } else {
                        bsCollapse.show();
                    }
                });
            });
        });
    </script>

    <script>

        document.getElementById('toggleBtn').addEventListener('click', function () {
            const dashContainer = document.querySelector('.main_sidebar');
            const toggleIcon = document.getElementById('toggleIcon');
            const toggleBtn = document.getElementById('toggleBtn');

            dashContainer.classList.toggle('open_side_mobile');
            dashContainer.classList.toggle('active');  // Add active class for sidebar movement

            toggleIcon.classList.toggle('ti-arrow-left');
            toggleIcon.classList.toggle('ti-arrow-right');

            // Move arrow button with sidebar
            if (dashContainer.classList.contains('active')) {
                toggleBtn.style.left = '248px'; // sidebar width
                toggleBtn.style.borderRadius = '0 50% 50% 0';
                toggleBtn.style.backgroundColor = '#EBF4FF';
            } else {
                toggleBtn.style.left = '0';
                toggleBtn.style.borderRadius = '0 50% 50% 0';
            }
        });

    </script>
