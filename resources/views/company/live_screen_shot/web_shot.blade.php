@extends('company.layouts.company')

@section('page-title')
    {{ __('Live Camera Shot') }}
@endsection

@section('page-icon')
    {{ asset('assets/assestsnew/web-camera-menu.svg') }}
@endsection

@push('css-page')
    <style>
        .disabled-btn {
            pointer-events: auto;
            opacity: 0.6;
        }
    </style>

    <style>
        #global-loader {
            position: fixed;
            top: 0;
            left: 0;
            height: 100%;
            width: 100%;
            background-color: rgb(0, 0, 0, 0.8);
            z-index: 9999;
            display: none;
            justify-content: center;
            align-items: center;
        }

        #global-loader img {
            width: 100px;
        }

        @-webkit-keyframes rotating {
            from {
                -webkit-transform: rotate(0deg);
                -o-transform: rotate(0deg);
                transform: rotate(0deg);
            }
            to {
                -webkit-transform: rotate(360deg);
                -o-transform: rotate(360deg);
                transform: rotate(360deg);
            }
        }

        @keyframes rotating {
            from {
                -ms-transform: rotate(0deg);
                -moz-transform: rotate(0deg);
                -webkit-transform: rotate(0deg);
                -o-transform: rotate(0deg);
                transform: rotate(0deg);
            }
            to {
                -ms-transform: rotate(360deg);
                -moz-transform: rotate(360deg);
                -webkit-transform: rotate(360deg);
                -o-transform: rotate(360deg);
                transform: rotate(360deg);
            }
        }

        .rotating_animation {
            -webkit-animation: rotating 1s linear infinite;
            -moz-animation: rotating 1s linear infinite;
            -ms-animation: rotating 1s linear infinite;
            -o-animation: rotating 1s linear infinite;
            animation: rotating 1s linear infinite;
        }
    </style>
@endpush

@push('script-page')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const modal = new bootstrap.Modal(document.getElementById('screenPopup'));
            const loader = document.getElementById('global-loader');
            const cancelBtn = document.getElementById('loader-cancel-btn');

            let pollInterval = null;

            cancelBtn.addEventListener('click', () => {
                if (pollInterval) {
                    clearInterval(pollInterval);
                    pollInterval = null;
                }
                loader.style.display = 'none';
            });

            document.querySelectorAll('.popupTrigger').forEach(trigger => {
                trigger.addEventListener('click', function (e) {
                    e.preventDefault();

                    const employeeId = this.getAttribute('data-id');
                    const isWebCam = this.classList.contains('webcam_liveshort');

                    loader.style.display = 'flex';

                    fetch("{{ route('organization.live_screenshot.getLive') }}", {
                        method: "POST",
                        headers: {
                            "Content-Type": "application/json",
                            "Accept": "application/json",
                            "X-CSRF-TOKEN": "{{ csrf_token() }}"
                        },
                        body: JSON.stringify({
                            employee_id: employeeId,
                            is_web_cam: isWebCam
                        })
                    })
                        .then(response => response.json())
                        .then(data => {
                            if (data.status === 'success') {
                                pollScreenshotStatus(data.incident_id);
                            } else {
                                loader.style.display = 'none';
                                Swal.fire('Error', data.message || 'Unable to generate screenshot.', 'error');
                            }
                        })
                        .catch(() => {
                            loader.style.display = 'none';
                            Swal.fire('Network Error', 'Please try again later.', 'error');
                        });

                    function pollScreenshotStatus(incidentId) {
                        const maxAttempts = 12;
                        let attempts = 0;

                        pollInterval = setInterval(() => {
                            attempts++;

                            fetch(`{{ route('organization.live_screenshot.checkStatus') }}?incident_id=${incidentId}`, {
                                method: 'GET',
                                headers: {'Accept': 'application/json'}
                            })
                                .then(response => response.json())
                                .then(statusData => {
                                    if (statusData.status === 'success') {
                                        clearInterval(pollInterval);
                                        pollInterval = null;
                                        loader.style.display = 'none';

                                        const emp = statusData.employee;
                                        document.querySelector('.employee-name').textContent = emp.name || 'N/A';
                                        document.querySelector('.employee-code').textContent = `(#${emp.employee_id || ''})`;
                                        document.querySelector('.employee-designation').textContent = emp.designation || 'No designation';
                                        document.querySelector('#screen-image').src = statusData.image_url + '?t=' + new Date().getTime();
                                        document.querySelector('.capture-time').textContent = new Date().toLocaleString();

                                        modal.show();
                                    } else if (attempts >= maxAttempts) {
                                        clearInterval(pollInterval);
                                        pollInterval = null;
                                        loader.style.display = 'none';
                                        Swal.fire('Timeout', 'Could not get the screenshot. Please try again later.', 'warning');
                                    }
                                })
                                .catch(() => {
                                    clearInterval(pollInterval);
                                    pollInterval = null;
                                    loader.style.display = 'none';
                                    Swal.fire('Error', 'Error checking screenshot status.', 'error');
                                });
                        }, 5000);
                    }
                });
            });
        });
    </script>

    <script>
        $(document).ready(function () {
            $('select[name="team_id"], select[name="employee_id"]').on('change', function () {
                $(this).closest('form').submit();
            });
        });
    </script>
