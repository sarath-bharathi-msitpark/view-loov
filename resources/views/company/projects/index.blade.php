@extends('company.layouts.company')

@section('page-title')
    {{__('Manage Projects')}}
@endsection

@section('page-icon')
    {{ asset('assets/assestsnew/Manage_projects.svg') }}
@endsection

@push('script-page')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
@endpush

@section('action-btn')
<div class="float-end d-flex align-items-center gap-2 mp-topbar">

    {{-- Search --}}
    <div class="mp-search-wrap">
        <i class="fa-solid fa-magnifying-glass mp-search-icon"></i>
        <input
            id="project_keyword"
            class="mp-search-input"
            type="search"
            name="keyword"
            placeholder="{{__('Search project…')}}"
            autocomplete="off"
            aria-label="Search"
        />
    </div>

    {{-- View toggle --}}
    @if($view == 'grid')
        <a href="{{ route('organization.projects.list','list') }}"
           data-bs-toggle="tooltip" title="{{__('List View')}}"
           class="mp-icon-btn">
            <i class="ti ti-list"></i>
        </a>
    @else
        <a href="{{ route('organization.projects.index') }}"
           data-bs-toggle="tooltip" title="{{__('Grid View')}}"
           class="mp-icon-btn">
            <i class="ti ti-layout-grid"></i>
        </a>
    @endif

    {{-- Sort dropdown --}}
    <div class="dropdown">
        <a href="#" class="mp-icon-btn" role="button"
           data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false"
           title="{{ __('Sort') }}">
            <i class="ti ti-arrows-sort"></i>
        </a>
        <div class="dropdown-menu mp-dropdown" id="project_sort">
            <div class="mp-dd-hdr">{{__('Sort by')}}</div>
            <a class="dropdown-item active" href="#" data-val="created_at-desc">
                <i class="ti ti-sort-descending"></i>{{__('Newest')}}
            </a>
            <a class="dropdown-item" href="#" data-val="created_at-asc">
                <i class="ti ti-sort-ascending"></i>{{__('Oldest')}}
            </a>
            <a class="dropdown-item" href="#" data-val="project_name-desc">
                <i class="ti ti-sort-descending-letters"></i>{{__('From Z–A')}}
            </a>
            <a class="dropdown-item" href="#" data-val="project_name-asc">
                <i class="ti ti-sort-ascending-letters"></i>{{__('From A–Z')}}
            </a>
        </div>
    </div>

    {{-- Status filter (existing dropdown — keeps project-filter-actions class) --}}
    <div class="dropdown">
        <a href="#" class="mp-filter-pill" id="mp-status-btn" role="button"
           data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            <i class="ti ti-filter" style="font-size:.78rem;"></i>
            <span id="mp-status-label">{{__('Status')}}</span>
            <i class="ti ti-chevron-down" style="font-size:.68rem;opacity:.55;"></i>
        </a>
        <div class="dropdown-menu mp-dropdown project-filter-actions" id="project_status">
            <div class="mp-dd-hdr">{{__('Filter by Status')}}</div>
            <a class="dropdown-item filter-action filter-show-all active" href="#">
                <span class="mp-sdot" style="background:var(--mp-muted);"></span>
                {{__('Show All')}}
            </a>
            @foreach(\App\Models\Project::$project_status as $key => $val)
                <a class="dropdown-item filter-action" href="#" data-val="{{ $key }}">
                    <span class="mp-sdot mp-sdot-{{ $key }}"></span>
                    {{__($val)}}
                </a>
            @endforeach
        </div>
    </div>

    {{-- Create --}}
    @can('project_management')
        <a href="#"
           data-size="lg"
           data-url="{{ route('organization.projects.create') }}"
           data-ajax-popup="true"
           data-bs-toggle="tooltip"
           title="{{__('Create New Project')}}"
           data-title="{{__('Create Project')}}"
           class="mp-create-btn">
            <i class="ti ti-plus"></i>
            <span>{{__('New Project')}}</span>
        </a>
    @endcan

</div>
@endsection

@section('content')
@include('company.layouts.partials.nav')

