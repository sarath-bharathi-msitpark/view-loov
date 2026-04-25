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

    // Group route checks
    $isDashboard = request()->routeIs('employee.dashboard');
    $isReport = request()->routeIs('employee.attendance', 'employee.breakInsight', 'employee.activity');
@endphp

@if (isset($setting['cust_theme_bg']) && $setting['cust_theme_bg'] == 'on')
    <nav class="light-sidebar main_hover_setter">
        @else
            <nav class="dash-sidebar light-sidebar" style="height:0px">
                @endif
                <div class="">
                    <div class="row">
                        <div class="main_sidebar">
                            <div class="row px-0 h-100">
                                <div class="main_linksbar" style="padding-top:80px;">
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
                                                <img src="{{ $logo . '/' . (!empty($company_favicon) ? $company_favicon : 'logo-dark.png') . '?' . time() }}" alt="{{ config('app.name', 'Loov') }}">
                                            @else
                                                <img src="{{ $logo . '/' . (!empty($company_favicon) ? $company_favicon : 'logo-light.png') . '?' . time() }}" alt="{{ config('app.name', 'Loov') }}">
                                            @endif
                                        </div>--}}
                                        @if (Auth::user()->type == 'Employee')
                                            {{-- Sidebar Icons --}}
                                            <div class="text-center main_icononly">
                                                {{--                                                <div class="sidebar_logo {{ $isDashboard ? 'active_logo' : '' }}">--}}
                                                {{--                                                    <img--}}
                                                {{--                                                        class="img_side_logo {{ $isDashboard ? 'active_side_logo' : '' }}"--}}
                                                {{--                                                        src="{{ asset('assets/assestsnew/dasboard.svg') }}"--}}
                                                {{--                                                        alt="Dashboard Icon">--}}
                                                {{--                                                </div>--}}
                                                <div class="sidebar_logo {{ $isReport ? 'active_logo' : '' }}">
                                                    <img class="img_side_logo {{ $isReport ? 'active_side_logo' : '' }}"
                                                         src="{{ asset('assets/assestsnew/reportslogo.svg') }}"
                                                         alt="Reports Icon">
                                                </div>
                                            </div>

                                            {{-- Text Links --}}
                                            <div class="row main_text_link">
                                                {{--                                                <div--}}
                                                {{--                                                    class="d-flex justify-content-start align-items-center gap-3 {{ $isDashboard ? 'active_link' : '' }}">--}}
                                                {{--                                                    <img class="img_side_logo mb-0"--}}
                                                {{--                                                         src="{{ asset('assets/assestsnew/dasboard.svg') }}"--}}
                                                {{--                                                         alt="Dashboard">--}}
                                                {{--                                                    <a class="px-0"--}}
                                                {{--                                                       href="{{ route('employee.dashboard') }}">Dashboard</a>--}}
                                                {{--                                                </div>--}}

                                                <div
                                                    class="d-flex justify-content-start align-items-center gap-1 {{ $isReport ? 'active_link' : '' }}">
                                                    <img src="{{ asset('assets/assestsnew/reportslogo.svg') }}"
                                                         alt="Reports">
                                                    <a href="{{ route('employee.attendance') }}">Reports</a>
                                                </div>
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


