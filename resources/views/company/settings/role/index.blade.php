@extends('company.layouts.company')
@section('page-title') {{ __('Role Settings') }} @endsection
@section('page-icon') {{ asset('assets/assestsnew/settings.svg') }} @endsection

@push('css-page')
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<style>
:root {
    --primary: #2563EB; --primary-light: #EFF6FF; --primary-mid: #BFDBFE; --primary-dark: #1D4ED8;
    --success: #059669; --success-light: #ECFDF5;
    --warning: #D97706; --warning-light: #FFFBEB;
    --danger: #DC2626; --danger-light: #FEF2F2;
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

/* ── HEADER + STATS ROW ── */
.role-header-stats-row { display:flex; align-items:center; justify-content:space-between; gap:20px; margin-bottom:20px; flex-wrap:wrap; }
.role-header-left { flex-shrink:0; }
.page-title-main { font-size:22px; font-weight:800; color:var(--gray-900); margin:0 0 4px; letter-spacing:-.4px; }
.page-title-sub { font-size:13px; color:var(--gray-400); margin:0; }

/* ── COMPACT STATS ── */
.rstat-grid { display:flex; align-items:center; gap:12px; flex-wrap:wrap; flex:1; justify-content:flex-end; }
.rstat-card { background:#fff; border-radius:var(--radius-md); padding:10px 16px; border:1px solid var(--gray-200); box-shadow:var(--shadow-sm); transition:transform .2s,box-shadow .2s; display:flex; align-items:center; gap:12px; flex:1; min-width:140px; max-width:260px; }
.rstat-card:hover { transform:translateY(-2px); box-shadow:var(--shadow-md); }
.rstat-card.c-blue  { border-left:3px solid var(--primary); }
.rstat-card.c-green { border-left:3px solid var(--success); }
.rstat-card.c-amber { border-left:3px solid var(--warning); }
.rstat-icon { width:34px; height:34px; flex-shrink:0; border-radius:var(--radius-sm); display:flex; align-items:center; justify-content:center; }
.rstat-card.c-blue  .rstat-icon { background:var(--primary-light); color:var(--primary); }
.rstat-card.c-green .rstat-icon { background:var(--success-light); color:var(--success); }
.rstat-card.c-amber .rstat-icon { background:var(--warning-light); color:var(--warning); }
.rstat-info { flex:1; min-width:0; }
.rstat-value { font-size:20px; font-weight:800; color:var(--gray-900); line-height:1; letter-spacing:-.5px; margin-bottom:2px; }
.rstat-label { font-size:10px; font-weight:600; color:var(--gray-400); text-transform:uppercase; letter-spacing:.4px; white-space:nowrap; }
.rstat-trend { font-size:10px; font-weight:700; padding:2px 8px; border-radius:var(--radius-full); flex-shrink:0; }
.rstat-card.c-blue  .rstat-trend { background:var(--primary-light); color:var(--primary); }
.rstat-card.c-green .rstat-trend { background:var(--success-light); color:var(--success); }
.rstat-card.c-amber .rstat-trend { background:var(--warning-light); color:var(--warning); }

/* ── FILTER BAR ── */
.filter-bar { display:flex; align-items:center; gap:10px; flex-wrap:wrap; background:#fff; border:1px solid var(--gray-200); border-radius:var(--radius-lg); padding:12px 16px; box-shadow:var(--shadow-sm); margin-bottom:16px; }
.filter-search-wrap { display:flex; align-items:center; background:var(--gray-50); border:1.5px solid var(--gray-200); border-radius:var(--radius-full); padding:0 8px 0 14px; flex:1; min-width:200px; max-width:340px; transition:border-color .18s,box-shadow .18s; }
.filter-search-wrap:focus-within { border-color:var(--primary); box-shadow:0 0 0 3px rgba(37,99,235,.08); background:#fff; }
.filter-search-wrap input { border:none; background:transparent; outline:none; font-size:13.5px; color:var(--gray-700); width:100%; padding:10px 0; }
.filter-search-wrap input::placeholder { color:var(--gray-400); }
.search-icon { color:var(--gray-400); flex-shrink:0; display:flex; align-items:center; margin-right:4px; }
.filter-search-wrap .search-btn { width:32px; height:32px; border-radius:var(--radius-full); background:var(--primary); border:none; color:#fff; cursor:pointer; display:flex; align-items:center; justify-content:center; flex-shrink:0; transition:background .15s; }
.filter-search-wrap .search-btn:hover { background:var(--primary-dark); }
.filter-divider { width:1px; height:28px; background:var(--gray-200); flex-shrink:0; }
.filter-spacer { flex:1; }
.btn-reset { display:flex; align-items:center; gap:6px; height:42px; padding:0 16px; border-radius:var(--radius-full); border:1.5px solid var(--gray-200); background:#fff; color:var(--gray-500); font-size:13px; font-weight:600; cursor:pointer; text-decoration:none; transition:all .18s; white-space:nowrap; }
.btn-reset:hover { border-color:var(--primary); color:var(--primary); background:var(--primary-light); }
.btn-add { display:flex; align-items:center; gap:8px; height:42px; padding:0 20px; border-radius:var(--radius-full); border:none; background:var(--primary); color:#fff; font-size:13px; font-weight:700; cursor:pointer; transition:all .18s; white-space:nowrap; box-shadow:0 2px 8px rgba(37,99,235,.28); text-decoration:none; }
.btn-add:hover { background:var(--primary-dark); box-shadow:0 4px 16px rgba(37,99,235,.36); transform:translateY(-1px); color:#fff; }
.btn-add .plus-circle { width:20px; height:20px; background:rgba(255,255,255,.2); border-radius:50%; display:flex; align-items:center; justify-content:center; flex-shrink:0; }

/* Active filter chips */
.active-filters { display:flex; align-items:center; gap:6px; flex-wrap:wrap; padding:0 4px 12px; }
.filter-chip { display:inline-flex; align-items:center; gap:6px; background:var(--primary-light); border:1px solid var(--primary-mid); color:var(--primary); border-radius:var(--radius-full); padding:4px 12px; font-size:12px; font-weight:600; }
.filter-chip .chip-remove { background:none; border:none; color:var(--primary); cursor:pointer; font-size:14px; line-height:1; opacity:.6; transition:opacity .15s; padding:0; display:flex; align-items:center; }
.filter-chip .chip-remove:hover { opacity:1; }

/* ── TABLE ── */
.role-table-card { background:#fff; border-radius:var(--radius-lg); border:1px solid var(--gray-200); overflow:hidden; box-shadow:var(--shadow-sm); }
.role-table-card table { width:100%; border-collapse:collapse; }
.role-table-card thead tr { background:var(--gray-50); border-bottom:2px solid var(--gray-200); }
.role-table-card th { padding:13px 18px; font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:.7px; color:var(--gray-400); white-space:nowrap; }
.role-table-card td { padding:15px 18px; border-bottom:1px solid var(--gray-100); vertical-align:middle; }
.role-table-card tbody tr:last-child td { border-bottom:none; }
.role-table-card tbody tr { transition:background .12s; }
.role-table-card tbody tr:hover { background:#FAFBFF; }
.row-num { width:28px; height:28px; border-radius:var(--radius-sm); background:var(--gray-100); display:flex; align-items:center; justify-content:center; font-size:11px; font-weight:700; color:var(--gray-400); }
.name-cell { display:flex; align-items:center; gap:12px; }
.name-avatar { width:38px; height:38px; border-radius:10px; background:linear-gradient(135deg,#EFF6FF,#BFDBFE); display:flex; align-items:center; justify-content:center; font-size:12px; font-weight:800; color:var(--primary); flex-shrink:0; text-transform:uppercase; border:1.5px solid var(--primary-mid); }
.name-main { font-size:14px; font-weight:700; color:var(--gray-900); }
.desc-text { font-size:13px; color:var(--gray-500); max-width:280px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
.perm-badge { display:inline-flex; align-items:center; gap:4px; background:var(--primary-light); border:1px solid var(--primary-mid); color:var(--primary); border-radius:var(--radius-full); padding:3px 10px; font-size:11px; font-weight:700; }
.act-btn { width:34px; height:34px; border-radius:var(--radius-sm); border:1px solid var(--gray-200); background:#fff; cursor:pointer; display:flex; align-items:center; justify-content:center; transition:all .15s; color:var(--gray-400); text-decoration:none; }
.act-btn.edit:hover { border-color:var(--primary); color:var(--primary); background:var(--primary-light); transform:scale(1.06); }
.act-btn.del:hover { border-color:var(--danger); color:var(--danger); background:var(--danger-light); transform:scale(1.06); }

/* Empty state */
.empty-state { text-align:center; padding:64px 20px; }
.empty-icon-wrap { width:72px; height:72px; background:var(--gray-100); border-radius:var(--radius-lg); display:flex; align-items:center; justify-content:center; margin:0 auto 16px; color:var(--gray-300); }
.empty-title { font-size:16px; font-weight:700; color:var(--gray-700); margin:0 0 6px; }
.empty-sub { font-size:13px; color:var(--gray-400); margin:0 0 20px; }

/* ── PAGINATION ── */
.pagination-bar { display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:12px; margin-top:20px; padding:14px 18px; background:#fff; border:1px solid var(--gray-200); border-radius:var(--radius-lg); box-shadow:var(--shadow-sm); }
.pagi-info { font-size:13px; color:var(--gray-400); font-weight:500; }
.pagi-info span { font-weight:700; color:var(--gray-700); }
.pagi-pages { display:flex; align-items:center; gap:4px; list-style:none; margin:0; padding:0; }
.pagi-pages li a { display:flex; align-items:center; justify-content:center; width:34px; height:34px; border-radius:var(--radius-sm); font-size:13px; font-weight:600; text-decoration:none; color:var(--gray-500); border:1px solid transparent; transition:all .15s; }
.pagi-pages li a:hover { background:var(--primary-light); color:var(--primary); border-color:var(--primary-mid); }
.pagi-pages li.active_pagination a { background:var(--primary); color:#fff; border-color:var(--primary); box-shadow:0 2px 8px rgba(37,99,235,.25); }
.pagi-pages li.disabled a { opacity:.35; pointer-events:none; }
.pagi-right { display:flex; align-items:center; gap:10px; }
.per-page-select { display:flex; align-items:center; gap:6px; font-size:13px; font-weight:500; color:var(--gray-500); }
.per-page-select select { height:34px; padding:0 10px; border:1px solid var(--gray-200); border-radius:var(--radius-sm); font-size:13px; font-weight:600; color:var(--gray-700); background:var(--gray-50); outline:none; cursor:pointer; }
.per-page-select select:focus { border-color:var(--primary); }
.goto-page { display:flex; align-items:center; gap:6px; }
.goto-page input { width:54px; height:34px; border:1px solid var(--gray-200); border-radius:var(--radius-sm); text-align:center; font-size:13px; font-weight:600; color:var(--gray-700); background:var(--gray-50); outline:none; }
.goto-page input:focus { border-color:var(--primary); }
.goto-page button { height:34px; padding:0 14px; border:none; background:var(--primary); color:#fff; border-radius:var(--radius-sm); font-size:13px; font-weight:600; cursor:pointer; transition:background .15s; }
.goto-page button:hover { background:var(--primary-dark); }

@media (max-width:768px) {
    .role-header-stats-row { flex-direction:column; align-items:flex-start; }
    .rstat-grid { justify-content:flex-start; width:100%; }
    .rstat-card { max-width:unset; }
    .pagination-bar { flex-direction:column; align-items:flex-start; }
    .settings-tab-bar { gap:3px; }
    .stab { padding:8px 12px; font-size:12px; }
}
@media (max-width:480px) {
    .rstat-grid { flex-direction:column; }
}
</style>
@endpush

@push('script-page')
<script>
document.addEventListener('DOMContentLoaded', function () {
    var searchInput = document.getElementById('searchInput');
    var searchBtn   = document.getElementById('searchBtn');
    var filterForm  = document.getElementById('filterForm');

    function doSearch() {
        document.getElementById('searchHidden').value = searchInput.value;
        filterForm.submit();
    }

    if (searchBtn) searchBtn.addEventListener('click', doSearch);
    if (searchInput) searchInput.addEventListener('keydown', function (e) {
        if (e.key === 'Enter') { e.preventDefault(); doSearch(); }
    });

    // Delete confirm
    document.querySelectorAll('.btn-delete-role').forEach(function (btn) {
        btn.addEventListener('click', function () {
            var id = this.dataset.id;
            Swal.fire({
                title: 'Delete Role?',
                text: 'This action cannot be undone!',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#2563EB',
                cancelButtonColor: '#DC2626',
                confirmButtonText: 'Yes, delete it!',
                cancelButtonText: 'Cancel',
            }).then(function (result) {
                if (result.isConfirmed) {
                    document.getElementById('delete-role-form-' + id).submit();
                }
            });
        });
    });

    // Per-page
    var perPageSel = document.getElementById('perPageSelect');
    if (perPageSel) {
        perPageSel.addEventListener('change', function () {
            document.getElementById('perPageForm').submit();
        });
    }
});
</script>
@endpush

@section('content')
@include('company.layouts.partials.nav')

@if ($errors->any())
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <ul class="mb-0">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<div class="col-12 entire_box1 mb-5">

    {{-- TABS --}}
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

    {{-- HEADER + STATS --}}
    @php
        $totalRoles     = $roles->total();
        $thisMonthRoles = \App\Models\Role::where(function($q){ $q->where('created_by', auth()->user()->creatorId())->orWhere('created_by',0); })->whereNotIn('name',['super admin','administrator','client','standard user','stealth user'])->whereMonth('created_at', now()->month)->whereYear('created_at', now()->year)->count();
        $lastSixMonths  = \App\Models\Role::where(function($q){ $q->where('created_by', auth()->user()->creatorId())->orWhere('created_by',0); })->whereNotIn('name',['super admin','administrator','client','standard user','stealth user'])->where('created_at','>=', now()->subMonths(6))->count();
    @endphp
    <div class="role-header-stats-row">
        <div class="role-header-left">
            <h1 class="page-title-main">Role Settings</h1>
            <p class="page-title-sub">Manage access levels and permission groups</p>
        </div>
        <div class="rstat-grid">
            <div class="rstat-card c-blue">
                <div class="rstat-icon">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                </div>
                <div class="rstat-info">
                    <div class="rstat-value">{{ $totalRoles }}</div>
                    <div class="rstat-label">Total Roles</div>
                </div>
                <span class="rstat-trend">All Time</span>
            </div>
            {{--<div class="rstat-card c-green">
                <div class="rstat-icon">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                </div>
                <div class="rstat-info">
                    <div class="rstat-value">{{ $thisMonthRoles }}</div>
                    <div class="rstat-label">Added This Month</div>
                </div>
                <span class="rstat-trend">{{ now()->format('M Y') }}</span>
            </div>
            <div class="rstat-card c-amber">
                <div class="rstat-icon">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/></svg>
                </div>
                <div class="rstat-info">
                    <div class="rstat-value">{{ $lastSixMonths }}</div>
                    <div class="rstat-label">Last 6 Months</div>
                </div>
                <span class="rstat-trend">6 Months</span>
            </div>--}}
        </div>
    </div>

    {{-- FILTER FORM (hidden) --}}
    <form method="GET" action="{{ route('organization.settings.role') }}" id="filterForm">
        @foreach(request()->except(['search','page']) as $k => $v)
            <input type="hidden" name="{{ $k }}" value="{{ $v }}">
        @endforeach
        <input type="hidden" name="search" id="searchHidden" value="{{ request('search') }}">
    </form>

    {{-- FILTER BAR --}}
    <div class="filter-bar">
        <div class="filter-search-wrap">
            <span class="search-icon">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
            </span>
            <input type="text" id="searchInput" placeholder="Search by name or description…" value="{{ request('search') }}" autocomplete="off">
            <button type="button" class="search-btn" id="searchBtn" title="Search">
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
            </button>
        </div>
        <div class="filter-divider"></div>
        <a href="{{ route('organization.settings.role') }}" class="btn-reset">
            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 12a9 9 0 1 0 9-9 9.75 9.75 0 0 0-6.74 2.74L3 8"/><path d="M3 3v5h5"/></svg>
            Reset
        </a>
        <div class="filter-spacer"></div>
        <a href="{{ route('organization.settings.role.create') }}" class="btn-add">
            <span class="plus-circle">+</span> Add Role
        </a>
    </div>

    {{-- ACTIVE CHIPS --}}
    @if(request('search'))
        <div class="active-filters">
            <span style="font-size:12px;color:var(--gray-400);font-weight:600;">Active filters:</span>
            <span class="filter-chip">
                <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                "{{ request('search') }}"
                <a href="{{ request()->fullUrlWithQuery(['search'=>null,'page'=>null]) }}" class="chip-remove">×</a>
            </span>
        </div>
    @endif

    {{-- TABLE --}}
    <div class="role-table-card">
        <table>
            <thead>
                <tr>
                    <th style="width:44px;">#</th>
                    <th>Role Name</th>
                    <th>Description</th>
                    <th>Permissions</th>
                    <th style="text-align:right; padding-right:22px;">Actions</th>
                </tr>
            </thead>
            <tbody>
            @php $protectedRoles = ['administrator','stealth user','standard user']; @endphp
            @forelse ($roles as $i => $role)
                @php
                    $words    = explode(' ', trim($role->name));
                    $initials = strtoupper(implode('', array_map(fn($w) => $w[0] ?? '', $words)));
                    $initials = substr($initials, 0, 2);
                    $permCount = $role->permissions->count();
                @endphp
                <tr>
                    <td><div class="row-num">{{ $roles->firstItem() + $i }}</div></td>
                    <td>
                        <div class="name-cell">
                            <div class="name-avatar">{{ $initials }}</div>
                            <span class="name-main">{{ \Str::title($role->name) }}</span>
                        </div>
                    </td>
                    <td>
                        <span class="desc-text" title="{{ $role->description }}">
                            {{ \Str::limit($role->description, 55) }}
                        </span>
                    </td>
                    <td>
                        <span class="perm-badge">
                            <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                            {{ $permCount }} {{ $permCount === 1 ? 'permission' : 'permissions' }}
                        </span>
                    </td>
                    <td>
                        <div style="display:flex; justify-content:flex-end; gap:6px;">
                            @if(!in_array(strtolower(trim($role->name)), $protectedRoles))
                                <a href="{{ route('organization.settings.role.edit', $role->id) }}" class="act-btn edit" title="Edit">
                                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                                </a>
                                <form id="delete-role-form-{{ $role->id }}" action="{{ route('organization.settings.role.destroy', $role->id) }}" method="POST" style="margin:0;">
                                    @csrf @method('DELETE')
                                    <button type="button" class="act-btn del btn-delete-role" data-id="{{ $role->id }}" title="Delete">
                                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/><path d="M10 11v6"/><path d="M14 11v6"/><path d="M9 6V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2"/></svg>
                                    </button>
                                </form>
                            @else
                                <span style="font-size:11px;font-weight:600;color:var(--gray-300);padding:0 8px;">Protected</span>
                            @endif
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5">
                        <div class="empty-state">
                            <div class="empty-icon-wrap">
                                <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/></svg>
                            </div>
                            <p class="empty-title">No roles found</p>
                            <p class="empty-sub">
                                @if(request('search'))
                                    No results match "<strong>{{ request('search') }}</strong>".
                                    <a href="{{ route('organization.settings.role') }}" style="color:var(--primary);font-weight:600;">Clear filter</a>
                                @else
                                    Get started by creating your first role.
                                @endif
                            </p>
                            @if(!request('search'))
                                <a href="{{ route('organization.settings.role.create') }}" class="btn-add" style="margin:0 auto; display:inline-flex;">
                                    <span class="plus-circle">+</span> Add First Role
                                </a>
                            @endif
                        </div>
                    </td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>

    {{-- PAGINATION --}}
    @if($roles->total() > 0)
    <div class="pagination-bar">
        <div class="pagi-info">
            Showing <span>{{ $roles->firstItem() }}–{{ $roles->lastItem() }}</span> of <span>{{ $roles->total() }}</span> roles
        </div>
        <ul class="pagi-pages">
            <li class="{{ $roles->onFirstPage() ? 'disabled' : '' }}">
                <a href="{{ $roles->url(1) }}"><svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="11 17 6 12 11 7"/><polyline points="18 17 13 12 18 7"/></svg></a>
            </li>
            <li class="{{ $roles->onFirstPage() ? 'disabled' : '' }}">
                <a href="{{ $roles->previousPageUrl() }}"><svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"/></svg></a>
            </li>
            @php $start = max(1, $roles->currentPage()-2); $end = min($start+4, $roles->lastPage()); @endphp
            @for($p=$start; $p<=$end; $p++)
                <li class="{{ $roles->currentPage()==$p ? 'active_pagination' : '' }}">
                    <a href="{{ $roles->url($p) }}">{{ $p }}</a>
                </li>
            @endfor
            <li class="{{ !$roles->hasMorePages() ? 'disabled' : '' }}">
                <a href="{{ $roles->nextPageUrl() }}"><svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"/></svg></a>
            </li>
            <li class="{{ !$roles->hasMorePages() ? 'disabled' : '' }}">
                <a href="{{ $roles->url($roles->lastPage()) }}"><svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="13 17 18 12 13 7"/><polyline points="6 17 11 12 6 7"/></svg></a>
            </li>
        </ul>
        <div class="pagi-right">
            <form id="perPageForm" method="GET" action="{{ url()->current() }}" style="display:flex;align-items:center;">
                @foreach(request()->except(['page','per_page']) as $key => $value)
                    <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                @endforeach
                <div class="per-page-select">
                    <span>Rows:</span>
                    <select name="per_page" id="perPageSelect">
                        @foreach([5,10,20,50] as $size)
                            <option value="{{ $size }}" {{ request('per_page',10)==$size ? 'selected' : '' }}>{{ $size }}</option>
                        @endforeach
                    </select>
                </div>
            </form>
            <form action="{{ url()->current() }}" method="GET" style="display:flex;align-items:center;">
                @foreach(request()->except('page') as $key => $value)
                    <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                @endforeach
                <div class="goto-page">
                    <span style="font-size:13px;color:var(--gray-400);font-weight:500;">Go to</span>
                    <input type="number" name="page" min="1" max="{{ $roles->lastPage() }}" placeholder="—">
                    <button type="submit">Go</button>
                </div>
            </form>
        </div>
    </div>
    @endif

</div>
@endsection