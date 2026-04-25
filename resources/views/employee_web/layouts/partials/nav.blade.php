@php
    $logo = Utility::get_file('uploads/logo');
    $company_favicon = $setting['company_favicon'] ?? '';
@endphp
<div class="col-12">
    <div class="row align-items-center justify-content-center mt-5">
        <div class="header_fixedshow">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <div class="d-flex justify-content-start align-items-center gap-2">
                    <img class="me-md-4" src="{{ $logo . '/' . (!empty($company_favicon) ? $company_favicon : 'favicon.png') . '?' . time() }}"
                             alt="Header Icon" width="60" height="60"/>
                     <img src="@yield('page-icon', asset('assets/assestsnew/download-logo.svg'))" 
             alt="Header Icon" width="32" height="32"/>
        
                        <h5 class="fw-semibold fs-2 let_1 mb-0">@yield('page-title')</h5>
                    </div>
                </div>
        
                <div class="col-md-6">
                    <div class="d-flex justify-content-end align-items-center flex-wrap gap-3 mt-3 mt-md-0">
        
                        <div>
                            @if(\Auth::user()->type == 'company' )
                                @impersonating($guard = null)
                                <a class="btn btn-danger btn-sm" href="{{ route('admin.exit.company') }}"><i
                                        class="ti ti-ban"></i>
                                    {{ __('Exit Login') }}
                                </a>
                                @endImpersonating
                            @endif
                        </div>
                        <!--<div class="help">-->
                        <!--    <div class="dropdown text-center position-relative">-->
                        <!--        <img src="https://loov.in/app2/assets/assestsnew/help-circle.svg" alt="">-->
                        <!--        <a class="dropdown-toggle fw-semibold text-decoration-none text-primary"-->
                        <!--           href="#"-->
                        <!--           role="button" data-bs-toggle="dropdown" aria-expanded="false">-->
                        <!--            Help-->
                        <!--        </a>-->
                        <!--        <ul-->
                        <!--            class="dropdown-menu dropdown-menu-center bg-transparent border-0 p-0 rounded-0">-->
                        <!--            <div class="container-center">-->
                        <!--                <div class="account-card">-->
                        <!--                    <a href="#" class="menu-item self_hov1 px-2"-->
                        <!--                       style="color: #6B6B6B;">-->
                        <!--                        <div class="menu-icon">-->
                        <!--                            <img src="https://loov.in/app2/assets/assestsnew/docs.svg"-->
                        <!--                                 alt="">-->
                        <!--                        </div>-->
                        <!--                        Documentation-->
                        <!--                    </a>-->
        
                        <!--                    <a href="#" class="menu-item self_hov1 px-2"-->
                        <!--                       style="color: #6B6B6B;">-->
                        <!--                        <div class="menu-icon">-->
                        <!--                            <img-->
                        <!--                                src="https://loov.in/app2/assets/assestsnew/take-care.svg"-->
                        <!--                                alt="">-->
                        <!--                        </div>-->
                        <!--                        Onboarding Support-->
                        <!--                    </a>-->
        
                        <!--                    <a href="#" class="menu-item self_hov1 px-2"-->
                        <!--                       style="color: #6B6B6B;">-->
                        <!--                        <div class="menu-icon">-->
                        <!--                            <img-->
                        <!--                                src="https://loov.in/app2/assets/assestsnew/problem.svg"-->
                        <!--                                alt="">-->
                        <!--                        </div>-->
                        <!--                        Report Issue-->
                        <!--                    </a>-->
                        <!--                </div>-->
                        <!--            </div>-->
                        <!--        </ul>-->
                        <!--    </div>-->
                        <!--</div>-->
        
                        <button
                            class="fw-normal but_1 text-white d-flex align-items-center gap-2 text-nowrap px-4 py-2"
                            data-bs-toggle="modal"
                            data-bs-target="#downloadModal">
                            <img  src="{{ asset('assets/assestsnew/download-logo.svg') }}"  
                                 alt="Download Icon"/>
                            Download LOOV
                        </button>
        
                        <div class="d-flex align-items-center gap-2">
        
        
        <img src="{{ asset('assets/assestsnew/menimg.png') }}"  
             alt="User Image"
             class="rounded-circle" 
             width="38" 
             height="38" />
                            <div class="dropdown">
                                <a class="dropdown-toggle fw-semibold text-decoration-none text-dark"
                                   type="button"
                                   data-bs-toggle="dropdown" aria-expanded="false">
                                    {{ \Illuminate\Support\Facades\Auth::user()->name }}
                                </a>
                                <ul class="dropdown-menu border-0 p-0 bg-transparent dropdown-menu-end">
                                    <div class="container-center">
                                        <div class="account-card">
                                            <div class="profile-section">
                                                <div class="profile-pic">
        
        
        <img src="{{ asset('assets/assestsnew/menimg.png') }}"  
             alt="User Image"
             class="rounded-circle" 
             width="38" 
             height="38" />
                                                </div>
                                                <div>
                                                    <h6 class="mb-0 text-black fw-semibold fs-6">{{ \Illuminate\Support\Facades\Auth::user()->company_name }}
                                                    </h6>
                                                    <small
                                                        style="color: #A2A2A2;">{{ \Illuminate\Support\Facades\Auth::user()->email }}</small>
                                                </div>
                                            </div>
        
                                
        
                                            <a href="#" class="menu-item self_hov1 px-2"
                                               style="color: #6B6B6B;">
                                                <div class="menu-icon">
                                                    <img src="{{ asset('assets/assestsnew/download.svg') }}"
                                                        alt="">
                                                </div>
                                                Download App
                                            </a>
        
                                            <!--                              <a href="#" onclick="event.preventDefault(); document.getElementById('frm-logout').submit();" class="dropdown-item">-->
                                            <!--    <i class="ti ti-power text-dark"></i><span>{{ __('Logout') }}</span>-->
                                            <!--</a>-->
        
                                            <!--<form id="frm-logout" action="{{ route('auth.logout') }}" method="POST" class="d-none">-->
                                            <!--    @csrf-->
                                            <!--</form>-->
        
        
                                            <form id="logout-form" action="{{ route('auth.logout') }}" method="POST"
                                                  style="display: none;">
                                                @csrf
                                            </form>
        
                                            <a href="#" class="menu-item self_hov1 px-2" style="color: #6B6B6B;"
                                               onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                                <div class="menu-icon">
                                                 
                                                    
                                                    <img src="{{ asset('assets/assestsnew/log-out.svg') }}" alt="Logout">
        
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
</div>


<!--Model For Download Button -->


<!-- Download Modal -->
<div class="modal fade" id="downloadModal" tabindex="-1" aria-labelledby="downloadModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-md">
        <div class="modal-content p-4">

            <div class="modal-header border-0">
                <h5 class="modal-title fw-bold" id="downloadModalLabel">Download LOOV</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <span class="fw-bold">Windows (Stealth User)</span>
                    <a href="#" class="btn  but_1 text-white">Download LOOV</a>
                </div>
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <span class="fw-bold">Windows (Standard User)</span>
                    <a href="#" class="btn  but_1 text-white">Download LOOV</a>
                </div>
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <span class="fw-bold">Mac (Stealth User)</span>
                    <a href="#" class="btn  but_1 text-white">Download LOOV</a>
                </div>
                <div class="d-flex justify-content-between align-items-center">
                    <span class="fw-bold">Mac (Standard User)</span>
                    <a href="#" class="btn  but_1 text-white">Download LOOV</a>
                </div>
            </div>

        </div>
    </div>
</div>

