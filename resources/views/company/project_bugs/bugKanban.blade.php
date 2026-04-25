@extends('company.layouts.company')
@section('page-title')
    {{ __('Manage Bug Report') }}
@endsection

@section('page-icon')
    {{ asset('assets/assestsnew/project_bug1.svg') }}
@endsection

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('organization.projects.index') }}">{{ __('Project') }}</a></li>
    <li class="breadcrumb-item"><a href="{{ route('organization.projects.show', $project->id) }}">
            {{ ucwords($project->project_name) }}</a>
    </li>
    <li class="breadcrumb-item">{{ __('Bug Report') }}</li>
@endsection

@push('css-page')
    <link rel="stylesheet" href="{{ asset('assets/css/plugins/dragula.min.css') }}" id="main-style-link">
@endpush

@push('script-page')
    <script src="{{ asset('assets/js/plugins/dragula.min.js') }}"></script>
    <script>
        !function (a) {
            "use strict";
            var t = function () { this.$body = a("body") };
            t.prototype.init = function () {
                a('[data-plugin="dragula"]').each(function () {
                    var t = a(this).data("containers"), n = [];
                    if (t) for (var i = 0; i < t.length; i++) n.push(a("#" + t[i])[0]);
                    else n = [a(this)[0]];
                    var r = a(this).data("handleclass");
                    var scrollSpeed = 15, scrollZone = 100, scrollInterval, verticalScrollInterval;
                    var scrollContainer = a(this)[0], currentVerticalContainer = null;
                    var dragulaInstance = r ? dragula(n, { moves: function (a, t, n) { return n.classList.contains(r) } }) : dragula(n);

                    dragulaInstance.on('drag', function () {
                        a(document).on('mousemove.dragula-autoscroll', function(e) {
                            if (scrollInterval) clearInterval(scrollInterval);
                            if (verticalScrollInterval) clearInterval(verticalScrollInterval);
                            var containerRect = scrollContainer.getBoundingClientRect();
                            var mouseX = e.clientX, mouseY = e.clientY;
                            if (mouseX < containerRect.left + scrollZone && scrollContainer.scrollLeft > 0) {
                                scrollInterval = setInterval(function() { scrollContainer.scrollLeft -= scrollSpeed; if (scrollContainer.scrollLeft <= 0) clearInterval(scrollInterval); }, 20);
                            } else if (mouseX > containerRect.right - scrollZone) {
                                var maxScroll = scrollContainer.scrollWidth - scrollContainer.clientWidth;
                                if (scrollContainer.scrollLeft < maxScroll) scrollInterval = setInterval(function() { scrollContainer.scrollLeft += scrollSpeed; if (scrollContainer.scrollLeft >= maxScroll) clearInterval(scrollInterval); }, 20);
                            } else { if (scrollInterval) { clearInterval(scrollInterval); scrollInterval = null; } }
                            var verticalContainers = a('.kb-col-body');
                            currentVerticalContainer = null;
                            verticalContainers.each(function() {
                                var rect = this.getBoundingClientRect();
                                if (mouseX >= rect.left && mouseX <= rect.right && mouseY >= rect.top && mouseY <= rect.bottom) { currentVerticalContainer = this; return false; }
                            });
                            if (currentVerticalContainer) {
                                var verticalRect = currentVerticalContainer.getBoundingClientRect(), scrollZoneVertical = 80;
                                if (mouseY < verticalRect.top + scrollZoneVertical && currentVerticalContainer.scrollTop > 0) {
                                    verticalScrollInterval = setInterval(function() { currentVerticalContainer.scrollTop -= scrollSpeed; if (currentVerticalContainer.scrollTop <= 0) clearInterval(verticalScrollInterval); }, 20);
                                } else if (mouseY > verticalRect.bottom - scrollZoneVertical) {
                                    var maxVerticalScroll = currentVerticalContainer.scrollHeight - currentVerticalContainer.clientHeight;
                                    if (currentVerticalContainer.scrollTop < maxVerticalScroll) verticalScrollInterval = setInterval(function() { currentVerticalContainer.scrollTop += scrollSpeed; if (currentVerticalContainer.scrollTop >= maxVerticalScroll) clearInterval(verticalScrollInterval); }, 20);
                                } else { if (verticalScrollInterval) { clearInterval(verticalScrollInterval); verticalScrollInterval = null; } }
                            } else { if (verticalScrollInterval) { clearInterval(verticalScrollInterval); verticalScrollInterval = null; } }
                        });
                    });

                    dragulaInstance.on('drop', function (el, target, source, sibling) {
                        a(document).off('mousemove.dragula-autoscroll');
                        if (scrollInterval) { clearInterval(scrollInterval); scrollInterval = null; }
                        if (verticalScrollInterval) { clearInterval(verticalScrollInterval); verticalScrollInterval = null; }
                        currentVerticalContainer = null;
                        var sort = [];
                        $("#" + target.id + " > div").each(function () { sort[$(this).index()] = $(this).attr('id'); });
                        var id = el.id;
                        var old_stage = $("#" + source.id).data('status');
                        var new_stage = $("#" + target.id).data('status');
                        var project_id = '{{ $project->id }}';
                        $("#" + source.id).closest('.kb-column').find('.kb-col-count').text($("#" + source.id + " > div").length);
                        $("#" + target.id).closest('.kb-column').find('.kb-col-count').text($("#" + target.id + " > div").length);
                        $.ajax({
                            url: '{{ route('organization.bug.kanban.order') }}',
                            type: 'POST',
                            data: { bug_id: id, sort: sort, status_id: new_stage, old_stage: old_stage, project_id: project_id, "_token": "{{ csrf_token() }}" },

                            success: function (data) {}
                        });
                    });

                    dragulaInstance.on('cancel', function () {
                        a(document).off('mousemove.dragula-autoscroll');
                        if (scrollInterval) { clearInterval(scrollInterval); scrollInterval = null; }
                        if (verticalScrollInterval) { clearInterval(verticalScrollInterval); verticalScrollInterval = null; }
                        currentVerticalContainer = null;
                    });
                })
            }, a.Dragula = new t, a.Dragula.Constructor = t
        }(window.jQuery),
        function (a) { "use strict"; a.Dragula.init() }(window.jQuery);
    </script>
@endpush

@section('action-btn')
    <div class="float-end d-flex gap-2 align-items-center">
        <a href="{{ route('organization.task.bug', $project->id) }}"
           data-bs-toggle="tooltip" title="{{ __('List View') }}"
           class="kb-action-icon-btn">
        
            <svg xmlns="http://www.w3.org/2000/svg" 
                 width="20" height="20" 
                 viewBox="0 0 24 24" 
                 fill="none" 
                 stroke="currentColor" 
                 stroke-width="2" 
                 stroke-linecap="round" 
                 stroke-linejoin="round">
                <line x1="9" y1="6" x2="20" y2="6"></line>
                <line x1="9" y1="12" x2="20" y2="12"></line>
                <line x1="9" y1="18" x2="20" y2="18"></line>
                <circle cx="5" cy="6" r="1"></circle>
                <circle cx="5" cy="12" r="1"></circle>
                <circle cx="5" cy="18" r="1"></circle>
            </svg>
        
        </a>
        <a href="#" data-size="lg" data-url="{{ route('organization.task.bug.create', $project->id) }}"
           data-ajax-popup="true" data-bs-toggle="tooltip" title="{{ __('Create New Bug') }}" class="kb-action-add-btn">
            <i class="ti ti-plus"></i>
            <span>{{ __('New Bug') }}</span>
        </a>
    </div>
@endsection

@section('content')
@include('company.layouts.partials.nav')

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
    --surface-2:    #f4f5fb;
    --border:       #e8eaf2;
    --text-pri:     #12142a;
    --text-muted:   #7c7f9a;
    --shadow-sm:    0 1px 4px rgba(18,20,42,.06);
    --shadow:       0 4px 20px rgba(18,20,42,.08);
    --shadow-lg:    0 8px 32px rgba(18,20,42,.12);
    --radius:       14px;
    --radius-sm:    10px;
    --radius-xs:    6px;
    --col-width:    300px;
}

/* ══ PAGE WRAP ══ */
.kb-page { padding: 16px 0 64px; font-family: 'DM Sans', sans-serif; }

