@extends('company.layouts.company')

@section('page-title')
    {{ __('Screenshots') }}
@endsection

@section('page-icon')
    {{ asset('assets/assestsnew/screenshot.svg') }}
@endsection

@push('script-page')
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const progressBars = document.querySelectorAll('.myProgressBar');

            progressBars.forEach(function (progressBar) {
                const value = parseInt(progressBar.getAttribute('aria-valuenow'));

                // Remove any previous Bootstrap background classes
                progressBar.classList.remove('bg-danger', 'bg-warning', 'bg-success');

                // Set background color based on value
                if (value < 40) {
                    progressBar.style.backgroundColor = '#D10808'; // Red
                } else if (value >= 40 && value < 80) {
                    progressBar.style.backgroundColor = '#ffc107'; // Yellow
                } else {
                    progressBar.style.backgroundColor = '#198754'; // Green
                }
            });
        });
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            document.querySelectorAll('.toggle-highlight-btn').forEach(button => {
                button.addEventListener('click', function () {
                    const url = this.dataset.url;
                    const currentHighlight = this.dataset.highlight;
                    const newHighlight = currentHighlight == 1 ? 0 : 1;
                    const buttonEl = this;

                    fetch(url, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({is_highlight: newHighlight})
                    })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                buttonEl.dataset.highlight = newHighlight;
                                const icon = buttonEl.querySelector('i');
                                icon.className = newHighlight == 1 ? 'fa-solid fa-star' : 'fa-regular fa-star';
                            } else {
                                alert('Failed to update highlight status');
                            }
                        })
                        .catch(err => {
                            console.error(err);
                            alert('Error updating highlight.');
                        });
                });
            });
        });
    </script>

    <script>
        $(function () {
            const $dateInput = $('input[name="date_range"]');
            const $form = $dateInput.closest('form');

            const serverDate = "{{ request('date_range') }}";
            let start = serverDate ? moment(serverDate.trim(), 'YYYY-MM-DD') : moment();

            const picker = $dateInput.daterangepicker({
                autoUpdateInput: false,
                autoApply: false,
                singleDatePicker: true,
                showDropdowns: false,
                startDate: start,
                maxDate: moment(),
                locale: {
                    format: 'YYYY-MM-DD',
                    applyLabel: 'Apply',
                    cancelLabel: 'Clear'
                },
                ranges: {
                    'Today': [moment(), moment()],
                    'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')]
                }
            });

            $dateInput.on('show.daterangepicker', function (ev, drp) {
                drp.container.find('.ranges li').off('click').on('click', function () {
                    const label = $(this).text();
                    if (label === 'Yesterday') {
                        const range = drp.ranges[label];
                        $dateInput.val(range[0].format('YYYY-MM-DD'));
                        $form.submit();
                        drp.hide();
                    }
                });
            });

            $dateInput.on('apply.daterangepicker', function (ev, picker) {
                const selectedDate = picker.startDate.clone().startOf('day');
                const yesterday = moment().subtract(1, 'days').startOf('day');

                if (selectedDate.isSame(yesterday)) {
                    $(this).val(selectedDate.format('YYYY-MM-DD'));
                    $form.submit();
                } else {
                    $(this).val(picker.startDate.format('YYYY-MM-DD'));
                    $form.submit();
                }
            });

            $dateInput.on('cancel.daterangepicker', function () {
                $(this).val('');
                $form.submit();
            });

            $('#team-id').on('change', function () {
                $form.submit();
            });
        });
    </script>
@endpush