<style>
:root {
    --mp-accent:      #4f52ff;
    --mp-accent-soft: #eeeeff;
    --mp-accent-glow: rgba(79,82,255,.15);
    --mp-danger:      #ff4f6a;
    --mp-success:     #00c48c;
    --mp-warn:        #ff9d0a;
    --mp-surface:     #ffffff;
    --mp-surface-2:   #f7f8fc;
    --mp-border:      #e8eaf2;
    --mp-text:        #12142a;
    --mp-muted:       #7c7f9a;
    --mp-shadow:      0 2px 20px rgba(18,20,42,.07);
    --mp-radius:      14px;
    --mp-radius-xs:   6px;
}

.mp-topbar { flex-wrap: wrap; gap: 8px !important; }

/* search */
.mp-search-wrap { position: relative; display: flex; align-items: center; }
.mp-search-icon { position: absolute; left: 13px; color: var(--mp-muted); font-size: .76rem; pointer-events: none; }
.mp-search-input {
    padding: 8px 14px 8px 34px;
    border: 1px solid var(--mp-border); border-radius: 6px;
    background: var(--mp-surface); font-size: .84rem; color: var(--mp-text);
    outline: none; width: 220px;
    transition: border-color .15s, box-shadow .15s, width .2s;
    box-shadow: var(--mp-shadow);
}
.mp-search-input:focus { border-color: var(--mp-accent); box-shadow: 0 0 0 3px var(--mp-accent-glow); width: 260px; }
.mp-search-input::placeholder { color: var(--mp-muted); }

/* icon btn */
.mp-icon-btn {
    display: inline-flex; align-items: center; justify-content: center;
    width: 36px; height: 36px; border-radius: var(--mp-radius-xs);
    border: 1px solid var(--mp-border); background: var(--mp-surface);
    color: var(--mp-muted); text-decoration: none; transition: all .13s;
    box-shadow: var(--mp-shadow);
}
.mp-icon-btn:hover { background: var(--mp-accent-soft); border-color: rgba(79,82,255,.22); color: var(--mp-accent); }
.mp-icon-btn i { font-size: .88rem; }

/* filter pill */
.mp-filter-pill {
    display: inline-flex; align-items: center; gap: 7px;
    padding: 7px 14px; border-radius: 6px;
    border: 1px solid var(--mp-border); background: var(--mp-surface);
    color: var(--mp-muted); font-size: .83rem; font-weight: 500;
    text-decoration: none; cursor: pointer;
    transition: all .13s; box-shadow: var(--mp-shadow);
}
.mp-filter-pill:hover, .mp-filter-pill.has-filter {
    border-color: var(--mp-accent); color: var(--mp-accent); background: var(--mp-accent-soft);
}

