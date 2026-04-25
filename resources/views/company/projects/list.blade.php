@php
    $statusImages = [
        'in_progress' => asset('assets/assestsnew/project_status4.svg'),
        'on_hold'     => asset('assets/assestsnew/project_status3.svg'),
        'complete'    => asset('assets/assestsnew/project_status2.svg'),
        'canceled'    => asset('assets/assestsnew/project_status6.svg'),
    ];
    $projectStatuses = \App\Models\Project::$project_status;
    $projectCounts   = \App\Models\Project::select('status', \DB::raw('COUNT(*) as total'))
        ->where('created_by', Auth::user()->creatorId())
        ->groupBy('status')
        ->pluck('total', 'status')
        ->toArray();
@endphp

<style>
@import url('https://fonts.googleapis.com/css2?family=Syne:wght@500;600;700&family=DM+Sans:wght@300;400;500&display=swap');

:root {
    --accent:       #4f52ff;
    --accent-soft:  #eeeeff;
    --accent-glow:  rgba(79,82,255,.16);
    --danger:       #ff4f6a;
    --danger-soft:  #fff0f3;
    --success:      #00c48c;
    --success-soft: #e6faf5;
    --warn:         #ff9d0a;
    --warn-soft:    #fff8ec;
    --surface:      #ffffff;
    --surface-2:    #f7f8fc;
    --border:       #e8eaf2;
    --text-pri:     #12142a;
    --text-muted:   #7c7f9a;
    --row-hover:    #f4f5ff;
    --shadow:       0 2px 20px rgba(18,20,42,.07);
    --shadow-md:    0 4px 24px rgba(18,20,42,.10);
    --radius:       14px;
    --radius-sm:    10px;
    --radius-xs:    6px;
}

/* ══ STAT CARDS ══ */
.pj-stat-card { background:var(--surface); border:1px solid var(--border); border-radius:var(--radius); box-shadow:var(--shadow); padding:18px 20px; display:flex; align-items:center; justify-content:space-between; gap:14px; transition:box-shadow .15s,transform .15s; font-family:'DM Sans',sans-serif; height:100%; }
.pj-stat-card:hover { box-shadow:var(--shadow-md); transform:translateY(-2px); }
.pj-stat-left { display:flex; align-items:center; gap:12px; }
.pj-stat-icon { width:42px; height:42px; border-radius:var(--radius-xs); background:var(--surface-2); border:1px solid var(--border); display:flex; align-items:center; justify-content:center; flex-shrink:0; }
.pj-stat-icon img { width:22px; height:22px; }
.pj-stat-label { font-weight:700; letter-spacing:.09em; text-transform:uppercase; color:var(--text-muted); margin-bottom:3px; font-size:.72rem; }
.pj-stat-count { font-size:1.7rem; font-weight:700; color:var(--accent); line-height:1; font-family:'Syne',sans-serif; }
.pj-stat-card[data-status="in_progress"] .pj-stat-count { color:var(--accent); }
.pj-stat-card[data-status="on_hold"]     .pj-stat-count { color:var(--warn); }
.pj-stat-card[data-status="complete"]    .pj-stat-count { color:var(--success); }
.pj-stat-card[data-status="canceled"]    .pj-stat-count { color:var(--danger); }

/* ══ FILTER BAR — pill shape matching image ══ */
.pj-filter-bar {
    background: var(--surface);
    border: 1px solid var(--border);
    border-radius: 15px;
    box-shadow: var(--shadow);
    padding: 10px 20px;
    display: flex; align-items: center; gap: 12px; flex-wrap: wrap;
    margin-bottom: 16px;
    font-family: 'DM Sans', sans-serif;
}
.pj-search-wrap { display:flex; align-items:center; gap:8px; flex:1; min-width:180px; }
.pj-search-wrap i { color:var(--text-muted); font-size:.82rem; flex-shrink:0; }
.pj-search-input { border:none; background:transparent; outline:none; font-family:'DM Sans',sans-serif; font-size:.875rem; color:var(--text-pri); width:100%; }
.pj-search-input::placeholder { color:var(--text-muted); }
.pj-filter-divider { width:1px; height:22px; background:var(--border); flex-shrink:0; }

/* custom dropdown trigger */
.pj-dropdown-wrap { position:relative; }
.pj-dropdown-trigger {
    display:inline-flex; align-items:center; gap:8px;
    padding:6px 14px; border:1px solid var(--border); border-radius:50px;
    background:var(--surface-2); font-family:'DM Sans',sans-serif;
    font-size:.84rem; color:var(--text-pri); cursor:pointer; user-select:none;
    transition:border-color .13s,background .13s; min-width:130px; white-space:nowrap;
}
.pj-dropdown-trigger:hover { border-color:var(--accent); background:var(--accent-soft); color:var(--accent); }
.pj-dropdown-trigger.active-filter { border-color:var(--accent); background:var(--accent-soft); color:var(--accent); }
.pj-dropdown-trigger .pj-dd-label { flex:1; }
.pj-dropdown-trigger i.chevron { font-size:.7rem; opacity:.5; transition:transform .15s; }
.pj-dropdown-trigger.open i.chevron { transform:rotate(180deg); }

