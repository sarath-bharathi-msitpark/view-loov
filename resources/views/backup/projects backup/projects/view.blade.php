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
                chart: {
                    type: 'area',
                    height: 60,
                    sparkline: {
                        enabled: true,
                    },
                },
                colors: ["#ffa21d"],
                dataLabels: {
                    enabled: false
                },
                stroke: {
                    curve: 'smooth',
                    width: 2,
                },
                series: [{
                    name: 'Bandwidth',
                    data: {{ json_encode(array_map('intval', $project_data['timesheet_chart']['chart'])) }}
                }],

                tooltip: {
                    followCursor: false,
                    fixed: {
                        enabled: false
                    },
                    x: {
                        show: false
                    },
                    y: {
                        title: {
                            formatter: function (seriesName) {
                                return ''
                            }
                        }
                    },
                    marker: {
                        show: false
                    }
                }
            }
            var chart = new ApexCharts(document.querySelector("#timesheet_chart"), options);
            chart.render();
        })();

        (function () {
            var options = {
                chart: {
                    type: 'area',
                    height: 60,
                    sparkline: {
                        enabled: true,
                    },
                },
                colors: ["#ffa21d"],
                dataLabels: {
                    enabled: false
                },
                stroke: {
                    curve: 'smooth',
                    width: 2,
                },
                series: [{
                    name: 'Bandwidth',
                    data: {{ json_encode($project_data['task_chart']['chart']) }}
                }],

                tooltip: {
                    followCursor: false,
                    fixed: {
                        enabled: false
                    },
                    x: {
                        show: false
                    },
                    y: {
                        title: {
                            formatter: function (seriesName) {
                                return ''
                            }
                        }
                    },
                    marker: {
                        show: false
                    }
                }
            }
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
                    method: 'POST',
                    dataType: 'json',
                    data: {
                        'project_id': project_id,
                        'user_id': user_id,
                        "_token": "{{ csrf_token() }}"
                    },
                    success: function (data) {
                        if (data.code == '200') {
                            show_toastr(data.status, data.success, 'success')
                            setInterval('location.reload()', 5000);
                            loadProjectUser();
                        } else if (data.code == '404') {
                            show_toastr(data.status, data.errors, 'error')
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
                data: {
                    project_id: project_id
                },
                beforeSend: function () {
                    $('#project_users').html(
                        '<tr><th colspan="2" class="h6 text-center pt-5">{{ __('Loading...') }}</th></tr>');
                },
                success: function (data) {
                    mainEle.html(data.html);
                    $('[id^=fire-modal]').remove();
                    mainEle.find('.avatar img').remove();
                    // loadConfirm();
                }
            });
        }
    </script>

    {{-- share project copy link --}}
    <script>
        function copyToClipboard(element) {

            var copyText = element.id;
            navigator.clipboard.writeText(copyText);
            // document.addEventListener('copy', function (e) {
            //     e.clipboardData.setData('text/plain', copyText);
            //     e.preventDefault();
            // }, true);
            //
            // document.execCommand('copy');
            show_toastr('success', 'Url copied to clipboard', 'success');
        }
    </script>
@endpush

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('organization.projects.index') }}">{{ __('Project') }}</a></li>
    <li class="breadcrumb-item">{{ ucwords($project->project_name) }}</li>
@endsection

@section('action-btn')
    <div class="float-end d-flex">

        <a href="{{ route('organization.task.bug', $project->id) }}"
           class="btn_for_status me-1">
            {{ __('Bug Report') }}
        </a>

        <a href="{{ route('organization.projects.tasks.index', $project->id) }}" class="btn_for_status me-1">
            {{ __('Task') }}
        </a>

        @can('project_management')
            <a href="#" data-size="lg" data-url="{{ route('organization.projects.edit', $project->id) }}"
               data-ajax-popup="true"
               data-bs-toggle="tooltip" title="{{ __('Edit Project') }}" class="rounded_add_btn me-1">
                <i class="ti ti-pencil text-primary"></i>
            </a>
        @endcan

    </div>
@endsection

