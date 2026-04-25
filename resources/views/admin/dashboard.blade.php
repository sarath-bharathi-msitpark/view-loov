@extends('admin.layouts.admin')

@section('page-title')
    {{ __('Dashboard') }}
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

@endpush
@php
    $admin_payment_setting = Utility::getAdminPaymentSetting();
@endphp

@section('content')
    @include('admin.layouts.partials.nav')

<div class="col-12">
        <div class="row">
            <form method="GET" action="{{ route('organization.dashboard') }}"
                  class="d-flex w-100 justify-content-between">
                <div class="selecters_head">
                    <div class="row gap-md-5 px-3 mb-3">
                        <select class="form-select select2" name="team_id" id="team-id">
                            <option value="">All Team</option>
                        <option value="">Team 1</option>
                        <option value="">Team 2</option>
                        <option value="">Team 3</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-6 selecters_head">
                    <div class="row justify-content-lg-end gap-3">
                        <input type="text" name="date_range" id="date-range" class="form-control"
                               value="{{ request('date_range') ?? \Carbon\Carbon::today()->format('Y-m-d') }}">
                        <span><a href="{{ route('organization.dashboard') }}" class="download_arrbtn"><i
                                    class="fas fa-redo-alt" style="margin:12px" aria-hidden="true"></i></a>Clear Filter</span>
                    </div>
                </div>
            </form>
        </div>
    </div>



   

    <div class="col-12 mt-4 entire_box">
        <div class="row">
            <div class="col-md-12 col-lg-12 col-xl-4">
                <div class="chart_box1 h-100">
                    <div class="d-flex justify-content-between">
                        <h2 class="text-dark fw-semibold fs-4">Today’s Attendance</h2>
                    </div>
                    <div class="row mt-4">
                        <div class="col-md-6">
                            <div class="blue_box1">
                                <div class="text_info">
                                    <h2 class="fs-4 fw-bold mb-0">7</h2>
                                    <p class="mb-1 fw-normal col_P">On Time Arrivals</p>
                                </div>
                                <div class="white_box mb-0">
                                    <p class="col_p1 mb-0">100%</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="blue_box1">
                                <div class="text_info">
                                    <h2 class="fs-4 fw-bold mb-0">0</h2>
                                    <p class="mb-1 fw-normal col_P">Late Arrivals</p>
                                </div>
                                <div class="white_box mb-0">
                                    <p class="col_p1 mb-0">0%</p>
                                </div>
                            </div>
                        </div>
                        <div style="position: relative; width: 300px; height: 300px; margin: auto;">
                            <div id="donut_single" style="width: 100%; height: 100%;"></div>
                            <div id="chart-center-text" style="
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
                        <h2 class="text-dark fw-semibold fs-4">Application Usage</h2>
                    </div>
                    <div class="col-12" style="overflow-x: auto; overflow-y: hidden;">
                        
                    <div id="chart_div" style="width: 100%; min-width: 500px; margin: auto;"></div>
                    </div>
                </div>
            </div>

            <div class="col-lg-12 col-md-12 mt-4">
                <div class="chart_box1">
                    <div class="d-flex justify-content-between">
                    </div>
                    <div class="container py-4">
                        <div class="row g-4">
                            <!-- Proactive Teams -->
                            <div class="col-md-6">
                                <div class="border rounded-3 shadow-sm p-1 bg-white">
                                    <h6 class="fw-bold text-success mb-3">
                                        <i class="bi bi-people-fill me-1"></i> <span
                                            class="text-black fw-medium fs-5">Proactive Teams</span>
                                    </h6>
                                    <div class="row align-items-center">
                                        <div class="col-6">
                                            <div class="d-flex align-items-center mb-3">
                                                <div class="rank-circle circ1 text-black fw-medium me-2">#1
                                                </div>
                                                <div class="d-flex justify-content-between">
                                                    <small class="fw-semibold">Development</small>

                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <div class="d-flex align-items-center">
                                                <div class="flex-grow-1 me-2">
                                                    <div class="progress" style="height: 8px;">
                                                        <div class="progress-bar bg-primary"
                                                             style="width: 98.74%"></div>
                                                    </div>
                                                </div>
                                                <small class="fw-semibold">98.74%</small>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row align-items-center">
                                        <div class="col-6">
                                            <div class="d-flex align-items-center mb-3">
                                                <div class="rank-circle circ1 text-black fw-medium me-2">#2
                                                </div>
                                                <div class="d-flex justify-content-between">
                                                    <small class="fw-semibold">QA Testing</small>

                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <div class="d-flex align-items-center">
                                                <div class="flex-grow-1 me-2">
                                                    <div class="progress" style="height: 8px;">
                                                        <div class="progress-bar bg-primary"
                                                             style="width: 80.74%"></div>
                                                    </div>
                                                </div>
                                                <small class="fw-semibold">80.74%</small>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row align-items-center">
                                        <div class="col-6">
                                            <div class="d-flex align-items-center mb-3">
                                                <div class="rank-circle circ1 text-black fw-medium me-2">#3
                                                </div>
                                                <div class="d-flex justify-content-between">
                                                    <small class="fw-semibold">Designing</small>

                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <div class="d-flex align-items-center">
                                                <div class="flex-grow-1 me-2">
                                                    <div class="progress" style="height: 8px;">
                                                        <div class="progress-bar bg-primary"
                                                             style="width: 60.74%"></div>
                                                    </div>
                                                </div>
                                                <small class="fw-semibold">60.74%</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>


                            <div class="col-md-6">
                                <div class="border rounded-3 shadow-sm p-1 bg-white">
                                    <h6 class="fw-bold text-danger mb-3">
                                        <i class="bi bi-people-fill me-1"></i> <span
                                            class="text-black fw-medium fs-5">Lethargic Teams</span>
                                    </h6>

                                    <div class="row align-items-center">
                                        <div class="col-6">
                                            <div class="d-flex align-items-center mb-3">
                                                <div class="rank-circle circ text-black fw-medium me-2">#1
                                                </div>
                                                <div class="d-flex justify-content-between">
                                                    <small class="fw-semibold">Development</small>

                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <div class="d-flex align-items-center">
                                                <div class="flex-grow-1 me-2">
                                                    <div class="progress" style="height: 8px;">
                                                        <div class="progress-bar bg-primary"
                                                             style="width: 20.74%"></div>
                                                    </div>
                                                </div>
                                                <small class="fw-semibold">20.74%</small>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row align-items-center">
                                        <div class="col-6">
                                            <div class="d-flex align-items-center mb-3">
                                                <div class="rank-circle circ text-black fw-medium me-2">#2
                                                </div>
                                                <div class="d-flex justify-content-between">
                                                    <small class="fw-semibold">QA Testing</small>

                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <div class="d-flex align-items-center">
                                                <div class="flex-grow-1 me-2">
                                                    <div class="progress" style="height: 8px;">
                                                        <div class="progress-bar bg-primary"
                                                             style="width: 20.74%"></div>
                                                    </div>
                                                </div>
                                                <small class="fw-semibold">20.74%</small>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row align-items-center">
                                        <div class="col-6">
                                            <div class="d-flex align-items-center mb-3">
                                                <div class="rank-circle circ text-black fw-medium me-2">#3
                                                </div>
                                                <div class="d-flex justify-content-between">
                                                    <small class="fw-semibold">Designing</small>

                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <div class="d-flex align-items-center">
                                                <div class="flex-grow-1 me-2">
                                                    <div class="progress" style="height: 8px;">
                                                        <div class="progress-bar bg-primary"
                                                             style="width: 20.74%"></div>
                                                    </div>
                                                </div>
                                                <small class="fw-semibold">20.74%</small>
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

