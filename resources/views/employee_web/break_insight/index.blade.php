@extends('company.layouts.company')
@section('page-title')
    {{ __('Break Insight') }}
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


    <script type="text/javascript" src="https://cdn.jsdelivr.net/jquery/latest/jquery.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css"/>

    {{--  <script type="text/javascript">
          $(function() {
              var start = moment().subtract(29, 'days');
              var end = moment();

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
                      'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1,
                          'month').endOf('month')]
                  }
              }, cb);

              cb(start, end);
          });
      </script> --}}

    <script>
        const selectedStartDate = "{{ request('start_date') ?? \Carbon\Carbon::now()->subDays(29)->format('Y-m-d') }}";
        const selectedEndDate = "{{ request('end_date') ?? \Carbon\Carbon::now()->format('Y-m-d') }}";
    </script>
    <script type="text/javascript">
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
                    'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1,
                        'month').endOf('month')]
                }
            }, cb);
            cb(start, end);
        });
    </script>

@endpush

@section('content')
    {{--    @include('employee_web.layouts.partials.nav')--}}
    @include('company.layouts.partials.nav')

    @php
        $user = \Illuminate\Support\Facades\Auth::user()->load('employee');
    @endphp

    <div class="col-12 pb-5">
        <div class="row align-items-center">
            <div class="col-md-6">
                <div class="d-flex justify-content-start align-items-center gap-2">
                    @if ($breaks->isNotEmpty())
                        <h5 class="fw-semibold fs-2 text-black mb-0">Hi,
                            {{ $breaks->first()->employee->user->name ?? 'N/A' }}
                        </h5>
                    @else
                        <h5 class="fw-semibold fs-2 text-black mb-0">Hi, Guest</h5>
                    @endif
                </div>


            </div>
            <div class="col-lg-6 selecters_head">
                <div class="row justify-content-lg-end gx-3">

                    <form method="GET" action="{{ route('employee.breakInsight') }}"
                          class="d-flex gap-2 justify-content-lg-end" style="flex-direction: row !important;">
                        <div class="col-auto d-flex justify-content-lg-end align-items-center gap-2">
                            <div id="reportrange"
                                 style="background: #fff; cursor: pointer; padding: 10px 12px; border: 1px solid #ccc; width: 100%; display: flex; align-items: inherit; border-radius: 100px;">
                                <i class="fa fa-calendar"></i>&nbsp;
                                <span></span> <i class="fa fa-caret-down"></i>
                            </div>
                            <input type="hidden" name="start_date" id="start_date">
                            <input type="hidden" name="end_date" id="end_date">

                        </div>

                        <button class="download_arrbtn" type="submit"><i class="fas fa-search"></i></button>
                        <span><a href="{{ route('employee.breakInsight') }}" class="download_arrbtn"><i
                                    class="fas fa-redo-alt" style="margin:12px"></i></a>Clear Filter</span>
                    </form>

                </div>
            </div>
        </div>

        <div class="col-12 mt-4">
            <div class="row">
                <div class="col-xl-3 col-md-12 mt-4 mt-xl-0">
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
                            <div class="mt-3 actives1 py-3 px-2">

                                <h6 class="text-black text-start fw-normal fs-5 mb-0">Attendance</h6>
                            </div>
                        </a>
                        <a href="{{ route('employee.breakInsight') }}">
                            <div class="mt-3 py-3 px-2  actives1 active-tab" data-section="break">

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

                <div class="col-xl-9 col-lg-12 col-md-12 mt-4 mt-xl-0" id="breakContent">
                    <div class="chart_box1">
                        <div class="d-flex justify-content-between">
                            <h2 class="text-dark fw-semibold fs-4">Break</h2>
                        </div>
                        <div class="row mt-2">
                            <div class="col-md-12  rounded-3">

                                <div class="attendance-table-outer">
                                    <table class="attendance-table">
                                        <thead>
                                        <tr>

                                            <th>Date</th>
                                            <th>Break Type</th>
                                            <th>Break Start</th>
                                            <th>Break End</th>
                                            <th>Break Duration</th>
                                        </tr>
                                        </thead>

                                        <tbody>
                                        @forelse($breaks as $break)
                                            <tr>

                                                <td class="tex_fix">{{ $break->created_at ? $break->created_at->format('Y-m-d') : '-' }}
                                                </td>

                                                <td>{{ ucfirst($break->breakType->break_name ?? 'Unknown') }}</td>
                                                <td>{{ \Carbon\Carbon::parse($break->break_started_at)->format('h:i A') }}
                                                </td>
                                                <td>{{ $break->break_ended_at ? \Carbon\Carbon::parse($break->break_ended_at)->format('h:i A') : 'Ongoing' }}
                                                </td>
                                                <td>{{ $break->duration ?? '-' }}</td>
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
                                                        <li class="{{ $breaks->onFirstPage() ? 'disabled' : '' }}">
                                                            <a href="{{ $breaks->url(1) }}"
                                                               class="page-link1">&#171;</a>
                                                        </li>

                                                        {{-- Previous Page --}}
                                                        <li class="{{ $breaks->onFirstPage() ? 'disabled' : '' }}">
                                                            <a href="{{ $breaks->previousPageUrl() }}"
                                                               class="page-link1">
                                                                <i class="fa-solid fa-chevron-left"></i>
                                                            </a>
                                                        </li>

                                                        {{-- Page Numbers --}}
                                                        @php
                                                            $start = max(1, $breaks->currentPage() - 2);
                                                            $end = min($start + 4, $breaks->lastPage());
                                                        @endphp
                                                        @for ($i = $start; $i <= $end; $i++)
                                                            <li class="{{ $breaks->currentPage() == $i ? 'active_pagination' : '' }}">
                                                                <a href="{{ $breaks->url($i) }}"
                                                                   class="page-link1">{{ $i }}</a>
                                                            </li>
                                                        @endfor

                                                        {{-- Next Page --}}
                                                        <li class="{{ !$breaks->hasMorePages() ? 'disabled' : '' }}">
                                                            <a href="{{ $breaks->nextPageUrl() }}" class="page-link1">
                                                                <i class="fa-solid fa-chevron-right"></i>
                                                            </a>
                                                        </li>

                                                        {{-- Last Page --}}
                                                        <li class="{{ !$breaks->hasMorePages() ? 'disabled' : '' }}">
                                                            <a href="{{ $breaks->url($breaks->lastPage()) }}"
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
                                                               max="{{ $breaks->lastPage() }}"
                                                               class="form-control form-control-sm" style="width: 80px;"
                                                               placeholder="Page">
                                                        <button class="btn btn-sm btn-primary" type="submit">Go</button>
                                                    </form>
                                                    <span class="text-nowrap small text-center text-md-start">of {{ $breaks->total() }} Records</span>
                                                </div>
                                            </div>

                                            <!-- Showing Range -->
                                            <div class="col-12 mt-3">
                                                <div class="d-flex justify-content-center">
                                                    <span>{{ $breaks->firstItem() }} to {{ $breaks->lastItem() }} of {{ $breaks->total() }} Records</span>
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