@section('content')
    @include('company.layouts.partials.nav')

    <div class="row pt-5">
        <div class="col-12">
            <div class="row align-items-stretch g-3">

                <div class="col-xl-8 col-12 pe-md-3 mb-4">
                    <div class="card h-100" style="background: #fff;">
                        <div class="card-body d-flex flex-column justify-content-between">

                            <div class="d-flex align-items-center gap-3 flex-wrap mb-3">
                                <img
                                        src="{{ asset('assets/assestsnew/Manage_projects.svg') }}"
                                        class="img-user p-2 wid-45 rounded border-2 border border-primary"
                                        alt="Male"
                                />
                                <h5 class="mb-0">{{ $project->project_name }}</h5>
                            </div>

                            <div class="mb-3">
                                @php
                                    $projectProgress = $project->project_progress($project, $last_task->id)['percentage'];
                                @endphp
                                <div class="progress-wrapper mt-2">
                                      <span class="progress-percentage">
                                        <small class="fw-bold">{{ __('Completed:') }} :</small>
                                        {{ $projectProgress }}
                                      </span>
                                    <div class="progress progress-xs mt-2" style="height: 6px;">
                                        <div
                                                class="progress-bar bg-info"
                                                role="progressbar"
                                                aria-valuenow="{{ $projectProgress }}"
                                                aria-valuemin="0"
                                                aria-valuemax="100"
                                                style="width: {{ $projectProgress }};"
                                        ></div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-12 description_seta_scroll">
                                    <p class="mb-0">{{ $project->description }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-4 col-12 mb-4">
                    <div class="card h-100 onboad_box_all mb-0">
                        <div class="card-body text-white">

                            <div class="d-flex justify-content-between flex-wrap mb-3">
                                <div class="flex-column">
                                    <span class="text-sm">{{ __('Onboard Date') }}</span>
                                    <h5 class="text-nowrap mb-0">{{ Utility::getDateFormated($project->on_board_date) }}</h5>
                                </div>
                                <div class="flex-column">
                                    <span class="text-sm">{{ __('Renewal Date') }}</span>
                                    <h5 class="text-nowrap mb-0">{{ Utility::getDateFormated($project->renewal_date) }}</h5>
                                </div>
                            </div>

                            <div class="d-flex justify-content-between flex-wrap">
                                <div class="flex-column">
                                    <span class="text-sm">{{ __('Support Start Date') }}</span>
                                    <h5 class="text-nowrap mb-0">{{ Utility::getDateFormated($project->support_start_date) }}</h5>
                                </div>
                                <div class="flex-column">
                                    <span class="text-sm">{{ __('Support End Date') }}</span>
                                    <h5 class="text-nowrap mb-0">{{ Utility::getDateFormated($project->support_start_date) }}</h5>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>

            </div>
        </div>


        <div class="col-xl-4 mb-4">
            <div class="card h-100 mb-0" style="background: #fff;">
                <div class="card-header" style="padding-bottom: 15px;">
                    <h5>Total Project</h5>
                </div>
                <hr class="my-0"/>
                <div class="card-body">
                    <div id="donutchart"></div>
                </div>
            </div>
        </div>

        <div class="col-xl-4 mb-4">
            <div class="card h-100 mb-0" style="background: #fff;">
                <div class="card-header" style="padding-bottom: 15px;">
                    <div class="d-flex align-items-center justify-content-between">
                        <h5>{{ __('Members') }}</h5>
                        <div class="float-end">
                            @can('project_management')
                                <a href="#" data-size="lg"
                                   data-url="{{ route('organization.invite.project.member.view', $project->id) }}"
                                   data-ajax-popup="true"
                                   data-bs-toggle="tooltip" title="" class="rounded_add_btn"
                                   data-bs-original-title="{{ __('Add Member') }}">
                                    <i class="ti ti-plus text-primary"></i>
                                </a>
                            @endcan
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <ul class="list-group list-group-flush list w-100 mt-3 verticle_scroll_maxiheight"
                        id="project_users">
                    </ul>
                </div>
            </div>
        </div>

        <div class="col-xl-4 mb-4">
            <div class="card h-100 mb-0" style="background: #fff;">
                <div class="card-header" style="padding-bottom: 15px;">
                    <h5>{{ __('Activity Log') }}</h5>
                    <small>{{ __('Activity Log of this project') }}</small>
                </div>
                <div class="card-body vertical-scroll-cards verticle_scroll_maxiheight">
                    @foreach ($project->activities as $activity)
                        <div class="card p-2 mb-2">
                            <div class="d-flex align-items-center justify-content-between">
                                <div class="d-flex align-items-center">
                                    <div class="theme-avtar bg-primary badge">
                                        <i class="ti {{ $activity->logIcon($activity->log_type) }}"></i>
                                    </div>
                                    <div class="ms-3">
                                        <h6 class="mb-0">{{ __($activity->log_type) }}</h6>
                                        <p class="text-muted text-sm mb-0">{!! $activity->getRemark() !!}</p>
                                    </div>
                                </div>
                                <p class="text-muted text-sm mb-0">{{ $activity->created_at->diffForHumans() }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!--<div class="col-md-4 col-12 mb-4">-->
        <!--    <div class="card h-100 mb-0" style="background: #fff;">-->
        <!--        <div class="card-header" style="padding-bottom: 15px;">-->
        <!--            <div class="d-flex align-items-center justify-content-between">-->
        <!--                <h5>{{ __('Milestones') }} ({{ count($project->milestones) }})</h5>-->
        <!--                <div class="float-end">-->
        <!--                    @can('project_management')-->
        <!--                        <a href="#" data-size="md"-->
        <!--                           data-url="{{ route('organization.project.milestone', $project->id) }}"-->
        <!--                           data-ajax-popup="true" data-bs-toggle="tooltip" title=""-->
        <!--                           class="rounded_add_btn" data-bs-original-title="{{ __('Create Milestone') }}">-->
        <!--                            <i class="ti ti-plus text-primary"></i>-->
        <!--                        </a>-->
        <!--                    @endcan-->
        <!--                </div>-->
        <!--            </div>-->
        <!--        </div>-->
        <!--        <div class="card-body">-->
        <!--            <ul class="list-group list-group-flush verticle_scroll_maxiheight">-->
        <!--                @if ($project->milestones->count() > 0)-->
        <!--                    @foreach ($project->milestones as $milestone)-->
        <!--                        <li class="list-group-item px-0">-->
        <!--                            <div class="d-flex flex-wrap align-items-center justify-content-between">-->
        <!--                                <div class="col-sm-auto mb-3 mb-sm-0">-->
        <!--                                    <div class="d-flex align-items-center">-->
        <!--                                        <div class="div">-->
        <!--                                            <h6 class="m-0">{{ $milestone->title }}-->
        <!--                                                <span-->
        <!--                                                    class="badge-xs badge bg-{{ \App\Models\Project::$status_color[$milestone->status] }} p-2 px-3 rounded">{{ __(\App\Models\Project::$project_status[$milestone->status]) }}</span>-->
        <!--                                            </h6>-->
        <!--                                            <small-->
        <!--                                                class="text-muted">{{ $milestone->tasks->count() . ' view.blade.php' . __('Tasks') }}</small>-->
        <!--                                        </div>-->
        <!--                                    </div>-->
        <!--                                </div>-->
        <!--                                <div class="col-sm-auto text-sm-end align-items-center">-->

        <!--                                    <div class="action-btn me-2" style="width:max-content;">-->
        <!--                                        <a href="#" data-size="lg"-->
        <!--                                           data-url="{{ route('organization.project.milestone.show', $milestone->id) }}"-->
        <!--                                           data-ajax-popup="true" data-bs-toggle="tooltip"-->
        <!--                                           title="{{ __('View') }}" class="btn btn-sm bg-warning">-->
        <!--                                            <i class="ti ti-eye text-white"></i>-->
        <!--                                        </a>-->
        <!--                                    </div>-->

        <!--                                    <div class="action-btn me-2" style="width:max-content;">-->
        <!--                                        <a href="#" data-size="md"-->
        <!--                                           data-url="{{ route('organization.project.milestone.edit', $milestone->id) }}"-->
        <!--                                           data-ajax-popup="true" data-bs-toggle="tooltip"-->
        <!--                                           title="{{ __('Edit') }}"-->
        <!--                                           data-title="{{ __('Edit Milestone') }}"-->
        <!--                                           class="btn btn-sm bg-info">-->
        <!--                                            <i class="ti ti-pencil text-white"></i>-->
        <!--                                        </a>-->
        <!--                                    </div>-->

        <!--                                    <div class="action-btn" style="width:max-content;">-->
        <!--                                        {!! Form::open(['method' => 'DELETE', 'route' => ['organization.project.milestone.destroy', $milestone->id]]) !!}-->
        <!--                                        <a href="#"-->
        <!--                                           class="btn btn-sm  align-items-center bs-pass-para"-->
        <!--                                           data-bs-toggle="tooltip" title="{{ __('Delete') }}"><i-->
        <!--                                                class="ti ti-trash text-white"></i></a>-->

        <!--                                        {!! Form::close() !!}-->
        <!--                                    </div>-->

        <!--                                </div>-->
        <!--                            </div>-->
        <!--                        </li>-->
        <!--                    @endforeach-->
        <!--                @else-->
        <!--                    <div class="py-5">-->
        <!--                        <h6 class="h6 text-center">{{ __('No Milestone Found.') }}</h6>-->
        <!--                    </div>-->
        <!--                @endif-->
        <!--            </ul>-->

        <!--        </div>-->
        <!--    </div>-->
        <!--</div>-->


        {{--        <div class="col-lg-6 col-md-6">--}}
        {{--            <div class="card activity-scroll">--}}
        {{--                <div class="card-header">--}}
        {{--                    <h5>{{ __('Attachments') }}</h5>--}}
        {{--                    <small>{{ __('Attachment that uploaded in this project') }}</small>--}}
        {{--                </div>--}}
        {{--                <div class="card-body">--}}
        {{--                    <ul class="list-group list-group-flush">--}}
        {{--                        @if ($project->projectAttachments()->count() > 0)--}}
        {{--                            @foreach ($project->projectAttachments() as $attachment)--}}
        {{--                                <li class="list-group-item px-0">--}}
        {{--                                    <div class="row align-items-center justify-content-between">--}}
        {{--                                        <div class="col mb-3 mb-sm-0">--}}
        {{--                                            <div class="d-flex align-items-center">--}}
        {{--                                                <div class="div">--}}
        {{--                                                    <h6 class="m-0">{{ $attachment->name }}</h6>--}}
        {{--                                                    <small class="text-muted">{{ $attachment->file_size }}</small>--}}
        {{--                                                </div>--}}
        {{--                                            </div>--}}
        {{--                                        </div>--}}

        {{--                                        @php--}}
        {{--                                            $file = \App\Models\Utility::get_file('uploads/tasks/');--}}
        {{--                                        @endphp--}}

        {{--                                        <div class="col-auto text-sm-end d-flex align-items-center">--}}
        {{--                                            <div class="action-btn me-2">--}}
        {{--                                                <a href="{{ $file . $attachment->file }}" data-bs-toggle="tooltip"--}}
        {{--                                                   title="{{ __('Download') }}" class="btn btn-sm bg-primary" download>--}}
        {{--                                                    <i class="ti ti-download text-white"></i>--}}
        {{--                                                </a>--}}
        {{--                                            </div>--}}
        {{--                                        </div>--}}
        {{--                                    </div>--}}
        {{--                                </li>--}}
        {{--                            @endforeach--}}
        {{--                        @else--}}
        {{--                            <div class="py-5">--}}
        {{--                                <h6 class="h6 text-center">{{ __('No Attachments Found.') }}</h6>--}}
        {{--                            </div>--}}
        {{--                        @endif--}}
        {{--                    </ul>--}}

        {{--                </div>--}}
        {{--            </div>--}}
        {{--        </div>--}}
    </div>
@endsection


<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
<script type="text/javascript">
    google.charts.load("current", {packages: ["corechart"]});
    google.charts.setOnLoadCallback(drawChart);

    function drawChart() {
        var data = google.visualization.arrayToDataTable([
            ["Project Status", "Percentage"],
            ["In Progress", 40],
            ["Pending", 25],
            ["Completed", 20],
            ["On Hold", 15],
        ]);

        var options = {
            pieHole: 0.4,
            legend: {
                position: "bottom",
                alignment: "center",
                textStyle: {color: "#555", fontSize: 13},
            },
            chartArea: {width: "90%", height: "70%"},
            pieSliceText: "percentage",
            pieSliceTextStyle: {
                color: "#fff",
                fontSize: 12,
            },
            colors: ["#2D8981", "#316FF6", "#F63182", "#F6E331"],
            backgroundColor: "transparent",
        };

        var chart = new google.visualization.PieChart(
            document.getElementById("donutchart")
        );
        chart.draw(data, options);

        // Make chart responsive
        window.addEventListener("resize", () => chart.draw(data, options));
    }
</script>