<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>

<!-- pie chart -->

<script type="text/javascript">
    google.charts.load('current', {'packages': ['corechart']});
    google.charts.setOnLoadCallback(drawChart);

    function drawChart() {
        // Example data
        var present = 25;
        var absent = 5;
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
                0: {color: '#316FF6'},  // Blue for Present
                1: {color: '#E8E8E8'}   // Gray for Absent
            }
        };

        var chart = new google.visualization.PieChart(document.getElementById('donut_single'));
        chart.draw(data, options);

        // Center Text (Total)
        document.getElementById('chart-center-text').innerHTML = `
            <div style="text-align: center;">
              <h6 style="margin: 0; font-size: 20px;">${total}</h6>
              <span style="font-size: 14px;">Total</span>
            </div>
          `;

        // Below Text (Present & Absent)
        document.getElementById('attendance-summary').innerHTML = `
             <div style="display: flex; justify-content: center; gap: 20px; margin-top: 10px; font-size: 14px;">

    <!-- Present -->
    <div style="display: flex; align-items: center; gap: 6px;">
      <span style="width: 20px; height: 20px; background-color: #316FF6; border-radius: 50%; display: inline-block;"></span>
      <span style="color: #316FF6; font-weight: 500;">
        ${present} Present
      </span>
    </div>

    <!-- Absent -->
    <div style="display: flex; align-items: center; gap: 6px;">
      <span style="width: 20px; height: 20px; background-color: #999; border-radius: 50%; display: inline-block;"></span>
      <span style="color: #999; font-weight: 500;">
        ${absent} Absent
      </span>
    </div>

  </div>
          `;
    }
</script>

<!-- Bar chart -->
<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
<script type="text/javascript">
    google.charts.load('current', {packages: ['corechart']});
    google.charts.setOnLoadCallback(drawChart);

    function drawChart() {
        var data = google.visualization.arrayToDataTable([
            ['Date', 'Present', 'Absent', 'Attendance %', 'Avg Working Hours'],
            ['05/04', 7, 2, 77, 6],
            ['06/04', 9, 1, 90, 7],
            ['07/04', 8, 2, 80, 7],
            ['08/04', 8, 2, 80, 7],
            ['09/04', 8, 2, 80, 7],
            ['10/04', 8, 2, 80, 7]
        ]);

        var options = {
            curveType: 'function',
            legend: {position: 'bottom'},
            seriesType: 'bars',
            series: {
                0: {color: '#316FF6'},
                1: {color: '#D3D3D3'},
                2: {
                    type: 'line',
                    color: '#FFA500',
                    targetAxisIndex: 1
                },
                3: {
                    type: 'line',
                    color: '#66BB6A',
                    targetAxisIndex: 1
                }
            },

            height: 400
        };

        var chart = new google.visualization.ComboChart(document.getElementById('chart_div'));
        chart.draw(data, options);
    }
</script>
