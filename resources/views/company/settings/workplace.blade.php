@extends('company.layouts.company')
@section('page-title') {{ __('Workplace Settings') }} @endsection
@section('page-icon') {{ asset('assets/assestsnew/settings.svg') }} @endsection

@push('css-page')
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.13.2/themes/base/jquery-ui.min.css">
<style>
:root {
    --primary: #2563EB; --primary-light: #EFF6FF; --primary-mid: #BFDBFE; --primary-dark: #1D4ED8;
    --success: #059669; --success-light: #ECFDF5;
    --warning: #D97706; --warning-light: #FFFBEB;
    --danger: #DC2626; --danger-light: #FEF2F2;
    --absent-color: #EF4444;
    --halfday-color: #3B82F6;
    --fullday-color: #2D8981;
    --gray-50: #F9FAFB; --gray-100: #F3F4F6; --gray-200: #E5E7EB; --gray-300: #D1D5DB;
    --gray-400: #9CA3AF; --gray-500: #6B7280; --gray-600: #4B5563; --gray-700: #374151; --gray-900: #111827;
    --radius-sm: 8px; --radius-md: 12px; --radius-lg: 16px; --radius-xl: 20px; --radius-full: 9999px;
    --shadow-sm: 0 1px 3px rgba(0,0,0,.06),0 1px 2px rgba(0,0,0,.04);
    --shadow-md: 0 4px 16px rgba(0,0,0,.07),0 2px 6px rgba(0,0,0,.04);
    --shadow-lg: 0 10px 40px rgba(0,0,0,.10),0 4px 12px rgba(0,0,0,.06);
    --font: 'Plus Jakarta Sans', sans-serif;
}
* { font-family: var(--font) !important; box-sizing: border-box; }

/* ── TABS ── */
.settings-tab-bar { display:flex; align-items:center; gap:4px; background:var(--gray-100); border-radius:var(--radius-lg); padding:5px; flex-wrap:wrap; margin-bottom:28px; border:1px solid var(--gray-200); }
.settings-tab-bar a { text-decoration:none; flex:1; min-width:90px; }
.stab { display:flex; align-items:center; justify-content:center; gap:7px; padding:9px 16px; border-radius:var(--radius-md); font-size:13px; font-weight:600; color:var(--gray-500); transition:all .2s; white-space:nowrap; cursor:pointer; }
.stab:hover { background:#fff; color:var(--gray-700); box-shadow:var(--shadow-sm); }
.stab.active { background:#fff; color:var(--primary); box-shadow:var(--shadow-sm); border:1px solid var(--primary-mid); }
.stab img { width:15px; height:15px; opacity:.5; transition:opacity .2s; filter:grayscale(1); }
.stab.active img, .stab:hover img { opacity:1; filter:none; }

/* ── PAGE HEADER ── */
.wp-header-row { display:flex; align-items:center; justify-content:space-between; gap:16px; margin-bottom:24px; flex-wrap:wrap; }
.page-title-main { font-size:22px; font-weight:800; color:var(--gray-900); margin:0 0 4px; letter-spacing:-.4px; }
.page-title-sub { font-size:13px; color:var(--gray-400); margin:0; }

/* ── MAIN CARD ── */
.wp-card { background:#fff; border:1px solid var(--gray-200); border-radius:var(--radius-lg); box-shadow:var(--shadow-sm); overflow:hidden;  }
.wp-card-header { padding:20px 28px 18px; border-bottom:1px solid var(--gray-100); background:var(--gray-50); display:flex; align-items:center; gap:14px; }
.wp-card-icon { width:44px; height:44px; border-radius:var(--radius-md); background:var(--primary-light); color:var(--primary); display:flex; align-items:center; justify-content:center; flex-shrink:0; }
.wp-card-title { font-size:15px; font-weight:800; color:var(--gray-900); margin:0 0 3px; }
.wp-card-sub { font-size:12.5px; color:var(--gray-400); margin:0; }
.wp-card-body { padding:28px; }

/* ── STAT CHIPS ── */
.wp-stats-row { display:flex; gap:12px; margin-bottom:28px; flex-wrap:wrap; }
.wp-stat-chip { display:flex; align-items:center; gap:10px; background:#fff; border:1.5px solid var(--gray-200); border-radius:var(--radius-md); padding:12px 18px; flex:1; min-width:140px; box-shadow:var(--shadow-sm); transition:transform .2s,box-shadow .2s; }
.wp-stat-chip:hover { transform:translateY(-2px); box-shadow:var(--shadow-md); }
.wp-stat-chip.absent  { border-left:3px solid var(--absent-color); }
.wp-stat-chip.halfday { border-left:3px solid var(--halfday-color); }
.wp-stat-chip.fullday { border-left:3px solid var(--fullday-color); }
.wp-stat-dot { width:10px; height:10px; border-radius:50%; flex-shrink:0; }
.absent  .wp-stat-dot { background:var(--absent-color); }
.halfday .wp-stat-dot { background:var(--halfday-color); }
.fullday .wp-stat-dot { background:var(--fullday-color); }
.wp-stat-info { flex:1; }
.wp-stat-label { font-size:10px; font-weight:700; text-transform:uppercase; letter-spacing:.5px; color:var(--gray-400); margin-bottom:2px; }
.wp-stat-value { font-size:20px; font-weight:800; color:var(--gray-900); line-height:1; letter-spacing:-.5px; }
.wp-stat-unit { font-size:11px; font-weight:600; color:var(--gray-400); margin-left:2px; }

/* ── SLIDER SECTION ── */
.slider-section-label { font-size:12px; font-weight:700; text-transform:uppercase; letter-spacing:.6px; color:var(--gray-400); margin-bottom:20px; display:flex; align-items:center; gap:8px; }
.slider-section-label::after { content:''; flex:1; height:1px; background:var(--gray-200); }

.slider-wrap { position:relative; padding:0 2px; margin-bottom:16px; }

/* jQuery UI slider override */
#dual-slider {
    height:10px; border-radius:var(--radius-full);
    border:none; background:var(--gray-200);
    margin:16px 0;
    box-shadow:none;
}
#dual-slider .ui-slider-range {
    background:var(--gray-300) !important;
    border-radius:0;
}
#dual-slider .ui-slider-handle {
    width:22px; height:22px; top:-6px;
    border-radius:50% !important;
    background:#fff !important;
    border:2.5px solid var(--primary) !important;
    box-shadow:0 2px 8px rgba(37,99,235,.25) !important;
    cursor:grab;
    transition:box-shadow .15s, border-color .15s;
    outline:none !important;
}
#dual-slider .ui-slider-handle:focus,
#dual-slider .ui-slider-handle:active {
    box-shadow:0 0 0 4px rgba(37,99,235,.15), 0 2px 8px rgba(37,99,235,.25) !important;
    cursor:grabbing;
}

