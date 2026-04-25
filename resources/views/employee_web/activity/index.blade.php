@extends('company.layouts.company')
@section('page-title')
    {{ __('Activity') }}
@endsection
@section('page-icon')
    {{ asset('assets/assestsnew/header-logo.svg') }}
@endsection
@push('css-page')
@endpush

@push('theme-script')
@endpush
@push('script-page')
    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>

    <script type="text/javascript" src="https://cdn.jsdelivr.net/jquery/latest/jquery.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css"/>

    <script>
        $(function () {
            // Default to last 7 days (including today)
            let start = moment().subtract(6, 'days');
            let end = moment();

            function cb(start, end) {
                $('#reportrange span').html(start.format('YYYY-MM-DD') + ' to ' + end.format('YYYY-MM-DD'));
                $('#start_dateattentace').val(start.format('YYYY-MM-DD'));
                $('#end_dateattentace').val(end.format('YYYY-MM-DD'));
            }

            $('#reportrange').daterangepicker({
                startDate: start,
                endDate: end,
                ranges: {
                    'Today': [moment(), moment()],
                    'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                    'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                    'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                    'This Month': [moment().startOf('month'), moment().endOf('month')],
                    'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1,
                        'month').endOf('month')]
                }
            }, cb);

            cb(start, end); // Set initial values on load
        });
    </script>

    <!-- Cart 2 in Activity -->
    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>

    <script type="text/javascript">
        google.charts.load('current', {
            'packages': ['corechart']
        });
        google.charts.setOnLoadCallback(drawCharts);

        function drawCharts() {
            const activeTime = {{ $activeHours }};
            const breakTime = {{ $breakHours }};
            drawChart('donut_single2', 'chart-center-text2', 'attendance-summary2', activeTime, breakTime);
        }

        function drawChart(chartId, centerTextId, summaryId, activeTime, breakTime) {
            var total = (activeTime + breakTime).toFixed(2);

            var data = google.visualization.arrayToDataTable([
                ['Status', 'Hours'],
                ['Active Time', activeTime],
                ['Break Time', breakTime]
            ]);

            var options = {
                pieHole: 0.7,
                legend: 'none',
                backgroundColor: 'transparent',
                pieSliceText: 'none',
                slices: {
                    0: {
                        color: '#316FF6'
                    },
                    1: {
                        color: '#E8E8E8'
                    }
                }
            };

            var chart = new google.visualization.PieChart(document.getElementById(chartId));
            chart.draw(data, options);

            document.getElementById(centerTextId).innerHTML = `
            <div style="text-align: center;">
                <h2 style="margin: 0; font-size: 25px;">${total} hrs</h2>
                <span style="font-size: 14px;">Total Hours</span>
            </div>
        `;

            document.getElementById(summaryId).innerHTML = `
            <div style="display: flex; justify-content: center; gap: 20px; margin-top: 10px; font-size: 14px;">
                <div style="display: flex; align-items: center; gap: 6px;">
                    <span style="width: 20px; height: 20px; background-color: #316FF6; border-radius: 50%; display: inline-block;"></span>
                    <span style="color: #316FF6; font-weight: 500;">
                        ${activeTime} hrs
                        <br> Active Time
                    </span>
                </div>

                <div style="display: flex; align-items: center; gap: 6px;">
                    <span style="width: 20px; height: 20px; background-color: #E8E8E8; border-radius: 50%; display: inline-block;"></span>
                    <span style="color: #999; font-weight: 500;">
                        ${breakTime} hrs
                        <br> Break Time
                    </span>
                </div>
            </div>
        `;
        }
    </script>

    <script>
        function showSection(section, element) {

            const sectionsToHide = ['attendanceContent', 'attendanceContent1', 'attendanceContent2', 'breakContent',
                'activityContent'
            ];
            sectionsToHide.forEach(id => {
                const el = document.getElementById(id);
                if (el) el.classList.add('d-none');
            });

            document.querySelectorAll('.actives, .actives1').forEach(el => el.classList.remove('active-tab'));


            if (section === 'attendance') {
                ['attendanceContent', 'attendanceContent1', 'attendanceContent2'].forEach(id => {
                    const el = document.getElementById(id);
                    if (el) el.classList.remove('d-none');
                });
            } else if (section === 'break') {
                const el = document.getElementById('breakContent');
                if (el) el.classList.remove('d-none');
            } else if (section === 'activity') {
                const el = document.getElementById('activityContent');
                if (el) el.classList.remove('d-none');
            }

            if (element) {
                element.classList.add('active-tab');
            }
        }


        document.querySelectorAll('.actives, .actives1').forEach(tab => {
            tab.addEventListener('click', function () {
                const section = this.dataset.section;
                showSection(section, this);
            });
        });
    </script>

    <script>
        tab.addEventListener('click', function () {
            const section = this.dataset.section;
            showSection(section, this);
        });
    </script>
