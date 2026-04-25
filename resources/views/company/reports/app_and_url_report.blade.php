@extends('company.layouts.company')

@section('page-title')
    {{ __('Apps And Url') }}
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
            const initialDate = $dateInput.val();

            let start = moment().subtract(6, 'days');
            let end = moment();

            // Parse initial range if present
            if (initialDate.includes(' to ')) {
                const parts = initialDate.split(' to ');
                start = moment(parts[0], 'YYYY-MM-DD');
                end = moment(parts[1], 'YYYY-MM-DD');
            } else if (initialDate) {
                start = end = moment(initialDate, 'YYYY-MM-DD');
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
                if (picker.startDate.format('YYYY-MM-DD') === picker.endDate.format('YYYY-MM-DD')) {
                    $(this).val(picker.startDate.format('YYYY-MM-DD'));
                } else {
                    $(this).val(picker.startDate.format('YYYY-MM-DD') + ' to ' + picker.endDate.format('YYYY-MM-DD'));
                }
                $('#appurlForm').submit();
            });

            $dateInput.on('cancel.daterangepicker', function () {
                $(this).val('');
                $('#appurlForm').submit();
            });
        });
    </script>
@endpush

@section('content')
    @include('company.layouts.partials.nav')

    <div class="row">
        <div class="col-6">
            <div class="row align-items-center justify-content-between">
                <div class="col-md-6 d-flex align-items-center px-4 gap-2">
                    <a class="text-decoration-none" href="{{ route('organization.report.index') }}"><img
                            src="{{ asset('assets/assestsnew/left_arrow.svg') }}" alt=""></a>
                    <p class="text-dark mb-0 fw-medium">Apps/URLs Report</p>
                </div>
            </div>
        </div>
    </div>
    <!--<div class="col-md-6 d-flex justify-content-end">-->
    <!--</div>-->

    <div class="row mt-4">
        <form method="GET" action="{{ route('organization.report.apps_and_urls') }}"
              class="d-flex justify-content-between" id="appurlForm">
            <div class="selecters_head">
                <div class="d-flex gap-3 report_form">
                    <select class="form-select select2" id="team-id" name="team_id" onchange="this.form.submit()">
                        <option value="">All Teams</option>
                        @foreach ($teams as $team)
                            <option value="{{ $team->id }}" {{ request('team_id') == $team->id ? 'selected' : '' }}>
                                {{ $team->name }}
                            </option>
                        @endforeach
                    </select>

                    <select class="form-select select2" id="user-id" name="user_id" onchange="this.form.submit()">
                        <option value="">All Users</option>
                        @foreach ($users as $user)
                            <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>
                                {{ $user->name }}
                            </option>
                        @endforeach
                    </select>

                    <div class="d-flex align-items-center gap-2 mb-3">
                        <input type="checkbox" name="type[]" value="app" id="apps_checkbox"
                               {{ in_array('app', (array) request('type')) ? 'checked' : '' }}
                               onchange="this.form.submit()">
                        <label class="mb-0" for="apps_checkbox">Apps</label>
                    </div>
                    <div class="d-flex align-items-center gap-2 mb-3">
                        <input type="checkbox" name="type[]" value="web" id="urls_checkbox"
                               {{ in_array('web', (array) request('type')) ? 'checked' : '' }}
                               onchange="this.form.submit()">
                        <label class="mb-0" for="urls_checkbox">URLs</label>
                    </div>
                </div>
            </div>

            <div class="selecters_head ">
                <div class="d-flex report_form justify-content-lg-end gap-lg-3 gap-3">
                    <input type="text" name="date" class="form-control"
                           value="{{ request('date', now()->toDateString()) }}" autocomplete="off"
                           placeholder="Select date or range">
                    <button type="submit" name="download" value="excel" class="download_arrbtn">
                        <i class="fas fa-download"></i>
                    </button>
                    <a href="{{ route('organization.report.apps_and_urls') }}" class="download_arrbtn">
                        <i class="fas fa-redo-alt" style="margin:12px"></i>
                    </a>
                </div>
            </div>
        </form>
    </div>

    <div class="row mt-5 mb-4">
        <div class="col-md-12 rounded-3">
            <div class="main-content">
                <div class="attendance-table-outer">
                    <table class="attendance-table">
                        <thead>
                        <tr>
                            <th>Employee</th>
                            <th>Employee ID</th>
                            <th>Team</th>
                            <th>Date</th>
                            <th>Application / URL</th>
                            <th>Usage Duration</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse($applicationLogs as $log)
                            @php
                                $employee = $log->user->employee ?? null;
                                $type = (Str::contains($log->application_name, '.') || filter_var($log->application_name, FILTER_VALIDATE_URL)) ? 'web' : 'app';
                            @endphp
                            <tr>
                                <td class="emp-info" style="background-color: #F7FFFF;">
                                    <div class="emp-avatar">
                                        <img src="{{ asset('assets/assestsnew/menimg.png') }}" alt="">
                                    </div>
                                    <span class="emp-name">{{ $log->user->name ?? 'N/A' }}</span>
                                </td>
                                <td>{{ $employee->employee_id ?? '-' }}</td>
                                <td>{{ $employee->team->name ?? '-' }}</td>
                                <td>{{ $employee->team->name ?? '-' }}</td>
                                <td>
                                        <span>
                                            <img
                                                src="{{ asset('assets/assestsnew/' . ($type === 'web' ? 'internet.svg' : 'computer.svg')) }}"
                                                alt="">
                                            {{ \Illuminate\Support\Str::limit($log->application_name, 50) }}
                                        </span>
                                </td>
                                <td>{{ gmdate('H:i:s', $log->total_screen_time ?? 0) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center">
                                    <img class="w-25" src="{{ asset('assets/assestsnew/no_datasvg.svg') }}"
                                         alt="No data found">
                                </td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="row justify-content-center">
                <div class="col-12">
                    <div class="row justify-content-end">
                        <div class="col-md-4">

                        </div>
                        @if ($applicationLogs->count() > 0)
                            <div class="col-md-4">
                                <nav aria-label="Page navigation">
                                    <ul class="pagination justify-content-center">
                                        {{-- Previous Page Link --}}
                                        @if ($applicationLogs->onFirstPage())
                                            <li class="page-item disabled">
                                                <span class="page-link1" style="pointer-events: none;">&lt;</span>
                                            </li>
                                        @else
                                            <li class="page-item">

                                                <a class="page-link1"
                                                   href="{{ $applicationLogs->appends(request()->query())->previousPageUrl() }}"
                                                   rel="prev">&lt;</a>
                                            </li>
                                        @endif

                                        {{-- Active Page Number --}}
                                        <li class="page-item active">
                                            <span class="page-link">{{ $applicationLogs->currentPage() }}</span>
                                        </li>

                                        {{-- Next Page Link --}}
                                        @if ($applicationLogs->hasMorePages())
                                            <li class="page-item">

                                                <a class="page-link1"
                                                   href="{{ $applicationLogs->appends(request()->query())->nextPageUrl() }}"
                                                   rel="next">&gt;</a>

                                            </li>
                                        @else
                                            <li class="page-item disabled">
                                                <span class="page-link1" style="pointer-events: none;">&gt;</span>
                                            </li>
                                        @endif
                                    </ul>
                                </nav>
                            </div>
                        @endif

                        <div class="col-md-4 ">

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection
