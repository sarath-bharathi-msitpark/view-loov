@extends('company.layouts.company')
@section('page-title')
    {{ ucwords($project->project_name) }}
@endsection

@section('page-icon')
    {{ asset('assets/assestsnew/Manage_projects.svg') }}
@endsection

@push('script-page')
    <script>
        (function () {
            var options = {
                chart: { type: 'area', height: 60, sparkline: { enabled: true } },
                colors: ["#ffa21d"],
                dataLabels: { enabled: false },
                stroke: { curve: 'smooth', width: 2 },
                series: [{ name: 'Bandwidth', data: {{ json_encode(array_map('intval', $project_data['timesheet_chart']['chart'])) }} }],
                tooltip: {
                    followCursor: false, fixed: { enabled: false },
                    x: { show: false },
                    y: { title: { formatter: function (seriesName) { return '' } } },
                    marker: { show: false }
                }
            };
            var chart = new ApexCharts(document.querySelector("#timesheet_chart"), options);
            chart.render();
        })();

        (function () {
            var options = {
                chart: { type: 'area', height: 60, sparkline: { enabled: true } },
                colors: ["#ffa21d"],
                dataLabels: { enabled: false },
                stroke: { curve: 'smooth', width: 2 },
                series: [{ name: 'Bandwidth', data: {{ json_encode($project_data['task_chart']['chart']) }} }],
                tooltip: {
                    followCursor: false, fixed: { enabled: false },
                    x: { show: false },
                    y: { title: { formatter: function (seriesName) { return '' } } },
                    marker: { show: false }
                }
            };
            var chart = new ApexCharts(document.querySelector("#task_chart"), options);
            chart.render();
        })();

        $(document).ready(function () {
            loadProjectUser();
            $(document).on('click', '.invite_usr', function () {
                var project_id = $('#project_id').val();
                var user_id = $(this).attr('data-id');
                $.ajax({
                    url: '{{ route('organization.invite.project.user.member') }}',
                    method: 'POST', dataType: 'json',
                    data: { 'project_id': project_id, 'user_id': user_id, "_token": "{{ csrf_token() }}" },
                    success: function (data) {
                        if (data.code == '200') {
                            show_toastr(data.status, data.success, 'success');
                            setInterval('location.reload()', 5000);
                            loadProjectUser();
                        } else if (data.code == '404') {
                            show_toastr(data.status, data.errors, 'error');
                        }
                    }
                });
            });
        });

        function loadProjectUser() {
            var mainEle = $('#project_users');
            var project_id = '{{ $project->id }}';
            $.ajax({
                url: '{{ route('organization.project.user') }}',
                data: { project_id: project_id },
                beforeSend: function () {
                    $('#project_users').html('<div class="pv3-loading">{{ __('Loading...') }}</div>');
                },
                success: function (data) {
                    mainEle.html(data.html);
                    $('[id^=fire-modal]').remove();
                    mainEle.find('.avatar img').remove();
                }
            });
        }
    </script>

    <script>
        function copyToClipboard(element) {
            var copyText = element.id;
            navigator.clipboard.writeText(copyText);
            show_toastr('success', 'Url copied to clipboard', 'success');
        }
    </script>
@endpush

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('organization.projects.index') }}">{{ __('Project') }}</a></li>
    <li class="breadcrumb-item">{{ ucwords($project->project_name) }}</li>
@endsection

@section('action-btn')
    <div class="float-end d-flex align-items-center gap-2">
        <a href="{{ route('organization.task.bug', $project->id) }}" class="pv3-btn">
            <i class="ti ti-bug"></i><span>{{ __('Bug Report') }}</span>
        </a>
        <a href="{{ route('organization.projects.tasks.index', $project->id) }}" class="pv3-btn pv3-btn--primary">
            <i class="ti ti-checklist"></i><span>{{ __('Tasks') }}</span>
        </a>
        @can('project_management')
            <a href="#" data-size="lg"
               data-url="{{ route('organization.projects.edit', $project->id) }}"
               data-ajax-popup="true"
               data-bs-toggle="tooltip" title="{{ __('Edit Project') }}"
               class="pv3-icon-btn">
                <i class="ti ti-pencil"></i>
            </a>
        @endcan
    </div>
