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
            <div class="col-lg-12 text-center">
                Welcome {{ \Auth::user()->name }}
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
