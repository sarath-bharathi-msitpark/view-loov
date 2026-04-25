@extends('company.layouts.company')
@section('page-title') {{ __('Team Settings') }} @endsection
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

/* ── TABS BAR ── */
.settings-tab-bar { display:flex; align-items:center; gap:4px; background:var(--gray-100); border-radius:var(--radius-lg); padding:5px; flex-wrap:wrap; margin-bottom:28px; border:1px solid var(--gray-200); }
.settings-tab-bar a { text-decoration:none; flex:1; min-width:90px; }
.stab { display:flex; align-items:center; justify-content:center; gap:7px; padding:9px 16px; border-radius:var(--radius-md); font-size:13px; font-weight:600; color:var(--gray-500); transition:all .2s; white-space:nowrap; cursor:pointer; }
.stab:hover { background:#fff; color:var(--gray-700); box-shadow:var(--shadow-sm); }
.stab.active { background:#fff; color:var(--primary); box-shadow:var(--shadow-sm); border:1px solid var(--primary-mid); }
.stab img { width:15px; height:15px; opacity:.5; transition:opacity .2s; filter:grayscale(1); }
.stab.active img, .stab:hover img { opacity:1; filter:none; }

/* ── PAGE HEADER ROW ── */
.team-header-row { display:flex; align-items:center; justify-content:space-between; gap:16px; margin-bottom:20px; flex-wrap:wrap; }
.page-title-main { font-size:22px; font-weight:800; color:var(--gray-900); margin:0 0 4px; letter-spacing:-.4px; }
.page-title-sub { font-size:13px; color:var(--gray-400); margin:0; }

/* User limit badge */
.user-limit-badge { display:flex; align-items:center; gap:12px; background:#fff; border:1px solid var(--gray-200); border-radius:var(--radius-md); padding:12px 18px; box-shadow:var(--shadow-sm); }
.user-limit-text h6 { font-size:13px; font-weight:700; color:var(--gray-700); margin:0 0 2px; }
.user-limit-text p { font-size:11px; color:var(--gray-400); margin:0; }
.user-limit-count { background:var(--primary); color:#fff; border-radius:var(--radius-md); padding:8px 16px; font-size:14px; font-weight:800; white-space:nowrap; }
.user-limit-count.warning { background:var(--warning); }
.user-limit-count.danger { background:var(--danger); }

.btn-add { display:inline-flex; align-items:center; gap:8px; height:42px; padding:0 20px; border-radius:var(--radius-full); border:none; background:var(--primary); color:#fff; font-size:13px; font-weight:700; cursor:pointer; transition:all .18s; white-space:nowrap; box-shadow:0 2px 8px rgba(37,99,235,.28); text-decoration:none; }
.btn-add:hover { background:var(--primary-dark); box-shadow:0 4px 16px rgba(37,99,235,.36); transform:translateY(-1px); color:#fff; }
.btn-add .plus-circle { width:20px; height:20px; background:rgba(255,255,255,.2); border-radius:50%; display:flex; align-items:center; justify-content:center; flex-shrink:0; }
.btn-add.disabled-btn { opacity:.6; cursor:not-allowed; transform:none !important; }

/* ── MAIN LAYOUT ── */
.team-layout { display:grid; grid-template-columns: 300px 1fr; gap:20px; align-items:start; }
@media (max-width:900px) { .team-layout { grid-template-columns:1fr; } }

/* ── LEFT PANEL: TEAM LIST ── */
.team-list-panel { background:#fff; border:1px solid var(--gray-200); border-radius:var(--radius-lg); box-shadow:var(--shadow-sm); overflow:hidden; }
.team-list-search { padding:14px 14px 10px; border-bottom:1px solid var(--gray-100); }
.team-search-wrap { display:flex; align-items:center; background:var(--gray-50); border:1.5px solid var(--gray-200); border-radius:var(--radius-full); padding:0 8px 0 14px; transition:border-color .18s,box-shadow .18s; }
.team-search-wrap:focus-within { border-color:var(--primary); box-shadow:0 0 0 3px rgba(37,99,235,.08); background:#fff; }
.team-search-wrap input { border:none; background:transparent; outline:none; font-size:13px; color:var(--gray-700); width:100%; padding:9px 0; }
.team-search-wrap input::placeholder { color:var(--gray-400); }
.team-search-btn { width:30px; height:30px; border-radius:var(--radius-full); background:var(--primary); border:none; color:#fff; cursor:pointer; display:flex; align-items:center; justify-content:center; flex-shrink:0; transition:background .15s; }
.team-search-btn:hover { background:var(--primary-dark); }
.team-list-scroll { max-height:420px; overflow-y:auto; }
.team-item { display:flex; align-items:center; gap:12px; padding:13px 16px; border-bottom:1px solid var(--gray-100); cursor:pointer; transition:background .12s; text-decoration:none; }
.team-item:last-child { border-bottom:none; }
.team-item:hover { background:var(--gray-50); }
.team-item.active { background:var(--primary-light); border-left:3px solid var(--primary); }
.team-item-avatar { width:36px; height:36px; border-radius:10px; background:linear-gradient(135deg,#EFF6FF,#BFDBFE); display:flex; align-items:center; justify-content:center; font-size:12px; font-weight:800; color:var(--primary); flex-shrink:0; text-transform:uppercase; border:1.5px solid var(--primary-mid); }
.team-item.active .team-item-avatar { background:var(--primary); color:#fff; border-color:var(--primary-dark); }
.team-item-name { font-size:13.5px; font-weight:600; color:var(--gray-700); }
.team-item.active .team-item-name { color:var(--primary); font-weight:700; }
.team-empty { text-align:center; padding:40px 20px; }
.team-empty-icon { font-size:28px; color:var(--gray-300); margin-bottom:8px; }
.team-empty-text { font-size:13px; color:var(--gray-400); font-weight:500; }

/* ── RIGHT PANEL: TEAM DETAIL ── */
.team-detail-panel { background:#fff; border:1px solid var(--gray-200); border-radius:var(--radius-lg); box-shadow:var(--shadow-sm); overflow:hidden; }
.team-detail-header { display:flex; align-items:center; justify-content:space-between; padding:18px 22px; border-bottom:1px solid var(--gray-100); background:var(--gray-50); }
.team-detail-name { font-size:17px; font-weight:800; color:var(--gray-900); margin:0; }
.team-detail-actions { display:flex; gap:8px; }
.act-btn { width:34px; height:34px; border-radius:var(--radius-sm); border:1px solid var(--gray-200); background:#fff; cursor:pointer; display:flex; align-items:center; justify-content:center; transition:all .15s; color:var(--gray-400); }
.act-btn.edit:hover { border-color:var(--primary); color:var(--primary); background:var(--primary-light); transform:scale(1.06); }
.act-btn.del:hover { border-color:var(--danger); color:var(--danger); background:var(--danger-light); transform:scale(1.06); }

/* ── INNER TABS ── */
.team-inner-tabs { display:flex; gap:2px; padding:12px 22px 0; border-bottom:1px solid var(--gray-200); }
.team-inner-tab { padding:10px 18px; font-size:13px; font-weight:600; color:var(--gray-500); cursor:pointer; border:none; background:none; border-bottom:2.5px solid transparent; margin-bottom:-1px; transition:all .15s; border-radius:var(--radius-sm) var(--radius-sm) 0 0; }
.team-inner-tab:hover { color:var(--gray-700); background:var(--gray-50); }
.team-inner-tab.active { color:var(--primary); border-bottom-color:var(--primary); background:var(--primary-light); }
.team-tab-content { padding:22px; }

/* Team Info tab rows */
.info-row { display:flex; align-items:center; justify-content:space-between; padding:14px 16px; background:var(--gray-50); border:1px solid var(--gray-200); border-radius:var(--radius-md); margin-bottom:10px; }
.info-row:last-child { margin-bottom:0; }
.info-row-label { font-size:13px; font-weight:600; color:var(--gray-700); }
.info-row-sub { font-size:11.5px; color:var(--gray-400); margin-top:2px; }
.info-select { height:38px; padding:0 12px; border:1.5px solid var(--gray-200); border-radius:var(--radius-full); font-size:13px; font-weight:600; color:var(--gray-700); background:var(--gray-50); outline:none; cursor:pointer; min-width:160px; }
.info-select:focus { border-color:var(--primary); box-shadow:0 0 0 3px rgba(37,99,235,.08); background:#fff; }

/* Members tab */
.member-search-row { display:flex; align-items:center; justify-content:space-between; gap:12px; margin-bottom:16px; flex-wrap:wrap; }
.member-search-wrap { display:flex; align-items:center; background:var(--gray-50); border:1.5px solid var(--gray-200); border-radius:var(--radius-full); padding:0 8px 0 14px; flex:1; min-width:180px; max-width:280px; transition:border-color .18s,box-shadow .18s; }
.member-search-wrap:focus-within { border-color:var(--primary); box-shadow:0 0 0 3px rgba(37,99,235,.08); background:#fff; }
.member-search-wrap input { border:none; background:transparent; outline:none; font-size:13px; color:var(--gray-700); width:100%; padding:9px 0; }
.member-search-wrap input::placeholder { color:var(--gray-400); }
.member-search-btn { width:30px; height:30px; border-radius:var(--radius-full); background:var(--primary); border:none; color:#fff; cursor:pointer; display:flex; align-items:center; justify-content:center; flex-shrink:0; }
.member-item { display:flex; align-items:center; justify-content:space-between; padding:13px 0; border-bottom:1px solid var(--gray-100); flex-wrap:wrap; gap:8px; }
.member-item:last-child { border-bottom:none; }
.member-info { display:flex; align-items:center; gap:12px; }
.member-avatar { width:38px; height:38px; border-radius:50%; background:linear-gradient(135deg,#EFF6FF,#BFDBFE); display:flex; align-items:center; justify-content:center; font-size:12px; font-weight:800; color:var(--primary); flex-shrink:0; border:1.5px solid var(--primary-mid); text-transform:uppercase; }
.member-name { font-size:14px; font-weight:600; color:var(--gray-900); }
.member-email { font-size:12px; color:var(--gray-400); }
.desig-badge { background:var(--primary-light); border:1px solid var(--primary-mid); color:var(--primary); border-radius:var(--radius-full); padding:4px 12px; font-size:11.5px; font-weight:700; }
.member-empty { text-align:center; padding:40px 0; }

/* Tracking settings */
.track-row { display:flex; align-items:center; justify-content:space-between; padding:14px 16px; background:var(--gray-50); border:1px solid var(--gray-200); border-radius:var(--radius-md); margin-bottom:10px; gap:12px; }
.track-row-sub { background:var(--gray-100); border:1px solid var(--gray-200); border-radius:var(--radius-md); padding:14px 16px; margin-bottom:10px; }
.track-row-label h6 { font-size:13.5px; font-weight:700; color:var(--gray-700); margin:0 0 2px; }
.track-row-label small { font-size:11.5px; color:var(--gray-400); }
.toggle-switch { position:relative; display:inline-block; width:44px; height:24px; flex-shrink:0; }
.toggle-switch input { opacity:0; width:0; height:0; }
.toggle-slider { position:absolute; inset:0; background:var(--gray-300); border-radius:100px; cursor:pointer; transition:.2s; }
.toggle-slider:before { content:''; position:absolute; height:18px; width:18px; left:3px; bottom:3px; background:#fff; border-radius:50%; transition:.2s; box-shadow:0 1px 4px rgba(0,0,0,.2); }
.toggle-switch input:checked + .toggle-slider { background:var(--primary); }
.toggle-switch input:checked + .toggle-slider:before { transform:translateX(20px); }
.track-select { height:36px; padding:0 12px; border:1.5px solid var(--gray-200); border-radius:var(--radius-full); font-size:13px; font-weight:600; color:var(--gray-700); background:#fff; outline:none; cursor:pointer; min-width:130px; }
.track-select:focus { border-color:var(--primary); }
.btn-save { height:40px; padding:0 28px; border:none; border-radius:var(--radius-full); background:var(--primary); color:#fff; font-size:13.5px; font-weight:700; cursor:pointer; transition:all .18s; box-shadow:0 2px 8px rgba(37,99,235,.28); }
.btn-save:hover { background:var(--primary-dark); transform:translateY(-1px); box-shadow:0 4px 14px rgba(37,99,235,.36); }

/* ── PLACEHOLDER (no team selected) ── */
.team-placeholder { display:flex; flex-direction:column; align-items:center; justify-content:center; padding:80px 40px; background:#fff; border:1px solid var(--gray-200); border-radius:var(--radius-lg); box-shadow:var(--shadow-sm); text-align:center; }
.team-placeholder-icon { width:72px; height:72px; background:var(--gray-100); border-radius:var(--radius-lg); display:flex; align-items:center; justify-content:center; margin-bottom:16px; color:var(--gray-300); }
.team-placeholder-title { font-size:16px; font-weight:700; color:var(--gray-700); margin:0 0 6px; }
.team-placeholder-sub { font-size:13px; color:var(--gray-400); margin:0; }

/* ── MODAL ── */
.modal-content { border-radius:var(--radius-xl) !important; border:none !important; box-shadow:var(--shadow-lg) !important; overflow:hidden; }
.modal-top-band { background:linear-gradient(135deg,var(--primary) 0%,#1D4ED8 100%); padding:22px 24px 20px; position:relative; overflow:hidden; }
.modal-top-band.green { background:linear-gradient(135deg,#059669,#047857); }
.modal-top-band.wide { background:linear-gradient(135deg,#7C3AED,#6D28D9); }
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
.mform-textarea { width:100%; border:1.5px solid var(--gray-200) !important; border-radius:var(--radius-md) !important; padding:12px 14px !important; font-size:14px !important; color:var(--gray-900); background:var(--gray-50); outline:none; transition:all .18s; resize:vertical; min-height:80px; }
.mform-textarea:focus { border-color:var(--primary) !important; background:#fff; box-shadow:0 0 0 3px rgba(37,99,235,.08) !important; }
.mform-select { width:100%; height:46px; border:1.5px solid var(--gray-200) !important; border-radius:var(--radius-md) !important; padding:0 14px !important; font-size:14px !important; color:var(--gray-900); background:var(--gray-50); outline:none; transition:all .18s; appearance:none; cursor:pointer; }
.mform-select:focus { border-color:var(--primary) !important; background:#fff; box-shadow:0 0 0 3px rgba(37,99,235,.08) !important; }
.mform-divider { border:none; border-top:1px solid var(--gray-100); margin:16px 0; }
.modal-footer-btns { display:flex; gap:10px; justify-content:flex-end; padding-top:8px; }
.btn-modal-cancel { height:42px; padding:0 20px; border:1.5px solid var(--gray-200); border-radius:var(--radius-full); background:#fff; color:var(--gray-500); font-size:13.5px; font-weight:600; cursor:pointer; transition:all .15s; }
.btn-modal-cancel:hover { border-color:var(--gray-300); color:var(--gray-700); }
.btn-modal-submit { height:42px; padding:0 24px; border:none; border-radius:var(--radius-full); background:var(--primary); color:#fff; font-size:13.5px; font-weight:700; cursor:pointer; transition:all .18s; box-shadow:0 2px 8px rgba(37,99,235,.28); display:flex; align-items:center; gap:6px; }
.btn-modal-submit:hover { background:var(--primary-dark); transform:translateY(-1px); box-shadow:0 4px 14px rgba(37,99,235,.36); }
.btn-modal-submit.green { background:linear-gradient(135deg,#059669,#047857); box-shadow:0 2px 8px rgba(5,150,105,.28); }
.btn-modal-submit.green:hover { box-shadow:0 4px 14px rgba(5,150,105,.36); }
.btn-modal-submit.purple { background:linear-gradient(135deg,#7C3AED,#6D28D9); box-shadow:0 2px 8px rgba(124,58,237,.28); }

/* Pagination inside panel */
.mini-pagi { display:flex; align-items:center; justify-content:center; gap:4px; list-style:none; margin:16px 0 0; padding:0; }
.mini-pagi li a { display:flex; align-items:center; justify-content:center; width:32px; height:32px; border-radius:var(--radius-sm); font-size:12px; font-weight:600; text-decoration:none; color:var(--gray-500); border:1px solid transparent; transition:all .15s; }
.mini-pagi li a:hover { background:var(--primary-light); color:var(--primary); border-color:var(--primary-mid); }
.mini-pagi li.active_pagination a { background:var(--primary); color:#fff; border-color:var(--primary); }
.mini-pagi li.disabled a { opacity:.35; pointer-events:none; }

@media (max-width:768px) {
    .settings-tab-bar { gap:3px; }
    .stab { padding:8px 12px; font-size:12px; }
    .team-header-row { flex-direction:column; align-items:flex-start; }
    .user-limit-badge { width:100%; }
    .member-search-row { flex-direction:column; align-items:flex-start; }
}
</style>
@endpush

@push('script-page')
<script>
const updateShiftRoute  = "{{ route('organization.settings.team.updateshift',  ['team' => '__TEAM_ID__']) }}";
const updatePolicyRoute = "{{ route('organization.settings.team.updatepolicy', ['team' => '__TEAM_ID__']) }}";

document.addEventListener('DOMContentLoaded', function () {

    /* ── Tracking toggle cascades ── */
    var trackingSwitch   = document.getElementById('trackingSwitch');
    var screenshotDetails = document.getElementById('screenshotDetails');
    if (trackingSwitch && screenshotDetails) {
        trackingSwitch.addEventListener('change', function () {
            screenshotDetails.classList.toggle('d-none', !this.checked);
        });
    }
    var screenshotSwitch = document.getElementById('screenshotSwitch');
    var idleTimeoutBox   = document.getElementById('idleTimeoutBox');
    if (screenshotSwitch && idleTimeoutBox) {
        screenshotSwitch.addEventListener('change', function () {
            idleTimeoutBox.classList.toggle('d-none', !this.checked);
        });
    }
    var appUrlSwitch   = document.getElementById('appUrlSwitch');
    var idleTimeoutBox1 = document.getElementById('idleTimeoutBox1');
    if (appUrlSwitch && idleTimeoutBox1) {
        appUrlSwitch.addEventListener('change', function () {
            idleTimeoutBox1.classList.toggle('d-none', !this.checked);
        });
    }

    /* ── Shift select AJAX ── */
    document.querySelectorAll('.shift-select').forEach(function (sel) {
        sel.addEventListener('change', function () {
            var teamId   = this.getAttribute('data-team-id');
            var url      = updateShiftRoute.replace('__TEAM_ID__', teamId);
            fetch(url, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                body: JSON.stringify({ shift_id: this.value, team_id: teamId })
            })
            .then(r => r.json())
            .then(data => {
                if (data.success) show_toastr('Success', data.message, 'success');
                else show_toastr('Error', 'Failed to update shift.', 'error');
            })
            .catch(() => show_toastr('Error', 'An unexpected error occurred.', 'error'));
        });
    });

    /* ── Delete team confirm ── */
    document.querySelectorAll('.btn-delete-team').forEach(function (btn) {
        btn.addEventListener('click', function () {
            var id = this.getAttribute('data-id');
            Swal.fire({
                title: 'Delete Team?', text: 'This action cannot be undone!', icon: 'warning',
                showCancelButton: true, confirmButtonColor: '#2563EB', cancelButtonColor: '#DC2626',
                confirmButtonText: 'Yes, delete it!', cancelButtonText: 'Cancel'
            }).then(function (result) {
                if (result.isConfirmed) document.getElementById('delete-form-' + id).submit();
            });
        });
    });

    /* ── User limit warning ── */
    document.addEventListener('click', function (e) {
        var btn = e.target.closest('.btn-add.disabled-btn');
        if (btn) {
            e.preventDefault(); e.stopPropagation();
            Swal.fire({ icon: 'warning', title: 'User Limit Reached', text: 'You have reached the maximum number of users allowed.', confirmButtonText: 'OK' });
        }
    });
});

/* ── AJAX: Add Team ── */
$(document).ready(function () {
    $('#submitAddTeam').click(function () {
        var form = $('#addTeamForm');
        $('.text-danger').text('');
        $.ajax({
            url: form.attr('action'), type: 'POST', data: form.serialize(),
            success: function () {
                $('#AddTeamModal').modal('hide');
                form[0].reset();
                show_toastr('Success', 'Team added successfully.', 'success');
                setTimeout(function(){ location.reload(); }, 900);
            },
            error: function (xhr) {
                if (xhr.status === 422) {
                    $.each(xhr.responseJSON.errors, function (key, value) {
                        $('.error-' + key).text(value[0]);
                    });
                } else { show_toastr('Error', 'An unexpected error occurred.', 'error'); }
            }
        });
    });
});

/* ── AJAX: Edit Team ── */
$(document).ready(function () {
    $('#submitEditTeam').click(function () {
        var form = $('#editTeamForm');
        $('.text-danger').text('');
        $.ajax({
            url: form.attr('action'), type: 'POST', data: form.serialize(),
            success: function () {
                $('#editTeamModal').modal('hide');
                show_toastr('Success', 'Team updated successfully.', 'success');
                setTimeout(function(){ location.reload(); }, 900);
            },
            error: function (xhr) {
                if (xhr.status === 422) {
                    $.each(xhr.responseJSON.errors, function (key, value) {
                        $('.editerror-' + key).text(value[0]);
                    });
                } else { show_toastr('Error', 'An unexpected error occurred.', 'error'); }
            }
        });
    });
});

/* ── AJAX: Add User ── */
$(document).ready(function () {
    $('#submitadduser').on('click', function (e) {
        e.preventDefault();
        var form = $('#adduservalidate');
        $('[id^=adderror-]').text('');
        $.ajax({
            type: 'POST', url: form.attr('action'), data: form.serialize(),
            success: function () {
                $('#AddUserModal').modal('hide');
                show_toastr('Success', 'User created successfully.', 'success');
                setTimeout(function(){ location.reload(); }, 900);
            },
            error: function (xhr) {
                if (xhr.status === 422) {
                    $.each(xhr.responseJSON.errors, function (key, value) {
                        $('#adderror-' + key).text(value[0]);
                    });
                } else { show_toastr('Error', 'Something went wrong. Please try again.', 'error'); }
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
    @php $canAddUser = $activeUserCount < auth()->user()->max_users; @endphp
    <div class="team-header-row">
        <div>
            <h1 class="page-title-main">Team Settings</h1>
            <p class="page-title-sub">Manage teams, members and tracking configurations</p>
        </div>
        <div style="display:flex; align-items:center; gap:12px; flex-wrap:wrap;">
            <div class="user-limit-badge">
                <div class="user-limit-text">
                    <h6>Active Users</h6>
                    <p>Excludes inactive or deactivated users</p>
                </div>
                <div class="user-limit-count {{ $activeUserCount >= auth()->user()->max_users ? 'danger' : ($activeUserCount >= auth()->user()->max_users * 0.8 ? 'warning' : '') }}">
                    {{ $activeUserCount }} / {{ auth()->user()->max_users }}
                </div>
            </div>
            <button data-bs-toggle="modal" data-bs-target="#AddTeamModal" class="btn-add">
                <span class="plus-circle">+</span> Add Team
            </button>
        </div>
    </div>

    {{-- MAIN LAYOUT --}}
    <div class="team-layout">

        {{-- LEFT: TEAM LIST --}}
        <div class="team-list-panel">
            <div class="team-list-search">
                <form method="GET" action="{{ route('organization.settings.team') }}">
                    @if(request('team_id')) <input type="hidden" name="team_id" value="{{ request('team_id') }}"> @endif
                    <div class="team-search-wrap">
                        <input type="text" name="search" placeholder="Search teams…" value="{{ request('search') }}">
                        <button type="submit" class="team-search-btn" title="Search">
                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                        </button>
                    </div>
                </form>
            </div>
            <div class="team-list-scroll">
                @forelse ($teams as $team)
                    @php
                        $twords = explode(' ', trim($team->name));
                        $tinit  = strtoupper(implode('', array_map(fn($w) => $w[0] ?? '', $twords)));
                        $tinit  = substr($tinit, 0, 2);
                    @endphp
                    <a href="{{ route('organization.settings.team', ['team_id' => $team->id]) }}"
                       class="team-item {{ request('team_id') == $team->id ? 'active' : '' }}">
                        <div class="team-item-avatar">{{ $tinit }}</div>
                        <span class="team-item-name">{{ $team->name }}</span>
                    </a>
                @empty
                    <div class="team-empty">
                        <div class="team-empty-icon">
                            <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                        </div>
                        <p class="team-empty-text">No teams found</p>
                    </div>
                @endforelse
            </div>
        </div>

        {{-- RIGHT: TEAM DETAIL OR PLACEHOLDER --}}
        @if (isset($selectedTeam))
        <div class="team-detail-panel">
            {{-- Header --}}
            <div class="team-detail-header">
                <h5 class="team-detail-name">{{ $selectedTeam->name }}</h5>
                <div class="team-detail-actions">
                    <button class="act-btn edit" data-bs-toggle="modal" data-bs-target="#editTeamModal" title="Edit team">
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                    </button>
                    <form id="delete-form-{{ $selectedTeam->id }}" method="POST"
                          action="{{ route('organization.settings.team.destroy', $selectedTeam->id) }}" style="margin:0;">
                        @csrf @method('DELETE')
                        <button type="button" class="act-btn del btn-delete-team" data-id="{{ $selectedTeam->id }}" title="Delete team">
                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/><path d="M10 11v6"/><path d="M14 11v6"/><path d="M9 6V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2"/></svg>
                        </button>
                    </form>
                </div>
            </div>

            {{-- Inner tabs --}}
            <div class="team-inner-tabs" id="teamInnerTabs">
                <button class="team-inner-tab {{ (!request()->has('user_search') && !request()->has('page')) ? 'active' : '' }}"
                        onclick="switchTab('tabInfo', this)">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="margin-right:5px;"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                    Team Info
                </button>
                <button class="team-inner-tab {{ (request()->has('user_search') || request()->has('page')) ? 'active' : '' }}"
                        onclick="switchTab('tabMembers', this)">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="margin-right:5px;"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/></svg>
                    Members
                </button>
                <button class="team-inner-tab" onclick="switchTab('tabTracking', this)">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="margin-right:5px;"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                    Tracking
                </button>
            </div>

            {{-- TAB: Team Info --}}
            <div class="team-tab-content" id="tabInfo" style="{{ (request()->has('user_search') || request()->has('page')) ? 'display:none;' : '' }}">
                <div class="info-row">
                    <div>
                        <div class="info-row-label">Shift Assignment</div>
                        <div class="info-row-sub">Select the work shift for this team</div>
                    </div>
                    <select class="info-select shift-select" data-team-id="{{ $selectedTeam->id }}">
                        <option value="">Select Shift</option>
                        @foreach ($shifts as $shift)
                            <option value="{{ $shift->id }}" {{ $selectedTeam->shift_id == $shift->id ? 'selected' : '' }}>
                                {{ $shift->shift_name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="info-row" style="margin-top:10px;">
                    <div>
                        <div class="info-row-label">Avg. Keyboard Clicks / Day</div>
                    </div>
                    <span style="font-size:14px;font-weight:700;color:var(--gray-700);">{{ $selectedTeam->avg_keyboard_clicks_per_day ?? '—' }}</span>
                </div>
                <div class="info-row" style="margin-top:10px;">
                    <div>
                        <div class="info-row-label">Avg. Mouse Clicks / Day</div>
                    </div>
                    <span style="font-size:14px;font-weight:700;color:var(--gray-700);">{{ $selectedTeam->avg_mouse_clicks_per_day ?? '—' }}</span>
                </div>
            </div>

            {{-- TAB: Members --}}
            <div class="team-tab-content" id="tabMembers" style="{{ (request()->has('user_search') || request()->has('page')) ? '' : 'display:none;' }}">
                <div class="member-search-row">
                    <form method="GET" action="{{ route('organization.settings.team') }}" style="display:flex;flex:1;max-width:280px;">
                        <input type="hidden" name="team_id" value="{{ request('team_id') }}">
                        <div class="member-search-wrap">
                            <input type="text" name="user_search" value="{{ request('user_search') }}" placeholder="Search members…">
                            <button type="submit" class="member-search-btn" title="Search">
                                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                            </button>
                        </div>
                    </form>
                    <button type="button"
                            class="btn-add {{ $canAddUser ? '' : 'disabled-btn' }}"
                            style="height:38px; padding:0 16px; font-size:13px;"
                            {{ $canAddUser ? 'data-bs-toggle=modal data-bs-target=#AddUserModal' : '' }}>
                        <span class="plus-circle" style="width:16px;height:16px;font-size:12px;">+</span> Add User
                    </button>
                </div>

                @forelse ($users as $member)
                    @php
                        $mwords = explode(' ', trim($member->name));
                        $minit  = strtoupper(implode('', array_map(fn($w) => $w[0] ?? '', $mwords)));
                        $minit  = substr($minit, 0, 2);
                    @endphp
                    <div class="member-item">
                        <div class="member-info">
                            <div class="member-avatar">{{ $minit }}</div>
                            <div>
                                <div class="member-name">{{ $member->name }}</div>
                                <div class="member-email">{{ $member->email }}</div>
                            </div>
                        </div>
                        <span class="desig-badge">{{ $member->designation->name ?? '—' }}</span>
                    </div>
                @empty
                    <div class="member-empty">
                        <svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" style="color:var(--gray-300);margin-bottom:10px;"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/></svg>
                        <p style="font-size:13px;color:var(--gray-400);font-weight:600;margin:0;">No members found</p>
                    </div>
                @endforelse

                @if ($users->total() > 0)
                    <ul class="mini-pagi">
                        <li class="{{ $users->onFirstPage() ? 'disabled' : '' }}">
                            <a href="{{ $users->url(1) }}">«</a>
                        </li>
                        <li class="{{ $users->onFirstPage() ? 'disabled' : '' }}">
                            <a href="{{ $users->previousPageUrl() }}">‹</a>
                        </li>
                        @php $us = max(1,$users->currentPage()-2); $ue = min($us+4,$users->lastPage()); @endphp
                        @for($p=$us; $p<=$ue; $p++)
                            <li class="{{ $users->currentPage()==$p ? 'active_pagination' : '' }}">
                                <a href="{{ $users->url($p) }}">{{ $p }}</a>
                            </li>
                        @endfor
                        <li class="{{ !$users->hasMorePages() ? 'disabled' : '' }}">
                            <a href="{{ $users->nextPageUrl() }}">›</a>
                        </li>
                        <li class="{{ !$users->hasMorePages() ? 'disabled' : '' }}">
                            <a href="{{ $users->url($users->lastPage()) }}">»</a>
                        </li>
                    </ul>
                    <p style="text-align:center;font-size:12px;color:var(--gray-400);margin-top:8px;">
                        {{ $users->firstItem() }}–{{ $users->lastItem() }} of {{ $users->total() }}
                    </p>
                @endif
            </div>

            {{-- TAB: Tracking Settings --}}
            <div class="team-tab-content" id="tabTracking" style="display:none;">
                <form action="{{ route('organization.settings.team.teamtrackupdate', $selectedTeam->id) }}" method="POST">
                    @csrf @method('PUT')
                    <input type="hidden" name="team_idd" value="{{ $selectedTeam->id }}">

                    {{-- Tracking Master --}}
                    <div class="track-row">
                        <div class="track-row-label">
                            <h6>Tracking</h6>
                            <small>Switch off to stop tracking metrics for the employee.</small>
                        </div>
                        <label class="toggle-switch">
                            <input type="checkbox" id="trackingSwitch" name="is_tracking" {{ old('is_tracking', $selectedTeam->is_tracking) ? 'checked' : '' }}>
                            <span class="toggle-slider"></span>
                        </label>
                    </div>

                    <div id="screenshotDetails" class="{{ $selectedTeam->is_tracking ? '' : 'd-none' }}" >
                        {{-- Livestream --}}
                        <div class="track-row-sub">
                            <div style="display:flex;align-items:center;justify-content:space-between;">
                                <div class="track-row-label">
                                    <h6>Livestream</h6>
                                    <small>Switch on to view live updates about the employee.</small>
                                </div>
                                <label class="toggle-switch">
                                    <input type="checkbox" name="is_livestream" {{ old('is_livestream', $selectedTeam->is_livestream) ? 'checked' : '' }}>
                                    <span class="toggle-slider"></span>
                                </label>
                            </div>
                        </div>

                        {{-- Screenshots --}}
                        <div class="track-row-sub">
                            <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:8px;">
                                <div class="track-row-label">
                                    <h6>Capture Screenshots</h6>
                                    <small>Switch on to take regular screenshots.</small>
                                </div>
                                <label class="toggle-switch">
                                    <input type="checkbox" id="screenshotSwitch" name="is_capturescreenshot" {{ old('is_capturescreenshot', $selectedTeam->is_capturescreenshot) ? 'checked' : '' }}>
                                    <span class="toggle-slider"></span>
                                </label>
                            </div>
                            <div id="idleTimeoutBox" class="{{ $selectedTeam->is_capturescreenshot ? '' : 'd-none' }}"
                                 style="background:var(--primary-light);border:1px solid var(--primary-mid);border-radius:var(--radius-sm);padding:12px 14px;display:flex;align-items:center;justify-content:space-between;gap:12px;margin-top:8px;">
                                <div class="track-row-label">
                                    <h6 style="color:var(--primary);">Screenshot Frequency</h6>
                                    <small>Set the frequency at which screenshots will be taken.</small>
                                </div>
                                <select class="track-select" name="is_screenshot_frequency">
                                    @foreach([5 => '5 Minutes', 10 => '10 Minutes', 15 => '15 Minutes'] as $val => $label)
                                        <option value="{{ $val }}" {{ old('is_screenshot_frequency', $selectedTeam->is_screenshot_frequency) == $val ? 'selected' : '' }}>{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        {{-- App & URLs --}}
                        <div class="track-row-sub">
                            <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:8px;">
                                <div class="track-row-label">
                                    <h6>App & URLs</h6>
                                    <small>Switch on to track app and URL usage.</small>
                                </div>
                                <label class="toggle-switch">
                                    <input type="checkbox" id="appUrlSwitch" name="is_app_url" {{ old('is_app_url', $selectedTeam->is_app_url) ? 'checked' : '' }}>
                                    <span class="toggle-slider"></span>
                                </label>
                            </div>
                            <div id="idleTimeoutBox1" class="{{ $selectedTeam->is_app_url ? '' : 'd-none' }}"
                                 style="background:var(--primary-light);border:1px solid var(--primary-mid);border-radius:var(--radius-sm);padding:12px 14px;display:flex;align-items:center;justify-content:space-between;gap:12px;margin-top:8px;">
                                <div class="track-row-label">
                                    <h6 style="color:var(--primary);">Keyboard & Mouse Interval</h6>
                                    <small>Set tracking interval for keyboard/mouse activity.</small>
                                </div>
                                <select class="track-select" name="is_keyboard_mouse">
                                    @foreach([5 => '5 Minutes', 10 => '10 Minutes', 15 => '15 Minutes'] as $val => $label)
                                        <option value="{{ $val }}" {{ old('is_keyboard_mouse', $selectedTeam->is_keyboard_mouse) == $val ? 'selected' : '' }}>{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    {{-- Idle Timeout --}}
                    <div class="track-row" style="margin-top:10px;">
                        <div class="track-row-label">
                            <h6>Idle Timeout Popup</h6>
                            <small>Alert the user after being inactive for this duration.</small>
                        </div>
                        <select class="track-select" name="idle_timeout_popup_reminder_in_minutes">
                            @foreach([5 => '5 Minutes', 10 => '10 Minutes', 15 => '15 Minutes'] as $val => $label)
                                <option value="{{ $val }}" {{ old('idle_timeout_popup_reminder_in_minutes', $selectedTeam->idle_timeout_popup_reminder_in_minutes) == $val ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Auto Punchout --}}
                    <div class="track-row" style="margin-top:10px;">
                        <div class="track-row-label">
                            <h6>Auto Punchout Threshold</h6>
                            <small>Punch out the user after being inactive for this duration.</small>
                        </div>
                        <select class="track-select" name="auto_punch_out_threshold">
                            @foreach([1 => '1 Hour', 2 => '2 Hours', 3 => '3 Hours'] as $val => $label)
                                <option value="{{ $val }}" {{ old('auto_punch_out_threshold', $selectedTeam->auto_punch_out_threshold) == $val ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div style="margin-top:20px;">
                        <button type="submit" class="btn-save">Save Changes</button>
                    </div>
                </form>
            </div>
        </div>

        @else
        {{-- Placeholder --}}
        <div class="team-placeholder">
            <div class="team-placeholder-icon">
                <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
            </div>
            <p class="team-placeholder-title">Select a team</p>
            <p class="team-placeholder-sub">Choose a team from the left to view details and settings.</p>
        </div>
        @endif

    </div>{{-- end team-layout --}}
</div>

{{-- ══ TAB SWITCH SCRIPT ══ --}}
<script>
function switchTab(tabId, btn) {
    document.querySelectorAll('.team-tab-content').forEach(function(t){ t.style.display = 'none'; });
    document.querySelectorAll('.team-inner-tab').forEach(function(b){ b.classList.remove('active'); });
    document.getElementById(tabId).style.display = 'block';
    btn.classList.add('active');
}
</script>

{{-- ══ ADD TEAM MODAL ══ --}}
<div class="modal fade" id="AddTeamModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width:500px;">
        <div class="modal-content">
            <div class="modal-top-band">
                <button type="button" class="modal-close-btn" data-bs-dismiss="modal">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                </button>
                <div class="modal-top-icon">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                </div>
                <h5 class="modal-top-title">Add Team</h5>
                <p class="modal-top-sub">Create a new team and set activity thresholds</p>
            </div>
            <div class="modal-body">
                <form method="POST" action="{{ route('organization.settings.team.store') }}" id="addTeamForm">
                    @csrf
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="mform-label">Team Name <span style="color:#DC2626">*</span></label>
                            <input type="text" class="mform-input" name="name" placeholder="e.g. Engineering" autocomplete="off">
                            <div class="text-danger mt-1 small error-name"></div>
                        </div>
                        <div class="col-12">
                            <label class="mform-label">Description <span style="color:#DC2626">*</span></label>
                            <textarea class="mform-textarea" name="description" placeholder="Describe the team's purpose…"></textarea>
                            <div class="text-danger mt-1 small error-description"></div>
                        </div>
                        <div class="col-md-6">
                            <label class="mform-label">Avg. Keyboard Clicks <span style="color:#DC2626">*</span></label>
                            <input type="number" class="mform-input" name="average_keyboard_clicks" placeholder="e.g. 500">
                            <div class="text-danger mt-1 small error-average_keyboard_clicks"></div>
                        </div>
                        <div class="col-md-6">
                            <label class="mform-label">Avg. Mouse Clicks <span style="color:#DC2626">*</span></label>
                            <input type="number" class="mform-input" name="average_mouse_clicks" placeholder="e.g. 200">
                            <div class="text-danger mt-1 small error-average_mouse_clicks"></div>
                        </div>
                        <div class="col-md-6">
                            <label class="mform-label">Exc. Keyboard Typing <span style="color:#DC2626">*</span></label>
                            <input type="number" class="mform-input" name="excessive_keyboard_typing" placeholder="e.g. 1000">
                            <div class="text-danger mt-1 small error-excessive_keyboard_typing"></div>
                        </div>
                        <div class="col-md-6">
                            <label class="mform-label">Exc. Mouse Clicking <span style="color:#DC2626">*</span></label>
                            <input type="number" class="mform-input" name="excessive_mouse_clicking" placeholder="e.g. 500">
                            <div class="text-danger mt-1 small error-excessive_mouse_clicking"></div>
                        </div>
                    </div>
                    <div class="modal-footer-btns mt-2">
                        <button type="button" class="btn-modal-cancel" data-bs-dismiss="modal">Cancel</button>
                        <button type="button" class="btn-modal-submit" id="submitAddTeam">
                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                            Create Team
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- ══ EDIT TEAM MODAL ══ --}}
@if(isset($selectedTeam))
<div class="modal fade" id="editTeamModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width:500px;">
        <div class="modal-content">
            <div class="modal-top-band green">
                <button type="button" class="modal-close-btn" data-bs-dismiss="modal">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                </button>
                <div class="modal-top-icon">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                </div>
                <h5 class="modal-top-title">Edit Team</h5>
                <p class="modal-top-sub">Update team details and activity thresholds</p>
            </div>
            <div class="modal-body">
                <form method="POST" action="{{ route('organization.settings.team.update', $selectedTeam->id) }}" id="editTeamForm">
                    @csrf
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="mform-label">Team Name <span style="color:#DC2626">*</span></label>
                            <input type="text" class="mform-input" name="name" value="{{ old('name', $selectedTeam->name) }}" placeholder="e.g. Engineering">
                            <div class="text-danger mt-1 small editerror-name"></div>
                        </div>
                        <div class="col-12">
                            <label class="mform-label">Description <span style="color:#DC2626">*</span></label>
                            <textarea class="mform-textarea" name="description" placeholder="Describe the team's purpose…">{{ old('description', $selectedTeam->description) }}</textarea>
                            <div class="text-danger mt-1 small editerror-description"></div>
                        </div>
                        <div class="col-md-6">
                            <label class="mform-label">Avg. Keyboard Clicks <span style="color:#DC2626">*</span></label>
                            <input type="number" class="mform-input" name="average_keyboard_clicks" value="{{ old('average_keyboard_clicks', $selectedTeam->avg_keyboard_clicks_per_day) }}">
                            <div class="text-danger mt-1 small editerror-average_keyboard_clicks"></div>
                        </div>
                        <div class="col-md-6">
                            <label class="mform-label">Avg. Mouse Clicks <span style="color:#DC2626">*</span></label>
                            <input type="number" class="mform-input" name="average_mouse_clicks" value="{{ old('average_mouse_clicks', $selectedTeam->avg_mouse_clicks_per_day) }}">
                            <div class="text-danger mt-1 small editerror-average_mouse_clicks"></div>
                        </div>
                        <div class="col-md-6">
                            <label class="mform-label">Exc. Keyboard Typing <span style="color:#DC2626">*</span></label>
                            <input type="number" class="mform-input" name="excessive_keyboard_typing" value="{{ old('excessive_keyboard_typing', $selectedTeam->excessive_keyboard_typing_per_day) }}">
                            <div class="text-danger mt-1 small editerror-excessive_keyboard_typing"></div>
                        </div>
                        <div class="col-md-6">
                            <label class="mform-label">Exc. Mouse Clicking <span style="color:#DC2626">*</span></label>
                            <input type="number" class="mform-input" name="excessive_mouse_clicking" value="{{ old('excessive_mouse_clicking', $selectedTeam->excessive_mouse_clicking_per_day) }}">
                            <div class="text-danger mt-1 small editerror-excessive_mouse_clicking"></div>
                        </div>
                    </div>
                    <div class="modal-footer-btns mt-2">
                        <button type="button" class="btn-modal-cancel" data-bs-dismiss="modal">Cancel</button>
                        <button type="button" class="btn-modal-submit green" id="submitEditTeam">
                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
                            Save Changes
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endif

{{-- ══ ADD USER MODAL ══ --}}
<div class="modal fade" id="AddUserModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width:760px;">
        <div class="modal-content">
            <div class="modal-top-band wide">
                <button type="button" class="modal-close-btn" data-bs-dismiss="modal">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                </button>
                <div class="modal-top-icon">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/><line x1="12" y1="11" x2="12" y2="19" stroke-width="1.5"/><line x1="8" y1="15" x2="16" y2="15" stroke-width="1.5"/></svg>
                </div>
                <h5 class="modal-top-title">Add User</h5>
                <p class="modal-top-sub">Create a new employee account</p>
            </div>
            <div class="modal-body">
                <form method="POST" action="{{ route('organization.settings.user.store') }}" id="adduservalidate">
                    @csrf
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="mform-label">Name <span style="color:#DC2626">*</span></label>
                            <input type="text" class="mform-input" name="name" placeholder="Full name" autocomplete="off">
                            <div class="text-danger mt-1 small" id="adderror-name"></div>
                        </div>
                        <div class="col-md-6">
                            <label class="mform-label">Employee ID <span style="color:#DC2626">*</span></label>
                            <input type="text" class="mform-input" name="employee_id" placeholder="e.g. EMP001" autocomplete="off">
                            <div class="text-danger mt-1 small" id="adderror-employee_id"></div>
                        </div>
                        <div class="col-md-6">
                            <label class="mform-label">Email <span style="color:#DC2626">*</span></label>
                            <input type="email" class="mform-input" name="email" placeholder="email@company.com">
                            <div class="text-danger mt-1 small" id="adderror-email"></div>
                        </div>
                        <div class="col-md-6">
                            <label class="mform-label">Password <span style="color:#DC2626">*</span></label>
                            <input type="text" class="mform-input" name="password" placeholder="Min 6 characters">
                            <div class="text-danger mt-1 small" id="adderror-password"></div>
                        </div>
                        <div class="col-md-6">
                            <label class="mform-label">Gender <span style="color:#DC2626">*</span></label>
                            <select class="mform-select" name="gender">
                                <option disabled selected>Select Gender</option>
                                <option value="male">Male</option>
                                <option value="female">Female</option>
                            </select>
                            <div class="text-danger mt-1 small" id="adderror-gender"></div>
                        </div>
                        <div class="col-md-6">
                            <label class="mform-label">Date of Birth</label>
                            <input type="date" class="mform-input" name="dob">
                            <div class="text-danger mt-1 small" id="adderror-dob"></div>
                        </div>
                        <div class="col-md-6">
                            <label class="mform-label">Date of Joining <span style="color:#DC2626">*</span></label>
                            <input type="date" class="mform-input" name="date_of_join">
                            <div class="text-danger mt-1 small" id="adderror-date_of_join"></div>
                        </div>
                        <div class="col-md-6">
                            <label class="mform-label">Phone Number <span style="color:#DC2626">*</span></label>
                            <input type="tel" class="mform-input" name="mobile_no" placeholder="Enter number">
                            <div class="text-danger mt-1 small" id="adderror-mobile_no"></div>
                        </div>
                        <div class="col-md-6">
                            <label class="mform-label">Team <span style="color:#DC2626">*</span></label>
                            <select class="mform-select" name="team_id">
                                <option disabled selected>Select Team</option>
                                @foreach ($teams as $t)
                                    <option value="{{ $t->id }}">{{ $t->name }}</option>
                                @endforeach
                            </select>
                            <div class="text-danger mt-1 small" id="adderror-team_id"></div>
                        </div>
                        <div class="col-md-6">
                            <label class="mform-label">Role</label>
                            <select class="mform-select" name="role_id">
                                <option value="" selected>Select Role</option>
                                @foreach ($roles as $r)
                                    <option value="{{ $r->id }}">{{ \Str::title($r->name) }}</option>
                                @endforeach
                            </select>
                            <div class="text-danger mt-1 small" id="adderror-role_id"></div>
                        </div>
                        <div class="col-md-6">
                            <label class="mform-label">Designation <span style="color:#DC2626">*</span></label>
                            <select class="mform-select" name="designation_id">
                                <option disabled selected>Select Designation</option>
                                @foreach ($designations as $d)
                                    <option value="{{ $d->id }}">{{ $d->name }}</option>
                                @endforeach
                            </select>
                            <div class="text-danger mt-1 small" id="adderror-designation_id"></div>
                        </div>
                        <div class="col-md-6">
                            <label class="mform-label">Shift <span style="color:#DC2626">*</span></label>
                            <select class="mform-select" name="shift_id">
                                <option disabled selected>Select Shift</option>
                                @foreach ($shifts as $s)
                                    <option value="{{ $s->id }}">{{ $s->shift_name }}</option>
                                @endforeach
                            </select>
                            <div class="text-danger mt-1 small" id="adderror-shift_id"></div>
                        </div>
                        <div class="col-md-6">
                            <label class="mform-label">Status <span style="color:#DC2626">*</span></label>
                            <select class="mform-select" name="is_active">
                                <option value="1" selected>Active</option>
                                <option value="0">Inactive</option>
                            </select>
                            <div class="text-danger mt-1 small" id="adderror-is_active"></div>
                        </div>
                        <div class="col-md-6">
                            <label class="mform-label">Tracking Type <span style="color:#DC2626">*</span></label>
                            <div style="display:flex; flex-wrap:wrap; gap:12px; margin-top:6px;">
                                @foreach(USER_APK_TYPES as $key => $value)
                                    <label style="display:flex;align-items:center;gap:6px;font-size:13px;font-weight:600;color:var(--gray-600);cursor:pointer;">
                                        <input type="radio" name="track_type" value="{{ $value }}"
                                               {{ $value === USER_APK_TYPE_SYSTEM_TRACK ? 'checked' : '' }}
                                               style="accent-color:var(--primary);">
                                        {{ ucfirst(str_replace('_', ' ', $value)) }}
                                    </label>
                                @endforeach
                            </div>
                            <div class="text-danger mt-1 small" id="adderror-apk_type"></div>
                        </div>
                    </div>
                    <div class="modal-footer-btns mt-2">
                        <button type="button" class="btn-modal-cancel" data-bs-dismiss="modal">Cancel</button>
                        <button type="button" class="btn-modal-submit purple" id="submitadduser">
                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                            Create User
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection

