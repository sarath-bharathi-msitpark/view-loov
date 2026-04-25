@extends('company.layouts.company')

@section('page-title')
    {{ __('Attendance Report') }}
@endsection

@section('page-icon')
    {{ asset('assets/assestsnew/header-logo.svg') }}
@endsection

@push('css-page')

@endpush

@push('theme-script')

@endpush
@push('script-page')

    <script>
        $(function () {
            const $dateInput = $('input[name="date"]');

            $dateInput.daterangepicker({
                autoUpdateInput: false,
                locale: {
                    cancelLabel: 'Clear'
                },
                ranges: {
                    'Today': [moment(), moment()],
                    'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                    'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                    'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                    'This Month': [moment().startOf('month'), moment().endOf('month')],
                    'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
                },
                startDate: moment().subtract(6, 'days'),
                endDate: moment()
            });

            $dateInput.on('apply.daterangepicker', function (ev, picker) {
                const val = picker.startDate.format('YYYY-MM-DD') === picker.endDate.format('YYYY-MM-DD')
                    ? picker.startDate.format('YYYY-MM-DD')
                    : picker.startDate.format('YYYY-MM-DD') + ' to ' + picker.endDate.format('YYYY-MM-DD');

                $(this).val(val);
                $('#attendanceForm').submit();
            });

            $dateInput.on('cancel.daterangepicker', function (ev, picker) {
                $(this).val('');
                $('#attendanceForm').submit();
            });
        });
    </script>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const modal = new bootstrap.Modal(document.getElementById('workLogModal'));

            document.querySelectorAll('.view-worklog').forEach(button => {
                button.addEventListener('click', function () {
                    const log = JSON.parse(this.getAttribute('data-worklog'));

                    if (log) {
                        const updatedAt = new Date(log.updated_at);
                        document.getElementById('worklogUpdatedAt').value = updatedAt.toLocaleString();

                        document.getElementById('worklogDescription').value = log.description;
                    }

                    modal.show();
                });
            });
        });
    </script>
@endpush