/* Tooltip above handles */
.slider-tooltip {
    position:absolute; top:-38px; transform:translateX(-50%);
    background:var(--gray-900); color:#fff; font-size:11px; font-weight:700;
    padding:4px 9px; border-radius:var(--radius-sm); white-space:nowrap; pointer-events:none;
    box-shadow:var(--shadow-sm); transition:left .05s;
}
.slider-tooltip::after { content:''; position:absolute; bottom:-4px; left:50%; transform:translateX(-50%); border:4px solid transparent; border-top-color:var(--gray-900); border-bottom:none; }

/* Time markers */
.time-markers { display:flex; justify-content:space-between; margin-top:4px; padding:0 1px; }
.time-marker { font-size:9.5px; font-weight:600; color:var(--gray-400); text-align:center; flex:1; }
.time-marker:first-child { text-align:left; }
.time-marker:last-child  { text-align:right; }

/* ── LEGEND ── */
.wp-legend { display:flex; gap:20px; flex-wrap:wrap; margin-top:24px; padding-top:20px; border-top:1px solid var(--gray-100); }
.legend-item { display:flex; align-items:center; gap:8px; }
.legend-dot { width:12px; height:12px; border-radius:50%; flex-shrink:0; }
.legend-text { font-size:12.5px; font-weight:600; color:var(--gray-600); }