@section('content')
    @include('company.layouts.partials.nav')

    <div class="col-12">
        <div class="row">
            <!-- Filters and Controls -->
            <form method="GET" action="{{ route('organization.screenshot.index') }}"
                  class="d-flex justify-content-between gap-3 flex-wrap" id="filterForm">
                <input type="hidden" name="employee_id" value="{{ request('employee_id') }}">
                <!-- Team Dropdown -->
                <div class="selecters_head">
                    <div class="row gap-md-5 px-3">
                        <select class="form-select select2" name="team_id" id="team-id">
                            <option value="" {{ !request('team_id') ? 'selected' : '' }}>All Team</option>
                            @foreach($teams as $team)
                                <option value="{{ $team->id }}" {{ request('team_id') == $team->id ? 'selected' : '' }}>
                                    {{ $team->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- Date Range Picker -->
                <div class="col-lg-6 selecters_head">
                    <div class="row justify-content-lg-end gap-3 px-3">
                        <input type="text" name="date_range" id="date-range" class="form-control"
                               value="{{ request('date_range') ?? now()->format('Y-m-d') }}">
                        <span>
                            <a href="{{ route('organization.screenshot.index') }}" class="download_arrbtn">
                                <i class="fas fa-redo-alt" style="margin:12px" aria-hidden="true"></i>
                            </a> Clear Filter
                        </span>
                    </div>
                </div>
            </form>

            <!-- Main Content -->
            <div class="col-12 mt-5">
                <div class="row p-3">
                    <div class="col-12 main_pagebox">
                        <div class="row p-3">
                            <!-- Employee List -->
                            <div class="col-xl-4 col-lg-12 col-md-12 borderscorll my-2">
                                <div class="row">
                                    <div class="col-12 bg_graytops">
                                        <div class="d-flex justify-content-between p-2">
                                            <span>User</span>
                                            <span>Total: {{ $employeesCount }}</span>
                                        </div>
                                    </div>
                                    <div class="col-12 heigh_scrolldash">
                                        <div class="row">
                                            @forelse($employees as $employee)
                                                @php
                                                    $gender = $employee['gender'] ?? null;
                                                @endphp

                                                <div
                                                    class="col-12 profile_showscroll self_hov {{ $selectedEmployeeId == $employee->id ? 'bg-primary text-white' : '' }}">
                                                    <a href="{{ route('organization.screenshot.index', [
                                                        'employee_id' => $employee->id,
                                                        'date' => request('date'),
                                                        'team_id' => request('team_id')
                                                    ]) }}"
                                                       class="d-flex align-items-center p-2 text-decoration-none text-reset">

                                                        @if($gender === GENDER_MALE)
                                                            <img src="{{ asset('assets/assestsnew/menimg.png') }}"
                                                                 alt="Male">
                                                        @elseif($gender === GENDER_FEMALE)
                                                            <img
                                                                src="{{ asset('assets/assestsnew/femaile-report.svg') }}"
                                                                alt="Female">
                                                        @else
                                                            <img src="{{ asset('assets/assestsnew/profile.png') }}"
                                                                 alt="Default">
                                                        @endif

                                                        <small class="ms-2">{{ $employee->name }}</small>
                                                    </a>
                                                </div>
                                            @empty
                                                <div class="col-12 profile_showscroll self_hov">No Employees Found</div>
                                            @endforelse
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Screenshots -->
                            <div class="col-xl-8 col-lg-12 col-md-12 my-2">
                                <div class="row">
                                    <div class="col-12 heightdash_scroll">
                                        <div class="row justify-content-center">
                                            @if($incidents->isEmpty())
                                                <div class="col-12 text-center">
                                                    <p>No screenshots found for this user.</p>
                                                </div>
                                            @else
                                                @php
                                                    $grouped = $incidents->groupBy(function($item) {
                                                        return \Carbon\Carbon::parse($item->capture_date_and_time)->format('h A');
                                                    });
                                                @endphp

                                                @foreach($grouped as $hour => $hourGroup)
                                                    <div class="row mb-4">
                                                        <div class="col-md-3">
                                                            <div
                                                                class="row align-items-center justify-content-end blue_spantimes">
                                                                <span>{{ $hour }}</span><i class="fas fa-circle"></i>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-9">
                                                            <div class="row">
                                                                @foreach($hourGroup as $incident)
                                                                    <div class="col-md-6 mb-3"
                                                                         style="position: relative;">
                                                                        <div class="row px-2">
                                                                            <div class="col-12 blue_bg_spans"
                                                                                 data-bs-toggle="modal"
                                                                                 data-bs-target="#screenPopup{{ $incident->id }}"
                                                                                 style="background-image: url({{ \App\Models\Utility::get_file($incident->screenshot) }});">

                                                                                <div class="row">
                                                                                    <span>{{ \Carbon\Carbon::parse($incident->capture_date_and_time)->format('h:i A') }}</span>

                                                                                    <div
                                                                                        class="progress px-0 progress_abshighlight mt-3">
                                                                                        <div
                                                                                            class="progress-bar myProgressBar"
                                                                                            role="progressbar"
                                                                                            style="width:  {{ $incident->action_percentage }}%"
                                                                                            ;
                                                                                            aria-valuenow=" {{ $incident->action_percentage }}"
                                                                                            aria-valuemin="0"
                                                                                            aria-valuemax="100">
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                            <div
                                                                                class="d-flex flex-column posiabs_highlight">

                                                                                <!-- download Button -->
                                                                                <a href="{{ \App\Models\Utility::get_file($incident->screenshot) }}"
                                                                                   class="download_arrbtn mb-2"
                                                                                   download="{{ basename($incident->screenshot) }}"
                                                                                   target="_blank">
                                                                                    <i class="fa-solid fa-download"
                                                                                       style="margin: 12px;"></i>
                                                                                </a>

                                                                                <!-- Star Button -->
                                                                                <button
                                                                                    class="download_arrbtn toggle-highlight-btn"
                                                                                    data-id="{{ $incident->id }}"
                                                                                    data-highlight="{{ $incident->is_highlight }}"
                                                                                    data-url="{{ route('organization.screenshot.toggle-highlight', $incident->id) }}">
                                                                                    <i class="fa{{ $incident->is_highlight ? '-solid' : '-regular' }} fa-star"></i>
                                                                                </button>

                                                                            </div>
                                                                        </div>
                                                                    </div>

                                                                    <!-- Include modal for this incident -->
                                                                    @include('company.screenshot.model', ['incident' => $incident])
                                                                @endforeach
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div> <!-- End Screenshot Column -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
