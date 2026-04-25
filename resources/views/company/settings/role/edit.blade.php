@extends('company.layouts.company')
@section('page-title') {{ __('Edit Role') }} @endsection
@section('page-icon') {{ asset('assets/assestsnew/settings.svg') }} @endsection

@push('css-page')
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<style>
:root {
    --primary: #2563EB; --primary-light: #EFF6FF; --primary-mid: #BFDBFE; --primary-dark: #1D4ED8;
    --success: #059669; --success-light: #ECFDF5;
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

/* ── PAGE HEADER ── */
.role-form-header { display:flex; align-items:center; gap:16px; margin-bottom:28px; flex-wrap:wrap; justify-content:space-between; }
.role-form-title { font-size:22px; font-weight:800; color:var(--gray-900); margin:0 0 4px; letter-spacing:-.4px; }
.role-form-sub { font-size:13px; color:var(--gray-400); margin:0; }
.role-name-badge { display:inline-flex; align-items:center; gap:6px; background:var(--primary-light); border:1px solid var(--primary-mid); color:var(--primary); border-radius:var(--radius-full); padding:4px 12px; font-size:12px; font-weight:700; margin-top:6px; }
.btn-back { display:inline-flex; align-items:center; gap:6px; height:38px; padding:0 16px; border-radius:var(--radius-full); border:1.5px solid var(--gray-200); background:#fff; color:var(--gray-500); font-size:13px; font-weight:600; cursor:pointer; text-decoration:none; transition:all .18s; }
.btn-back:hover { border-color:var(--primary); color:var(--primary); background:var(--primary-light); }

/* ── FORM CARD ── */
.form-section { background:#fff; border:1px solid var(--gray-200); border-radius:var(--radius-lg); box-shadow:var(--shadow-sm); margin-bottom:20px; overflow:hidden; }
.form-section-header { display:flex; align-items:center; justify-content:space-between; padding:16px 20px; border-bottom:1px solid var(--gray-100); background:var(--gray-50); }
.form-section-title { display:flex; align-items:center; gap:10px; }
.form-section-icon { width:32px; height:32px; border-radius:var(--radius-sm); display:flex; align-items:center; justify-content:center; background:var(--primary-light); color:var(--primary); flex-shrink:0; }
.form-section-label { font-size:14px; font-weight:700; color:var(--gray-700); margin:0; }
.form-section-sub { font-size:12px; color:var(--gray-400); margin:0; }
.form-section-body { padding:20px; }
.select-actions { display:flex; align-items:center; gap:6px; }
.sel-btn { font-size:12px; font-weight:700; border:none; background:none; cursor:pointer; padding:5px 10px; border-radius:var(--radius-full); transition:all .15s; }
.sel-btn.primary { color:var(--primary); background:var(--primary-light); }
.sel-btn.primary:hover { background:var(--primary-mid); }
.sel-btn.gray { color:var(--gray-500); background:var(--gray-100); }
.sel-btn.gray:hover { background:var(--gray-200); color:var(--gray-700); }
.sel-sep { color:var(--gray-300); font-size:12px; }

/* ── FORM INPUTS ── */
.mform-label { font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:.6px; color:var(--gray-400); margin-bottom:7px; display:block; }
.mform-input { width:100%; height:46px; border:1.5px solid var(--gray-200); border-radius:var(--radius-md); padding:0 14px; font-size:14px; color:var(--gray-900); background:var(--gray-50); outline:none; transition:all .18s; }
.mform-input:focus { border-color:var(--primary); background:#fff; box-shadow:0 0 0 3px rgba(37,99,235,.08); }
.mform-textarea { width:100%; border:1.5px solid var(--gray-200); border-radius:var(--radius-md); padding:12px 14px; font-size:14px; color:var(--gray-900); background:var(--gray-50); outline:none; transition:all .18s; resize:vertical; min-height:90px; margin-top: 0px !important; }
.mform-textarea:focus { border-color:var(--primary); background:#fff; box-shadow:0 0 0 3px rgba(37,99,235,.08); }

/* ── CHECKBOX GRID ── */
.perm-grid { display:grid; grid-template-columns:repeat(auto-fill, minmax(180px, 1fr)); gap:10px; }
.perm-item { display:flex; align-items:center; gap:10px; padding:11px 14px; background:var(--gray-50); border:1.5px solid var(--gray-200); border-radius:var(--radius-md); cursor:pointer; transition:all .15s; }
.perm-item:hover { border-color:var(--primary-mid); background:var(--primary-light); }
.perm-item input[type=checkbox] { width:16px; height:16px; accent-color:var(--primary); cursor:pointer; flex-shrink:0; }
.perm-item label { font-size:13px; font-weight:600; color:var(--gray-600); cursor:pointer; margin:0; line-height:1.3; }
.perm-item:has(input:checked) { border-color:var(--primary); background:var(--primary-light); }
.perm-item:has(input:checked) label { color:var(--primary); }

/* ── ACTION BUTTONS ── */
.form-actions { display:flex; justify-content:flex-end; gap:10px; margin-top:8px; }
.btn-cancel { height:42px; padding:0 20px; border:1.5px solid var(--gray-200); border-radius:var(--radius-full); background:#fff; color:var(--gray-500); font-size:13.5px; font-weight:600; cursor:pointer; transition:all .15s; text-decoration:none; display:inline-flex; align-items:center; }
.btn-cancel:hover { border-color:var(--gray-300); color:var(--gray-700); }
.btn-submit { height:42px; padding:0 28px; border:none; border-radius:var(--radius-full); background:linear-gradient(135deg,#059669,#047857); color:#fff; font-size:13.5px; font-weight:700; cursor:pointer; transition:all .18s; box-shadow:0 2px 8px rgba(5,150,105,.28); display:inline-flex; align-items:center; gap:6px; }
.btn-submit:hover { transform:translateY(-1px); box-shadow:0 4px 14px rgba(5,150,105,.36); }
.btn-submit:disabled { opacity:.6; cursor:not-allowed; transform:none; }

@media (max-width:768px) {
    .perm-grid { grid-template-columns:repeat(auto-fill, minmax(150px, 1fr)); }
    .settings-tab-bar { gap:3px; }
    .stab { padding:8px 12px; font-size:12px; }
}
</style>
@endpush

@push('script-page')
<script>
document.addEventListener('DOMContentLoaded', function () {
    function bindToggle(selectId, deselectId, cls) {
        var sel = document.getElementById(selectId);
        var des = document.getElementById(deselectId);
        if (sel) sel.addEventListener('click', function(e){ e.preventDefault(); document.querySelectorAll('.' + cls).forEach(cb => cb.checked = true); });
        if (des) des.addEventListener('click', function(e){ e.preventDefault(); document.querySelectorAll('.' + cls).forEach(cb => cb.checked = false); });
    }
    bindToggle('selectAllFeatures','deselectAllFeatures','feature-checkbox');
    bindToggle('selectAllReports','deselectAllReports','report-checkbox');
    bindToggle('selectAllSettings','deselectAllSettings','settings-checkbox');
});

$(document).ready(function () {
    $('#editsubmituser').on('click', function (e) {
        e.preventDefault();
        var form = $(this).closest('form');
        $('.text-danger').text('');
        $(this).prop('disabled', true).text('Saving…');

        $.ajax({
            url: form.attr('action'), method: 'POST',
            data: {
                _token: $('input[name="_token"]').val(),
                _method: 'PUT',
                name: $('input[name="name"]').val(),
                description: $('textarea[name="description"]').val(),
                features: $('input[name="features[]"]:checked').map(function(){ return this.value; }).get(),
                reports: $('input[name="reports[]"]:checked').map(function(){ return this.value; }).get(),
                allReports: $('input[name="allReports[]"]:checked').map(function(){ return this.value; }).get(),
                settings: $('input[name="settings[]"]:checked').map(function(){ return this.value; }).get(),
            },
            success: function (response) {
                show_toastr('Success', response.message ?? 'Role updated successfully!', 'success');
                setTimeout(function(){ window.location.href = response.redirect ?? "{{ route('organization.settings.role') }}"; }, 900);
            },
            error: function (xhr) {
                if (xhr.status === 422) {
                    $.each(xhr.responseJSON.errors, function(field, messages){
                        $('#error-' + field.replace(/\.\d+$/, '')).text(messages[0]);
                    });
                } else {
                    show_toastr('Error', 'An unexpected error occurred.', 'error');
                }
                $('#editsubmituser').prop('disabled', false).text('Save Changes');
            }
        });
    });
});
</script>
@endpush

@section('content')
@include('company.layouts.partials.nav')

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
                <div class="stab active">
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
    <div class="role-form-header">
        <div>
            <h1 class="role-form-title">Edit Role</h1>
            <p class="role-form-sub">Update permissions and details for this role</p>
            <span class="role-name-badge">
                <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/></svg>
                {{ \Str::title($role->name) }}
            </span>
        </div>
        <a href="{{ route('organization.settings.role') }}" class="btn-back">
            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"/></svg>
            Back to Roles
        </a>
    </div>

    <form action="{{ route('organization.settings.role.update', $role->id) }}" method="POST">
        @csrf @method('PUT')

        {{-- NAME & DESCRIPTION --}}
        <div class="form-section">
            <div class="form-section-header">
                <div class="form-section-title">
                    <div class="form-section-icon" style="background:linear-gradient(135deg,#EFF6FF,#BFDBFE);">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/></svg>
                    </div>
                    <div>
                        <p class="form-section-label">Role Details</p>
                        <p class="form-section-sub">Update the name and description</p>
                    </div>
                </div>
            </div>
            <div class="form-section-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="mform-label">Role Name <span style="color:#DC2626">*</span></label>
                        <input type="text" name="name" class="mform-input" value="{{ old('name', $role->name) }}" placeholder="e.g. Team Manager" autocomplete="off">
                        <span id="error-name" class="text-danger small mt-1 d-block"></span>
                    </div>
                    <div class="col-md-6">
                        <label class="mform-label">Description <span style="color:#DC2626">*</span></label>
                        <textarea name="description" class="mform-textarea" style="min-height:46px; resize:none;" placeholder="Briefly describe this role's purpose…">{{ old('description', $role->description) }}</textarea>
                        <span id="error-description" class="text-danger small mt-1 d-block"></span>
                    </div>
                </div>
            </div>
        </div>

        {{-- FEATURE PERMISSIONS --}}
        <div class="form-section">
            <div class="form-section-header">
                <div class="form-section-title">
                    <div class="form-section-icon">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/></svg>
                    </div>
                    <div>
                        <p class="form-section-label">Feature Visibility</p>
                        <p class="form-section-sub">Choose which features this role can access</p>
                    </div>
                </div>
                <div class="select-actions">
                    <button class="sel-btn primary" id="selectAllFeatures">Select All</button>
                    <span class="sel-sep">|</span>
                    <button class="sel-btn gray" id="deselectAllFeatures">Deselect All</button>
                </div>
            </div>
            <div class="form-section-body">
                <div class="perm-grid">
                    @foreach ($features as $key => $label)
                        <label class="perm-item">
                            <input class="feature-checkbox" type="checkbox" name="features[]" value="{{ $key }}"
                                {{ in_array($key, old('features', $assignedPermissions)) ? 'checked' : '' }}>
                            <label style="pointer-events:none;">{{ $label }}</label>
                        </label>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- REPORT PERMISSIONS --}}
        <div class="form-section">
            <div class="form-section-header">
                <div class="form-section-title">
                    <div class="form-section-icon" style="background:var(--success-light);color:var(--success);">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>
                    </div>
                    <div>
                        <p class="form-section-label">Report Visibility</p>
                        <p class="form-section-sub">Control which reports are visible to this role</p>
                    </div>
                </div>
                <div class="select-actions">
                    <button class="sel-btn primary" id="selectAllReports">Select All</button>
                    <span class="sel-sep">|</span>
                    <button class="sel-btn gray" id="deselectAllReports">Deselect All</button>
                </div>
            </div>
            <div class="form-section-body">
                <div class="perm-grid">
                    @foreach ($reports as $key => $label)
                        <label class="perm-item">
                            <input class="report-checkbox" type="checkbox" name="reports[]" value="{{ $key }}"
                                {{ in_array($key, old('reports', $assignedPermissions)) ? 'checked' : '' }}>
                            <label style="pointer-events:none;">{{ $label }}</label>
                        </label>
                    @endforeach
                </div>
                <hr style="border:none;border-top:1px solid var(--gray-100);margin:18px 0;">
                <p style="font-size:12px;font-weight:700;color:var(--gray-500);text-transform:uppercase;letter-spacing:.5px;margin-bottom:12px;">All Teams Reports</p>
                <div class="perm-grid">
                    @foreach ($allReports as $key => $label)
                        <label class="perm-item">
                            <input class="all-report-checkbox" type="checkbox" name="allReports[]" value="{{ $key }}"
                                {{ in_array($key, old('allReports', $assignedPermissions)) ? 'checked' : '' }}>
                            <label style="pointer-events:none;">{{ $label }}</label>
                        </label>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- SETTINGS PERMISSIONS --}}
        <div class="form-section">
            <div class="form-section-header">
                <div class="form-section-title">
                    <div class="form-section-icon" style="background:#FFF7ED;color:#C2410C;">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="3"/><path d="M19.07 4.93a10 10 0 0 1 0 14.14"/><path d="M4.93 4.93a10 10 0 0 0 0 14.14"/></svg>
                    </div>
                    <div>
                        <p class="form-section-label">Settings Visibility</p>
                        <p class="form-section-sub">Choose settings sections this role can manage</p>
                    </div>
                </div>
                <div class="select-actions">
                    <button class="sel-btn primary" id="selectAllSettings">Select All</button>
                    <span class="sel-sep">|</span>
                    <button class="sel-btn gray" id="deselectAllSettings">Deselect All</button>
                </div>
            </div>
            <div class="form-section-body">
                <div class="perm-grid">
                    @foreach ($settings as $key => $label)
                        <label class="perm-item">
                            <input class="settings-checkbox" type="checkbox" name="settings[]" value="{{ $key }}"
                                {{ in_array($key, old('settings', $assignedPermissions)) ? 'checked' : '' }}>
                            <label style="pointer-events:none;">{{ $label }}</label>
                        </label>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- ACTIONS --}}
        <div class="form-actions">
            <a href="{{ route('organization.settings.role') }}" class="btn-cancel">Cancel</a>
            <button type="button" class="btn-submit" id="editsubmituser">
                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
                Save Changes
            </button>
        </div>

    </form>
</div>
@endsection