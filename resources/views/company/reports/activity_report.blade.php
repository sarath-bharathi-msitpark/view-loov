@extends('company.layouts.company')

@section('page-title')
    {{ __('Activity Report') }}
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
        const activityForm = document.getElementById('activityForm');
        const input = $('input[name="date"]');
        let initialDate = input.val();

        // Default to last 7 days
        let start = moment().subtract(6, 'days');
        let end = moment();

        // If user-selected date exists, parse it
        if (initialDate.includes(' to ')) {
            let dates = initialDate.split(' to ');
            start = moment(dates[0], 'YYYY-MM-DD');
            end = moment(dates[1], 'YYYY-MM-DD');
        } else if (initialDate !== '') {
            start = end = moment(initialDate, 'YYYY-MM-DD');
        }

        input.daterangepicker({
            autoUpdateInput: true,
            locale: {
                cancelLabel: 'Clear',
                format: 'YYYY-MM-DD'
            },
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
        }, function (start, end) {
            let formattedDate = start.format('YYYY-MM-DD');
            if (start.format('YYYY-MM-DD') !== end.format('YYYY-MM-DD')) {
                formattedDate += ' to ' + end.format('YYYY-MM-DD');
            }
            input.val(formattedDate);
            activityForm.submit();
        });

        input.on('cancel.daterangepicker', function () {
            $(this).val('');
            activityForm.submit();
        });
    </script>
@endpush

