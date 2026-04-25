@extends('company.layouts.company')

@section('page-title')
    {{ __('Reports') }}
@endsection

@section('page-icon')
    {{ asset('assets/assestsnew/header-logo.svg') }}
@endsection

@push('css-page')
<style>
    @import url('https://fonts.googleapis.com/css2?family=Sora:wght@300;400;500;600;700&family=DM+Sans:ital,opsz,wght@0,9..40,300;0,9..40,400;0,9..40,500;1,9..40,300&display=swap');

    :root {
        --primary: #1a56ff;
        --primary-light: #e8edff;
        --primary-mid: #c2d0ff;
        --surface: #ffffff;
        --surface-2: #f5f7ff;
        --surface-3: #eef1fb;
        --border: #e2e6f3;
        --text-primary: #0d1226;
        --text-secondary: #5a6380;
        --text-muted: #9ba3be;
        --shadow-card: 0 2px 12px rgba(26, 86, 255, 0.07), 0 1px 3px rgba(13, 18, 38, 0.06);
        --shadow-hover: 0 8px 32px rgba(26, 86, 255, 0.16), 0 2px 8px rgba(13, 18, 38, 0.08);
        --radius: 16px;
        --radius-sm: 10px;
    }

    .reports-wrapper {
        font-family: 'DM Sans', sans-serif;
        padding: 36px 4px 60px;
        margin: 0 auto;
    }

    /* ── Page Header ── */
    .reports-header {
        display: flex;
        align-items: flex-end;
        justify-content: space-between;
        margin-bottom: 40px;
        gap: 16px;
    }

    .reports-header-left {
        display: flex;
        flex-direction: column;
        gap: 6px;
    }

    .reports-eyebrow {
        font-family: 'Sora', sans-serif;
        font-size: 11px;
        font-weight: 600;
        letter-spacing: 0.14em;
        text-transform: uppercase;
        color: var(--primary);
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .reports-eyebrow::before {
        content: '';
        display: inline-block;
        width: 18px;
        height: 2px;
        background: var(--primary);
        border-radius: 2px;
    }

    .reports-title {
        font-family: 'Sora', sans-serif;
        font-size: 28px;
        font-weight: 700;
        color: var(--text-primary);
        margin: 0;
        letter-spacing: -0.02em;
        line-height: 1.2;
    }

    .reports-subtitle {
        font-size: 14px;
        color: var(--text-secondary);
        margin: 0;
        font-weight: 400;
    }

    .reports-header-badge {
        background: var(--primary-light);
        color: var(--primary);
        font-family: 'Sora', sans-serif;
        font-size: 11px;
        font-weight: 600;
        letter-spacing: 0.06em;
        padding: 6px 14px;
        border-radius: 100px;
        white-space: nowrap;
    }

    /* ── Section Label ── */
    .section-label {
        font-family: 'Sora', sans-serif;
        font-size: 11px;
        font-weight: 600;
        letter-spacing: 0.12em;
        text-transform: uppercase;
        color: var(--text-muted);
        margin-bottom: 18px;
        padding-left: 2px;
    }

    /* ── Report Grid ── */
    .reports-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 20px;
    }

    /* ── Report Card ── */
    .report-card {
        position: relative;
        background: var(--surface);
        border: 1.5px solid var(--border);
        border-radius: var(--radius);
        padding: 28px 26px 26px;
        text-decoration: none;
        display: flex;
        flex-direction: column;
        gap: 20px;
        box-shadow: var(--shadow-card);
        transition: transform 0.22s cubic-bezier(.22,.68,0,1.2),
                    box-shadow 0.22s ease,
                    border-color 0.18s ease;
        overflow: hidden;
    }

    .report-card::before {
        content: '';
        position: absolute;
        top: 0; left: 0; right: 0;
        height: 3px;
        background: linear-gradient(90deg, var(--primary) 0%, #5b8aff 100%);
        opacity: 0;
        transition: opacity 0.22s ease;
        border-radius: var(--radius) var(--radius) 0 0;
    }

    .report-card::after {
        content: '';
        position: absolute;
        inset: 0;
        background: linear-gradient(135deg, rgba(26,86,255,0.025) 0%, transparent 60%);
        opacity: 0;
        transition: opacity 0.22s ease;
        pointer-events: none;
    }

    .report-card:hover {
        transform: translateY(-4px);
        box-shadow: var(--shadow-hover);
        border-color: var(--primary-mid);
        text-decoration: none;
    }

    .report-card:hover::before { opacity: 1; }
    .report-card:hover::after  { opacity: 1; }

    /* ── Card Top Row ── */
    .card-top {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
    }

    .card-icon-wrap {
        width: 48px;
        height: 48px;
        border-radius: var(--radius-sm);
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        transition: background 0.18s ease;
    }

    .report-card:hover .card-icon-wrap {
        border: 2px solid var(--primary-mid);
    }

    .card-icon-wrap img {
        width: 35px;
        height: 35px;
        object-fit: contain;
    }

    .card-arrow {
        width: 32px;
        height: 32px;
        border-radius: 50%;
        background: var(--surface-2);
        border: 1.5px solid var(--border);
        display: flex;
        align-items: center;
        justify-content: center;
        transition: background 0.18s ease, border-color 0.18s ease, transform 0.22s cubic-bezier(.22,.68,0,1.2);
        flex-shrink: 0;
    }

    .card-arrow img {
        width: 14px;
        height: 14px;
        filter: invert(50%) sepia(10%) saturate(400%) hue-rotate(210deg);
        transition: filter 0.18s ease;
    }

    .report-card:hover .card-arrow {
        background: var(--primary);
        border-color: var(--primary);
        transform: rotate(45deg);
    }

    .report-card:hover .card-arrow img {
        filter: invert(1) brightness(100);
    }

    /* ── Card Body ── */
    .card-body-text {
        display: flex;
        flex-direction: column;
        gap: 8px;
    }

    .card-title {
        font-family: 'Sora', sans-serif;
        font-size: 1.5rem;
        font-weight: 600;
        color: var(--text-primary);
        margin: 0;
        letter-spacing: -0.01em;
        line-height: 1.3;
    }

    .card-desc {
        color: var(--text-secondary);
        margin: 0;
        font-weight: 400;
    }

    /* ── Card Footer ── */
    .card-footer-row {
        display: flex;
        align-items: center;
        gap: 6px;
        padding-top: 14px;
        border-top: 1px solid var(--border);
        margin-top: auto;
    }

    .card-tag {
        font-size: 11px;
        font-weight: 500;
        color: var(--primary);
        background: var(--primary-light);
        padding: 3px 10px;
        border-radius: 100px;
        font-family: 'Sora', sans-serif;
        letter-spacing: 0.04em;
    }

    /* ── Responsive ── */
    @media (max-width: 991px) {
        .reports-grid {
            grid-template-columns: repeat(2, 1fr);
        }
    }

    @media (max-width: 640px) {
        .reports-grid {
            grid-template-columns: 1fr;
        }
        .reports-title { font-size: 22px; }
        .reports-header { flex-direction: column; align-items: flex-start; }
        .reports-wrapper { padding: 24px 0 40px; }
    }

    /* ── Staggered load animation ── */
    @keyframes cardIn {
        from { opacity: 0; transform: translateY(18px); }
        to   { opacity: 1; transform: translateY(0); }
    }

    .report-card {
        opacity: 0;
        animation: cardIn 0.45s cubic-bezier(.22,.68,0,1.2) forwards;
    }

    .report-card:nth-child(1) { animation-delay: 0.05s; }
    .report-card:nth-child(2) { animation-delay: 0.12s; }
    .report-card:nth-child(3) { animation-delay: 0.19s; }
    .report-card:nth-child(4) { animation-delay: 0.26s; }
    .report-card:nth-child(5) { animation-delay: 0.33s; }
    .report-card:nth-child(6) { animation-delay: 0.40s; }
</style>
@endpush

@push('theme-script')

@endpush

@push('script-page')

@endpush

@section('content')
    @include('company.layouts.partials.nav')

    <div class="reports-wrapper">

        {{-- ── Page Header ── --}}
        <div class="reports-header">
            <div class="reports-header-left">
                <span class="reports-eyebrow">Analytics</span>
                <h1 class="reports-title">Attendance &amp; Analysis</h1>
            </div>
        </div>

        {{-- ── Report Cards Grid ── --}}
        <div class="reports-grid">

            @if(\Auth::user()->can('break_report'))
                <a class="report-card" href="{{ route('organization.report.break') }}">
                    <div class="card-top">
                        <div class="card-icon-wrap">
                            <img src="{{ asset('assets/assestsnew/coffee-break.svg') }}" alt="">
                        </div>
                        <div class="card-arrow">
                            <img src="{{ asset('assets/assestsnew/arrow-up-right.svg') }}" alt="">
                        </div>
                    </div>
                    <div class="card-body-text">
                        <h2 class="card-title">Break Report</h2>
                        <p class="card-desc">Download reports of breaks taken by your employees</p>
                    </div>
                    <div class="card-footer-row">
                        <span class="card-tag">Total rest</span>
                    </div>
                </a>
            @endif

            @if(\Auth::user()->can('daily_attendance_report'))
                <a class="report-card" href="{{ route('organization.report.attendance') }}">
                    <div class="card-top">
                        <div class="card-icon-wrap">
                            <img src="{{ asset('assets/assestsnew/attendance.svg') }}" alt="">
                        </div>
                        <div class="card-arrow">
                            <img src="{{ asset('assets/assestsnew/arrow-up-right.svg') }}" alt="">
                        </div>
                    </div>
                    <div class="card-body-text">
                        <h2 class="card-title">Attendance Report</h2>
                        <p class="card-desc">Daily attendance including punch-in and punch-out timings across your organisation</p>
                    </div>
                    <div class="card-footer-row">
                        <span class="card-tag">Daily Attendance</span>
                    </div>
                </a>
            @endif

            @if(\Auth::user()->can('activity_report'))
                <a class="report-card" href="{{ route('organization.report.activity') }}">
                    <div class="card-top">
                        <div class="card-icon-wrap">
                            <img src="{{ asset('assets/assestsnew/technology.svg') }}" alt="">
                        </div>
                        <div class="card-arrow">
                            <img src="{{ asset('assets/assestsnew/arrow-up-right.svg') }}" alt="">
                        </div>
                    </div>
                    <div class="card-body-text">
                        <h2 class="card-title">Activity Report</h2>
                        <p class="card-desc">Comprehensive employee activity reports covering keyboard &amp; mouse usage</p>
                    </div>
                    <div class="card-footer-row">
                        <span class="card-tag">Productivity</span>
                    </div>
                </a>
            @endif

            {{-- Apps & URLs report (commented out in original — preserved) --}}
            {{--
            @if(\Auth::user()->can('apps_and_urls_report'))
            <a class="report-card" href="{{ route('organization.report.apps_and_urls') }}">
                <div class="card-top">
                    <div class="card-icon-wrap">
                        <img src="{{ asset('assets/assestsnew/network.svg') }}" alt="">
                    </div>
                    <div class="card-arrow">
                        <img src="{{ asset('assets/assestsnew/arrow-up-right.svg') }}" alt="">
                    </div>
                </div>
                <div class="card-body-text">
                    <h2 class="card-title">App & URLs Reports</h2>
                    <p class="card-desc">Download reports of Usage App & URLs by your employees</p>
                </div>
                <div class="card-footer-row">
                    <span class="card-tag">Usage</span>
                </div>
            </a>
            @endif
            --}}

            @if(\Auth::user()->can('highlights_report'))
                <a class="report-card" href="{{ route('organization.report.highlight') }}">
                    <div class="card-top">
                        <div class="card-icon-wrap">
                            <img src="{{ asset('assets/assestsnew/network.svg') }}" alt="">
                        </div>
                        <div class="card-arrow">
                            <img src="{{ asset('assets/assestsnew/arrow-up-right.svg') }}" alt="">
                        </div>
                    </div>
                    <div class="card-body-text">
                        <h2 class="card-title">Highlights</h2>
                        <p class="card-desc">Download reports of App &amp; URL usage by your employees</p>
                    </div>
                    <div class="card-footer-row">
                        <span class="card-tag">Usage</span>
                    </div>
                </a>
            @endif

        </div>
    </div>
@endsection