@endsection

@section('content')
@include('company.layouts.partials.nav')

<style>
/* ── Tokens ─────────────────────────────────────────── */
:root {
    --pv3-accent:       #4f6ef7;
    --pv3-accent-soft:  rgba(79,110,247,.09);
    --pv3-accent-mid:   rgba(79,110,247,.18);
    --pv3-green:        #10b981;
    --pv3-green-soft:   rgba(16,185,129,.10);
    --pv3-amber:        #f59e0b;
    --pv3-amber-soft:   rgba(245,158,11,.10);
    --pv3-red-soft:     rgba(239,68,68,.10);
    --pv3-red:          #ef4444;
    --pv3-text:         #1e293b;
    --pv3-muted:        #64748b;
    --pv3-border:       #e9ecf0;
    --pv3-bg:           #f4f6fb;
    --pv3-white:        #ffffff;
    --pv3-panel-bg:     #eef1f8;
    --pv3-r:            12px;
    --pv3-r-sm:         8px;
    --pv3-shadow:       0 1px 4px rgba(0,0,0,.06), 0 4px 20px rgba(0,0,0,.04);
    --pv3-shadow-card:  0 2px 12px rgba(79,110,247,.08);
}

/* ── Layout shell ───────────────────────────────────── */
.pv3-wrap { padding: 16px 0 48px; background: var(--pv3-bg); }

.pv3-shell {
    display: grid;
    grid-template-columns: 270px 1fr;
    gap: 20px;
    align-items: start;
}

/* ── Panel (left sidebar) ───────────────────────────── */
.pv3-panel {
    display: flex;
    flex-direction: column;
    gap: 12px;
    position: sticky;
    top: 20px;
}

.pv3-panel-block {
    background: var(--pv3-white);
    border: 1px solid var(--pv3-border);
    border-radius: var(--pv3-r);
    box-shadow: var(--pv3-shadow);
    padding: 22px 20px;
}

/* Identity block */
.pv3-thumb {
    width: 50px; height: 50px; border-radius: 10px;
    border: 1.5px solid var(--pv3-accent-mid);
    background: var(--pv3-accent-soft);
    display: flex; align-items: center; justify-content: center;
    overflow: hidden; margin-bottom: 14px;
}
.pv3-thumb img { width: 100%; height: 100%; object-fit: cover; }

.pv3-proj-id {
    font-size: 10px; font-weight: 700; letter-spacing: .1em;
    text-transform: uppercase; color: var(--pv3-accent); margin: 0 0 5px;
}
.pv3-proj-name {
    font-size: 17px; font-weight: 700; color: var(--pv3-text);
    margin: 0 0 10px; line-height: 1.35;
}
.pv3-proj-desc {
    font-size: 12.5px; line-height: 1.7; color: var(--pv3-muted);
    margin: 0; max-height: 96px; overflow-y: auto;
    scrollbar-width: thin; scrollbar-color: var(--pv3-border) transparent;
}
.pv3-proj-desc::-webkit-scrollbar { width: 3px; }
.pv3-proj-desc::-webkit-scrollbar-thumb { background: var(--pv3-border); border-radius: 2px; }

/* Progress */
.pv3-prog-header { display: flex; justify-content: space-between; margin-bottom: 8px; }
.pv3-prog-label { font-size: 11px; font-weight: 600; letter-spacing: .05em; text-transform: uppercase; color: var(--pv3-muted); }
.pv3-prog-pct   { font-size: 12px; font-weight: 700; color: var(--pv3-accent); }
.pv3-track { height: 6px; border-radius: 99px; background: var(--pv3-accent-soft); overflow: hidden; }
.pv3-fill  { height: 100%; border-radius: 99px; background: var(--pv3-accent); transition: width .6s cubic-bezier(.4,0,.2,1); }