/* ══ ACTION BUTTONS ══ */
.kb-action-icon-btn {
    display: inline-flex; align-items: center; justify-content: center;
    width: 36px; height: 36px; border-radius: var(--radius-xs);
    border: 1px solid var(--border); background: var(--surface);
    color: var(--text-muted); text-decoration: none; transition: all .13s;
}
.kb-action-icon-btn:hover { background: var(--accent-soft); color: var(--accent); border-color: rgba(79,82,255,.22); }
.kb-action-add-btn {
    display: inline-flex; align-items: center; gap: 6px;
    padding: 7px 16px; border-radius: var(--radius-xs);
    background: var(--accent); color: #fff;
    font-size: .84rem; font-weight: 600; font-family: 'DM Sans', sans-serif;
    text-decoration: none; box-shadow: 0 2px 10px var(--accent-glow);
    transition: opacity .13s;
}
.kb-action-add-btn:hover { opacity: .88; color: #fff; }

/* ══ FILTER BAR ══ */
.kb-filter-bar {
    background: var(--surface);
    border: 1px solid var(--border);
    border-radius: var(--radius);
    box-shadow: var(--shadow-sm);
    padding: 12px 16px;
    display: flex; align-items: flex-start; gap: 8px; flex-wrap: wrap;
    margin-bottom: 20px;
}
.kb-search-wrap {
    display: flex; align-items: center;
    flex: 1; min-width: 180px; max-width: 260px;
    background: var(--surface-2);
    border: 1px solid var(--border); border-radius: 50px;
    padding: 6px 12px; gap: 7px;
    transition: border-color .15s, box-shadow .15s;
}
.kb-search-wrap:focus-within { border-color: var(--accent); box-shadow: 0 0 0 3px var(--accent-glow); background: var(--surface); }
.kb-search-wrap i { color: var(--text-muted); font-size: .82rem; flex-shrink: 0; }
.kb-search-input { border: none; background: transparent; font-family: 'DM Sans', sans-serif; font-size: .83rem; color: var(--text-pri); outline: none; width: 100%; }
.kb-search-input::placeholder { color: var(--text-muted); }

/* Multi-select dropdown */
.br-ms-wrap { position: relative; min-width: 130px; }
.br-ms-trigger {
    display: flex; align-items: center; gap: 6px;
    background: var(--surface-2); border: 1px solid var(--border); border-radius: 50px;
    padding: 6px 12px; font-size: .81rem; font-family: 'DM Sans', sans-serif;
    color: var(--text-pri); cursor: pointer; transition: all .15s; user-select: none; white-space: nowrap;
}
.br-ms-trigger:hover { border-color: #c5c9df; background: var(--surface); }
.br-ms-trigger.open { border-color: var(--accent); box-shadow: 0 0 0 3px var(--accent-glow); background: var(--surface); }
.br-ms-trigger i.chevron { margin-left: auto; font-size: .65rem; color: var(--text-muted); transition: transform .15s; }
.br-ms-trigger.open i.chevron { transform: rotate(180deg); }
.br-ms-badge { background: var(--accent); color: #fff; border-radius: 50%; width: 17px; height: 17px; display: inline-flex; align-items: center; justify-content: center; font-size: .62rem; font-weight: 700; flex-shrink: 0; }
.br-ms-dropdown {
    position: absolute; top: calc(100% + 6px); left: 0; z-index: 1050;
    background: var(--surface); border: 1px solid var(--border); border-radius: var(--radius-sm);
    box-shadow: var(--shadow-lg); min-width: 200px;
    overflow: hidden; display: none; flex-direction: column;
}
.br-ms-dropdown.open { display: flex; }
.br-ms-search-wrap { padding: 9px 11px; border-bottom: 1px solid var(--border); display: flex; align-items: center; gap: 6px; }
.br-ms-search-wrap i { color: var(--text-muted); font-size: .75rem; }
.br-ms-search { border: none; background: transparent; outline: none; font-family: 'DM Sans', sans-serif; font-size: .8rem; color: var(--text-pri); width: 100%; }
.br-ms-list { max-height: 210px; overflow-y: auto; padding: 5px 0; }
.br-ms-item { display: flex; align-items: center; gap: 8px; padding: 7px 12px; cursor: pointer; transition: background .1s; font-size: .82rem; color: var(--text-pri); }
.br-ms-item:hover { background: var(--surface-2); }
.br-ms-checkbox { width: 15px; height: 15px; border-radius: 4px; border: 1.5px solid var(--border); display: flex; align-items: center; justify-content: center; flex-shrink: 0; transition: all .12s; background: var(--surface); }
.br-ms-item.selected .br-ms-checkbox { background: var(--accent); border-color: var(--accent); }
.br-ms-item.selected .br-ms-checkbox::after { content: ''; width: 8px; height: 4px; border-left: 2px solid #fff; border-bottom: 2px solid #fff; transform: rotate(-45deg) translate(1px, -1px); display: block; }
.br-ms-dot { width: 7px; height: 7px; border-radius: 50%; flex-shrink: 0; }
.br-ms-footer { padding: 7px 11px; border-top: 1px solid var(--border); display: flex; align-items: center; justify-content: space-between; gap: 8px; }
.br-ms-clear-btn { font-size: .76rem; color: var(--text-muted); cursor: pointer; background: none; border: none; font-family: 'DM Sans', sans-serif; padding: 0; transition: color .12s; }
.br-ms-clear-btn:hover { color: var(--danger); }
.br-ms-apply-btn { font-size: .76rem; color: #fff; cursor: pointer; background: var(--accent); border: none; padding: 4px 13px; border-radius: 50px; font-family: 'DM Sans', sans-serif; font-weight: 600; transition: opacity .12s; }
.br-ms-apply-btn:hover { opacity: .88; }

.kb-reset-btn {
    display: inline-flex; align-items: center; gap: 5px;
    padding: 6px 14px; border-radius: 50px;
    border: 1px solid var(--border); background: var(--surface);
    font-size: .80rem; font-family: 'DM Sans', sans-serif;
    color: var(--text-muted); cursor: pointer; transition: all .13s;
}
.kb-reset-btn:hover { border-color: var(--danger); color: var(--danger); }
.kb-filter-count {
    font-size: .78rem; color: var(--text-muted); padding: 4px 12px;
    background: var(--surface-2); border: 1px solid var(--border);
    border-radius: 50px; white-space: nowrap; align-self: center;
}
.kb-filter-count strong { color: var(--accent); font-weight: 700; }
.kb-active-chips { display: flex; flex-wrap: wrap; gap: 5px; width: 100%; margin-top: 6px; }
.kb-chip {
    display: inline-flex; align-items: center; gap: 4px;
    padding: 3px 9px 3px 7px; border-radius: 50px; font-size: .72rem; font-weight: 500;
    background: var(--accent-soft); border: 1px solid rgba(79,82,255,.2); color: var(--accent);
}
.kb-chip-remove { cursor: pointer; opacity: .55; font-size: .65rem; margin-left: 2px; }
.kb-chip-remove:hover { opacity: 1; }

/* ══ KANBAN BOARD SCROLL WRAPPER ══ */
.kb-board-outer {
    overflow-x: auto;
    overflow-y: visible;
    padding-bottom: 12px;
    /* Custom scrollbar */
    scrollbar-width: thin;
    scrollbar-color: var(--border) transparent;
}
.kb-board-outer::-webkit-scrollbar { height: 6px; }
.kb-board-outer::-webkit-scrollbar-track { background: transparent; }
.kb-board-outer::-webkit-scrollbar-thumb { background: var(--border); border-radius: 3px; }
.kb-board-outer::-webkit-scrollbar-thumb:hover { background: #c5c9df; }

.kb-board-inner {
    display: flex;
    gap: 16px;
    align-items: flex-start;
    min-width: max-content;
    padding: 4px 2px 8px;
}

/* ══ KANBAN COLUMN ══ */
.kb-column {
    width: var(--col-width);
    flex-shrink: 0;
    display: flex;
    flex-direction: column;
}

.kb-col-wrap {
    background: var(--surface-2);
    border: 1px solid var(--border);
    border-radius: var(--radius);
    display: flex;
    flex-direction: column;
    overflow: hidden;
}

.kb-col-header {
    padding: 14px 16px 12px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    border-bottom: 1px solid var(--border);
    background: var(--surface);
    flex-shrink: 0;
}

.kb-col-header-left {
    display: flex;
    align-items: center;
    gap: 10px;
}

.kb-col-dot {
    width: 9px; height: 9px;
    border-radius: 50%;
    flex-shrink: 0;
}

.kb-col-title {
    font-family: 'Syne', sans-serif;
    font-size: .82rem;
    font-weight: 700;
    color: var(--text-pri);
    margin: 0;
    letter-spacing: .01em;
}

.kb-col-count {
    display: inline-flex; align-items: center; justify-content: center;
    min-width: 22px; height: 22px;
    background: var(--accent-soft);
    color: var(--accent);
    border-radius: 50px;
    font-size: .68rem;
    font-weight: 700;
    padding: 0 6px;
    border: 1px solid rgba(79,82,255,.15);
}

.kb-col-body {
    padding: 10px;
    overflow-y: auto;
    max-height: calc(100vh - 280px);
    min-height: 80px;
    scrollbar-width: thin;
    scrollbar-color: var(--border) transparent;
}
.kb-col-body::-webkit-scrollbar { width: 4px; }
.kb-col-body::-webkit-scrollbar-track { background: transparent; }
.kb-col-body::-webkit-scrollbar-thumb { background: var(--border); border-radius: 2px; }

/* Empty column drop zone */
.kb-col-body:empty::after,
.kb-col-empty-hint {
    display: flex; align-items: center; justify-content: center;
    min-height: 80px;
    color: var(--text-muted);
    font-size: .78rem;
    border: 2px dashed var(--border);
    border-radius: var(--radius-sm);
    margin: 4px;
}

/* ══ KANBAN CARD ══ */
.kb-card {
    background: var(--surface);
    border: 1px solid var(--border);
    border-radius: var(--radius-sm);
    margin-bottom: 10px;
    cursor: grab;
    transition: box-shadow .15s, transform .15s, border-color .15s;
    position: relative;
    overflow: hidden;
}
.kb-card:last-child { margin-bottom: 0; }
.kb-card:hover {
    box-shadow: var(--shadow);
    border-color: rgba(79,82,255,.18);
    transform: translateY(-1px);
}
.kb-card:active { cursor: grabbing; transform: rotate(1deg) scale(1.01); }
.kb-card.gu-mirror { opacity: .96; box-shadow: var(--shadow-lg); transform: rotate(1.5deg); }
.kb-card.gu-transit { opacity: .35; }

/* Card accent line top */
.kb-card::before {
    content: '';
    position: absolute; top: 0; left: 0; right: 0;
    height: 3px;
    background: var(--accent);
    opacity: 0;
    transition: opacity .15s;
    border-radius: var(--radius-sm) var(--radius-sm) 0 0;
}
.kb-card:hover::before { opacity: 1; }

.kb-card-header {
    padding: 12px 12px 8px;
    display: flex; align-items: flex-start; justify-content: space-between; gap: 8px;
}

.kb-card-uid {
    display: inline-flex; align-items: center; gap: 4px;
    font-family: 'Syne', sans-serif;
    font-size: .70rem; font-weight: 700;
    color: var(--accent);
    background: var(--accent-soft);
    border: 1px solid rgba(79,82,255,.15);
    border-radius: var(--radius-xs);
    padding: 2px 8px;
    text-decoration: none;
    transition: all .13s;
    cursor: pointer;
}
.kb-card-uid:hover { background: var(--accent); color: #fff; }

.kb-card-menu-btn {
    width: 24px; height: 24px; border-radius: var(--radius-xs);
    border: none; background: transparent;
    display: flex; align-items: center; justify-content: center;
    cursor: pointer; color: var(--text-muted); flex-shrink: 0;
    transition: all .12s;
}
.kb-card-menu-btn:hover { background: var(--surface-2); color: var(--text-pri); }

.kb-card-title {
    padding: 0 12px 10px;
    font-size: .84rem;
    font-weight: 500;
    color: var(--text-pri);
    line-height: 1.45;
    margin: 0;
}

.kb-card-badges {
    padding: 0 12px 10px;
    display: flex; flex-wrap: wrap; gap: 5px; align-items: center;
}

/* Priority badges */
.kb-pri {
    display: inline-flex; align-items: center; gap: 4px;
    padding: 2px 8px; border-radius: var(--radius-xs);
    font-size: .69rem; font-weight: 700; letter-spacing: .02em;
    text-transform: capitalize;
}
.kb-pri::before { content: ''; width: 5px; height: 5px; border-radius: 50%; display: inline-block; }
.kb-pri-critical { background: #fff0f3; color: #c0003a; }.kb-pri-critical::before { background: #c0003a; }
.kb-pri-high     { background: #fff1ee; color: #c94f1f; }.kb-pri-high::before     { background: #c94f1f; }
.kb-pri-medium   { background: var(--warn-soft); color: #b56b00; }.kb-pri-medium::before { background: var(--warn); }
.kb-pri-low      { background: var(--success-soft); color: #007a58; }.kb-pri-low::before    { background: var(--success); }

.kb-card-divider {
    height: 1px; background: var(--border); margin: 0 12px 10px;
}

.kb-card-dates {
    padding: 0 12px 10px;
    display: flex; align-items: center; justify-content: space-between;
    font-size: .73rem; color: var(--text-muted);
}
.kb-card-dates span {
    display: inline-flex; align-items: center; gap: 4px;
}
.kb-card-dates i { font-size: .68rem; }

.kb-card-footer {
    padding: 8px 12px 12px;
    display: flex; align-items: center; justify-content: space-between;
}

.kb-avatar-stack { display: flex; align-items: center; }
.kb-avatar-stack img {
    width: 26px; height: 26px;
    border-radius: 50%;
    border: 2px solid var(--surface);
    object-fit: cover;
    margin-right: -6px;
    transition: margin .12s;
}
.kb-avatar-stack:hover img { margin-right: 2px; }

.kb-file-badge {
    display: inline-flex; align-items: center; gap: 4px;
    font-size: .72rem; color: var(--text-muted);
    padding: 3px 8px;
    background: var(--surface-2);
    border: 1px solid var(--border);
    border-radius: var(--radius-xs);
    cursor: pointer;
    transition: all .12s;
}
.kb-file-badge:hover { background: var(--accent-soft); color: var(--accent); border-color: rgba(79,82,255,.2); }
.kb-file-badge i { font-size: .68rem; }

/* Hidden states */
.kanban-card-hidden { display: none !important; }

/* ══ BUG DETAIL MODAL ══ */
.bug-modal-overlay {
    position: fixed; inset: 0; z-index: 99999;
    background: rgba(18,20,42,.5);
    display: flex; align-items: center; justify-content: center;
    padding: 20px; opacity: 0; pointer-events: none; transition: opacity .2s;
}
.bug-modal-overlay.show { opacity: 1; pointer-events: all; }
.bug-modal-box {
    background: var(--surface); border-radius: var(--radius);
    box-shadow: 0 24px 64px rgba(18,20,42,.28);
    width: 100%; max-width: 760px; max-height: 88vh; overflow: hidden;
    display: flex; flex-direction: column;
    transform: translateY(22px) scale(.98); transition: transform .22s, opacity .22s;
}
.bug-modal-overlay.show .bug-modal-box { transform: translateY(0) scale(1); }
.bug-modal-header {
    padding: 16px 22px 12px; border-bottom: 1px solid var(--border);
    display: flex; align-items: center; justify-content: space-between; flex-shrink: 0;
    background: var(--surface);
}
.bug-modal-title { font-family: 'Syne', sans-serif; font-size: .95rem; font-weight: 700; color: var(--text-pri); margin: 0; }
.bug-modal-close {
    width: 28px; height: 28px; border-radius: var(--radius-xs);
    border: 1px solid var(--border); background: var(--surface);
    display: flex; align-items: center; justify-content: center;
    cursor: pointer; color: var(--text-muted); font-size: .75rem; transition: all .12s;
    flex-shrink: 0;
}
.bug-modal-close:hover { background: var(--danger-soft); border-color: rgba(255,79,106,.2); color: var(--danger); }
.bug-modal-body { overflow-y: auto; flex: 1; }

/* bvm (bug view modal) inner styles */
.bvm-body{font-family:'DM Sans',sans-serif;color:var(--text-pri)}
.bvm-hero{background:var(--surface);border-bottom:1px solid var(--border);padding:18px 24px 14px}
.bvm-hero-title{font-family:'Syne',sans-serif;font-size:.95rem;font-weight:700;margin:0 0 9px;display:flex;align-items:center;gap:8px}
.bvm-hero-title::before{content:'';width:8px;height:8px;border-radius:50%;background:var(--accent);flex-shrink:0;box-shadow:0 0 0 3px var(--accent-glow)}
.bvm-hero-meta{display:flex;flex-wrap:wrap;gap:6px;align-items:center}
.bvm-pri-badge{display:inline-flex;align-items:center;gap:5px;padding:3px 10px;border-radius:50px;font-size:.72rem;font-weight:600;border:1px solid transparent}
.bvm-pri-badge::before{content:'';width:5px;height:5px;border-radius:50%;display:inline-block}
.bvm-pri-critical{background:var(--danger-soft);color:var(--danger);border-color:rgba(255,79,106,.18)}.bvm-pri-critical::before{background:var(--danger)}
.bvm-pri-high{background:#fff4f0;color:#e05a2b;border-color:rgba(224,90,43,.18)}.bvm-pri-high::before{background:#e05a2b}
.bvm-pri-medium{background:var(--warn-soft);color:var(--warn);border-color:rgba(255,157,10,.18)}.bvm-pri-medium::before{background:var(--warn)}
.bvm-pri-low{background:var(--success-soft);color:var(--success);border-color:rgba(0,196,140,.18)}.bvm-pri-low::before{background:var(--success)}
.bvm-pri-default{background:var(--surface-2);color:var(--text-muted);border-color:var(--border)}.bvm-pri-default::before{background:var(--text-muted)}
.bvm-assign-chip{display:inline-flex;align-items:center;gap:6px;padding:3px 10px 3px 4px;background:var(--accent-soft);border:1px solid rgba(79,82,255,.18);border-radius:50px;color:var(--accent);font-size:.75rem;font-weight:500}
.bvm-assign-chip .av{width:20px;height:20px;border-radius:50%;background:var(--accent);display:flex;align-items:center;justify-content:center;font-size:.6rem;font-weight:700;color:#fff}
.bvm-date-chip{display:inline-flex;align-items:center;gap:5px;font-size:.73rem;color:var(--text-muted);padding:3px 9px;background:var(--surface-2);border:1px solid var(--border);border-radius:50px}
.bvm-meta-grid{display:grid;grid-template-columns:1fr 1fr}
.bvm-meta-cell{padding:12px 24px;border-right:1px solid var(--border);border-bottom:1px solid var(--border)}
.bvm-meta-cell:nth-child(even){border-right:none}
.bvm-meta-cell:nth-last-child(-n+2){border-bottom:none}
.bvm-meta-label{font-size:.68rem;font-weight:700;letter-spacing:.1em;text-transform:uppercase;color:var(--text-muted);margin-bottom:4px;display:flex;align-items:center;gap:5px}
.bvm-meta-value{font-size:.875rem;color:var(--text-pri);font-weight:500;margin:0}
.bvm-desc-cell{padding:12px 24px;border-bottom:1px solid var(--border)}
.bvm-desc-text{font-size:.875rem;color:var(--text-pri);line-height:1.65;margin:0}
.bvm-tabs-bar{display:flex;border-bottom:1px solid var(--border);background:var(--surface);padding:0 24px;list-style:none;margin:0}
.bvm-tab-btn{display:inline-flex;align-items:center;gap:7px;padding:11px 16px;font-family:'DM Sans',sans-serif;font-size:.83rem;font-weight:500;color:var(--text-muted);border:none;background:transparent;border-bottom:2px solid transparent;margin-bottom:-1px;cursor:pointer;transition:color .15s,border-color .15s;white-space:nowrap}
.bvm-tab-btn.active,.bvm-tab-btn:hover{color:var(--accent);border-bottom-color:var(--accent)}
.bvm-tab-count{background:var(--surface-2);color:var(--text-muted);border-radius:50px;padding:1px 7px;font-size:.68rem;font-weight:700}
.bvm-tab-btn.active .bvm-tab-count{background:var(--accent);color:#fff}
.bvm-tab-content{background:var(--surface-2);padding:16px 24px 20px;min-height:160px}
.bvm-tab-pane{display:none}
.bvm-tab-pane.active{display:block}
.bvm-comment-form-wrap{display:flex;align-items:center;gap:10px;background:var(--surface);border:1px solid var(--border);border-radius:var(--radius);padding:8px 8px 8px 14px;box-shadow:var(--shadow-sm);margin-bottom:14px}
.bvm-comment-form-wrap i{color:var(--text-muted);font-size:.85rem;flex-shrink:0}
.bvm-comment-form-wrap input{flex:1;border:none;background:transparent;font-family:'DM Sans',sans-serif;font-size:.875rem;color:var(--text-pri);outline:none}
.bvm-comment-form-wrap input::placeholder{color:var(--text-muted)}
.bvm-send-btn{width:34px;height:34px;border-radius:var(--radius-xs);background:var(--accent);border:none;color:#fff;display:flex;align-items:center;justify-content:center;cursor:pointer;flex-shrink:0;box-shadow:0 2px 8px var(--accent-glow)}
.bvm-send-btn i{font-size:.78rem;color:#fff!important}
.bvm-comment-item{display:flex;align-items:flex-start;gap:10px;padding:12px 14px;background:var(--surface);border:1px solid var(--border);border-radius:var(--radius-sm);margin-bottom:8px}
.bvm-comment-avatar{width:32px;height:32px;border-radius:50%;object-fit:cover;flex-shrink:0;border:2px solid var(--border)}
.bvm-comment-body{flex:1;min-width:0}
.bvm-comment-name{font-size:.82rem;font-weight:600;color:var(--text-pri);margin-bottom:3px}
.bvm-comment-text{font-size:.84rem;color:#3a3d5c;line-height:1.55;word-break:break-word}
.bvm-comment-time{display:flex;align-items:center;gap:4px;font-size:.72rem;color:var(--accent);margin-top:4px}
.bvm-dot-btn{background:transparent;border:none;padding:4px 6px;border-radius:var(--radius-xs);color:var(--text-muted);cursor:pointer;line-height:1}
.bvm-dot-btn:hover{background:var(--surface-2)}
.bvm-files-header{display:flex;align-items:center;justify-content:space-between;margin-bottom:14px}
.bvm-files-title{font-family:'Syne',sans-serif;font-size:.85rem;font-weight:700;color:var(--text-pri);display:flex;align-items:center;gap:7px}
.bvm-files-title i{color:var(--accent)}
.bvm-add-file-btn{display:inline-flex;align-items:center;gap:6px;padding:6px 14px;border-radius:var(--radius-xs);background:var(--accent-soft);color:var(--accent);border:1px solid rgba(79,82,255,.2);font-size:.78rem;font-weight:600;cursor:pointer;font-family:'DM Sans',sans-serif}
.bvm-add-file-btn:hover{background:var(--accent);color:#fff}
.bvm-upload-area{background:var(--surface);border:2px dashed var(--border);border-radius:var(--radius-sm);padding:14px;margin-bottom:14px;display:none}
.bvm-upload-area.open{display:block}
.bvm-upload-row{display:flex;align-items:center;gap:10px;flex-wrap:wrap}
.bvm-upload-area .form-control{flex:1;min-width:160px;border:1px solid var(--border);border-radius:var(--radius-xs);font-size:.83rem;background:var(--surface-2);padding:6px 10px;outline:none}
.bvm-upload-confirm{width:36px;height:36px;border-radius:var(--radius-xs);background:var(--success);border:none;color:#fff;display:flex;align-items:center;justify-content:center;cursor:pointer;flex-shrink:0}
.bvm-preview-wrap{margin-top:10px;text-align:center;background:var(--surface-2);border-radius:var(--radius-xs);padding:8px;border:1px solid var(--border);display:none}
.bvm-preview-wrap.show{display:block}
.bvm-file-item{display:flex;align-items:center;gap:12px;padding:11px 14px;background:var(--surface);border:1px solid var(--border);border-radius:var(--radius-sm);margin-bottom:8px}
.bvm-file-item:hover{box-shadow:var(--shadow-sm)}
.bvm-file-icon{width:38px;height:38px;border-radius:var(--radius-xs);background:var(--accent-soft);display:flex;align-items:center;justify-content:center;flex-shrink:0;overflow:hidden}
.bvm-file-icon img.thumb{width:38px;height:38px;object-fit:cover}
.bvm-file-icon i{color:var(--accent);font-size:.9rem}
.bvm-file-info{flex:1;min-width:0}
.bvm-file-name{font-size:.84rem;font-weight:600;color:var(--text-pri);margin:0 0 2px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap}
.bvm-file-meta{font-size:.72rem;color:var(--text-muted);display:flex;align-items:center;gap:6px}
.bvm-empty{text-align:center;padding:28px 16px;color:var(--text-muted);font-size:.84rem}
.bvm-empty i{font-size:1.6rem;opacity:.22;display:block;margin-bottom:8px}
.dropdown-menu{z-index:999999!important}
.swal-zindex-fix{z-index:9999999!important}
.swal2-container{z-index:9999999!important}
</style>

<div class="kb-page">

    {{-- ═══ FILTER BAR ═══ --}}
    <div class="kb-filter-bar">

        {{-- Search --}}
        <div class="kb-search-wrap">
            <i class="ti ti-search"></i>
            <input type="text" id="kb-search" class="kb-search-input" placeholder="Search title, ID…" autocomplete="off">
        </div>

        {{-- Assignee --}}
        <div class="br-ms-wrap" id="kb-ms-assignee-wrap">
            <div class="br-ms-trigger" id="kb-ms-assignee-trigger">
                <i class="ti ti-user" style="font-size:.78rem;"></i>
                <span>Assigned To</span>
                <span class="br-ms-badge" id="kb-ms-assignee-count" style="display:none;"></span>
                <i class="ti ti-chevron-down chevron"></i>
            </div>
            <div class="br-ms-dropdown" id="kb-ms-assignee-dropdown">
                <div class="br-ms-search-wrap">
                    <i class="ti ti-search"></i>
                    <input type="text" class="br-ms-search" placeholder="Search user…" id="kb-ms-assignee-search">
                </div>
                <div class="br-ms-list" id="kb-ms-assignee-list">
                    @php
                        $allBugsFlat = collect();
                        foreach ($bugStatus as $bsObj) {
                            $allBugsFlat = $allBugsFlat->merge($bsObj->bugs($project->id));
                        }
                        $kanbanAssignees = $allBugsFlat->flatMap(function($b) {
                            return \App\Models\User::whereIn('id', explode(',', $b->assign_to))->get();
                        })->unique('id');
                    @endphp
                    @foreach($kanbanAssignees as $usr)
                    <div class="br-ms-item" data-value="{{ $usr->id }}" data-label="{{ $usr->name }}">
                        <div class="br-ms-checkbox"></div>
                        <div style="width:20px;height:20px;border-radius:50%;background:linear-gradient(135deg,#4f52ff,#8486ff);display:flex;align-items:center;justify-content:center;color:#fff;font-size:.58rem;font-weight:700;flex-shrink:0;">
                            {{ strtoupper(substr($usr->name,0,1)) }}
                        </div>
                        <span>{{ $usr->name }}</span>
                    </div>
                    @endforeach
                </div>
                <div class="br-ms-footer">
                    <button class="br-ms-clear-btn" data-target="kb-assignee">Clear</button>
                    <button class="br-ms-apply-btn" data-target="kb-assignee">Apply</button>
                </div>
            </div>
        </div>

        {{-- Priority --}}
        <div class="br-ms-wrap" id="kb-ms-priority-wrap">
            <div class="br-ms-trigger" id="kb-ms-priority-trigger">
                <i class="ti ti-flag" style="font-size:.78rem;"></i>
                <span>Priority</span>
                <span class="br-ms-badge" id="kb-ms-priority-count" style="display:none;"></span>
                <i class="ti ti-chevron-down chevron"></i>
            </div>
            <div class="br-ms-dropdown" id="kb-ms-priority-dropdown">
                <div class="br-ms-list" id="kb-ms-priority-list">
                    <div class="br-ms-item" data-value="critical" data-label="Critical"><div class="br-ms-checkbox"></div><span class="br-ms-dot" style="background:#c0003a;"></span>Critical</div>
                    <div class="br-ms-item" data-value="high"     data-label="High"    ><div class="br-ms-checkbox"></div><span class="br-ms-dot" style="background:#c94f1f;"></span>High</div>
                    <div class="br-ms-item" data-value="medium"   data-label="Medium"  ><div class="br-ms-checkbox"></div><span class="br-ms-dot" style="background:#ff9d0a;"></span>Medium</div>
                    <div class="br-ms-item" data-value="low"      data-label="Low"     ><div class="br-ms-checkbox"></div><span class="br-ms-dot" style="background:#00c48c;"></span>Low</div>
                </div>
                <div class="br-ms-footer">
                    <button class="br-ms-clear-btn" data-target="kb-priority">Clear</button>
                    <button class="br-ms-apply-btn" data-target="kb-priority">Apply</button>
                </div>
            </div>
        </div>

        {{-- Status --}}
        <div class="br-ms-wrap" id="kb-ms-status-wrap">
            <div class="br-ms-trigger" id="kb-ms-status-trigger">
                <i class="ti ti-circle-check" style="font-size:.78rem;"></i>
                <span>Status</span>
                <span class="br-ms-badge" id="kb-ms-status-count" style="display:none;"></span>
                <i class="ti ti-chevron-down chevron"></i>
            </div>
            <div class="br-ms-dropdown" id="kb-ms-status-dropdown">
                <div class="br-ms-list" id="kb-ms-status-list">
                    @foreach($bugStatus as $bs)
                        @php
                            $tl  = strtolower($bs->title);
                            $dot = match(true) {
                                str_contains($tl, 'open')     => '#ff9d0a',
                                str_contains($tl, 'progress') => '#4f52ff',
                                str_contains($tl, 'resolv')   => '#00c48c',
                                str_contains($tl, 'close')    => '#aaa',
                                default                       => '#8884ff',
                            };
                        @endphp
                        <div class="br-ms-item" data-value="{{ $bs->id }}" data-label="{{ $bs->title }}">
                            <div class="br-ms-checkbox"></div>
                            <span class="br-ms-dot" style="background:{{ $dot }};"></span>
                            {{ $bs->title }}
                        </div>
                    @endforeach
                </div>
                <div class="br-ms-footer">
                    <button class="br-ms-clear-btn" data-target="kb-status">Clear</button>
                    <button class="br-ms-apply-btn" data-target="kb-status">Apply</button>
                </div>
            </div>
        </div>

        <span style="flex:1;"></span>
        <span class="kb-filter-count">Showing <strong id="kb-visible-count">{{ $allBugsFlat->count() }}</strong> bugs</span>
        <button class="kb-reset-btn" id="kb-reset-btn" type="button">
            <i class="ti ti-refresh" style="font-size:.78rem;"></i> Reset
        </button>

        <div class="kb-active-chips" id="kb-active-chips" style="display:none;"></div>
    </div>

    {{-- ═══ KANBAN BOARD ═══ --}}
    @php
        $json = [];
        foreach ($bugStatus as $status) { $json[] = 'task-list-' . $status->id; }

        /* Column accent colors — cycle through palette */
        $colColors = ['#4f52ff','#00c48c','#ff9d0a','#ff4f6a','#8b5cf6','#0ea5e9','#f97316','#10b981'];
    @endphp

    <div class="kb-board-outer">
        <div class="kb-board-inner" data-containers='{{ json_encode($json) }}' data-plugin="dragula">
            @foreach ($bugStatus as $si => $status)
                @php
                    $bugs      = $status->bugs($project->id);
                    $accentCol = $colColors[$si % count($colColors)];
                @endphp
                <div class="kb-column" data-status-id="{{ $status->id }}" data-status-title="{{ strtolower($status->title) }}">
                    <div class="kb-col-wrap" style="border-top: 3px solid {{ $accentCol }};">

                        {{-- Column Header --}}
                        <div class="kb-col-header">
                            <div class="kb-col-header-left">
                                <span class="kb-col-dot" style="background:{{ $accentCol }};"></span>
                                <h4 class="kb-col-title">{{ $status->title }}</h4>
                            </div>
                            <span class="kb-col-count" id="col-count-{{ $status->id }}" style="background:{{ $accentCol }}18;color:{{ $accentCol }};border-color:{{ $accentCol }}30;">
                                {{ count($bugs) }}
                            </span>
                        </div>

                        {{-- Cards --}}
                        <div class="kb-col-body main_heighttrack"
                             id="task-list-{{ $status->id }}"
                             data-id="{{ $status->id }}"
                             data-status="{{ $status->id }}">

                            @forelse ($bugs as $bug)
                                @php
                                    $assignedIds   = explode(',', $bug->assign_to);
                                    $assignedUsers = \App\Models\User::whereIn('id', $assignedIds)->get();
                                    $bugPriority   = strtolower($bug->priority ?? '');
                                    $priClass      = match($bugPriority) {
                                        'critical' => 'kb-pri-critical',
                                        'high'     => 'kb-pri-high',
                                        'medium'   => 'kb-pri-medium',
                                        'low'      => 'kb-pri-low',
                                        default    => '',
                                    };
                                    $cardFileCount = $bug->bugFiles ? $bug->bugFiles->count() : 0;
                                @endphp

                                <div class="kb-card"
                                     id="{{ $bug->id }}"
                                     data-title="{{ strtolower($bug->title) }}"
                                     data-bugid="{{ strtolower($bug->task_uid) }}"
                                     data-priority="{{ $bugPriority }}"
                                     data-assignees="{{ implode(',', $assignedIds) }}"
                                     data-status="{{ $status->id }}">

                                    {{-- Card Header --}}
                                    <div class="kb-card-header">
                                        <a href="#!" class="kb-card-uid open-bug-modal"
                                           data-bug-id="{{ $bug->id }}"
                                           data-project-id="{{ $project->id }}">
                                            <i class="ti ti-bug" style="font-size:.65rem;"></i>
                                            {{ $bug->task_uid }}
                                        </a>
                                        @if (Gate::check('edit bug report') || Gate::check('delete bug report'))
                                        <div class="btn-group">
                                            <button type="button" class="kb-card-menu-btn"
                                                    data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                <i class="ti ti-dots-vertical" style="font-size:.78rem;"></i>
                                            </button>
                                            <div class="dropdown-menu icon-dropdown dropdown-menu-end">
                                                <a href="#!" data-size="lg"
                                                   data-url="{{ route('organization.task.bug.edit', [$project->id, $bug->id]) }}"
                                                   data-ajax-popup="true" class="dropdown-item">
                                                    <i class="ti ti-pencil"></i> <span>{{ __('Edit') }}</span>
                                                </a>
                                                {!! Form::open(['method' => 'DELETE', 'route' => ['organization.task.bug.destroy', [$project->id, $bug->id]]]) !!}
                                                <a href="#!" class="dropdown-item bs-pass-para">
                                                    <i class="ti ti-trash"></i> <span>{{ __('Delete') }}</span>
                                                </a>
                                                {!! Form::close() !!}
                                            </div>
                                        </div>
                                        @endif
                                    </div>

                                    {{-- Title --}}
                                    <p class="kb-card-title">{{ $bug->title }}</p>

                                    {{-- Priority badge --}}
                                    @if($bugPriority)
                                    <div class="kb-card-badges">
                                        <span class="kb-pri {{ $priClass }}">{{ ucfirst($bugPriority) }}</span>
                                    </div>
                                    @endif

                                    {{-- Dates --}}
                                    <div class="kb-card-dates">
                                        <span>
                                            <i class="ti ti-calendar"></i>
                                            {{ \Auth::user()->dateFormat($bug->start_date) }}
                                        </span>
                                        <span>
                                            <i class="ti ti-calendar-due"></i>
                                            {{ \Auth::user()->dateFormat($bug->due_date) }}
                                        </span>
                                    </div>

                                    {{-- Footer --}}
                                    <div class="kb-card-footer">
                                        <div class="kb-avatar-stack">
                                            @foreach ($assignedUsers as $user)
                                                <img
                                                    @if (($user->employee->gender ?? null) === GENDER_MALE)
                                                        src="{{ asset('assets/assestsnew/menimg.png') }}"
                                                    @elseif (($user->employee->gender ?? null) === GENDER_FEMALE)
                                                        src="{{ asset('assets/assestsnew/femaile-report.svg') }}"
                                                    @else
                                                        src="{{ $user->avatar ? asset('/storage/uploads/avatar/' . $user->avatar) : asset('assets/assestsnew/profile.png') }}"
                                                    @endif
                                                    title="{{ $user->name }}"
                                                    alt="{{ $user->name }}">
                                            @endforeach
                                        </div>

                                        @if($cardFileCount > 0)
                                        <span class="kb-file-badge open-bug-modal"
                                              data-bug-id="{{ $bug->id }}"
                                              data-project-id="{{ $project->id }}"
                                              data-open-tab="files">
                                            <i class="ti ti-paperclip"></i> {{ $cardFileCount }}
                                        </span>
                                        @endif
                                    </div>

                                </div>
                            @empty
                                <div class="kb-col-empty-hint" style="display:flex;">
                                    <span style="display:flex;align-items:center;gap:6px;font-size:.75rem;">
                                        <i class="ti ti-inbox" style="font-size:.9rem;opacity:.4;"></i>
                                        No bugs here
                                    </span>
                                </div>
                            @endforelse

                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

</div>

{{-- ═══ BUG DETAIL MODAL ═══ --}}
<div class="bug-modal-overlay" id="bug-detail-modal">
    <div class="bug-modal-box">
        <div class="bug-modal-header">
            <h5 class="bug-modal-title" id="bm-title">Bug Details</h5>
            <button class="bug-modal-close" id="bug-modal-close-btn">
                <i class="ti ti-x"></i>
            </button>
        </div>
        <div class="bug-modal-body" id="bm-body">
            <div style="text-align:center;padding:50px;color:var(--text-muted);">
                <i class="ti ti-loader" style="font-size:2rem;"></i>
            </div>
        </div>
    </div>
</div>

<script>
(function () {

    /* ══ FILTER STATE ══ */
    const msState = {
        'kb-assignee': new Set(),
        'kb-priority':  new Set(),
        'kb-status':    new Set(),
    };

    ['kb-assignee', 'kb-priority', 'kb-status'].forEach(key => {
        const short      = key.replace('kb-', '');
        const trigger    = document.getElementById('kb-ms-' + short + '-trigger');
        const dropdown   = document.getElementById('kb-ms-' + short + '-dropdown');
        const countBadge = document.getElementById('kb-ms-' + short + '-count');
        const list       = document.getElementById('kb-ms-' + short + '-list');
        const searchInput= document.getElementById('kb-ms-' + short + '-search');

        if (!trigger) return;

        trigger.addEventListener('click', e => {
            e.stopPropagation();
            const isOpen = dropdown.classList.contains('open');
            closeAllKbDropdowns();
            if (!isOpen) {
                dropdown.classList.add('open');
                trigger.classList.add('open');
                if (searchInput) searchInput.focus();
            }
        });

        if (searchInput) {
            searchInput.addEventListener('input', () => {
                const q = searchInput.value.toLowerCase();
                list.querySelectorAll('.br-ms-item').forEach(item => {
                    item.style.display = (item.dataset.label || '').toLowerCase().includes(q) ? '' : 'none';
                });
            });
        }

        list.addEventListener('click', e => {
            const item = e.target.closest('.br-ms-item');
            if (!item) return;
            const val = item.dataset.value;
            if (msState[key].has(val)) { msState[key].delete(val); item.classList.remove('selected'); }
            else { msState[key].add(val); item.classList.add('selected'); }
            updateKbBadge(key, countBadge);
        });

        const footer = dropdown.querySelector('.br-ms-footer');
        if (footer) {
            footer.querySelector('.br-ms-clear-btn').addEventListener('click', () => {
                msState[key].clear();
                list.querySelectorAll('.br-ms-item').forEach(i => i.classList.remove('selected'));
                updateKbBadge(key, countBadge);
            });
            footer.querySelector('.br-ms-apply-btn').addEventListener('click', () => {
                dropdown.classList.remove('open');
                trigger.classList.remove('open');
                applyKbFilters();
            });
        }
    });

    function closeAllKbDropdowns() {
        ['kb-assignee', 'kb-priority', 'kb-status'].forEach(k => {
            const s = k.replace('kb-', '');
            const d = document.getElementById('kb-ms-' + s + '-dropdown');
            const t = document.getElementById('kb-ms-' + s + '-trigger');
            if (d) d.classList.remove('open');
            if (t) t.classList.remove('open');
        });
    }
    document.addEventListener('click', closeAllKbDropdowns);
    document.querySelectorAll('.br-ms-dropdown').forEach(d => d.addEventListener('click', e => e.stopPropagation()));

    function updateKbBadge(key, el) {
        const count = msState[key].size;
        if (!el) return;
        el.textContent = count;
        el.style.display = count > 0 ? 'inline-flex' : 'none';
    }

    /* ══ APPLY FILTERS ══ */
    function applyKbFilters() {
        const q         = (document.getElementById('kb-search').value || '').toLowerCase().trim();
        const assignees = msState['kb-assignee'];
        const priorities= msState['kb-priority'];
        const statuses  = msState['kb-status'];
        let visibleCount= 0;

        document.querySelectorAll('.kb-card').forEach(card => {
            const matchQ      = !q || (card.dataset.title || '').includes(q) || (card.dataset.bugid || '').includes(q);
            const matchAssign = assignees.size === 0 || [...assignees].some(id => (card.dataset.assignees || '').split(',').includes(String(id)));
            const matchPri    = priorities.size === 0 || [...priorities].some(p  => (card.dataset.priority || '').includes(p));
            const matchStatus = statuses.size === 0   || [...statuses].some(s   => (card.dataset.status || '') === String(s));
            const show        = matchQ && matchAssign && matchPri && matchStatus;
            card.classList.toggle('kanban-card-hidden', !show);
            if (show) visibleCount++;
        });

        document.getElementById('kb-visible-count').textContent = visibleCount;
        renderKbChips();

        /* Update per-column counts */
        document.querySelectorAll('.kb-col-body').forEach(col => {
            const colId  = col.id.replace('task-list-', '');
            const visible= col.querySelectorAll('.kb-card:not(.kanban-card-hidden)').length;
            const el     = document.getElementById('col-count-' + colId);
            if (el) el.textContent = visible;
        });
    }

    /* ══ CHIPS ══ */
    function renderKbChips() {
        const wrap = document.getElementById('kb-active-chips');
        wrap.innerHTML = '';
        let hasAny = false;
        const keyMap = {
            'kb-assignee': { icon: 'user',         listId: 'kb-ms-assignee-list' },
            'kb-priority': { icon: 'flag',         listId: 'kb-ms-priority-list' },
            'kb-status':   { icon: 'circle-check', listId: 'kb-ms-status-list'  },
        };
        Object.entries(keyMap).forEach(([key, cfg]) => {
            msState[key].forEach(val => {
                hasAny = true;
                const item  = document.querySelector('#' + cfg.listId + ' .br-ms-item[data-value="' + val + '"]');
                const label = item ? item.dataset.label : val;
                const chip  = document.createElement('span');
                chip.className = 'kb-chip';
                chip.innerHTML = `<i class="ti ti-${cfg.icon}" style="font-size:.65rem;"></i> ${label} <span class="kb-chip-remove" data-key="${key}" data-val="${val}">✕</span>`;
                wrap.appendChild(chip);
            });
        });
        wrap.style.display = hasAny ? 'flex' : 'none';
        wrap.querySelectorAll('.kb-chip-remove').forEach(btn => {
            btn.addEventListener('click', () => {
                const k = btn.dataset.key, v = btn.dataset.val;
                msState[k].delete(v);
                const s = k.replace('kb-', '');
                const item = document.querySelector('#kb-ms-' + s + '-list .br-ms-item[data-value="' + v + '"]');
                if (item) item.classList.remove('selected');
                updateKbBadge(k, document.getElementById('kb-ms-' + s + '-count'));
                applyKbFilters();
            });
        });
    }

    document.getElementById('kb-search')?.addEventListener('input', applyKbFilters);
    document.getElementById('kb-reset-btn')?.addEventListener('click', () => {
        document.getElementById('kb-search').value = '';
        ['kb-assignee', 'kb-priority', 'kb-status'].forEach(key => {
            msState[key].clear();
            const s = key.replace('kb-', '');
            document.querySelectorAll('#kb-ms-' + s + '-list .br-ms-item').forEach(i => i.classList.remove('selected'));
            updateKbBadge(key, document.getElementById('kb-ms-' + s + '-count'));
        });
        applyKbFilters();
    });

    /* ══ MODAL ══ */
    const modal    = document.getElementById('bug-detail-modal');
    const closeBtn = document.getElementById('bug-modal-close-btn');
    const bmTitle  = document.getElementById('bm-title');
    const bmBody   = document.getElementById('bm-body');

    const bugShowUrls = {};
    @foreach($bugStatus as $bStatus)
        @foreach($bStatus->bugs($project->id) as $kBug)
        bugShowUrls['{{ $kBug->id }}'] = '{{ route('organization.task.bug.show', [$project->id, $kBug->id]) }}';
        @endforeach
    @endforeach

    function openBugModal(bugId, openTab) {
        const url = bugShowUrls[String(bugId)];
        if (!url) return;
        modal.classList.add('show');
        document.body.style.overflow = 'hidden';
        bmTitle.textContent = 'Loading…';
        bmBody.innerHTML = '<div style="text-align:center;padding:50px;color:var(--text-muted);"><i class="ti ti-loader-2" style="font-size:2rem;animation:spin 1s linear infinite;"></i></div>';

        fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'text/html' } })
        .then(r => r.text())
        .then(html => {
            bmBody.innerHTML = html;
            const hero = bmBody.querySelector('.bvm-hero-title');
            if (hero) bmTitle.textContent = hero.textContent.trim().replace(/^\./,'').trim();

            const body       = bmBody.querySelector('.bvm-body');
            const uploadUrl  = body ? body.dataset.uploadUrl  : '';
            const commentUrl = body ? body.dataset.commentUrl : '';

            bmBody.querySelectorAll('.bvm-tab-btn').forEach(btn => {
                btn.addEventListener('click', function () {
                    bmBody.querySelectorAll('.bvm-tab-btn').forEach(b => b.classList.remove('active'));
                    bmBody.querySelectorAll('.bvm-tab-pane').forEach(p => p.classList.remove('active'));
                    btn.classList.add('active');
                    const pane = bmBody.querySelector('#' + btn.dataset.tab);
                    if (pane) pane.classList.add('active');
                });
            });

            if (openTab === 'files') {
                setTimeout(() => {
                    const fb = bmBody.querySelector('#bvm-files-tab-btn,[data-tab="bvm-files"]');
                    if (fb) fb.click();
                }, 80);
            }

            const toggleBtn  = bmBody.querySelector('#bvm-toggle-upload');
            const uploadArea = bmBody.querySelector('#bvm-upload-area');
            if (toggleBtn && uploadArea) toggleBtn.addEventListener('click', () => uploadArea.classList.toggle('open'));

            const fileInput   = bmBody.querySelector('#bvm-file-input');
            const prevWrap    = bmBody.querySelector('#bvm-preview-wrap');
            const prevImg     = bmBody.querySelector('#bvm-prev-img');
            const prevVideo   = bmBody.querySelector('#bvm-prev-video');
            const prevAudio   = bmBody.querySelector('#bvm-prev-audio');
            const prevGeneric = bmBody.querySelector('#bvm-prev-generic');
            const prevName    = bmBody.querySelector('#bvm-prev-name');

            function hideAllPrev() {
                [prevImg, prevVideo, prevAudio, prevGeneric].forEach(el => { if (el) el.style.display = 'none'; });
            }
            if (fileInput) {
                fileInput.addEventListener('change', function () {
                    const file = this.files[0];
                    if (!file) { if (prevWrap) prevWrap.classList.remove('show'); return; }
                    const objUrl = URL.createObjectURL(file);
                    const type   = file.type.toLowerCase();
                    hideAllPrev();
                    if (prevWrap) prevWrap.classList.add('show');
                    if (type.startsWith('image/'))      { if (prevImg)   { prevImg.src   = objUrl; prevImg.style.display   = 'block'; } }
                    else if (type.startsWith('video/')) { if (prevVideo) { prevVideo.src = objUrl; prevVideo.style.display = 'block'; } }
                    else if (type.startsWith('audio/')) { if (prevAudio) { prevAudio.src = objUrl; prevAudio.style.display = 'block'; } }
                    else { if (prevName) prevName.textContent = file.name; if (prevGeneric) prevGeneric.style.display = 'block'; }
                });
            }

            const uploadSubmit = bmBody.querySelector('#bvm-upload-submit');
            if (uploadSubmit && uploadUrl) {
                uploadSubmit.addEventListener('click', function () {
                    const file = fileInput ? fileInput.files[0] : null;
                    if (!file) { show_toastr('Error', 'Please select a file!', 'error'); return; }
                    const fd = new FormData();
                    fd.append('file', file);
                    fd.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));
                    uploadSubmit.disabled = true;
                    uploadSubmit.innerHTML = '<i class="fa fa-spinner fa-spin" style="font-size:.78rem;"></i>';
                    fetch(uploadUrl, { method: 'POST', body: fd, headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' } })
                    .then(r => r.json())
                    .then(data => {
                        if (data.error) { show_toastr('Error', data.error, 'error'); return; }
                        const ext   = (data.extension || '').replace('.', '').toLowerCase();
                        const isImg = ['jpg','jpeg','png','gif','webp','svg'].includes(ext);
                        let iconHtml = isImg
                            ? '<img class="thumb" src="' + data.file_url + '" style="width:38px;height:38px;object-fit:cover;" alt="">'
                            : (function () {
                                let ic = 'ti-file', col = 'var(--accent)';
                                if (ext === 'pdf') { ic = 'ti-file-type-pdf'; col = '#e0003f'; }
                                else if (['doc','docx'].includes(ext)) { ic = 'ti-file-type-doc'; col = '#185fa5'; }
                                else if (['xls','xlsx','csv'].includes(ext)) { ic = 'ti-file-type-xls'; col = '#00c48c'; }
                                else if (['mp4','mov','webm'].includes(ext)) ic = 'ti-video';
                                else if (['mp3','wav'].includes(ext)) ic = 'ti-music';
                                return '<i class="ti ' + ic + '" style="color:' + col + ';font-size:1rem;"></i>';
                              })();
                        const newItem = '<div class="bvm-file-item bug-file" data-id="' + data.id + '"><div class="bvm-file-icon">' + iconHtml + '</div><div class="bvm-file-info"><p class="bvm-file-name">' + data.name + '</p><span class="bvm-file-meta"><i class="ti ti-clock"></i> Uploaded just now' + (data.file_size ? ' &bull; ' + data.file_size : '') + '</span></div><div class="dropdown"><button class="bvm-dot-btn" data-bs-toggle="dropdown"><i class="ti ti-dots-vertical"></i></button><ul class="dropdown-menu dropdown-menu-end" style="z-index:999999;"><li><a class="dropdown-item" href="' + data.file_url + '" target="_blank"><i class="ti ti-eye"></i> View</a></li><li><a class="dropdown-item" href="' + data.file_url + '" download="' + data.name + '"><i class="ti ti-download"></i> Download</a></li><li><a class="dropdown-item text-danger bvm-delete-file" href="#" data-url="' + data.deleteUrl + '"><i class="ti ti-trash"></i> Delete</a></li></ul></div></div>';
                        const noFiles  = bmBody.querySelector('#bvm-no-files');  if (noFiles)  noFiles.remove();
                        const fileList = bmBody.querySelector('#bvm-file-list'); if (fileList) fileList.insertAdjacentHTML('afterbegin', newItem);
                        const countEl  = bmBody.querySelector('#bvm-files-count'); if (countEl) countEl.textContent = parseInt(countEl.textContent || 0) + 1;
                        if (fileInput) fileInput.value = '';
                        hideAllPrev(); if (prevWrap) prevWrap.classList.remove('show');
                        if (uploadArea) uploadArea.classList.remove('open');
                        show_toastr('Success', 'File uploaded successfully!', 'success');
                    })
                    .catch(() => show_toastr('Error', 'File upload failed!', 'error'))
                    .finally(() => { uploadSubmit.disabled = false; uploadSubmit.innerHTML = '<i class="fa-solid fa-check" style="font-size:.78rem;"></i>'; });
                });
            }

            const commentInput  = bmBody.querySelector('#bvm-comment-input');
            const commentSubmit = bmBody.querySelector('#bvm-comment-submit');
            if (commentSubmit && commentUrl) {
                commentSubmit.addEventListener('click', function () {
                    const text = (commentInput ? commentInput.value : '').trim();
                    if (!text) { show_toastr('Error', 'Please write a comment!', 'error'); return; }
                    commentSubmit.disabled = true;
                    commentSubmit.innerHTML = '<i class="fa fa-spinner fa-spin" style="font-size:.78rem;"></i>';
                    const fd = new FormData();
                    fd.append('comment', text);
                    fd.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));
                    fetch(commentUrl, { method: 'POST', body: fd, headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' } })
                    .then(r => r.json())
                    .then(data => {
                        if (!data.is_success) { show_toastr('Error', 'Something went wrong!', 'error'); return; }
                        const newComment = '<div class="bvm-comment-item comment-box"><img src="' + data.data.avatar + '" class="bvm-comment-avatar" alt="' + data.data.name + '"><div class="bvm-comment-body"><div class="bvm-comment-name">' + data.data.name + '</div><div class="bvm-comment-text">' + data.data.comment + '</div><div class="bvm-comment-time"><i class="ti ti-clock"></i> just now</div></div><div class="dropdown"><button class="bvm-dot-btn" data-bs-toggle="dropdown"><i class="ti ti-dots-vertical"></i></button><ul class="dropdown-menu dropdown-menu-end" style="z-index:999999;"><li><a class="dropdown-item text-danger bvm-delete-comment" href="#" data-url="' + data.data.deleteUrl + '"><i class="ti ti-trash"></i> Delete</a></li></ul></div></div>';
                        const list  = bmBody.querySelector('#bvm-comments-list');
                        const empty = list ? list.querySelector('.bvm-empty') : null;
                        if (empty) empty.remove();
                        if (list) list.insertAdjacentHTML('afterbegin', newComment);
                        if (commentInput) commentInput.value = '';
                        show_toastr('Success', 'Comment added!', 'success');
                    })
                    .catch(() => show_toastr('Error', 'Something went wrong!', 'error'))
                    .finally(() => { commentSubmit.disabled = false; commentSubmit.innerHTML = '<i class="fa-solid fa-paper-plane"></i>'; });
                });
            }

            if (typeof bootstrap !== 'undefined') {
                bmBody.querySelectorAll('[data-bs-toggle="dropdown"]').forEach(el => {
                    try { new bootstrap.Dropdown(el); } catch (e) {}
                });
            }
        })
        .catch(() => {
            bmBody.innerHTML = '<div style="text-align:center;padding:40px;color:var(--text-muted);">Failed to load. Please try again.</div>';
        });
    }

    function closeBugModal() {
        modal.classList.remove('show');
        document.body.style.overflow = '';
    }

    document.addEventListener('click', function (e) {
        const trigger = e.target.closest('.open-bug-modal');
        if (trigger) {
            e.preventDefault();
            e.stopPropagation();
            openBugModal(trigger.dataset.bugId, trigger.dataset.openTab || '');
        }
    });

    if (closeBtn) closeBtn.addEventListener('click', closeBugModal);
    if (modal)    modal.addEventListener('click', e => { if (e.target === modal) closeBugModal(); });
    document.addEventListener('keydown', e => { if (e.key === 'Escape') closeBugModal(); });

    document.addEventListener('click', function (e) {
        const dc = e.target.closest('.bvm-delete-comment');
        if (dc && modal.classList.contains('show')) {
            e.preventDefault();
            Swal.fire({ title: 'Are you sure?', text: 'Delete this comment?', icon: 'warning', showCancelButton: true, confirmButtonColor: '#d33', cancelButtonColor: '#3085d6', confirmButtonText: 'Yes, delete!', customClass: { popup: 'swal-zindex-fix' } }).then(r => {
                if (!r.isConfirmed) return;
                fetch(dc.dataset.url, { method: 'DELETE', headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'), 'X-Requested-With': 'XMLHttpRequest' } })
                .then(r => r.json()).then(() => { dc.closest('.comment-box')?.remove(); show_toastr('Success', 'Comment deleted!', 'success'); }).catch(() => show_toastr('Error', 'Unable to delete!', 'error'));
            });
        }
        const df = e.target.closest('.bvm-delete-file');
        if (df && modal.classList.contains('show')) {
            e.preventDefault();
            Swal.fire({ title: 'Are you sure?', text: 'Delete this file?', icon: 'warning', showCancelButton: true, confirmButtonColor: '#d33', cancelButtonColor: '#3085d6', confirmButtonText: 'Yes, delete!', customClass: { popup: 'swal-zindex-fix' } }).then(r => {
                if (!r.isConfirmed) return;
                fetch(df.dataset.url, { method: 'DELETE', headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'), 'X-Requested-With': 'XMLHttpRequest' } })
                .then(r => r.json()).then(() => {
                    const item = df.closest('.bug-file'); if (item) item.remove();
                    const countEl = bmBody.querySelector('#bvm-files-count'); if (countEl) countEl.textContent = Math.max(0, parseInt(countEl.textContent || 0) - 1);
                    const list = bmBody.querySelector('#bvm-file-list'); if (list && !list.querySelector('.bug-file')) list.innerHTML = '<div class="bvm-empty" id="bvm-no-files"><i class="ti ti-paperclip-off"></i> No files attached yet.</div>';
                    show_toastr('Success', 'File deleted!', 'success');
                }).catch(() => show_toastr('Error', 'Unable to delete!', 'error'));
            });
        }
    });

})();
</script>

@endsection