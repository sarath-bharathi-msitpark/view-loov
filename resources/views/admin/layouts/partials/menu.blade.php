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
            <nav class="dash-sidebar light-sidebar" style="height:0px ;">
                @endif
                <div class="">
                    <div class="row">
                        <div class="main_sidebar" id="mobileSidebar">
                            <div class="row px-0 h-100">
                                <div class="main_linksbar" style="padding-top: 80px;">
                                    <div class="row links_content justify-content-center">
                                        {{--<div class="logo d-none">
                                            @if ($setting['cust_darklayout'] == 'on')
                                                <img src="{{ $logo . '/' . (!empty($company_logos) ? $company_logos : 'logo-dark.png') . '?' . time() }}" alt="{{ config('app.name', 'Loov') }}">
                                            @else
                                                <img src="{{ $logo . '/' . (!empty($company_logo) ? $company_logo : 'logo-light.png') . '?' . time() }}" alt="{{ config('app.name', 'Loov') }}">
                                            @endif
                                        </div>
                                        <div class="logo1 d-none">
                                            @if ($setting['cust_darklayout'] == 'on')
                                                <img src="{{ $logo . '/' . (!empty($company_favicon) ? $company_favicon : 'favicon.png') . '?' . time() }}" alt="{{ config('app.name', 'Loov') }}">
                                            @else
                                                <img src="{{ $logo . '/' . (!empty($company_favicon) ? $company_favicon : 'logo-light.png') . '?' . time() }}" alt="{{ config('app.name', 'Loov') }}">
                                            @endif
                                        </div>--}}
                                        @if (Auth::user()->type == 'super admin')
                                            <div class="text-center main_icononly">
                                                <div
                                                    class="sidebar_logo {{ request()->routeIs('organization.dashboard') ? 'active_logo' : '' }}">
                                                    <img
                                                        class="img_side_logo {{ request()->routeIs('organization.dashboard') ? 'active_side_logo' : '' }}"
                                                        src="{{ asset('assets/assestsnew/dasboard.svg') }}"
                                                        alt="">
                                                </div>
                                                <div
                                                    class="sidebar_logo {{ request()->routeIs('organization.screenshot.index') ? 'active_logo' : '' }}">
                                                    <img
                                                        class="img_side_logo {{ request()->routeIs('organization.screenshot.index') ? 'active_side_logo' : '' }}"
                                                        src="{{ asset('assets/assestsnew/subscription.svg') }}"
                                                        alt="">
                                                </div>
                                                <div
                                                    class="sidebar_logo {{ request()->routeIs('admin.users.*', 'admin.debug.index', 'admin.debug.toggleDebugMode') ? 'active_logo' : '' }}">
                                                    <img
                                                        class="img_side_logo {{ request()->routeIs('admin.users.*', 'admin.debug.index', 'admin.debug.toggleDebugMode') ? 'active_side_logo' : '' }}"
                                                        src="{{ asset('assets/assestsnew/company.svg') }}"
                                                        alt="">
                                                </div>
                                                <div
                                                    class="sidebar_logo {{ request()->routeIs('admin.systems.*') ? 'active_logo' : '' }}">
                                                    <img
                                                        class="img_side_logo {{ request()->routeIs('admin.systems.*') ? 'active_side_logo' : '' }}"
                                                        src="{{ asset('assets/assestsnew/settingslogo.svg') }}"
                                                        alt="">
                                                </div>
                                                <div
                                                    class="sidebar_logo {{ request()->routeIs('general.blogs.*') ? 'active_logo' : '' }}">
                                                    <img
                                                        class="img_side_logo {{ request()->routeIs('general.blogs.*') ? 'active_side_logo' : '' }}"
                                                        src="{{ asset('assets/assestsnew/FolderSetting.svg') }}"
                                                        alt="">
                                                </div>
                                                <div
                                                    class="sidebar_logo {{ request()->routeIs('admin.other-users.*') ? 'active_logo' : '' }}">
                                                    <img
                                                        class="img_side_logo {{ request()->routeIs('admin.other-users.*') ? 'active_side_logo' : '' }}"
                                                        src="{{ asset('assets/assestsnew/team.svg') }}"
                                                        alt="">
                                                </div>

                                            </div>

                                            <div class="row main_text_link">
                                                <div
                                                    class="d-flex justify-content-start align-items-center gap-3 hover_linksets {{ request()->routeIs('organization.dashboard') ? 'active_link' : '' }}">
                                                    <img class="img_side_logo mb-0"
                                                         src="{{ asset('assets/assestsnew/dasboard.svg') }}"
                                                         alt="">
                                                    <a class="px-0"
                                                       href="{{ route('admin.dashboard') }}">Dashboard</a>
                                                </div>

                                                <div
                                                    class="d-flex justify-content-start align-items-center gap-1 hover_linksets {{ request()->routeIs('organization.screenshot.index') ? 'active_link' : '' }}">
                                                    <img
                                                        src="{{ asset('assets/assestsnew/subscription.svg') }}"

                                                        alt="">
                                                    <a href="{{ route('general.plans.index') }}">Subscriptions</a>
                                                </div>


                                                <div
                                                    class="d-flex justify-content-start align-items-center gap-1 hover_linksets {{ request()->routeIs('admin.users.*', 'admin.debug.index', 'admin.debug.toggleDebugMode') ? 'active_link' : '' }}">
                                                    <img src="{{ asset('assets/assestsnew/company.svg') }}"
                                                         alt="">
                                                    <a href="{{ route('admin.users.index') }}">Company</a>
                                                </div>

                                                <div
                                                    class="d-flex justify-content-start align-items-center gap-1 hover_linksets {{ request()->routeIs('admin.systems.*') ? 'active_link' : '' }}">
                                                    <img src="{{ asset('assets/assestsnew/settingslogo.svg') }}"
                                                         alt="">
                                                    <a href="{{ route('admin.systems.index') }}">Settings</a>
                                                </div>

                                                <div
                                                    class="d-flex justify-content-start align-items-center gap-1 hover_linksets {{ request()->routeIs('general.blogs.*') ? 'active_link' : '' }}">
                                                    <img src="{{ asset('assets/assestsnew/FolderSetting.svg') }}"
                                                         alt="">
                                                    <a href="{{ route('general.blogs.index') }}">Blogs</a>
                                                </div>

                                                <div
                                                    class="d-flex justify-content-start align-items-center gap-1 hover_linksets {{ request()->routeIs('admin.other-users.*') ? 'active_link' : '' }}">
                                                    <img src="{{ asset('assets/assestsnew/team.svg') }}"
                                                         alt="" style="width: 28px !important;">
                                                    <a href="{{ route('admin.other-users.index') }}">Staffs</a>
                                                </div>
                                            </div>
                                        @else

                                            <div class="text-center main_icononly">
                                                <div
                                                    class="sidebar_logo {{ request()->routeIs('organization.dashboard') ? 'active_logo' : '' }}">
                                                    <img
                                                        class="img_side_logo {{ request()->routeIs('organization.dashboard') ? 'active_side_logo' : '' }}"
                                                        src="{{ asset('assets/assestsnew/dasboard.svg') }}"
                                                        alt="">
                                                </div>

                                                @if (Auth::user()->can('manage blogs'))
                                                    <div
                                                        class="sidebar_logo {{ request()->routeIs('general.blogs.*') ? 'active_logo' : '' }}">
                                                        <img
                                                            class="img_side_logo {{ request()->routeIs('general.blogs.*') ? 'active_side_logo' : '' }}"
                                                            src="{{ asset('assets/assestsnew/FolderSetting.svg') }}"
                                                            alt="">
                                                    </div>
                                                @endif

                                            </div>

                                            <div class="row main_text_link">
                                                <div
                                                    class="d-flex justify-content-start align-items-center gap-3 hover_linksets {{ request()->routeIs('organization.dashboard') ? 'active_link' : '' }}">
                                                    <img class="img_side_logo mb-0"
                                                         src="{{ asset('assets/assestsnew/dasboard.svg') }}"

                                                         alt="">
                                                    <a class="px-0"
                                                       href="{{ route('admin.dashboard') }}">Dashboard</a>
                                                </div>
                                                @if (Auth::user()->can('manage blogs'))
                                                    <div
                                                        class="d-flex justify-content-start align-items-center gap-1 hover_linksets {{ request()->routeIs('general.blogs.*') ? 'active_link' : '' }}">
                                                        <img src="{{ asset('assets/assestsnew/FolderSetting.svg') }}"
                                                             alt="">
                                                        <a href="{{ route('general.blogs.index') }}">Blogs</a>
                                                    </div>
                                                @endif
                                            </div>
                                        @endif
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
