@extends('company.layouts.company')
@section('page-title')
    {{ __('Attendance') }}
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

    <script type="text/javascript">
        const selectedStartDate = "{{ request('start_date') ?? \Carbon\Carbon::now()->startOfMonth()->format('Y-m-d') }}";
        const selectedEndDate = "{{ request('end_date') ?? \Carbon\Carbon::now()->format('Y-m-d') }}";

        $(function () {
            var start = moment(selectedStartDate, "YYYY-MM-DD");
            var end = moment(selectedEndDate, "YYYY-MM-DD");

            function cb(start, end) {
                $('#reportrange span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
                $('#start_date').val(start.format('YYYY-MM-DD'));
                $('#end_date').val(end.format('YYYY-MM-DD'));
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
                    'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
                }
            }, cb);

            cb(start, end);
        });
    </script>

    <!-- Cart 1 Attentance -->

    <script type="text/javascript">
        google.charts.load('current', {
            'packages': ['corechart']
        });
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
                    0: {
                        color: '#316FF6'
                    },
                    1: {
                        color: '#E8E8E8'
                    }
                }
            };

            var chart = new google.visualization.PieChart(document.getElementById('donut_single'));
            chart.draw(data, options);

            document.getElementById('chart-center-text').innerHTML = `
        <div style="text-align: center;">
          <h2 style="margin: 0; font-size: 32px;">${total}</h2>
          <span style="font-size: 14px;">Total</span>
        </div>`;

            document.getElementById('attendance-summary').innerHTML = `
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
        </div>`;
        }
    </script>

    <!-- Cart 2 in Activity -->
    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>

    <script type="text/javascript">
        google.charts.load('current', {
            'packages': ['corechart']
        });
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

    <!--calendar-container-->

    <script>
        const monthlyAttendanceRoute = "{{ route('employee.attendance.monthly') }}";

        let currentDate = new Date();

        function renderCalendar(date, presentDates = [], absentDatesArray = []) {
            const monthYear = document.getElementById('monthYear');
            const calendarDates = document.getElementById('calendarDates');
            calendarDates.innerHTML = '';

            const year = date.getFullYear();
            const month = date.getMonth();

            const firstDay = new Date(year, month, 1).getDay();
            const lastDate = new Date(year, month + 1, 0).getDate();

            const today = new Date();
            today.setHours(0, 0, 0, 0); // Ensure time doesn't interfere with date comparison

            const isThisMonth = today.getMonth() === month && today.getFullYear() === year;

            monthYear.textContent = date.toLocaleString('default', {
                month: 'long',
                year: 'numeric'
            });

            // Fill in empty slots before the first day of the month
            for (let i = 0; i < firstDay; i++) {
                calendarDates.appendChild(document.createElement('div'));
            }

            for (let day = 1; day <= lastDate; day++) {
                const dayCell = document.createElement('div');
                dayCell.textContent = day;

                const currentLoopDate = new Date(year, month, day);
                currentLoopDate.setHours(0, 0, 0, 0); // Normalize for comparison

                // Format the date to match backend format
                const formattedDate = `${year}-${String(month + 1).padStart(2, '0')}-${String(day).padStart(2, '0')}`;

                // Do not apply any color classes if the date is in the future
                if (currentLoopDate > today) {
                    calendarDates.appendChild(dayCell);
                    continue;
                }

                if (presentDates.includes(formattedDate)) {
                    dayCell.classList.add('present');
                } else if (absentDatesArray.includes(formattedDate)) {
                    dayCell.classList.add('absent');
                } else if (isThisMonth && day === today.getDate()) {
                    dayCell.classList.add('today');
                }

                calendarDates.appendChild(dayCell);
            }
        }

        function fetchAttendanceAndRender(date) {
            const year = date.getFullYear();
            const month = date.getMonth() + 1;

            fetch(`${monthlyAttendanceRoute}?year=${year}&month=${month}`)
                .then(response => response.json())
                .then(data => {
                    const presentDates = data.presentDates || [];
                    const absentDates = Array.isArray(data.absentDates) ? data.absentDates : Object.values(data
                        .absentDates);
                    renderCalendar(date, presentDates, absentDates);
                })
                .catch(error => {
                    console.error("Error fetching attendance data:", error);
                });
        }

        function prevMonth() {
            currentDate.setMonth(currentDate.getMonth() - 1);
            fetchAttendanceAndRender(currentDate);
        }

        function nextMonth() {
            currentDate.setMonth(currentDate.getMonth() + 1);
            fetchAttendanceAndRender(currentDate);
        }

        // Initial render
        fetchAttendanceAndRender(currentDate);
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const modal = document.getElementById('editTeamModal');

            modal.addEventListener('show.bs.modal', function (event) {
                const button = event.relatedTarget;
                const name = button.getAttribute('data-name');
                const email = button.getAttribute('data-email');
                const date = button.getAttribute('data-date');
                const breaks = JSON.parse(button.getAttribute('data-breaks'));
                const logs = JSON.parse(button.getAttribute('data-logs'));

                // Update modal header
                modal.querySelector('.profile-circle').textContent = name ? name.substring(0, 2).toUpperCase() : 'UN';
                modal.querySelector('.userr').textContent = name;
                modal.querySelector('.userr').nextElementSibling.textContent = email;
                modal.querySelector('.datadata').textContent = date;

                // --- Work logs ---
                const workBody = modal.querySelector('.worklogs-body');
                workBody.innerHTML = '';
                if (logs.length > 0) {
                    logs.forEach(log => {
                        const row = document.createElement('tr');
                        row.innerHTML = `
                    <td style="background: transparent;">${log.start}</td>
                    <td>${log.end}</td>
                `;
                        workBody.appendChild(row);
                    });
                } else {
                    const row = document.createElement('tr');
                    row.innerHTML = '<td colspan="2">No work logs</td>';
                    workBody.appendChild(row);
                }

                // --- Break logs ---
                const breakBody = modal.querySelector('.breaklogs-body');
                breakBody.innerHTML = '';
                if (breaks.length > 0) {
                    breaks.forEach(b => {
                        const row = document.createElement('tr');
                        row.innerHTML = `
                    <td>${b.duration}</td>
                    <td>${formatTime(b.start)}</td>
                    <td>${formatTime(b.end)}</td>
                `;
                        breakBody.appendChild(row);
                    });
                } else {
                    const row = document.createElement('tr');
                    row.innerHTML = '<td colspan="3">No breaks</td>';
                    breakBody.appendChild(row);
                }
            });

            function formatTime(datetime) {
                const date = new Date(datetime.replace(' ', 'T'));
                return isNaN(date) ? 'Invalid Date' : date.toLocaleTimeString([], {
                    hour: '2-digit',
                    minute: '2-digit',
                    hour12: true
                });
            }
        });
    </script>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const modal = document.getElementById('exampleModal');
            const submitBtn = document.getElementById("submitWorkLog");

            // When modal opens → set values from button
            modal.addEventListener('show.bs.modal', function (event) {
                const button = event.relatedTarget;
                const date = button.getAttribute('data-date');
                const employeeId = button.getAttribute('data-employee');
                const workLogs = JSON.parse(button.getAttribute('data-worklog') || '[]');

                // Fill modal fields
                document.getElementById('date_of_workLog_display').value = date;
                document.getElementById('date_of_workLog').value = date;
                document.getElementById('employee_id').value = employeeId;

                // Pre-fill textarea with existing work logs (if any)
                const description = workLogs.length > 0
                    ? workLogs.map(w => w.description).join("\n\n") // <-- removed updated_at
                    : '';
                document.getElementById('worklog_description').value = description;

                // Clear previous validation errors
                document.querySelectorAll(".text-danger").forEach(el => el.textContent = "");
            });

            // Handle submit
            submitBtn.addEventListener("click", function () {
                const date = document.getElementById("date_of_workLog").value;
                const employeeId = document.getElementById("employee_id").value;
                const description = document.getElementById("worklog_description").value;

                // Clear old errors
                document.querySelectorAll(".text-danger").forEach(el => el.textContent = "");

                fetch("{{ route('employee.update.work.Log') }}", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": "{{ csrf_token() }}"
                    },
                    body: JSON.stringify({
                        date_of_workLog: date,
                        employee_id: employeeId,
                        worklog_description: description
                    })
                })
                    .then(async response => {
                        if (!response.ok) {
                            let errorData = await response.json();
                            if (errorData.errors) {
                                Object.keys(errorData.errors).forEach(key => {
                                    const errorElement = document.getElementById(`error-${key}`);
                                    if (errorElement) errorElement.textContent = errorData.errors[key][0];
                                });
                            }
                            throw new Error("Validation failed");
                        }
                        return response.json();
                    })
                    .then(data => {
                        if (data.success) {
                            const bsModal = bootstrap.Modal.getInstance(modal);
                            bsModal.hide();

                            Swal.fire({
                                icon: 'success',
                                title: 'Success',
                                text: data.message,
                                timer: 2000,
                                showConfirmButton: false
                            });

                            location.reload();
                        }
                    })
                    .catch(error => {
                        console.error("Error submitting work log:", error);
                    });
            });
        });
    </script>

