@extends('field_track.layouts.fieldTrack')

@section('page-title')
    {{ __('Field Track Dashboard') }}
@endsection

@section('page-icon')
    {{ asset('assets/assestsnew/dashboard_logo.svg') }}
@endsection

@push('css-page')
    <style>
        .apexcharts-yaxis {
            transform: translate(20px, 0px) !important;
        }
    </style>
@endpush

@push('theme-script')
    <script src="{{ asset('assets/libs/apexcharts/dist/apexcharts.min.js') }}"></script>
@endpush

@push('script-page')
    <script>
        $(function () {
            const $dateInput = $('input[name="date_range"]');
            const $form = $dateInput.closest('form');

            // Get server-side date range from the Blade template (e.g., '2025-06-01 to 2025-06-07')
            const serverRange = "{{ request('date_range') }}";
            let start = moment().startOf('day');
            let end = moment().startOf('day');

            if (serverRange && serverRange.includes('to')) {
                const parts = serverRange.split('to');
                start = moment(parts[0].trim(), 'YYYY-MM-DD');
                end = moment(parts[1].trim(), 'YYYY-MM-DD');
            } else if (serverRange) {
                start = moment(serverRange.trim(), 'YYYY-MM-DD');
                end = start;
            }

            $dateInput.daterangepicker({
                autoUpdateInput: true,
                startDate: start,
                endDate: end,
                locale: {
                    format: 'YYYY-MM-DD',
                    cancelLabel: 'Clear'
                },
                ranges: {
                    'Today': [moment(), moment()],
                    'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                    'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                    'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                    'This Month': [moment().startOf('month'), moment().endOf('month')],
                    'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
                }
            });

            $dateInput.on('apply.daterangepicker', function (ev, picker) {
                const val = picker.startDate.format('YYYY-MM-DD') === picker.endDate.format('YYYY-MM-DD')
                    ? picker.startDate.format('YYYY-MM-DD')
                    : picker.startDate.format('YYYY-MM-DD') + ' to ' + picker.endDate.format('YYYY-MM-DD');
                $(this).val(val);
                $form.submit();
            });

            $dateInput.on('cancel.daterangepicker', function () {
                $(this).val('');
                $form.submit();
            });

            $('#team-id').on('change', function () {
                $form.submit();
            });
        });
    </script>
@endpush


@section('content')
    @include('field_track.layouts.partials.nav')

    <div class="col-12 mt-md-0 mt-4">
        <div class="row">

            {{--            <div class="col-lg-4 col-md-6">--}}
            {{--                <div class="d-flex h-100">--}}
            {{--                    <div class="card w-100">--}}
            {{--                        <div class="card-body p-3 main_dashupgrade upgrade1grid">--}}
            {{--                            <div class="theme-avtar badge ms-3" style="background:#B12147;">--}}
            {{--                                <i class="fas fa-users"></i>--}}
            {{--                            </div>--}}
            {{--                            <div class="d-flex align-items-center mb-3 mt-3">--}}
            {{--                                <div class="ms-3 mb-2 mt-3">--}}
            {{--                                    <h6 class="ml-4">Today Visits</h6>--}}
            {{--                                </div>--}}
            {{--                                <div class="number-icon ms-3 mb-2 mt-3">--}}
            {{--                                    <h3>0</h3>--}}
            {{--                                </div>--}}
            {{--                            </div>--}}
            {{--                            <div class="ms-3 mb-3 mt-3">--}}
            {{--                                <h6>Onboarding Shops : <span class="text-dark">0</span>--}}
            {{--                                </h6>--}}
            {{--                            </div>--}}
            {{--                        </div>--}}
            {{--                    </div>--}}
            {{--                </div>--}}
            {{--            </div>--}}

            <div class="col-lg-4 col-md-6">
                <div class="d-flex h-100">
                    <div class="card w-100">
                        <div class="card-body p-3 main_dashupgrade upgrade2grid">
                            <div class="theme-avtar bg-secondary badge ms-3">
                                <i class="fas fa-users"></i>
                            </div>
                            <div class="d-flex align-items-center mb-3 mt-3">
                                <div class="ms-3 mb-2 mt-3">
                                    <h6 class="ml-4">Today New Customer's</h6>
                                </div>
                                <div class="number-icon ms-3 mb-2 mt-3">
                                    <h3>{{ $todayCustomer }}</h3>
                                </div>
                            </div>
                            <div class="ms-3 mb-3 mt-3">
                                <h6>Customers : <span class="text-dark">{{ $totalCustomer }}</span>
                                </h6>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4 col-md-6">
                <div class="d-flex h-100">
                    <div class="card w-100">
                        <div class="card-body p-3 main_dashupgrade upgrade3grid">
                            <div class="theme-avtar bg-success badge ms-3">
                                <i class="fas fa-users"></i>
                            </div>
                            <div class="d-flex align-items-center mb-3 mt-3">
                                <div class="ms-3 mb-2 mt-3">
                                    <h6 class="ml-4">Today Visits</h6>
                                </div>
                                <div class="number-icon ms-3 mb-2 mt-3">
                                    <h3>{{ $todayVisits }}</h3>
                                </div>
                            </div>
                            <div class="ms-3 mb-3 mt-3">
                                <h6>This Month Visit's : <span class="text-dark">{{ $currentMonthVisits }}</span>
                                </h6>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-12">
        <div class="row">
            <form method="GET" action="{{ route('fieldTrack.dashboard') }}"
                  class="d-flex w-100 justify-content-between">
                <div class="selecters_head">
                    <div class="row gap-md-5 px-3 mb-3">
                        <select class="form-select select2" name="team_id" id="team-id">
                            <option selected disabled>All Team</option>
                            @foreach ($teams as $team)
                                <option value="{{ $team->id }}" {{ request('team_id') == $team->id ? 'selected' : '' }}>
                                    {{ $team->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-6 selecters_head">
                    <div class="row justify-content-lg-end gap-3">
                        <input type="text" name="date_range" id="date-range" class="form-control"
                               value="{{ request('date_range') ?? \Carbon\Carbon::today()->format('Y-m-d') }}">
                        <span><a href="{{ route('fieldTrack.dashboard') }}" class="download_arrbtn"><i
                                    class="fas fa-redo-alt" style="margin:12px" aria-hidden="true"></i></a>Clear Filter</span>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="col-12 mt-4 entire_box mb-5">
        <div class="row">
            <div class="col-md-12 col-lg-12 col-xl-4">
                <div class="chart_box1 h-100">
                    <div class="d-flex justify-content-between">
                        <h2 class="text-dark fw-semibold fs-4">Today Attendance</h2>
                        {{--                        <img src="{{ asset('assets/assestsnew/grapicon.svg') }}" alt="">--}}
                    </div>
                    <div class="row mt-4">
                        <div class="col-md-6">
                            <div class="blue_box1">
                                <div class="text_info">
                                    <h2 class="fs-4 fw-bold mb-0">{{ $onTimeCount }}</h2>
                                    <p class="mb-1 fw-normal col_P">On Time Arrivals</p>
                                </div>
                                <div class="white_box mb-0">
                                    <p class="col_p1 mb-0">{{ $onTimePercent }}%</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="blue_box1">
                                <div class="text_info">
                                    <h2 class="fs-4 fw-bold mb-0">{{ $lateCount }}</h2>
                                    <p class="mb-1 fw-normal col_P">Late Arrivals</p>
                                </div>
                                <div class="white_box mb-0">
                                    <p class="col_p1 mb-0">{{ $latePercent }}%</p>
                                </div>
                            </div>
                        </div>
                        <div style="position: relative; width: 300px; height: 300px; margin: auto;">
                            <div id="donut_single" style="width: 100%; height: 100%;"></div>
                            <div class="dount_chart_align" id="chart-center-text" style="
                                          position: absolute;
                                          top: 50%;
                                          left: 50%;
                                          transform: translate(-50%, -50%);
                                          pointer-events: none;
                                          font-family: Arial, sans-serif;
                                          color: #333;
                                        "></div>
                        </div>
                        <div id="attendance-summary"></div>
                    </div>
                </div>
            </div>
            <div class="col-md-12 col-lg-12 col-xl-8 mt-4 mt-xl-0">
                <div class="chart_box1">
                    <div class="d-flex justify-content-between">
                        <h2 class="text-dark fw-semibold fs-4">Attendance</h2>
                        <img src=".{{ asset('assets/assestsnew/grapicon.svg') }}" alt="">
                    </div>
                    <div class="col-12" style="overflow-x: auto; overflow-y: hidden;">

                        <div id="chart_div" style="width: 100%; min-width: 500px; margin: auto;"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
<!-- pie chart -->
@php
    $isAdmin = auth()->user()->hasRole(ROLE_ADMINISTRATOR);
    $presentOnClick = $isAdmin ? "onclick=\"window.location.href='" . route('fieldTrack.attendanceemployee.index') . "'\"" : '';
    $presentCursor = $isAdmin ? 'pointer' : 'default';
@endphp

<script type="text/javascript">
    google.charts.load('current', {'packages': ['corechart']});
    google.charts.setOnLoadCallback(drawChart);

    function drawChart() {
        var present = {{ $presentCount }};
        var absent = {{ $absentCount }};
        var total = present + absent;

        var data = google.visualization.arrayToDataTable([
            ['Status', 'Count'],
            ['Present', present],
            ['Absent', absent]
        ]);

        var options = {
            pieHole: 0.7,
            legend: 'none',
            backgroundColor: 'transparent',
            pieSliceText: 'none',
            slices: {
                0: {color: '#316FF6'},  // Blue
                1: {color: '#E8E8E8'}   // Gray
            }
        };

        var chart = new google.visualization.PieChart(document.getElementById('donut_single'));
        chart.draw(data, options);

        document.getElementById('chart-center-text').innerHTML = `
            <div style="text-align: center;">
              <h6 style="margin: 0; font-size: 20px;">${total}</h6>
              <span style="font-size: 14px;">Total</span>
            </div>
        `;

        document.getElementById('attendance-summary').innerHTML = `
            <div class="chart-label" style="display: flex; justify-content: center; gap: 20px; margin-top: 10px; font-size: 14px;">
                <div style="display: flex; align-items: center; gap: 6px;">
                    <span style="width: 20px; height: 20px; background-color: #316FF6; border-radius: 50%; display: inline-block;"></span>
                    <span
                        style="color: #316FF6; font-weight: 500; cursor: {{ $presentCursor }};"
                        {!! $presentOnClick !!}>
                        ${present} Present ({{ $presentPercent }}%)
                    </span>
                </div>
                <div style="display: flex; align-items: center; gap: 6px;">
                    <span style="width: 20px; height: 20px; background-color: #999; border-radius: 50%; display: inline-block;"></span>
                    <span
                        style="color: #999; font-weight: 500; cursor: {{ $presentCursor }};"
                        {!! $presentOnClick !!}>
                        ${absent} Absent ({{ $absentPercent }}%)
                    </span>
                </div>
            </div>
        `;
    }
</script>

<!-- Bar chart -->

<script type="text/javascript">
    google.charts.load('current', {packages: ['corechart']});
    google.charts.setOnLoadCallback(drawChart);

    function drawChart() {
        var data = google.visualization.arrayToDataTable([
            ['Date', 'Present', 'Absent'],
                @foreach($chartData as $data)
            ['{{ $data['date'] }}', {{ $data['present'] }}, {{ $data['absent'] }}],
            @endforeach
        ]);

        var options = {
            curveType: 'function',
            legend: {position: 'bottom'},
            seriesType: 'bars',
            series: {
                0: {color: '#316FF6'},
                1: {color: '#D3D3D3'}
            },
            height: 400
        };

        var chart = new google.visualization.ComboChart(document.getElementById('chart_div'));
        chart.draw(data, options);
    }
</script>