@section('content')
    @include('company.layouts.partials.nav')

    <div class="row">
        <div class="col-12">
            <div class="row align-items-center justify-content-between">
                <div class="col-md-6 d-flex align-items-center px-4 gap-2">
                    <a class="text-decoration-none" href="{{ route('organization.report.index')}}"><img
                            src="{{ asset('assets/assestsnew/left_arrow.svg') }}" alt=""></a>
                    <p class="text-dark mb-0 fw-medium">Activity Report</p>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <form method="GET" action="{{ route('organization.report.activity') }}"
              class="d-flex mt-4 justify-content-between " id="activityForm">

            <div class="selecters_head">
                <div class="d-flex report_form gap-3">
                    <select name="team_id" id="team_id" class="form-control select2 mb-3" onchange="this.form.submit()">
                        <option value="">All Teams</option> {{-- was selected+disabled, now selectable --}}
                        @foreach($teams as $team)
                            <option value="{{ $team->id }}" {{ request('team_id') == $team->id ? 'selected' : '' }}>
                                {{ $team->name }}
                            </option>
                        @endforeach
                    </select>

                    <select name="user_id" id="user_id" class="form-control select2 mb-3" onchange="this.form.submit()">
                        <option value="">All Users</option> {{-- was selected+disabled, now selectable --}}
                        @foreach($users as $user)
                            <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>
                                {{ $user->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="selecters_head">
                <div class="d-flex justify-content-lg-end gap-lg-3 gap-3">
                    <input type="text" name="date" class="form-control"
                           value="{{ request('date', now()->toDateString()) }}" autocomplete="off">

                    <button type="submit" name="download" value="excel" class="download_arrbtn">
                        <i class="fas fa-download"></i>
                    </button>
                    <span class="px-0">
                        <a href="{{ route('organization.report.activity') }}" class="download_arrbtn">
                            <i class="fas fa-redo-alt" style="margin:12px"></i>
                        </a>Clear Filter
                    </span>
                </div>
            </div>
        </form>
    </div>

    <div class="row mt-5 px-lg-4 mb-3">
        <div class="col-md-12 rounded-3 p-0">
            <div class="main-content">
                <div class="attendance-table-outer">
                    <table class="attendance-table">
                        <thead>
                        <tr>
                            <th>Employees</th>
                            <th>Employee ID</th>
                            <th>Date</th>
                            <th>Key Presses</th>
                            <th>Mouse Clicks</th>
                            <th>Idle Time Out Count</th>
                            <th>Progress</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tbody>
                        @forelse($incidents as $incident)
                            <tr>
                                <td class="emp-info tex_fix">
                                    <div class="emp-avatar">
                                        @php
                                            $gender = $incident->user['employee']['gender'] ?? null;
                                        @endphp

                                        @if($gender === GENDER_MALE)
                                            <img src="{{ asset('assets/assestsnew/menimg.png') }}" alt="Male">
                                        @else
                                            <img src="{{ asset('assets/assestsnew/femaile-report.svg') }}" alt="Female">
                                        @endif
                                    </div>
                                    <span class="emp-name">{{ $incident->user->name ?? 'N/A' }}</span>
                                </td>
                                {{--                                <td>{{ $incident->user->name ?? 'N/A' }}</td>--}}
                                <td>{{ $incident->user->employee->employee_id ?? 'N/A' }}</td>
                                <td class="text-center">{{ \Carbon\Carbon::parse($incident->activity_date)->format('Y-m-d') }}</td>
                                <td class="text-center">{{ $incident->total_keyboard_actions }}</td>
                                <td class="text-center">{{ $incident->total_mouse_actions }}</td>
                                <td class="text-center">{{ $incident->idle_time_out_count }}</td>
                                <td class="text-center">
                                    <div class="d-flex align-items-center">
                                        <div class="flex-grow-1 me-2">
                                            <div class="progress" style="height: 8px;">
                                                <div class="progress-bar bg-primary"
                                                     style="width: {{ $incident->activity_percentage }}%"></div>
                                            </div>
                                        </div>
                                        <small
                                            class="fw-semibold">{{ $incident->activity_percentage }}
                                            %</small>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7">
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
                                    <li class="{{$incidents->onFirstPage() ? 'disabled' : '' }}">
                                        <a href="{{$incidents->url(1) }}" class="page-link1">&#171;</a>
                                    </li>

                                    {{-- Previous Page --}}
                                    <li class="{{$incidents->onFirstPage() ? 'disabled' : '' }}">
                                        <a href="{{$incidents->previousPageUrl() }}" class="page-link1">
                                            <i class="fa-solid fa-chevron-left"></i>
                                        </a>
                                    </li>

                                    {{-- Page Numbers --}}
                                    @php
                                        $start = max(1,$incidents->currentPage() - 2);
                                        $end = min($start + 4,$incidents->lastPage());
                                    @endphp
                                    @for ($i = $start; $i <= $end; $i++)
                                        <li class="{{$incidents->currentPage() == $i ? 'active_pagination' : '' }}">
                                            <a href="{{$incidents->url($i) }}"
                                               class="page-link1">{{ $i }}</a>
                                        </li>
                                    @endfor

                                    {{-- Next Page --}}
                                    <li class="{{ !$incidents->hasMorePages() ? 'disabled' : '' }}">
                                        <a href="{{$incidents->nextPageUrl() }}" class="page-link1">
                                            <i class="fa-solid fa-chevron-right"></i>
                                        </a>
                                    </li>

                                    {{-- Last Page --}}
                                    <li class="{{ !$incidents->hasMorePages() ? 'disabled' : '' }}">
                                        <a href="{{$incidents->url($incidents->lastPage()) }}"
                                           class="page-link1">&#187;</a>
                                    </li>
                                </ul>
                            </div>
                        </div>

                        <!-- Page Jump Input -->
                        <div class="col-12 col-lg-3 col-md-4 mb-3 mb-md-0">
                            <div
                                class="d-flex flex-column flex-md-row align-items-center justify-content-center justify-content-md-start gap-2">
                                <form action="{{ url()->current() }}" method="GET"
                                      class="d-flex align-items-center gap-2" style="flex-direction:row !important;">
                                    {{-- Preserve filters --}}
                                    @foreach(request()->except('page') as $key => $value)
                                        <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                                    @endforeach

                                    <input type="number" name="page" min="1"
                                           max="{{$incidents->lastPage() }}"
                                           class="form-control form-control-sm" style="width: 80px;"
                                           placeholder="Page">
                                    <button class="btn btn-sm btn-primary" type="submit">Go</button>
                                </form>
                                <span class="text-nowrap small text-center text-md-start">of {{$incidents->total() }} Data</span>
                            </div>
                        </div>

                        <!-- Showing Range -->
                        <div class="col-12 mt-3">
                            <div class="d-flex justify-content-center">
                                <span>{{$incidents->firstItem() }} to {{$incidents->lastItem() }} of {{$incidents->total() }} Data</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