@endpush

@section('content')
    <style>
        .activity_modal_overflow {
            max-height: 60vh;
            overflow: auto;
        }

        .attentance_spandateinput span {
            font-weight: 400;
        }

        .calendar-dates div {
            display: inline-block;
            width: 40px;
            height: 40px;
            line-height: 40px;
            text-align: center;
            margin: 2px;
            border-radius: 4px;
            padding: 0px;
        }

        .present {
            background-color: #28a745;
            /* Green */
            color: white;
        }

        .absent {
            background-color: #dc3545;
            /* Red */
            color: white;
        }

        .future {
            background-color: #e0e0e0;
        }

        .today {
            border: 2px solid #007bff;
        }
    </style>

    {{--@include('employee_web.layouts.partials.nav')--}}
    @include('company.layouts.partials.nav')

    @php
        $user = \Illuminate\Support\Facades\Auth::user()->load('employee');
    @endphp

    <div class="col-12 mb-5">
        <div class="row align-items-center">
            <div class="col-md-6">
                <div class="d-flex justify-content-start align-items-center gap-2">
                    <h5 class="fw-semibold fs-2 text-black mb-0">Hi,
                        {{ $user->name ?? 'N/A' }}
                    </h5>
                </div>
            </div>
        </div>

        <div class="col-12 mt-4">
            <div class="row">
                <div class="col-xl-3 col-lg-6 col-md-12 mt-4 mt-xl-0">
                    <div class="chart_box2 h-100">
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
                            <h4 class="text-black fw-semibold fs-4">
                                {{ $user->name ?? 'N/A' }}
                            </h4>
                        </div>
                        <a href="{{ route('employee.self-report') }}">
                            <div class="mt-3 actives1 py-3 px-2 active-tab" data-section="attendance">

                                <h6 class="text-black text-start fw-normal fs-5 mb-0">Attendance</h6>

                            </div>
                        </a>
                        <a href="{{ route('employee.breakInsight') }}">
                            <div class="mt-3 py-3 px-2  actives1">

                                <h6 class="text-black fw-normal fs-5 mb-0">Break</h6>

                            </div>
                        </a>
                        <a href="{{ route('employee.activity') }}">
                            <div class="mt-3 py-3 px-2 actives1">

                                <h6 class="text-black fw-normal fs-5 mb-0">Activity</h6>

                            </div>
                        </a>
                    </div>
                </div>

                <div class="col-xl-4 col-lg-6 col-md-12 mt-4 mt-xl-0" id="attendanceContent">
                    <div class="chart_box1">
                        <div class="d-flex justify-content-between">
                            <h2 class="text-dark fw-semibold fs-4">Attendance</h2>
                        </div>
                        <div style="position: relative; width: 300px; height: 300px; margin: auto;">
                            <div id="donut_single" style="width: 100%; height: 100%;"></div>
                            <div id="chart-center-text"
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
                        <div id="attendance-summary"></div>
                    </div>
                </div>
                <div class="col-xl-5 col-lg-12 col-md-12 mt-4 mt-xl-0" id="attendanceContent1">
                    <div class="chart_box1">
                        <div class="d-flex justify-content-between">
                            <h2 class="text-dark fw-semibold fs-4">Monthly Attendance</h2>
                        </div>
                        <div class="calendar-container1 mt-2">
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
                        <div class="d-flex flex-wrap justify-content-between align-items-center">
                            <h2 class="text-dark fw-semibold fs-4 mb-md-0">Details</h2>
                            <div class="col-lg-6 selecters_head">
                                <div class="row justify-content-lg-end gx-3">
                                    <form method="GET" action="{{ route('employee.self-report') }}"
                                          class="d-flex flex-wrap gap-2 justify-content-lg-end"
                                          style="flex-direction: row !important;">
                                        <div
                                            class="col-auto d-flex justify-content-lg-end align-items-center gap-2 attentance_spandateinput">
                                            <div id="reportrange"
                                                 style="background: #fff; cursor: pointer; padding: 10px 12px; border: 1px solid #ccc; width: 100%; display: flex; align-items: inherit; border-radius: 100px;">
                                                <i class="fa fa-calendar"></i>&nbsp;
                                                <span></span> <i class="fa fa-caret-down"></i>
                                            </div>

                                            <input type="hidden" name="start_date" id="start_date">
                                            <input type="hidden" name="end_date" id="end_date">

                                        </div>
                                        <button class="download_arrbtn" type="submit"><i class="fas fa-search"></i>
                                        </button>
                                        <span><a href="{{ route('employee.self-report') }}" class="download_arrbtn"><i
                                                    class="fas fa-redo-alt"
                                                    style="margin:12px"></i></a>Clear Filter</span>
                                    </form>
                                </div>

                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-md-12 rounded-3">

                                <div class="attendance-table-outer">

                                    <table class="attendance-table">
                                        <thead>
                                        <tr>
                                            <th>Date</th>
                                            <th>In</th>
                                            <th>Out</th>
                                            <th>Day Status</th>
                                            <th>Log</th>
                                            <th>Late</th>
                                            <th>Early Leaving</th>
                                            <th>Overtime</th>
                                            <th>Total Rest</th>
                                        </tr>
                                        </thead>

                                        <tbody>
                                        @forelse($attendanceRecords as $attendance)
                                            <tr>

                                                <td class="tex_fix">{{ $attendance->date }}</td>
                                                <td>{{ $attendance->clock_in }}</td>
                                                <td>{{ $attendance->clock_out }}</td>
                                                <td>
                                                    @if($attendance->workplace_status)
                                                        @if($attendance->workplace_status === 'Full Day')
                                                            <span
                                                                class="badge bg-success">{{ $attendance->workplace_status }}</span>
                                                        @elseif($attendance->workplace_status === 'Absent')
                                                            <span
                                                                class="badge bg-danger">{{ $attendance->workplace_status }}</span>
                                                        @else
                                                            <span
                                                                class="badge bg-warning">{{ $attendance->workplace_status }}</span>
                                                        @endif
                                                    @endif
                                                </td>

                                                @php
                                                    $formattedBreaks = $attendance->breakTimes->map(function ($break) use ($attendance) {
                                                        return [
                                                            'start' => $attendance->date . ' ' . $break->break_started_at,
                                                            'end' => $attendance->date . ' ' . $break->break_ended_at,
                                                            'duration' => $break->duration,
                                                        ];
                                                    });
                                                @endphp

                                                <td class="d-flex gap-2 flex-wrap">
                                                    <div class="eye" data-bs-toggle="modal"
                                                         data-bs-target="#editTeamModal"
                                                         data-name="{{ $attendance->employee->user->name ?? 'UN' }}"
                                                         data-date="{{ $attendance->date }}"
                                                         data-email="{{ $attendance->employee->user->email ?? '' }}"
                                                         data-breaks='@json($formattedBreaks)'
                                                         data-logs='@json($attendance->log->map(function ($log) {
                                                                 return [
                                                                     "start" => $log->formatted_clock_in,
                                                                     "end" => $log->formatted_clock_out,
                                                                 ];
                                                             }))'>
                                                        <img src="{{ asset('assets/assestsnew/eye.svg') }}" alt="">
                                                    </div>

                                                    {{-- Worklog button --}}
                                                    <div data-bs-toggle="modal"
                                                         data-bs-target="#exampleModal"
                                                         class="work_logsetsbtn"
                                                         data-date="{{ $attendance->date }}"
                                                         data-employee="{{ $attendance->employee->id ?? '' }}"
                                                         data-worklog='@json($attendance->work_logs)'>
                                                        <i class="fa-solid fa-comment-dots"></i>
                                                    </div>
                                                </td>
                                                <td>{{ $attendance->late }}</td>
                                                <td>{{ $attendance->early_leaving }}</td>
                                                <td>{{ $attendance->overtime }}</td>
                                                <td>{{ $attendance->total_rest }}</td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="10">
                                                    <div class="row justify-content-center text-center">
                                                        <img class="w-25"
                                                             src="{{ asset('assets/assestsnew/no_datasvg.svg') }}"
                                                             alt="">
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforelse
                                    </table>
                                </div>

                                <!-- Pagination Links -->
                                <div class="row mt-3 justify-content-center">
                                    <div class="col-12 optional_inputpagi">
                                        <div class="row align-items-start justify-content-center">

                                            <!-- Left Spacer -->
                                            <div class="col-12 col-md-3 mb-3 mb-md-0">
                                                <div class="data_table_select">
                                                    <form id="perPageForm" method="GET" action="{{ url()->current() }}"
                                                          class="d-flex align-items-center gap-2 m-0">
                                                        {{-- Preserve filters --}}
                                                        @foreach(request()->except(['page', 'per_page']) as $key => $value)
                                                            <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                                                        @endforeach

                                                        <label for="per_page" class="small mb-0 text-nowrap">Items per
                                                            page:</label>

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
                                                        <li class="{{ $attendanceRecords->onFirstPage() ? 'disabled' : '' }}">
                                                            <a href="{{ $attendanceRecords->url(1) }}"
                                                               class="page-link1">&#171;</a>
                                                        </li>

                                                        {{-- Previous Page --}}
                                                        <li class="{{ $attendanceRecords->onFirstPage() ? 'disabled' : '' }}">
                                                            <a href="{{ $attendanceRecords->previousPageUrl() }}"
                                                               class="page-link1">
                                                                <i class="fa-solid fa-chevron-left"></i>
                                                            </a>
                                                        </li>

                                                        {{-- Page Numbers --}}
                                                        @php
                                                            $start = max(1, $attendanceRecords->currentPage() - 2);
                                                            $end = min($start + 4, $attendanceRecords->lastPage());
                                                        @endphp
                                                        @for ($i = $start; $i <= $end; $i++)
                                                            <li class="{{ $attendanceRecords->currentPage() == $i ? 'active_pagination' : '' }}">
                                                                <a href="{{ $attendanceRecords->url($i) }}"
                                                                   class="page-link1">{{ $i }}</a>
                                                            </li>
                                                        @endfor

                                                        {{-- Next Page --}}
                                                        <li class="{{ !$attendanceRecords->hasMorePages() ? 'disabled' : '' }}">
                                                            <a href="{{ $attendanceRecords->nextPageUrl() }}"
                                                               class="page-link1">
                                                                <i class="fa-solid fa-chevron-right"></i>
                                                            </a>
                                                        </li>

                                                        {{-- Last Page --}}
                                                        <li class="{{ !$attendanceRecords->hasMorePages() ? 'disabled' : '' }}">
                                                            <a href="{{ $attendanceRecords->url($attendanceRecords->lastPage()) }}"
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
                                                            <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                                                        @endforeach

                                                        <input type="number" name="page" min="1"
                                                               max="{{ $attendanceRecords->lastPage() }}"
                                                               class="form-control form-control-sm" style="width: 80px;"
                                                               placeholder="Page">
                                                        <button class="btn btn-sm btn-primary" type="submit">Go</button>
                                                    </form>
                                                    <span class="text-nowrap small text-center text-md-start">of {{ $attendanceRecords->total() }} Records</span>
                                                </div>
                                            </div>

                                            <!-- Showing Range -->
                                            <div class="col-12 mt-3">
                                                <div class="d-flex justify-content-center">
                                                    <span>{{ $attendanceRecords->firstItem() }} to {{ $attendanceRecords->lastItem() }} of {{ $attendanceRecords->total() }} Records</span>
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

    {{--Work Log--}}
    <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content p-0">
                <div class="modal-header">
                    <h4 class="modal-title fs-5" id="exampleModalLabel">Work Log</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <div class="row px-3 work_updatemodalcontent">
                        {{-- Hidden employee ID --}}
                        <input type="hidden" id="employee_id" name="employee_id">

                        {{-- Date (shown but not editable) --}}
                        <div class="mb-2">
                            <label class="form-label">Date</label>
                            <input type="text" id="date_of_workLog_display" class="form-control" readonly>
                            <input type="hidden" id="date_of_workLog" name="date_of_workLog">
                            <div class="text-danger" id="error-date_of_workLog"></div>
                        </div>

                        {{-- Worklog description --}}
                        <div>
                            <label class="form-label">Worklog Description</label>
                            <textarea id="worklog_description" name="worklog_description" class="form-control"
                                      placeholder="Leave worklog description here" required></textarea>
                            <div class="text-danger" id="error-worklog_description"></div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" data-bs-dismiss="modal" id="submitWorkLog">Submit
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{--Log--}}
    <div class="modal fade" id="editTeamModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-end" style="margin-right: 0; margin-top: 0;">
            <div class="modal-content rounded-5 shadow p-4" style="height: 100vh; width: 500px; overflow-y: auto;">
                <div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="container mt-4">
                        <div class="profile-box">
                            <div class="profile-circle">UN</div>
                            <div>
                                <h6 class="mb-0 fw-semibold userr">User Name</h6>
                                <small class="text-muted">user@example.com</small><br>
                                <small class="text-muted datadata">Date</small>
                            </div>
                        </div>

                        <div class="activity_modal_overflow">
                            <div class="schedule-container mt-4">
                                <h6 class="fw-bold mb-0 p-3">Work Logs</h6>
                                <table class="table table-borderless mb-4 time-grid">
                                    <thead>
                                    <tr>
                                        <th style="background:#f8f9fd; border-bottom-color: #f1f1f1;">Clock In</th>
                                        <th>Clock Out</th>
                                    </tr>
                                    </thead>
                                    <tbody class="worklogs-body"></tbody>
                                </table>
                            </div>
                            <div class="schedule-container mt-4">

                                <h6 class="fw-bold mb-0 p-3">Break Logs</h6>
                                <table class="table table-borderless mb-0 time-grid">
                                    <thead>
                                    <tr>
                                        <th style="background: #f8f9fd; border-bottom-color: #f1f1f1;">Duration</th>
                                        <th style="background:#f8f9fd">Break Start</th>
                                        <th>Break End</th>
                                    </tr>
                                    </thead>
                                    <tbody class="breaklogs-body"></tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection
