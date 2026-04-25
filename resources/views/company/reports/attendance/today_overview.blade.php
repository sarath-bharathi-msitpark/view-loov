@extends('company.layouts.company')

@section('page-title')
    {{ __('Today Attendance Report') }}
@endsection

@section('page-icon')
    {{ asset('assets/assestsnew/header-logo.svg') }}
@endsection

@push('css-page')

@endpush

@push('theme-script')

@endpush
@push('script-page')

@endpush

@section('content')
    @include('company.layouts.partials.nav')

    <div class="row">
        <div class="col-12">
            <div class="row align-items-center justify-content-between">
                <div class="col-md-6 d-flex align-items-center px-4 gap-2">
                    <a class="text-decoration-none" href="{{ route('organization.report.attendance')}}"><img
                            src="{{ asset('assets/assestsnew/left_arrow.svg') }}" alt=""></a>
                    <p class="text-dark mb-0 fw-medium">Attendance Reports</p>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-4">
        <form method="GET" action="{{ route('organization.report.today.attendance') }}"
              class="d-flex justify-content-between" id="attendanceForm">
            <div class="selecters_head">
                <div class="d-flex gap-3">
                    <select name="team_id" id="team-id" class="form-control select2 mb-3" onchange="this.form.submit()">
                        <option value="" {{ is_null(request('team_id')) ? 'selected' : '' }}>All Team</option>
                        @foreach($teams as $team)
                            <option value="{{ $team->id }}" {{ request('team_id') == $team->id ? 'selected' : '' }}>
                                {{ $team->name }}
                            </option>
                        @endforeach
                    </select>
                    <select name="user_id" id="user-id" class="form-control select2 mb-3" onchange="this.form.submit()">
                        <option value="" {{ is_null(request('user_id')) ? 'selected' : '' }}>All User</option>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>
                                {{ $user->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="selecters_head">
                <div class="justify-content-lg-end gap-lg-3 gap-3 d-flex">
                    <button type="submit" name="download" value="excel" class="download_arrbtn"><i
                            class="fas fa-download"></i></button>

                    <span class="px-0"><a href="{{ route('organization.report.today.attendance') }}"
                                          class="download_arrbtn"><i
                                class="fas fa-redo-alt" style="margin:12px"></i></a>Clear Filter</span>
                </div>
            </div>
        </form>
    </div>

    <div class="row mt-5 px-lg-4 p-md-3 p-1">
        <div class="col-md-12 rounded-3">
            <div class="main-content">
                <div class="attendance-table-outer">
                    <table class="attendance-table">
                        <thead>
                        <tr>
                            <th>Employee</th>
                            <th>Employee ID</th>
                            <th>Team</th>
                            <th>Attendance</th>
                            <th>Shift</th>
                            <th>Online Hours</th>
                            <th>Active Hours</th>
                            <th>Over Time</th>
                            <th>Break Time</th>
                        </tr>
                        </thead>

                        <tbody>
                        @forelse($reports as $record)
                            <tr>
                                <td class="emp-info tex_fix" style="background-color: #F7FFFF;">
                                    <div class="emp-avatar">
                                        @php
                                            $gender = $record['employee']['gender'] ?? null;
                                        @endphp

                                        @if($gender === GENDER_MALE)
                                            <img src="{{ asset('assets/assestsnew/menimg.png') }}" alt="Male">
                                        @else
                                            <img src="{{ asset('assets/assestsnew/femaile-report.svg') }}" alt="Female">
                                        @endif
                                    </div>
                                    <span class="emp-name">{{ $record['employee']->user->name ?? 'N/A' }}</span>
                                </td>
                                <td>{{ $record['employee']->employee_id ?? 'N/A' }}</td>
                                <td>{{ $record['employee']->team->name ?? 'N/A' }}</td>
                                <td>
                                    @if($record['is_present'] === 'true')
                                        <span class="badge bg-success">Present</span>
                                    @else
                                        <span class="badge bg-danger">Absent</span>
                                    @endif
                                </td>
                                <td>{{ $record['employee']->shift->shift_name ?? 'N/A' }}</td>
                                <td>{{ $record['online_hours'] }}</td>
                                <td>{{ $record['active_hours'] }}</td>
                                <td>{{ $record['overtime'] }}</td>
                                <td>{{ $record['break_hours'] }}</td>
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
                                        <li class="{{ $reports->onFirstPage() ? 'disabled' : '' }}">
                                            <a href="{{ $reports->url(1) }}" class="page-link1">&#171;</a>
                                        </li>

                                        {{-- Previous Page --}}
                                        <li class="{{ $reports->onFirstPage() ? 'disabled' : '' }}">
                                            <a href="{{ $reports->previousPageUrl() }}" class="page-link1">
                                                <i class="fa-solid fa-chevron-left"></i>
                                            </a>
                                        </li>

                                        {{-- Page Numbers --}}
                                        @php
                                            $start = max(1, $reports->currentPage() - 2);
                                            $end = min($start + 4, $reports->lastPage());
                                        @endphp
                                        @for ($i = $start; $i <= $end; $i++)
                                            <li class="{{ $reports->currentPage() == $i ? 'active_pagination' : '' }}">
                                                <a href="{{ $reports->url($i) }}" class="page-link1">{{ $i }}</a>
                                            </li>
                                        @endfor

                                        {{-- Next Page --}}
                                        <li class="{{ !$reports->hasMorePages() ? 'disabled' : '' }}">
                                            <a href="{{ $reports->nextPageUrl() }}" class="page-link1">
                                                <i class="fa-solid fa-chevron-right"></i>
                                            </a>
                                        </li>

                                        {{-- Last Page --}}
                                        <li class="{{ !$reports->hasMorePages() ? 'disabled' : '' }}">
                                            <a href="{{ $reports->url($reports->lastPage()) }}" class="page-link1">&#187;</a>
                                        </li>
                                    </ul>
                                </div>
                            </div>

                            <!-- Page Jump Input -->
                            <div class="col-12 col-lg-3 col-md-4 mb-3 mb-md-0">
                                <div
                                    class="d-flex  flex-md-row align-items-center justify-content-center justify-content-md-start gap-2">
                                    <form action="{{ url()->current() }}" method="GET"
                                          class="d-flex align-items-center gap-2"
                                          style="flex-direction:row !important;">
                                        {{-- Preserve filters --}}
                                        @foreach(request()->except('page') as $key => $value)
                                            <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                                        @endforeach

                                        <input type="number" name="page" min="1" max="{{ $reports->lastPage() }}"
                                               class="form-control form-control-sm" style="width: 80px;"
                                               placeholder="Page">
                                        <button class="btn btn-sm btn-primary" type="submit">Go</button>
                                    </form>
                                    <span class="text-nowrap small text-center text-md-start">of {{ $reports->total() }} Data</span>
                                </div>
                            </div>

                            <!-- Showing Range -->
                            <div class="col-12 mt-3">
                                <div class="d-flex justify-content-center">
                                    <span>{{ $reports->firstItem() }} to {{ $reports->lastItem() }} of {{ $reports->total() }} Data</span>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>


        </div>
    </div>
@endsection