/* Section label */
.pv3-sec-label {
    font-size: 10px; font-weight: 700; letter-spacing: .1em; text-transform: uppercase;
    color: var(--pv3-muted); margin: 0 0 12px;
}

/* Date rows */
.pv3-date-row { display: flex; justify-content: space-between; align-items: center; padding: 7px 0; border-bottom: 1px solid var(--pv3-border); }
.pv3-date-row:last-child { border-bottom: none; padding-bottom: 0; }
.pv3-date-row:first-child { padding-top: 0; }
.pv3-date-key { font-size: 12px; color: var(--pv3-muted); }
.pv3-date-val { font-size: 12px; font-weight: 600; color: var(--pv3-text); }

/* Status badges */
.pv3-badge-row { display: flex; flex-direction: column; gap: 8px; }
.pv3-badge {
    display: flex; align-items: center; justify-content: space-between;
    background: var(--pv3-panel-bg);
    border: 1px solid var(--pv3-border);
    border-radius: var(--pv3-r-sm); padding: 9px 12px;
    font-size: 12px; color: var(--pv3-muted);
}
.pv3-badge i { font-size: 13px; color: var(--pv3-accent); margin-right: 7px; }
.pv3-badge strong { font-weight: 700; color: var(--pv3-text); }

/* Quick links */
.pv3-qlinks { display: flex; flex-direction: column; gap: 2px; }
.pv3-qlink {
    display: flex; align-items: center; gap: 9px;
    padding: 9px 10px; border-radius: var(--pv3-r-sm);
    font-size: 12.5px; font-weight: 500; color: var(--pv3-muted);
    text-decoration: none; transition: all .14s;
}
.pv3-qlink i { font-size: 14px; width: 18px; text-align: center; color: var(--pv3-accent); }
.pv3-qlink:hover { background: var(--pv3-accent-soft); color: var(--pv3-accent); }

/* ── Right content ──────────────────────────────────── */
.pv3-content { display: flex; flex-direction: column; gap: 16px; }

/* Cards */
.pv3-card {
    background: var(--pv3-white);
    border: 1px solid var(--pv3-border);
    border-radius: var(--pv3-r);
    box-shadow: var(--pv3-shadow);
    overflow: hidden;
}
.pv3-card-head {
    display: flex; align-items: center; justify-content: space-between;
    padding: 18px 22px 0;
}
.pv3-card-title {
    font-size: 11px; font-weight: 700; letter-spacing: .08em; text-transform: uppercase;
    color: var(--pv3-muted); display: flex; align-items: center; gap: 6px; margin: 0;
}
.pv3-card-title i { font-size: 13px; color: var(--pv3-accent); }
.pv3-card-body { padding: 18px 22px 22px; }

/* Two-col row */
.pv3-grid-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }

/* Members */
.pv3-members { display: flex; flex-direction: column; }
.pv3-member {
    display: flex; align-items: center; gap: 11px;
    padding: 9px 0; border-bottom: 1px solid var(--pv3-border);
}
.pv3-member:last-child { border-bottom: none; padding-bottom: 0; }
.pv3-member:first-child { padding-top: 0; }
.pv3-avatar {
    width: 36px; height: 36px; border-radius: 50%;
    background: var(--pv3-accent-soft); color: var(--pv3-accent);
    display: flex; align-items: center; justify-content: center;
    font-size: 12px; font-weight: 700; flex-shrink: 0;
}
.pv3-member-name { font-size: 13px; font-weight: 600; color: var(--pv3-text); margin: 0; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.pv3-member-role { font-size: 11.5px; color: var(--pv3-muted); margin: 0; }
.pv3-members-scroll { max-height: 288px; overflow-y: auto; scrollbar-width: thin; scrollbar-color: var(--pv3-border) transparent; }
.pv3-members-scroll::-webkit-scrollbar { width: 3px; }
.pv3-members-scroll::-webkit-scrollbar-thumb { background: var(--pv3-border); border-radius: 2px; }
.pv3-loading { font-size: 13px; color: var(--pv3-muted); padding: 18px 0; text-align: center; }

.pv3-add-btn {
    width: 28px; height: 28px; border-radius: var(--pv3-r-sm);
    background: var(--pv3-accent-soft); color: var(--pv3-accent);
    border: 1px solid var(--pv3-accent-mid);
    display: flex; align-items: center; justify-content: center;
    text-decoration: none; font-size: 13px; transition: all .14s;
}
.pv3-add-btn:hover { background: var(--pv3-accent); color: #fff; }

/* Activity */
.pv3-activity-scroll { max-height: 360px; overflow-y: auto; scrollbar-width: thin; scrollbar-color: var(--pv3-border) transparent; }
.pv3-activity-scroll::-webkit-scrollbar { width: 3px; }
.pv3-activity-scroll::-webkit-scrollbar-thumb { background: var(--pv3-border); border-radius: 2px; }

.pv3-activity { display: flex; align-items: flex-start; gap: 12px; padding: 11px 0; border-bottom: 1px solid var(--pv3-border); }
.pv3-activity:last-child { border-bottom: none; padding-bottom: 0; }
.pv3-activity:first-child { padding-top: 0; }
.pv3-act-icon {
    width: 32px; height: 32px; border-radius: 8px; flex-shrink: 0;
    display: flex; align-items: center; justify-content: center;
    background: var(--pv3-accent-soft); color: var(--pv3-accent); font-size: 13px;
}
.pv3-act-type   { font-size: 13px; font-weight: 600; color: var(--pv3-text); margin: 0 0 2px; }
.pv3-act-remark { font-size: 12px; color: var(--pv3-muted); margin: 0; line-height: 1.5; }
.pv3-act-time   { font-size: 11px; color: var(--pv3-muted); white-space: nowrap; padding-top: 2px; margin-left: auto; }

/* Empty */
.pv3-empty { text-align: center; padding: 28px 0; font-size: 13px; color: var(--pv3-muted); }
.pv3-empty i { font-size: 26px; display: block; margin-bottom: 8px; opacity: .3; }

/* Top action buttons */
.pv3-btn {
    display: inline-flex; align-items: center; gap: 6px;
    padding: 7px 14px; border-radius: var(--pv3-r-sm);
    font-size: 12.5px; font-weight: 600;
    border: 1px solid var(--pv3-border); background: var(--pv3-white);
    color: var(--pv3-text); text-decoration: none; transition: all .14s;
}
.pv3-btn:hover { background: var(--pv3-bg); color: var(--pv3-text); }
.pv3-btn--primary { background: var(--pv3-accent); color: #fff; border-color: var(--pv3-accent); }
.pv3-btn--primary:hover { background: #3d5ce0; color: #fff; }
.pv3-icon-btn {
    width: 34px; height: 34px; border-radius: var(--pv3-r-sm);
    display: flex; align-items: center; justify-content: center;
    border: 1px solid var(--pv3-border); background: var(--pv3-white);
    color: var(--pv3-accent); text-decoration: none; transition: all .14s;
}
.pv3-icon-btn:hover { background: var(--pv3-accent-soft); }
.float-end a > i { color: unset; }

/* Responsive */
@media (max-width: 1024px) {
    .pv3-shell { grid-template-columns: 240px 1fr; }
}
@media (max-width: 820px) {
    .pv3-shell { grid-template-columns: 1fr; }
    .pv3-panel { position: static; }
    .pv3-grid-2 { grid-template-columns: 1fr; }
}
.list-group-item{
    margin-bottom: 15px;
}
</style>

<div class="pv3-wrap mt-2">
<div class="container-fluid px-3 px-md-4">
<div class="pv3-shell">

    {{-- ══════════════════════════════
         LEFT PANEL
         ══════════════════════════════ --}}
    <aside class="pv3-panel">

        {{-- Identity --}}
        <div class="pv3-panel-block">
            @php
                $image = $project->project_image
                    ? \App\Models\Utility::get_file($project->project_image)
                    : asset('assets/assestsnew/Manage_projects.svg');
            @endphp
            <div class="pv3-thumb">
                <img src="{{ $image }}" alt="{{ $project->project_name }}">
            </div>
            <p class="pv3-proj-id">#{{ $project->id }}</p>
            <h4 class="pv3-proj-name">{{ $project->project_name }}</h4>
            <p class="pv3-proj-desc">{{ $project->description }}</p>
        </div>

        {{-- Progress --}}
        <div class="pv3-panel-block">
            <div class="pv3-prog-header">
                <span class="pv3-prog-label">{{ __('Progress') }}</span>
                <span class="pv3-prog-pct">{{ $projectProgress }}%</span>
            </div>
            <div class="pv3-track">
                <div class="pv3-fill" style="width:{{ $projectProgress }}%"></div>
            </div>
        </div>

        {{-- Timeline --}}
        <div class="pv3-panel-block">
            <p class="pv3-sec-label">{{ __('Timeline') }}</p>
            <div class="pv3-date-row">
                <span class="pv3-date-key">{{ __('Onboard') }}</span>
                <span class="pv3-date-val">{{ Utility::getDateFormated($project->on_board_date) }}</span>
            </div>
            <div class="pv3-date-row">
                <span class="pv3-date-key">{{ __('Renewal') }}</span>
                <span class="pv3-date-val">{{ Utility::getDateFormated($project->renewal_date) }}</span>
            </div>
            <div class="pv3-date-row">
                <span class="pv3-date-key">{{ __('Support Start') }}</span>
                <span class="pv3-date-val">{{ Utility::getDateFormated($project->support_start_date) }}</span>
            </div>
            <div class="pv3-date-row">
                <span class="pv3-date-key">{{ __('Support End') }}</span>
                <span class="pv3-date-val">{{ Utility::getDateFormated($project->support_end_date ?? $project->support_start_date) }}</span>
            </div>
        </div>

        {{-- Status --}}
        <div class="pv3-panel-block">
            <p class="pv3-sec-label">{{ __('Status') }}</p>
            <div class="pv3-badge-row">
                <div class="pv3-badge">
                    <span><i class="ti ti-chart-donut"></i>{{ __('Completion') }}</span>
                    <strong>{{ $projectProgress }}%</strong>
                </div>
                <div class="pv3-badge">
                    <span><i class="ti ti-bolt"></i>{{ __('Status') }}</span>
                    <strong>{{ __('Active') }}</strong>
                </div>
            </div>
        </div>

        {{-- Quick links --}}
        <div class="pv3-panel-block">
            <p class="pv3-sec-label">{{ __('Quick Links') }}</p>
            <div class="pv3-qlinks">
                <a href="{{ route('organization.projects.tasks.index', $project->id) }}" class="pv3-qlink">
                    <i class="ti ti-checklist"></i> {{ __('View Tasks') }}
                </a>
                <a href="{{ route('organization.task.bug', $project->id) }}" class="pv3-qlink">
                    <i class="ti ti-bug"></i> {{ __('Bug Reports') }}
                </a>
                @can('project_management')
                    <a href="#"
                       data-size="lg"
                       data-url="{{ route('organization.projects.edit', $project->id) }}"
                       data-ajax-popup="true"
                       class="pv3-qlink">
                        <i class="ti ti-pencil"></i> {{ __('Edit Project') }}
                    </a>
                @endcan
            </div>
        </div>

    </aside>

    {{-- ══════════════════════════════
         RIGHT CONTENT
         ══════════════════════════════ --}}
    <div class="pv3-content">

        {{-- Row 1: Chart + Members --}}
        <div class="pv3-grid-2">

            {{-- Task Status Donut --}}
            <div class="pv3-card">
                <div class="pv3-card-head">
                    <p class="pv3-card-title">
                        <i class="ti ti-chart-donut-3"></i>{{ __('Task Status') }}
                    </p>
                </div>
                <div class="pv3-card-body">
                    <div id="donutchart"></div>
                </div>
            </div>

            {{-- Members --}}
            <div class="pv3-card">
                <div class="pv3-card-head">
                    <p class="pv3-card-title">
                        <i class="ti ti-users"></i>{{ __('Team Members') }}
                    </p>
                    @can('project_management')
                        <a href="#"
                           data-size="lg"
                           data-url="{{ route('organization.invite.project.member.view', $project->id) }}"
                           data-ajax-popup="true"
                           data-bs-toggle="tooltip"
                           title="{{ __('Add Member') }}"
                           class="pv3-add-btn">
                            <i class="ti ti-plus"></i>
                        </a>
                    @endcan
                </div>
                <div class="pv3-card-body">
                    <div class="pv3-members-scroll">
                        <div class="pv3-members" id="project_users">
                            <div class="pv3-loading">{{ __('Loading...') }}</div>
                        </div>
                    </div>
                </div>
            </div>

        </div>{{-- /grid-2 --}}

        {{-- Row 2: Activity Log --}}
        <div class="pv3-card">
            <div class="pv3-card-head">
                <p class="pv3-card-title">
                    <i class="ti ti-activity"></i>{{ __('Activity Log') }}
                </p>
            </div>
            <div class="pv3-card-body">
                @if($project->activities->isEmpty())
                    <div class="pv3-empty">
                        <i class="ti ti-mood-empty"></i>
                        {{ __('No activity recorded yet.') }}
                    </div>
                @else
                    <div class="pv3-activity-scroll">
                        @foreach ($project->activities as $activity)
                            <div class="pv3-activity">
                                <div class="pv3-act-icon">
                                    <i class="ti {{ $activity->logIcon($activity->log_type) }}"></i>
                                </div>
                                <div style="flex:1;min-width:0">
                                    <p class="pv3-act-type">{{ __($activity->log_type) }}</p>
                                    <p class="pv3-act-remark">{!! $activity->getRemark() !!}</p>
                                </div>
                                <span class="pv3-act-time">{{ $activity->created_at->diffForHumans() }}</span>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>

    </div>{{-- /pv3-content --}}

</div>{{-- /pv3-shell --}}
</div>
</div>

@endsection

<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
<script type="text/javascript">
    google.charts.load("current", { packages: ["corechart"] });
    google.charts.setOnLoadCallback(drawChart);

    function drawChart() {
        var data = google.visualization.arrayToDataTable([
            ["Stage", "Percentage"],
            @foreach ($stages as $stage)
                ["{{ $stage->name }}", {{ $stage->percentage }}],
            @endforeach
        ]);

        var options = {
            pieHole: 0.45,
            legend: {
                position: "bottom",
                alignment: "center",
                textStyle: { color: "#64748b", fontSize: 12 },
            },
            chartArea: { width: "90%", height: "68%" },
            pieSliceText: "percentage",
            pieSliceTextStyle: { color: "#fff", fontSize: 12 },
            colors: ["#4f6ef7", "#10b981", "#f59e0b", "#ef4444", "#8b5cf6", "#06b6d4"],
            backgroundColor: "transparent",
        };

        var chart = new google.visualization.PieChart(document.getElementById("donutchart"));
        chart.draw(data, options);
        window.addEventListener("resize", () => chart.draw(data, options));
    }
</script>