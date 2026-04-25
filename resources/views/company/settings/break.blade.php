@extends('company.layouts.company')

@section('page-title')
    {{ __('Break Settings') }}
@endsection

@section('page-icon')
    {{ asset('assets/assestsnew/settings.svg') }}
@endsection

@push('css-page')
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<style>
:root {
    --primary: #2563EB;
    --primary-light: #EFF6FF;
    --primary-mid: #BFDBFE;
    --success: #059669;
    --success-light: #ECFDF5;
    --danger: #DC2626;
    --danger-light: #FEF2F2;
    --warning: #D97706;
    --warning-light: #FFFBEB;
    --purple: #7C3AED;
    --purple-light: #F5F3FF;
    --gray-50: #F9FAFB;
    --gray-100: #F3F4F6;
    --gray-200: #E5E7EB;
    --gray-300: #D1D5DB;
    --gray-400: #9CA3AF;
    --gray-500: #6B7280;
    --gray-700: #374151;
    --gray-900: #111827;
    --radius-sm: 8px;
    --radius-md: 12px;
    --radius-lg: 16px;
    --radius-xl: 20px;
    --radius-full: 9999px;
    --shadow-sm: 0 1px 3px rgba(0,0,0,.06), 0 1px 2px rgba(0,0,0,.04);
    --shadow-md: 0 4px 16px rgba(0,0,0,.07), 0 2px 6px rgba(0,0,0,.04);
    --shadow-lg: 0 10px 40px rgba(0,0,0,.10), 0 4px 12px rgba(0,0,0,.06);
    --font: 'Plus Jakarta Sans', sans-serif;
}

* { font-family: var(--font) !important; }

/* ═══════════════════════════════════════
   SETTINGS TABS
═══════════════════════════════════════ */
.settings-tab-bar {
    display: flex;
    align-items: center;
    gap: 4px;
    background: var(--gray-100);
    border-radius: var(--radius-lg);
    padding: 5px;
    flex-wrap: wrap;
    margin-bottom: 28px;
    border: 1px solid var(--gray-200);
}
.settings-tab-bar a {
    text-decoration: none;
    flex: 1;
    min-width: 90px;
}
.stab {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 7px;
    padding: 9px 16px;
    border-radius: var(--radius-md);
    font-size: 13px;
    font-weight: 600;
    color: var(--gray-500);
    transition: all .2s ease;
    white-space: nowrap;
    cursor: pointer;
}
.stab:hover {
    background: #fff;
    color: var(--gray-700);
    box-shadow: var(--shadow-sm);
}
.stab.active {
    background: #fff;
    color: var(--primary);
    box-shadow: var(--shadow-sm);
    border: 1px solid var(--primary-mid);
}
.stab img {
    width: 15px;
    height: 15px;
    opacity: .5;
    transition: opacity .2s;
    filter: grayscale(1);
}
.stab.active img,
.stab:hover img {
    opacity: 1;
    filter: none;
}
.stab.active img { filter: saturate(2) hue-rotate(0deg); }

/* ═══════════════════════════════════════
   PAGE HEADER
═══════════════════════════════════════ */
.break-page-header {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    gap: 16px;
    margin-bottom: 24px;
    flex-wrap: wrap;
}
.break-page-title {
    font-size: 22px;
    font-weight: 800;
    color: var(--gray-900);
    margin: 0 0 4px 0;
    letter-spacing: -.4px;
}
.break-page-sub {
    font-size: 13px;
    color: var(--gray-400);
    margin: 0;
}


/* ═══════════════════════════════════════
   FILTER BAR
═══════════════════════════════════════ */
.filter-bar {
    display: flex;
    align-items: center;
    gap: 10px;
    flex-wrap: wrap;
    margin-bottom: 16px;
    background: #fff;
    border: 1px solid var(--gray-200);
    border-radius: var(--radius-lg);
    padding: 12px 16px;
    box-shadow: var(--shadow-sm);
}
.filter-search-wrap {
    display: flex;
    align-items: center;
    background: var(--gray-50);
    border: 1.5px solid var(--gray-200);
    border-radius: var(--radius-full);
    padding: 0 6px 0 14px;
    flex: 1;
    min-width: 180px;
    max-width: 320px;
    transition: border-color .18s, box-shadow .18s;
}
.filter-search-wrap:focus-within {
    border-color: var(--primary);
    box-shadow: 0 0 0 3px rgba(37,99,235,.08);
    background: #fff;
}
.filter-search-wrap input {
    border: none; background: transparent; outline: none;
    font-size: 13.5px; color: var(--gray-700); width: 100%;
    padding: 9px 0;
}
.filter-search-wrap input::placeholder { color: var(--gray-300); }
.filter-search-wrap .search-btn {
    width: 32px; height: 32px; border-radius: var(--radius-full);
    background: var(--primary-light); border: none; color: var(--primary);
    cursor: pointer; display: flex; align-items: center; justify-content: center;
    flex-shrink: 0; transition: background .15s;
}
.filter-search-wrap .search-btn:hover { background: var(--primary-mid); }

/* Status dropdown */
.filter-select-wrap {
    position: relative;
    display: flex;
    align-items: center;
}
.filter-select-wrap .select-icon {
    position: absolute;
    left: 12px;
    top: 50%; transform: translateY(-50%);
    color: var(--gray-400);
    font-size: 13px;
    pointer-events: none;
    z-index: 1;
}
.filter-select-wrap select {
    appearance: none;
    -webkit-appearance: none;
    background: var(--gray-50);
    border: 1.5px solid var(--gray-200);
    border-radius: var(--radius-full);
    padding: 9px 36px 9px 34px;
    font-size: 13px;
    font-weight: 600;
    color: var(--gray-700);
    cursor: pointer;
    outline: none;
    min-width: 140px;
    transition: border-color .18s, box-shadow .18s;
    font-family: var(--font) !important;
}
.filter-select-wrap select:focus {
    border-color: var(--primary);
    box-shadow: 0 0 0 3px rgba(37,99,235,.08);
    background: #fff;
}
.filter-select-wrap .chevron-icon {
    position: absolute;
    right: 12px; top: 50%; transform: translateY(-50%);
    color: var(--gray-400); font-size: 11px; pointer-events: none;
}