@section('content')
    @include('company.layouts.partials.nav')

    <div class="row">
        <div class="col-12">
            <div class="row align-items-center justify-content-between">
                <div class="col-md-6 d-flex align-items-center px-4 gap-2">
                    <a class="text-decoration-none" href="{{ route('organization.report.attendance')}}"><img
                            src="{{ asset('assets/assestsnew/left_arrow.svg') }}" alt=""></a>
                    <p class="text-dark mb-0 fw-medium">Daily Attendance Reports</p>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-4">
        <form method="GET" action="{{ route('organization.report.attendance.individual', $employee->id) }}"
              class="d-flex justify-content-between align-items-center" id="attendanceForm">
            <div class="selecters_head">
                <div class="d-flex gap-3">

                </div>
            </div>

            <div class="selecters_head">
                <div class="row justify-content-end gap-lg-3 gap-3 px-3" style="flex-direction: row !important;">
                    <input type="month" name="month" class="form-control"
                           value="{{ request('month', now()->format('Y-m')) }}"
                           onchange="document.getElementById('attendanceForm').submit();">
                    <button type="submit" name="download" value="excel" class="download_arrbtn"><i
                            class="fas fa-download"></i></button>
                    <span class="px-0"><a href="{{ route('organization.report.attendance') }}"
                                          class="download_arrbtn"><i
                                class="fas fa-redo-alt" style="margin:12px"></i></a>Clear Filter</span>
                </div>
            </div>
        </form>
    </div>

    <div class="container mt-5 p-0">
        <div class="card bg-white border shadow-none rounded-3  py-3">
            <div class="text-center mb-4">
                <h5 class="fw-bold mb-0 mt-4">Employee Information</h5>
            </div>
            <div class="card-body">
                <div class="row gy-4">
                    <div class="col-md-4">
                        <label class="fw-bold text-muted mb-1">Employee Name:</label>
                        <div>{{ $employee->name }}</div>
                    </div>
                    <div class="col-md-4">
                        <label class="fw-bold text-muted mb-1">Employee ID:</label>
                        <div>{{ $employee->employee_id }}</div>
                    </div>
                    <div class="col-md-4">
                        <label class="fw-bold text-muted mb-1">Email:</label>
                        <div>{{ $employee->email }}</div>
                    </div>

                    <div class="col-md-4">
                        <label class="fw-bold text-muted mb-1">Gender:</label>
                        <div>{{ $employee->gender }}</div>
                    </div>
                    <div class="col-md-4">
                        <label class="fw-bold text-muted mb-1">DOB:</label>
                        <div>{{ $employee->dob }}</div>
                    </div>
                    <div class="col-md-4">
                        <label class="fw-bold text-muted mb-1">Contact:</label>
                        <div>{{ $employee->phone }}</div>
                    </div>

                    <div class="col-md-4">
                        <label class="fw-bold text-muted mb-1">Date Of Joining:</label>
                        <div>{{ $employee->company_doj }}</div>
                    </div>
                    <div class="col-md-4">
                        <label class="fw-bold text-muted mb-1">Team:</label>
                        <div>{{ $employee->team->name }}</div>
                    </div>
                    <div class="col-md-4">
                        <label class="fw-bold text-muted mb-1">Designation:</label>
                        <div>{{ $employee->designation->name }}</div>
                    </div>

                    <div class="col-md-4">
                        <label class="fw-bold text-muted mb-1">Shift:</label>
                        <div>{{ $employee->shift->shift_name }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-5">
        <div class="col-md-12 rounded-3">
            <div class="main-content">
                <div class="attendance-table-outer">
                    <table class="attendance-table">
                        <thead>
                        <tr>
                            <th>Date</th>
                            <th>In</th>
                            <th>Out</th>
                            <th>Status</th>
                            <th>Log</th>
                            <th>Work Log</th>
                            <th>Late</th>
                            <th>Early Leaving</th>
                            <th>Online Time</th>
                            <th>Overtime</th>
                            <th>Total Rest</th>
                            <th>Idle Time Count</th>
                        </tr>
                        </thead>

                        <tbody>
                        @forelse($attendances as $attendance)
                            <tr>
                                <td class="tex_fix">{{ $attendance->date }}</td>
                                <td>{{ $attendance->clock_in }}</td>
                                <td>{{ $attendance->clock_out }}</td>
                                <td>
                                    @if($attendance->workplace_status === 'Full Day')
                                        <span class="badge bg-success">{{ $attendance->workplace_status }}</span>
                                    @elseif($attendance->workplace_status === 'Absent')
                                        <span class="badge bg-danger">{{ $attendance->workplace_status }}</span>
                                    @else
                                        <span class="badge bg-warning">{{ $attendance->workplace_status }}</span>
                                    @endif
                                </td>
                                <td>
                                    @forelse($attendance->log as $log)
                                        {{ $log->formatted_clock_in }} to {{ $log->formatted_clock_out }}<br>
                                    @empty
                                        --
                                    @endforelse
                                </td>
                                <td>
                                    @if($attendance->work_logs->isNotEmpty())
                                        <a href="javascript:void(0);"
                                           class="view-worklog"
                                           data-worklog='@json($attendance->work_logs->first())'>
                                            <img src="{{ asset('assets/assestsnew/eye.svg') }}" alt="View Work Logs"
                                                 style="width:20px;height:20px;">
                                        </a>
                                    @else
                                        <img src="{{ asset('assets/assestsnew/eye-off.svg') }}" alt="No Work Logs"
                                             style="width:20px;height:20px;">
                                    @endif
                                </td>

                                <td>{{ $attendance->late }}</td>
                                <td>{{ $attendance->early_leaving }}</td>
                                <td>{{ $attendance->online_time }}</td>
                                <td>{{ $attendance->overtime }}</td>
                                <td>{{ $attendance->total_rest }}</td>
                                <td>{{ $attendance->idleTimeOut->count() ?? '-' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10">
                                    <div class="row justify-content-center text-center">
                                        <img class="w-25" src="{{ asset('assets/assestsnew/no_datasvg.svg') }}"
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

                                        <label for="per_page" class="small mb-0 text-nowrap">Items per page:</label>

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
                                        <li class="{{$attendances->onFirstPage() ? 'disabled' : '' }}">
                                            <a href="{{$attendances->url(1) }}" class="page-link1">&#171;</a>
                                        </li>

                                        {{-- Previous Page --}}
                                        <li class="{{$attendances->onFirstPage() ? 'disabled' : '' }}">
                                            <a href="{{$attendances->previousPageUrl() }}" class="page-link1">
                                                <i class="fa-solid fa-chevron-left"></i>
                                            </a>
                                        </li>

                                        {{-- Page Numbers --}}
                                        @php
                                            $start = max(1,$attendances->currentPage() - 2);
                                            $end = min($start + 4,$attendances->lastPage());
                                        @endphp
                                        @for ($i = $start; $i <= $end; $i++)
                                            <li class="{{$attendances->currentPage() == $i ? 'active_pagination' : '' }}">
                                                <a href="{{$attendances->url($i) }}"
                                                   class="page-link1">{{ $i }}</a>
                                            </li>
                                        @endfor

                                        {{-- Next Page --}}
                                        <li class="{{ !$attendances->hasMorePages() ? 'disabled' : '' }}">
                                            <a href="{{$attendances->nextPageUrl() }}" class="page-link1">
                                                <i class="fa-solid fa-chevron-right"></i>
                                            </a>
                                        </li>

                                        {{-- Last Page --}}
                                        <li class="{{ !$attendances->hasMorePages() ? 'disabled' : '' }}">
                                            <a href="{{$attendances->url($attendances->lastPage()) }}"
                                               class="page-link1">&#187;</a>
                                        </li>
                                    </ul>
                                </div>
                            </div>

                            <!-- Page Jump Input -->
                            <div class="col-12 col-md-3 mb-3 mb-md-0">
                                <div
                                    class="d-flex flex-md-row justify-content-center justify-content-md-start gap-2 align-items-center">
                                    <form action="{{ url()->current() }}" method="GET"
                                          class="d-flex align-items-center gap-2"
                                          style="flex-direction:row !important;">
                                        {{-- Preserve filters --}}
                                        @foreach(request()->except('page') as $key => $value)
                                            <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                                        @endforeach

                                        <input type="number" name="page" min="1"
                                               max="{{$attendances->lastPage() }}"
                                               class="form-control form-control-sm" style="width: 80px;"
                                               placeholder="Page">
                                        <button class="btn btn-sm btn-primary" type="submit">Go</button>
                                    </form>
                                    <span class="text-nowrap">of {{$attendances->total() }} Data</span>
                                </div>
                            </div>

                            <!-- Showing Range -->
                            <div class="col-12 mt-3">
                                <div class="d-flex justify-content-center">
                                    <span>{{$attendances->firstItem() }} to {{$attendances->lastItem() }} of {{$attendances->total() }} Data</span>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="workLogModal" tabindex="-1" aria-labelledby="workLogModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="workLogModalLabel">Work Log</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Updated At</label>
                        <input type="text" id="worklogUpdatedAt" class="form-control" readonly>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea id="worklogDescription" class="form-control" rows="4" readonly></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

@endsection
