@extends('company.layouts.company')

@section('page-title')
    {{ __('Apps & Urls') }}
@endsection

@section('page-icon')
    {{ asset('assets/assestsnew/app&urllogo.svg') }}
@endsection

@push('css-page')
@endpush

@push('theme-script')

@endpush

@push('script-page')
    <script>
        $(function () {
            const $dateInput = $('input[name="date_range"]');
            const $form = $('#filterForm');

            // Parse server-side date range
            const serverRange = "{{ request('date_range') }}";
            let start = moment().startOf('day');
            let end = moment().startOf('day');

            if (serverRange && serverRange.includes('to')) {
                const parts = serverRange.split('to');
                start = moment(parts[0].trim(), 'YYYY-MM-DD');
                end = moment(parts[1].trim(), 'YYYY-MM-DD');
            } else if (serverRange) {
                start = moment(serverRange.trim(), 'YYYY-MM-DD');
                end = start;
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
                const val = picker.startDate.format('YYYY-MM-DD') === picker.endDate.format('YYYY-MM-DD')
                    ? picker.startDate.format('YYYY-MM-DD')
                    : picker.startDate.format('YYYY-MM-DD') + ' to ' + picker.endDate.format('YYYY-MM-DD');
                $(this).val(val);
                $form.submit();
            });

            $dateInput.on('cancel.daterangepicker', function () {
                $(this).val('');
                $form.submit();
            });
        });
    </script>
@endpush

@section('content')
    @include('company.layouts.partials.nav')
    <div class="col-12">
        <form action="{{ route('organization.apps_and_urls.index') }}" method="GET" id="filterForm">
            <!--<div class="d-flex py-3  justify-content-between align-items-center ">-->
            <div class="d-flex flex-column flex-lg-row py-3 justify-content-between align-items-center">
                <div class="selecters_head">
                    <div class="row gap-md-5 px-3 mb-3">
                        <select class="form-select select2" id="team-id" name="team_id"
                                onchange="document.getElementById('filterForm').submit();">
                            <option value="" {{ is_null(request('team_id')) ? 'selected' : '' }}>All Team</option>
                            @foreach($teams as $team)
                                <option
                                    value="{{ $team->id }}" {{ (request('team_id') == $team->id) ? 'selected' : '' }}>
                                    {{ $team->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="selecters_head">
                    <div class=" justify-content-lg-end gx-3 d-flex p-0">
                        <div class="col-auto">
                            <input type="text" name="date_range" id="date-range" class="form-control"
                                   value="{{ request('date_range') ?? \Carbon\Carbon::today()->format('Y-m-d') }}">
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <div class="row mt-4">
        <div class="col-12">
            <div class="row g-3">
                <div class="col-lg-3 col-md-6">
                    <div class="box_2">
                        <p class="fs-6 fw-normal col_P mb-0">Top Application</p>
                        <h3 class="text-dark fw-semibold fs-3 mt-2">{{ $topApplication->application_name ?? 'N/A' }}</h3>
                        <div class="inside_round mt-3">
                            <p class="mb-0">{{ gmdate('H\h:i\m', $topApplication->total_seconds ?? 0) }}</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="box_3">
                        <p class="fs-6 fw-normal col_P mb-0">Top Category</p>
                        <h3 class="text-dark fw-semibold fs-3 mt-2">{{ $topCategory->category ?? 'N/A' }}</h3>
                        <div class="inside_round1 mt-3">
                            <p class="mb-0">{{ gmdate('H\h:i\m', $topCategory->total_seconds ?? 0) }}</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 col-md-12">
                    <div class="box_4">
                        <p class="fs-6 fw-normal col_P mb-0">Top URL</p>
                        <h3 class="text-dark fw-semibold fs-3 mt-2">
                            {{ $topUrl ? (parse_url($topUrl->url, PHP_URL_HOST) ?? $topUrl->url) : 'N/A' }}
                        </h3>
                        <div class="inside_round2 mt-3">
                            <p class="mb-0">{{ gmdate('H\h:i\m', $topUrl->total_seconds ?? 0) }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-12 mt-5 mb-4">
        <div class="row g-4">
            <div class="col-xl-6 col-lg-12">
                @include('company.apps_and_urls.category_utilization')
                @include('company.apps_and_urls.application_usage')
                @include('company.apps_and_urls.url_usage')
            </div>
        </div>
    </div>
@endsection