/* Status options styling */
.filter-select-wrap select option[value="1"] { color: var(--success); }
.filter-select-wrap select option[value="0"] { color: var(--danger); }

.filter-divider {
    width: 1px; height: 28px;
    background: var(--gray-200);
    flex-shrink: 0;
}
.filter-spacer { flex: 1; }

.btn-reset {
    display: flex; align-items: center; gap: 6px;
    height: 40px; padding: 0 16px;
    border-radius: var(--radius-full);
    border: 1.5px solid var(--gray-200);
    background: #fff;
    color: var(--gray-500);
    font-size: 13px; font-weight: 600;
    cursor: pointer; text-decoration: none;
    transition: all .18s;
    white-space: nowrap;
    font-family: var(--font) !important;
}
.btn-reset:hover {
    border-color: var(--primary);
    color: var(--primary);
    background: var(--primary-light);
}
.btn-add-break {
    display: flex; align-items: center; gap: 8px;
    height: 40px; padding: 0 18px;
    border-radius: var(--radius-full);
    border: none;
    background: var(--primary);
    color: #fff;
    font-size: 13px; font-weight: 700;
    cursor: pointer; text-decoration: none;
    transition: all .18s;
    white-space: nowrap;
    box-shadow: 0 2px 8px rgba(37,99,235,.28);
    font-family: var(--font) !important;
}
.btn-add-break:hover {
    background: #1D4ED8;
    box-shadow: 0 4px 16px rgba(37,99,235,.36);
    transform: translateY(-1px);
}
.btn-add-break .plus-circle {
    width: 20px; height: 20px;
    background: rgba(255,255,255,.2);
    border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    
}

/* Active filter chips */
.active-filters {
    display: flex; align-items: center; gap: 6px; flex-wrap: wrap;
    padding: 0 16px 12px;
}
.filter-chip {
    display: inline-flex; align-items: center; gap: 6px;
    background: var(--primary-light);
    border: 1px solid var(--primary-mid);
    color: var(--primary);
    border-radius: var(--radius-full);
    padding: 4px 10px;
    font-size: 12px; font-weight: 600;
}
.filter-chip a {
    color: var(--primary); text-decoration: none;
    display: flex; align-items: center;
    opacity: .6; transition: opacity .15s;
}
.filter-chip a:hover { opacity: 1; }

/* ═══════════════════════════════════════
   TABLE
═══════════════════════════════════════ */
.break-table-card {
    background: #fff;
    border-radius: var(--radius-lg);
    border: 1px solid var(--gray-200);
    overflow: hidden;
    box-shadow: var(--shadow-sm);
}
.break-table-card table {
    width: 100%;
    border-collapse: collapse;
}
.break-table-card thead tr {
    background: var(--gray-50);
    border-bottom: 1px solid var(--gray-200);
}
.break-table-card th {
    padding: 12px 18px;
    font-size: 11px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .7px;
    color: var(--gray-400);
    white-space: nowrap;
}
.break-table-card td {
    padding: 14px 18px;
    border-bottom: 1px solid var(--gray-100);
    vertical-align: middle;
}
.break-table-card tbody tr:last-child td { border-bottom: none; }
.break-table-card tbody tr {
    transition: background .12s;
}
.break-table-card tbody tr:hover { background: #FAFBFF; }

/* Row number */
.row-num {
    width: 28px; height: 28px;
    border-radius: var(--radius-sm);
    background: var(--gray-100);
    display: flex; align-items: center; justify-content: center;
    font-size: 11px; font-weight: 700;
    color: var(--gray-400);
}

/* Name cell */
.name-cell {
    display: flex; align-items: center; gap: 11px;
}
.name-avatar {
    width: 36px; height: 36px;
    border-radius: 10px;
    background: linear-gradient(135deg, #EFF6FF, #BFDBFE);
    display: flex; align-items: center; justify-content: center;
    font-size: 12px; font-weight: 800;
    color: var(--primary);
    flex-shrink: 0;
    text-transform: uppercase;
    border: 1px solid var(--primary-mid);
}
.name-main {
    font-size: 14px; font-weight: 600;
    color: var(--gray-900);
}

/* Time badge */
.time-badge {
    display: inline-flex; align-items: center; gap: 5px;
    background: var(--gray-50);
    border: 1px solid var(--gray-200);
    border-radius: var(--radius-full);
    padding: 5px 12px;
    font-size: 12.5px; font-weight: 600;
    color: var(--gray-700);
}
.time-badge i { color: var(--primary); font-size: 11px; }

/* Status badges */
.status-badge {
    display: inline-flex; align-items: center; gap: 5px;
    border-radius: var(--radius-full);
    padding: 5px 12px;
    font-size: 12px; font-weight: 700;
    white-space: nowrap;
}
.status-badge::before {
    content: '';
    width: 6px; height: 6px;
    border-radius: 50%;
}
.status-active  {
    background: var(--success-light);
    color: var(--success);
    border: 1px solid #A7F3D0;
}
.status-active::before { background: var(--success); }
.status-inactive {
    background: var(--danger-light);
    color: var(--danger);
    border: 1px solid #FECACA;
}
.status-inactive::before { background: var(--danger); }

/* Action buttons */
.action-wrap { display: flex; gap: 6px; align-items: center; }
.act-btn {
    width: 32px; height: 32px;
    border-radius: var(--radius-sm);
    border: 1px solid var(--gray-200);
    background: #fff;
    cursor: pointer;
    display: flex; align-items: center; justify-content: center;
    transition: all .15s;
    color: var(--gray-400);
    font-size: 13px;
}
.act-btn.edit:hover {
    border-color: var(--primary);
    color: var(--primary);
    background: var(--primary-light);
    transform: scale(1.05);
}
.act-btn.del:hover {
    border-color: var(--danger);
    color: var(--danger);
    background: var(--danger-light);
    transform: scale(1.05);
}

/* Empty state */
.empty-state {
    text-align: center;
    padding: 64px 20px;
}
.empty-icon-wrap {
    width: 72px; height: 72px;
    background: var(--gray-100);
    border-radius: var(--radius-lg);
    display: flex; align-items: center; justify-content: center;
    margin: 0 auto 16px;
    font-size: 28px; color: var(--gray-300);
}
.empty-title {
    font-size: 16px; font-weight: 700;
    color: var(--gray-700); margin: 0 0 6px;
}
.empty-sub {
    font-size: 13px; color: var(--gray-400); margin: 0 0 20px;
}

/* ═══════════════════════════════════════
   PAGINATION
═══════════════════════════════════════ */
.pagination-bar {
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 12px;
    margin-top: 20px;
    padding: 14px 18px;
    background: #fff;
    border: 1px solid var(--gray-200);
    border-radius: var(--radius-lg);
    box-shadow: var(--shadow-sm);
}
.pagi-info { font-size: 13px; color: var(--gray-400); font-weight: 500; }
.pagi-info span { font-weight: 700; color: var(--gray-700); }
.pagi-pages {
    display: flex; align-items: center; gap: 4px;
    list-style: none; margin: 0; padding: 0;
}
.pagi-pages li a,
.pagi-pages li span {
    display: flex; align-items: center; justify-content: center;
    width: 34px; height: 34px;
    border-radius: var(--radius-sm);
    font-size: 13px; font-weight: 600;
    text-decoration: none;
    color: var(--gray-500);
    border: 1px solid transparent;
    transition: all .15s;
}
.pagi-pages li a:hover {
    background: var(--primary-light);
    color: var(--primary);
    border-color: var(--primary-mid);
}
.pagi-pages li.active_pagination a {
    background: var(--primary);
    color: #fff;
    border-color: var(--primary);
    box-shadow: 0 2px 8px rgba(37,99,235,.25);
}
.pagi-pages li.disabled a {
    opacity: .35; pointer-events: none;
}
.pagi-right {
    display: flex; align-items: center; gap: 8px;
}
.per-page-select {
    display: flex; align-items: center; gap: 6px;
    font-size: 13px; font-weight: 500; color: var(--gray-500);
}
.per-page-select select {
    height: 34px; padding: 0 10px;
    border: 1px solid var(--gray-200);
    border-radius: var(--radius-sm);
    font-size: 13px; font-weight: 600;
    color: var(--gray-700);
    background: var(--gray-50);
    outline: none; cursor: pointer;
    font-family: var(--font) !important;
}
.per-page-select select:focus { border-color: var(--primary); }
.goto-page {
    display: flex; align-items: center; gap: 6px;
}
.goto-page input {
    width: 54px; height: 34px;
    border: 1px solid var(--gray-200);
    border-radius: var(--radius-sm);
    text-align: center;
    font-size: 13px; font-weight: 600;
    color: var(--gray-700);
    background: var(--gray-50);
    outline: none;
    font-family: var(--font) !important;
}
.goto-page input:focus { border-color: var(--primary); }
.goto-page button {
    height: 34px; padding: 0 14px;
    border: none;
    background: var(--primary);
    color: #fff;
    border-radius: var(--radius-sm);
    font-size: 13px; font-weight: 600;
    cursor: pointer;
    transition: background .15s;
    font-family: var(--font) !important;
}
.goto-page button:hover { background: #1D4ED8; }

/* ═══════════════════════════════════════
   MODAL
═══════════════════════════════════════ */
.modal-content {
    border-radius: var(--radius-xl) !important;
    border: none !important;
    box-shadow: var(--shadow-lg) !important;
    overflow: hidden;
}
.modal-top-band {
    background: linear-gradient(135deg, var(--primary) 0%, #1D4ED8 100%);
    padding: 22px 24px 18px;
    position: relative;
    overflow: hidden;
}
.modal-top-band::before {
    content: '';
    position: absolute;
    top: -30px; right: -30px;
    width: 120px; height: 120px;
    border-radius: 50%;
    background: rgba(255,255,255,.08);
}
.modal-top-band::after {
    content: '';
    position: absolute;
    bottom: -20px; left: 20px;
    width: 80px; height: 80px;
    border-radius: 50%;
    background: rgba(255,255,255,.05);
}
.modal-top-icon {
    width: 44px; height: 44px;
    background: rgba(255,255,255,.15);
    border-radius: var(--radius-md);
    display: flex; align-items: center; justify-content: center;
    font-size: 20px; color: #fff;
    margin-bottom: 10px;
    backdrop-filter: blur(4px);
    border: 1px solid rgba(255,255,255,.2);
}
.modal-top-title {
    font-size: 18px; font-weight: 800;
    color: #fff; margin: 0 0 3px;
    letter-spacing: -.3px;
}
.modal-top-sub {
    font-size: 12.5px; color: rgba(255,255,255,.7); margin: 0;
}
.modal-close-btn {
    position: absolute;
    top: 16px; right: 16px;
    width: 30px; height: 30px;
    border-radius: 50%;
    background: rgba(255,255,255,.15);
    border: 1px solid rgba(255,255,255,.2);
    color: #fff;
    display: flex; align-items: center; justify-content: center;
    cursor: pointer; font-size: 13px;
    transition: background .15s;
}
.modal-close-btn:hover { background: rgba(255,255,255,.25); }

.modal-body { padding: 22px 24px !important; }

.mform-label {
    font-size: 11px; font-weight: 700;
    text-transform: uppercase; letter-spacing: .6px;
    color: var(--gray-400); margin-bottom: 7px; display: block;
}
.mform-input {
    width: 100%; height: 44px;
    border: 1.5px solid var(--gray-200) !important;
    border-radius: var(--radius-md) !important;
    padding: 0 14px !important;
    font-size: 14px !important;
    color: var(--gray-900);
    background: var(--gray-50);
    outline: none;
    transition: all .18s;
    font-family: var(--font) !important;
}
.mform-input:focus {
    border-color: var(--primary) !important;
    background: #fff;
    box-shadow: 0 0 0 3px rgba(37,99,235,.08) !important;
}
.input-suffix-wrap {
    display: flex;
    border: 1.5px solid var(--gray-200);
    border-radius: var(--radius-md);
    overflow: hidden;
    background: var(--gray-50);
    transition: all .18s;
}
.input-suffix-wrap:focus-within {
    border-color: var(--primary);
    background: #fff;
    box-shadow: 0 0 0 3px rgba(37,99,235,.08);
}
.input-suffix-wrap input {
    flex: 1; height: 44px;
    border: none; outline: none;
    padding: 0 14px; font-size: 14px;
    background: transparent;
    color: var(--gray-900);
    font-family: var(--font) !important;
}
.input-suffix-wrap .suffix {
    height: 44px;
    background: var(--primary-light);
    color: var(--primary);
    font-weight: 700; font-size: 12px;
    display: flex; align-items: center;
    padding: 0 16px;
    border-left: 1.5px solid var(--primary-mid);
    letter-spacing: .5px;
}
.mform-divider {
    border: none; border-top: 1px solid var(--gray-100); margin: 16px 0;
}

/* Checkbox row */
.check-row {
    display: flex; align-items: center; gap: 10px;
    padding: 10px 12px;
    background: var(--gray-50);
    border: 1px solid var(--gray-200);
    border-radius: var(--radius-md);
    margin-top: 8px;
    cursor: pointer;
    transition: all .15s;
}
.check-row:hover { border-color: var(--primary-mid); background: var(--primary-light); }
.check-row input[type=checkbox] {
    width: 16px; height: 16px;
    accent-color: var(--primary); cursor: pointer;
}
.check-row label {
    font-size: 13px; font-weight: 600;
    color: var(--gray-600); cursor: pointer; margin: 0;
}

/* Toggle */
.toggle-row {
    display: flex; align-items: center;
    justify-content: space-between;
    padding: 12px 14px;
    background: var(--gray-50);
    border: 1.5px solid var(--gray-200);
    border-radius: var(--radius-md);
    transition: all .15s;
}
.toggle-row:has(input:checked) {
    background: var(--primary-light);
    border-color: var(--primary-mid);
}
.toggle-row-left { font-size: 13px; font-weight: 600; color: var(--gray-600); }
.toggle-row-left small { font-size: 11.5px; color: var(--gray-400); display: block; margin-top: 2px; }
.toggle-switch {
    position: relative;
    display: inline-block;
    width: 44px; height: 24px;
}
.toggle-switch input { opacity: 0; width: 0; height: 0; }
.toggle-slider {
    position: absolute; inset: 0;
    background: var(--gray-300);
    border-radius: 100px; cursor: pointer;
    transition: .2s;
}
.toggle-slider:before {
    content: '';
    position: absolute;
    height: 18px; width: 18px;
    left: 3px; bottom: 3px;
    background: #fff;
    border-radius: 50%;
    transition: .2s;
    box-shadow: 0 1px 4px rgba(0,0,0,.2);
}
.toggle-switch input:checked + .toggle-slider { background: var(--primary); }
.toggle-switch input:checked + .toggle-slider:before { transform: translateX(20px); }

/* Modal footer */
.modal-footer-btns {
    display: flex; gap: 10px; justify-content: flex-end;
    padding-top: 8px;
}
.btn-modal-cancel {
    height: 42px; padding: 0 20px;
    border: 1.5px solid var(--gray-200);
    border-radius: var(--radius-full);
    background: #fff; color: var(--gray-500);
    font-size: 13.5px; font-weight: 600;
    cursor: pointer; transition: all .15s;
    font-family: var(--font) !important;
}
.btn-modal-cancel:hover { border-color: var(--gray-300); color: var(--gray-700); }
.btn-modal-submit {
    height: 42px; padding: 0 24px;
    border: none;
    border-radius: var(--radius-full);
    background: var(--primary); color: #fff;
    font-size: 13.5px; font-weight: 700;
    cursor: pointer; transition: all .18s;
    box-shadow: 0 2px 8px rgba(37,99,235,.28);
    font-family: var(--font) !important;
}
.btn-modal-submit:hover { background: #1D4ED8; transform: translateY(-1px); box-shadow: 0 4px 14px rgba(37,99,235,.36); }
.bstat-grid {
    display: flex;          /* change grid → flex so it stays inline */
    align-items: center;
    gap: 45px;
    flex-wrap: wrap;
    margin-bottom: 0;       /* remove bottom margin, row handles spacing */
    flex: 1;
    justify-content: flex-end;
}
.bstat-card {
    background: #fff;
    border-radius: var(--radius-md);
    padding: 10px 14px;     /* tighter padding */
    border: 1px solid var(--gray-200);
    box-shadow: var(--shadow-sm);
    transition: transform .2s, box-shadow .2s;
    display: flex;
    align-items: center;
    gap: 10px;
    min-width: 130px;
    max-width: 220px;
    flex: 1;
}
.bstat-card:hover { transform: translateY(-2px); box-shadow: var(--shadow-md); }
.bstat-card.c-blue  { border-left: 3px solid var(--primary); }
.bstat-card.c-green { border-left: 3px solid var(--success); }
.bstat-card.c-red   { border-left: 3px solid var(--danger); }

.bstat-icon {
    width: 32px; height: 32px; flex-shrink: 0;
    border-radius: var(--radius-sm);
    display: flex; align-items: center; justify-content: center;
}
.bstat-card.c-blue  .bstat-icon { background: var(--primary-light); color: var(--primary); }
.bstat-card.c-green .bstat-icon { background: var(--success-light); color: var(--success); }
.bstat-card.c-red   .bstat-icon { background: var(--danger-light);  color: var(--danger); }

.bstat-info { flex: 1; min-width: 0; }
.bstat-value {
    font-size: 18px; font-weight: 800;
    color: var(--gray-900); line-height: 1;
    letter-spacing: -0.5px; margin-bottom: 2px;
}
.bstat-label {
    font-size: 10px; font-weight: 600;
    color: var(--gray-400); text-transform: uppercase;
    letter-spacing: .4px; white-space: nowrap;
}
.bstat-trend {
    font-size: 10px; font-weight: 700;
    padding: 2px 7px; border-radius: var(--radius-full);
    flex-shrink: 0;
}
.bstat-card.c-blue  .bstat-trend { background: var(--primary-light); color: var(--primary); }
.bstat-card.c-green .bstat-trend { background: var(--success-light); color: var(--success); }
.bstat-card.c-red   .bstat-trend { background: var(--danger-light);  color: var(--danger); }

/* Responsive: stack on small screens */
@media (max-width: 768px) {
    .break-header-stats-row { flex-direction: column; align-items: flex-start; }
    .bstat-grid { justify-content: flex-start; width: 100%; }
    .bstat-card { max-width: unset; }
}
/* ═══════════ HEADER + STATS SAME ROW ═══════════ */
.break-header-stats-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 20px;
    margin-bottom: 20px;
}
.break-header-left {
    flex-shrink: 0;
}
</style>
@endpush

@push('script-page')
<script>
    function toggleStatusText(el) {
        document.getElementById('statusLabel').textContent = el.checked ? 'Active' : 'Inactive';
    }
    function toggleEditStatusText(el) {
        document.getElementById('editStatusLabel').textContent = el.checked ? 'Active' : 'Inactive';
    }

    document.addEventListener('DOMContentLoaded', function () {
        // Edit modal
        document.querySelectorAll('.edit-shift-btn').forEach(function (btn) {
            btn.addEventListener('click', function () {
                var form   = document.getElementById('editBreakForm');
                var status = this.dataset.status;
                form.action = this.dataset.url;
                document.getElementById('breakName').value             = this.dataset.name ?? '';
                document.getElementById('maximumBreakTime').value      = this.dataset.max  ?? '';
                document.getElementById('breakLimitCheckbox').checked  = (this.dataset.limit === '1');
                document.getElementById('editStatusSwitch').checked    = (status === '1');
                document.getElementById('editStatusLabel').textContent = (status === '1') ? 'Active' : 'Inactive';
            });
        });

        // Delete confirm
        document.querySelectorAll('.show_confirm').forEach(function (btn) {
            btn.addEventListener('click', function (e) {
                e.preventDefault();
                var id = this.dataset.id;
                Swal.fire({
                    title: 'Delete Break?',
                    text: 'This action cannot be undone!',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#2563EB',
                    cancelButtonColor: '#DC2626',
                    confirmButtonText: 'Yes, delete it!',
                    cancelButtonText: 'Cancel',
                    borderRadius: '16px',
                }).then(function (result) {
                    if (result.isConfirmed) {
                        document.getElementById('delete-form-' + id).submit();
                    }
                });
            });
        });

        // Status filter auto-submit
        var statusFilter = document.getElementById('statusFilter');
        if (statusFilter) {
            statusFilter.addEventListener('change', function () {
                document.getElementById('filterForm').submit();
            });
        }
    });

    // AJAX: Add Break
    $(document).ready(function () {
        $('#saveBreakBtn').click(function (e) {
            e.preventDefault();
            var form = $(this).closest('form');
            $('.text-danger').html('');
            $.ajax({
                type: 'POST', url: form.attr('action'), data: form.serialize(),
                success: function () {
                    $('#AddTeamModal').modal('hide');
                    show_toastr('Success', 'Break created successfully.', 'success');
                    setTimeout(function () { location.reload(); }, 900);
                },
                error: function (xhr) {
                    if (xhr.status === 422) {
                        $.each(xhr.responseJSON.errors, function (field, messages) {
                            $('#error-' + field).html(messages[0]);
                        });
                    } else {
                        show_toastr('Error', 'An unexpected error occurred.', 'error');
                    }
                }
            });
        });
    });

    // AJAX: Edit Break
    $(document).ready(function () {
        $('#editsaveBreakBtn').click(function (e) {
            e.preventDefault();
            var form = $('#editBreakForm');
            $('.text-danger').text('');
            $.ajax({
                url: form.attr('action'), type: 'POST', data: form.serialize(),
                headers: { 'X-CSRF-TOKEN': $('input[name="_token"]').val() },
                success: function () {
                    $('#editTeamModal').modal('hide');
                    show_toastr('Success', 'Break updated successfully.', 'success');
                    setTimeout(function () { location.reload(); }, 900);
                },
                error: function (xhr) {
                    if (xhr.status === 422) {
                        $.each(xhr.responseJSON.errors, function (key, value) {
                            $('#editerror-' + key).text(value[0]);
                        });
                    } else {
                        show_toastr('Error', 'An unexpected error occurred.', 'error');
                    }
                }
            });
        });
    });
</script>
@endpush

@section('content')
@include('company.layouts.partials.nav')

@if ($errors->any())
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <ul class="mb-0">
            @foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<div class="col-12 entire_box1 mb-5">

    {{-- ══════════════════════════════════════
         SETTINGS TABS
    ═══════════════════════════════════════ --}}
    @php $user = auth()->user(); @endphp
    <div class="settings-tab-bar">
        @if ($user->hasRole('administrator') || ($user->can('settings') && $user->can('company_setting_break')))
            <a href="{{ route('organization.settings.break') }}" class="text-decoration-none">
                <div class="stab {{ request()->routeIs('organization.settings.break*') ? 'active' : '' }}">
                    <img src="{{ asset('assets/assestsnew/coffee.svg') }}" alt="Break">
                    Break
                </div>
            </a>
        @endif
        @if ($user->hasRole('administrator') || ($user->can('settings') && $user->can('company_setting_designation')))
            <a href="{{ route('organization.settings.designation') }}" class="text-decoration-none">
                <div class="stab {{ request()->routeIs('organization.settings.designation*') ? 'active' : '' }}">
                    <img src="{{ asset('assets/assestsnew/briefcase.svg') }}" alt="Designation">
                    Designation
                </div>
            </a>
        @endif
        @if ($user->hasRole('administrator') || ($user->can('settings') && $user->can('company_setting_roles')))
            <a href="{{ route('organization.settings.role') }}" class="text-decoration-none">
                <div class="stab {{ request()->routeIs('organization.settings.role*') ? 'active' : '' }}">
                    <img src="{{ asset('assets/assestsnew/user-management.svg') }}" alt="Roles">
                    Roles
                </div>
            </a>
        @endif
        @if ($user->hasRole('administrator') || ($user->can('settings') && $user->can('company_setting_shifts')))
            <a href="{{ route('organization.settings.shift') }}" class="text-decoration-none">
                <div class="stab {{ request()->routeIs('organization.settings.shift*') ? 'active' : '' }}">
                    <img src="{{ asset('assets/assestsnew/user-account.svg') }}" alt="Shift">
                    Shift
                </div>
            </a>
        @endif
        @if ($user->hasRole('administrator') || ($user->can('settings') && $user->can('company_setting_teams')))
            <a href="{{ route('organization.settings.team') }}" class="text-decoration-none">
                <div class="stab {{ request()->routeIs('organization.settings.team*') ? 'active' : '' }}">
                    <img src="{{ asset('assets/assestsnew/groups.svg') }}" alt="Teams">
                    Teams
                </div>
            </a>
        @endif
        @if ($user->hasRole('administrator') || $user->can('settings'))
            <a href="{{ route('organization.settings.user') }}" class="text-decoration-none">
                <div class="stab {{ request()->routeIs('organization.settings.user*') ? 'active' : '' }}">
                    <img src="{{ asset('assets/assestsnew/user.svg') }}" alt="User">
                    User
                </div>
            </a>
        @endif
        @if ($user->hasRole('administrator') || ($user->can('settings') && $user->can('company_setting_workspace')))
            <a href="{{ route('organization.settings.workplace') }}" class="text-decoration-none">
                <div class="stab {{ request()->routeIs('organization.settings.workplace*') ? 'active' : '' }}">
                    <img src="{{ asset('assets/assestsnew/workplace.svg') }}" alt="Workplace">
                    Workplace
                </div>
            </a>
        @endif
    </div>

    {{-- ══════════════════════════════════════
         PAGE HEADER
    ═══════════════════════════════════════ --}}
   {{-- PAGE HEADER + STATS ROW --}}
<div class="break-header-stats-row">
    <div class="break-header-left">
        <h1 class="break-page-title">Break Settings</h1>
        <p class="break-page-sub">Manage and configure employee break types</p>
    </div>
<div class="bstat-grid">
    {{--<div class="bstat-card c-blue">
        <div class="bstat-icon">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="12 2 2 7 12 12 22 7 12 2"/><polyline points="2 17 12 22 22 17"/><polyline points="2 12 12 17 22 12"/></svg>
        </div>
        <div class="bstat-info">
            <div class="bstat-value">{{ $breaks->total() }}</div>
            <div class="bstat-label">All Breaks</div>
        </div>
        <span class="bstat-trend">Total</span>
    </div>--}}

    <div class="bstat-card c-green">
        <div class="bstat-icon">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
        </div>
        <div class="bstat-info">
            <div class="bstat-value">{{ $activeCount ?? $breaks->getCollection()->where('status', 1)->count() }}</div>
            <div class="bstat-label">Running Breaks</div>
        </div>
        <span class="bstat-trend">Active</span>
    </div>

    <div class="bstat-card c-red">
        <div class="bstat-icon">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>
        </div>
        <div class="bstat-info">
            <div class="bstat-value">{{ $inactiveCount ?? $breaks->getCollection()->where('status', 0)->count() }}</div>
            <div class="bstat-label">Paused Breaks</div>
        </div>
        <span class="bstat-trend">Inactive</span>
    </div>
</div></div>


    {{-- ══════════════════════════════════════
         FILTER BAR
    ═══════════════════════════════════════ --}}
    <form method="GET" action="{{ route('organization.settings.break') }}" id="filterForm">
        @foreach(request()->except(['search', 'status', 'page']) as $k => $v)
            <input type="hidden" name="{{ $k }}" value="{{ $v }}">
        @endforeach

        <div class="filter-bar">
            {{-- Search --}}
            <div class="filter-search-wrap">
                <input type="text" name="search" placeholder="Search breaks…"
                       value="{{ request('search') }}" id="searchInput">
                <button type="submit" class="search-btn" title="Search">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                </button>
            </div>

            {{-- Status Dropdown --}}
            <div class="filter-select-wrap">
                <span class="select-icon"><svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"/></svg></span>
                <select name="status" id="statusFilter">
                    <option value="">All Status</option>
                    <option value="1" {{ request('status') === '1' ? 'selected' : '' }}>Active</option>
                    <option value="0" {{ request('status') === '0' ? 'selected' : '' }}>Inactive</option>
                </select>
                <span class="chevron-icon"><svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"/></svg></span>
            </div>

            <div class="filter-divider"></div>

            {{-- Reset --}}
            <a href="{{ route('organization.settings.break') }}" class="btn-reset">
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 12a9 9 0 1 0 9-9 9.75 9.75 0 0 0-6.74 2.74L3 8"/><path d="M3 3v5h5"/></svg> Reset
            </a>

            <div class="filter-spacer"></div>

            {{-- Add Break --}}
            <button type="button" class="btn-add-break"
                    data-bs-toggle="modal" data-bs-target="#AddTeamModal">
                <span class="plus-circle">+</span>
                Add Break
            </button>
        </div>
    </form>

    {{-- Active filter chips --}}
    @if(request('search') || request('status') !== null && request('status') !== '')
        <div class="active-filters">
            <span style="font-size:12px;color:var(--gray-400);font-weight:600;">Filters:</span>
            @if(request('search'))
                <span class="filter-chip">
                    Search: "{{ request('search') }}"
                    <a href="{{ request()->fullUrlWithQuery(['search' => null]) }}">×</a>
                </span>
            @endif
            @if(request('status') !== null && request('status') !== '')
                <span class="filter-chip">
                    Status: {{ request('status') == '1' ? 'Active' : 'Inactive' }}
                    <a href="{{ request()->fullUrlWithQuery(['status' => null]) }}">×</a>
                </span>
            @endif
        </div>
    @endif

    {{-- ══════════════════════════════════════
         TABLE
    ═══════════════════════════════════════ --}}
    <div class="break-table-card">
        <table>
            <thead>
                <tr>
                    <th style="width:44px;">#</th>
                    <th>Break Name</th>
                    <th>Max Time</th>
                    <th>Status</th>
                    <th style="text-align:right;">Actions</th>
                </tr>
            </thead>
            <tbody>
            @forelse ($breaks as $i => $break)
                @php
                    $words    = explode(' ', trim($break->break_name));
                    $initials = strtoupper(implode('', array_map(fn($w) => $w[0], $words)));
                    $initials = substr($initials, 0, 2);
                @endphp
                <tr>
                    <td>
                        <div class="row-num">{{ $breaks->firstItem() + $i }}</div>
                    </td>
                    <td>
                        <div class="name-cell">
                            <div class="name-avatar">{{ $initials }}</div>
                            <span class="name-main">{{ $break->break_name }}</span>
                        </div>
                    </td>
                    <td>
                        <span class="time-badge">
                            <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                            {{ $break->maximum_break_time }} min
                        </span>
                    </td>
                    
                    <td>
                        @if ($break->status)
                            <span class="status-badge status-active">Active</span>
                        @else
                            <span class="status-badge status-inactive">Inactive</span>
                        @endif
                    </td>
                    <td>
                        <div class="action-wrap" style="justify-content:flex-end;">
                            <button class="act-btn edit edit-shift-btn"
                                    data-id="{{ $break->id }}"
                                    data-name="{{ e($break->break_name) }}"
                                    data-max="{{ $break->maximum_break_time }}"
                                    data-limit="{{ $break->break_limit_apply }}"
                                    data-status="{{ $break->status }}"
                                    data-url="{{ route('organization.settings.break.update', $break->id) }}"
                                    data-bs-toggle="modal" data-bs-target="#editTeamModal"
                                    title="Edit">
                                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                            </button>
                            <form id="delete-form-{{ $break->id }}" method="POST"
                                  action="{{ route('organization.settings.break.destroy', $break->id) }}" style="margin:0;">
                                @csrf
                                @method('DELETE')
                                <button type="button" class="act-btn del show_confirm"
                                        data-id="{{ $break->id }}" title="Delete">
                                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/><path d="M10 11v6"/><path d="M14 11v6"/><path d="M9 6V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2"/></svg>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6">
                        <div class="empty-state">
                            <div class="empty-icon-wrap">
                                <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M17 8h1a4 4 0 1 1 0 8h-1"/><path d="M3 8h14v9a4 4 0 0 1-4 4H7a4 4 0 0 1-4-4Z"/><line x1="6" y1="2" x2="6" y2="4"/><line x1="10" y1="2" x2="10" y2="4"/><line x1="14" y1="2" x2="14" y2="4"/></svg>
                            </div>
                            <p class="empty-title">No breaks found</p>
                            <p class="empty-sub">
                                @if(request('search') || request('status') !== '')
                                    Try adjusting your filters or
                                    <a href="{{ route('organization.settings.break') }}" style="color:var(--primary);">clear all filters</a>
                                @else
                                    Get started by adding your first break type.
                                @endif
                            </p>
                            @if(!request('search') && request('status') === '')
                                <button class="btn-add-break" data-bs-toggle="modal" data-bs-target="#AddTeamModal"
                                        style="margin:0 auto;">
                                    <span class="plus-circle">+</span> Add First Break
                                </button>
                            @endif
                        </div>
                    </td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>

    {{-- ══════════════════════════════════════
         PAGINATION
    ═══════════════════════════════════════ --}}
    @if($breaks->total() > 0)
    <div class="pagination-bar">
        <div class="pagi-info">
            Showing <span>{{ $breaks->firstItem() }}–{{ $breaks->lastItem() }}</span>
            of <span>{{ $breaks->total() }}</span> breaks
        </div>

        <ul class="pagi-pages">
            <li class="{{ $breaks->onFirstPage() ? 'disabled' : '' }}">
                <a href="{{ $breaks->url(1) }}"><svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="11 17 6 12 11 7"/><polyline points="18 17 13 12 18 7"/></svg></a>
            </li>
            <li class="{{ $breaks->onFirstPage() ? 'disabled' : '' }}">
                <a href="{{ $breaks->previousPageUrl() }}"><svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"/></svg></a>
            </li>
            @php
                $start = max(1, $breaks->currentPage() - 2);
                $end   = min($start + 4, $breaks->lastPage());
            @endphp
            @for ($i = $start; $i <= $end; $i++)
                <li class="{{ $breaks->currentPage() == $i ? 'active_pagination' : '' }}">
                    <a href="{{ $breaks->url($i) }}">{{ $i }}</a>
                </li>
            @endfor
            <li class="{{ !$breaks->hasMorePages() ? 'disabled' : '' }}">
                <a href="{{ $breaks->nextPageUrl() }}"><svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"/></svg></a>
            </li>
            <li class="{{ !$breaks->hasMorePages() ? 'disabled' : '' }}">
                <a href="{{ $breaks->url($breaks->lastPage()) }}"><svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="13 17 18 12 13 7"/><polyline points="6 17 11 12 6 7"/></svg></a>
            </li>
        </ul>

        <div class="pagi-right">
            <form id="perPageForm" method="GET" action="{{ url()->current() }}"
                  style="display:flex;align-items:center;gap:0;flex-direction:row !important;">
                @foreach(request()->except(['page', 'per_page']) as $key => $value)
                    <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                @endforeach
                <div class="per-page-select">
                    <span>Rows:</span>
                    <select name="per_page" onchange="document.getElementById('perPageForm').submit()">
                        @foreach([5, 10, 20, 50] as $size)
                            <option value="{{ $size }}" {{ request('per_page', 10) == $size ? 'selected' : '' }}>{{ $size }}</option>
                        @endforeach
                    </select>
                </div>
            </form>

            <form action="{{ url()->current() }}" method="GET"
                  style="display:flex;align-items:center;gap:0;flex-direction:row !important;">
                @foreach(request()->except('page') as $key => $value)
                    <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                @endforeach
                <div class="goto-page">
                    <span style="font-size:13px;color:var(--gray-400);font-weight:500;">Go to</span>
                    <input type="number" name="page" min="1" max="{{ $breaks->lastPage() }}" placeholder="—">
                    <button type="submit">Go</button>
                </div>
            </form>
        </div>
    </div>
    @endif

    {{-- ══════════════════════════════════════
         ADD MODAL
    ═══════════════════════════════════════ --}}
    <div class="modal fade" id="AddTeamModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" style="width:520px;">
            <div class="modal-content">
                <div class="modal-top-band">
                    <button type="button" class="modal-close-btn" data-bs-dismiss="modal">
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                    </button>
                    <div class="modal-top-icon"><svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M17 8h1a4 4 0 1 1 0 8h-1"/><path d="M3 8h14v9a4 4 0 0 1-4 4H7a4 4 0 0 1-4-4Z"/><line x1="6" y1="2" x2="6" y2="4"/><line x1="10" y1="2" x2="10" y2="4"/><line x1="14" y1="2" x2="14" y2="4"/></svg></div>
                    <h5 class="modal-top-title">Add New Break</h5>
                    <p class="modal-top-sub">Configure a new break type for your team</p>
                </div>
                <div class="modal-body">
                    <form method="POST" action="{{ route('organization.settings.break.store') }}">
                        @csrf

                        <div class="mb-3">
                            <label class="mform-label">Break Name <span style="color:#DC2626">*</span></label>
                            <input type="text" class="mform-input" name="break_name" placeholder="e.g. Lunch Break, Tea Break">
                            <div class="text-danger mt-1 small" id="error-break_name"></div>
                        </div>

                        <hr class="mform-divider">

                        <div class="mb-3">
                            <label class="mform-label">Maximum Break Time <span style="color:#DC2626">*</span></label>
                            <div class="input-suffix-wrap">
                                <input type="number" name="maximum_break_time" placeholder="Enter duration" min="1">
                                <span class="suffix">MIN</span>
                            </div>
                            <div class="text-danger mt-1 small" id="error-maximum_break_time"></div>
                        </div>

                        <div class="mb-3">
                            <label class="mform-label">Day Limit</label>
                            <label class="check-row">
                                <input type="hidden" name="break_limit_apply" value="0">
                                <input type="checkbox" name="break_limit_apply" value="1">
                                <label style="font-size:13px;font-weight:600;color:var(--gray-600);cursor:pointer;margin:0;">
                                    Apply limit across entire day
                                </label>
                            </label>
                            <div class="text-danger mt-1 small" id="error-break_limit_apply"></div>
                        </div>

                        <hr class="mform-divider">

                        <div class="mb-3">
                            <label class="mform-label">Status</label>
                            <div class="toggle-row">
                                <div class="toggle-row-left">
                                    Break Status
                                    <small>Set whether this break is currently active</small>
                                </div>
                                <input type="hidden" name="status" value="0">
                                <label class="toggle-switch">
                                    <input type="checkbox" id="statusSwitch" name="status" value="1"
                                           checked onchange="toggleStatusText(this)">
                                    <span class="toggle-slider"></span>
                                </label>
                            </div>
                            <div class="text-danger mt-1 small" id="error-status"></div>
                        </div>

                        <div class="modal-footer-btns">
                            <button type="button" class="btn-modal-cancel" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn-modal-submit" id="saveBreakBtn">
                                <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" style="margin-right:4px;"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                                Create Break
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- ══════════════════════════════════════
         EDIT MODAL
    ═══════════════════════════════════════ --}}
    <div class="modal fade" id="editTeamModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" style="width:520px;">
            <div class="modal-content">
                <div class="modal-top-band" style="background:linear-gradient(135deg,#059669,#047857);">
                    <button type="button" class="modal-close-btn" data-bs-dismiss="modal">
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                    </button>
                    <div class="modal-top-icon"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg></div>
                    <h5 class="modal-top-title">Edit Break</h5>
                    <p class="modal-top-sub">Update the break configuration</p>
                </div>
                <div class="modal-body">
                    <form method="POST" id="editBreakForm" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label class="mform-label">Break Name <span style="color:#DC2626">*</span></label>
                            <input type="text" class="mform-input" id="breakName" name="break_name" placeholder="e.g. Lunch Break">
                            <div class="text-danger mt-1 small" id="editerror-break_name"></div>
                        </div>

                        <hr class="mform-divider">

                        <div class="mb-3">
                            <label class="mform-label">Maximum Break Time <span style="color:#DC2626">*</span></label>
                            <div class="input-suffix-wrap">
                                <input type="number" name="maximum_break_time" id="maximumBreakTime" placeholder="Enter duration" min="1">
                                <span class="suffix">MIN</span>
                            </div>
                            <div class="text-danger mt-1 small" id="editerror-maximum_break_time"></div>
                        </div>

                        <div class="mb-3">
                            <label class="mform-label">Day Limit</label>
                            <label class="check-row">
                                <input type="hidden" name="break_limit_apply" value="0">
                                <input type="checkbox" id="breakLimitCheckbox" name="break_limit_apply" value="1">
                                <label style="font-size:13px;font-weight:600;color:var(--gray-600);cursor:pointer;margin:0;">
                                    Apply limit across entire day
                                </label>
                            </label>
                            <div class="text-danger mt-1 small" id="editerror-break_limit_apply"></div>
                        </div>

                        <hr class="mform-divider">

                        <div class="mb-3">
                            <label class="mform-label">Status</label>
                            <div class="toggle-row">
                                <div class="toggle-row-left">
                                    Break Status
                                    <small id="editStatusLabel">Active</small>
                                </div>
                                <input type="hidden" name="status" value="0">
                                <label class="toggle-switch">
                                    <input type="checkbox" id="editStatusSwitch" name="status"
                                           value="1" onchange="toggleEditStatusText(this)">
                                    <span class="toggle-slider"></span>
                                </label>
                            </div>
                            <div class="text-danger mt-1 small" id="editerror-status"></div>
                        </div>

                        <div class="modal-footer-btns">
                            <button type="button" class="btn-modal-cancel" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" id="editsaveBreakBtn" class="btn-modal-submit"
                                    style="background:linear-gradient(135deg,#059669,#047857);box-shadow:0 2px 8px rgba(5,150,105,.28);">
                                <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" style="margin-right:4px;"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
                                Save Changes
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection