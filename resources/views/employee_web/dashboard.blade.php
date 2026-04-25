@extends('employee_web.layouts.employee_web')

@section('page-title')
    {{ __('Dashboard') }}
@endsection

@section('page-icon')
    {{ asset('assets/assestsnew/dashboard_logo.svg') }}
@endsection

@push('css-page')

@endpush

@push('theme-script')

@endpush
@push('script-page')
    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>

    <script type="text/javascript">
        google.charts.load('current', {'packages': ['corechart']});
        google.charts.setOnLoadCallback(drawChart);

        function drawChart() {

            var present = 5;
            var absent = 1;
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
                    0: {color: '#316FF6'},
                    1: {color: '#E8E8E8'}
                }
            };

            var chart = new google.visualization.PieChart(document.getElementById('donut_single'));
            chart.draw(data, options);


            document.getElementById('chart-center-text').innerHTML = `
            <div style="text-align: center;">
              <h2 style="margin: 0; font-size: 32px;">${total}</h2>
              <span style="font-size: 14px;">Total</span>
            </div>
          `;

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


    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>

    <script type="text/javascript">
        google.charts.load('current', {'packages': ['corechart']});
        google.charts.setOnLoadCallback(drawCharts);

        function drawCharts() {

            drawChart('donut_single2', 'chart-center-text2', 'attendance-summary2', 3, 2);
        }

        function drawChart(chartId, centerTextId, summaryId, present, absent) {
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
                    0: {color: '#316FF6'},
                    1: {color: '#E8E8E8'}
                }
            };

            var chart = new google.visualization.PieChart(document.getElementById(chartId));
            chart.draw(data, options);

            document.getElementById(centerTextId).innerHTML = `
            <div style="text-align: center;">
              <h2 style="margin: 0; font-size: 32px;">${total}</h2>
              <span style="font-size: 14px;">Total</span>
            </div>
        `;

            document.getElementById(summaryId).innerHTML = `
            <div style="display: flex; justify-content: center; gap: 20px; margin-top: 10px; font-size: 14px;">
                <div style="display: flex; align-items: center; gap: 6px;">
                  <span style="width: 20px; height: 20px; background-color: #316FF6; border-radius: 50%; display: inline-block;"></span>
                  <span style="color: #316FF6; font-weight: 500;">
                    ${present} Present
                  </span>
                </div>

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
    <script>
        let currentDate = new Date();

        function renderCalendar(date) {
            const monthYear = document.getElementById('monthYear');
            const calendarDates = document.getElementById('calendarDates');
            calendarDates.innerHTML = '';

            const year = date.getFullYear();
            const month = date.getMonth();

            const firstDay = new Date(year, month, 1).getDay();
            const lastDate = new Date(year, month + 1, 0).getDate();

            const today = new Date();
            const isThisMonth = today.getMonth() === month && today.getFullYear() === year;

            monthYear.textContent = date.toLocaleString('default', {month: 'long', year: 'numeric'});


            for (let i = 0; i < firstDay; i++) {
                const emptyCell = document.createElement('div');
                calendarDates.appendChild(emptyCell);
            }

            for (let day = 1; day <= lastDate; day++) {
                const dayCell = document.createElement('div');
                dayCell.textContent = day;

                if (isThisMonth && day === today.getDate()) {
                    dayCell.classList.add('today');
                }

                calendarDates.appendChild(dayCell);
            }
        }

        function prevMonth() {
            currentDate.setMonth(currentDate.getMonth() - 1);
            renderCalendar(currentDate);
        }

        function nextMonth() {
            currentDate.setMonth(currentDate.getMonth() + 1);
            renderCalendar(currentDate);
        }

        renderCalendar(currentDate);
    </script>

    <script>
        function showSection(section, element) {

            const sectionsToHide = ['attendanceContent', 'attendanceContent1', 'attendanceContent2', 'breakContent', 'activityContent'];
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


    <script>tab.addEventListener('click', function () {
            const section = this.dataset.section;
            showSection(section, this);
        });</script>  --}}
@endpush

@section('content')
    @include('employee_web.layouts.partials.nav')

    {{--

 <div class="col-12 py-5">
        <div class="row align-items-center justify-content-center">
            <div class="col-md-6">
                <div class="d-flex justify-content-start align-items-center gap-2">
                    <h5 class="fw-semibold fs-2 text-black mb-0">Hi, User 0125</h5>
                </div>
            </div>
            <div class="col-lg-6 selecters_head">
                <div class="row justify-content-lg-end gx-3">
                    <div class="col-auto d-flex justify-content-lg-end align-items-center gap-2">
                        <input type="date" class="form-control">
                        <input type="date" class="form-control">
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

        <div class="col-12 mt-5">
            <div class="row">
                <div class="col-xl-3 col-lg-6 col-md-12 mt-4 mt-xl-0">
                    <div class="chart_box2">
                        <div class="d-flex justify-content-start align-items-center gap-3">
                            <div>

                                <img src="{{ asset('assets/assestsnew/menimg2.svg') }}" alt="">
                            </div>
                            <div>
                                <h4 class="text-black fw-semibold fs-4">User 012</h4>
                                <p class="text-primary fw-semibold fs-6 mb-0">View Profile</p>
                            </div>
                        </div>

                        <div class="mt-5 actives1 py-3 px-2 active-tab" data-section="attendance">
                            <h6 class="text-black text-start fw-normal fs-5 mb-0">Attendance</h6>
                        </div>
                        <div class="mt-3 py-3 px-2 actives1" data-section="break">
                            <h6 class="text-black fw-normal fs-5 mb-0">Break</h6>
                        </div>
                        <div class="mt-3 py-3 px-2 actives1" data-section="activity">
                            <h6 class="text-black fw-normal fs-5 mb-0">Activity</h6>
                        </div>
                    </div>
                </div>

                <div class="col-xl-4 col-lg-6 col-md-12 mt-4 mt-xl-0" id="attendanceContent">
                    <div class="chart_box1">
                        <div class="d-flex justify-content-between">
                            <h2 class="text-dark fw-semibold fs-4">Attendance</h2>
                            <img src="{{ asset('assets/assestsnew/grapicon.svg') }}" alt="">
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
                <div class="col-xl-5 col-lg-12 col-md-12 mt-4 mt-xl-0" id="attendanceContent1">
                    <div class="chart_box1">
                        <div class="d-flex justify-content-between">
                            <!--<h2 class="text-dark fw-semibold fs-4">Monthly Attendance</h2>-->
                            <img src="{{ asset('assets/assestsnew/grapicon.svg') }}" alt="">
                        </div>
                        <div class="calendar-container mt-2">
                            <div class="calendar">
                                <div class="calendar-header">
                                    <button onclick="prevMonth()">❮</button>
                                    <div id="monthYear"></div>
                                    <button onclick="nextMonth()">❯</button>
                                </div>
                                <div class="calendar-days">
                                    <div>Sun</div>
                                    <div>Mon</div>
                                    <div>Tue</div>
                                    <div>Wed</div>
                                    <div>Thu</div>
                                    <div>Fri</div>
                                    <div>Sat</div>
                                </div>
                                <div class="calendar-dates" id="calendarDates"></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-12 col-md-12 mt-4" id="attendanceContent2">
                    <div class="chart_box1">
                        <div class="d-flex justify-content-between">
                            <h2 class="text-dark fw-semibold fs-4">Details</h2>

                        </div>
                        <div class="row mt-3">
                            <div class="col-md-12 bg-white rounded-3">

                                <div class="attendance-table-outer">
                                    <table class="attendance-table">
                                        <thead>
                                        <tr>
                                            <th>SNo</th>
                                            <th>Date</th>
                                            <th>In</th>
                                            <th>Out</th>
                                            <th>Duration</th>
                                            <th>Log</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <tr class="tex_fix">
                                            <td>1</td>
                                            <td>12-March-2025</td>
                                            <td>10:00 AM</td>
                                            <td>07:00 PM</td>
                                            <td>09:20 m</td>
                                            <td>
                                                <div class="eye" data-bs-toggle="modal"
                                                     data-bs-target="#editTeamModal">

                                                    <img src="{{ asset('assets/assestsnew/eye.svg') }}" alt="">
                                                </div>
                                            </td>

                                        </tr>
                                        <tr>
                                            <td>2</td>
                                            <td>12-March-2025</td>
                                            <td>10:00 AM</td>
                                            <td>07:00 PM</td>
                                            <td>09:20 m</td>
                                            <td>
                                                <div class="eye" data-bs-toggle="modal"
                                                     data-bs-target="#editTeamModal">

                                                    <img src="{{ asset('assets/assestsnew/eye.svg') }}" alt="">
                                                </div>
                                            </td>

                                        <tr class="tex_fix">
                                            <td>3</td>
                                            <td>12-March-2025</td>
                                            <td>10:00 AM</td>
                                            <td>07:00 PM</td>
                                            <td>09:20 m</td>
                                            <td>
                                                <div class="eye" data-bs-toggle="modal"
                                                     data-bs-target="#editTeamModal">

                                                    <img src="{{ asset('assets/assestsnew/eye.svg') }}" alt="">
                                                </div>
                                            </td>

                                        <tr>
                                            <td>4</td>
                                            <td>12-March-2025</td>
                                            <td>10:00 AM</td>
                                            <td>07:00 PM</td>
                                            <td>09:20 m</td>
                                            <td>
                                                <div class="eye" data-bs-toggle="modal"
                                                     data-bs-target="#editTeamModal">

                                                    <img src="{{ asset('assets/assestsnew/eye.svg') }}" alt="">
                                                </div>
                                            </td>
                                        <tr class="tex_fix">
                                            <td>5</td>
                                            <td>12-March-2025</td>
                                            <td>10:00 AM</td>
                                            <td>07:00 PM</td>
                                            <td>09:20 m</td>
                                            <td>
                                                <div class="eye" data-bs-toggle="modal"
                                                     data-bs-target="#editTeamModal">

                                                    <img src="{{ asset('assets/assestsnew/eye.svg') }}" alt="">
                                                </div>
                                            </td>

                                        <tr>
                                            <td>6</td>
                                            <td>12-March-2025</td>
                                            <td>10:00 AM</td>
                                            <td>07:00 PM</td>
                                            <td>09:20 m</td>
                                            <td>
                                                <div class="eye" data-bs-toggle="modal"
                                                     data-bs-target="#editTeamModal">

                                                    <img src="{{ asset('assets/assestsnew/eye.svg') }}" alt="">
                                                </div>
                                            </td>

                                        <tr class="tex_fix">
                                            <td>7</td>
                                            <td>12-March-2025</td>
                                            <td>10:00 AM</td>
                                            <td>07:00 PM</td>
                                            <td>09:20 m</td>
                                            <td>
                                                <div class="eye" data-bs-toggle="modal"
                                                     data-bs-target="#editTeamModal">

                                                    <img src="{{ asset('assets/assestsnew/eye.svg') }}" alt="">
                                                </div>
                                            </td>

                                        <tr>
                                            <td>8</td>
                                            <td>12-March-2025</td>
                                            <td>10:00 AM</td>
                                            <td>07:00 PM</td>
                                            <td>09:20 m</td>
                                            <td>
                                                <div class="eye" data-bs-toggle="modal"
                                                     data-bs-target="#editTeamModal">

                                                    <img src="{{ asset('assets/assestsnew/eye.svg') }}" alt="">
                                                </div>
                                            </td>
                                        </tbody>
                                    </table>
                                </div>

                                <div class="row mt-3">
                                    <div class="col-12">
                                        <div class="row justify-content-end">
                                            <div class="col-md-4">

                                            </div>
                                            <div class="col-md-12">
                                                <nav aria-label="Page navigation">
                                                    <ul class="pagination justify-content-center">
                                                        <li class="page-item">
                                                            <a class="page-link1" href="#">&lt;</a>
                                                        </li>
                                                        <li class="page-item active">
                                                            <a class="page-link" href="#">2</a>
                                                        </li>
                                                        <li class="page-item">
                                                            <a class="page-link1" href="#">&gt;</a>
                                                        </li>
                                                    </ul>
                                                </nav>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-9 col-lg-12 col-md-12 mt-4 mt-xl-0 d-none" id="breakContent">
                    <div class="chart_box1">
                        <div class="d-flex justify-content-between">
                            <h2 class="text-dark fw-semibold fs-4">Break</h2>
                        </div>
                        <div class="row mt-2">
                            <div class="col-md-12 bg-white rounded-3">

                                <div class="attendance-table-outer">
                                    <table class="attendance-table">
                                        <thead>
                                        <tr>
                                            <th>SNo</th>
                                            <th>Date</th>
                                            <th>Break Type</th>
                                            <th>Break Start</th>
                                            <th>Break End</th>
                                            <th>Break Duration</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <tr class="tex_fix">
                                            <td>
                                                1
                                            </td>
                                            <td>12-March-2025</td>
                                            <td>Lunch</td>
                                            <td>03:05 pm</td>
                                            <td>04:07 pm</td>
                                            <td>00:59m:58s</td>
                                        </tr>
                                        <tr class="">
                                            <td>
                                                2
                                            </td>
                                            <td>12-March-2025</td>
                                            <td>Lunch</td>
                                            <td>03:05 pm</td>
                                            <td>04:07 pm</td>
                                            <td>00:59m:58s</td>
                                        </tr>
                                        <tr class="tex_fix">
                                            <td>
                                                3
                                            </td>
                                            <td>12-March-2025</td>
                                            <td>Lunch</td>
                                            <td>03:05 pm</td>
                                            <td>04:07 pm</td>
                                            <td>00:59m:58s</td>
                                        </tr>

                                        <tr>
                                            <td>
                                                4
                                            </td>
                                            <td>12-March-2025</td>
                                            <td>Lunch</td>
                                            <td>03:05 pm</td>
                                            <td>04:07 pm</td>
                                            <td>00:59m:58s</td>
                                        </tr>

                                        <tr class="tex_fix">
                                            <td>
                                                5
                                            </td>
                                            <td>12-March-2025</td>
                                            <td>Lunch</td>
                                            <td>03:05 pm</td>
                                            <td>04:07 pm</td>
                                            <td>00:59m:58s</td>
                                        </tr>

                                        <tr>
                                            <td>
                                                6
                                            </td>
                                            <td>12-March-2025</td>
                                            <td>Lunch</td>
                                            <td>03:05 pm</td>
                                            <td>04:07 pm</td>
                                            <td>00:59m:58s</td>
                                        </tr>
                                        <tr class="tex_fix">
                                            <td>
                                                7
                                            </td>
                                            <td>12-March-2025</td>
                                            <td>Lunch</td>
                                            <td>03:05 pm</td>
                                            <td>04:07 pm</td>
                                            <td>00:59m:58s</td>
                                        </tr>
                                        <tr>
                                            <td>
                                                8
                                            </td>
                                            <td>12-March-2025</td>
                                            <td>Lunch</td>
                                            <td>03:05 pm</td>
                                            <td>04:07 pm</td>
                                            <td>00:59m:58s</td>
                                        </tr>

                                        </tbody>
                                    </table>
                                </div>

                                <div class="row mt-3">
                                    <div class="col-12">
                                        <div class="row justify-content-end">
                                            <div class="col-md-4">

                                            </div>
                                            <div class="col-md-12">
                                                <nav aria-label="Page navigation">
                                                    <ul class="pagination justify-content-center">
                                                        <li class="page-item">
                                                            <a class="page-link1" href="#">&lt;</a>
                                                        </li>
                                                        <li class="page-item active">
                                                            <a class="page-link" href="#">2</a>
                                                        </li>
                                                        <li class="page-item">
                                                            <a class="page-link1" href="#">&gt;</a>
                                                        </li>
                                                    </ul>
                                                </nav>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-9 d-none" id="activityContent">
                    <div class="row g-3">
                        <div class="col-lg-3 col-md-6">
                            <div class="box_2">
                                <p class="fs-6 fw-normal col_P mb-0">Top Application</p>
                                <h3 class="text-dark fw-semibold fs-3 mt-2">Chrome</h3>
                                <div class="inside_round mt-3">
                                    <p class="mb-0">01h:00m</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-6">
                            <div class="box_3">
                                <p class="fs-6 fw-normal col_P mb-0">Top Category</p>
                                <h3 class="text-dark fw-semibold fs-3 mt-2">Software</h3>
                                <div class="inside_round1 mt-3">
                                    <p class="mb-0">01h:00m</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6 col-md-12">
                            <div class="box_4">
                                <p class="fs-6 fw-normal col_P mb-0">Top URL</p>
                                <h3 class="text-dark fw-semibold fs-3 mt-2">https://dribbble.com/</h3>
                                <div class="inside_round2 mt-3">
                                    <p class="mb-0">01h:00m</p>
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
                                    <div class="col-4 mb-3">
                                        <div style="position: relative; width: 300px; height: 300px; margin: auto;">
                                            <div id="donut_single2" style="width: 100%; height: 100%;"></div>
                                            <div id="chart-center-text2" style="
                                                      position: absolute;
                                                      top: 50%;
                                                      left: 50%;
                                                      transform: translate(-50%, -50%);
                                                      pointer-events: none;
                                                      font-family: Arial, sans-serif;
                                                      color: #333;
                                                    "></div>
                                        </div>
                                        <div id="attendance-summary2"></div>
                                    </div>
                                    <div class="col-8">
                                        <div class="py-4">
                                            <div class="d-flex gap-3 justify-content-evenly">

                                                <div class="info-card">
                                                    <div class="small-text">
                                                        <span class="green-dot"></span> Total online time
                                                    </div>
                                                    <div class="big-text">
                                                        32h 18m
                                                    </div>
                                                    <div class="bottom-text">
                                                        For the last 7 days
                                                    </div>
                                                </div>

                                                <div class="info-card">
                                                    <div class="small-text">
                                                        <span class="green-dot"></span> Average online time
                                                    </div>
                                                    <div class="big-text">
                                                        06h 27m
                                                    </div>
                                                    <div class="bottom-text">
                                                        Average per day
                                                    </div>
                                                </div>

                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-12">
                            <div class="row mt-3">
                                <div class="col-md-12 bg-white rounded-3">

                                    <div class="attendance-table-outer">
                                        <table class="attendance-table">
                                            <thead>
                                            <tr>
                                                <th>Attendance</th>
                                                <th>Online Time</th>
                                                <th>Active Time</th>
                                                <th>Idle Time</th>
                                                <th>Break Time</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            <tr class="tex_fix">
                                                <td>5</td>
                                                <td>06h 27m</td>
                                                <td>04h 27m</td>
                                                <td>01h 27m</td>
                                                <td>01h 27m</td>
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

        </div>
    </div>

    <div class="modal fade" id="editTeamModal" tabindex="-1" aria-labelledby="editTeamModalLabel"
         aria-hidden="true">
        <div class="modal-dialog modal-dialog-end" style="margin-right: 0; margin-top: 0;">
            <div class="modal-content rounded-5 shadow p-4"
                 style="height: 100vh; width: 500px; overflow-y: auto;">
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
    </div>  --}}

    <div class="col-12 py-5">
        <h1 class="text-primary fs-1 fw-bold text-center mt-5">Welcome To Dashboard</h1>
    </div>
@endsection