.pj-dropdown-panel {
    display:none; position:absolute; top:calc(100% + 6px); left:0; z-index:1000;
    background:var(--surface); border:1px solid var(--border); border-radius:var(--radius-sm);
    box-shadow:0 8px 28px rgba(18,20,42,.12); padding:6px; min-width:200px;
    font-family:'DM Sans',sans-serif;
}
.pj-dropdown-panel.open { display:block; animation:pj-dd-in .12s ease; }
@keyframes pj-dd-in { from{opacity:0;transform:translateY(-4px);} to{opacity:1;transform:translateY(0);} }

.pj-dd-header { font-family:'Syne',sans-serif; font-size:.6rem; font-weight:700; letter-spacing:.1em; text-transform:uppercase; color:var(--text-muted); padding:5px 10px 7px; }
.pj-dd-item { display:flex; align-items:center; gap:9px; padding:7px 10px; border-radius:var(--radius-xs); cursor:pointer; transition:background .1s; font-size:.84rem; color:var(--text-pri); user-select:none; }
.pj-dd-item:hover { background:var(--surface-2); }
.pj-dd-item.selected { background:transparent !important;border:1px solid var(--accent-soft); color:var(--accent); }
.pj-dd-checkbox { width:15px; height:15px; border-radius:4px; border:1.5px solid var(--border); background:var(--surface); display:flex; align-items:center; justify-content:center; flex-shrink:0; transition:background .12s,border-color .12s; }
.pj-dd-item.selected .pj-dd-checkbox { background:var(--accent); border-color:var(--accent); }
.pj-dd-check { display:none; color:#fff; font-size:.6rem; }
.pj-dd-item.selected .pj-dd-check { display:block; }
.pj-dd-status-dot { width:7px; height:7px; border-radius:50%; flex-shrink:0; }

.pj-reset-btn { display:inline-flex; align-items:center; gap:6px; padding:6px 14px; border-radius:50px; border:1px solid var(--border); background:var(--surface); font-size:.83rem; font-family:'DM Sans',sans-serif; color:var(--text-muted); cursor:pointer; transition:border-color .13s,color .13s; white-space:nowrap; margin-left:auto; }
.pj-reset-btn:hover { border-color:#c5c9df; color:var(--text-pri); }
.pj-reset-btn i { font-size:.78rem; }

/* ══ TABLE CARD ══ */
.pj-card { background:var(--surface); border:1px solid var(--border); border-radius:var(--radius); box-shadow:var(--shadow); overflow:hidden; font-family:'DM Sans',sans-serif; }

.pj-show-row { display:flex; align-items:center; gap:8px; padding:13px 22px; border-bottom:1px solid var(--border); font-size:.83rem; color:var(--text-muted); }
.pj-entries-select { appearance:none; background:var(--surface-2) url("data:image/svg+xml,%3Csvg width='10' height='6' viewBox='0 0 10 6' fill='none' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M1 1L5 5L9 1' stroke='%237c7f9a' stroke-width='1.5' stroke-linecap='round' stroke-linejoin='round'/%3E%3C/svg%3E") no-repeat right 8px center; border:1px solid var(--border); border-radius:var(--radius-xs); padding:4px 24px 4px 8px; font-size:.82rem; font-family:'DM Sans',sans-serif; color:var(--text-pri); cursor:pointer; outline:none; }
.pj-entries-select:focus { border-color:var(--accent); }

.pj-table { width:100%; border-collapse:collapse; font-size:.875rem; }
.pj-table thead th { padding:11px 16px; font-family:'Syne',sans-serif; font-size:.67rem; font-weight:700; letter-spacing:.09em; text-transform:uppercase; color:var(--text-muted); background:var(--surface-2); border-bottom:1px solid var(--border); white-space:nowrap; }
.pj-table thead th.col-num  { width:46px; text-align:center; }
.pj-table thead th.col-action { text-align:center; }
.pj-table tbody tr { border-bottom:1px solid var(--border); transition:background .13s; }
.pj-table tbody tr:last-child { border-bottom:none; }
.pj-table tbody tr:hover { background:var(--row-hover); }
.pj-table tbody td { padding:14px 16px; vertical-align:middle; color:var(--text-pri); }
.pj-table tbody td.col-num { text-align:center; color:var(--text-muted); font-size:.8rem; width:46px; }
.pj-table tbody td.col-action { text-align:center; }

.pj-name-cell { display:flex; align-items:center; gap:12px; }
.pj-thumb { width:38px; height:38px; border-radius:var(--radius-xs); object-fit:cover; border:1px solid var(--border); flex-shrink:0; background:var(--surface-2); }
.pj-name-link { font-size:.875rem; font-weight:600; color:var(--text-pri); text-decoration:none; transition:color .12s; display:block; max-width:200px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap; }
.pj-name-link:hover { color:var(--accent); }

.pj-status-badge { display:inline-flex; align-items:center; gap:5px; padding:3px 10px; border-radius:5px; font-size:.72rem; font-weight:600; white-space:nowrap; }
.badge.bg-primary,  .pj-status-in-progress { background:var(--accent-soft)  !important; color:var(--accent)    !important; }
.badge.bg-warning,  .pj-status-on-hold     { background:var(--warn-soft)    !important; color:var(--warn)      !important; }
.badge.bg-success,  .pj-status-complete    { background:var(--success-soft) !important; color:var(--success)   !important; }
.badge.bg-danger,   .pj-status-canceled    { background:var(--danger-soft)  !important; color:var(--danger)    !important; }
.badge.bg-secondary,.pj-status-not-started { background:var(--surface-2)    !important; color:var(--text-muted)!important; }
.pj-status-in_progress::before { background:var(--accent); }
.pj-status-on_hold::before     { background:var(--warn); }
.pj-status-complete::before    { background:var(--success); }
.pj-status-canceled::before    { background:var(--danger); }
.pj-status-not_started::before { background:var(--text-muted); }

.pj-avatar-group { display:flex; align-items:center; }
.pj-avatar { width:30px; height:30px; border-radius:50%; object-fit:cover; border:2px solid var(--surface); margin-left:-6px; flex-shrink:0; box-shadow:0 1px 4px rgba(18,20,42,.12); }
.pj-avatar:first-child { margin-left:0; }
.pj-avatar-more { width:30px; height:30px; border-radius:50%; background:var(--surface-2); border:2px solid var(--surface); margin-left:-6px; display:flex; align-items:center; justify-content:center; font-size:.68rem; font-weight:700; color:var(--text-muted); box-shadow:0 1px 4px rgba(18,20,42,.1); }

.pj-progress-wrap { min-width:120px; }
.pj-progress-top { display:flex; align-items:center; justify-content:space-between; margin-bottom:5px; }
.pj-progress-pct { font-size:.78rem; font-weight:700; color:var(--success); }
.pj-progress-bar-track { width:100%; height:7px; background:var(--surface-2); border-radius:50px; overflow:hidden; border:1px solid var(--border); }
.pj-progress-bar-fill { height:100%; border-radius:50px; background:linear-gradient(90deg,var(--accent) 0%,var(--success) 100%); transition:width .4s ease; }

.pj-action-wrap { display:flex; align-items:center; justify-content:center; gap:5px; }
.pj-act-btn { width:30px; height:30px; border-radius:var(--radius-xs); display:inline-flex; align-items:center; justify-content:center; border:1px solid var(--border); background:var(--surface); color:var(--text-muted); transition:all .13s; cursor:pointer; text-decoration:none; }
.pj-act-btn i { font-size:.78rem; }
.pj-act-btn.invite:hover { background:var(--accent-soft); border-color:rgba(79,82,255,.22); color:var(--accent); box-shadow:0 0 0 3px var(--accent-glow); }
.pj-act-btn.edit:hover   { background:var(--warn-soft);   border-color:rgba(255,157,10,.2);  color:var(--warn);   box-shadow:0 0 0 3px rgba(255,157,10,.12); }
.pj-act-btn.del:hover    { background:var(--danger-soft); border-color:rgba(255,79,106,.18); color:var(--danger); box-shadow:0 0 0 3px rgba(255,79,106,.1); }

.pj-empty { padding:60px 20px; text-align:center; color:var(--text-muted); }
.pj-empty i { font-size:2.2rem; opacity:.22; display:block; margin-bottom:10px; }
.pj-empty p { font-size:.88rem; margin:0; }

/* ══ BOTTOM BAR ══ */
.pj-bottom-bar { display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:10px; padding:14px 22px; border-top:1px solid var(--border); background:var(--surface); border-radius:0 0 var(--radius) var(--radius); }
.pj-showing { font-size:.82rem; color:var(--text-muted); white-space:nowrap; }
.pj-showing strong { color:var(--text-pri); }
.pj-pag { display:flex; align-items:center; gap:3px; list-style:none; margin:0; padding:0; }
.pj-pag li a,.pj-pag li span { display:inline-flex; align-items:center; justify-content:center; min-width:34px; height:34px; padding:0 6px; border-radius:var(--radius-xs); border:1px solid var(--border); background:var(--surface); font-size:.82rem; color:var(--text-muted); text-decoration:none; transition:all .13s; cursor:pointer; }
.pj-pag li a:hover { background:var(--accent-soft); border-color:rgba(79,82,255,.22); color:var(--accent); }
.pj-pag li.active_pagination a { background:var(--accent); border-color:var(--accent); color:#fff; font-weight:600; box-shadow:0 2px 8px var(--accent-glow); }
.pj-pag li.disabled a { opacity:.35; cursor:not-allowed; pointer-events:none; }
.pj-right-controls { display:flex; align-items:center; gap:10px; font-size:.82rem; color:var(--text-muted); white-space:nowrap; }
.pj-rows-select { appearance:none; background:var(--surface-2) url("data:image/svg+xml,%3Csvg width='10' height='6' viewBox='0 0 10 6' fill='none' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M1 1L5 5L9 1' stroke='%237c7f9a' stroke-width='1.5' stroke-linecap='round' stroke-linejoin='round'/%3E%3C/svg%3E") no-repeat right 8px center; border:1px solid var(--border); border-radius:var(--radius-xs); padding:5px 24px 5px 8px; font-size:.82rem; font-family:'DM Sans',sans-serif; color:var(--text-pri); cursor:pointer; outline:none; }
.pj-rows-select:focus { border-color:var(--accent); }
.pj-goto-input { width:52px; text-align:center; border:1px solid var(--border); border-radius:var(--radius-xs); padding:5px 6px; font-size:.82rem; font-family:'DM Sans',sans-serif; color:var(--text-pri); background:var(--surface-2); outline:none; transition:border-color .13s; }
.pj-goto-input:focus { border-color:var(--accent); }
.pj-go-btn { padding:5px 16px; border-radius:var(--radius-xs); background:var(--accent); color:#fff; border:none; font-size:.82rem; font-family:'DM Sans',sans-serif; font-weight:600; cursor:pointer; transition:opacity .13s; box-shadow:0 2px 8px var(--accent-glow); }
.pj-go-btn:hover { opacity:.88; }

@media(max-width:768px){
    .pj-filter-bar { border-radius:var(--radius); }
    .pj-bottom-bar { flex-direction:column; align-items:flex-start; }
    .pj-pag { flex-wrap:wrap; }
}
</style>

{{-- ══ STAT CARDS ══ --}}
<div class="row g-3 mb-4">
    @foreach($projectStatuses as $statusKey => $statusLabel)
        @php $count = $projectCounts[$statusKey] ?? 0; @endphp
        <div class="col-12 col-sm-6 col-md-3">
            <div class="pj-stat-card" data-status="{{ $statusKey }}">
                <div class="pj-stat-left">
                    <div class="pj-stat-icon">
                        <img src="{{ $statusImages[$statusKey] ?? asset('assets/assestsnew/default_status.svg') }}"
                             alt="{{ $statusLabel }}">
                    </div>
                    <div><div class="pj-stat-label">{{ $statusLabel }}</div></div>
                </div>
                <div class="pj-stat-count">{{ $count }}</div>
            </div>
        </div>
    @endforeach
</div>

{{-- ══ FILTER BAR ══ --}}
<div class="pj-filter-bar">

    {{-- Search --}}
    <div class="pj-search-wrap">
        <i class="ti ti-search"></i>
        <input type="text" id="pj-search" class="pj-search-input"
               placeholder="Search by project name…" autocomplete="off">
    </div>

    <div class="pj-filter-divider"></div>
    
    {{-- Project multi-select --}}
    <div class="pj-dropdown-wrap">
        <div class="pj-dropdown-trigger" id="pj-project-trigger"
             onclick="pjToggleDropdown('pj-project-panel','pj-project-trigger')">
            <i class="ti ti-folder" style="font-size:.78rem;"></i>
            <span class="pj-dd-label" id="pj-project-label">All Projects</span>
            <i class="ti ti-chevron-down chevron"></i>
        </div>
        <div class="pj-dropdown-panel" id="pj-project-panel" style="min-width:230px; max-height:260px; overflow-y:auto;">
            <div class="pj-dd-header">Filter by Project</div>
            {{-- Search inside dropdown --}}
            <div style="padding:0 6px 6px;">
                <div style="display:flex;align-items:center;gap:6px;background:var(--surface-2);border:1px solid var(--border);border-radius:var(--radius-xs);padding:5px 10px;">
                    <i class="ti ti-search" style="font-size:.72rem;color:var(--text-muted);flex-shrink:0;"></i>
                    <input type="text" id="pj-project-search"
                           placeholder="Search projects…"
                           autocomplete="off"
                           style="border:none;background:transparent;outline:none;font-family:'DM Sans',sans-serif;font-size:.82rem;color:var(--text-pri);width:100%;"
                           oninput="pjFilterProjectDropdown(this.value)">
                </div>
            </div>
            <div class="pj-dd-item selected" data-val="" id="pj-project-all" onclick="pjSelectProject(this)">
                <div class="pj-dd-checkbox"><i class="ti ti-check pj-dd-check"></i></div>
                All Projects
            </div>
            <div id="pj-project-items">
                @if(isset($projects) && count($projects) > 0)
                    @foreach($projects as $project)
                        <div class="pj-dd-item pj-project-option"
                             data-val="{{ $project->id }}"
                             data-label="{{ $project->project_name }}"
                             onclick="pjSelectProject(this)">
                            <div class="pj-dd-checkbox"><i class="ti ti-check pj-dd-check"></i></div>
                            <span style="overflow:hidden;text-overflow:ellipsis;white-space:nowrap;max-width:160px;display:block;">
                                {{ $project->project_name }}
                            </span>
                        </div>
                    @endforeach
                @endif
            </div>
        </div>
    </div>

    {{-- Status multi-select --}}
    <div class="pj-dropdown-wrap">
        <div class="pj-dropdown-trigger" id="pj-status-trigger"
             onclick="pjToggleDropdown('pj-status-panel','pj-status-trigger')">
            <i class="ti ti-filter" style="font-size:.78rem;"></i>
            <span class="pj-dd-label" id="pj-status-label">All Status</span>
            <i class="ti ti-chevron-down chevron"></i>
        </div>
        <div class="pj-dropdown-panel" id="pj-status-panel">
            <div class="pj-dd-header">Filter by Status</div>
            <div class="pj-dd-item selected" data-val="" onclick="pjSelectStatus(this)">
                <div class="pj-dd-checkbox"><i class="ti ti-check pj-dd-check"></i></div>
                <span class="pj-dd-status-dot" style="background:var(--text-muted);"></span>
                Show All
            </div>
            @foreach($projectStatuses as $sKey => $sLabel)
                @php
                    $dotColor = match($sKey) {
                        'in_progress' => 'var(--accent)',
                        'on_hold'     => 'var(--warn)',
                        'complete'    => 'var(--success)',
                        'canceled'    => 'var(--danger)',
                        default       => 'var(--text-muted)',
                    };
                @endphp
                <div class="pj-dd-item" data-val="{{ $sKey }}" onclick="pjSelectStatus(this)">
                    <div class="pj-dd-checkbox"><i class="ti ti-check pj-dd-check"></i></div>
                    <span class="pj-dd-status-dot" style="background:{{ $dotColor }};"></span>
                    {{ __($sLabel) }}
                </div>
            @endforeach
        </div>
    </div>

    {{-- Completion filter --}}
    <div class="pj-dropdown-wrap">
        <div class="pj-dropdown-trigger" id="pj-progress-trigger"
             onclick="pjToggleDropdown('pj-progress-panel','pj-progress-trigger')">
            <i class="ti ti-chart-bar" style="font-size:.78rem;"></i>
            <span class="pj-dd-label" id="pj-progress-label">Completion</span>
            <i class="ti ti-chevron-down chevron"></i>
        </div>
        <div class="pj-dropdown-panel" id="pj-progress-panel">
            <div class="pj-dd-header">Completion Range</div>
            <div class="pj-dd-item selected" data-val="" onclick="pjSelectProgress(this)">
                <div class="pj-dd-checkbox"><i class="ti ti-check pj-dd-check"></i></div>
                All
            </div>
            <div class="pj-dd-item" data-val="0-25"   onclick="pjSelectProgress(this)"><div class="pj-dd-checkbox"><i class="ti ti-check pj-dd-check"></i></div>0% – 25%</div>
            <div class="pj-dd-item" data-val="26-50"  onclick="pjSelectProgress(this)"><div class="pj-dd-checkbox"><i class="ti ti-check pj-dd-check"></i></div>26% – 50%</div>
            <div class="pj-dd-item" data-val="51-75"  onclick="pjSelectProgress(this)"><div class="pj-dd-checkbox"><i class="ti ti-check pj-dd-check"></i></div>51% – 75%</div>
            <div class="pj-dd-item" data-val="76-100" onclick="pjSelectProgress(this)"><div class="pj-dd-checkbox"><i class="ti ti-check pj-dd-check"></i></div>76% – 100%</div>
        </div>
    </div>

    {{-- Reset --}}
    <button class="pj-reset-btn" id="pj-reset-btn" type="button">
        <i class="ti ti-refresh"></i> Reset
    </button>
</div>

{{-- ══ TABLE CARD ══ --}}
<div class="pj-card mb-3">

    <div class="pj-show-row">
        <span>Show</span>
        <select class="pj-entries-select" id="pj-per-page">
            <option value="5">5</option>
            <option value="10">10</option>
            <option value="20">20</option>
            <option value="50">50</option>
        </select>
        <span>entries</span>
    </div>

    <div class="table-responsive">
        <table class="pj-table attendance-table">
            <thead>
                <tr>
                    <th class="col-num">#</th>
                    <th>{{__('Project')}}</th>
                    <th>{{__('Status')}}</th>
                    <th>{{__('Team')}}</th>
                    <th>{{__('Completion')}}</th>
                    <th class="col-action">{{__('Actions')}}</th>
                </tr>
            </thead>
            <tbody id="pj-tbody">
            @if(isset($projects) && !empty($projects) && count($projects) > 0)
                @foreach ($projects as $key => $project)
                    @php
                        $image = $project->project_image
                            ? \App\Models\Utility::get_file($project->project_image)
                            : asset('assets/assestsnew/Manage_projects.svg');
                        $statusKey   = $project->status;
                        $statusLabel = \App\Models\Project::$project_status[$statusKey] ?? $statusKey;
                        $statusSlug  = str_replace('_', '-', $statusKey);
                    @endphp
                    <tr
                        data-name="{{ strtolower($project->project_name) }}"
                        data-status="{{ $statusKey }}"
                        data-progress="{{ $project->progress }}"
                        data-id="{{ $project->id }}"
                    >
                        <td class="col-num pj-row-num"></td>

                        <td class="tex_fix">
                            <div class="pj-name-cell">
                                <img src="{{ $image }}" class="pj-thumb"
                                     alt="{{ $project->project_name }}" width="38" height="38">
                                <a href="{{ route('organization.projects.show', $project) }}"
                                   class="pj-name-link" title="{{ $project->project_name }}">
                                    {{ $project->project_name }}
                                </a>
                            </div>
                        </td>

                        <td>
                            <span class="pj-status-badge pj-status-{{ $statusSlug }}">
                                {{ __($statusLabel) }}
                            </span>
                        </td>

                        <td>
                            <div class="pj-avatar-group" id="project_{{ $project->id }}">
                                @if(isset($project->users) && $project->users->count() > 0)
                                    @foreach($project->users->take(3) as $user)
                                        @php
                                            $gender = $user->employee->gender ?? null;
                                            $avatarSrc = match(true) {
                                                $gender === GENDER_MALE   => asset('assets/assestsnew/menimg.png'),
                                                $gender === GENDER_FEMALE => asset('assets/assestsnew/femaile-report.svg'),
                                                default => $user->avatar
                                                    ? asset('/storage/uploads/avatar/' . $user->avatar)
                                                    : asset('assets/assestsnew/profile.png'),
                                            };
                                        @endphp
                                        <img src="{{ $avatarSrc }}" class="pj-avatar"
                                             width="30" height="30"
                                             title="{{ $user->name }}" alt="{{ $user->name }}">
                                    @endforeach
                                    @if($project->users->count() > 3)
                                        <div class="pj-avatar-more">+{{ $project->users->count() - 3 }}</div>
                                    @endif
                                @else
                                    <span style="color:var(--text-muted);font-size:.82rem;">—</span>
                                @endif
                            </div>
                        </td>

                        <td>
                            <div class="pj-progress-wrap">
                                <div class="pj-progress-top">
                                    <span class="pj-progress-pct">{{ $project->progress }}%</span>
                                </div>
                                <div class="pj-progress-bar-track">
                                    <div class="pj-progress-bar-fill" style="width:{{ $project->progress }}%;"></div>
                                </div>
                            </div>
                        </td>

                        <td class="col-action Action">
                            <div class="pj-action-wrap">
                                <a href="#" class="pj-act-btn invite copy_com"
                                   data-url="{{ route('organization.invite.project.member.view', $project->id) }}"
                                   data-ajax-popup="true" data-size="lg"
                                   data-bs-toggle="tooltip" title="{{__('Invite User')}}"
                                   data-title="{{__('Invite User')}}">
                                    <i class="ti ti-send"></i>
                                </a>
                                <a href="#" class="pj-act-btn edit copy_com"
                                   data-url="{{ URL::to('projects/'.$project->id.'/edit') }}"
                                   data-ajax-popup="true" data-size="lg"
                                   data-bs-toggle="tooltip" title="{{__('Edit')}}"
                                   data-title="{{__('Edit Project')}}">
                                    <i class="fas fa-edit"></i>
                                </a>
                                {!! Form::open(['method' => 'DELETE', 'route' => ['organization.projects.user.destroy', [$project->id, $user->id]], 'style' => 'display:inline']) !!}
                                <a href="#" class="pj-act-btn del bs-pass-para copy_com"
                                   data-bs-toggle="tooltip" title="{{__('Delete')}}">
                                    <i class="fa-solid fa-trash"></i>
                                </a>
                                {!! Form::close() !!}
                            </div>
                        </td>
                    </tr>
                @endforeach
            @else
                <tr id="pj-server-empty">
                    <td colspan="6">
                        <div class="pj-empty">
                            <i class="ti ti-folder-off"></i>
                            <p>{{__('No Projects Found.')}}</p>
                        </div>
                    </td>
                </tr>
            @endif
            </tbody>
        </table>
    </div>

    {{-- ══ BOTTOM BAR ══ --}}
    <div class="pj-bottom-bar">
        <span class="pj-showing">
            Showing <strong id="pj-show-from">–</strong>–<strong id="pj-show-to">–</strong>
            of <strong id="pj-show-total">–</strong> entries
        </span>
        <ul class="pj-pag" id="pj-pag-list"></ul>
        <div class="pj-right-controls">
            <span>Rows:</span>
            <select class="pj-rows-select" id="pj-per-page-2">
                <option value="5">5</option>
                <option value="10">10</option>
                <option value="20">20</option>
                <option value="50">50</option>
            </select>
            <span>Go to</span>
            <input type="number" min="1" class="pj-goto-input" id="pj-goto-input" placeholder="—">
            <button class="pj-go-btn" id="pj-go-btn" type="button">Go</button>
        </div>
    </div>
</div>

<script>
/* ════════════════════════════════
   DROPDOWN TOGGLE
════════════════════════════════ */
function pjToggleDropdown(panelId, triggerId) {
    var panel   = document.getElementById(panelId);
    var trigger = document.getElementById(triggerId);
    var isOpen  = panel.classList.contains('open');
    document.querySelectorAll('.pj-dropdown-panel.open').forEach(function(p){ p.classList.remove('open'); });
    document.querySelectorAll('.pj-dropdown-trigger.open').forEach(function(t){ t.classList.remove('open'); });
    if (!isOpen) { panel.classList.add('open'); trigger.classList.add('open'); }
}
document.addEventListener('click', function(e) {
    if (!e.target.closest('.pj-dropdown-wrap')) {
        document.querySelectorAll('.pj-dropdown-panel.open').forEach(function(p){ p.classList.remove('open'); });
        document.querySelectorAll('.pj-dropdown-trigger.open').forEach(function(t){ t.classList.remove('open'); });
    }
});

/* ════════════════════════════════
   STATUS MULTI-SELECT
════════════════════════════════ */
var pjSelectedStatuses = [];

function pjSelectStatus(el) {
    var val = el.dataset.val;
    if (val === '') {
        document.querySelectorAll('#pj-status-panel .pj-dd-item').forEach(function(i){ i.classList.remove('selected'); });
        el.classList.add('selected');
        pjSelectedStatuses = [];
        document.getElementById('pj-status-label').textContent = 'All Status';
        document.getElementById('pj-status-trigger').classList.remove('active-filter');
    } else {
        document.querySelector('#pj-status-panel .pj-dd-item[data-val=""]').classList.remove('selected');
        if (el.classList.contains('selected')) {
            el.classList.remove('selected');
            pjSelectedStatuses = pjSelectedStatuses.filter(function(s){ return s !== val; });
        } else {
            el.classList.add('selected');
            pjSelectedStatuses.push(val);
        }
        if (pjSelectedStatuses.length === 0) {
            document.querySelector('#pj-status-panel .pj-dd-item[data-val=""]').classList.add('selected');
            document.getElementById('pj-status-label').textContent = 'All Status';
            document.getElementById('pj-status-trigger').classList.remove('active-filter');
        } else {
            document.getElementById('pj-status-label').textContent = pjSelectedStatuses.length + ' selected';
            document.getElementById('pj-status-trigger').classList.add('active-filter');
        }
    }
    pjCurrentPage = 1; pjRender();
}

/* ════════════════════════════════
   PROGRESS SINGLE-SELECT
════════════════════════════════ */
var pjProgressFilter = '';

function pjSelectProgress(el) {
    document.querySelectorAll('#pj-progress-panel .pj-dd-item').forEach(function(i){ i.classList.remove('selected'); });
    el.classList.add('selected');
    pjProgressFilter = el.dataset.val;
    if (!pjProgressFilter) {
        document.getElementById('pj-progress-label').textContent = 'Completion';
        document.getElementById('pj-progress-trigger').classList.remove('active-filter');
    } else {
        document.getElementById('pj-progress-label').textContent = el.textContent.trim();
        document.getElementById('pj-progress-trigger').classList.add('active-filter');
    }
    pjCurrentPage = 1; pjRender();
}

/* ════════════════════════════════
   PROJECT MULTI-SELECT
════════════════════════════════ */
var pjSelectedProjects = [];   /* array of project id strings */

function pjSelectProject(el) {
    var val = el.dataset.val;
    if (val === '') {
        /* "All Projects" — clear everything */
        document.querySelectorAll('#pj-project-panel .pj-dd-item').forEach(function(i){ i.classList.remove('selected'); });
        el.classList.add('selected');
        pjSelectedProjects = [];
        document.getElementById('pj-project-label').textContent = 'All Projects';
        document.getElementById('pj-project-trigger').classList.remove('active-filter');
    } else {
        document.getElementById('pj-project-all').classList.remove('selected');
        if (el.classList.contains('selected')) {
            el.classList.remove('selected');
            pjSelectedProjects = pjSelectedProjects.filter(function(id){ return id !== val; });
        } else {
            el.classList.add('selected');
            pjSelectedProjects.push(val);
        }
        if (pjSelectedProjects.length === 0) {
            document.getElementById('pj-project-all').classList.add('selected');
            document.getElementById('pj-project-label').textContent = 'All Projects';
            document.getElementById('pj-project-trigger').classList.remove('active-filter');
        } else {
            document.getElementById('pj-project-label').textContent = pjSelectedProjects.length + ' selected';
            document.getElementById('pj-project-trigger').classList.add('active-filter');
        }
    }
    pjCurrentPage = 1; pjRender();
}

function pjFilterProjectDropdown(q) {
    var term = q.toLowerCase().trim();
    document.querySelectorAll('#pj-project-items .pj-project-option').forEach(function(item) {
        var label = (item.dataset.label || '').toLowerCase();
        item.style.display = (!term || label.includes(term)) ? '' : 'none';
    });
}


var pjCurrentPage = 1;

(function () {
    var tbody   = document.getElementById('pj-tbody');
    if (!tbody) return;
    var allRows = Array.from(tbody.querySelectorAll('tr[data-name]'));
    if (!allRows.length) return;

    var perPage      = 5;
    var filteredRows = allRows.slice();

    var searchEl  = document.getElementById('pj-search');
    var resetBtn  = document.getElementById('pj-reset-btn');
    var perPage1  = document.getElementById('pj-per-page');
    var perPage2  = document.getElementById('pj-per-page-2');
    var pagList   = document.getElementById('pj-pag-list');
    var showFrom  = document.getElementById('pj-show-from');
    var showTo    = document.getElementById('pj-show-to');
    var showTotal = document.getElementById('pj-show-total');
    var gotoInput = document.getElementById('pj-goto-input');
    var goBtn     = document.getElementById('pj-go-btn');

    /* expose globally so dropdown callbacks can call render */
    window.pjRender = render;

    function applyFilters() {
        var q = searchEl.value.toLowerCase().trim();
        filteredRows = allRows.filter(function(row) {
            var name     = row.dataset.name     || '';
            var status   = row.dataset.status   || '';
            var progress = parseInt(row.dataset.progress || '0', 10);
            var rowId    = row.dataset.id || '';
            var matchQ   = !q || name.includes(q);
            var matchSt  = pjSelectedStatuses.length === 0 || pjSelectedStatuses.indexOf(status) !== -1;
            var matchPj  = pjSelectedProjects.length === 0 || pjSelectedProjects.indexOf(rowId) !== -1;
            var matchPr  = true;
            if (pjProgressFilter) {
                var parts = pjProgressFilter.split('-');
                matchPr = progress >= parseInt(parts[0]) && progress <= parseInt(parts[1]);
            }
            return matchQ && matchSt && matchPj && matchPr;
        });
        pjCurrentPage = 1;
        render();
    }

    function render() {
        allRows.forEach(function(r){ r.style.display = 'none'; });
        var total    = filteredRows.length;
        var totalPgs = Math.max(1, Math.ceil(total / perPage));
        if (pjCurrentPage > totalPgs) pjCurrentPage = totalPgs;
        var start = (pjCurrentPage - 1) * perPage;
        var end   = Math.min(start + perPage, total);
        filteredRows.slice(start, end).forEach(function(row, i) {
            row.style.display = '';
            var nc = row.querySelector('.pj-row-num');
            if (nc) nc.textContent = start + i + 1;
        });
        showFrom.textContent  = total ? start + 1 : 0;
        showTo.textContent    = end;
        showTotal.textContent = total;
        renderPagination(totalPgs);
        var noRes = tbody.querySelector('.pj-js-empty');
        if (!total) {
            if (!noRes) {
                noRes = document.createElement('tr');
                noRes.className = 'pj-js-empty';
                noRes.innerHTML = '<td colspan="6"><div class="pj-empty"><i class="ti ti-search-off"></i><p>No projects match your filters.</p></div></td>';
                tbody.appendChild(noRes);
            }
            noRes.style.display = '';
        } else if (noRes) {
            noRes.style.display = 'none';
        }
    }

    function renderPagination(totalPgs) {
        pagList.innerHTML = '';
        function mkLi(html, page, disabled, active) {
            var li = document.createElement('li');
            if (disabled) li.classList.add('disabled');
            if (active)   li.classList.add('active_pagination');
            var a = document.createElement('a');
            a.href = '#'; a.innerHTML = html;
            if (!disabled) {
                a.addEventListener('click', function(e){ e.preventDefault(); pjCurrentPage = page; render(); });
            }
            li.appendChild(a); return li;
        }
        pagList.appendChild(mkLi('&#171;', 1, pjCurrentPage === 1));
        pagList.appendChild(mkLi('<i class="fa-solid fa-chevron-left" style="font-size:.6rem;"></i>', pjCurrentPage - 1, pjCurrentPage === 1));
        var s = Math.max(1, pjCurrentPage - 2);
        var e = Math.min(totalPgs, s + 4);
        if (e - s < 4) s = Math.max(1, e - 4);
        for (var p = s; p <= e; p++) pagList.appendChild(mkLi(p, p, false, p === pjCurrentPage));
        pagList.appendChild(mkLi('<i class="fa-solid fa-chevron-right" style="font-size:.6rem;"></i>', pjCurrentPage + 1, pjCurrentPage === totalPgs));
        pagList.appendChild(mkLi('&#187;', totalPgs, pjCurrentPage === totalPgs));
    }

    function syncPerPage(val) {
        perPage = parseInt(val);
        perPage1.value = val; perPage2.value = val;
        pjCurrentPage = 1; render();
    }

    function resetAll() {
        searchEl.value = '';
        pjSelectedStatuses = []; pjProgressFilter = ''; pjSelectedProjects = [];
        document.querySelectorAll('#pj-status-panel .pj-dd-item').forEach(function(i){ i.classList.remove('selected'); });
        document.querySelector('#pj-status-panel .pj-dd-item[data-val=""]').classList.add('selected');
        document.getElementById('pj-status-label').textContent = 'All Status';
        document.getElementById('pj-status-trigger').classList.remove('active-filter');
        document.querySelectorAll('#pj-progress-panel .pj-dd-item').forEach(function(i){ i.classList.remove('selected'); });
        document.querySelector('#pj-progress-panel .pj-dd-item[data-val=""]').classList.add('selected');
        document.getElementById('pj-progress-label').textContent = 'Completion';
        document.getElementById('pj-progress-trigger').classList.remove('active-filter');
        /* reset project dropdown */
        document.querySelectorAll('#pj-project-panel .pj-dd-item').forEach(function(i){ i.classList.remove('selected'); });
        document.getElementById('pj-project-all').classList.add('selected');
        document.getElementById('pj-project-label').textContent = 'All Projects';
        document.getElementById('pj-project-trigger').classList.remove('active-filter');
        var pjPS = document.getElementById('pj-project-search');
        if (pjPS) { pjPS.value = ''; pjFilterProjectDropdown(''); }
        applyFilters();
    }

    searchEl.addEventListener('input', applyFilters);
    resetBtn.addEventListener('click', resetAll);
    perPage1.addEventListener('change', function(e){ syncPerPage(e.target.value); });
    perPage2.addEventListener('change', function(e){ syncPerPage(e.target.value); });
    goBtn.addEventListener('click', function(){
        var totPgs = Math.max(1, Math.ceil(filteredRows.length / perPage));
        var pg = parseInt(gotoInput.value);
        if (!isNaN(pg)){ pjCurrentPage = Math.min(Math.max(1, pg), totPgs); render(); }
        gotoInput.value = '';
    });
    gotoInput.addEventListener('keydown', function(e){ if (e.key === 'Enter') goBtn.click(); });

    render();
})();
</script>