@extends('company.layouts.company')

@section('page-title')
    {{ __('Break') }}
@endsection

@section('page-icon')
    {{ asset('assets/assestsnew/header-logo.svg') }}
@endsection

@push('css-page')
@endpush

@push('theme-script')
@endpush
@push('script-page')


@endpush

@section('content')
    @include('company.layouts.partials.nav')

    <!-- Screenshort Based Task -->
    <div class="col-12">
        <div class="inform_box">
            <div class="row">
                <div class="col-3">
                    <h4 class="fw-medium fs-5" style="color: #316FF6;">
                        <a class="text-decoration-none" href="">ABC Project 01</a>
                    </h4>
                    <div class="d-flex align-items-center gap-2">
                        <div
                            style="background-color: #FCB424; width: 10px; height: 10px; border-radius: 50%; display: inline-block;">
                        </div>
                        <p class="mb-0 fs-6 text-black">In Progress</p>
                    </div>
                </div>
                <div class="col-3">
                    <h4 class="fw-normal text-black fs-6">Members</h4>
                    <div class="d-flex align-items-center">
                        <img src="{{ asset('assets/assestsnew/woman1.png') }}" alt="Member 1">
                        <img class="imgmove" src="{{ asset('assets/assestsnew/woman2.png') }}" alt="Member 2">
                        <img class="imgmove" src="{{ asset('assets/assestsnew/newman1.png') }}" alt="Member 3">
                        <img class="imgmove" src="{{ asset('assets/assestsnew/newman1.png') }}" alt="Member 3">
                        <img class="imgmove" src="{{ asset('assets/assestsnew/newman1.png') }}" alt="Member 3">
                        <img class="imgmove" src="{{ asset('assets/assestsnew/newman1.png') }}" alt="Member 3">
                    </div>
                </div>
                <div class="col-3">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1 me-2">
                            <small class="fw-semibold">20%</small>
                            <div class="progress mt-3" style="height: 8px;">
                                <div class="custom-progress-red " style="width: 20%;"></div>
                            </div>
                        </div>

                    </div>
                </div>
                <div class="col-3 d-flex flex-column align-items-end">
                    <div class="tag_1">
                        <p class="mb-0">Medium</p>
                    </div>
                    <p class="mb-0 fw-normal" style="color: #A2A2A2;">Due Date: 25 Jan 2025</p>
                </div>
            </div>
        </div>
        <div class="row p-3">
            <div class="col-12 main_pagebox">
                <div class="row p-3">
                    <div class="col-md-4 borderscorll my-2">
                        <div class="row">
                            <div class="col-12 bg_graytops">
                                <div class="d-flex justify-content-between p-2">
                                    <span>User</span>
                                    <span>Total: 08</span>
                                </div>
                            </div>
                            <div class="col-12 heigh_scrolldash">
                                <div class="row">
                                    <div class="col-12 profile_showscroll">
                                        <div class="d-flex align-items-center p-2">
                                            <img src="{{ asset('assets/assestsnew/profile.png') }}" alt="">
                                            <small>Rebekah Coleman</small>
                                        </div>
                                    </div>
                                    <div class="col-12 profile_showscroll">
                                        <div class="d-flex align-items-center p-2">
                                            <img src="{{ asset('assets/assestsnew/profile.png') }}" alt="">
                                            <small>Rebekah Coleman</small>
                                        </div>
                                    </div>
                                    <div class="col-12 profile_showscroll">
                                        <div class="d-flex align-items-center p-2">
                                            <img src="{{ asset('assets/assestsnew/profile.png') }}" alt="">
                                            <small>Rebekah Coleman</small>
                                        </div>
                                    </div>
                                    <div class="col-12 profile_showscroll">
                                        <div class="d-flex align-items-center p-2">
                                            <img src="{{ asset('assets/assestsnew/profile.png') }}" alt="">
                                            <small>Rebekah Coleman</small>
                                        </div>
                                    </div>
                                    <div class="col-12 profile_showscroll">
                                        <div class="d-flex align-items-center p-2">
                                            <img src="{{ asset('assets/assestsnew/profile.png') }}" alt="">
                                            <small>Rebekah Coleman</small>
                                        </div>
                                    </div>
                                    <div class="col-12 profile_showscroll">
                                        <div class="d-flex align-items-center p-2">
                                            <img src="{{ asset('assets/assestsnew/profile.png') }}" alt="">
                                            <small>Rebekah Coleman</small>
                                        </div>
                                    </div>
                                    <div class="col-12 profile_showscroll">
                                        <div class="d-flex align-items-center p-2">
                                            <img src="{{ asset('assets/assestsnew/profile.png') }}" alt="">
                                            <small>Rebekah Coleman</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-8 my-2">
                        <div class="row">
                            <div class="col-12 heightdash_scroll">
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="row align-items-center justify-content-end blue_spantimes">
                                            <span>09:00 AM</span><i class="fas fa-circle"></i>
                                        </div>
                                    </div>
                                    <div class="col-md-9">
                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <div class="row px-2">
                                                    <div class="col-12 blue_bg_spans" id="popupTrigger"
                                                         data-bs-toggle="modal" data-bs-target="#screenPopup">
                                                        <div class="row">
                                                            <span>09:10 AM</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <div class="row px-2">
                                                    <div class="col-12 blue_bg_spans" id="popupTrigger"
                                                         data-bs-toggle="modal" data-bs-target="#screenPopup">
                                                        <div class="row">
                                                            <span>09:10 AM</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <div class="row px-2">
                                                    <div class="col-12 blue_bg_spans" id="popupTrigger"
                                                         data-bs-toggle="modal" data-bs-target="#screenPopup">
                                                        <div class="row">
                                                            <span>09:10 AM</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <div class="row px-2">
                                                    <div class="col-12 blue_bg_spans" id="popupTrigger"
                                                         data-bs-toggle="modal" data-bs-target="#screenPopup">
                                                        <div class="row">
                                                            <span>09:10 AM</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <div class="row px-2">
                                                    <div class="col-12 blue_bg_spans" id="popupTrigger"
                                                         data-bs-toggle="modal" data-bs-target="#screenPopup">
                                                        <div class="row">
                                                            <span>09:10 AM</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Screen Popup Mode -->
    <div class="modal fade modal-xl" id="screenPopup" tabindex="-1" aria-labelledby="screenPopupLabel"
         aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content rounded-4 overflow-hidden p-0">
                <div class="modal-body p-0 bg-light">
                    <div class="container-fluid g-0">
                        <div class="row g-0">
                            <div class="col-12">
                                <div class="d-flex justify-content- align-items-center p-3 border-bottom"
                                     style="background-color: #E9F2F4;">
                                    <div>
                                        <span class="fw-semibold">Krishna Kumar -</span>
                                        <span class="fw-semifold"> 02/04/2025 &nbsp; 03:00 PM</span>
                                    </div>
                                    <div class="ms-auto">
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                aria-label="Close"></button>
                                    </div>
                                </div>
                            </div>
                        </div>


                        <div class="row g-0 h-100">
                            <div class="col-md-7 p-3">
                                <div class="h-100 position-relative">
                                    <img src="{{ asset('assets/assestsnew/pop_img.png') }}"
                                         class="img-fluid w-100 h-100"
                                         alt="Screen Snapshot">
                                    <button class="btn_round">
                                        <img src="{{ asset('assets/assestsnew/maximize-2.svg') }}" alt="Maximize">
                                    </button>
                                </div>
                            </div>

                            <div class="col-md-5 p-3 h-100">
                                <div class="light_blue_box">
                                    <h5 class="p-3 mb-0">Application Log</h5>
                                </div>
                                <div class="bg-white shadow-lg" style="border-radius: 0px 0px 10px 10px;">
                                    <div class="row justify-content-center">
                                        <div class="col-md-11">
                                            <div class="bg-light rounded-3 p-2 mt-3 mb-3">
                                                <div class="d-flex align-items-center gap-1">
                                                    <img src="{{ asset('assets/assestsnew/figma.png') }}"
                                                         alt="Figma Logo"
                                                         style="width: 40px; height: 40px;">
                                                    <div>
                                                        <h6 class="text-black fw-semibold mb-1">Figma</h6>
                                                        <p class="text-muted mb-0">Untitled file - figma</p>
                                                    </div>

                                                    <div
                                                        class="d-flex align-items-center justify-content-start gap-1 ms-auto">
                                                        <img src="{{ asset('assets/assestsnew/clock.svg') }}" alt="">
                                                        <p class="mb-0" style="color: #676767;">08m:00s</p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row justify-content-center">
                                        <div class="col-md-11">
                                            <div class="bg-light rounded-3 p-2 mt-3 mb-3">
                                                <div class="d-flex align-items-center gap-1">
                                                    <img src="{{ asset('assets/assestsnew/figma.png') }}"
                                                         alt="Figma Logo"
                                                         style="width: 40px; height: 40px;">
                                                    <div>
                                                        <h6 class="text-black fw-semibold mb-1">Figma</h6>
                                                        <p class="text-muted mb-0">Untitled file - figma</p>
                                                    </div>

                                                    <div
                                                        class="d-flex align-items-center justify-content-start gap-1 ms-auto">
                                                        <img src="{{ asset('assets/assestsnew/clock.svg') }}" alt="">
                                                        <p class="mb-0" style="color: #676767;">08m:00s</p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                            </div>
                            <div class="row m-0 p-3">
                                <div class="col-12 main_popupscreenshort">
                                    <div class="row justify-content-center">
                                        <div class="col-12 my-3">
                                            <div class="d-flex justify-content-between">
                                                <h5>Productivity Details</h5>
                                                <span>Activity Level : <b>50%</b></span>
                                            </div>
                                        </div>
                                        <div class="col-12 my-3">
                                            <div class="progress" role="progressbar" aria-label="Info example"
                                                 aria-valuenow="50" aria-valuemin="0" aria-valuemax="100">
                                                <div class="progress-bar bg-info" style="width: 50%"></div>
                                            </div>
                                        </div>
                                        <div class="col-md-8 my-3 popupgryboxes">
                                            <div class="row">
                                                <div class="col-md-4 my-2">
                                                    <div class="row text-center">
                                                        <b>10m:00s</b>
                                                        <span>Duration</span>
                                                    </div>
                                                </div>
                                                <div class="col-md-4 my-2">
                                                    <div class="row text-center">
                                                        <b>15</b>
                                                        <span>Key Presses</span>
                                                    </div>
                                                </div>
                                                <div class="col-md-4 my-2">
                                                    <div class="row text-center">
                                                        <b>25</b>
                                                        <span>Mouse Click</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

