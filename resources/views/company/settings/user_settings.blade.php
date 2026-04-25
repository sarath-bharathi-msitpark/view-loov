@extends('company.layouts.company')
@section('page-title') {{ __('User Settings') }} @endsection
@section('page-icon') {{ asset('assets/assestsnew/settings.svg') }} @endsection

@push('css-page')
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet">
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

/* ── HEADER ── */
.user-header-row { display:flex; align-items:center; justify-content:space-between; gap:16px; margin-bottom:20px; flex-wrap:wrap; }
.page-title-main { font-size:22px; font-weight:800; color:var(--gray-900); margin:0 0 4px; letter-spacing:-.4px; }
.page-title-sub { font-size:13px; color:var(--gray-400); margin:0; }
.user-stat-chip { display:flex; align-items:center; gap:14px; background:#fff; border:1px solid var(--gray-200); border-radius:var(--radius-md); padding:14px 20px; box-shadow:var(--shadow-sm); }
.user-stat-icon { width:40px; height:40px; background:var(--primary-light); color:var(--primary); border-radius:var(--radius-sm); display:flex; align-items:center; justify-content:center; flex-shrink:0; }
.user-stat-label { font-size:12px; font-weight:600; color:var(--gray-400); text-transform:uppercase; letter-spacing:.5px; margin-bottom:2px; }
.user-stat-value { font-size:18px; font-weight:800; color:var(--gray-900); line-height:1; }
.user-stat-sub { font-size:11px; color:var(--gray-400); margin-top:2px; }

/* ── FILTER BAR ── */
.filter-bar { display:flex; align-items:center; gap:10px; flex-wrap:wrap; background:#fff; border:1px solid var(--gray-200); border-radius:var(--radius-lg); padding:12px 16px; box-shadow:var(--shadow-sm); margin-bottom:16px; }
.filter-search-wrap { display:flex; align-items:center; background:var(--gray-50); border:1.5px solid var(--gray-200); border-radius:var(--radius-full); padding:0 8px 0 14px; flex:1; min-width:290px; max-width:280px; transition:border-color .18s,box-shadow .18s; }
.filter-search-wrap:focus-within { border-color:var(--primary); box-shadow:0 0 0 3px rgba(37,99,235,.08); background:#fff; }
.filter-search-wrap input { border:none; background:transparent; outline:none; font-size:13.5px; color:var(--gray-700); width:100%; padding:10px 0; }
.filter-search-wrap input::placeholder { color:var(--gray-400); }
.search-icon { color:var(--gray-400); flex-shrink:0; display:flex; align-items:center; margin-right:4px; }
.filter-search-wrap .search-btn { width:32px; height:32px; border-radius:var(--radius-full); background:var(--primary); border:none; color:#fff; cursor:pointer; display:flex; align-items:center; justify-content:center; flex-shrink:0; transition:background .15s; }
.filter-search-wrap .search-btn:hover { background:var(--primary-dark); }
.filter-select { height:42px; padding:0 14px; border:1.5px solid var(--gray-200); border-radius:var(--radius-full); font-size:13px; font-weight:600; color:var(--gray-700); background:var(--gray-50); outline:none; cursor:pointer; appearance:none; min-width:140px; transition:border-color .18s; }
.filter-select:focus { border-color:var(--primary); background:#fff; box-shadow:0 0 0 3px rgba(37,99,235,.08); }
.filter-divider { width:1px; height:28px; background:var(--gray-200); flex-shrink:0; }
.filter-spacer { flex:1; }
.btn-download { display:inline-flex; align-items:center; gap:6px; height:42px; padding:0 18px; border-radius:var(--radius-full); border:1.5px solid var(--gray-200); background:#fff; color:var(--gray-600); font-size:13px; font-weight:600; cursor:pointer; transition:all .18s; white-space:nowrap; text-decoration:none; }
.btn-download:hover { border-color:var(--primary); color:var(--primary); background:var(--primary-light); }
.btn-add { display:inline-flex; align-items:center; gap:8px; height:42px; padding:0 20px; border-radius:var(--radius-full); border:none; background:var(--primary); color:#fff; font-size:13px; font-weight:700; cursor:pointer; transition:all .18s; white-space:nowrap; box-shadow:0 2px 8px rgba(37,99,235,.28); }
.btn-add:hover { background:var(--primary-dark); box-shadow:0 4px 16px rgba(37,99,235,.36); transform:translateY(-1px); }
.btn-add .plus-circle { width:20px; height:20px; background:rgba(255,255,255,.2); border-radius:50%; display:flex; align-items:center; justify-content:center; flex-shrink:0; }
.btn-add.disabled-btn { opacity:.6; cursor:not-allowed; transform:none !important; pointer-events:auto; }

/* ── TABLE ── */
.user-table-card { background:#fff; border-radius:var(--radius-lg); border:1px solid var(--gray-200); overflow:hidden; box-shadow:var(--shadow-sm); }
.emp-cell { display:flex; align-items:center; gap:10px; }
.emp-avatar { width:36px; height:36px; border-radius:50%; border:1.5px solid var(--gray-200); object-fit:cover; flex-shrink:0; }
.emp-name { font-size:13.5px; font-weight:700; color:var(--gray-900); }
.emp-id-badge { font-family:'Courier New',monospace; font-size:12px; font-weight:700; color:var(--gray-500); background:var(--gray-100); border-radius:var(--radius-sm); padding:3px 8px; }
.shift-badge { background:var(--primary-light); border:1px solid var(--primary-mid); color:var(--primary); border-radius:var(--radius-full); padding:3px 10px; font-size:11.5px; font-weight:700; white-space:nowrap; }
.track-icon-wrap { width:28px; height:28px; border-radius:var(--radius-sm); background:linear-gradient(135deg,#DBEAFE,#EDE9FE); display:flex; align-items:center; justify-content:center; }
.track-icon-wrap img { width:16px; height:16px; }
.toggle-switch { position:relative; display:inline-block; width:40px; height:22px; }
.toggle-switch input { opacity:0; width:0; height:0; }
.toggle-slider { position:absolute; inset:0; background:var(--gray-300); border-radius:100px; cursor:pointer; transition:.2s; }
.toggle-slider:before { content:''; position:absolute; height:16px; width:16px; left:3px; bottom:3px; background:#fff; border-radius:50%; transition:.2s; box-shadow:0 1px 3px rgba(0,0,0,.2); }
.toggle-switch input:checked + .toggle-slider { background:var(--success); }
.toggle-switch input:checked + .toggle-slider:before { transform:translateX(18px); }
.act-btn { width:34px; height:34px; border-radius:var(--radius-sm); border:1px solid var(--gray-200); background:#fff; cursor:pointer; display:flex; align-items:center; justify-content:center; transition:all .15s; color:var(--gray-400); text-decoration:none; }
.act-btn.edit:hover { border-color:var(--primary); color:var(--primary); background:var(--primary-light); transform:scale(1.06); }
.act-btn.impersonate { background:linear-gradient(135deg,#6366F1,#8B5CF6); border-color:transparent; color:#fff; gap:4px; width:auto; padding:0 12px; font-size:12px; font-weight:700; border-radius:var(--radius-sm); }
.act-btn.impersonate:hover { opacity:.9; transform:translateY(-1px); }
.rows-select-wrap { display:flex; align-items:center; gap:8px; padding:10px 16px; border-bottom:1px solid var(--gray-100); font-size:13px; color:var(--gray-500); font-weight:500; }
.rows-select-wrap select { height:28px; padding:0 8px; border:1px solid var(--gray-200); border-radius:var(--radius-sm); font-size:12px; font-weight:600; color:var(--gray-700); background:var(--gray-50); outline:none; cursor:pointer; }
.empty-state { text-align:center; padding:60px 20px; }
.empty-icon-wrap { width:72px; height:72px; background:var(--gray-100); border-radius:var(--radius-lg); display:flex; align-items:center; justify-content:center; margin:0 auto 16px; color:var(--gray-300); }
.empty-title { font-size:16px; font-weight:700; color:var(--gray-700); margin:0 0 6px; }
.empty-sub { font-size:13px; color:var(--gray-400); margin:0; }

/* ── PAGINATION ── */
.pagination-bar { display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:12px; margin-top:16px; padding:14px 18px; background:#fff; border:1px solid var(--gray-200); border-radius:var(--radius-lg); box-shadow:var(--shadow-sm); }
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
.goto-page { display:flex; align-items:center; gap:6px; }
.goto-page input { width:54px; height:34px; border:1px solid var(--gray-200); border-radius:var(--radius-sm); text-align:center; font-size:13px; font-weight:600; color:var(--gray-700); background:var(--gray-50); outline:none; }
.goto-page button { height:34px; padding:0 14px; border:none; background:var(--primary); color:#fff; border-radius:var(--radius-sm); font-size:13px; font-weight:600; cursor:pointer; }

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
.mform-select { width:100%; height:46px; border:1.5px solid var(--gray-200) !important; border-radius:var(--radius-md) !important; padding:0 14px !important; font-size:14px !important; color:var(--gray-900); background:var(--gray-50); outline:none; transition:all .18s; appearance:none; cursor:pointer; }
.mform-select:focus { border-color:var(--primary) !important; background:#fff; box-shadow:0 0 0 3px rgba(37,99,235,.08) !important; }
.modal-footer-btns { display:flex; gap:10px; justify-content:flex-end; padding-top:8px; }
.btn-modal-cancel { height:42px; padding:0 20px; border:1.5px solid var(--gray-200); border-radius:var(--radius-full); background:#fff; color:var(--gray-500); font-size:13.5px; font-weight:600; cursor:pointer; transition:all .15s; }
.btn-modal-cancel:hover { border-color:var(--gray-300); color:var(--gray-700); }
.btn-modal-submit { height:42px; padding:0 24px; border:none; border-radius:var(--radius-full); background:var(--primary); color:#fff; font-size:13.5px; font-weight:700; cursor:pointer; transition:all .18s; box-shadow:0 2px 8px rgba(37,99,235,.28); display:flex; align-items:center; gap:6px; }
.btn-modal-submit:hover { background:var(--primary-dark); transform:translateY(-1px); box-shadow:0 4px 14px rgba(37,99,235,.36); }
.btn-modal-submit.green { background:linear-gradient(135deg,#059669,#047857); box-shadow:0 2px 8px rgba(5,150,105,.28); }
.btn-modal-submit.green:hover { box-shadow:0 4px 14px rgba(5,150,105,.36); }

/* ── SELECT2 CUSTOM THEME ── */
.select2-container { width:100% !important; }
.select2-container--default .select2-selection--multiple {
    border:1.5px solid var(--gray-200) !important;
    border-radius:var(--radius-md) !important;
    background:var(--gray-50) !important;
    min-height:46px !important;
    padding:4px 8px !important;
    cursor:pointer;
    transition:all .18s;
}
.select2-container--default.select2-container--focus .select2-selection--multiple {
    border-color:var(--primary) !important;
    background:#fff !important;
    box-shadow:0 0 0 3px rgba(37,99,235,.08) !important;
    outline:none !important;
}
.select2-container--default .select2-selection--multiple .select2-selection__rendered { padding:0 !important; }
.select2-container--default .select2-selection--multiple .select2-selection__choice {
    background:var(--primary-light) !important;
    border:1px solid var(--primary-mid) !important;
    color:var(--primary) !important;
    border-radius:var(--radius-full) !important;
    padding:2px 10px 2px 8px !important;
    font-size:12px !important;
    font-weight:700 !important;
    margin:3px 4px 3px 0 !important;
    line-height:1.7 !important;
}
.select2-container--default .select2-selection--multiple .select2-selection__choice__remove {
    color:var(--primary) !important;
    margin-right:5px !important;
    font-size:13px !important;
    font-weight:700 !important;
    opacity:.6;
    border:none !important;
    background:none !important;
    float:left;
}
.select2-container--default .select2-selection--multiple .select2-selection__choice__remove:hover {
    color:var(--danger) !important;
    opacity:1;
    background:none !important;
}
.select2-container--default .select2-selection--multiple .select2-selection__placeholder {
    color:var(--gray-400) !important;
    font-size:14px !important;
    font-weight:500 !important;
    line-height:36px !important;
}
.select2-dropdown {
    border:1.5px solid var(--primary-mid) !important;
    border-radius:var(--radius-md) !important;
    box-shadow:var(--shadow-md) !important;
    font-size:13.5px !important;
    overflow:hidden;
    z-index:9999 !important;
}
.select2-container--default .select2-results__option {
    padding:9px 14px !important;
    font-weight:500 !important;
    color:var(--gray-700) !important;
    transition:background .12s;
}
.select2-container--default .select2-results__option--highlighted[aria-selected] {
    background:var(--primary-light) !important;
    color:var(--primary) !important;
}
.select2-container--default .select2-results__option[aria-selected="true"] {
    background:var(--primary) !important;
    color:#fff !important;
}
.select2-container--default .select2-search--dropdown .select2-search__field {
    border:1.5px solid var(--gray-200) !important;
    border-radius:var(--radius-sm) !important;
    padding:7px 10px !important;
    font-size:13px !important;
    outline:none !important;
    margin:6px !important;
    width:calc(100% - 12px) !important;
    box-sizing:border-box !important;
    font-family:var(--font) !important;
}
.select2-container--default .select2-search--dropdown .select2-search__field:focus {
    border-color:var(--primary) !important;
    box-shadow:0 0 0 3px rgba(37,99,235,.08) !important;
}
.select2-results { max-height:200px !important; }

@media (max-width:768px) {
    .filter-bar { gap:8px; }
    .filter-search-wrap { max-width:100%; }
    .pagination-bar { flex-direction:column; align-items:flex-start; }
    .settings-tab-bar { gap:3px; }
    .stab { padding:8px 12px; font-size:12px; }
    .user-header-row { flex-direction:column; align-items:flex-start; }
}
</style>
@endpush

@push('script-page')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>

/* ══════════════════════════════════════
   SELECT2 — init on modal open
══════════════════════════════════════ */
$('#AddUserModal').on('shown.bs.modal', function () {
    var $modal = $(this);
    // Destroy if already initialized, then re-init
    var $sel = $modal.find('#add_role_id');
    if ($sel.hasClass('select2-hidden-accessible')) {
        $sel.select2('destroy');
    }
    $sel.select2({
        dropdownParent: $modal,
        placeholder: 'Select roles…',
        allowClear: true,
        width: '100%',
    });
});

$('#AddUserModal').on('hidden.bs.modal', function () {
    $(this).find('#add_role_id').val(null).trigger('change');
});

$('#editTeamModal').on('shown.bs.modal', function (event) {
    var $modal = $(this);
    var btn    = $(event.relatedTarget);
    var form   = $modal.find('#editUserForm');

    /* ── Init Select2 for roles ── */
    var $rolesSel = $modal.find('#role_id');
    if ($rolesSel.hasClass('select2-hidden-accessible')) {
        $rolesSel.select2('destroy');
    }
    $rolesSel.select2({
        dropdownParent: $modal,
        placeholder: 'Select roles…',
        allowClear: true,
        width: '100%',
    });

    /* ── Populate all fields ── */
    form.attr('action', "{{ route('organization.settings.user.update', ['id' => '::ID::']) }}"
        .replace('::ID::', btn.data('id')));

    form.find('#employee_id').val(btn.data('id'));
    form.find('#editid').val(btn.data('id'));
    form.find('#teamName').val(btn.data('name'));
    form.find('#teamEmail').val(btn.data('email'));
    form.find('#mobile_no').val(btn.data('mobile_no'));
    form.find('#dob').val(btn.data('dob'));
    form.find('#date_of_join').val(btn.data('date_of_join'));
    form.find('#employeeId').val(btn.data('employee_id'));
    form.find('#editpassword').val('');
    form.find('#gender').val(btn.data('gender'));
    form.find('#designation_id').val(btn.data('designation_id'));
    form.find('#shift_id').val(btn.data('shift_id'));
    form.find('#team_id').val(btn.data('team_id'));
    form.find('#is_active').val(String(btn.data('is_active')));

    var apkType = btn.data('apk_type') || "{{ USER_APK_TYPE_SYSTEM_TRACK }}";
    form.find('input[name="track_type"]').prop('checked', false);
    form.find('input[name="track_type"][value="' + apkType + '"]').prop('checked', true);

    /* ── Set Select2 roles (small delay ensures DOM ready) ── */
    var roleIds = btn.data('role_ids') || [];
    setTimeout(function () {
        var vals = Array.isArray(roleIds) ? roleIds.map(String) : [];
        $rolesSel.val(vals).trigger('change');
    }, 80);
});

$('#editTeamModal').on('hidden.bs.modal', function () {
    $(this).find('#role_id').val(null).trigger('change');
});

/* ══════════════════════════════════════
   AJAX: Add User
══════════════════════════════════════ */
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

/* ══════════════════════════════════════
   AJAX: Edit User
══════════════════════════════════════ */
$('#submitEditUser').click(function (e) {
    e.preventDefault();
    var form = $('#editUserForm')[0];
    var formData = new FormData(form);
    formData.append('_method', 'PUT');
    $('#editUserForm .text-danger').text('');
    $.ajax({
        url: $('#editUserForm').attr('action'), type: 'POST',
        data: formData, contentType: false, processData: false,
        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
        success: function () {
            $('#editTeamModal').modal('hide');
            show_toastr('Success', 'User updated successfully.', 'success');
            setTimeout(function(){ location.reload(); }, 900);
        },
        error: function (xhr) {
            if (xhr.status === 422) {
                $.each(xhr.responseJSON.errors, function (key, value) {
                    $('#editerror-' + key).text(value[0]);
                });
            } else { show_toastr('Error', 'Something went wrong. Please try again.', 'error'); }
        }
    });
});

/* ══════════════════════════════════════
   Toggle Active Status
══════════════════════════════════════ */
$(document).ready(function () {
    $('.toggle-active').change(function () {
        var cb = $(this);
        $.ajax({
            url: cb.data('url'), method: 'POST',
            data: { _token: '{{ csrf_token() }}', is_active: cb.is(':checked') ? 1 : 0 },
            success: function () { show_toastr('Success', 'Status updated successfully.', 'success'); },
            error: function () {
                show_toastr('Error', 'Failed to update status.', 'error');
                cb.prop('checked', !cb.is(':checked'));
            }
        });
    });
});

/* ══════════════════════════════════════
   Select All Checkbox
══════════════════════════════════════ */
document.getElementById('serialcheckbox').addEventListener('change', function () {
    document.querySelectorAll('.employee-checkbox').forEach(cb => cb.checked = this.checked);
});
document.querySelectorAll('.employee-checkbox').forEach(cb => {
    cb.addEventListener('change', function () {
        var all = document.querySelectorAll('.employee-checkbox');
        var chk = document.querySelectorAll('.employee-checkbox:checked');
        document.getElementById('serialcheckbox').checked = all.length === chk.length;
    });
});

/* ══════════════════════════════════════
   Team filter auto-submit
══════════════════════════════════════ */
$(document).ready(function () {
    $('#teamSelect').on('change', function () { $(this).closest('form').submit(); });
});

/* ══════════════════════════════════════
   User limit warning
══════════════════════════════════════ */
document.addEventListener('DOMContentLoaded', function () {
    var addBtn = document.querySelector('.btn-add.disabled-btn');
    if (addBtn) {
        addBtn.addEventListener('click', function (e) {
            e.preventDefault();
            Swal.fire({ icon:'warning', title:'User Limit Reached', text:'You have reached the maximum number of users allowed.', confirmButtonText:'OK' });
        });
    }
});

/* ── Per page ── */
var perPageSel = document.getElementById('perPageSelect');
if (perPageSel) perPageSel.addEventListener('change', function(){ document.getElementById('perPageForm').submit(); });
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

    {{-- HEADER + STAT --}}
    @php $canAddUser = $activeUserCount < auth()->user()->owner_max_users; @endphp
    <div class="user-header-row">
        <div>
            <h1 class="page-title-main">User Settings</h1>
            <p class="page-title-sub">Manage employees, roles and access across your organisation</p>
        </div>
        <div class="user-stat-chip">
            <div class="user-stat-icon">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
            </div>
            <div>
                <div class="user-stat-label">Active Users</div>
                <div class="user-stat-value">{{ $activeUserCount }} <span style="font-size:13px;color:var(--gray-400);font-weight:600;">/ {{ auth()->user()->owner_max_users  }}</span></div>
                <div class="user-stat-sub">Excludes inactive / deactivated</div>
            </div>
        </div>
    </div>

    {{-- FILTER BAR --}}
    <div class="filter-bar">
        <form method="GET" action="{{ route('organization.settings.user') }}" style="display:contents;">
            @foreach(request()->except(['search','page']) as $k => $v)
                <input type="hidden" name="{{ $k }}" value="{{ $v }}">
            @endforeach
            <div class="filter-search-wrap">
                <span class="search-icon">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                </span>
                <input type="text" name="search" placeholder="Search by name or email…" value="{{ request('search') }}" autocomplete="off">
                <button type="submit" class="search-btn">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                </button>
            </div>
        </form>

        <form method="GET" action="{{ route('organization.settings.user') }}" id="teamFilterForm">
            @foreach(request()->except(['team_id','page']) as $k => $v)
                <input type="hidden" name="{{ $k }}" value="{{ $v }}">
            @endforeach
            <select class="filter-select" name="team_id" id="teamSelect" style="min-width:160px;">
                <option value="" {{ !request('team_id') ? 'selected' : '' }}>All Teams</option>
                @foreach ($teams as $team)
                    <option value="{{ $team->id }}" {{ request('team_id') == $team->id ? 'selected' : '' }}>{{ $team->name }}</option>
                @endforeach
            </select>
        </form>

      

        <div class="filter-divider"></div>
        <a href="{{ route('organization.settings.user') }}" class="btn-download">
            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 12a9 9 0 1 0 9-9 9.75 9.75 0 0 0-6.74 2.74L3 8"/><path d="M3 3v5h5"/></svg>
            Reset
        </a>
        <div class="filter-spacer"></div>
        <a href="{{ route('organization.settings.user.download', request()->all()) }}" class="btn-download">
            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
            Download
        </a>
        <button type="button" class="btn-add {{ $canAddUser ? '' : 'disabled-btn' }}"
                {{ $canAddUser ? 'data-bs-toggle=modal data-bs-target=#AddUserModal' : '' }}>
            <span class="plus-circle">+</span> Add User
        </button>
    </div>

    {{-- TABLE --}}
    <div class="user-table-card">
        <div class="rows-select-wrap">
            <span>Show</span>
            <form id="perPageForm" method="GET" action="{{ url()->current() }}" style="display:inline-flex;align-items:center;">
                @foreach(request()->except(['page','per_page']) as $key => $value)
                    <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                @endforeach
                <select name="per_page" id="perPageSelect">
                    @foreach([5,10,20,50] as $size)
                        <option value="{{ $size }}" {{ request('per_page',10)==$size ? 'selected' : '' }}>{{ $size }}</option>
                    @endforeach
                </select>
            </form>
            <span>entries</span>
        </div>

        <div style="overflow-x:auto;">
            <table style="width:100%;border-collapse:collapse;">
                <thead>
                    <tr style="background:var(--gray-50);border-bottom:2px solid var(--gray-200);">
                        <th style="padding:12px 16px;font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.7px;color:var(--gray-400);width:50px;">
                            <div style="display:flex;align-items:center;gap:8px;">
                                <input type="checkbox" id="serialcheckbox" style="width:15px;height:15px;accent-color:var(--primary);cursor:pointer;">
                                #
                            </div>
                        </th>
                        <th style="padding:12px 16px;font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.7px;color:var(--gray-400);">Employee</th>
                        <th style="padding:12px 16px;font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.7px;color:var(--gray-400);">Email</th>
                        <th style="padding:12px 16px;font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.7px;color:var(--gray-400);">Emp ID</th>
                        <th style="padding:12px 16px;font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.7px;color:var(--gray-400);text-align:center;">Type</th>
                        <th style="padding:12px 16px;font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.7px;color:var(--gray-400);text-align:center;">Status</th>
                        <th style="padding:12px 16px;font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.7px;color:var(--gray-400);">Shift</th>
                        @if (Auth::user()->type == 'company')
                            <th style="padding:12px 16px;font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.7px;color:var(--gray-400);">Impersonate</th>
                        @endif
                        <th style="padding:12px 16px;font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.7px;color:var(--gray-400);text-align:right;padding-right:22px;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                @php $i = ($employees->currentPage() - 1) * $employees->perPage() + 1; @endphp
                @forelse($employees as $employee)
                    <tr style="border-bottom:1px solid var(--gray-100);transition:background .12s;" onmouseover="this.style.background='#FAFBFF'" onmouseout="this.style.background=''">
                        <td style="padding:13px 16px;vertical-align:middle;">
                            <div style="display:flex;align-items:center;gap:8px;">
                                <input type="checkbox" class="employee-checkbox" data-id="{{ $employee->id }}" style="width:15px;height:15px;accent-color:var(--primary);cursor:pointer;">
                                <span style="font-size:12px;font-weight:600;color:var(--gray-400);">{{ $i++ }}</span>
                            </div>
                        </td>
                        <td style="padding:13px 16px;vertical-align:middle;">
                            <div class="emp-cell">
                                @if(($employee['gender'] ?? null) === GENDER_MALE)
                                    <img src="{{ asset('assets/assestsnew/menimg.png') }}" alt="M" class="emp-avatar">
                                @else
                                    <img src="{{ asset('assets/assestsnew/femaile-report.svg') }}" alt="F" class="emp-avatar">
                                @endif
                                <span class="emp-name">{{ $employee->name ?? 'N/A' }}</span>
                            </div>
                        </td>
                        <td style="padding:13px 16px;vertical-align:middle;font-size:13px;color:var(--gray-500);">{{ $employee->email }}</td>
                        <td style="padding:13px 16px;vertical-align:middle;">
                            <span class="emp-id-badge">{{ $employee->employee_id ?? '—' }}</span>
                        </td>
                        <td style="padding:13px 16px;vertical-align:middle;text-align:center;">
                            <div class="track-icon-wrap" style="margin:0 auto;">
                                @if($employee->track_type === USER_APK_TYPE_SYSTEM_TRACK)
                                    <img data-bs-toggle="tooltip" data-bs-title="System Track" src="{{ asset('assets/assestsnew/monitoring.svg') }}" alt="System Track">
                                @elseif($employee->track_type === USER_APK_TYPE_FIELD_TRACK)
                                    <img data-bs-toggle="tooltip" data-bs-title="Field Track" src="{{ asset('assets/assestsnew/track.svg') }}" alt="Field Track">
                                @elseif($employee->track_type === USER_APK_TYPE_CALL_TRACK)
                                    <img data-bs-toggle="tooltip" data-bs-title="Call Track" src="{{ asset('assets/assestsnew/callrecord.svg') }}" alt="Call Track">
                                @endif
                            </div>
                        </td>
                        <td style="padding:13px 16px;vertical-align:middle;text-align:center;">
                            <label class="toggle-switch">
                                <input type="checkbox" class="toggle-active"
                                       data-url="{{ route('organization.settings.user.toggleactive', $employee->id) }}"
                                       {{ $employee->emp_is_active == 1 ? 'checked' : '' }}>
                                <span class="toggle-slider"></span>
                            </label>
                        </td>
                        <td style="padding:13px 16px;vertical-align:middle;">
                            @if($employee->shift_id && $employee->shift)
                                <span class="shift-badge">{{ $employee->shift->shift_name }}</span>
                            @else
                                <span style="color:var(--gray-300);font-size:13px;">—</span>
                            @endif
                        </td>
                        @if (Auth::user()->type == 'company')
                            <td style="padding:13px 16px;vertical-align:middle;">
                                <a href="{{ route('organization.login.with.standard-user', $employee->id) }}" class="act-btn impersonate">
                                    <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                                    Login
                                </a>
                            </td>
                        @endif
                        <td style="padding:13px 16px;vertical-align:middle;">
                            <div style="display:flex;justify-content:flex-end;">
                                <button class="act-btn edit"
                                        data-bs-toggle="modal" data-bs-target="#editTeamModal"
                                        data-id="{{ $employee->id }}"
                                        data-name="{{ e($employee->name) }}"
                                        data-email="{{ e($employee->email) }}"
                                        data-mobile_no="{{ $employee->mobile_no }}"
                                        data-dob="{{ $employee->dob }}"
                                        data-date_of_join="{{ $employee->company_doj }}"
                                        data-gender="{{ $employee->gender }}"
                                        data-designation_id="{{ $employee->emp_designation_id }}"
                                        data-shift_id="{{ $employee->shift_id }}"
                                        data-team_id="{{ $employee->emp_team_id }}"
                                        data-role_ids='@json($employee->roles->pluck("id"))'
                                        data-employee_id="{{ $employee->employee_id }}"
                                        data-is_active="{{ $employee->emp_is_active }}"
                                        data-apk_type="{{ $employee->track_type ?? USER_APK_TYPE_SYSTEM_TRACK }}"
                                        title="Edit user">
                                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="{{ Auth::user()->type == 'company' ? 9 : 8 }}">
                            <div class="empty-state">
                                <div class="empty-icon-wrap">
                                    <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/></svg>
                                </div>
                                <p class="empty-title">No users found</p>
                                <p class="empty-sub">Try adjusting your filters or add a new user.</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- PAGINATION --}}
    @if($employees->total() > 0)
    <div class="pagination-bar">
        <div class="pagi-info">
            Showing <span>{{ $employees->firstItem() }}–{{ $employees->lastItem() }}</span> of <span>{{ $employees->total() }}</span> users
        </div>
        <ul class="pagi-pages">
            <li class="{{ $employees->onFirstPage() ? 'disabled' : '' }}">
                <a href="{{ $employees->url(1) }}"><svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="11 17 6 12 11 7"/><polyline points="18 17 13 12 18 7"/></svg></a>
            </li>
            <li class="{{ $employees->onFirstPage() ? 'disabled' : '' }}">
                <a href="{{ $employees->previousPageUrl() }}"><svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"/></svg></a>
            </li>
            @php $start = max(1,$employees->currentPage()-2); $end = min($start+4,$employees->lastPage()); @endphp
            @for($p=$start; $p<=$end; $p++)
                <li class="{{ $employees->currentPage()==$p ? 'active_pagination' : '' }}">
                    <a href="{{ $employees->url($p) }}">{{ $p }}</a>
                </li>
            @endfor
            <li class="{{ !$employees->hasMorePages() ? 'disabled' : '' }}">
                <a href="{{ $employees->nextPageUrl() }}"><svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"/></svg></a>
            </li>
            <li class="{{ !$employees->hasMorePages() ? 'disabled' : '' }}">
                <a href="{{ $employees->url($employees->lastPage()) }}"><svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="13 17 18 12 13 7"/><polyline points="6 17 11 12 6 7"/></svg></a>
            </li>
        </ul>
        <div class="pagi-right">
            <form id="perPageForm2" method="GET" action="{{ url()->current() }}" style="display:flex;align-items:center;">
                @foreach(request()->except(['page','per_page']) as $key => $value)
                    <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                @endforeach
                <div class="per-page-select">
                    <span>Rows:</span>
                    <select name="per_page" onchange="document.getElementById('perPageForm2').submit()">
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
                    <input type="number" name="page" min="1" max="{{ $employees->lastPage() }}" placeholder="—">
                    <button type="submit">Go</button>
                </div>
            </form>
        </div>
    </div>
    @endif

</div>

{{-- ══ ADD USER MODAL ══ --}}
<div class="modal fade" id="AddUserModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width:760px;">
        <div class="modal-content">
            <div class="modal-top-band">
                <button type="button" class="modal-close-btn" data-bs-dismiss="modal">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                </button>
                <div class="modal-top-icon">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
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
                            <input type="tel" class="mform-input" name="mobile_no" placeholder="10-digit number">
                            <div class="text-danger mt-1 small" id="adderror-mobile_no"></div>
                        </div>
                        <div class="col-md-6">
                            <label class="mform-label">Team <span style="color:#DC2626">*</span></label>
                            <select class="mform-select" name="team_id">
                                <option disabled selected>Select Team</option>
                                @foreach ($teams as $team)
                                    <option value="{{ $team->id }}">{{ $team->name }}</option>
                                @endforeach
                            </select>
                            <div class="text-danger mt-1 small" id="adderror-team_id"></div>
                        </div>
                        <div class="col-md-6">
                            <label class="mform-label">Roles</label>
                            <select name="role_id[]" id="add_role_id" multiple style="width:100%;">
                                @foreach ($roles as $role)
                                    <option value="{{ $role->id }}">{{ \Str::title($role->name) }}</option>
                                @endforeach
                            </select>
                            <div class="text-danger mt-1 small" id="adderror-role_id"></div>
                        </div>
                        <div class="col-md-6">
                            <label class="mform-label">Designation <span style="color:#DC2626">*</span></label>
                            <select class="mform-select" name="designation_id">
                                <option disabled selected>Select Designation</option>
                                @foreach ($designations as $designation)
                                    <option value="{{ $designation->id }}">{{ $designation->name }}</option>
                                @endforeach
                            </select>
                            <div class="text-danger mt-1 small" id="adderror-designation_id"></div>
                        </div>
                        <div class="col-md-6">
                            <label class="mform-label">Shift <span style="color:#DC2626">*</span></label>
                            <select class="mform-select" name="shift_id">
                                <option disabled selected>Select Shift</option>
                                @foreach ($shifts as $shift)
                                    <option value="{{ $shift->id }}">{{ $shift->shift_name }}</option>
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
                            <div style="display:flex;flex-wrap:wrap;gap:12px;margin-top:8px;">
                                @foreach(USER_APK_TYPES as $key => $value)
                                    <label style="display:flex;align-items:center;gap:6px;font-size:13px;font-weight:600;color:var(--gray-600);cursor:pointer;">
                                        <input type="radio" name="track_type" value="{{ $value }}" {{ $value === USER_APK_TYPE_SYSTEM_TRACK ? 'checked' : '' }} style="accent-color:var(--primary);">
                                        {{ ucfirst(str_replace('_', ' ', $value)) }}
                                    </label>
                                @endforeach
                            </div>
                            <div class="text-danger mt-1 small" id="adderror-apk_type"></div>
                        </div>
                    </div>
                    <div class="modal-footer-btns mt-2">
                        <button type="button" class="btn-modal-cancel" data-bs-dismiss="modal">Cancel</button>
                        <button type="button" class="btn-modal-submit" id="submitadduser">
                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                            Create User
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- ══ EDIT USER MODAL ══ --}}
<div class="modal fade" id="editTeamModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width:760px;">
        <div class="modal-content">
            <div class="modal-top-band green">
                <button type="button" class="modal-close-btn" data-bs-dismiss="modal">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                </button>
                <div class="modal-top-icon">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                </div>
                <h5 class="modal-top-title">Edit User</h5>
                <p class="modal-top-sub">Update employee details and settings</p>
            </div>
            <div class="modal-body">
                <form id="editUserForm" method="POST" action="">
                    @csrf @method('PUT')
                    <input type="hidden" id="employee_id" name="id">
                    <input type="hidden" id="editid">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="mform-label">Name <span style="color:#DC2626">*</span></label>
                            <input type="text" class="mform-input" id="teamName" name="name" autocomplete="off">
                            <div class="text-danger mt-1 small" id="editerror-name"></div>
                        </div>
                        <div class="col-md-6">
                            <label class="mform-label">Employee ID <span style="color:#DC2626">*</span></label>
                            <input type="text" class="mform-input" id="employeeId" name="employee_id">
                            <div class="text-danger mt-1 small" id="editerror-employee_id"></div>
                        </div>
                        <div class="col-md-6">
                            <label class="mform-label">Email <span style="color:#DC2626">*</span></label>
                            <input type="email" class="mform-input" id="teamEmail" name="email">
                            <div class="text-danger mt-1 small" id="editerror-email"></div>
                        </div>
                        <div class="col-md-6">
                            <label class="mform-label">New Password</label>
                            <input type="password" class="mform-input" id="editpassword" name="password" placeholder="Leave blank to keep current" minlength="6">
                            <div class="text-danger mt-1 small" id="editerror-password"></div>
                        </div>
                        <div class="col-md-6">
                            <label class="mform-label">Gender <span style="color:#DC2626">*</span></label>
                            <select class="mform-select" id="gender" name="gender">
                                <option value="male">Male</option>
                                <option value="female">Female</option>
                            </select>
                            <div class="text-danger mt-1 small" id="editerror-gender"></div>
                        </div>
                        <div class="col-md-6">
                            <label class="mform-label">Date of Birth</label>
                            <input type="date" class="mform-input" id="dob" name="dob">
                            <div class="text-danger mt-1 small" id="editerror-dob"></div>
                        </div>
                        <div class="col-md-6">
                            <label class="mform-label">Date of Joining <span style="color:#DC2626">*</span></label>
                            <input type="date" class="mform-input" id="date_of_join" name="date_of_join">
                            <div class="text-danger mt-1 small" id="editerror-date_of_join"></div>
                        </div>
                        <div class="col-md-6">
                            <label class="mform-label">Phone <span style="color:#DC2626">*</span></label>
                            <input type="tel" class="mform-input" id="mobile_no" name="mobile_no">
                            <div class="text-danger mt-1 small" id="editerror-mobile_no"></div>
                        </div>
                        <div class="col-md-6">
                            <label class="mform-label">Team <span style="color:#DC2626">*</span></label>
                            <select class="mform-select" id="team_id" name="team_id">
                                @foreach($teams as $team)
                                    <option value="{{ $team->id }}">{{ $team->name }}</option>
                                @endforeach
                            </select>
                            <div class="text-danger mt-1 small" id="editerror-team_id"></div>
                        </div>
                        <div class="col-md-6">
                            <label class="mform-label">Roles</label>
                            {{-- NOTE: plain select, Select2 initialized via JS on modal open --}}
                            <select id="role_id" name="role_id[]" multiple style="width:100%;">
                                @foreach ($roles as $role)
                                    <option value="{{ $role->id }}">{{ $role->name }}</option>
                                @endforeach
                            </select>
                            <div class="text-danger mt-1 small" id="editerror-role_id"></div>
                        </div>
                        <div class="col-md-6">
                            <label class="mform-label">Designation <span style="color:#DC2626">*</span></label>
                            <select class="mform-select" id="designation_id" name="designation_id">
                                @foreach($designations as $designation)
                                    <option value="{{ $designation->id }}">{{ $designation->name }}</option>
                                @endforeach
                            </select>
                            <div class="text-danger mt-1 small" id="editerror-designation_id"></div>
                        </div>
                        <div class="col-md-6">
                            <label class="mform-label">Shift <span style="color:#DC2626">*</span></label>
                            <select class="mform-select" id="shift_id" name="shift_id">
                                @foreach($shifts as $shift)
                                    <option value="{{ $shift->id }}">{{ $shift->shift_name }}</option>
                                @endforeach
                            </select>
                            <div class="text-danger mt-1 small" id="editerror-shift_id"></div>
                        </div>
                        <div class="col-md-6">
                            <label class="mform-label">Status</label>
                            <select class="mform-select" id="is_active" name="is_active">
                                <option value="1">Active</option>
                                <option value="0">Inactive</option>
                            </select>
                            <div class="text-danger mt-1 small" id="editerror-is_active"></div>
                        </div>
                        <div class="col-md-6">
                            <label class="mform-label">Tracking Type <span style="color:#DC2626">*</span></label>
                            <div style="display:flex;flex-wrap:wrap;gap:12px;margin-top:8px;">
                                @foreach(USER_APK_TYPES as $key => $value)
                                    <label style="display:flex;align-items:center;gap:6px;font-size:13px;font-weight:600;color:var(--gray-600);cursor:pointer;">
                                        <input type="radio" name="track_type" value="{{ $value }}" {{ $value === USER_APK_TYPE_SYSTEM_TRACK ? 'checked' : '' }} style="accent-color:var(--primary);">
                                        {{ ucfirst(str_replace('_', ' ', $value)) }}
                                    </label>
                                @endforeach
                            </div>
                            <div class="text-danger mt-1 small" id="editerror-apk_type"></div>
                        </div>
                    </div>
                    <div class="modal-footer-btns mt-2">
                        <button type="button" class="btn-modal-cancel" data-bs-dismiss="modal">Cancel</button>
                        <button type="button" class="btn-modal-submit green" id="submitEditUser">
                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
                            Save Changes
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection