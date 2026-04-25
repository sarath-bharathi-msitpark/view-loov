@if(isset($projects) && !empty($projects) && count($projects) > 0)
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
    $allProjects = \App\Models\Project::where('created_by', Auth::user()->creatorId())
        ->orderBy('project_name')
        ->get(['id','project_name']);
@endphp

<style>
/* ============================================================
   DESIGN TOKENS — keep in sync with your existing :root block
   (only card-specific tokens are added/overridden here)
   ============================================================ */
:root {
    --p-accent:        #4f52ff;
    --p-accent-2:      #7c5cfc;
    --p-accent-soft:   #f0f0ff;
    --p-accent-glow:   rgba(79,82,255,.18);
    --p-danger:        #f43f5e;
    --p-danger-soft:   #fff1f3;
    --p-success:       #10b981;
    --p-success-soft:  #ecfdf5;
    --p-warn:          #f59e0b;
    --p-warn-soft:     #fffbeb;
    --p-surface:       #ffffff;
    --p-surface-2:     #f8f9fc;
    --p-surface-3:     #f1f3f9;
    --p-border:        #e4e7f0;
    --p-border-2:      #d0d5e8;
    --p-text:          #0f1229;
    --p-text-2:        #3d4166;
    --p-muted:         #8890b0;
    --p-shadow-sm:     0 1px 3px rgba(15,18,41,.06), 0 1px 2px rgba(15,18,41,.04);
    --p-shadow:        0 4px 16px rgba(15,18,41,.08), 0 1px 4px rgba(15,18,41,.04);
    --p-shadow-md:     0 8px 32px rgba(15,18,41,.12), 0 2px 8px rgba(15,18,41,.06);
    --p-shadow-lg:     0 16px 48px rgba(15,18,41,.14);
    --p-radius:        16px;
    --p-radius-sm:     10px;
    --p-radius-xs:     7px;
}

