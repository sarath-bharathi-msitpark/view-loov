@extends('layouts.admin')
@section('page-title')
    {{ __('Dashboard') }}
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
        (function () {
            var chartBarOptions = {
                series: [{
                    name: '{{ __('Income') }}',
                    data: {!! json_encode($chartData['data']) !!},

                },],

                chart: {
                    height: 300,
                    type: 'area',
                    // type: 'line',
                    dropShadow: {
                        enabled: true,
                        color: '#000',
                        top: 18,
                        left: 7,
                        blur: 10,
                        opacity: 0.2
                    },
                    toolbar: {
                        show: false
                    }
                },
                dataLabels: {
                    enabled: false
                },
                stroke: {
                    width: 2,
                    curve: 'smooth'
                },
                title: {
                    text: '',
                    align: 'left'
                },
                xaxis: {
                    categories: {!! json_encode($chartData['label']) !!},
                    title: {
                        text: '{{ __('Months') }}'
                    }
                },
                colors: ['#6fd944', '#6fd944'],

                grid: {
                    strokeDashArray: 4,
                },
                legend: {
                    show: false,
                },
                // markers: {
                //     size: 4,
                //     colors: ['#ffa21d', '#FF3A6E'],
                //     opacity: 0.9,
                //     strokeWidth: 2,
                //     hover: {
                //         size: 7,
                //     }
                // },
                yaxis: {
                    title: {
                        text: '{{ __('Income') }}',
                        offsetX: 30,
                        offsetY: -10,
                    },

                }

            };
            var arChart = new ApexCharts(document.querySelector("#chart-sales"), chartBarOptions);
            arChart.render();
        })();
    </script>
@endpush
@php
    $admin_payment_setting = Utility::getAdminPaymentSetting();
@endphp

@section('content')
    <div class="col-12">
        <div class="row align-items-center justify-content-center">
            <div class="col-md-6">
                <div class="d-flex justify-content-start align-items-center gap-2">
                    <img src="{{ asset('assets/assestsnew/dashboard_logo.svg') }}" alt="Header Logo" width="32"
                         height="32"/>
                    <h5 class="fw-semibold fs-2 let_1 mb-0">Dashboard</h5>
                </div>
            </div>

            <div class="col-md-6">
                <div class="d-flex justify-content-end align-items-center flex-wrap gap-3 mt-3 mt-md-0">
                    <div class="help">
                        <div class="dropdown text-center position-relative">
                            <img src="{{ asset('assets/assestsnew/help-circle.svg') }}" alt="">
                            <a class="dropdown-toggle fw-semibold text-decoration-none text-primary" href="#"
                               role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                Help
                            </a>
                            <ul
                                class="dropdown-menu dropdown-menu-center bg-transparent border-0 p-0 rounded-0">
                                <div class="container-center">
                                    <div class="account-card">
                                        <a href="#" class="menu-item self_hov1 px-2" style="color: #6B6B6B;">
                                            <div class="menu-icon">
                                                <img src="{{ asset('assets/assestsnew/docs.svg') }}" alt="">
                                            </div>
                                            Documentation
                                        </a>

                                        <a href="#" class="menu-item self_hov1 px-2" style="color: #6B6B6B;">
                                            <div class="menu-icon">
                                                <img src="{{ asset('assets/assestsnew/take-care.svg') }}" alt="">
                                            </div>
                                            Onboarding Support
                                        </a>

                                        <a href="#" class="menu-item self_hov1 px-2" style="color: #6B6B6B;">
                                            <div class="menu-icon">
                                                <img src="{{ asset('assets/assestsnew/problem.svg') }}" alt="">
                                            </div>
                                            Report Issue
                                        </a>
                                    </div>
                                </div>
                            </ul>
                        </div>
                    </div>


                    <button class="fw-normal but_1 text-white d-flex align-items-center gap-2 text-nowrap px-4 py-2">
                        <img src="{{ asset('assets/assestsnew/download-logo.svg') }}" alt="Download Icon"/>
                        Download LOOV
                    </button>

                    <div class="d-flex align-items-center gap-2">
                        <img src="{{ asset('assets/assestsnew/menimg.png') }}" alt="User Image"
                             class="rounded-circle" width="38"
                             height="38"/>

                        <div class="dropdown">
                            <a class="dropdown-toggle fw-semibold text-decoration-none text-dark" type="button"
                               data-bs-toggle="dropdown" aria-expanded="false">
                                {{ Auth::user()->name }}
                            </a>
                            <ul class="dropdown-menu border-0 p-0 bg-transparent dropdown-menu-end">
                                <div class="container-center">
                                    <div class="account-card">
                                        <div class="profile-section">
                                            <div class="profile-pic">
                                                <img src="{{ asset('assets/assestsnew/menimg.png') }}" alt="">
                                            </div>
                                            <div>
                                                <h6 class="mb-0 text-black fw-semibold fs-6">{{ Auth::user()->name }}
                                                </h6>
                                                <small style="color: #A2A2A2;">{{ Auth::user()->email }}</small>
                                            </div>
                                        </div>

                                        <a href="#" class="menu-item self_hov1 px-2" style="color: #6B6B6B;">
                                            <div class="menu-icon">
                                                <img src="{{ asset('assets/assestsnew/bill.svg') }}" alt="">
                                            </div>
                                            Billing
                                        </a>

                                        <a href="#" class="menu-item self_hov1 px-2" style="color: #6B6B6B;">
                                            <div class="menu-icon">
                                                <img src="{{ asset('assets/assestsnew/download.svg') }}" alt="">
                                            </div>
                                            Download App
                                        </a>

                                        <form id="logout-form" action="{{ route('logout') }}" method="POST"
                                              style="display: none;">
                                            @csrf
                                        </form>

                                        <a href="#" class="menu-item self_hov1 px-2" style="color: #6B6B6B;"
                                           onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                            <div class="menu-icon">
                                                <img src="{{ asset('assets/assestsnew/log-out.svg') }}"
                                                     alt="Logout">
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
        <div class="col-12">
            <div class="row">
                <div class="col-lg-6 selecters_head">
                    <div class="row gap-md-5 px-3">
                        <select class="form-select">
                            <option value="">All Team</option>
                            <option value="">Team 1</option>
                            <option value="">Team 2</option>
                            <option value="">Team 3</option>
                        </select>
                    </div>
                </div>
                <div class="col-lg-6 selecters_head">
                    <div class="row justify-content-lg-end gx-3">
                        <div class="col-auto">
                            <input type="date" class="form-control">
                        </div>
                        <div class="col-auto">
                            <button class="download_arrbtn"><i class="fas fa-download"></i></button>
                        </div>
                        <div class="col-auto">
                                    <span>
                                        <button class="download_arrbtn"><i class="fas fa-redo-alt"></i></button>
                                        Clear Filter
                                    </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 entire_box">
            <div class="row">
                <div class="col-md-12 col-lg-12 col-xl-4">
                    <div class="chart_box1 h-100">
                        <div class="d-flex justify-content-between">
                            <h2 class="text-dark fw-semibold fs-4">Today’s Attendance</h2>
                            <img src="{{ asset('assets/assestsnew/grapicon.svg') }}" alt="">
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
                            <img src=".{{ asset('assets/assestsnew/grapicon.svg') }}" alt="">
                        </div>
                        <div id="chart_div" style="width: 100%; max-width: 800px; margin: auto;"></div>
                    </div>
                </div>

                <div class="col-lg-12 col-md-12 mt-4">
                    <div class="chart_box1">
                        <div class="d-flex justify-content-between">
                            <h2 class="text-dark fw-semibold fs-4">URL Usage</h2>
                            <img src="{{ asset('assets/assestsnew/grapicon.svg') }}" alt="">
                        </div>
                        <div class="container py-4">
                            <div class="row g-4">
                                <!-- Proactive Teams -->
                                <div class="col-md-6">
                                    <div class="border rounded-3 shadow-sm p-3 bg-white">
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
                                    <div class="border rounded-3 shadow-sm p-3 bg-white">
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
            title: 'Attendance Trends',
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