/* create btn */
.mp-create-btn {
    display: inline-flex; align-items: center; gap: 7px;
    padding: 7px 16px; border-radius: var(--mp-radius-xs);
    background: var(--mp-accent); color: #fff; font-size: .84rem; font-weight: 600;
    text-decoration: none; transition: opacity .13s, box-shadow .13s;
    box-shadow: 0 2px 10px var(--mp-accent-glow);
}
.mp-create-btn:hover { opacity: .9; box-shadow: 0 4px 16px var(--mp-accent-glow); color: #fff; }
.mp-create-btn i { font-size: .88rem; }

/* dropdown menus */
.mp-dropdown {
    border: 1px solid var(--mp-border); border-radius: 10px;
    box-shadow: 0 8px 28px rgba(18,20,42,.11); padding: 6px; min-width: 186px;
}
.mp-dd-hdr {
    font-size: .6rem; font-weight: 700; letter-spacing: .1em;
    text-transform: uppercase; color: var(--mp-muted); padding: 6px 10px 8px;
}
.mp-dropdown .dropdown-item {
    display: flex; align-items: center; gap: 8px;
    border-radius: var(--mp-radius-xs); padding: 7px 10px;
    font-size: .83rem; color: var(--mp-text); transition: background .1s;
}
.mp-dropdown .dropdown-item:hover { background: var(--mp-surface-2); }
.mp-dropdown .dropdown-item.active { background: var(--mp-accent-soft); color: var(--mp-accent); font-weight: 600; }
.mp-dropdown .dropdown-item i { font-size: .8rem; color: var(--mp-muted); }
.mp-dropdown .dropdown-item.active i { color: var(--mp-accent); }

/* status dots */
.mp-sdot { width: 7px; height: 7px; border-radius: 50%; flex-shrink: 0; display: inline-block; }
.mp-sdot-not_started { background: var(--mp-muted); }
.mp-sdot-in_progress { background: var(--mp-accent); }
.mp-sdot-on_hold     { background: var(--mp-warn); }
.mp-sdot-cancelled   { background: var(--mp-danger); }
.mp-sdot-completed   { background: var(--mp-success); }

/* chips */
.mp-chips-strip { display: flex; align-items: center; gap: 8px; flex-wrap: wrap; margin-bottom: 16px; }
.mp-chip {
    display: inline-flex; align-items: center; gap: 6px;
    padding: 4px 10px; border-radius: 50px;
    background: var(--mp-accent-soft); border: 1px solid rgba(79,82,255,.2);
    color: var(--mp-accent); font-size: .75rem; font-weight: 600;
}
.mp-chip-remove {
    background: none; border: none; color: var(--mp-accent);
    cursor: pointer; padding: 0; line-height: 1; font-size: .75rem; opacity: .6;
}
.mp-chip-remove:hover { opacity: 1; }
.float-end a>i{
    color: unset;
}
/* content */
.mp-content { padding: 24px 0 56px; }
.mp-loading {
    display: flex; align-items: center; justify-content: center;
    padding: 80px 0; color: var(--mp-muted); gap: 10px; font-size: .9rem;
}
.mp-loading i { font-size: 1.2rem; animation: mp-spin .8s linear infinite; }
@keyframes mp-spin { to { transform: rotate(360deg); } }
#project_view { animation: mp-fade .2s ease; }
@keyframes mp-fade { from{opacity:0;transform:translateY(4px);}to{opacity:1;transform:translateY(0);} }

@media(max-width:768px){
    .mp-search-input { width: 160px; }
    .mp-search-input:focus { width: 200px; }
}
</style>

<div class="mp-content">
    <div class="mp-chips-strip" id="mp-chips-strip" style="display:none;"></div>
    <div class="row" id="project_view">
        <div class="col-12">
            <div class="mp-loading">
                <i class="ti ti-loader-2"></i> {{__('Loading projects…')}}
            </div>
        </div>
    </div>
</div>
@endsection

@push('script-page')
<script>
/* ── global state (outer scope so ajaxFilterProjectView can read them) ── */
var sort               = 'created_at-desc';
var status             = '';
var activeStatusLabels = {};

$(document).ready(function () {

    /* shared filter state — grid partial writes these via window.* so parent can read */
    window.gProjectIds = [];
    window.gDateFrom   = '';
    window.gDateTo     = '';

    /* ── initial load ── */
    ajaxFilterProjectView();

    /* ════════════════════════
       STATUS  (existing logic — unchanged)
    ════════════════════════ */
    $(".project-filter-actions").on('click', '.filter-action', function (e) {
        e.preventDefault();
        if ($(this).hasClass('filter-show-all')) {
            $('.filter-action').removeClass('active');
            $(this).addClass('active');
            activeStatusLabels = {};
        } else {
            $('.filter-show-all').removeClass('active');
            var key = $(this).attr('data-val');
            var lbl = $.trim($(this).text());
            if ($(this).hasClass('active')) {
                $(this).removeClass('active');
                delete activeStatusLabels[key];
            } else {
                $(this).addClass('active');
                activeStatusLabels[key] = lbl;
            }
        }
        var arr = [];
        $('div.project-filter-actions').find('.active:not(.filter-show-all)').each(function () {
            arr.push($(this).attr('data-val'));
        });
        status = arr;
        if (arr.length) {
            $('#mp-status-btn').addClass('has-filter');
            $('#mp-status-label').text(arr.length + ' selected');
        } else {
            $('#mp-status-btn').removeClass('has-filter');
            $('#mp-status-label').text('{{ __("Status") }}');
        }
        updateChips();
        ajaxFilterProjectView();
    });

    /* ════════════════════════
       SORT  (existing — unchanged)
    ════════════════════════ */
    $('#project_sort').on('click', 'a', function (e) {
        e.preventDefault();
        sort = $(this).attr('data-val');
        $('#project_sort a').removeClass('active');
        $(this).addClass('active');
        ajaxFilterProjectView();
    });

    /* ════════════════════════
       KEYWORD  (existing — unchanged)
    ════════════════════════ */
    $(document).on('keyup', '#project_keyword', function () {
        ajaxFilterProjectView();
    });

    /* ════════════════════════
       INVITE USER  (existing — unchanged)
    ════════════════════════ */
    $(document).on('click', '.invite_usr', function () {
        var project_id = $('#project_id').val();
        var user_id    = $(this).attr('data-id');
        $.ajax({
            url: '{{ route('organization.invite.project.user.member') }}',
            method: 'POST', dataType: 'json',
            data: { project_id: project_id, user_id: user_id, "_token": "{{ csrf_token() }}" },
            success: function (data) {
                if (data.code == '200') { show_toastr(data.status, data.success, 'success'); setInterval('location.reload()', 5000); }
                else if (data.code == '404') { show_toastr(data.status, data.errors, 'error'); }
            }
        });
    });

    /* ════════════════════════
       STATUS CHIPS  (existing — unchanged)
    ════════════════════════ */
    function updateChips() {
        var $strip = $('#mp-chips-strip');
        $strip.empty();
        var keys = Object.keys(activeStatusLabels);
        if (!keys.length) { $strip.hide(); return; }
        $strip.show();
        keys.forEach(function (key) {
            var $chip = $('<span class="mp-chip"><span>' + activeStatusLabels[key] + '</span><button class="mp-chip-remove" title="Remove">&#x2715;</button></span>');
            $chip.find('button').on('click', function () {
                $('div.project-filter-actions .filter-action[data-val="' + key + '"]').removeClass('active');
                delete activeStatusLabels[key];
                var rem = Object.keys(activeStatusLabels);
                status = rem;
                if (!rem.length) { $('.filter-show-all').addClass('active'); $('#mp-status-btn').removeClass('has-filter'); $('#mp-status-label').text('{{ __("Status") }}'); }
                else { $('#mp-status-label').text(rem.length + ' selected'); }
                updateChips(); ajaxFilterProjectView();
            });
            $strip.append($chip);
        });
    }
});

/* ════════════════════════════════
   AJAX LOADER  — extended with project_ids, date_from, date_to
   The grid partial calls window.ajaxFilterProjectView() directly.
════════════════════════════════ */
var currentRequest = null;

function ajaxFilterProjectView() {
    var mainEle = $('#project_view');
    var view    = '{{$view}}';

    /* read shared state — window.G is the single source of truth set by the grid partial */
    var G         = window.G || {};
    var projectIds = (G.projIds  || []).join(',');
    var dateFrom   = G.dateFrom  || '';
    var dateTo     = G.dateTo    || '';
    var gridStatus = G.statuses  || [];

    /* read parent state */
    var keyword   = G.keyword || $('#project_keyword').val() || '';
    var sortVal   = sort || 'created_at-desc';
    /* parent status (from topbar dropdown) takes precedence, else use grid status */
    var parentStatus = Array.isArray(status) ? status : (status ? [status] : []);
    var statusVal    = parentStatus.length ? parentStatus : gridStatus;

    var loaderTimer = setTimeout(function () {
        mainEle.html('<div class="col-12"><div class="mp-loading"><i class="ti ti-loader-2"></i> {{__("Loading projects…")}}</div></div>');
    }, 300);

    if (currentRequest != null) { currentRequest.abort(); }

    currentRequest = $.ajax({
        url: '{{ route('organization.filter.project.view') }}',
        data: {
            view:        view,
            sort:        sortVal,
            keyword:     keyword,
            status:      statusVal,
            project_ids: projectIds,
            date_from:   dateFrom,
            date_to:     dateTo,
        },
        success: function (data) {
            clearTimeout(loaderTimer);
            mainEle.html(data.html);
            $('[id^=fire-modal]').remove();
            if (typeof loadConfirm === 'function') { loadConfirm(); }
        },
        error: function (xhr) {
            if (xhr.statusText !== 'abort') {
                clearTimeout(loaderTimer);
                mainEle.html('<div class="col-12"><div class="mp-loading" style="color:var(--mp-danger);"><i class="ti ti-alert-circle"></i> {{__("Failed to load projects.")}}</div></div>');
            }
        }
    });
}

/* expose globally so the grid partial can call it */
window.ajaxFilterProjectView = ajaxFilterProjectView;
</script>
@endpush