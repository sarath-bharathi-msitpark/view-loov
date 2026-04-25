@extends('company.layouts.company')

@section('page-title')
    {{ __('Designation Settings') }}
@endsection

@section('page-icon')
    {{ asset('assets/assestsnew/settings.svg') }}
@endsection

@push('css-page')
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;1,400&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">

<style>
.filter-search-wrap .search-btn {
    width: 32px; height: 32px; border-radius: var(--radius-full);
    background: var(--primary); border: none; color: #fff;
    cursor: pointer; display: flex; align-items: center; justify-content: center;
    flex-shrink: 0; transition: background .15s;
}
.filter-search-wrap .search-btn:hover { background: var(--primary-dark); }
:root {
    --primary: #2563EB;
    --primary-light: #EFF6FF;
    --primary-mid: #BFDBFE;
    --primary-dark: #1D4ED8;
    --success: #059669;
    --success-light: #ECFDF5;
    --warning: #D97706;
    --warning-light: #FFFBEB;
    --purple: #7C3AED;
    --purple-light: #F5F3FF;
    --danger: #DC2626;
    --danger-light: #FEF2F2;
    --gray-50: #F9FAFB;
    --gray-100: #F3F4F6;
    --gray-200: #E5E7EB;
    --gray-300: #D1D5DB;
    --gray-400: #9CA3AF;
    --gray-500: #6B7280;
    --gray-600: #4B5563;
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
* { font-family: var(--font) !important; box-sizing: border-box; }

/* ═══════════ TABS ═══════════ */
.settings-tab-bar {
    display: flex; align-items: center; gap: 4px;
    background: var(--gray-100); border-radius: var(--radius-lg);
    padding: 5px; flex-wrap: wrap; margin-bottom: 28px;
    border: 1px solid var(--gray-200);
}
.settings-tab-bar a { text-decoration: none; flex: 1; min-width: 90px; }
.stab {
    display: flex; align-items: center; justify-content: center; gap: 7px;
    padding: 9px 16px; border-radius: var(--radius-md);
    font-size: 13px; font-weight: 600; color: var(--gray-500);
    transition: all .2s ease; white-space: nowrap; cursor: pointer;
}
.stab:hover { background: #fff; color: var(--gray-700); box-shadow: var(--shadow-sm); }
.stab.active {
    background: #fff; color: var(--primary);
    box-shadow: var(--shadow-sm); border: 1px solid var(--primary-mid);
}
.stab img { width: 15px; height: 15px; opacity: .5; transition: opacity .2s; filter: grayscale(1); }
.stab.active img, .stab:hover img { opacity: 1; filter: none; }

/* ═══════════ PAGE HEADER ═══════════ */
.page-header-row {
    display: flex; align-items: flex-start;
    justify-content: space-between; gap: 16px;
    margin-bottom: 24px; flex-wrap: wrap;
}
.page-title-main { font-size: 22px; font-weight: 800; color: var(--gray-900); margin: 0 0 4px; letter-spacing: -.4px; }
.page-title-sub  { font-size: 13px; color: var(--gray-400); margin: 0; }

/* ═══════════ COMPACT STATS — same as break page ═══════════ */
.dstat-grid {
    display: flex;
    align-items: center;
    gap: 14px;
    flex-wrap: wrap;
    margin-bottom: 24px;
}
.dstat-card {
    background: #fff;
    border-radius: var(--radius-md);
    padding: 10px 16px;
    border: 1px solid var(--gray-200);
    box-shadow: var(--shadow-sm);
    transition: transform .2s, box-shadow .2s;
    display: flex;
    align-items: center;
    gap: 12px;
    flex: 1;
    min-width: 150px;
    max-width: 262px;
}
.dstat-card:hover { transform: translateY(-2px); box-shadow: var(--shadow-md); }
.dstat-card.c-blue  { border-left: 3px solid var(--primary); }
.dstat-card.c-green { border-left: 3px solid var(--success); }
.dstat-card.c-amber { border-left: 3px solid var(--warning); }

.dstat-icon {
    width: 34px; height: 34px; flex-shrink: 0;
    border-radius: var(--radius-sm);
    display: flex; align-items: center; justify-content: center;
}
.dstat-card.c-blue  .dstat-icon { background: var(--primary-light); color: var(--primary); }
.dstat-card.c-green .dstat-icon { background: var(--success-light); color: var(--success); }
.dstat-card.c-amber .dstat-icon { background: var(--warning-light); color: var(--warning); }

.dstat-info { flex: 1; min-width: 0; }
.dstat-value {
    font-size: 20px; font-weight: 800;
    color: var(--gray-900); line-height: 1;
    letter-spacing: -0.5px; margin-bottom: 2px;
}
.dstat-label {
    font-size: 10px; font-weight: 600;
    color: var(--gray-400); text-transform: uppercase;
    letter-spacing: .4px; white-space: nowrap;
}
.dstat-trend {
    font-size: 10px; font-weight: 700;
    padding: 2px 8px; border-radius: var(--radius-full);
    flex-shrink: 0;
}
.dstat-card.c-blue  .dstat-trend { background: var(--primary-light); color: var(--primary); }
.dstat-card.c-green .dstat-trend { background: var(--success-light); color: var(--success); }
.dstat-card.c-amber .dstat-trend { background: var(--warning-light); color: var(--warning); }

/* Remove bar — not needed in compact style */
.dstat-bar { display: none; }
.dstat-top { display: none; }

@media (max-width: 768px) {
    .dstat-grid { gap: 10px; }
    .dstat-card { max-width: unset; }
}
@media (max-width: 480px) {
    .dstat-grid { flex-direction: column; }
    .dstat-card { max-width: 100%; }
}
/* ═══════════ HEADER + STATS SAME ROW ═══════════ */
.desig-header-stats-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 20px;
    margin-bottom: 20px;
    flex-wrap: wrap;
}
.desig-header-left { flex-shrink: 0; }

/* dstat-grid margin reset — row handles it */
.desig-header-stats-row .dstat-grid {
    margin-bottom: 0;
    flex: 1;
    justify-content: flex-end;
}

@media (max-width: 768px) {
    .desig-header-stats-row { flex-direction: column; align-items: flex-start; }
    .desig-header-stats-row .dstat-grid { justify-content: flex-start; max-width: 100%; }
}

/* ═══════════ FILTER BAR ═══════════ */
.filter-bar {
    display: flex; align-items: center; gap: 10px; flex-wrap: wrap;
    background: #fff; border: 1px solid var(--gray-200);
    border-radius: var(--radius-lg); padding: 12px 16px;
    box-shadow: var(--shadow-sm); margin-bottom: 16px;
}

/* Search */
.filter-search-wrap {
    display: flex; align-items: center; background: var(--gray-50);
    border: 1.5px solid var(--gray-200); border-radius: var(--radius-full);
    padding: 0 8px 0 14px; flex: 1; min-width: 200px; max-width: 320px;
    transition: border-color .18s, box-shadow .18s;
}
.filter-search-wrap:focus-within {
    border-color: var(--primary); box-shadow: 0 0 0 3px rgba(37,99,235,.08); background: #fff;
}
.filter-search-wrap input {
    border: none; background: transparent; outline: none;
    font-size: 13.5px; color: var(--gray-700); width: 100%; padding: 10px 0;
}
.filter-search-wrap input::placeholder { color: var(--gray-400); }
.search-spinner {
    width: 18px; height: 18px; flex-shrink: 0; display: none; margin-right: 4px;
}
.search-spinner.active { display: block; }
.search-spinner svg { animation: spin .7s linear infinite; }
@keyframes spin { to { transform: rotate(360deg); } }
.search-icon { color: var(--gray-400); flex-shrink: 0; display: flex; align-items: center; margin-right: 2px; }

/* Date Range */
.filter-range-wrap {
    display: flex; align-items: center; gap: 8px;
    background: var(--gray-50); border: 1.5px solid var(--gray-200);
    border-radius: var(--radius-full); padding: 0 16px; height: 46px;
    transition: border-color .18s, box-shadow .18s; cursor: pointer; min-width: 240px;
}
.filter-range-wrap:focus-within {
    border-color: var(--primary); box-shadow: 0 0 0 3px rgba(37,99,235,.08); background: #fff;
}
.filter-range-wrap .cal-icon { color: var(--gray-400); display: flex; align-items: center; flex-shrink: 0; }
.filter-range-wrap input {
    border: none; background: transparent; outline: none;
    font-size: 13px; font-weight: 600; color: var(--gray-700);
    cursor: pointer; width: 100%; font-family: var(--font) !important;
}
.filter-range-wrap input::placeholder { color: var(--gray-400); font-weight: 500; }
.clear-date-btn {
    width: 18px; height: 18px; border-radius: 50%; background: var(--gray-200);
    border: none; color: var(--gray-500); cursor: pointer; font-size: 11px; font-weight: 700;
    display: none; align-items: center; justify-content: center; flex-shrink: 0;
    transition: all .15s; line-height: 1;
}
.clear-date-btn.visible { display: flex; }
.clear-date-btn:hover { background: var(--danger); color: #fff; }

.filter-divider { width: 1px; height: 28px; background: var(--gray-200); flex-shrink: 0; }
.filter-spacer  { flex: 1; }

.btn-reset {
    display: flex; align-items: center; gap: 6px; height: 42px; padding: 0 16px;
    border-radius: var(--radius-full); border: 1.5px solid var(--gray-200);
    background: #fff; color: var(--gray-500); font-size: 13px; font-weight: 600;
    cursor: pointer; text-decoration: none; transition: all .18s; white-space: nowrap;
}
.btn-reset:hover { border-color: var(--primary); color: var(--primary); background: var(--primary-light); }

.btn-add {
    display: flex; align-items: center; gap: 8px; height: 42px; padding: 0 20px;
    border-radius: var(--radius-full); border: none; background: var(--primary); color: #fff;
    font-size: 13px; font-weight: 700; cursor: pointer; transition: all .18s; white-space: nowrap;
    box-shadow: 0 2px 8px rgba(37,99,235,.28);
}
.btn-add:hover { background: var(--primary-dark); box-shadow: 0 4px 16px rgba(37,99,235,.36); transform: translateY(-1px); }
.btn-add .plus-circle {
    width: 20px; height: 20px; background: rgba(255,255,255,.2); border-radius: 50%;
    display: flex; align-items: center; justify-content: center; 
    flex-shrink: 0;
}

/* Active filter chips */
.active-filters { display: flex; align-items: center; gap: 6px; flex-wrap: wrap; padding: 0 4px 12px; }
.filter-chip {
    display: inline-flex; align-items: center; gap: 6px;
    background: var(--primary-light); border: 1px solid var(--primary-mid);
    color: var(--primary); border-radius: var(--radius-full); padding: 4px 12px;
    font-size: 12px; font-weight: 600;
}
.filter-chip .chip-remove {
    background: none; border: none; color: var(--primary); cursor: pointer;
    font-size: 14px; line-height: 1; opacity: .6; transition: opacity .15s; padding: 0;
    display: flex; align-items: center;
}
.filter-chip .chip-remove:hover { opacity: 1; }

/* ═══════════ TABLE ═══════════ */
.desig-table-card {
    background: #fff; border-radius: var(--radius-lg);
    border: 1px solid var(--gray-200); overflow: hidden; box-shadow: var(--shadow-sm);
}
.desig-table-card table { width: 100%; border-collapse: collapse; }
.desig-table-card thead tr { background: var(--gray-50); border-bottom: 2px solid var(--gray-200); }
.desig-table-card th {
    padding: 13px 18px; font-size: 11px; font-weight: 700;
    text-transform: uppercase; letter-spacing: .7px; color: var(--gray-400); white-space: nowrap;
}
.desig-table-card td { padding: 15px 18px; border-bottom: 1px solid var(--gray-100); vertical-align: middle; }
.desig-table-card tbody tr:last-child td { border-bottom: none; }
.desig-table-card tbody tr { transition: background .12s; }
.desig-table-card tbody tr:hover { background: #FAFBFF; }

.row-num {
    width: 28px; height: 28px; border-radius: var(--radius-sm);
    background: var(--gray-100); display: flex; align-items: center; justify-content: center;
    font-size: 11px; font-weight: 700; color: var(--gray-400);
}
.name-cell { display: flex; align-items: center; gap: 12px; }
.name-avatar {
    width: 38px; height: 38px; border-radius: 10px;
    background: linear-gradient(135deg, #EFF6FF, #BFDBFE);
    display: flex; align-items: center; justify-content: center;
    font-size: 12px; font-weight: 800; color: var(--primary);
    flex-shrink: 0; text-transform: uppercase; border: 1.5px solid var(--primary-mid);
    letter-spacing: .5px;
}
.name-main { font-size: 14px; font-weight: 700; color: var(--gray-900); }

/* Description with expand */
.desc-wrap { max-width: 300px; }
.desc-short { font-size: 13px; color: var(--gray-500); line-height: 1.5; }
.desc-full  { font-size: 13px; color: var(--gray-600); line-height: 1.6; display: none; }
.desc-toggle {
    font-size: 11.5px; font-weight: 700; color: var(--primary);
    background: none; border: none; cursor: pointer; padding: 2px 0 0; display: block;
    transition: color .15s;
}
.desc-toggle:hover { color: var(--primary-dark); }

.date-badge {
    display: inline-flex; align-items: center; gap: 5px;
    background: var(--gray-50); border: 1px solid var(--gray-200);
    border-radius: var(--radius-full); padding: 5px 12px;
    font-size: 12px; font-weight: 600; color: var(--gray-600); white-space: nowrap;
}
.act-btn {
    width: 34px; height: 34px; border-radius: var(--radius-sm);
    border: 1px solid var(--gray-200); background: #fff; cursor: pointer;
    display: flex; align-items: center; justify-content: center;
    transition: all .15s; color: var(--gray-400);
}
.act-btn.edit:hover {
    border-color: var(--primary); color: var(--primary);
    background: var(--primary-light); transform: scale(1.06);
}

/* Loading overlay for table */
.table-loading-overlay {
    position: relative;
}
.table-loading-overlay::after {
    content: ''; position: absolute; inset: 0;
    background: rgba(255,255,255,.7); border-radius: var(--radius-lg);
    display: none; z-index: 5;
}
.table-loading-overlay.loading::after { display: block; }
.table-spinner-wrap {
    position: absolute; top: 50%; left: 50%; transform: translate(-50%,-50%);
    z-index: 10; display: none; flex-direction: column; align-items: center; gap: 10px;
}
.table-loading-overlay.loading .table-spinner-wrap { display: flex; }
.table-spinner {
    width: 32px; height: 32px; border: 3px solid var(--primary-mid);
    border-top-color: var(--primary); border-radius: 50%;
    animation: spin .7s linear infinite;
}
.table-spinner-text { font-size: 12px; font-weight: 600; color: var(--gray-400); }

/* Empty state */
.empty-state { text-align: center; padding: 64px 20px; }
.empty-icon-wrap {
    width: 72px; height: 72px; background: var(--gray-100); border-radius: var(--radius-lg);
    display: flex; align-items: center; justify-content: center;
    margin: 0 auto 16px; font-size: 28px; color: var(--gray-300);
}
.empty-title { font-size: 16px; font-weight: 700; color: var(--gray-700); margin: 0 0 6px; }
.empty-sub   { font-size: 13px; color: var(--gray-400); margin: 0 0 20px; }

/* ═══════════ PAGINATION ═══════════ */
.pagination-bar {
    display: flex; align-items: center; justify-content: space-between;
    flex-wrap: wrap; gap: 12px; margin-top: 20px; padding: 14px 18px;
    background: #fff; border: 1px solid var(--gray-200);
    border-radius: var(--radius-lg); box-shadow: var(--shadow-sm);
}
.pagi-info { font-size: 13px; color: var(--gray-400); font-weight: 500; }
.pagi-info span { font-weight: 700; color: var(--gray-700); }
.pagi-pages { display: flex; align-items: center; gap: 4px; list-style: none; margin: 0; padding: 0; }
.pagi-pages li a {
    display: flex; align-items: center; justify-content: center;
    width: 34px; height: 34px; border-radius: var(--radius-sm);
    font-size: 13px; font-weight: 600; text-decoration: none;
    color: var(--gray-500); border: 1px solid transparent; transition: all .15s;
}
.pagi-pages li a:hover { background: var(--primary-light); color: var(--primary); border-color: var(--primary-mid); }
.pagi-pages li.active_pagination a { background: var(--primary); color: #fff; border-color: var(--primary); box-shadow: 0 2px 8px rgba(37,99,235,.25); }
.pagi-pages li.disabled a { opacity: .35; pointer-events: none; }
.pagi-right { display: flex; align-items: center; gap: 10px; }
.per-page-select { display: flex; align-items: center; gap: 6px; font-size: 13px; font-weight: 500; color: var(--gray-500); }
.per-page-select select {
    height: 34px; padding: 0 10px; border: 1px solid var(--gray-200);
    border-radius: var(--radius-sm); font-size: 13px; font-weight: 600;
    color: var(--gray-700); background: var(--gray-50); outline: none; cursor: pointer;
}
.per-page-select select:focus { border-color: var(--primary); }
.goto-page { display: flex; align-items: center; gap: 6px; }
.goto-page input {
    width: 54px; height: 34px; border: 1px solid var(--gray-200);
    border-radius: var(--radius-sm); text-align: center;
    font-size: 13px; font-weight: 600; color: var(--gray-700);
    background: var(--gray-50); outline: none;
}
.goto-page input:focus { border-color: var(--primary); }
.goto-page button {
    height: 34px; padding: 0 14px; border: none; background: var(--primary); color: #fff;
    border-radius: var(--radius-sm); font-size: 13px; font-weight: 600;
    cursor: pointer; transition: background .15s;
}
.goto-page button:hover { background: var(--primary-dark); }

/* ═══════════ MODAL ═══════════ */
.modal-content { border-radius: var(--radius-xl) !important; border: none !important; box-shadow: var(--shadow-lg) !important; overflow: hidden; }
.modal-top-band {
    background: linear-gradient(135deg, var(--primary) 0%, #1D4ED8 100%);
    padding: 22px 24px 20px; position: relative; overflow: hidden;
}
.modal-top-band.green { background: linear-gradient(135deg, #059669, #047857); }
.modal-top-band::before {
    content: ''; position: absolute; top: -30px; right: -30px;
    width: 120px; height: 120px; border-radius: 50%; background: rgba(255,255,255,.08);
}
.modal-top-band::after {
    content: ''; position: absolute; bottom: -20px; left: 20px;
    width: 80px; height: 80px; border-radius: 50%; background: rgba(255,255,255,.05);
}
.modal-top-icon {
    width: 44px; height: 44px; background: rgba(255,255,255,.15); border-radius: var(--radius-md);
    display: flex; align-items: center; justify-content: center;
    margin-bottom: 10px; border: 1px solid rgba(255,255,255,.2);
}
.modal-top-title { font-size: 18px; font-weight: 800; color: #fff; margin: 0 0 3px; letter-spacing: -.3px; }
.modal-top-sub   { font-size: 12.5px; color: rgba(255,255,255,.7); margin: 0; }
.modal-close-btn {
    position: absolute; top: 16px; right: 16px; width: 30px; height: 30px;
    border-radius: 50%; background: rgba(255,255,255,.15); border: 1px solid rgba(255,255,255,.2);
    color: #fff; display: flex; align-items: center; justify-content: center;
    cursor: pointer; font-size: 13px; transition: background .15s;
}
.modal-close-btn:hover { background: rgba(255,255,255,.28); }
.modal-body { padding: 22px 24px !important; }
.mform-label {
    font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: .6px;
    color: var(--gray-400); margin-bottom: 7px; display: block;
}
.mform-input {
    width: 100%; height: 46px; border: 1.5px solid var(--gray-200) !important;
    border-radius: var(--radius-md) !important; padding: 0 14px !important;
    font-size: 14px !important; color: var(--gray-900); background: var(--gray-50);
    outline: none; transition: all .18s;
}
.mform-input:focus { border-color: var(--primary) !important; background: #fff; box-shadow: 0 0 0 3px rgba(37,99,235,.08) !important; }
.mform-textarea {
    width: 100%; border: 1.5px solid var(--gray-200) !important;
    border-radius: var(--radius-md) !important; padding: 12px 14px !important;
    font-size: 14px !important; color: var(--gray-900); background: var(--gray-50);
    outline: none; transition: all .18s; resize: vertical; min-height: 96px;
}
.mform-textarea:focus { border-color: var(--primary) !important; background: #fff; box-shadow: 0 0 0 3px rgba(37,99,235,.08) !important; }
.mform-divider { border: none; border-top: 1px solid var(--gray-100); margin: 16px 0; }
.modal-footer-btns { display: flex; gap: 10px; justify-content: flex-end; padding-top: 8px; }
.btn-modal-cancel {
    height: 42px; padding: 0 20px; border: 1.5px solid var(--gray-200);
    border-radius: var(--radius-full); background: #fff; color: var(--gray-500);
    font-size: 13.5px; font-weight: 600; cursor: pointer; transition: all .15s;
}
.btn-modal-cancel:hover { border-color: var(--gray-300); color: var(--gray-700); }
.btn-modal-submit {
    height: 42px; padding: 0 24px; border: none; border-radius: var(--radius-full);
    background: var(--primary); color: #fff; font-size: 13.5px; font-weight: 700;
    cursor: pointer; transition: all .18s; box-shadow: 0 2px 8px rgba(37,99,235,.28);
    display: flex; align-items: center; gap: 6px;
}
.btn-modal-submit:hover { background: var(--primary-dark); transform: translateY(-1px); box-shadow: 0 4px 14px rgba(37,99,235,.36); }
.btn-modal-submit.green { background: linear-gradient(135deg,#059669,#047857); box-shadow: 0 2px 8px rgba(5,150,105,.28); }
.btn-modal-submit.green:hover { box-shadow: 0 4px 14px rgba(5,150,105,.36); }

/* ═══════════ FLATPICKR THEME ═══════════ */
.flatpickr-calendar {
    font-family: var(--font) !important;
    border-radius: var(--radius-lg) !important;
    box-shadow: var(--shadow-lg) !important;
    border: 1px solid var(--gray-200) !important;
    overflow: hidden;
}
.flatpickr-months { background: var(--primary) !important; }
.flatpickr-month  { background: var(--primary) !important; color: #fff !important; }
.flatpickr-current-month { color: #fff !important; }
.flatpickr-current-month input.cur-year { color: #fff !important; }
.flatpickr-current-month .flatpickr-monthDropdown-months,
.flatpickr-current-month .flatpickr-monthDropdown-months option { color: #fff !important; background: var(--primary) !important; }
.flatpickr-prev-month svg, .flatpickr-next-month svg { fill: rgba(255,255,255,.85) !important; }
.flatpickr-prev-month:hover svg, .flatpickr-next-month:hover svg { fill: #fff !important; }
.flatpickr-weekday { color: var(--gray-500) !important; font-weight: 700 !important; font-size: 11px !important; background: var(--gray-50) !important; }
.flatpickr-weekdays { background: var(--gray-50) !important; }
.flatpickr-day { font-size: 13px !important; font-weight: 500 !important; border-radius: var(--radius-sm) !important; transition: all .12s !important; }
.flatpickr-day:hover { background: var(--primary-light) !important; border-color: var(--primary-mid) !important; color: var(--primary) !important; }
.flatpickr-day.selected,
.flatpickr-day.startRange,
.flatpickr-day.endRange {
    background: var(--primary) !important;
    border-color: var(--primary) !important;
    color: #fff !important;
    font-weight: 700 !important;
}
.flatpickr-day.inRange {
    background: var(--primary-light) !important;
    border-color: transparent !important;
    color: var(--primary) !important;
    box-shadow: -5px 0 0 var(--primary-light), 5px 0 0 var(--primary-light) !important;
}
.flatpickr-day.today { border-color: var(--primary-mid) !important; color: var(--primary) !important; font-weight: 700 !important; }
.flatpickr-day.today.selected { color: #fff !important; }

/* ═══════════ RESPONSIVE ═══════════ */
@media (max-width: 768px) {
    .dstat-grid { grid-template-columns: 1fr 1fr; }
    .filter-bar { gap: 8px; }
    .filter-search-wrap { max-width: 100%; }
    .filter-range-wrap { min-width: 200px; }
    .pagination-bar { flex-direction: column; align-items: flex-start; }
    .settings-tab-bar { gap: 3px; }
    .stab { padding: 8px 12px; font-size: 12px; }
}
@media (max-width: 480px) {
    .dstat-grid { grid-template-columns: 1fr; }
    .filter-range-wrap { min-width: unset; width: 100%; }
}
</style>
@endpush

@push('script-page')
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {

    /* ─── Flatpickr Date Range Picker ─── */
    var fp = flatpickr('#dateRangePicker', {
        mode: 'range',
        dateFormat: 'Y-m-d',
        conjunction: ' to ',
        allowInput: false,
        disableMobile: true,
        onChange: function (selectedDates, dateStr) {
            var clearBtn = document.getElementById('clearDateBtn');
            if (dateStr) {
                clearBtn.classList.add('visible');
            } else {
                clearBtn.classList.remove('visible');
            }
            // Auto-submit only when both start & end dates are selected
            if (selectedDates.length === 2) {
                submitFilter();
            }
        },
        onReady: function (selectedDates, dateStr) {
            // Restore from URL on page load
            var val = '{{ request("date_range") }}';
            if (val && val.includes(' to ')) {
                var parts = val.split(' to ');
                fp.setDate([parts[0].trim(), parts[1].trim()], false);
                document.getElementById('clearDateBtn').classList.add('visible');
            }
        }
    });

    /* Clear date range */
    document.getElementById('clearDateBtn').addEventListener('click', function (e) {
        e.stopPropagation();
        fp.clear();
        this.classList.remove('visible');
        document.getElementById('dateRangeInput').value = '';
        submitFilter();
    });

   // Search: trigger on button click OR Enter key
var searchInput = document.getElementById('searchInput');
var searchBtn   = document.getElementById('searchBtn');

function doSearch() {
    document.getElementById('searchHidden').value = searchInput.value;
    document.getElementById('filterForm').submit();
}

searchBtn.addEventListener('click', doSearch);

searchInput.addEventListener('keydown', function (e) {
    if (e.key === 'Enter') {
        e.preventDefault();
        doSearch();
    }
});

    /* ─── Per-page auto-submit ─── */
    var perPageSel = document.getElementById('perPageSelect');
    if (perPageSel) {
        perPageSel.addEventListener('change', function () {
            document.getElementById('perPageForm').submit();
        });
    }

    /* ─── Expand / collapse description ─── */
    document.querySelectorAll('.desc-toggle').forEach(function (btn) {
        btn.addEventListener('click', function () {
            var wrap  = this.closest('.desc-wrap');
            var short = wrap.querySelector('.desc-short');
            var full  = wrap.querySelector('.desc-full');
            if (full.style.display === 'block') {
                full.style.display = 'none';
                short.style.display = 'block';
                this.textContent = 'Show more';
            } else {
                full.style.display = 'block';
                short.style.display = 'none';
                this.textContent = 'Show less';
            }
        });
    });

    /* ─── Edit modal population ─── */
    document.querySelectorAll('.edit-btn').forEach(function (btn) {
        btn.addEventListener('click', function () {
            document.getElementById('editName').value        = this.dataset.name;
            document.getElementById('editDescription').value = this.dataset.description;
            var url = "{{ route('organization.settings.designation.update', ['id' => '__ID__']) }}"
                        .replace('__ID__', this.dataset.id);
            document.getElementById('editDesignationForm').setAttribute('action', url);
        });
    });

    /* ─── AJAX: Add ─── */
    $('#submitAddDesignation').on('click', function (e) {
        e.preventDefault();
        var form = $('#addDesignationForm');
        $('.error-name, .error-description').text('');
        $.ajax({
            url: form.attr('action'), type: 'POST', data: form.serialize(),
            success: function () {
                $('#addDesignationModal').modal('hide');
                show_toastr('Success', 'Designation created successfully.', 'success');
                setTimeout(function () { location.reload(); }, 900);
            },
            error: function (xhr) {
                if (xhr.status === 422) {
                    $.each(xhr.responseJSON.errors, function (key, value) {
                        $('.error-' + key).text(value[0]);
                    });
                } else {
                    show_toastr('Error', 'An unexpected error occurred.', 'error');
                }
            }
        });
    });

    /* ─── AJAX: Edit ─── */
    $('#submitEditDesignation').on('click', function (e) {
        e.preventDefault();
        var form = $('#editDesignationForm');
        $('#editerror-name, #editerror-description').text('');
        $.ajax({
            url: form.attr('action'), type: 'POST', data: form.serialize(),
            success: function () {
                $('#editDesignationModal').modal('hide');
                show_toastr('Success', 'Designation updated successfully.', 'success');
                setTimeout(function () { location.reload(); }, 900);
            },
            error: function (xhr) {
                if (xhr.status === 422) {
                    var errors = xhr.responseJSON.errors;
                    if (errors.name)        $('#editerror-name').text(errors.name[0]);
                    if (errors.description) $('#editerror-description').text(errors.description[0]);
                } else {
                    show_toastr('Error', 'Something went wrong.', 'error');
                }
            }
        });
    });

});

/* ─── Submit filter form helper ─── */
function submitFilter() {
    var form  = document.getElementById('filterForm');
    var range = document.getElementById('dateRangePicker').value;
    document.getElementById('dateRangeInput').value = range;
    form.submit();
}
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

    {{-- ══════ TABS ══════ --}}
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

{{-- ══════ PAGE HEADER + STATS ROW ══════ --}}
@php
    $totalAll    = $designations->total();
    $monthPct    = $totalAll > 0 ? min(100, round(($thisMonthCount / $totalAll) * 100)) : 0;
    $sixMonthPct = $totalAll > 0 ? min(100, round(($lastSixMonthsCount / $totalAll) * 100)) : 0;
@endphp
<div class="desig-header-stats-row">
    <div class="desig-header-left">
        <h1 class="page-title-main">Designation Settings</h1>
        <p class="page-title-sub">Manage job titles and role descriptions</p>
    </div>
    <div class="dstat-grid">
        <div class="dstat-card c-blue">
            <div class="dstat-icon">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 7V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v2"/></svg>
            </div>
            <div class="dstat-info">
                <div class="dstat-value">{{ $totalAll }}</div>
                <div class="dstat-label">Total Designations</div>
            </div>
            <span class="dstat-trend">All Time</span>
        </div>
        {{--<div class="dstat-card c-green">
            <div class="dstat-icon">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
            </div>
            <div class="dstat-info">
                <div class="dstat-value">{{ $thisMonthCount }}</div>
                <div class="dstat-label">Added This Month</div>
            </div>
            <span class="dstat-trend">{{ now()->format('M Y') }}</span>
        </div>
        <div class="dstat-card c-amber">
            <div class="dstat-icon">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/></svg>
            </div>
            <div class="dstat-info">
                <div class="dstat-value">{{ $lastSixMonthsCount }}</div>
                <div class="dstat-label">Last 6 Months</div>
            </div>
            <span class="dstat-trend">6 Months</span>
        </div>--}}
    </div>
</div>

  
    {{-- ══════ FILTER BAR ══════ --}}
    {{-- Hidden form that actually submits --}}
    <form method="GET" action="{{ route('organization.settings.designation') }}" id="filterForm">
        @foreach(request()->except(['search', 'date_range', 'page']) as $k => $v)
            <input type="hidden" name="{{ $k }}" value="{{ $v }}">
        @endforeach
        {{-- These hidden inputs carry the real values into the form --}}
        <input type="hidden" name="search"     id="searchHidden"    value="{{ request('search') }}">
        <input type="hidden" name="date_range" id="dateRangeInput"  value="{{ request('date_range') }}">
    </form>

    <div class="filter-bar">

        {{-- Search: designation name OR description --}}
        <div class="filter-search-wrap">
            <span class="search-icon">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
            </span>
            <input type="text" id="searchInput"
                   placeholder="Search by name"
                   value="{{ request('search') }}"
                   autocomplete="off">
            <button type="button" class="search-btn" id="searchBtn" title="Search">
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
            </button>
        </div>

        <div class="filter-divider"></div>

        {{-- Flatpickr date range --}}
        <div class="filter-range-wrap" id="dateRangeWrap">
            <span class="cal-icon">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
            </span>
            <input type="text" id="dateRangePicker"
                   placeholder="Pick a date range…" readonly>
            <button type="button" class="clear-date-btn" id="clearDateBtn" title="Clear dates">×</button>
        </div>

        <div class="filter-divider"></div>

        {{-- Reset all --}}
        <a href="{{ route('organization.settings.designation') }}" class="btn-reset">
            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 12a9 9 0 1 0 9-9 9.75 9.75 0 0 0-6.74 2.74L3 8"/><path d="M3 3v5h5"/></svg>
            Reset
        </a>

        <div class="filter-spacer"></div>

        {{-- Add --}}
        <button type="button" class="btn-add" data-bs-toggle="modal" data-bs-target="#addDesignationModal">
            <span class="plus-circle">+</span> Add Designation
        </button>
    </div>

    {{-- Active filter chips --}}
    @if(request('search') || request('date_range'))
        <div class="active-filters">
            <span style="font-size:12px;color:var(--gray-400);font-weight:600;">Active filters:</span>
            @if(request('search'))
                <span class="filter-chip">
                    <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                    "{{ request('search') }}"
                    <a href="{{ request()->fullUrlWithQuery(['search' => null, 'page' => null]) }}" class="chip-remove">×</a>
                </span>
            @endif
            @if(request('date_range'))
                <span class="filter-chip">
                    <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                    {{ request('date_range') }}
                    <a href="{{ request()->fullUrlWithQuery(['date_range' => null, 'page' => null]) }}" class="chip-remove">×</a>
                </span>
            @endif
        </div>
    @endif

    {{-- ══════ TABLE ══════ --}}
    <div class="desig-table-card table-loading-overlay" id="tableWrapper">
        <div class="table-spinner-wrap">
            <div class="table-spinner"></div>
            <span class="table-spinner-text">Loading…</span>
        </div>
        <table>
            <thead>
                <tr>
                    <th style="width:44px;">#</th>
                    <th>Designation</th>
                    <th>Description</th>
                    <th>Created At</th>
                    <th style="text-align:right; padding-right:22px;">Actions</th>
                </tr>
            </thead>
            <tbody>
            @forelse ($designations as $i => $designation)
                @php
                    $words    = explode(' ', trim($designation->name));
                    $initials = strtoupper(implode('', array_map(fn($w) => $w[0] ?? '', $words)));
                    $initials = substr($initials, 0, 2);
                    $descFull  = $designation->description ?? '';
                    $descShort = \Illuminate\Support\Str::limit($descFull, 70);
                    $needsToggle = strlen($descFull) > 70;
                @endphp
                <tr>
                    <td><div class="row-num">{{ $designations->firstItem() + $i }}</div></td>
                    <td>
                        <div class="name-cell">
                            <div class="name-avatar">{{ $initials }}</div>
                            <span class="name-main">{{ $designation->name }}</span>
                        </div>
                    </td>
                    <td>
                        <div class="desc-wrap">
                            <span class="desc-short">{{ $descShort }}</span>
                            @if($needsToggle)
                                <span class="desc-full">{{ $descFull }}</span>
                                <button class="desc-toggle">Show more</button>
                            @endif
                        </div>
                    </td>
                    <td>
                        <span class="date-badge">
                            <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                            {{ $designation->created_at->format('d M Y') }}
                        </span>
                    </td>
                    <td>
                        <div style="display:flex;justify-content:flex-end;">
                            <button class="act-btn edit edit-btn"
                                    data-id="{{ $designation->id }}"
                                    data-name="{{ e($designation->name) }}"
                                    data-description="{{ e($designation->description) }}"
                                    data-bs-toggle="modal"
                                    data-bs-target="#editDesignationModal"
                                    title="Edit designation">
                                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                            </button>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5">
                        <div class="empty-state">
                            <div class="empty-icon-wrap">
                                <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 7V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v2"/></svg>
                            </div>
                            <p class="empty-title">No designations found</p>
                            <p class="empty-sub">
                                @if(request('search') || request('date_range'))
                                    No results match your current filters.
                                    <a href="{{ route('organization.settings.designation') }}" style="color:var(--primary);font-weight:600;">Clear all filters</a>
                                @else
                                    Get started by adding your first designation.
                                @endif
                            </p>
                            @if(!request('search') && !request('date_range'))
                                <button class="btn-add" data-bs-toggle="modal" data-bs-target="#addDesignationModal"
                                        style="margin:0 auto;">
                                    <span class="plus-circle">+</span> Add First Designation
                                </button>
                            @endif
                        </div>
                    </td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>

    {{-- ══════ PAGINATION ══════ --}}
    @if($designations->total() > 0)
    <div class="pagination-bar">
        <div class="pagi-info">
            Showing <span>{{ $designations->firstItem() }}–{{ $designations->lastItem() }}</span>
            of <span>{{ $designations->total() }}</span> designations
        </div>

        <ul class="pagi-pages">
            <li class="{{ $designations->onFirstPage() ? 'disabled' : '' }}">
                <a href="{{ $designations->url(1) }}" title="First">
                    <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="11 17 6 12 11 7"/><polyline points="18 17 13 12 18 7"/></svg>
                </a>
            </li>
            <li class="{{ $designations->onFirstPage() ? 'disabled' : '' }}">
                <a href="{{ $designations->previousPageUrl() }}" title="Previous">
                    <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"/></svg>
                </a>
            </li>
            @php
                $start = max(1, $designations->currentPage() - 2);
                $end   = min($start + 4, $designations->lastPage());
            @endphp
            @for ($p = $start; $p <= $end; $p++)
                <li class="{{ $designations->currentPage() == $p ? 'active_pagination' : '' }}">
                    <a href="{{ $designations->url($p) }}">{{ $p }}</a>
                </li>
            @endfor
            <li class="{{ !$designations->hasMorePages() ? 'disabled' : '' }}">
                <a href="{{ $designations->nextPageUrl() }}" title="Next">
                    <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"/></svg>
                </a>
            </li>
            <li class="{{ !$designations->hasMorePages() ? 'disabled' : '' }}">
                <a href="{{ $designations->url($designations->lastPage()) }}" title="Last">
                    <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="13 17 18 12 13 7"/><polyline points="6 17 11 12 6 7"/></svg>
                </a>
            </li>
        </ul>

        <div class="pagi-right">
            <form id="perPageForm" method="GET" action="{{ url()->current() }}" style="display:flex;align-items:center;">
                @foreach(request()->except(['page', 'per_page']) as $key => $value)
                    <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                @endforeach
                <div class="per-page-select">
                    <span>Rows:</span>
                    <select name="per_page" id="perPageSelect">
                        @foreach([5, 10, 20, 50] as $size)
                            <option value="{{ $size }}" {{ request('per_page', 10) == $size ? 'selected' : '' }}>{{ $size }}</option>
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
                    <input type="number" name="page" min="1" max="{{ $designations->lastPage() }}" placeholder="—">
                    <button type="submit">Go</button>
                </div>
            </form>
        </div>
    </div>
    @endif

    {{-- ══════ ADD MODAL ══════ --}}
    <div class="modal fade" id="addDesignationModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" style="max-width:440px;">
            <div class="modal-content">
                <div class="modal-top-band">
                    <button type="button" class="modal-close-btn" data-bs-dismiss="modal">
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                    </button>
                    <div class="modal-top-icon">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 7V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v2"/></svg>
                    </div>
                    <h5 class="modal-top-title">Add Designation</h5>
                    <p class="modal-top-sub">Create a new job title or role</p>
                </div>
                <div class="modal-body">
                    <form action="{{ route('organization.settings.designation.store') }}" method="POST" id="addDesignationForm">
                        @csrf
                        <div class="mb-3">
                            <label class="mform-label">Name <span style="color:#DC2626">*</span></label>
                            <input type="text" class="mform-input" name="name" placeholder="e.g. Senior Developer" autocomplete="off">
                            <div class="text-danger mt-1 small error-name"></div>
                        </div>
                        <hr class="mform-divider">
                        <div class="mb-3">
                            <label class="mform-label">Description <span style="color:#DC2626">*</span></label>
                            <textarea class="mform-textarea" name="description" placeholder="Describe the role and responsibilities…"></textarea>
                            <div class="text-danger mt-1 small error-description"></div>
                        </div>
                        <div class="modal-footer-btns">
                            <button type="button" class="btn-modal-cancel" data-bs-dismiss="modal">Cancel</button>
                            <button type="button" class="btn-modal-submit" id="submitAddDesignation">
                                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                                Create Designation
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- ══════ EDIT MODAL ══════ --}}
    <div class="modal fade" id="editDesignationModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" style="max-width:440px;">
            <div class="modal-content">
                <div class="modal-top-band green">
                    <button type="button" class="modal-close-btn" data-bs-dismiss="modal">
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                    </button>
                    <div class="modal-top-icon">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                    </div>
                    <h5 class="modal-top-title">Edit Designation</h5>
                    <p class="modal-top-sub">Update the designation details</p>
                </div>
                <div class="modal-body">
                    <form method="POST" id="editDesignationForm">
                        @csrf
                        @method('PUT')
                        <div class="mb-3">
                            <label class="mform-label">Name <span style="color:#DC2626">*</span></label>
                            <input type="text" class="mform-input" name="name" id="editName" placeholder="e.g. Senior Developer" autocomplete="off">
                            <div class="text-danger mt-1 small" id="editerror-name"></div>
                        </div>
                        <hr class="mform-divider">
                        <div class="mb-3">
                            <label class="mform-label">Description <span style="color:#DC2626">*</span></label>
                            <textarea class="mform-textarea" name="description" id="editDescription" placeholder="Describe the role…"></textarea>
                            <div class="text-danger mt-1 small" id="editerror-description"></div>
                        </div>
                        <div class="modal-footer-btns">
                            <button type="button" class="btn-modal-cancel" data-bs-dismiss="modal">Cancel</button>
                            <button type="button" class="btn-modal-submit green" id="submitEditDesignation">
                                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
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