@endpush

@section('content')
    @include('company.layouts.partials.nav')

    @if (session('success'))
        <script> Swal.fire('Success', '{{ session('success') }}', 'success'); </script>
    @endif

    @if (session('error'))
        <script> Swal.fire('Error', '{{ session('error') }}', 'error'); </script>
    @endif

    <div class="col-12" style="min-height: 80vh">
        <div class="row">
            <div class="col-lg-12 selecters_head">
                <div class="row gap-md-5">
                    <form method="GET" action="{{ route('organization.live_cam_shot.index') }}" class="d-flex gap-3">
                        <select class="form-select form-control select2" name="team_id" id="teamSelect"
                                style="padding-right: 2.5rem; height: 48px; min-width: 100%;">
                            <option selected disabled>All Teams</option>
                            @foreach ($teams as $team)
                                <option
                                    value="{{ $team->id }}" {{ request('team_id') == $team->id ? 'selected' : '' }}>
                                    {{ $team->name }}
                                </option>
                            @endforeach
                        </select>

                        <select class="form-select form-control select2" name="employee_id" id="employeeSelect"
                                style="padding-right: 2.5rem; height: 48px; min-width: 100%;">
                            <option selected disabled>All Employee</option>
                            @foreach ($employees as $employee)
                                <option
                                    value="{{ $employee->id }}" {{ request('employee_id') == $employee->id ? 'selected' : '' }}>
                                    {{ $employee->name }} (#{{ $employee->employee_id }})
                                </option>
                            @endforeach
                        </select>
                    </form>
                </div>
            </div>
        </div>

        {{-- Modal --}}
        <div class="modal fade" id="screenPopup" tabindex="-1" aria-labelledby="screenPopupLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">
                            Live Screenshot - <span class="employee-name">Loading...</span>
                            <small class="employee-code"></small>
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body text-center">
                        <p><strong>Designation:</strong> <span class="employee-designation"></span></p>
                        <img id="screen-image" src="" alt="Screenshot" class="img-fluid rounded"
                             style="max-height: 400px;">
                        <p class="mt-3"><small>Captured at: <span class="capture-time"></span></small></p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Employee Cards --}}
        <div class="col-12 mt-4">
            <div class="row justify-content-center p-2 mb-4">
                <div class="col-12  live_screen_box">
                    <div class="row p-lg-3 p-1 g-lg-3 g-1 cam_shot_white_box">
                        @forelse ($employees as $employee)
                            <div class="col-xl-4 col-lg-6 col-md-6 mb-3">
                                <div
                                    class="card_1 mx-auto shadow_setters h-100 d-flex justify-content-between flex-column"
                                    style="width: 300px; max-width: 100%; position: relative; background:#F6F6F6;">
                                    <img class="live_imgiconer" src="{{ asset('assets/assestsnew/live.svg') }}"
                                         alt="Live Icon">
                                    <div class="second_grid pb-3 pt-4 d-flex align-items-center gap-3 px-3"
                                         style="background:#F6F6F6;">
                                        @php
                                            $gender = $employee['gender'] ?? null;
                                        @endphp

                                        <img
                                            src="{{ $gender === GENDER_MALE ? asset('assets/assestsnew/menimg.png')
                                            : ($gender === GENDER_FEMALE
                                            ? asset('assets/assestsnew/femaile-report.svg')
                                            : asset('assets/assestsnew/profile.png')) }}"
                                            alt="Avatar"
                                            style="width: 50px; height: 50px; border-radius: 50%;"
                                        >

                                        <div>
                                            <h1 class="text-black fw-medium fs-5 mb-1">{{ $employee->name }}</h1>
                                            <div class="d-flex gap-3">
                                                <p class="num mb-0">#{{ $employee->employee_id }}</p>
                                                <p class="txt mb-0">{{ $employee->designation->name ?? 'Team' }}</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div
                                        class="second_grid py-3 d-flex align-items-center justify-content-evenly gap-3 px-3">
                                        <a href="#" class="webcam_liveshort text-white popupTrigger w-100"
                                           data-id="{{ $employee->id }}"
                                           data-name="{{ $employee->name }}"
                                           data-code="{{ $employee->employee_id }}"
                                           data-designation="{{ $employee->designation->name ?? 'Team' }}">
                                            <img src="{{ asset('assets/assestsnew/web-camera.svg') }}"
                                                 alt="Webcam Icon">Camera
                                        </a>

                                        {{--                                        <a href="#" class="system_liveshort popupTrigger"--}}
                                        {{--                                           data-id="{{ $employee->id }}"--}}
                                        {{--                                           data-name="{{ $employee->name }}"--}}
                                        {{--                                           data-code="{{ $employee->employee_id }}"--}}
                                        {{--                                           data-designation="{{ $employee->designation->name ?? 'Team' }}">--}}
                                        {{--                                            <img src="{{ asset('assets/assestsnew/monitor.svg') }}" alt="System Icon">System--}}
                                        {{--                                        </a>--}}
                                    </div>
                                </div>
                            </div>
                        @empty
                            <p class="text-center text-muted">No employees found.</p>
                        @endforelse

                        <!-- Pagination & Items Per Page -->
                        <div class="col-12 optional_inputpagi">
                            <div class="row align-items-center justify-content-center mt-4">

                                <!-- Items Per Page -->
                                <div class="col-12 col-md-3 mb-3 mb-md-0">
                                    <div class="data_table_select">
                                        <form id="perPageForm" method="GET" action="{{ url()->current() }}"
                                              class="d-flex align-items-center gap-2 m-0">
                                            {{-- Preserve filters --}}
                                            @foreach(request()->except(['page', 'per_page']) as $key => $value)
                                                @if(is_array($value))
                                                    @foreach($value as $v)
                                                        <input type="hidden" name="{{ $key }}[]" value="{{ $v }}">
                                                    @endforeach
                                                @else
                                                    <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                                                @endif
                                            @endforeach

                                            <label for="per_page" class="small mb-0 text-nowrap">Items per page:</label>

                                            <select name="per_page" id="per_page" class="form-select form-select-sm"
                                                    style="width: 90px; min-width: 80px;"
                                                    onchange="document.getElementById('perPageForm').submit()">
                                                @foreach([5,10,20,50] as $size)
                                                    <option
                                                        value="{{ $size }}" {{ request('per_page', 10) == $size ? 'selected' : '' }}>
                                                        {{ $size }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </form>
                                    </div>
                                </div>

                                <!-- Pagination Links -->
                                <div class="col-12 col-md-auto mb-3 mb-md-0">
                                    <div class="d-flex justify-content-center">
                                        <ul class="paginatio_ulist d-flex align-items-center gap-lg-4 gap-2 m-0 p-0">
                                            {{-- First Page --}}
                                            <li class="{{ $employees->onFirstPage() ? 'disabled' : '' }}">
                                                <a href="{{ $employees->url(1) }}" class="page-link1">&#171;</a>
                                            </li>

                                            {{-- Previous Page --}}
                                            <li class="{{ $employees->onFirstPage() ? 'disabled' : '' }}">
                                                <a href="{{ $employees->previousPageUrl() }}" class="page-link1">
                                                    <i class="fa-solid fa-chevron-left"></i>
                                                </a>
                                            </li>

                                            {{-- Page Numbers --}}
                                            @php
                                                $start = max(1, $employees->currentPage() - 2);
                                                $end = min($start + 4, $employees->lastPage());
                                            @endphp
                                            @for ($i = $start; $i <= $end; $i++)
                                                <li class="{{ $employees->currentPage() == $i ? 'active_pagination' : '' }}">
                                                    <a href="{{ $employees->url($i) }}" class="page-link1">{{ $i }}</a>
                                                </li>
                                            @endfor

                                            {{-- Next Page --}}
                                            <li class="{{ !$employees->hasMorePages() ? 'disabled' : '' }}">
                                                <a href="{{ $employees->nextPageUrl() }}" class="page-link1">
                                                    <i class="fa-solid fa-chevron-right"></i>
                                                </a>
                                            </li>

                                            {{-- Last Page --}}
                                            <li class="{{ !$employees->hasMorePages() ? 'disabled' : '' }}">
                                                <a href="{{ $employees->url($employees->lastPage()) }}"
                                                   class="page-link1">&#187;</a>
                                            </li>
                                        </ul>
                                    </div>
                                </div>

                                <!-- Page Jump -->
                                <div class="col-12 col-lg-3 col-md-4 mb-3 mb-md-0">
                                    <div
                                        class="d-flex flex-md-row align-items-center justify-content-center justify-content-md-start gap-2">
                                        <form action="{{ url()->current() }}" method="GET"
                                              class="d-flex flex-wrap align-items-center gap-2"
                                              style="flex-direction:row !important;">
                                            {{-- Preserve filters --}}
                                            @foreach(request()->except('page') as $key => $value)
                                                @if(is_array($value))
                                                    @foreach($value as $v)
                                                        <input type="hidden" name="{{ $key }}[]" value="{{ $v }}">
                                                    @endforeach
                                                @else
                                                    <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                                                @endif
                                            @endforeach

                                            <input type="number" name="page" min="1" max="{{ $employees->lastPage() }}"
                                                   class="form-control form-control-sm" style="width: 80px;"
                                                   placeholder="Page">
                                            <button class="btn btn-sm btn-primary" type="submit">Go</button>
                                        </form>
                                        <span class="text-nowrap small text-center text-md-start">of {{ $employees->total() }} Data</span>
                                    </div>
                                </div>

                                <!-- Showing Range -->
                                <div class="col-12">
                                    <div class="d-flex justify-content-center">
                                        <span>{{ $employees->firstItem() }} to {{ $employees->lastItem() }} of {{ $employees->total() }} Data</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Global Loader --}}
    <div id="global-loader" style="justify-content: center; align-items: center; height: 100vh;">
        <div class="main_loaderinndiv text-center p-4 bg-white rounded shadow"
             style="position: relative; max-width: 350px; width: 100%;">
            <!-- Close Button -->
            <button id="loader-cancel-btn" class="close_btn_loadermodal"
                    style="position: absolute; top: 10px; right: 10px; background: none;color:#000; border: none; font-size: 20px;">
                <i class="fa-solid fa-xmark"></i>
            </button>

            <!-- Loader Icon -->
            <h4 class="text-primary mb-3">
                <i class="fa-solid fa-spinner rotating_animation" style="margin-right: 8px;"></i> Please Wait!
            </h4>

            <!-- Main Message -->
            <p class="mb-1">Capturing employee activity…</p>
            <p class="text-muted mb-2">⏳ This may take 1–3 minutes.</p>
            <p class="text-muted mb-3">You can cancel anytime using the close button.</p>

            <!-- Note -->
            <small class="text-muted">🍁 <span class="text-danger">Note:</span> If the employee is inactive, no camshot
                will be taken.</small>
        </div>
    </div>
@endsection