@endpush

@section('content')
    {{--    @include('employee_web.layouts.partials.nav')--}}
    @include('company.layouts.partials.nav')

    @php
        $user = \Illuminate\Support\Facades\Auth::user()->load('employee');
    @endphp

    <div class="col-12 mb-5">
        <div class="row align-items-center">
            <div class="col-md-6">
                <div class="d-flex justify-content-start align-items-center gap-2">
                    @if ($employee)
                        <h5 class="fw-semibold fs-2 text-black mb-0">Hi,
                            {{ $employee->user->name ?? 'N/A' }}
                        </h5>
                    @else
                        <h5 class="fw-semibold fs-2 text-black mb-0">Hi, Guest</h5>
                    @endif
                </div>
            </div>
            <div class="col-lg-6 selecters_head">
                <div class="row justify-content-lg-end gx-3">
                    <form method="GET" action="{{ route('employee.activity') }}"
                          class="d-flex gap-2 justify-content-lg-end" style="flex-direction: row !important;">
                        <div class="col-auto d-flex justify-content-lg-end align-items-center gap-2">
                            <div id="reportrange"
                                 style="background: #fff; cursor: pointer; padding: 10px 12px; border: 1px solid #ccc; width: 100%; display: flex; align-items: inherit; border-radius: 100px;">
                                <i class="fa fa-calendar"></i>&nbsp;
                                <span></span> <i class="fa fa-caret-down"></i>
                            </div>
                            <input type="hidden" name="start_date" id="start_dateattentace">
                            <input type="hidden" name="end_date" id="end_dateattentace">
                        </div>
                        <button class="download_arrbtn" type="submit"><i class="fas fa-search"></i></button>
                        <span><a href="{{ route('employee.activity') }}" class="download_arrbtn"><i
                                    class="fas fa-redo-alt"
                                    style="margin:12px"></i></a>Clear Filter</span>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-12 mt-4">
            <div class="row">
                <div class="col-xl-3 col-md-12 mt-4 mt-xl-0 mb-4">
                    <div class="chart_box2" style="height:unset;">
                        <div class="d-flex justify-content-start align-items-center gap-3">
                            <div>
                                @php
                                    $gender = optional($user->employee)->gender;
                                @endphp

                                @if($gender === GENDER_MALE)
                                    <img src="{{ asset('assets/assestsnew/menimg2.svg') }}" alt="Male">
                                @elseif($gender === GENDER_FEMALE)
                                    <img src="{{ asset('assets/assestsnew/female-emp.svg') }}" alt="Female">
                                @else
                                    <img src="{{ asset('assets/assestsnew/menimg2.svg') }}" alt="Default">
                                @endif
                            </div>
                            <div>
                                <h4 class="text-black fw-semibold fs-4">
                                    {{ $user->name ?? 'N/A' }}
                                </h4>
                            </div>
                        </div>
                        <a href="{{ route('employee.self-report') }}">

                            <div class="mt-3 actives1 py-3 px-2" data-section="attendance">
                                <h6 class="text-black text-start fw-normal fs-5 mb-0">Attendance</h6>
                            </div>
                        </a>

                        <a href="{{ route('employee.breakInsight') }}">

                            <div class="mt-3 py-3 px-2  actives1">
                                <h6 class="text-black fw-normal fs-5 mb-0">Break</h6>
                            </div>
                        </a>

                        <a href="{{ route('employee.activity') }}">

                            <div class="mt-3 py-3 px-2 actives1 active-tab" data-section="activity">
                                <h6 class="text-black fw-normal fs-5 mb-0">Activity</h6>
                            </div>
                        </a>

                    </div>
                </div>

                <div class="col-xl-9" id="activityContent">
                    <div class="row g-3">

                        <div class="col-xl-3 col-lg-6 col-md-6">
                            <div class="box_2">
                                <p class="fs-6 fw-normal col_P mb-0">Top Application</p>
                                <h3 class="text-dark fw-semibold fs-3 mt-2 text-break">
                                    {{ $topApplication->application_name ?? 'N/A' }}
                                </h3>
                                <div class="inside_round mt-3">
                                    <p class="mb-0">{{ gmdate('H\h:i\m', $topApplication->total_seconds ?? 0) }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-3 col-lg-6 col-md-6">
                            <div class="box_3">
                                <p class="fs-6 fw-normal col_P mb-0">Top Category</p>
                                <h3 class="text-dark fw-semibold fs-3 mt-2 text-break">{{ $topCategory->category ?? 'N/A' }}</h3>
                                <div class="inside_round1 mt-3">
                                    <p class="mb-0">{{ gmdate('H\h:i\m', $topCategory->total_seconds ?? 0) }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-6 col-md-12">
                            <div class="box_4">
                                <p class="fs-6 fw-normal col_P mb-0">Top URL</p>
                                <h3 class="text-dark fw-semibold fs-3 mt-2 text-break">
                                    {{ $topUrl ? parse_url($topUrl->url, PHP_URL_HOST) ?? $topUrl->url : 'N/A' }}
                                </h3>
                                <div class="inside_round2 mt-3">
                                    <p class="mb-0">{{ gmdate('H\h:i\m', $topUrl->total_seconds ?? 0) }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-12">
                            <div class="chart_box1">
                                <div class="d-flex justify-content-between">
                                    <h2 class="text-dark fw-semibold fs-4">Attendance</h2>
                                    <img src="./assest/grapicon.svg" alt="">
                                </div>


                                <div class="row align-items-center">
                                    <div class="col-md-4 mb-3">
                                        <div style="position: relative; width: 300px; height: 300px; margin: auto;">
                                            <div id="donut_single2" style="width: 100%; height: 100%;"></div>
                                            <div id="chart-center-text2"
                                                 style="
                                                      position: absolute;
                                                      top: 50%;
                                                      left: 50%;
                                                      transform: translate(-50%, -50%);
                                                      pointer-events: none;
                                                      font-family: Arial, sans-serif;
                                                      color: #333;
                                                    ">
                                            </div>
                                        </div>
                                        <div id="attendance-summary2"></div>
                                    </div>
                                    <div class="col-md-8">
                                        <div class="py-4">
                                            <div class="d-flex flex-wrap gap-3 justify-content-evenly">
                                                @php
                                                    use Carbon\Carbon;
                                                @endphp

                                                <div class="info-card">
                                                    <div class="small-text">
                                                        <span class="green-dot"></span> Total online time
                                                    </div>
                                                    <div class="big-text">
                                                        {{ $totalOnlineFormatted }}

                                                    </div>
                                                    <!--<div class="bottom-text">-->
                                                    <!--    For the last 7 days-->
                                                    <!--</div>-->
                                                </div>

                                                <div class="info-card">
                                                    <div class="small-text">
                                                        <span class="green-dot"></span> Average online time
                                                    </div>
                                                    <div class="big-text">
                                                        {{ $averageOnlineFormatted }}
                                                    </div>
                                                    <!--<div class="bottom-text">-->
                                                    <!--    Average per day-->
                                                    <!--</div>-->
                                                </div>

                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-12">
                            <div class="row" style="padding: 0px 13px">
                                <div class="col-md-12 bg-white rounded-3">

                                    <div class="attendance-table-outer">
                                        <table class="attendance-table mt-3">
                                            <thead>
                                            <tr>
                                                <th>Date</th>

                                                <th>Online Time</th>
                                                <th>Active Time</th>
                                                <!--<th>Idle Warning</th>-->
                                                <!--<th>Idle Time</th>-->
                                                <th>Break Time</th>
                                                <!--<th>Activity %</th>-->

                                            </tr>
                                            </thead>
                                            <tbody>

                                            <tbody>
                                            @forelse($incidents as $incident)
                                                @php

                                                    // Attendance with breaks
                                                    $attendances = \App\Models\AttendanceEmployee::with([
                                                        'employee.user',
                                                        'breakTimes',
                                                    ])
                                                        ->where('employee_id', $employee->id)
                                                        ->whereDate('date', $incident->activity_date)
                                                        ->get();

                                                    $onlineTimeFormatted = '-';
                                                    $breakTimeFormatted = '-';
                                                    $activeTimeFormatted = '-';
                                                    $totalonlineSeconds = 0;
                                                    $totalBreakSeconds = 0;
                                                    $totalactiveSeconds = 0;

                                                    foreach ($attendances as $attendance) {
                                                        $breaks = $attendance?->breakTimes ?? collect();

                                                        $clockIn =
                                                            $attendance?->date . ' ' . $attendance?->clock_in;
                                                        $clockOut =
                                                            $attendance?->clock_out_date .
                                                            ' ' .
                                                            $attendance?->clock_out;

                                                        if ($clockIn) {
                                                            $in = Carbon::parse($clockIn);
                                                            if (
                                                                !empty($attendance?->clock_out) &&
                                                                $attendance?->clock_out != '00:00:00'
                                                            ) {
                                                                $out = $clockOut
                                                                    ? Carbon::parse($clockOut)
                                                                    : Carbon::now();
                                                                $onlineSeconds = $in->diffInSeconds($out);
                                                            } else {
                                                                //    $onlineSeconds = 0;

                                                                if ($in->isToday()) {
                                                                    $onlineSeconds = $in->diffInSeconds(
                                                                        Carbon::now(),
                                                                    );
                                                                } else {
                                                                    $onlineSeconds = 0;
                                                                }
                                                            }

                                                            $totalonlineSeconds += $onlineSeconds;

                                                            // Sum durations safely
                                                            $totalBreakSecond = $breaks->sum(function ($break) {
                                                                if (is_numeric($break->duration)) {
                                                                    return $break->duration;
                                                                }
                                                                $start = Carbon::parse($break->break_started_at);
                                                                $end = $break->break_ended_at
                                                                    ? Carbon::parse($break->break_ended_at)
                                                                    : Carbon::now();
                                                                return $start->diffInSeconds($end);
                                                            });

                                                            $totalBreakSeconds += $totalBreakSecond;

                                                            $activeSeconds = max(
                                                                0,
                                                                $onlineSeconds - $totalBreakSeconds,
                                                            );

                                                            $totalactiveSeconds += $activeSeconds;
                                                        }
                                                        $breakTimeFormatted = gmdate('H:i:s', $totalBreakSeconds);

                                                        $onlineTimeFormatted = gmdate('H:i:s', $totalonlineSeconds);

                                                        $activeTimeFormatted = gmdate('H:i:s', $totalactiveSeconds);
                                                    }

                                                @endphp
                                                <tr>
                                                    <td class="tex_fix">{{ $incident->activity_date }}</td>

                                                    <td>{{ $onlineTimeFormatted }}</td>
                                                    <!--<td>  {{ $onlineTimeFormatted }} - {{ $breakTimeFormatted }} = {{ $activeTimeFormatted }}</td>-->
                                                    <td>

                                                        {{ $activeTimeFormatted }}
                                                    </td>

                                                    <td>{{ $breakTimeFormatted }}</td>

                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="7">
                                                        <div class="row justify-content-center text-center">
                                                            <img class="w-25"
                                                                 src="{{ asset('assets/assestsnew/no_datasvg.svg') }}"
                                                                 alt="">
                                                        </div>
                                                    </td>
                                                </tr>
                                            @endforelse
                                            </tbody>
                                        </table>

                                    </div>

                                    <!-- Pagination Links -->
                                    @if ($incidents->count() > 0)
                                        <div class="row mt-3 justify-content-center">
                                            <div class="col-12 optional_inputpagi">
                                                <div class="row align-items-start justify-content-center">

                                                    <!-- Left Spacer -->
                                                    <div class="col-12 col-md-3 mb-3 mb-md-0">
                                                        <div class="data_table_select">
                                                            <form id="perPageForm" method="GET"
                                                                  action="{{ url()->current() }}"
                                                                  class="d-flex align-items-center gap-2 m-0">
                                                                {{-- Preserve filters --}}
                                                                @foreach(request()->except(['page', 'per_page']) as $key => $value)
                                                                    <input type="hidden" name="{{ $key }}"
                                                                           value="{{ $value }}">
                                                                @endforeach

                                                                <label for="per_page" class="small mb-0 text-nowrap">Items
                                                                    per page:</label>

                                                                <select name="per_page" id="per_page"
                                                                        class="form-select form-select-sm"
                                                                        style="width: 90px; min-width: 80px;"
                                                                        onchange="document.getElementById('perPageForm').submit()">
                                                                    @foreach([5, 10, 20, 50] as $size)
                                                                        <option
                                                                            value="{{ $size }}" {{ request('per_page', 10) == $size ? 'selected' : '' }}>
                                                                            {{ $size }}
                                                                        </option>
                                                                    @endforeach
                                                                </select>
                                                            </form>
                                                        </div>
                                                    </div>

                                                    <!-- Pagination Numbers -->
                                                    <div class="col-12 col-md-auto mb-3 mb-md-0">
                                                        <div class="d-flex justify-content-center">
                                                            <ul class="paginatio_ulist d-flex align-items-center gap-lg-4 gap-2 m-0 p-0">
                                                                {{-- First Page --}}
                                                                <li class="{{ $incidents->onFirstPage() ? 'disabled' : '' }}">
                                                                    <a href="{{ $incidents->url(1) }}"
                                                                       class="page-link1">&#171;</a>
                                                                </li>

                                                                {{-- Previous Page --}}
                                                                <li class="{{ $incidents->onFirstPage() ? 'disabled' : '' }}">
                                                                    <a href="{{ $incidents->previousPageUrl() }}"
                                                                       class="page-link1">
                                                                        <i class="fa-solid fa-chevron-left"></i>
                                                                    </a>
                                                                </li>

                                                                {{-- Page Numbers --}}
                                                                @php
                                                                    $start = max(1, $incidents->currentPage() - 2);
                                                                    $end = min($start + 4, $incidents->lastPage());
                                                                @endphp
                                                                @for ($i = $start; $i <= $end; $i++)
                                                                    <li class="{{ $incidents->currentPage() == $i ? 'active_pagination' : '' }}">
                                                                        <a href="{{ $incidents->url($i) }}"
                                                                           class="page-link1">{{ $i }}</a>
                                                                    </li>
                                                                @endfor

                                                                {{-- Next Page --}}
                                                                <li class="{{ !$incidents->hasMorePages() ? 'disabled' : '' }}">
                                                                    <a href="{{ $incidents->nextPageUrl() }}"
                                                                       class="page-link1">
                                                                        <i class="fa-solid fa-chevron-right"></i>
                                                                    </a>
                                                                </li>

                                                                {{-- Last Page --}}
                                                                <li class="{{ !$incidents->hasMorePages() ? 'disabled' : '' }}">
                                                                    <a href="{{ $incidents->url($incidents->lastPage()) }}"
                                                                       class="page-link1">&#187;</a>
                                                                </li>
                                                            </ul>
                                                        </div>
                                                    </div>

                                                    <!-- Page Jump Input -->
                                                    <div class="col-12 col-lg-3 col-md-4 mb-3 mb-md-0">
                                                        <div
                                                            class="d-flex flex-md-row align-items-center justify-content-center justify-content-md-start gap-2">
                                                            <form action="{{ url()->current() }}" method="GET"
                                                                  class="d-flex align-items-center gap-2"
                                                                  style="flex-direction:row !important;">
                                                                {{-- Preserve filters --}}
                                                                @foreach(request()->except('page') as $key => $value)
                                                                    <input type="hidden" name="{{ $key }}"
                                                                           value="{{ $value }}">
                                                                @endforeach

                                                                <input type="number" name="page" min="1"
                                                                       max="{{ $incidents->lastPage() }}"
                                                                       class="form-control form-control-sm"
                                                                       style="width: 80px;" placeholder="Page">
                                                                <button class="btn btn-sm btn-primary" type="submit">
                                                                    Go
                                                                </button>
                                                            </form>
                                                            <span class="text-nowrap small text-center text-md-start">of {{ $incidents->total() }} Records</span>
                                                        </div>
                                                    </div>

                                                    <!-- Showing Range -->
                                                    <div class="col-12 mt-3">
                                                        <div class="d-flex justify-content-center">
                                                            <span>{{ $incidents->firstItem() }} to {{ $incidents->lastItem() }} of {{ $incidents->total() }} Records</span>
                                                        </div>
                                                    </div>

                                                </div>
                                            </div>
                                        </div>
                                    @endif

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <div class="modal fade" id="editTeamModal" tabindex="-1" aria-labelledby="editTeamModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-end" style="margin-right: 0; margin-top: 0;">
            <div class="modal-content rounded-5 shadow p-4" style="height: 100vh; width: 500px; overflow-y: auto;">
                <div class="">
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" style="overflow-y: auto;">
                    <div class="container mt-4">
                        <div class="profile-box">
                            <div class="profile-circle">UN</div>
                            <div>
                                <h6 class="mb-0 fw-semibold">User Name</h6>
                                <small class="text-muted">usernamemsitpark@gmail.com</small><br>
                                <small class="text-muted">03-04-2025</small>
                            </div>
                        </div>

                        <div class="container">
                            <div class="schedule-container">
                                <table class="table table-borderless mb-0 time-grid">
                                    <thead>
                                    <tr>
                                        <th>In</th>
                                        <th>Out</th>
                                        <th>Duration</th>
                                    </tr>
                                    </thead>
                                    <tbody class="tex_fix">
                                    <tr>
                                        <td>10:00 AM</td>
                                        <td>11:00 AM</td>
                                        <td>01h:00m:20s</td>
                                    </tr>
                                    <tr>
                                        <td>11:00 AM</td>
                                        <td>07:00 PM</td>
                                        <td>06h:09m:20s</td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