/* ════════════════════
   STAT CARDS  (unchanged)
════════════════════ */
.p-stats-row { margin-bottom: 24px; }
.p-stat-card {
    background: var(--p-surface);
    border: 1px solid var(--p-border);
    border-radius: var(--p-radius);
    box-shadow: var(--p-shadow-sm);
    padding: 20px;
    display: flex; align-items: center;
    justify-content: space-between;
    gap: 14px; height: 100%;
    transition: box-shadow .2s, transform .2s;
    position: relative; overflow: hidden;
}
.p-stat-card::before {
    content: '';
    position: absolute; top: 0; left: 0; right: 0; height: 3px;
    border-radius: var(--p-radius) var(--p-radius) 0 0;
}
.p-stat-card:hover { box-shadow: var(--p-shadow-md); transform: translateY(-3px); }
.p-stat-card[data-status="in_progress"]::before { background: linear-gradient(90deg,var(--p-accent),var(--p-accent-2)); }
.p-stat-card[data-status="on_hold"]::before     { background: linear-gradient(90deg,var(--p-warn),#fb923c); }
.p-stat-card[data-status="complete"]::before    { background: linear-gradient(90deg,var(--p-success),#34d399); }
.p-stat-card[data-status="canceled"]::before    { background: linear-gradient(90deg,var(--p-danger),#fb7185); }
.p-stat-left { display: flex; align-items: center; gap: 12px; }
.p-stat-icon {
    width: 44px; height: 44px; border-radius: var(--p-radius-xs);
    background: var(--p-surface-3);
    border: 1px solid var(--p-border);
    display: flex; align-items: center; justify-content: center; flex-shrink: 0;
}
.p-stat-icon img { width: 22px; height: 22px; }
.p-stat-label { font-size: .65rem; font-weight: 700; letter-spacing: .1em; text-transform: uppercase; color: var(--p-muted); margin-bottom: 2px; }
.p-stat-sub { font-size: .75rem; color: var(--p-muted); }
.p-stat-count { font-size: 2rem; font-weight: 800; line-height: 1; letter-spacing: -.03em; }
.p-stat-card[data-status="in_progress"] .p-stat-count { color: var(--p-accent); }
.p-stat-card[data-status="on_hold"]     .p-stat-count { color: var(--p-warn); }
.p-stat-card[data-status="complete"]    .p-stat-count { color: var(--p-success); }
.p-stat-card[data-status="canceled"]    .p-stat-count { color: var(--p-danger); }

/* ════════════════════
   FILTER BAR  (unchanged — all filter CSS kept as-is)
════════════════════ */
.p-filter-card {
    background: var(--p-surface); border: 1px solid var(--p-border);
    border-radius: var(--p-radius-sm); box-shadow: var(--p-shadow-sm);
    padding: 10px 14px; display: flex; align-items: center;
    gap: 8px; flex-wrap: wrap; margin-bottom: 20px;
}
.p-search-wrap {
    display: flex; align-items: center; gap: 6px;
    flex: 1; min-width: 200px; background: var(--p-surface-2);
    border: 1.5px solid var(--p-border); border-radius: var(--p-radius-xs);
    padding: 6px 8px 6px 10px; transition: border-color .15s, box-shadow .15s;
}
.p-search-wrap:focus-within { border-color: var(--p-accent); box-shadow: 0 0 0 3px var(--p-accent-glow); background: var(--p-surface); }
.p-search-wrap i { font-size: .8rem; flex-shrink: 0; }
.p-search-input { border: none; background: transparent; outline: none; font-size: .85rem; color: var(--p-text); flex: 1; min-width: 0; }
.p-search-input::placeholder { color: var(--p-muted); }
.p-search-btn { display: inline-flex; align-items: center; justify-content: center; width: 26px; height: 26px; border-radius: 6px; background: var(--p-accent); color: #fff; border: none; cursor: pointer; flex-shrink: 0; transition: opacity .13s; }
.p-search-btn:hover { opacity: .85; }
.p-search-btn i { font-size: .7rem; }
.p-dd-wrap { position: relative; }
.p-pill { display: inline-flex; align-items: center; gap: 6px; padding: 6px 12px; border: 1.5px solid var(--p-border); border-radius: var(--p-radius-xs); background: var(--p-surface); font-size: .82rem; color: var(--p-text-2); cursor: pointer; user-select: none; white-space: nowrap; transition: all .13s; }
.p-pill:hover { border-color: var(--p-accent); color: var(--p-accent); background: var(--p-accent-soft); }
.p-pill.active { border-color: var(--p-accent); color: var(--p-accent); background: var(--p-accent-soft); font-weight: 600; }
.p-pill i.p-pi { font-size: .72rem; opacity: .55; transition: transform .15s; }
.p-pill.p-open i.p-pi { transform: rotate(180deg); }
.p-vdiv { width: 1px; height: 20px; background: var(--p-border); flex-shrink: 0; }
.p-reset-btn { display: inline-flex; align-items: center; gap: 5px; padding: 6px 12px; border-radius: var(--p-radius-xs); border: 1.5px solid var(--p-border); background: var(--p-surface); font-size: .8rem; color: var(--p-muted); cursor: pointer; margin-left: auto; transition: all .13s; white-space: nowrap; }
.p-reset-btn:hover { border-color: var(--p-danger); color: var(--p-danger); background: var(--p-danger-soft); }
.p-dd-panel { display: none; position: absolute; top: calc(100% + 6px); left: 0; z-index: 1050; background: var(--p-surface); border: 1px solid var(--p-border); border-radius: var(--p-radius-sm); box-shadow: var(--p-shadow-md); padding: 6px; min-width: 230px; max-height: 290px; overflow-y: auto; }
.p-dd-panel.open { display: block; animation: p-pop .12s cubic-bezier(.2,.8,.2,1); }
.p-date-panel { display: none; position: absolute; top: calc(100% + 6px); right: 0; z-index: 1050; background: var(--p-surface); border: 1px solid var(--p-border); border-radius: var(--p-radius-sm); box-shadow: var(--p-shadow-md); padding: 16px; min-width: 265px; }
.p-date-panel.open { display: block; animation: p-pop .12s cubic-bezier(.2,.8,.2,1); }
@keyframes p-pop { from{opacity:0;transform:translateY(-6px) scale(.97);}to{opacity:1;transform:translateY(0) scale(1);} }
.p-dd-hdr { font-size: .58rem; font-weight: 800; letter-spacing: .12em; text-transform: uppercase; color: var(--p-muted); padding: 5px 10px 8px; }
.p-dd-isearch { display: flex; align-items: center; gap: 6px; background: var(--p-surface-2); border: 1px solid var(--p-border); border-radius: var(--p-radius-xs); padding: 5px 9px; margin: 0 4px 6px; }
.p-dd-isearch i { font-size: .7rem; color: var(--p-muted); flex-shrink: 0; }
.p-dd-isearch input { border: none; background: transparent; outline: none; font-size: .81rem; color: var(--p-text); width: 100%; }
.p-dd-item { display: flex; align-items: center; gap: 9px; padding: 7px 10px; border-radius: var(--p-radius-xs); cursor: pointer; font-size: .83rem; color: var(--p-text-2); transition: background .1s; user-select: none; }
.p-dd-item:hover { background: var(--p-surface-2); }
.p-dd-item.sel { background: var(--p-accent-soft); color: var(--p-accent); }
.p-chk { width: 15px; height: 15px; border-radius: 4px; border: 1.5px solid var(--p-border); background: var(--p-surface); display: flex; align-items: center; justify-content: center; flex-shrink: 0; transition: all .12s; }
.p-dd-item.sel .p-chk { background: var(--p-accent); border-color: var(--p-accent); }
.p-chk-i { display: none; color: #fff; font-size: .58rem; }
.p-dd-item.sel .p-chk-i { display: block; }
.p-sdot { width: 7px; height: 7px; border-radius: 50%; flex-shrink: 0; display: inline-block; }
.p-date-lbl { font-size: .6rem; font-weight: 700; letter-spacing: .1em; text-transform: uppercase; color: var(--p-muted); display: block; margin-bottom: 4px; }
.p-date-input { width: 100%; border: 1.5px solid var(--p-border); border-radius: var(--p-radius-xs); padding: 7px 10px; font-size: .83rem; color: var(--p-text); background: var(--p-surface-2); outline: none; margin-bottom: 10px; transition: border-color .13s, box-shadow .13s; }
.p-date-input:focus { border-color: var(--p-accent); box-shadow: 0 0 0 3px var(--p-accent-glow); }
.p-date-btns { display: flex; gap: 8px; }
.p-btn-apply { flex: 1; padding: 8px; border-radius: var(--p-radius-xs); background: var(--p-accent); color: #fff; border: none; font-size: .82rem; font-weight: 700; cursor: pointer; transition: opacity .13s; letter-spacing: .02em; }
.p-btn-apply:hover { opacity: .88; }
.p-btn-clear { padding: 8px 14px; border-radius: var(--p-radius-xs); background: var(--p-surface-2); color: var(--p-muted); border: 1.5px solid var(--p-border); font-size: .82rem; cursor: pointer; transition: all .13s; }
.p-btn-clear:hover { color: var(--p-danger); border-color: var(--p-danger); background: var(--p-danger-soft); }
.p-results-bar { display: flex; align-items: center; justify-content: space-between; margin-bottom: 16px; flex-wrap: wrap; gap: 8px; }
.p-results-txt { font-size: .82rem; color: var(--p-muted); }
.p-results-txt strong { color: var(--p-text); font-weight: 700; }
.p-chips { display: flex; align-items: center; gap: 6px; flex-wrap: wrap; }
.p-chip { display: inline-flex; align-items: center; gap: 5px; padding: 3px 10px; border-radius: 50px; background: var(--p-accent-soft); border: 1px solid rgba(79,82,255,.2); color: var(--p-accent); font-size: .73rem; font-weight: 600; }
.p-chip-x { background: none; border: none; color: var(--p-accent); cursor: pointer; padding: 0 1px; line-height: 1; font-size: .7rem; opacity: .6; }
.p-chip-x:hover { opacity: 1; }

/* ════════════════════════════════════════════
   ★ PREMIUM PROJECT CARD GRID — redesigned
════════════════════════════════════════════ */

/* Stagger-in animation for cards */
@keyframes pc-rise {
    from { opacity: 0; transform: translateY(18px) scale(.98); }
    to   { opacity: 1; transform: translateY(0)    scale(1);   }
}

/* ── Card shell ── */
.p-card {
    background: var(--p-surface);
    border: 1px solid var(--p-border);
    border-radius: 0px 0px 15px 15px;
    box-shadow: 0 2px 8px rgba(15,18,41,.05), 0 1px 2px rgba(15,18,41,.04);
    overflow: hidden;
    display: flex; flex-direction: column;
    height: 100%;
    position: relative;
    transition: box-shadow .28s cubic-bezier(.4,0,.2,1),
                transform   .28s cubic-bezier(.4,0,.2,1),
                border-color .28s;
    animation: pc-rise .42s cubic-bezier(.4,0,.2,1) both;
}
/* stagger via nth-child */
.p-card:nth-child(1)  { animation-delay: .04s; }
.p-card:nth-child(2)  { animation-delay: .08s; }
.p-card:nth-child(3)  { animation-delay: .12s; }
.p-card:nth-child(4)  { animation-delay: .16s; }
.p-card:nth-child(5)  { animation-delay: .20s; }
.p-card:nth-child(6)  { animation-delay: .24s; }
.p-card:nth-child(7)  { animation-delay: .28s; }
.p-card:nth-child(8)  { animation-delay: .32s; }

.p-card:hover {
    box-shadow: 0 16px 48px rgba(79,82,255,.13), 0 4px 16px rgba(15,18,41,.08);
    transform: translateY(-5px);
    border-color: rgba(79,82,255,.22);
}

/* ── Coloured status cap at the top ── */
.p-card-cap {
    height: 4px;
    width: 100%;
    flex-shrink: 0;
    border-radius: 20px 20px 0 0;
}
.p-card[data-status="in_progress"] .p-card-cap { background: linear-gradient(90deg, #4f52ff 0%, #7c5cfc 100%); }
.p-card[data-status="on_hold"]     .p-card-cap { background: linear-gradient(90deg, #f59e0b 0%, #fb923c 100%); }
.p-card[data-status="complete"]    .p-card-cap { background: linear-gradient(90deg, #10b981 0%, #34d399 100%); }
.p-card[data-status="canceled"]    .p-card-cap { background: linear-gradient(90deg, #f43f5e 0%, #fb7185 100%); }
.p-card[data-status="default"]     .p-card-cap { background: var(--p-border); }

/* ── Card header ── */
.p-card-head {
    padding: 18px 18px 14px;
    display: flex; align-items: flex-start; gap: 12px;
}
.p-card-thumb {
    width: 46px; height: 46px;
    border-radius: 12px;
    object-fit: cover;
    border: 1px solid var(--p-border);
    flex-shrink: 0;
    background: var(--p-surface-3);
    box-shadow: 0 2px 8px rgba(15,18,41,.08);
}
.p-card-meta { flex: 1; min-width: 0; padding-top: 1px; }
.p-card-name {
    font-size: .9rem; font-weight: 700; color: var(--p-text);
    text-decoration: none; display: block;
    overflow: hidden; text-overflow: ellipsis; white-space: nowrap;
    transition: color .15s; line-height: 1.35; margin-bottom: 5px;
    letter-spacing: -.01em;
}
.p-card-name:hover { color: var(--p-accent); }

/* status badge */
.p-sbadge {
    display: inline-flex; align-items: center; gap: 5px;
    padding: 3px 9px; border-radius: 50px;
    font-size: .67rem; font-weight: 700; letter-spacing: .03em;
}
.p-sbadge::before {
    content: ''; width: 5px; height: 5px;
    border-radius: 50%; display: inline-block;
}
.p-s-in_progress { background: rgba(79,82,255,.1);  color: var(--p-accent);   }
.p-s-in_progress::before { background: var(--p-accent); }
.p-s-on_hold     { background: rgba(245,158,11,.1); color: var(--p-warn);     }
.p-s-on_hold::before     { background: var(--p-warn); }
.p-s-complete    { background: rgba(16,185,129,.1); color: var(--p-success);  }
.p-s-complete::before    { background: var(--p-success); }
.p-s-canceled    { background: rgba(244,63,94,.1);  color: var(--p-danger);   }
.p-s-canceled::before    { background: var(--p-danger); }
.p-s-default     { background: var(--p-surface-3);  color: var(--p-muted);    }
.p-s-default::before     { background: var(--p-muted); }

/* dot-menu */
.p-dot-btn {
    background: transparent; border: none; padding: 5px 6px;
    border-radius: 8px; color: var(--p-muted);
    cursor: pointer; transition: background .12s, color .12s; line-height: 1;
    flex-shrink: 0;
}
.p-dot-btn:hover { background: var(--p-surface-3); color: var(--p-text); }
.p-card .dropdown-menu {
    border: 1px solid var(--p-border); border-radius: 12px;
    box-shadow: var(--p-shadow-md); padding: 5px; font-size: .82rem; min-width: 155px;
}
.p-card .dropdown-item {
    border-radius: 7px; padding: 7px 12px;
    display: flex; align-items: center; gap: 9px;
    color: var(--p-text-2); transition: background .1s; font-size: .82rem;
}
.p-card .dropdown-item:hover { background: var(--p-surface-2); }
.p-card .dropdown-item.text-danger { color: var(--p-danger) !important; }
.p-card .dropdown-item.text-danger:hover { background: var(--p-danger-soft); }
.p-card .dropdown-item i {
    width: 24px; height: 24px; border-radius: 6px;
    display: flex; align-items: center; justify-content: center;
    font-size: .72rem; background: var(--p-accent-soft); color: var(--p-accent);
}
.p-card .dropdown-item.text-danger i { background: var(--p-danger-soft); color: var(--p-danger); }

/* ── Card body ── */
.p-card-body {
    padding: 0 18px 16px; flex: 1;
}
.p-divider {
    height: 1px;
    background: var(--p-border);
    margin-bottom: 16px;
}

/* Members row */
.p-members-row {
    display: flex; align-items: center; justify-content: space-between;
    margin-bottom: 16px;
}
.p-members-lbl {
    font-size: .62rem; font-weight: 700; color: var(--p-muted);
    text-transform: uppercase; letter-spacing: .1em;
}
.p-avatars { display: flex; align-items: center; }
.p-av {
    width: 28px; height: 28px; border-radius: 50%;
    object-fit: cover; border: 2.5px solid var(--p-surface);
    margin-left: -8px; flex-shrink: 0;
    box-shadow: 0 1px 4px rgba(15,18,41,.14);
    transition: transform .15s;
}
.p-av:first-child { margin-left: 0; }
.p-av:hover { transform: scale(1.15); z-index: 2; }
.p-av-more {
    width: 28px; height: 28px; border-radius: 50%;
    background: var(--p-accent-soft); border: 2.5px solid var(--p-surface);
    margin-left: -8px; display: flex; align-items: center; justify-content: center;
    font-size: .58rem; font-weight: 800; color: var(--p-accent);
    letter-spacing: -.01em;
}

/* Progress */
.p-prog-wrap { }
.p-prog-top {
    display: flex; align-items: center; justify-content: space-between;
    margin-bottom: 7px;
}
.p-prog-lbl {
    font-size: .65rem; font-weight: 700; color: var(--p-muted);
    text-transform: uppercase; letter-spacing: .09em;
}
.p-prog-pct {
    font-size: .8rem; font-weight: 800; color: var(--p-success);
    letter-spacing: -.02em;
}
.p-prog-track {
    width: 100%; height: 6px; border-radius: 50px;
    background: var(--p-surface-3); overflow: hidden;
    box-shadow: inset 0 1px 2px rgba(15,18,41,.06);
}
.p-prog-fill {
    height: 100%; border-radius: 50px;
    background: linear-gradient(90deg, var(--p-accent) 0%, #34d399 100%);
    transition: width .6s cubic-bezier(.4,0,.2,1);
    position: relative;
}
/* shimmer on progress bar */
.p-prog-fill::after {
    content: '';
    position: absolute; top: 0; left: -60%; height: 100%; width: 40%;
    background: linear-gradient(90deg, transparent, rgba(255,255,255,.45), transparent);
    animation: pc-shimmer 2.2s ease-in-out infinite;
    border-radius: 50px;
}
@keyframes pc-shimmer {
    0%   { left: -60%; }
    100% { left: 120%; }
}

/* ── Card footer ── */
.p-card-foot {
    padding: 13px 18px 15px;
    border-top: 1px solid var(--p-border);
    background: var(--p-surface-2);
    border-radius: 0 0 20px 20px;
}
.p-date-grid {
    display: grid; grid-template-columns: 1fr 1fr; gap: 12px;
}
.p-date-item {
    display: flex; flex-direction: column; gap: 2px;
}
.p-date-item:nth-child(even) { align-items: flex-end; }
.p-date-lbl-sm {
    font-size: .58rem; font-weight: 700; color: var(--p-muted);
    text-transform: uppercase; letter-spacing: .09em;
}
.p-date-val {
    font-size: .78rem; font-weight: 600; color: var(--p-text-2);
    letter-spacing: -.01em;
}
.p-date-val.overdue { color: var(--p-danger); }

/* ── Empty state ── */
.p-empty {
    padding: 70px 20px; text-align: center; color: var(--p-muted); width: 100%;
}
.p-empty-icon {
    width: 56px; height: 56px; border-radius: 14px;
    background: var(--p-surface-3); border: 1px solid var(--p-border);
    display: flex; align-items: center; justify-content: center;
    margin: 0 auto 14px;
}
.p-empty-icon i { font-size: 1.5rem; opacity: .3; }
.p-empty p { font-size: .88rem; margin: 0; }
</style>

{{-- ══ STAT CARDS ══ --}}
<div class="row g-3 p-stats-row w-100">
    @foreach($projectStatuses as $statusKey => $statusLabel)
        @php $count = $projectCounts[$statusKey] ?? 0; @endphp
        <div class="col-12 col-sm-6 col-md-3">
            <div class="p-stat-card" data-status="{{ $statusKey }}">
                <div class="p-stat-left">
                    <div class="p-stat-icon">
                        <img src="{{ $statusImages[$statusKey] ?? asset('assets/assestsnew/default_status.svg') }}"
                             alt="{{ $statusLabel }}">
                    </div>
                    <div>
                        <div class="p-stat-label">{{ $statusLabel }}</div>
                        <div class="p-stat-sub">projects</div>
                    </div>
                </div>
                <div class="p-stat-count">{{ $count }}</div>
            </div>
        </div>
    @endforeach
</div>

{{-- ══ FILTER BAR ══ --}}
<div class="p-filter-card w-100">

    {{-- Search --}}
    <div class="p-search-wrap">
        <i class="ti ti-search"></i>
        <input type="text" id="g-search" class="p-search-input"
               placeholder="{{__('Search by project name…')}}" autocomplete="off"
               value="{{ request('keyword') }}"
               onkeydown="if(event.key==='Enter'){gDoSearch();}">
        <button type="button" class="p-search-btn" onclick="gDoSearch()">
            <i class="ti ti-arrow-right"></i>
        </button>
    </div>

    <div class="p-vdiv"></div>

    {{-- Project multi-select --}}
    <div class="p-dd-wrap">
        <div class="p-pill" id="g-proj-trigger"
             onclick="gToggle('g-proj-panel','g-proj-trigger')">
            <i class="ti ti-folder" style="font-size:.76rem;"></i>
            <span id="g-proj-lbl">{{__('Project')}}</span>
            <i class="ti ti-chevron-down p-pi"></i>
        </div>
        <div class="p-dd-panel" id="g-proj-panel">
            <div class="p-dd-hdr">{{__('Filter by Project')}}</div>
            <div class="p-dd-isearch">
                <i class="ti ti-search"></i>
                <input type="text" id="g-proj-isearch"
                       placeholder="{{__('Search…')}}"
                       oninput="gProjSearch(this.value)">
            </div>
            <div class="p-dd-item sel" data-val="" onclick="gSelProj(this)">
                <div class="p-chk"><i class="ti ti-check p-chk-i"></i></div>
                {{__('All Projects')}}
            </div>
            @foreach($allProjects as $proj)
                <div class="p-dd-item g-proj-opt"
                     data-val="{{ $proj->id }}"
                     data-lbl="{{ strtolower($proj->project_name) }}"
                     onclick="gSelProj(this)">
                    <div class="p-chk"><i class="ti ti-check p-chk-i"></i></div>
                    <span style="overflow:hidden;text-overflow:ellipsis;white-space:nowrap;max-width:150px;display:block;">
                        {{ $proj->project_name }}
                    </span>
                </div>
            @endforeach
        </div>
    </div>

    {{-- Status multi-select --}}
    <div class="p-dd-wrap">
        <div class="p-pill" id="g-status-trigger"
             onclick="gToggle('g-status-panel','g-status-trigger')">
            <i class="ti ti-adjustments-horizontal" style="font-size:.76rem;"></i>
            <span id="g-status-lbl">{{__('Status')}}</span>
            <i class="ti ti-chevron-down p-pi"></i>
        </div>
        <div class="p-dd-panel" id="g-status-panel">
            <div class="p-dd-hdr">{{__('Filter by Status')}}</div>
            <div class="p-dd-item sel" data-val="" onclick="gSelStatus(this)">
                <div class="p-chk"><i class="ti ti-check p-chk-i"></i></div>
                <span class="p-sdot" style="background:var(--p-muted);"></span>
                {{__('Show All')}}
            </div>
            @foreach($projectStatuses as $sKey => $sLabel)
                @php
                    $dc = match($sKey) {
                        'in_progress' => 'var(--p-accent)',
                        'on_hold'     => 'var(--p-warn)',
                        'complete'    => 'var(--p-success)',
                        'canceled'    => 'var(--p-danger)',
                        default       => 'var(--p-muted)',
                    };
                @endphp
                <div class="p-dd-item" data-val="{{ $sKey }}" onclick="gSelStatus(this)">
                    <div class="p-chk"><i class="ti ti-check p-chk-i"></i></div>
                    <span class="p-sdot" style="background:{{ $dc }};"></span>
                    {{ __($sLabel) }}
                </div>
            @endforeach
        </div>
    </div>

    {{-- Date range --}}
    <div class="p-dd-wrap">
        <div class="p-pill" id="g-date-trigger"
             onclick="gToggle('g-date-panel','g-date-trigger')">
            <i class="ti ti-calendar-event" style="font-size:.76rem;"></i>
            <span id="g-date-lbl">{{__('Date Range')}}</span>
            <i class="ti ti-chevron-down p-pi"></i>
        </div>
        <div class="p-date-panel" id="g-date-panel">
            <div class="p-dd-hdr" style="padding-bottom:12px;">{{__('Onboard Date Range')}}</div>
            <label class="p-date-lbl">{{__('From')}}</label>
            <input type="date" id="g-date-from" class="p-date-input" value="{{ request('date_from') }}">
            <label class="p-date-lbl">{{__('To')}}</label>
            <input type="date" id="g-date-to" class="p-date-input" value="{{ request('date_to') }}">
            <div class="p-date-btns">
                <button class="p-btn-apply" type="button" onclick="gApplyDate()">{{__('Apply')}}</button>
                <button class="p-btn-clear" type="button" onclick="gClearDate()">{{__('Clear')}}</button>
            </div>
        </div>
    </div>

    <div class="p-vdiv"></div>

    {{-- Reset --}}
    <button class="p-reset-btn" type="button" onclick="gResetAll()">
        <i class="ti ti-rotate" style="font-size:.78rem;"></i>
        {{__('Reset')}}
    </button>
</div>

{{-- ══ RESULTS BAR ══ --}}
<div class="p-results-bar w-100">
    <span class="p-results-txt">
        {{__('Showing')}} <strong>{{ count($projects) }}</strong> {{__('of')}} <strong>{{ count($allProjects) }}</strong> {{__('projects')}}
    </span>
    <div class="p-chips" id="p-active-chips"></div>
</div>

{{-- ══ PROJECT CARDS — premium grid ══ --}}
<div class="row g-3 w-100">
    @foreach ($projects as $project)
        @php
            $image     = $project->project_image ? \App\Models\Utility::get_file($project->project_image) : asset('assets/assestsnew/Manage_projects.svg');
            $sKey      = $project->status;
            $sLabel    = \App\Models\Project::$project_status[$sKey] ?? $sKey;
            $sClass    = 'p-s-' . $sKey;
            $onboard   = $project->on_board_date;
            $renewal   = $project->renewal_date;
            $suppStart = $project->support_start_date;
            $suppEnd   = $project->support_end_date;
        @endphp
        <div class="col-xxl-3 col-xl-4 col-md-4 col-sm-6 col-12">
            <div class="p-card" data-status="{{ $sKey }}">

                {{-- Coloured status cap --}}
                <div class="p-card-cap"></div>

                {{-- Header --}}
                <div class="p-card-head">
                    <img src="{{ $image }}" class="p-card-thumb"
                         alt="{{ $project->project_name }}" width="46" height="46">
                    <div class="p-card-meta">
                        <a href="{{ route('organization.projects.show', $project) }}"
                           class="p-card-name" title="{{ $project->project_name }}">
                            {{ $project->project_name }}
                        </a>
                        <span class="p-sbadge {{ $sClass }}">{{ __($sLabel) }}</span>
                    </div>
                    @can('project_management')
                    <div class="dropdown">
                        <button class="p-dot-btn" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="ti ti-dots-vertical"></i>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li>
                                <a class="dropdown-item" href="#!"
                                   data-size="lg"
                                   data-url="{{ route('organization.projects.edit', $project->id) }}"
                                   data-ajax-popup="true">
                                    <i class="ti ti-pencil"></i> {{ __('Edit') }}
                                </a>
                            </li>
                            <li>
                                {!! Form::open(['method' => 'DELETE', 'route' => ['organization.projects.destroy', $project->id]]) !!}
                                <a href="#!" class="dropdown-item text-danger bs-pass-para">
                                    <i class="ti ti-trash"></i> {{ __('Delete') }}
                                </a>
                                {!! Form::close() !!}
                            </li>
                        </ul>
                    </div>
                    @endcan
                </div>

                {{-- Body --}}
                <div class="p-card-body">
                    <div class="p-divider"></div>

                    {{-- Members --}}
                    <div class="p-members-row">
                        <span class="p-members-lbl">{{__('Team')}}</span>
                        <div class="p-avatars">
                            @forelse($project->users->take(4) as $user)
                                @php
                                    $gender = $user->employee->gender ?? null;
                                    $avSrc  = match(true) {
                                        $gender === GENDER_MALE   => asset('assets/assestsnew/menimg.png'),
                                        $gender === GENDER_FEMALE => asset('assets/assestsnew/femaile-report.svg'),
                                        default => $user->avatar ? asset('/storage/uploads/avatar/'.$user->avatar) : asset('assets/assestsnew/profile.png'),
                                    };
                                @endphp
                                <img src="{{ $avSrc }}" class="p-av"
                                     width="28" height="28"
                                     title="{{ $user->name }}" alt="{{ $user->name }}">
                            @empty
                                <span style="font-size:.78rem;color:var(--p-muted);">—</span>
                            @endforelse
                            @if($project->users->count() > 4)
                                <div class="p-av-more">+{{ $project->users->count() - 4 }}</div>
                            @endif
                        </div>
                    </div>

                    {{-- Progress --}}
                    <div class="p-prog-wrap">
                        <div class="p-prog-top">
                            <span class="p-prog-lbl">{{__('Progress')}}</span>
                            <span class="p-prog-pct">{{ $project->progress }}%</span>
                        </div>
                        <div class="p-prog-track">
                            <div class="p-prog-fill" style="width:{{ $project->progress }}%;"></div>
                        </div>
                    </div>
                </div>

                {{-- Footer --}}
                <div class="p-card-foot">
                    <div class="p-date-grid">
                        <div class="p-date-item">
                            <span class="p-date-lbl-sm">{{__('Onboard')}}</span>
                            <span class="p-date-val {{ $onboard && strtotime($onboard) < time() ? 'overdue' : '' }}">
                                {{ Utility::getDateFormated($onboard) ?: '—' }}
                            </span>
                        </div>
                        <div class="p-date-item">
                            <span class="p-date-lbl-sm">{{__('Renewal')}}</span>
                            <span class="p-date-val">{{ Utility::getDateFormated($renewal) ?: '—' }}</span>
                        </div>
                        <div class="p-date-item">
                            <span class="p-date-lbl-sm">{{__('Supp. Start')}}</span>
                            <span class="p-date-val {{ $suppStart && strtotime($suppStart) < time() ? 'overdue' : '' }}">
                                {{ Utility::getDateFormated($suppStart) ?: '—' }}
                            </span>
                        </div>
                        <div class="p-date-item">
                            <span class="p-date-lbl-sm">{{__('Supp. End')}}</span>
                            <span class="p-date-val">{{ Utility::getDateFormated($suppEnd) ?: '—' }}</span>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    @endforeach
</div>

<script>
(function () {
/* ─────────────────────────────────────────────────────────
   window.G = single source of truth. Never reset on re-render.
   gRestoreUI() re-applies state after each AJAX re-render.
───────────────────────────────────────────────────────── */
if (!window.G) {
    window.G = { projIds: [], statuses: [], dateFrom: '', dateTo: '', keyword: '' };
}

function el(id)   { return document.getElementById(id); }
function qs(s)    { return document.querySelector(s); }
function qsa(s)   { return document.querySelectorAll(s); }
function setText(id, v) { if (el(id)) el(id).textContent = v; }

function gFireAjax() {
    window.gProjectIds = window.G.projIds;
    window.gDateFrom   = window.G.dateFrom;
    window.gDateTo     = window.G.dateTo;
    window.gStatuses   = window.G.statuses;
    if (typeof window.ajaxFilterProjectView === 'function') window.ajaxFilterProjectView();
}

/* ── restore UI & chips after re-render ── */
function gRestoreUI() {
    var G = window.G;

    /* project dropdown */
    qsa('#g-proj-panel .p-dd-item').forEach(function(i){ i.classList.remove('sel'); });
    if (G.projIds.length === 0) {
        var a = qs('#g-proj-panel .p-dd-item[data-val=""]');
        if (a) a.classList.add('sel');
        setText('g-proj-lbl', '{{__("Project")}}');
        if (el('g-proj-trigger')) el('g-proj-trigger').classList.remove('active');
    } else {
        G.projIds.forEach(function(id) {
            var item = qs('#g-proj-panel .p-dd-item[data-val="' + id + '"]');
            if (item) item.classList.add('sel');
        });
        setText('g-proj-lbl', G.projIds.length + ' {{__("selected")}}');
        if (el('g-proj-trigger')) el('g-proj-trigger').classList.add('active');
    }

    /* status dropdown */
    qsa('#g-status-panel .p-dd-item').forEach(function(i){ i.classList.remove('sel'); });
    if (G.statuses.length === 0) {
        var b = qs('#g-status-panel .p-dd-item[data-val=""]');
        if (b) b.classList.add('sel');
        setText('g-status-lbl', '{{__("Status")}}');
        if (el('g-status-trigger')) el('g-status-trigger').classList.remove('active');
    } else {
        G.statuses.forEach(function(s) {
            var item = qs('#g-status-panel .p-dd-item[data-val="' + s + '"]');
            if (item) item.classList.add('sel');
        });
        setText('g-status-lbl', G.statuses.length + ' {{__("selected")}}');
        if (el('g-status-trigger')) el('g-status-trigger').classList.add('active');
    }

    /* date */
    if (el('g-date-from')) el('g-date-from').value = G.dateFrom;
    if (el('g-date-to'))   el('g-date-to').value   = G.dateTo;
    if (G.dateFrom || G.dateTo) {
        setText('g-date-lbl', (G.dateFrom || '…') + ' → ' + (G.dateTo || '…'));
        if (el('g-date-trigger')) el('g-date-trigger').classList.add('active');
    } else {
        setText('g-date-lbl', '{{__("Date Range")}}');
        if (el('g-date-trigger')) el('g-date-trigger').classList.remove('active');
    }

    /* search */
    if (el('g-search')) el('g-search').value = G.keyword || '';

    /* active chips */
    renderChips();
}

function renderChips() {
    var wrap = el('p-active-chips');
    if (!wrap) return;
    wrap.innerHTML = '';
    var G = window.G;

    function chip(label, onRemove) {
        var span = document.createElement('span');
        span.className = 'p-chip';
        span.innerHTML = label + '<button class="p-chip-x" title="Remove">&#x2715;</button>';
        span.querySelector('.p-chip-x').onclick = onRemove;
        wrap.appendChild(span);
    }

    /* project chips */
    G.projIds.forEach(function(id) {
        var item = qs('#g-proj-panel .p-dd-item[data-val="' + id + '"] span');
        var lbl  = item ? item.textContent.trim() : id;
        chip(lbl, function() {
            var idx = G.projIds.indexOf(id);
            if (idx > -1) G.projIds.splice(idx, 1);
            gRestoreUI(); gFireAjax();
        });
    });

    /* status chips */
    G.statuses.forEach(function(s) {
        var item = qs('#g-status-panel .p-dd-item[data-val="' + s + '"]');
        var lbl  = item ? item.textContent.trim() : s;
        chip(lbl, function() {
            var idx = G.statuses.indexOf(s);
            if (idx > -1) G.statuses.splice(idx, 1);
            gRestoreUI(); gFireAjax();
        });
    });

    /* date chip */
    if (G.dateFrom || G.dateTo) {
        chip((G.dateFrom || '…') + ' → ' + (G.dateTo || '…'), function() {
            G.dateFrom = ''; G.dateTo = '';
            gRestoreUI(); gFireAjax();
        });
    }

    /* search chip */
    if (G.keyword) {
        chip('Search: ' + G.keyword, function() {
            G.keyword = '';
            var kw = el('project_keyword'); if (kw) kw.value = '';
            gRestoreUI(); gFireAjax();
        });
    }
}

/* run immediately */
gRestoreUI();

/* ── DROPDOWN TOGGLE ── */
window.gToggle = function(panelId, triggerId) {
    var panel = el(panelId), trigger = el(triggerId);
    if (!panel || !trigger) return;
    var isOpen = panel.classList.contains('open');
    qsa('.p-dd-panel.open,.p-date-panel.open').forEach(function(p){ p.classList.remove('open'); });
    qsa('.p-pill.p-open').forEach(function(t){ t.classList.remove('p-open'); });
    if (!isOpen) { panel.classList.add('open'); trigger.classList.add('p-open'); }
};

if (window._gOutsideClick) document.removeEventListener('click', window._gOutsideClick);
window._gOutsideClick = function(e) {
    if (!e.target.closest('.p-dd-wrap')) {
        qsa('.p-dd-panel.open,.p-date-panel.open').forEach(function(p){ p.classList.remove('open'); });
        qsa('.p-pill.p-open').forEach(function(t){ t.classList.remove('p-open'); });
    }
};
document.addEventListener('click', window._gOutsideClick);

/* ── PROJECT MULTI-SELECT ── */
window.gSelProj = function(elem) {
    var val = elem.dataset.val, G = window.G;
    if (val === '') { G.projIds = []; }
    else {
        var idx = G.projIds.indexOf(val);
        if (idx === -1) G.projIds.push(val); else G.projIds.splice(idx, 1);
    }
    gRestoreUI(); gFireAjax();
};
window.gProjSearch = function(q) {
    var t = q.toLowerCase().trim();
    qsa('#g-proj-panel .g-proj-opt').forEach(function(item) {
        item.style.display = (!t || (item.dataset.lbl || '').includes(t)) ? '' : 'none';
    });
};

/* ── STATUS MULTI-SELECT ── */
window.gSelStatus = function(elem) {
    var val = elem.dataset.val, G = window.G;
    if (val === '') { G.statuses = []; }
    else {
        var idx = G.statuses.indexOf(val);
        if (idx === -1) G.statuses.push(val); else G.statuses.splice(idx, 1);
    }
    gRestoreUI(); gFireAjax();
};

/* ── DATE RANGE ── */
window.gApplyDate = function() {
    var G = window.G;
    G.dateFrom = el('g-date-from') ? el('g-date-from').value : '';
    G.dateTo   = el('g-date-to')   ? el('g-date-to').value   : '';
    if (el('g-date-panel')) el('g-date-panel').classList.remove('open');
    if (el('g-date-trigger')) el('g-date-trigger').classList.remove('p-open');
    gRestoreUI(); gFireAjax();
};
window.gClearDate = function() {
    window.G.dateFrom = ''; window.G.dateTo = '';
    gRestoreUI(); gFireAjax();
};

/* ── SEARCH ── */
if (window._gSearchHandler) document.removeEventListener('input', window._gSearchHandler);
window._gSearchHandler = null;
window.gDoSearch = function() {
    var inp = el('g-search');
    if (inp) window.G.keyword = inp.value;
    var kw = el('project_keyword'); if (kw) kw.value = window.G.keyword;
    gRestoreUI(); gFireAjax();
};

/* ── RESET ── */
window.gResetAll = function() {
    window.G = { projIds: [], statuses: [], dateFrom: '', dateTo: '', keyword: '' };
    var kw = el('project_keyword'); if (kw) kw.value = '';
    gRestoreUI(); gFireAjax();
};

})();
</script>

@else
<div class="col-12">
    <div style="background:#fff;border:1px solid #e4e7f0;border-radius:16px;box-shadow:0 4px 16px rgba(15,18,41,.08);padding:70px 20px;text-align:center;color:#8890b0;">
        <div style="width:56px;height:56px;border-radius:14px;background:#f1f3f9;border:1px solid #e4e7f0;display:flex;align-items:center;justify-content:center;margin:0 auto 14px;">
            <i class="ti ti-folder-off" style="font-size:1.5rem;opacity:.3;"></i>
        </div>
        <p style="font-size:.9rem;margin:0;font-weight:500;">{{__('No Projects Found.')}}</p>
    </div>
</div>
@endif