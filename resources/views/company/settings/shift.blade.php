@extends('company.layouts.company')
@section('page-title') {{ __('Shift Settings') }} @endsection
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
.shift-header-stats-row { display:flex; align-items:center; justify-content:space-between; gap:20px; margin-bottom:20px; flex-wrap:wrap; }
.shift-header-left { flex-shrink:0; }
.page-title-main { font-size:22px; font-weight:800; color:var(--gray-900); margin:0 0 4px; letter-spacing:-.4px; }
.page-title-sub { font-size:13px; color:var(--gray-400); margin:0; }

/* ── COMPACT STATS ── */
.sstat-grid { display:flex; align-items:center; gap:12px; flex-wrap:wrap; flex:1; justify-content:flex-end; }
.sstat-card { background:#fff; border-radius:var(--radius-md); padding:10px 16px; border:1px solid var(--gray-200); box-shadow:var(--shadow-sm); transition:transform .2s,box-shadow .2s; display:flex; align-items:center; gap:12px; flex:1; min-width:140px; max-width:255px; }
.sstat-card:hover { transform:translateY(-2px); box-shadow:var(--shadow-md); }
.sstat-card.c-blue  { border-left:3px solid var(--primary); }
.sstat-card.c-green { border-left:3px solid var(--success); }
.sstat-card.c-amber { border-left:3px solid var(--warning); }
.sstat-icon { width:34px; height:34px; flex-shrink:0; border-radius:var(--radius-sm); display:flex; align-items:center; justify-content:center; }
.sstat-card.c-blue  .sstat-icon { background:var(--primary-light); color:var(--primary); }
.sstat-card.c-green .sstat-icon { background:var(--success-light); color:var(--success); }
.sstat-card.c-amber .sstat-icon { background:var(--warning-light); color:var(--warning); }
.sstat-info { flex:1; min-width:0; }
.sstat-value { font-size:20px; font-weight:800; color:var(--gray-900); line-height:1; letter-spacing:-.5px; margin-bottom:2px; }
.sstat-label { font-size:10px; font-weight:600; color:var(--gray-400); text-transform:uppercase; letter-spacing:.4px; white-space:nowrap; }
.sstat-trend { font-size:10px; font-weight:700; padding:2px 8px; border-radius:var(--radius-full); flex-shrink:0; }
.sstat-card.c-blue  .sstat-trend { background:var(--primary-light); color:var(--primary); }
.sstat-card.c-green .sstat-trend { background:var(--success-light); color:var(--success); }
.sstat-card.c-amber .sstat-trend { background:var(--warning-light); color:var(--warning); }

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
.btn-add { display:flex; align-items:center; gap:8px; height:42px; padding:0 20px; border-radius:var(--radius-full); border:none; background:var(--primary); color:#fff; font-size:13px; font-weight:700; cursor:pointer; transition:all .18s; white-space:nowrap; box-shadow:0 2px 8px rgba(37,99,235,.28); }
.btn-add:hover { background:var(--primary-dark); box-shadow:0 4px 16px rgba(37,99,235,.36); transform:translateY(-1px); }
.btn-add .plus-circle { width:20px; height:20px; background:rgba(255,255,255,.2); border-radius:50%; display:flex; align-items:center; justify-content:center; flex-shrink:0; }

/* Active filter chips */
.active-filters { display:flex; align-items:center; gap:6px; flex-wrap:wrap; padding:0 4px 12px; }
.filter-chip { display:inline-flex; align-items:center; gap:6px; background:var(--primary-light); border:1px solid var(--primary-mid); color:var(--primary); border-radius:var(--radius-full); padding:4px 12px; font-size:12px; font-weight:600; }
.filter-chip .chip-remove { background:none; border:none; color:var(--primary); cursor:pointer; font-size:14px; line-height:1; opacity:.6; transition:opacity .15s; padding:0; display:flex; align-items:center; }
.filter-chip .chip-remove:hover { opacity:1; }

/* ── TABLE ── */
.shift-table-card { background:#fff; border-radius:var(--radius-lg); border:1px solid var(--gray-200); overflow:hidden; box-shadow:var(--shadow-sm); }
.shift-table-card table { width:100%; border-collapse:collapse; }
.shift-table-card thead tr { background:var(--gray-50); border-bottom:2px solid var(--gray-200); }
.shift-table-card th { padding:13px 18px; font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:.7px; color:var(--gray-400); white-space:nowrap; }
.shift-table-card td { padding:15px 18px; border-bottom:1px solid var(--gray-100); vertical-align:middle; }
.shift-table-card tbody tr:last-child td { border-bottom:none; }
.shift-table-card tbody tr { transition:background .12s; }
.shift-table-card tbody tr:hover { background:#FAFBFF; }
.row-num { width:28px; height:28px; border-radius:var(--radius-sm); background:var(--gray-100); display:flex; align-items:center; justify-content:center; font-size:11px; font-weight:700; color:var(--gray-400); }
.name-cell { display:flex; align-items:center; gap:12px; }
.name-avatar { width:38px; height:38px; border-radius:10px; background:linear-gradient(135deg,#EFF6FF,#BFDBFE); display:flex; align-items:center; justify-content:center; font-size:12px; font-weight:800; color:var(--primary); flex-shrink:0; text-transform:uppercase; border:1.5px solid var(--primary-mid); }
.name-main { font-size:14px; font-weight:700; color:var(--gray-900); }
.time-badge { display:inline-flex; align-items:center; gap:5px; background:var(--gray-50); border:1px solid var(--gray-200); border-radius:var(--radius-full); padding:5px 12px; font-size:12px; font-weight:600; color:var(--gray-700); white-space:nowrap; }
.tz-badge { display:inline-flex; align-items:center; gap:5px; background:var(--primary-light); border:1px solid var(--primary-mid); border-radius:var(--radius-full); padding:4px 10px; font-size:11px; font-weight:700; color:var(--primary); }
.weekoff-wrap { display:flex; flex-wrap:wrap; gap:4px; max-width:200px; }
.day-chip { font-size:10px; font-weight:700; padding:2px 8px; border-radius:var(--radius-full); background:var(--warning-light); color:var(--warning); border:1px solid #FDE68A; white-space:nowrap; }
.act-btn { width:34px; height:34px; border-radius:var(--radius-sm); border:1px solid var(--gray-200); background:#fff; cursor:pointer; display:flex; align-items:center; justify-content:center; transition:all .15s; color:var(--gray-400); }
.act-btn.edit:hover { border-color:var(--primary); color:var(--primary); background:var(--primary-light); transform:scale(1.06); }

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

/* ── MODAL ── */
.modal-content { border-radius:var(--radius-xl) !important; border:none !important; box-shadow:var(--shadow-lg) !important; overflow:hidden; }
.modal-top-band { background:linear-gradient(135deg,var(--primary) 0%,#1D4ED8 100%); padding:22px 24px 20px; position:relative; overflow:hidden; }
.modal-top-band.green { background:linear-gradient(135deg,#059669,#047857); }
.modal-top-band::before { content:''; position:absolute; top:-30px; right:-30px; width:120px; height:120px; border-radius:50%; background:rgba(255,255,255,.08); }
.modal-top-band::after { content:''; position:absolute; bottom:-20px; left:20px; width:80px; height:80px; border-radius:50%; background:rgba(255,255,255,.05); }
.modal-top-icon { width:44px; height:44px; background:rgba(255,255,255,.15); border-radius:var(--radius-md); display:flex; align-items:center; justify-content:center; margin-bottom:10px; border:1px solid rgba(255,255,255,.2); }
.modal-top-title { font-size:18px; font-weight:800; color:#fff; margin:0 0 3px; letter-spacing:-.3px; }
.modal-top-sub { font-size:12.5px; color:rgba(255,255,255,.7); margin:0; }
.modal-close-btn { position:absolute; top:16px; right:16px; width:30px; height:30px; border-radius:50%; background:rgba(255,255,255,.15); border:1px solid rgba(255,255,255,.2); color:#fff; display:flex; align-items:center; justify-content:center; cursor:pointer; font-size:13px; transition:background .15s; }
.modal-close-btn:hover { background:rgba(255,255,255,.28); }
.modal-body { padding:22px 24px !important; }
.mform-label { font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:.6px; color:var(--gray-400); margin-bottom:7px; display:block; }
.mform-input { width:100%; height:46px; border:1.5px solid var(--gray-200) !important; border-radius:var(--radius-md) !important; padding:0 14px !important; font-size:14px !important; color:var(--gray-900); background:var(--gray-50); outline:none; transition:all .18s; }
.mform-input:focus { border-color:var(--primary) !important; background:#fff; box-shadow:0 0 0 3px rgba(37,99,235,.08) !important; }
.mform-input[readonly] { background:var(--gray-100) !important; color:var(--gray-500); cursor:not-allowed; }
.input-suffix-wrap { display:flex; border:1.5px solid var(--gray-200); border-radius:var(--radius-md); overflow:hidden; background:var(--gray-50); transition:all .18s; }
.input-suffix-wrap:focus-within { border-color:var(--primary); background:#fff; box-shadow:0 0 0 3px rgba(37,99,235,.08); }
.input-suffix-wrap input { flex:1; height:46px; border:none; outline:none; padding:0 14px; font-size:14px; background:transparent; color:var(--gray-900); }
.input-suffix-wrap .suffix { height:46px; background:var(--primary-light); color:var(--primary); font-weight:700; font-size:12px; display:flex; align-items:center; padding:0 14px; border-left:1.5px solid var(--primary-mid); letter-spacing:.5px; white-space:nowrap; }
.mform-divider { border:none; border-top:1px solid var(--gray-100); margin:16px 0; }

/* Weekday checkboxes */
.weekday-grid { display:grid; grid-template-columns:repeat(4, 1fr); gap:8px; }
.weekday-item { display:flex; align-items:center; gap:8px; padding:10px 12px; background:var(--gray-50); border:1.5px solid var(--gray-200); border-radius:var(--radius-md); cursor:pointer; transition:all .15s; }
.weekday-item:hover { border-color:var(--warning); background:var(--warning-light); }
.weekday-item input[type=checkbox] { width:15px; height:15px; accent-color:var(--warning); cursor:pointer; flex-shrink:0; }
.weekday-item label { font-size:12.5px; font-weight:600; color:var(--gray-600); cursor:pointer; margin:0; }
.weekday-item:has(input:checked) { border-color:var(--warning); background:var(--warning-light); }
.weekday-item:has(input:checked) label { color:var(--warning); }

.modal-footer-btns { display:flex; gap:10px; justify-content:flex-end; padding-top:8px; }
.btn-modal-cancel { height:42px; padding:0 20px; border:1.5px solid var(--gray-200); border-radius:var(--radius-full); background:#fff; color:var(--gray-500); font-size:13.5px; font-weight:600; cursor:pointer; transition:all .15s; }
.btn-modal-cancel:hover { border-color:var(--gray-300); color:var(--gray-700); }
.btn-modal-submit { height:42px; padding:0 24px; border:none; border-radius:var(--radius-full); background:var(--primary); color:#fff; font-size:13.5px; font-weight:700; cursor:pointer; transition:all .18s; box-shadow:0 2px 8px rgba(37,99,235,.28); display:flex; align-items:center; gap:6px; }
.btn-modal-submit:hover { background:var(--primary-dark); transform:translateY(-1px); box-shadow:0 4px 14px rgba(37,99,235,.36); }
.btn-modal-submit.green { background:linear-gradient(135deg,#059669,#047857); box-shadow:0 2px 8px rgba(5,150,105,.28); }
.btn-modal-submit.green:hover { box-shadow:0 4px 14px rgba(5,150,105,.36); }

@media (max-width:768px) {
    .shift-header-stats-row { flex-direction:column; align-items:flex-start; }
    .sstat-grid { justify-content:flex-start; width:100%; }
    .sstat-card { max-width:unset; }
    .pagination-bar { flex-direction:column; align-items:flex-start; }
    .weekday-grid { grid-template-columns:repeat(2, 1fr); }
    .settings-tab-bar { gap:3px; }
    .stab { padding:8px 12px; font-size:12px; }
}
</style>
@endpush

@push('script-page')
<script>
document.addEventListener('DOMContentLoaded', function () {

    /* ── Search: button click or Enter ── */
    var searchInput = document.getElementById('searchInput');
    var searchBtn   = document.getElementById('searchBtn');
    var filterForm  = document.getElementById('filterForm');

    function doSearch() {
        document.getElementById('searchHidden').value = searchInput.value;
        filterForm.submit();
    }
    if (searchBtn)   searchBtn.addEventListener('click', doSearch);
    if (searchInput) searchInput.addEventListener('keydown', function(e){
        if (e.key === 'Enter') { e.preventDefault(); doSearch(); }
    });

    /* ── Per-page ── */
    var perPageSel = document.getElementById('perPageSelect');
    if (perPageSel) perPageSel.addEventListener('change', function(){
        document.getElementById('perPageForm').submit();
    });

    /* ── Edit modal population ── */
    document.querySelectorAll('.edit-shift-btn').forEach(function(btn){
        btn.addEventListener('click', function(){
            var id      = this.dataset.id;
            var weekoff = this.dataset.weekoff.split(',');

            // Set action URL
            document.getElementById('editShiftForm').action =
                "{{ route('organization.settings.shift.update', ':id') }}".replace(':id', id);

            // Fill inputs
            document.querySelector('#editShiftForm input[name="shift_name"]').value  = this.dataset.name;
            document.querySelector('#editShiftForm input[name="timezone"]').value    = this.dataset.timezone;
            document.querySelector('#editShiftForm input[name="start_time"]').value  = this.dataset.start;
            document.querySelector('#editShiftForm input[name="end_time"]').value    = this.dataset.end;
            document.querySelector('#editShiftForm input[name="grace_period"]').value = this.dataset.grace;
            document.querySelector('#editShiftForm input[name="max_break_time"]').value = this.dataset.break;

            // Reset + check weekoff
            document.querySelectorAll('#editShiftForm input[type="checkbox"][name="week_off[]"]').forEach(cb => cb.checked = false);
            weekoff.forEach(function(day){
                document.querySelectorAll('#editShiftForm input[type="checkbox"][name="week_off[]"]').forEach(cb => {
                    if (cb.value.trim() === day.trim()) cb.checked = true;
                });
            });
        });
    });
});

/* ── AJAX: Add Shift ── */
$(document).ready(function(){
    $('#saveShiftBtn').click(function(e){
        e.preventDefault();
        var form = $('#addShiftForm');
        $('.text-danger').text('');
        $.ajax({
            url: form.attr('action'), type: 'POST', data: form.serialize(),
            success: function(){
                $('#addShiftModal').modal('hide');
                show_toastr('Success', 'Shift created successfully.', 'success');
                setTimeout(function(){ location.reload(); }, 900);
            },
            error: function(xhr){
                if (xhr.status === 422) {
                    $.each(xhr.responseJSON.errors, function(key, value){
                        $('#error-' + key).text(value[0]);
                    });
                } else { show_toastr('Error', 'An unexpected error occurred.', 'error'); }
            }
        });
    });
});

/* ── AJAX: Edit Shift ── */
$(document).ready(function(){
    $('#editShiftSubmitBtn').on('click', function(){
        var form = $('#editShiftForm');
        $('[id^=editerror-]').text('');
        $.ajax({
            type: 'POST', url: form.attr('action'), data: form.serialize(),
            success: function(){
                $('#editShiftModal').modal('hide');
                show_toastr('Success', 'Shift updated successfully.', 'success');
                setTimeout(function(){ location.reload(); }, 900);
            },
            error: function(xhr){
                if (xhr.status === 422) {
                    $.each(xhr.responseJSON.errors, function(key, messages){
                        $('#editerror-' + key).text(messages[0]);
                    });
                } else { show_toastr('Error', 'Something went wrong.', 'error'); }
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
        $totalShifts     = $shifts->total();
        $thisMonthShifts = \App\Models\Shift::where('created_by', auth()->user()->creatorId())->whereMonth('created_at', now()->month)->whereYear('created_at', now()->year)->count();
        $lastSixShifts   = \App\Models\Shift::where('created_by', auth()->user()->creatorId())->where('created_at', '>=', now()->subMonths(6))->count();
    @endphp
    <div class="shift-header-stats-row">
        <div class="shift-header-left">
            <h1 class="page-title-main">Shift Settings</h1>
            <p class="page-title-sub">Configure work schedules and weekly off patterns</p>
        </div>
        <div class="sstat-grid">
            <div class="sstat-card c-blue">
                <div class="sstat-icon">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                </div>
                <div class="sstat-info">
                    <div class="sstat-value">{{ $totalShifts }}</div>
                    <div class="sstat-label">Total Shifts</div>
                </div>
                <span class="sstat-trend">All Time</span>
            </div>
            {{--<div class="sstat-card c-green">
                <div class="sstat-icon">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                </div>
                <div class="sstat-info">
                    <div class="sstat-value">{{ $thisMonthShifts }}</div>
                    <div class="sstat-label">Added This Month</div>
                </div>
                <span class="sstat-trend">{{ now()->format('M Y') }}</span>
            </div>
            <div class="sstat-card c-amber">
                <div class="sstat-icon">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/></svg>
                </div>
                <div class="sstat-info">
                    <div class="sstat-value">{{ $lastSixShifts }}</div>
                    <div class="sstat-label">Last 6 Months</div>
                </div>
                <span class="sstat-trend">6 Months</span>
            </div>--}}
        </div>
    </div>

    {{-- FILTER FORM --}}
    <form method="GET" action="{{ route('organization.settings.shift') }}" id="filterForm">
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
            <input type="text" id="searchInput" placeholder="Search by shift name…" value="{{ request('search') }}" autocomplete="off">
            <button type="button" class="search-btn" id="searchBtn" title="Search">
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
            </button>
        </div>
        <div class="filter-divider"></div>
        <a href="{{ route('organization.settings.shift') }}" class="btn-reset">
            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 12a9 9 0 1 0 9-9 9.75 9.75 0 0 0-6.74 2.74L3 8"/><path d="M3 3v5h5"/></svg>
            Reset
        </a>
        <div class="filter-spacer"></div>
        <button type="button" class="btn-add" data-bs-toggle="modal" data-bs-target="#addShiftModal">
            <span class="plus-circle">+</span> Add Shift
        </button>
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
    <div class="shift-table-card">
        <table>
            <thead>
                <tr>
                    <th style="width:44px;">#</th>
                    <th>Shift Name</th>
                    <th>Timezone</th>
                    <th>Start / End</th>
                    <th>Grace Period</th>
                    <th>Max Break</th>
                    <th>Week Off</th>
                    <th style="text-align:right; padding-right:22px;">Actions</th>
                </tr>
            </thead>
            <tbody>
            @forelse ($shifts as $i => $shift)
                @php
                    $words    = explode(' ', trim($shift->shift_name));
                    $initials = strtoupper(implode('', array_map(fn($w) => $w[0] ?? '', $words)));
                    $initials = substr($initials, 0, 2);
                    $days     = array_filter(array_map('trim', explode(',', $shift->week_off)));
                @endphp
                <tr>
                    <td><div class="row-num">{{ $shifts->firstItem() + $i }}</div></td>
                    <td>
                        <div class="name-cell">
                            <div class="name-avatar">{{ $initials }}</div>
                            <span class="name-main">{{ $shift->shift_name }}</span>
                        </div>
                    </td>
                    <td><span class="tz-badge">{{ $shift->timezone }}</span></td>
                    <td>
                        <div style="display:flex; flex-direction:column; gap:4px;">
                            <span class="time-badge">
                                <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                                {{ \Carbon\Carbon::parse($shift->start_time)->format('H:i') }} → {{ \Carbon\Carbon::parse($shift->end_time)->format('H:i') }}
                            </span>
                        </div>
                    </td>
                    <td>
                        <span class="time-badge">{{ $shift->grace_period }} min</span>
                    </td>
                    <td>
                        <span class="time-badge">{{ $shift->max_break_time }} min</span>
                    </td>
                    <td>
                        <div class="weekoff-wrap">
                            @forelse($days as $day)
                                <span class="day-chip">{{ substr($day, 0, 3) }}</span>
                            @empty
                                <span style="font-size:12px;color:var(--gray-300);font-weight:600;">None</span>
                            @endforelse
                        </div>
                    </td>
                    <td>
                        <div style="display:flex; justify-content:flex-end;">
                            <button class="act-btn edit edit-shift-btn"
                                    data-id="{{ $shift->id }}"
                                    data-name="{{ e($shift->shift_name) }}"
                                    data-timezone="{{ $shift->timezone }}"
                                    data-start="{{ $shift->start_time }}"
                                    data-end="{{ $shift->end_time }}"
                                    data-grace="{{ $shift->grace_period }}"
                                    data-break="{{ $shift->max_break_time }}"
                                    data-weekoff="{{ $shift->week_off }}"
                                    data-bs-toggle="modal" data-bs-target="#editShiftModal"
                                    title="Edit shift">
                                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                            </button>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8">
                        <div class="empty-state">
                            <div class="empty-icon-wrap">
                                <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                            </div>
                            <p class="empty-title">No shifts found</p>
                            <p class="empty-sub">
                                @if(request('search'))
                                    No results match "<strong>{{ request('search') }}</strong>".
                                    <a href="{{ route('organization.settings.shift') }}" style="color:var(--primary);font-weight:600;">Clear filter</a>
                                @else
                                    Get started by adding your first shift.
                                @endif
                            </p>
                            @if(!request('search'))
                                <button class="btn-add" data-bs-toggle="modal" data-bs-target="#addShiftModal" style="margin:0 auto;">
                                    <span class="plus-circle">+</span> Add First Shift
                                </button>
                            @endif
                        </div>
                    </td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>

    {{-- PAGINATION --}}
    @if($shifts->total() > 0)
    <div class="pagination-bar">
        <div class="pagi-info">
            Showing <span>{{ $shifts->firstItem() }}–{{ $shifts->lastItem() }}</span> of <span>{{ $shifts->total() }}</span> shifts
        </div>
        <ul class="pagi-pages">
            <li class="{{ $shifts->onFirstPage() ? 'disabled' : '' }}">
                <a href="{{ $shifts->url(1) }}"><svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="11 17 6 12 11 7"/><polyline points="18 17 13 12 18 7"/></svg></a>
            </li>
            <li class="{{ $shifts->onFirstPage() ? 'disabled' : '' }}">
                <a href="{{ $shifts->previousPageUrl() }}"><svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"/></svg></a>
            </li>
            @php $start = max(1,$shifts->currentPage()-2); $end = min($start+4,$shifts->lastPage()); @endphp
            @for($p=$start; $p<=$end; $p++)
                <li class="{{ $shifts->currentPage()==$p ? 'active_pagination' : '' }}">
                    <a href="{{ $shifts->url($p) }}">{{ $p }}</a>
                </li>
            @endfor
            <li class="{{ !$shifts->hasMorePages() ? 'disabled' : '' }}">
                <a href="{{ $shifts->nextPageUrl() }}"><svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"/></svg></a>
            </li>
            <li class="{{ !$shifts->hasMorePages() ? 'disabled' : '' }}">
                <a href="{{ $shifts->url($shifts->lastPage()) }}"><svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="13 17 18 12 13 7"/><polyline points="6 17 11 12 6 7"/></svg></a>
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
                    <input type="number" name="page" min="1" max="{{ $shifts->lastPage() }}" placeholder="—">
                    <button type="submit">Go</button>
                </div>
            </form>
        </div>
    </div>
    @endif

    {{-- ══ ADD SHIFT MODAL ══ --}}
    <div class="modal fade" id="addShiftModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-top-band">
                    <button type="button" class="modal-close-btn" data-bs-dismiss="modal">
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                    </button>
                    <div class="modal-top-icon">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                    </div>
                    <h5 class="modal-top-title">Add Shift</h5>
                    <p class="modal-top-sub">Configure a new work schedule</p>
                </div>
                <div class="modal-body">
                    <form method="POST" action="{{ route('organization.settings.shift.store') }}" id="addShiftForm">
                        @csrf
                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="mform-label">Shift Name <span style="color:#DC2626">*</span></label>
                                <input type="text" class="mform-input" name="shift_name" placeholder="e.g. Morning Shift" autocomplete="off">
                                <div class="text-danger mt-1 small" id="error-shift_name"></div>
                            </div>
                            <div class="col-md-6">
                                <label class="mform-label">Timezone <span style="color:#DC2626">*</span></label>
                                <input type="text" class="mform-input" name="timezone" value="IST" readonly>
                                <div class="text-danger mt-1 small" id="error-timezone"></div>
                            </div>
                        </div>
                        <div class="row g-3 mb-3">
                            <div class="col-md-4">
                                <label class="mform-label">Start Time <span style="color:#DC2626">*</span></label>
                                <input type="time" class="mform-input" name="start_time">
                                <div class="text-danger mt-1 small" id="error-start_time"></div>
                            </div>
                            <div class="col-md-4">
                                <label class="mform-label">End Time <span style="color:#DC2626">*</span></label>
                                <input type="time" class="mform-input" name="end_time">
                                <div class="text-danger mt-1 small" id="error-end_time"></div>
                            </div>
                            <div class="col-md-4">
                                <label class="mform-label">Grace Period <span style="color:#DC2626">*</span></label>
                                <div class="input-suffix-wrap">
                                    <input type="number" name="grace_period" placeholder="15" min="1">
                                    <span class="suffix">MIN</span>
                                </div>
                                <div class="text-danger mt-1 small" id="error-grace_period"></div>
                            </div>
                        </div>
                        <div class="row g-3 mb-3">
                            <div class="col-md-4">
                                <label class="mform-label">Max Break Time <span style="color:#DC2626">*</span></label>
                                <div class="input-suffix-wrap">
                                    <input type="number" name="max_break_time" placeholder="30" min="1">
                                    <span class="suffix">MIN</span>
                                </div>
                                <div class="text-danger mt-1 small" id="error-max_break_time"></div>
                            </div>
                        </div>
                        <hr class="mform-divider">
                        <label class="mform-label" style="margin-bottom:12px;">Weekly Off Days</label>
                        <div class="weekday-grid">
                            @foreach (['Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday'] as $day)
                                <label class="weekday-item">
                                    <input type="checkbox" name="week_off[]" value="{{ $day }}">
                                    <label style="pointer-events:none;">{{ $day }}</label>
                                </label>
                            @endforeach
                        </div>
                        <div class="text-danger mt-1 small" id="error-week_off"></div>
                        <div class="modal-footer-btns mt-4">
                            <button type="button" class="btn-modal-cancel" data-bs-dismiss="modal">Cancel</button>
                            <button type="button" class="btn-modal-submit" id="saveShiftBtn">
                                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                                Create Shift
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- ══ EDIT SHIFT MODAL ══ --}}
    <div class="modal fade" id="editShiftModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-top-band green">
                    <button type="button" class="modal-close-btn" data-bs-dismiss="modal">
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                    </button>
                    <div class="modal-top-icon">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                    </div>
                    <h5 class="modal-top-title">Edit Shift</h5>
                    <p class="modal-top-sub">Update the shift configuration</p>
                </div>
                <div class="modal-body">
                    <form id="editShiftForm" method="POST" action="">
                        @csrf @method('POST')
                        <input type="hidden" name="id" id="editShiftId" value="">
                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="mform-label">Shift Name <span style="color:#DC2626">*</span></label>
                                <input type="text" class="mform-input" name="shift_name" placeholder="e.g. Morning Shift">
                                <div class="text-danger mt-1 small" id="editerror-shift_name"></div>
                            </div>
                            <div class="col-md-6">
                                <label class="mform-label">Timezone <span style="color:#DC2626">*</span></label>
                                <input type="text" class="mform-input" name="timezone" value="IST" readonly>
                            </div>
                        </div>
                        <div class="row g-3 mb-3">
                            <div class="col-md-4">
                                <label class="mform-label">Start Time <span style="color:#DC2626">*</span></label>
                                <input type="time" class="mform-input" name="start_time">
                                <div class="text-danger mt-1 small" id="editerror-start_time"></div>
                            </div>
                            <div class="col-md-4">
                                <label class="mform-label">End Time <span style="color:#DC2626">*</span></label>
                                <input type="time" class="mform-input" name="end_time">
                                <div class="text-danger mt-1 small" id="editerror-end_time"></div>
                            </div>
                            <div class="col-md-4">
                                <label class="mform-label">Grace Period <span style="color:#DC2626">*</span></label>
                                <div class="input-suffix-wrap">
                                    <input type="number" name="grace_period" placeholder="15" min="1">
                                    <span class="suffix">MIN</span>
                                </div>
                                <div class="text-danger mt-1 small" id="editerror-grace_period"></div>
                            </div>
                        </div>
                        <div class="row g-3 mb-3">
                            <div class="col-md-4">
                                <label class="mform-label">Max Break Time <span style="color:#DC2626">*</span></label>
                                <div class="input-suffix-wrap">
                                    <input type="number" name="max_break_time" placeholder="30" min="1">
                                    <span class="suffix">MIN</span>
                                </div>
                                <div class="text-danger mt-1 small" id="editerror-max_break_time"></div>
                            </div>
                        </div>
                        <hr class="mform-divider">
                        <label class="mform-label" style="margin-bottom:12px;">Weekly Off Days</label>
                        <div class="weekday-grid">
                            @foreach (['Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday'] as $day)
                                <label class="weekday-item">
                                    <input type="checkbox" name="week_off[]" value="{{ $day }}">
                                    <label style="pointer-events:none;">{{ $day }}</label>
                                </label>
                            @endforeach
                        </div>
                        <div class="text-danger mt-1 small" id="editerror-week_off"></div>
                        <div class="modal-footer-btns mt-4">
                            <button type="button" class="btn-modal-cancel" data-bs-dismiss="modal">Cancel</button>
                            <button type="button" class="btn-modal-submit green" id="editShiftSubmitBtn">
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