/* ── HOW IT WORKS ── */
.wp-info-grid { display:grid; grid-template-columns:repeat(3,1fr); gap:12px; margin-top:24px; }
.wp-info-box { border-radius:var(--radius-md); padding:16px; text-align:center; }
.wp-info-box.absent  { background:#FEF2F2; border:1px solid #FECACA; }
.wp-info-box.halfday { background:#EFF6FF; border:1px solid var(--primary-mid); }
.wp-info-box.fullday { background:#ECFDF5; border:1px solid #A7F3D0; }
.wp-info-icon { width:36px; height:36px; border-radius:50%; display:flex; align-items:center; justify-content:center; margin:0 auto 8px; font-size:16px; }
.absent  .wp-info-icon { background:#FEE2E2; color:var(--absent-color); }
.halfday .wp-info-icon { background:var(--primary-light); color:var(--halfday-color); }
.fullday .wp-info-icon { background:var(--success-light); color:var(--fullday-color); }
.wp-info-title { font-size:12px; font-weight:800; color:var(--gray-700); margin:0 0 4px; text-transform:uppercase; letter-spacing:.5px; }
.wp-info-desc { font-size:11.5px; color:var(--gray-500); margin:0; line-height:1.5; }
.wp-info-hours { font-size:18px; font-weight:800; color:var(--gray-900); margin:6px 0 2px; line-height:1; }

/* ── SAVE BUTTON ── */
.wp-footer { display:flex; justify-content:flex-end; margin-top:28px; padding-top:20px; border-top:1px solid var(--gray-100); }
.btn-save { height:44px; padding:0 32px; border:none; border-radius:var(--radius-full); background:var(--primary); color:#fff; font-size:14px; font-weight:700; cursor:pointer; transition:all .18s; box-shadow:0 2px 8px rgba(37,99,235,.28); display:flex; align-items:center; gap:8px; }
.btn-save:hover { background:var(--primary-dark); transform:translateY(-1px); box-shadow:0 4px 14px rgba(37,99,235,.36); }

@media (max-width:640px) {
    .wp-info-grid { grid-template-columns:1fr; }
    .wp-stats-row { flex-direction:column; }
    .settings-tab-bar { gap:3px; }
    .stab { padding:8px 12px; font-size:12px; }
}
</style>
@endpush

@push('script-page')
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.13.2/jquery-ui.min.js"></script>
<script>
$(function () {
    var minHalfDay = parseFloat($("#min_hours_half_day").val()) || 6;
    var minFullDay = parseFloat($("#min_hours_full_day").val()) || 18;

    function formatHours(h) {
        return h + 'h';
    }

    function updateDisplay(v0, v1) {
        // Stat chips
        $('#stat-absent').text(v0 + 'h');
        $('#stat-halfday').text((v1 - v0) + 'h');
        $('#stat-fullday').text((24 - v1) + 'h');

        // Info boxes
        $('#info-absent-hours').text('< ' + v0 + 'h');
        $('#info-halfday-hours').text(v0 + 'h – ' + v1 + 'h');
        $('#info-fullday-hours').text('≥ ' + v1 + 'h');

        // Tooltip positions (as %)
        var pct0 = (v0 / 24) * 100;
        var pct1 = (v1 / 24) * 100;
        $('#tooltip-0').css('left', pct0 + '%').text(formatHours(v0));
        $('#tooltip-1').css('left', pct1 + '%').text(formatHours(v1));

        // Hidden fields
        $("#min_hours_half_day").val(v0);
        $("#min_hours_full_day").val(v1);
        $("#workplace_max_hours_for_absent").val(v0);
        $("#workplace_min_hours_for_half_day").val(v1 - v0);
        $("#workplace_min_hours_for_full_day").val(24 - v1);

        // Slider gradient
        updateSliderColors(v0, v1);
    }

    function updateSliderColors(v0, v1) {
        var p0 = (v0 / 24) * 100;
        var p1 = (v1 / 24) * 100;
        $('#slider-color-style').remove();
        $('<style id="slider-color-style">')
            .prop('type', 'text/css')
            .html('#dual-slider { background: linear-gradient(to right, #EF4444 0%, #EF4444 ' + p0 + '%, #3B82F6 ' + p0 + '%, #3B82F6 ' + p1 + '%, #2D8981 ' + p1 + '%, #2D8981 100%) !important; }')
            .appendTo('head');
    }

    $("#dual-slider").slider({
        range: true, min: 0, max: 24,
        values: [minHalfDay, minFullDay],
        slide: function (event, ui) {
            updateDisplay(ui.values[0], ui.values[1]);
        }
    });

    // Init
    updateDisplay(minHalfDay, minFullDay);
    $(window).on('load resize', function(){ updateDisplay(minHalfDay, minFullDay); });
});
</script>
@endpush

@section('content')
@include('company.layouts.partials.nav')

{{--@if (session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif
@if ($errors->any())
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <ul class="mb-0">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif--}}

<div class="col-12 entire_box1 mb-5">

    {{-- SETTINGS TABS --}}
    @php $user = auth()->user(); @endphp
    <div class="settings-tab-bar">
        @if ($user->hasRole('administrator') || ($user->can('settings') && $user->can('company_setting_break')))
            <a href="{{ route('organization.settings.break') }}" class="text-decoration-none">
                <div class="stab {{ request()->routeIs('organization.settings.break*') ? 'active' : '' }}">
                    <img src="{{ asset('assets/assestsnew/coffee.svg') }}" alt="Break"> Break
                </div>
            </a>
        @endif
        @if ($user->hasRole('administrator') || ($user->can('settings') && $user->can('company_setting_designation')))
            <a href="{{ route('organization.settings.designation') }}" class="text-decoration-none">
                <div class="stab {{ request()->routeIs('organization.settings.designation*') ? 'active' : '' }}">
                    <img src="{{ asset('assets/assestsnew/briefcase.svg') }}" alt="Designation"> Designation
                </div>
            </a>
        @endif
        @if ($user->hasRole('administrator') || ($user->can('settings') && $user->can('company_setting_roles')))
            <a href="{{ route('organization.settings.role') }}" class="text-decoration-none">
                <div class="stab {{ request()->routeIs('organization.settings.role*') ? 'active' : '' }}">
                    <img src="{{ asset('assets/assestsnew/user-management.svg') }}" alt="Roles"> Roles
                </div>
            </a>
        @endif
        @if ($user->hasRole('administrator') || ($user->can('settings') && $user->can('company_setting_shifts')))
            <a href="{{ route('organization.settings.shift') }}" class="text-decoration-none">
                <div class="stab {{ request()->routeIs('organization.settings.shift*') ? 'active' : '' }}">
                    <img src="{{ asset('assets/assestsnew/user-account.svg') }}" alt="Shift"> Shift
                </div>
            </a>
        @endif
        @if ($user->hasRole('administrator') || ($user->can('settings') && $user->can('company_setting_teams')))
            <a href="{{ route('organization.settings.team') }}" class="text-decoration-none">
                <div class="stab {{ request()->routeIs('organization.settings.team*') ? 'active' : '' }}">
                    <img src="{{ asset('assets/assestsnew/groups.svg') }}" alt="Teams"> Teams
                </div>
            </a>
        @endif
        @if ($user->hasRole('administrator') || $user->can('settings'))
            <a href="{{ route('organization.settings.user') }}" class="text-decoration-none">
                <div class="stab {{ request()->routeIs('organization.settings.user*') ? 'active' : '' }}">
                    <img src="{{ asset('assets/assestsnew/user.svg') }}" alt="User"> User
                </div>
            </a>
        @endif
        @if ($user->hasRole('administrator') || ($user->can('settings') && $user->can('company_setting_workspace')))
            <a href="{{ route('organization.settings.workplace') }}" class="text-decoration-none">
                <div class="stab {{ request()->routeIs('organization.settings.workplace*') ? 'active' : '' }}">
                    <img src="{{ asset('assets/assestsnew/workplace.svg') }}" alt="Workplace"> Workplace
                </div>
            </a>
        @endif
    </div>

    {{-- PAGE HEADER --}}
    <div class="wp-header-row">
        <div>
            <h1 class="page-title-main">Workplace Settings</h1>
            <p class="page-title-sub">Define attendance thresholds for absent, half-day and full-day</p>
        </div>
    </div>

    @php
        $workplace    = \App\Models\WorkPlace::where('user_id', auth()->id())->first();
        $maxAbsent    = $workplace->workplace_max_hours_for_absent    ?? 6;
        $minHalfDay   = $workplace->workplace_min_hours_for_half_day  ?? 12;
        $minFullDay   = $workplace->workplace_min_hours_for_full_day  ?? 6;
        $sliderVal0   = $maxAbsent;
        $sliderVal1   = $maxAbsent + $minHalfDay;
    @endphp

    {{-- MAIN CARD --}}
    <div class="wp-card">
        <div class="wp-card-header">
            <div class="wp-card-icon">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
            </div>
            <div>
                <p class="wp-card-title">Attendance Hour Thresholds</p>
                <p class="wp-card-sub">Drag the handles to set half-day and full-day boundaries across a 24-hour scale</p>
            </div>
        </div>

        <div class="wp-card-body">

            {{-- STAT CHIPS --}}
            <div class="wp-stats-row">
                <div class="wp-stat-chip absent">
                    <div class="wp-stat-dot"></div>
                    <div class="wp-stat-info">
                        <div class="wp-stat-label">Absent / Below</div>
                        <div><span class="wp-stat-value" id="stat-absent">{{ $sliderVal0 }}h</span></div>
                    </div>
                </div>
                <div class="wp-stat-chip halfday">
                    <div class="wp-stat-dot"></div>
                    <div class="wp-stat-info">
                        <div class="wp-stat-label">Half Day Range</div>
                        <div><span class="wp-stat-value" id="stat-halfday">{{ $minHalfDay }}h</span></div>
                    </div>
                </div>
                <div class="wp-stat-chip fullday">
                    <div class="wp-stat-dot"></div>
                    <div class="wp-stat-info">
                        <div class="wp-stat-label">Full Day</div>
                        <div><span class="wp-stat-value" id="stat-fullday">{{ $minFullDay }}h</span></div>
                    </div>
                </div>
            </div>

            <p class="slider-section-label">Drag to Adjust</p>

            {{-- FORM --}}
            <form action="{{ route('organization.settings.workplace.update') }}" method="POST">
                @csrf
                <input type="hidden" name="min_hours_half_day" id="min_hours_half_day" value="{{ old('min_hours_half_day', $sliderVal0) }}">
                <input type="hidden" name="min_hours_full_day" id="min_hours_full_day" value="{{ old('min_hours_full_day', $sliderVal1) }}">
                <input type="hidden" id="workplace_max_hours_for_absent"   value="{{ $maxAbsent }}">
                <input type="hidden" id="workplace_min_hours_for_half_day" value="{{ $minHalfDay }}">
                <input type="hidden" id="workplace_min_hours_for_full_day" value="{{ $minFullDay }}">

                {{-- SLIDER --}}
                <div class="slider-wrap">
                    {{-- Floating tooltips --}}
                    <div style="position:relative; height:40px;">
                        <span class="slider-tooltip" id="tooltip-0" style="left:{{ ($sliderVal0/24)*100 }}%;">{{ $sliderVal0 }}h</span>
                        <span class="slider-tooltip" id="tooltip-1" style="left:{{ ($sliderVal1/24)*100 }}%;">{{ $sliderVal1 }}h</span>
                    </div>
                    <div id="dual-slider" style="min-width:300px;"></div>
                    <div class="time-markers">
                        @for($h = 0; $h <= 24; $h++)
                            <div class="time-marker">{{ str_pad($h, 2, '0', STR_PAD_LEFT) }}</div>
                        @endfor
                    </div>
                </div>

                {{-- LEGEND --}}
                <div class="wp-legend">
                    <div class="legend-item">
                        <div class="legend-dot" style="background:var(--absent-color);"></div>
                        <span class="legend-text">Absent — Below minimum presence</span>
                    </div>
                    <div class="legend-item">
                        <div class="legend-dot" style="background:var(--halfday-color);"></div>
                        <span class="legend-text">Half Day — Between thresholds</span>
                    </div>
                    <div class="legend-item">
                        <div class="legend-dot" style="background:var(--fullday-color);"></div>
                        <span class="legend-text">Full Day — Above full-day threshold</span>
                    </div>
                </div>

                {{-- HOW IT WORKS --}}
                <div class="wp-info-grid" style="margin-top:24px;">
                    <div class="wp-info-box absent">
                        <div class="wp-info-icon">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>
                        </div>
                        <p class="wp-info-title">Absent</p>
                        <p class="wp-info-hours" id="info-absent-hours">< {{ $sliderVal0 }}h</p>
                        <p class="wp-info-desc">Employee marked absent if worked less than this threshold</p>
                    </div>
                    <div class="wp-info-box halfday">
                        <div class="wp-info-icon">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="M12 2v10l4.24 4.24"/></svg>
                        </div>
                        <p class="wp-info-title">Half Day</p>
                        <p class="wp-info-hours" id="info-halfday-hours">{{ $sliderVal0 }}h – {{ $sliderVal1 }}h</p>
                        <p class="wp-info-desc">Counted as half-day attendance within this range</p>
                    </div>
                    <div class="wp-info-box fullday">
                        <div class="wp-info-icon">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                        </div>
                        <p class="wp-info-title">Full Day</p>
                        <p class="wp-info-hours" id="info-fullday-hours">≥ {{ $sliderVal1 }}h</p>
                        <p class="wp-info-desc">Full attendance credit above this threshold</p>
                    </div>
                </div>

                <div class="wp-footer">
                    <button type="submit" class="btn-save">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
                        Save Settings
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection