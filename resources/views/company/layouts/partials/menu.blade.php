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
                                        @if ($user->hasRole(['administrator']) || $user->can('dashboard'))
                                            <div
                                                class="sidebar_logo {{ request()->routeIs('organization.dashboard') ? 'active_logo' : '' }}">
                                                <img
                                                    class="img_side_logo {{ request()->routeIs('organization.dashboard') ? 'active_side_logo' : '' }}"
                                                    src="{{ asset('assets/assestsnew/dasboard.svg') }}"
                                                    alt="">
                                            </div>
                                        @endif

                                        @if ($user->hasRole(['standard user']))
                                            <div
                                                class="sidebar_logo {{ request()->routeIs('employee.self-report') ? 'active_logo' : '' }}">
                                                <img
                                                    class="img_side_logo {{ request()->routeIs('employee.self-report') ? 'active_side_logo' : '' }}"
                                                    src="{{ asset('assets/assestsnew/my-report.svg') }}"
                                                    alt="">
                                            </div>
                                        @endif

                                        @if ($user->hasRole(['administrator']) || $user->can('screenshot'))
                                            <div
                                                class="sidebar_logo {{ request()->routeIs('organization.screenshot.index') ? 'active_logo' : '' }}">
                                                <img
                                                    class="img_side_logo {{ request()->routeIs('organization.screenshot.index') ? 'active_side_logo' : '' }}"
                                                    src="{{ asset('assets/assestsnew/screenshotlogo.svg') }}"
                                                    alt="">
                                            </div>
                                        @endif

                                        @if ($user->hasRole(['administrator']) || $user->can('live_shot'))
                                            <div
                                                class="sidebar_logo {{ request()->routeIs('organization.live_screenshot.index') ? 'active_logo' : '' }}">
                                                <img
                                                    class="img_side_logo {{ request()->routeIs('organization.live_screenshot.index') ? 'active_side_logo' : '' }}"
                                                    src="{{ asset('assets/assestsnew/livestreamlogo.svg') }}"
                                                    alt="">
                                            </div>
                                        @endif

                                        @if ($user->hasRole(['administrator']) || $user->can('live_cam_shot'))
                                            <div
                                                class="sidebar_logo {{ request()->routeIs('organization.live_cam_shot.index') ? 'active_logo' : '' }}">
                                                <img
                                                    class="img_side_logo {{ request()->routeIs('organization.live_cam_shot.index') ? 'active_side_logo' : '' }}"
                                                    src="{{ asset('assets/assestsnew/web-camera-menu.svg') }}"
                                                    alt="">
                                            </div>
                                        @endif

                                        @if ($user->hasRole(['administrator']) || $user->can('apps_and_urls'))
                                            <div
                                                class="sidebar_logo {{ request()->routeIs('organization.apps_and_urls.index') ? 'active_logo' : '' }}">
                                                <img
                                                    class="img_side_logo {{ request()->routeIs('organization.apps_and_urls.index') ? 'active_side_logo' : '' }}"
                                                    src="{{ asset('assets/assestsnew/app-urllogo.svg') }}"
                                                    alt="">
                                            </div>
                                        @endif

                                        @if ($user->hasRole(['administrator']) || ($user->can('reports') ||
                                            $user->can('break_report') ||
                                            $user->can('daily_attendance_report') ||
                                            $user->can('activity_report') ||
                                            $user->can('apps_and_urls_report') ||
                                            $user->can('highlights_report')))
                                            <div
                                                class="sidebar_logo {{ request()->routeIs('organization.report.*') ? 'active_logo' : '' }}">
                                                <img
                                                    class="img_side_logo {{ request()->routeIs('organization.report.*') ? 'active_side_logo' : '' }}"
                                                    src="{{ asset('assets/assestsnew/reportslogo.svg') }}"
                                                    alt="">
                                            </div>
                                        @endif

                                        @if ($user->hasRole(['administrator']) || $user->can('settings'))
                                            <div
                                                class="sidebar_logo {{ request()->routeIs('organization.settings.*','organization.setting.*') ? 'active_logo' : '' }}">
                                                <img
                                                    class="img_side_logo {{ request()->routeIs('organization.settings.*','organization.setting.*') ? 'active_side_logo' : '' }}"
                                                    src="{{ asset('assets/assestsnew/settingslogo.svg') }}"
                                                    alt="">
                                            </div>
                                        @endif

                                        {{--                                        @if ($user->hasRole(['administrator']) || $user->can('crm'))--}}
                                        {{--                                            <div--}}
                                        {{--                                                class="sidebar_logo {{ request()->routeIs('clients.*','leads.*','deals.*','form_builder.*','contract.*','pipelines.*','pipelines.*','lead_stages.*','stages.*','sources.*','labels.*','contractType.*') ? 'active_logo' : '' }}">--}}
                                        {{--                                                <img--}}
                                        {{--                                                    class="img_side_logo {{ request()->routeIs('clients.*','leads.*','deals.*','form_builder.*','contract.*','pipelines.*','pipelines.*','lead_stages.*','stages.*','sources.*','labels.*','contractType.*') ? 'active_side_logo' : '' }}"--}}
                                        {{--                                                    src="{{ asset('assets/assestsnew/crm.svg') }}"--}}
                                        {{--                                                    alt="">--}}
                                        {{--                                            </div>--}}
                                        {{--                                        @endif--}}

                                        @if ($user->hasRole([ROLE_ADMINISTRATOR,ROLE_STANDARD_USER]))
                                            <div
                                                class="sidebar_logo {{ request()->routeIs('organization.projects.*','organization.taskboard.*','organization.timesheet-list.*','calendar.*','organization.time-tracker.*','organization.bugs-report.*','organization.project_report.*','organization.project-task-stages.*') ? 'active_logo' : '' }}">
                                                <img
                                                    class="img_side_logo {{ request()->routeIs('organization.projects.*','organization.taskboard.*','organization.timesheet-list.*','calendar.*','organization.time-tracker.*','organization.bugs-report.*','organization.project_report.*','organization.project-task-stages.*') ? 'active_side_logo' : '' }}"
                                                    src="{{ asset('assets/assestsnew/projectsicon.svg') }}"
                                                    alt="">
                                            </div>
                                        @endif
                                    </div>

                                    <div class="row main_text_link scroller_tabbtnsetbefore">
                                        @if ($user->hasRole(['administrator']) || $user->can('dashboard'))
                                            <div
                                                class="d-flex justify-content-start align-items-center gap-3 hover_linksets {{ request()->routeIs('organization.dashboard') ? 'active_link' : '' }}">
                                                <img class="img_side_logo mb-0"
                                                     src="{{ asset('assets/assestsnew/dasboard.svg') }}"
                                                     alt="">
                                                <a class="px-0"
                                                   href="{{ route('organization.dashboard') }}">Dashboard</a>
                                            </div>
                                        @endif

                                        @if ($user->hasRole(['standard user']))
                                            <div
                                                class="d-flex justify-content-start align-items-center gap-3 hover_linksets {{ request()->routeIs('employee.self-report') ? 'active_link' : '' }}">
                                                <img class="img_side_logo mb-0"
                                                     src="{{ asset('assets/assestsnew/my-report.svg') }}"
                                                     alt="">
                                                <a class="px-0"
                                                   href="{{ route('employee.self-report') }}">My
                                                    Reports</a>
                                            </div>
                                        @endif

                                        @if ($user->hasRole(['administrator']) || $user->can('screenshot'))
                                            <div
                                                class="d-flex justify-content-start align-items-center gap-1 hover_linksets {{ request()->routeIs('organization.screenshot.index') ? 'active_link' : '' }}">
                                                <img
                                                    src="{{ asset('assets/assestsnew/screenshotlogo.svg') }}"
                                                    alt="">
                                                <a href="{{ route('organization.screenshot.index') }}">Screenshots</a>
                                            </div>
                                        @endif

                                        @if ($user->hasRole(['administrator']) || $user->can('live_shot'))
                                            <div
                                                class="d-flex justify-content-start align-items-center gap-1 hover_linksets {{ request()->routeIs('organization.live_screenshot.index') ? 'active_link' : '' }}">
                                                <img
                                                    src="{{ asset('assets/assestsnew/livestreamlogo.svg') }}"
                                                    alt="">
                                                <a href="{{ route('organization.live_screenshot.index') }}">Live
                                                    Shot</a>
                                            </div>
                                        @endif

                                        @if ($user->hasRole(['administrator']) || $user->can('live_cam_shot'))
                                            <div
                                                class="d-flex justify-content-start align-items-center gap-1 hover_linksets {{ request()->routeIs('organization.live_cam_shot.index') ? 'active_link' : '' }}">
                                                <img
                                                    src="{{ asset('assets/assestsnew/web-camera-menu.svg') }}"
                                                    alt="">
                                                <a href="{{ route('organization.live_cam_shot.index') }}">Cam
                                                    Shot</a>
                                            </div>
                                        @endif

                                        @if ($user->hasRole(['administrator']) || $user->can('apps_and_urls'))
                                            <div
                                                class="d-flex justify-content-start align-items-center gap-1 hover_linksets {{ request()->routeIs('organization.apps_and_urls.index') ? 'active_link' : '' }}">
                                                <img
                                                    src="{{ asset('assets/assestsnew/app-urllogo.svg') }}"
                                                    alt="">
                                                <a href="{{ route('organization.apps_and_urls.index') }}">App
                                                    &
                                                    URLs</a>
                                            </div>
                                        @endif

                                        @if ($user->hasRole(['administrator']) || ($user->can('reports') ||
                                            $user->can('break_report') ||
                                            $user->can('daily_attendance_report') ||
                                            $user->can('activity_report') ||
                                            $user->can('apps_and_urls_report') ||
                                            $user->can('highlights_report')))
                                            <div
                                                class="d-flex justify-content-start align-items-center gap-1 hover_linksets {{ request()->routeIs('organization.report.*') ? 'active_link' : '' }}">
                                                <img
                                                    src="{{ asset('assets/assestsnew/reportslogo.svg') }}"
                                                    alt="">
                                                <a href="{{ route('organization.report.index') }}">Reports</a>
                                            </div>
                                        @endif

                                        @if ($user->hasRole(['administrator']) || $user->can('settings'))
                                            <div
                                                class="d-flex justify-content-start align-items-center gap-1 hover_linksets {{ request()->routeIs('organization.settings.*','organization.setting.*') ? 'active_link' : '' }}">
                                                <img
                                                    src="{{ asset('assets/assestsnew/settingslogo.svg') }}"
                                                    alt="">
                                                <a href="{{ route('organization.settings.user') }}">Settings</a>
                                            </div>
                                        @endif

                                        {{--                                        @if ($user->hasRole(['administrator']) || $user->can('crm'))--}}
                                        {{--                                            <div class="menudropdownset px-0">--}}
                                        {{--                                                <div--}}
                                        {{--                                                    class="d-flex justify-content-start align-items-center gap-1 hover_linksets collapse-toggle {{ request()->routeIs('clients.*','leads.*','deals.*','form_builder.*','contract.*','pipelines.*','pipelines.*','lead_stages.*','stages.*','sources.*','labels.*','contractType.*') ? 'active_link' : '' }}"--}}
                                        {{--                                                    data-target="#crmdrop"--}}
                                        {{--                                                    role="button"--}}
                                        {{--                                                >--}}
                                        {{--                                                    <img src="{{ asset('assets/assestsnew/crm.svg') }}" alt="">--}}
                                        {{--                                                    <span class="text-nowrap">CRM</span>--}}
                                        {{--                                                </div>--}}
                                        {{--                                                <div class="collapse pt-2 ps-4" id="crmdrop">--}}
                                        {{--                                                    <ul class="list-unstyled mb-0">--}}
                                        {{--                                                        <li><a class="nav-link py-1"--}}
                                        {{--                                                               href="{{ route('clients.index') }}">Clients</a>--}}
                                        {{--                                                        </li>--}}
                                        {{--                                                        <li><a class="nav-link py-2"--}}
                                        {{--                                                               href="{{ route('leads.index') }}">Leads</a></li>--}}
                                        {{--                                                        <li><a class="nav-link py-2"--}}
                                        {{--                                                               href="{{ route('deals.index') }}">Deals</a></li>--}}
                                        {{--                                                        <li><a class="nav-link py-2"--}}
                                        {{--                                                               href="{{ route('form_builder.index') }}">Form--}}
                                        {{--                                                                Builder</a>--}}
                                        {{--                                                        </li>--}}
                                        {{--                                                        <li><a class="nav-link py-2"--}}
                                        {{--                                                               href="{{ route('contract.index') }}">Contract</a>--}}
                                        {{--                                                        </li>--}}
                                        {{--                                                        <li><a class="nav-link py-2 text-nowrap"--}}
                                        {{--                                                               href="{{ route('pipelines.index') }}">CRM--}}
                                        {{--                                                                System Setup</a></li>--}}
                                        {{--                                                    </ul>--}}
                                        {{--                                                </div>--}}
                                        {{--                                            </div>--}}
                                        {{--                                        @endif--}}

                                        @if ($user->hasRole([ROLE_ADMINISTRATOR,ROLE_STANDARD_USER]))
                                            <div class="menudropdownset px-0">
                                                <div
                                                    class="d-flex justify-content-start align-items-center gap-1 hover_linksets collapse-toggle {{ request()->routeIs('organization.projects.*','organization.taskboard.*','organization.timesheet-list.*','calendar.*','organization.time-tracker.*','organization.bugs-report.*','organization.project_report.*','organization.project-task-stages.*') ? 'active_link' : '' }}"
                                                    data-target="#projectsdrop"
                                                    role="button"
                                                >
                                                    <img src="{{ asset('assets/assestsnew/projectsicon.svg') }}"
                                                         alt="">
                                                    <span class="text-nowrap">Projects</span>
                                                </div>
                                                <div class="collapse pt-2 ps-4" id="projectsdrop">
                                                    <ul class="list-unstyled mb-0">
                                                        <li><a class="nav-link py-2"
                                                               href="{{ route('organization.projects.index') }}">Projects</a>
                                                        </li>
                                                        {{--                                                    <li><a class="nav-link py-2"--}}
                                                        {{--                                                           href="{{ route('organization.taskBoard.view', 'list') }}">Tasks</a>--}}
                                                        {{--                                                    </li>--}}
                                                        {{--                                                    <li><a class="nav-link py-2"--}}
                                                        {{--                                                           href="{{ route('organization.bugs.view', 'list') }}">Bugs</a>--}}
                                                        {{--                                                    </li>--}}
                                                        <li><a class="nav-link py-2"
                                                               href="{{ route('organization.bugstatus.index') }}">Bug
                                                                System Setup</a>
                                                        </li>
                                                        <li><a class="nav-link text-nowrap py-2"
                                                               href="{{ route('project-task-stages.index') }}">Project
                                                                System Setup</a></li>
                                                    </ul>
                                                </div>
                                            </div>
                                        @endif
                                        
                                        @if ($user->hasRole([ROLE_ADMINISTRATOR]) || $user->can('crm'))
                                            <div class="menudropdownset px-0">
                                                <div
                                                    class="d-flex justify-content-start align-items-center gap-1 hover_linksets collapse-toggle {{ request()->routeIs('organization.leads.*', 'organization.pipelines.*', 'organization.stages.*', 'organization.labels.*','organization.lead_stages.*', 'organization.sources.*') ? 'active_link' : '' }}"
                                                    data-target="#crmdrop"
                                                    role="button"
                                                >
                                                    <img src="{{ asset('assets/assestsnew/projectsicon.svg') }}"
                                                         alt="">
                                                    <span class="text-nowrap">CRM</span>
                                                </div>
                                                <div class="collapse pt-2 ps-4" id="crmdrop">
                                                    <ul class="list-unstyled mb-0">
                                                        <li><a class="nav-link py-2"
                                                               href="{{ route('organization.leads.list') }}">Leads</a>
                                                        </li>
                                                        @if ($user->hasRole([ROLE_ADMINISTRATOR]))
                                                        <li><a class="nav-link py-2"
                                                               href="{{ route('organization.pipelines.index') }}">CRM Setup</a>
                                                        </li>
                                                        @endif
                                                    </ul>
                                                </div>
                                            </div>
                                        @endif
                                    </div>

                                    @if ($user->hasRole(['administrator']))
                                        <div class="row justify-content-center align-items-end">
                                            <div
                                                class="flex-wrap pt-3 justify-content-center p-3 gap-3 btn_sidebartabsmain">
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
                                    @endif
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
