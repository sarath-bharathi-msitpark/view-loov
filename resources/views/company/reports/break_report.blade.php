@extends('company.layouts.company')

@section('page-title')
    {{ __('Break') }}
@endsection

@section('page-icon')
    {{ asset('assets/assestsnew/header-logo.svg') }}
@endsection

@push('css-page')
<style>
    /* ─── Design Tokens ─────────────────────────────────────────── */
    :root {
        --t-bg:            #f0f4f9;
        --t-surface:       #ffffff;
        --t-surface-2:     #f7f9fc;
        --t-border:        #e4eaf2;
        --t-border-light:  #eef2f8;
        --t-accent:        #2563eb;
        --t-accent-soft:   #dbeafe;
        --t-accent-dim:    #eff6ff;
        --t-teal:          #0d9488;
        --t-teal-soft:     #ccfbf1;
        --t-amber:         #d97706;
        --t-amber-soft:    #fef3c7;
        --t-green:         #16a34a;
        --t-green-soft:    #dcfce7;
        --t-red:           #dc2626;
        --t-red-soft:      #fee2e2;
        --t-text-primary:  #0f172a;
        --t-text-secondary:#334155;
        --t-text-muted:    #64748b;
        --t-text-light:    #94a3b8;
        --t-shadow-xs:     0 1px 3px rgba(15,23,42,.06), 0 1px 2px rgba(15,23,42,.04);
        --t-shadow-sm:     0 4px 12px rgba(15,23,42,.07), 0 1px 4px rgba(15,23,42,.05);
        --t-shadow-md:     0 8px 24px rgba(15,23,42,.09), 0 2px 8px rgba(15,23,42,.06);
        --t-radius:        14px;
        --t-radius-sm:     8px;
        --t-radius-xs:     5px;
        --t-radius-pill:   999px;
    }

    /* ─── Page shell ────────────────────────────────────────────── */
    .aul-page {
        background: var(--t-bg);
        min-height: 100vh;
        padding-bottom: 3rem;
    }

    /* ─── Filter bar ────────────────────────────────────────────── */
    .aul-filter-bar {
        background: var(--t-surface);
        border: 1px solid var(--t-border);
        border-radius: var(--t-radius);
        padding: .9rem 1.25rem;
        box-shadow: var(--t-shadow-xs);
        display: flex;
        flex-wrap: wrap;
        gap: .65rem;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 1.5rem;
    }

    .aul-filter-left,
    .aul-filter-right {
        display: flex;
        align-items: center;
        gap: .65rem;
    }

    /* shared input/select look */
    .aul-ctrl {
        height: 36px;
        border: 1.5px solid var(--t-border);
        border-radius: var(--t-radius-sm);
        background: var(--t-surface-2);
        color: var(--t-text-primary);
        font-size: .83rem;
        font-weight: 500;
        padding: 0 .8rem;
        transition: border-color .15s, box-shadow .15s;
        outline: none;
    }
    .aul-ctrl:focus {
        border-color: var(--t-accent);
        box-shadow: 0 0 0 3px rgba(37,99,235,.1);
        background: #fff;
    }
    select.aul-ctrl { min-width: 140px; }
    input[type="text"].aul-ctrl { min-width: 195px; cursor: pointer; }

    /* action buttons */
    .aul-btn {
        height: 36px;
        min-width: 36px;
        border: 1.5px solid var(--t-border);
        border-radius: var(--t-radius-sm);
        background: var(--t-surface-2);
        color: var(--t-text-muted);
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: .4rem;
        font-size: .8rem;
        font-weight: 600;
        padding: 0 .8rem;
        cursor: pointer;
        text-decoration: none;
        transition: border-color .14s, background .14s, color .14s, box-shadow .14s;
    }
    .aul-btn:hover {
        border-color: var(--t-accent);
        background: var(--t-accent-dim);
        color: var(--t-accent);
        box-shadow: 0 0 0 3px rgba(37,99,235,.08);
    }
    .aul-btn.primary {
        background: var(--t-accent);
        border-color: var(--t-accent);
        color: #fff;
    }
    .aul-btn.primary:hover {
        background: #1d4ed8;
        border-color: #1d4ed8;
        color: #fff;
        box-shadow: 0 0 0 3px rgba(37,99,235,.18);
    }

    /* divider between left & right */
    .aul-filter-sep {
        width: 1px;
        height: 20px;
        background: var(--t-border);
        flex-shrink: 0;
    }

    /* ─── Table panel ───────────────────────────────────────────── */
    .aul-panel {
        background: var(--t-surface);
        border: 1px solid var(--t-border);
        border-radius: var(--t-radius);
        box-shadow: var(--t-shadow-xs);
        overflow: hidden;
    }

    .aul-panel-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: .9rem 1.35rem;
        border-bottom: 1px solid var(--t-border);
        flex-wrap: wrap;
        gap: .5rem;
    }
    .aul-panel-title {
        display: flex;
        align-items: center;
        gap: .6rem;
    }
    .aul-panel-icon {
        width: 40px; height: 40px;
        border-radius: var(--t-radius-sm);
        background: var(--t-accent-soft);
        color: var(--t-accent);
        display: flex; align-items: center; justify-content: center;
        flex-shrink: 0;
    }
    .aul-panel-title h6 {
        font-size: large;
        font-weight: 600;
        color: var(--t-text-primary);
        margin: 0;
        letter-spacing: -.01em;
    }
    .aul-panel-title .sub {
        color: var(--t-text-light);
        margin: 0;
    }
    .aul-count-badge {
        font-size: .7rem;
        font-weight: 700;
        background: var(--t-accent-soft);
        color: var(--t-accent);
        padding: .18rem .6rem;
        border-radius: var(--t-radius-pill);
        letter-spacing: .03em;
    }

    /* ─── Table ─────────────────────────────────────────────────── */
    .aul-table-wrap {
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
    }
    .aul-table {
        width: 100%;
        border-collapse: collapse;
    }
    .aul-table thead tr {
        background: var(--t-surface-2);
        border-bottom: 1.5px solid var(--t-border);
    }
    .aul-table thead th {
        padding: .7rem 1rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: .07em;
        color: var(--t-text-light);
        white-space: nowrap;
        text-align: left;
    }
    .aul-table thead th:first-child { padding-left: 1.35rem; }
    .aul-table thead th:last-child  { padding-right: 1.35rem; text-align: right; }

    .aul-table tbody tr {
        border-bottom: 1px solid var(--t-border-light);
        transition: background .12s;
    }
    .aul-table tbody tr:last-child { border-bottom: none; }
    .aul-table tbody tr:hover { background: var(--t-accent-dim); }

    .aul-table td {
        padding: .78rem 1rem;
        color: var(--t-text-secondary);
        vertical-align: middle;
    }
    .aul-table td:first-child { padding-left: 1.35rem; }
    .aul-table td:last-child  { padding-right: 1.35rem; text-align: right; }

    /* Employee cell */
    .emp-cell {
        display: flex;
        align-items: center;
        gap: .6rem;
        white-space: nowrap;
    }
    .emp-avatar {
        width: 32px; height: 32px;
        border-radius: 50%;
        overflow: hidden;
        flex-shrink: 0;
        border: 1.5px solid var(--t-border);
        background: var(--t-surface-2);
    }
    .emp-avatar img { width: 100%; height: 100%; object-fit: cover; }
    .emp-name {
        font-weight: 600;
        color: var(--t-text-primary);
        font-size: 1rem;
    }

    /* ID / Team pills */
    .aul-mono {
        font-weight: 600;
        font-variant-numeric: tabular-nums;
        color: var(--t-text-muted);
        background: var(--t-surface-2);
        border: 1px solid var(--t-border);
        border-radius: var(--t-radius-xs);
        padding: .15rem .5rem;
        white-space: nowrap;
    }
    .aul-team-badge {
        font-size: .72rem;
        font-weight: 600;
        color: var(--t-teal);
        background: var(--t-teal-soft);
        border-radius: var(--t-radius-pill);
        padding: .18rem .6rem;
        white-space: nowrap;
    }

    /* Date */
    .aul-date {
        font-variant-numeric: tabular-nums;
        color: var(--t-text-muted);
        white-space: nowrap;
    }

    /* Break type badge */
    .aul-type-badge {
        font-size: .72rem;
        font-weight: 600;
        color: #7c3aed;
        background: #f5f3ff;
        border: 1px solid #ede9fe;
        border-radius: var(--t-radius-pill);
        padding: .18rem .6rem;
        white-space: nowrap;
    }

    /* Break time */
    .aul-time {
        font-weight: 600;
        font-variant-numeric: tabular-nums;
        color: var(--t-text-secondary);
        white-space: nowrap;
    }
    .aul-time.ongoing {
        color: var(--t-amber);
        font-style: italic;
    }

    /* Duration badge */
    .aul-duration {
        display: inline-flex;
        align-items: center;
        gap: .3rem;
        font-size: .78rem;
        font-weight: 700;
        font-variant-numeric: tabular-nums;
        color: var(--t-accent);
        background: var(--t-accent-soft);
        border-radius: var(--t-radius-pill);
        padding: .22rem .7rem;
        white-space: nowrap;
    }

    /* Empty state */
    .aul-empty {
        text-align: center;
        padding: 3.5rem 1rem;
    }
    .aul-empty img { width: 120px; opacity: .55; margin-bottom: .75rem; }
    .aul-empty p {
        font-size: .83rem;
        color: var(--t-text-light);
        margin: 0;
    }

    /* ─── Pagination ────────────────────────────────────────────── */
    .aul-pagination-bar {
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: .75rem;
        padding: .9rem 1.35rem;
        border-top: 1px solid var(--t-border);
        background: var(--t-surface-2);
    }
    .aul-pag-info {
        font-size: .75rem;
        color: var(--t-text-light);
        font-weight: 500;
        white-space: nowrap;
    }
    .aul-pag-info strong {
        color: var(--t-text-secondary);
        font-weight: 700;
    }

    /* per-page select */
    .aul-per-page-wrap {
        display: flex;
        align-items: center;
        gap: .45rem;
        font-size: .75rem;
        color: var(--t-text-muted);
        font-weight: 500;
    }
    .aul-per-page-wrap select {
        height: 30px;
        border: 1.5px solid var(--t-border);
        border-radius: var(--t-radius-xs);
        background: var(--t-surface);
        color: var(--t-text-primary);
        font-size: .78rem;
        font-weight: 600;
        padding: 0 .5rem;
        outline: none;
    }
    .aul-per-page-wrap select:focus {
        border-color: var(--t-accent);
        box-shadow: 0 0 0 2px rgba(37,99,235,.1);
    }

    /* page buttons */
    .aul-pag-pages {
        display: flex;
        align-items: center;
        gap: .25rem;
        list-style: none;
        margin: 0; padding: 0;
    }
    .aul-pag-pages li a,
    .aul-pag-pages li span {
        display: flex; align-items: center; justify-content: center;
        width: 30px; height: 30px;
        border: 1.5px solid var(--t-border);
        border-radius: var(--t-radius-xs);
        background: var(--t-surface);
        color: var(--t-text-muted);
        font-size: .75rem;
        font-weight: 600;
        text-decoration: none;
        transition: all .13s;
        cursor: pointer;
    }
    .aul-pag-pages li a:hover {
        border-color: var(--t-accent);
        background: var(--t-accent-dim);
        color: var(--t-accent);
    }
    .aul-pag-pages li.active_pagination a,
    .aul-pag-pages li.active_pagination span {
        background: var(--t-accent);
        border-color: var(--t-accent);
        color: #fff;
        box-shadow: 0 2px 6px rgba(37,99,235,.3);
    }
    .aul-pag-pages li.disabled a,
    .aul-pag-pages li.disabled span {
        opacity: .35;
        cursor: not-allowed;
        pointer-events: none;
    }

    /* go-to page */
    .aul-goto-wrap {
        display: flex;
        align-items: center;
        gap: .4rem;
        font-size: .75rem;
        color: var(--t-text-muted);
    }
    .aul-goto-wrap input[type="number"] {
        width: 56px;
        height: 30px;
        border: 1.5px solid var(--t-border);
        border-radius: var(--t-radius-xs);
        background: var(--t-surface);
        color: var(--t-text-primary);
        font-size: .78rem;
        font-weight: 600;
        padding: 0 .45rem;
        outline: none;
        text-align: center;
    }
    .aul-goto-wrap input[type="number"]:focus {
        border-color: var(--t-accent);
        box-shadow: 0 0 0 2px rgba(37,99,235,.1);
    }
    .aul-goto-wrap button {
        height: 30px;
        padding: 0 .65rem;
        border: 1.5px solid var(--t-accent);
        border-radius: var(--t-radius-xs);
        background: var(--t-accent);
        color: #fff;
        font-size: .73rem;
        font-weight: 700;
        cursor: pointer;
        transition: background .13s;
    }
    .aul-goto-wrap button:hover { background: #1d4ed8; border-color: #1d4ed8; }

    /* Select2 overrides */
    .select2-container--default .select2-selection--single {
        height: 36px !important;
        border: 1.5px solid var(--t-border) !important;
        border-radius: var(--t-radius-sm) !important;
        background: var(--t-surface-2) !important;
        display: flex; align-items: center;
    }
    .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 36px !important;
        color: var(--t-text-primary) !important;
        font-size: .83rem; font-weight: 500;
        padding-left: .8rem !important;
    }
    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 34px !important;
    }
    .select2-container--default.select2-container--focus .select2-selection--single {
        border-color: var(--t-accent) !important;
        box-shadow: 0 0 0 3px rgba(37,99,235,.1) !important;
    }
    .choices__inner {
        height: 36px !important;
        border: 1.5px solid var(--t-border) !important;
        border-radius: var(--t-radius-sm) !important;
        background: var(--t-surface-2) !important;
        display: flex; align-items: center;
    }

    /* Responsive */
    @media (max-width: 767px) {
        .aul-filter-bar { flex-direction: column; align-items: stretch; }
        .aul-filter-left, .aul-filter-right { width: 100%; }
        select.aul-ctrl, input.aul-ctrl { width: 100%; }
        .aul-pagination-bar { flex-direction: column; align-items: flex-start; }
    }
</style>
@endpush

@push('theme-script')
@endpush

@push('script-page')
    <script>
        $(function () {
            const $dateInput = $('input[name="date_range"]');
            const $form = $('#filterForm');

            let serverRange = @json(request('date_range')) ||
            moment().format('YYYY-MM-DD');
            let start = moment();
            let end = moment();

            if (serverRange.includes(' to ')) {
                const parts = serverRange.split(' to ');
                start = moment(parts[0].trim(), 'YYYY-MM-DD');
                end = moment(parts[1].trim(), 'YYYY-MM-DD');
            } else if (serverRange.includes('-') && serverRange.split('-').length === 3) {
                start = end = moment(serverRange.trim(), 'YYYY-MM-DD');
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

            // Apply date
            $dateInput.on('apply.daterangepicker', function (ev, picker) {
                $(this).val(
                    picker.startDate.format('YYYY-MM-DD') === picker.endDate.format('YYYY-MM-DD')
                        ? picker.startDate.format('YYYY-MM-DD')
                        : picker.startDate.format('YYYY-MM-DD') + ' to ' + picker.endDate.format('YYYY-MM-DD')
                );
                $form.submit();
            });

            // Clear date
            $dateInput.on('cancel.daterangepicker', function () {
                $(this).val('');
                $form.submit();
            });

            // Handle team/user changes via JS
            $('#team-id, #user-id').on('change', function () {
                $form.submit();
            });
        });
    </script>
@endpush

@section('content')
    @include('company.layouts.partials.nav')

    <div class="aul-page">

        {{-- ── Filter Bar ──────────────────────────────────────────────── --}}
        <form method="GET" action="{{ route('organization.report.break') }}"
              id="filterForm">
            <div class="aul-filter-bar">

                {{-- Left: Team + User selects --}}
                <div class="aul-filter-left">

                    {{-- Team --}}
                    <select class="aul-ctrl select2" id="team-id" name="team_id">
                        <option value="" {{ is_null(request('team_id')) ? 'selected' : '' }}>All Teams</option>
                        @foreach ($teams as $team)
                            <option value="{{ $team->id }}" {{ request('team_id') == $team->id ? 'selected' : '' }}>
                                {{ $team->name }}
                            </option>
                        @endforeach
                    </select>

                    {{-- User --}}
                    <select class="aul-ctrl select2" id="user-id" name="user_id">
                        <option value="" {{ is_null(request('user_id')) ? 'selected' : '' }}>All Users</option>
                        @foreach ($users as $user)
                            <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>
                                {{ $user->name }}
                            </option>
                        @endforeach
                    </select>

                </div>

                {{-- Right: date + actions --}}
                <div class="aul-filter-right">
                    <input type="text"
                           name="date_range"
                           id="date-range"
                           class="aul-ctrl form-control"
                           value="{{ request('date_range') ?? \Carbon\Carbon::today()->format('Y-m-d') }}"
                           autocomplete="off"
                           placeholder="Select date or range">

                    <div class="aul-filter-sep"></div>

                    <button type="submit" name="download" value="excel" class="aul-btn" title="Export to Excel">
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                        Export
                    </button>

                    <a href="{{ route('organization.report.break') }}" class="aul-btn" title="Reset filters">
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><polyline points="1 4 1 10 7 10"/><path d="M3.51 15a9 9 0 1 0 .49-3.61"/></svg>
                    </a>
                </div>

            </div>
        </form>

        {{-- ── Table Panel ──────────────────────────────────────────────── --}}
        <div class="aul-panel">

            {{-- Panel header --}}
            <div class="aul-panel-header">
                <div class="aul-panel-title">
                    <div class="aul-panel-icon">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                    </div>
                    <div>
                        <h6>Break Logs</h6>
                        <p class="sub">Employee break activity and durations</p>
                    </div>
                </div>
                @if(isset($breaks) && $breaks->count() > 0)
                    <span class="aul-count-badge">{{ $breaks->total() }} records</span>
                @endif
            </div>

            {{-- Table --}}
            <div class="aul-table-wrap">
                <table class="aul-table">
                    <thead>
                        <tr>
                            <th>Employee</th>
                            <th>Employee ID</th>
                            <th>Team</th>
                            <th>Date</th>
                            <th>Break Type</th>
                            <th>Break Start</th>
                            <th>Break End</th>
                            <th>Duration</th>
                        </tr>
                    </thead>
                    <tbody>
                    @forelse($breaks as $break)
                        <tr>
                            {{-- Employee --}}
                            <td>
                                <div class="emp-cell">
                                    <div class="emp-avatar">
                                        @php $gender = $break->employee['gender'] ?? null; @endphp
                                        @if($gender === GENDER_MALE)
                                            <img src="{{ asset('assets/assestsnew/menimg.png') }}" alt="Male">
                                        @else
                                            <img src="{{ asset('assets/assestsnew/femaile-report.svg') }}" alt="Female">
                                        @endif
                                    </div>
                                    <span class="emp-name">{{ $break->employee->user->name ?? 'N/A' }}</span>
                                </div>
                            </td>

                            {{-- ID --}}
                            <td><span class="aul-mono">{{ $break->employee->employee_id }}</span></td>

                            {{-- Team --}}
                            <td><span class="aul-team-badge">{{ $break->employee->team->name ?? 'N/A' }}</span></td>

                            {{-- Date --}}
                            <td>
                                <span class="aul-date">
                                    {{ $break->created_at ? $break->created_at->format('Y-m-d') : '—' }}
                                </span>
                            </td>

                            {{-- Break Type --}}
                            <td><span class="aul-type-badge">{{ ucfirst($break->breakType->break_name ?? 'Unknown') }}</span></td>

                            {{-- Break Start --}}
                            <td><span class="aul-time">{{ \Carbon\Carbon::parse($break->break_started_at)->format('h:i A') }}</span></td>

                            {{-- Break End --}}
                            <td>
                                @if($break->break_ended_at)
                                    <span class="aul-time">{{ \Carbon\Carbon::parse($break->break_ended_at)->format('h:i A') }}</span>
                                @else
                                    <span class="aul-time ongoing">Ongoing</span>
                                @endif
                            </td>

                            {{-- Duration --}}
                            <td>
                                <span class="aul-duration">
                                    <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                                    {{ $break->duration ?? '—' }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8">
                                <div class="aul-empty">
                                    <img src="{{ asset('assets/assestsnew/no_datasvg.svg') }}" alt="No data">
                                    <p>No break records found for the selected filters.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>

            {{-- ── Pagination ──────────────────────────────────────────── --}}
            @if(isset($breaks) && $breaks->count() > 0)
            <div class="aul-pagination-bar">

                {{-- Per page --}}
                <div class="aul-per-page-wrap">
                    <form id="perPageForm" method="GET" action="{{ url()->current() }}"
                          style="display:flex;align-items:center;gap:.45rem;margin:0">
                        @foreach(request()->except(['page', 'per_page']) as $key => $value)
                            <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                        @endforeach
                        <span>Rows</span>
                        <select name="per_page" onchange="document.getElementById('perPageForm').submit()">
                            @foreach([5, 10, 20, 50] as $size)
                                <option value="{{ $size }}" {{ request('per_page', 10) == $size ? 'selected' : '' }}>{{ $size }}</option>
                            @endforeach
                        </select>
                    </form>
                </div>

                {{-- Page numbers --}}
                <ul class="aul-pag-pages">
                    <li class="{{ $breaks->onFirstPage() ? 'disabled' : '' }}">
                        <a href="{{ $breaks->url(1) }}">&#171;</a>
                    </li>
                    <li class="{{ $breaks->onFirstPage() ? 'disabled' : '' }}">
                        <a href="{{ $breaks->previousPageUrl() }}">
                            <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="15 18 9 12 15 6"/></svg>
                        </a>
                    </li>
                    @php
                        $pStart = max(1, $breaks->currentPage() - 2);
                        $pEnd   = min($pStart + 4, $breaks->lastPage());
                    @endphp
                    @for ($i = $pStart; $i <= $pEnd; $i++)
                        <li class="{{ $breaks->currentPage() == $i ? 'active_pagination' : '' }}">
                            <a href="{{ $breaks->url($i) }}">{{ $i }}</a>
                        </li>
                    @endfor
                    <li class="{{ !$breaks->hasMorePages() ? 'disabled' : '' }}">
                        <a href="{{ $breaks->nextPageUrl() }}">
                            <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="9 18 15 12 9 6"/></svg>
                        </a>
                    </li>
                    <li class="{{ !$breaks->hasMorePages() ? 'disabled' : '' }}">
                        <a href="{{ $breaks->url($breaks->lastPage()) }}">&#187;</a>
                    </li>
                </ul>

                {{-- Go to page + total --}}
                <div class="aul-goto-wrap">
                    <form action="{{ url()->current() }}" method="GET"
                          style="display:flex;align-items:center;gap:.4rem">
                        @foreach(request()->except('page') as $key => $value)
                            <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                        @endforeach
                        <span>Go to</span>
                        <input type="number" name="page" min="1"
                               max="{{ $breaks->lastPage() }}"
                               placeholder="{{ $breaks->currentPage() }}">
                        <button type="submit">Go</button>
                    </form>
                    <span class="aul-pag-info">
                        <strong>{{ $breaks->firstItem() }}–{{ $breaks->lastItem() }}</strong>
                        of <strong>{{ $breaks->total() }}</strong>
                    </span>
                </div>

            </div>
            @endif

        </div>{{-- /.aul-panel --}}

    </div>{{-- /.aul-page --}}
@endsection