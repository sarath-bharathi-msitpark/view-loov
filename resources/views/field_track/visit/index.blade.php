@extends('field_track.layouts.fieldTrack')

@section('page-title')
    {{ __('Visit Management') }}
@endsection

@section('page-icon')
    {{ asset('assets/assestsnew/field_visit.svg') }}
@endsection

@section('content')
    @include('field_track.layouts.partials.nav')

    <div class="col-12 entire_box1 mb-5">
        <form action="{{ route('fieldTrack.visits.index') }}" method="GET" class="row g-3">

            <div class="col-12 col-lg-12">
                <div class="d-flex w-100 search_maincontain">
                    <input type="text" name="search" placeholder="Search Customers"
                           value="{{ request('search') }}" class="form-control">
                    <button type="submit" class="btn btn-primary ms-2 px-0">
                        <i class="fa-solid fa-magnifying-glass"></i>
                    </button>
                </div>
            </div>

            <!-- Customer -->
            <div class="col-12 col-sm-6 col-md-6 col-lg-4">
                <label for="customer" class="form-label fw-semibold">Customer</label>
                <select name="customer" id="customer" class="form-select select2">
                    <option value="">Select Customer</option>
                    @foreach($customers as $cust)
                        <option value="{{ $cust->id }}" {{ request('customer') == $cust->id ? 'selected' : '' }}>
                            {{ $cust->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Employee -->
            <div class="col-12 col-sm-6 col-md-6 col-lg-4">
                <label for="employee" class="form-label fw-semibold">Employee</label>
                <select name="employee" id="employee" class="form-select select2">
                    <option value="">Select Employee</option>
                    @foreach($employees as $emp)
                        <option value="{{ $emp->id }}" {{ request('employee') == $emp->id ? 'selected' : '' }}>
                            {{ $emp->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Date Range -->
            <div class="col-12 col-sm-6 col-md-6 col-lg-4">
                <label for="date_range" class="form-label fw-semibold">Date Range</label>
                <input type="text" name="date_range" id="date_range"
                       value="{{ request('date_range') }}"
                       class="form-control" style="border-radius:100px;" autocomplete="off"/>
            </div>

            <!-- Filter Button -->
            <div class="col-12 col-sm-6 col-md-6 col-lg-4 d-flex gap-2 align-items-end">
                <button type="submit"
                        class="btn btn-primary d-flex align-items-center justify-content-center gap-2 px-4">
                    <i class="fas fa-filter"></i> Filter
                </button>
                <a href="{{ route('fieldTrack.visits.index') }}"
                   class="btn btn-secondary d-flex align-items-center justify-content-center gap-2 px-4"><i
                        class="fa-solid fa-rotate-right"></i> Clear
                </a>
            </div>
        </form>
    </div>

    <div class="row mt-5">
        <div class="col-md-12 rounded-3">
            <div class="attendance-table-outer1">
                <table class="attendance-table">
                    <thead>
                    <tr>
                        <th>S.No</th>
                        <th>Customer</th>
                        <th>Employee</th>
                        <th>Next Visit</th>
                        <th>Last Visit</th>
                        <th>Last Visit Proof</th>
                    </tr>
                    </thead>
                    <tbody>
                    @php $i = ($visits->currentPage() - 1) * $visits->perPage() + 1; @endphp
                    @forelse($visits as $visit)
                        <tr>
                            <td class="tex_fix">
                                <div class="d-flex align-items-center">
                                    <span>{{ $i++ }}</span>
                                </div>
                            </td>
                            <td class="tex_fix">
                                {{ $visit->customer?->name ?? 'N/A' }} <br>
                                {{ $visit->customer?->email ?? 'N/A' }} <br>
                                {{ $visit->customer?->contact ?? 'N/A' }}
                            </td>
                            <td>
                                {{ $visit->employee?->name ?? 'N/A' }} <br>
                                {{ $visit->employee?->email ?? 'N/A' }}
                            </td>
                            <td>{{ $visit->visit_date_formatted }} {{ $visit->visit_time_formatted }}</td>
                            <td>{{ $visit->created_at->format('d-m-Y H:i') }}</td>
                            <td>
                                @if($visit->image)
                                    <a href="{{ $visit->image }}" target="_blank">
                                        <i class="fa-solid fa-eye"></i>
                                    </a>
                                @else
                                    N/A
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center">
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
                            <li class="{{$visits->onFirstPage() ? 'disabled' : '' }}">
                                <a href="{{$visits->url(1) }}" class="page-link1">&#171;</a>
                            </li>

                            {{-- Previous Page --}}
                            <li class="{{$visits->onFirstPage() ? 'disabled' : '' }}">
                                <a href="{{$visits->previousPageUrl() }}" class="page-link1">
                                    <i class="fa-solid fa-chevron-left"></i>
                                </a>
                            </li>

                            {{-- Page Numbers --}}
                            @php
                                $start = max(1,$visits->currentPage() - 2);
                                $end = min($start + 4,$visits->lastPage());
                            @endphp
                            @for ($i = $start; $i <= $end; $i++)
                                <li class="{{$visits->currentPage() == $i ? 'active_pagination' : '' }}">
                                    <a href="{{$visits->url($i) }}"
                                       class="page-link1">{{ $i }}</a>
                                </li>
                            @endfor

                            {{-- Next Page --}}
                            <li class="{{ !$visits->hasMorePages() ? 'disabled' : '' }}">
                                <a href="{{$visits->nextPageUrl() }}" class="page-link1">
                                    <i class="fa-solid fa-chevron-right"></i>
                                </a>
                            </li>

                            {{-- Last Page --}}
                            <li class="{{ !$visits->hasMorePages() ? 'disabled' : '' }}">
                                <a href="{{$visits->url($visits->lastPage()) }}"
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
                                   max="{{$visits->lastPage() }}"
                                   class="form-control form-control-sm" style="width: 80px;"
                                   placeholder="Page">
                            <button class="btn btn-sm btn-primary" type="submit">Go</button>
                        </form>
                        <span
                            class="text-nowrap small text-center text-md-start">of {{$visits->total() }} Data</span>
                    </div>
                </div>

                <!-- Showing Range -->
                <div class="col-12 mt-3">
                    <div class="d-flex justify-content-center">
                        <span>{{$visits->firstItem() }} to {{$visits->lastItem() }} of {{$visits->total() }} Data</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>
@endsection

@push('script-page')
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const deleteForms = document.querySelectorAll('.delete-form');

            deleteForms.forEach(form => {
                form.addEventListener('submit', function (e) {
                    e.preventDefault();

                    Swal.fire({
                        title: 'Are you sure?',
                        text: "This action cannot be undone!",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#d33',
                        cancelButtonColor: '#6c757d',
                        confirmButtonText: 'Yes, delete it!',
                        cancelButtonText: 'Cancel'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            form.submit();
                        }
                    });
                });
            });
        });
    </script>

    <script>
        $(function () {
            const $dateInput = $('input[name="date_range"]');

            let serverRange = @json(request('date_range'));

            let start = moment().startOf('month');
            let end = moment().endOf('month');

            if (serverRange) {
                if (serverRange.includes(' to ')) {
                    const parts = serverRange.split(' to ');
                    start = moment(parts[0].trim(), 'YYYY-MM-DD');
                    end = moment(parts[1].trim(), 'YYYY-MM-DD');
                } else if (serverRange.includes(' - ')) {
                    const parts = serverRange.split(' - ');
                    start = moment(parts[0].trim(), 'YYYY-MM-DD');
                    end = moment(parts[1].trim(), 'YYYY-MM-DD');
                } else if (serverRange.split('-').length === 3) {
                    start = end = moment(serverRange.trim(), 'YYYY-MM-DD');
                }
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
                $(this).val(
                    picker.startDate.format('YYYY-MM-DD') === picker.endDate.format('YYYY-MM-DD')
                        ? picker.startDate.format('YYYY-MM-DD')
                        : picker.startDate.format('YYYY-MM-DD') + ' to ' + picker.endDate.format('YYYY-MM-DD')
                );
            });

            $dateInput.on('cancel.daterangepicker', function () {
                $(this).val('');
            });
        });
    </script>
@endpush

