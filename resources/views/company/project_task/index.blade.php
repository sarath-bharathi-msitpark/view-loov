@extends('company.layouts.company')
@section('page-title')
    {{ ucwords($project->project_name) . __("'s Tasks") }}
@endsection

@push('css-page')
    <link rel="stylesheet" href="{{ asset('css/summernote/summernote-bs4.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/plugins/dragula.min.css') }}" id="main-style-link">
@endpush
@push('script-page')
    <script src="{{ asset('css/summernote/summernote-bs4.js') }}"></script>
    <script src="{{ asset('assets/js/plugins/dragula.min.js') }}"></script>
    <script>
        // Add this code after your existing dragula initialization
        // Place it right after the dragula initialization block in your script section

        // Add this code after your existing dragula initialization
        // Place it right after the dragula initialization block in your script section

        !function (a) {
            "use strict";
            var t = function () {
                this.$body = a("body")
            };
            t.prototype.init = function () {
                a('[data-plugin="dragula"]').each(function () {
                    var t = a(this).data("containers"),
                        n = [];
                    if (t)
                        for (var i = 0; i < t.length; i++) n.push(a("#" + t[i])[0]);
                    else n = [a(this)[0]];
                    var r = a(this).data("handleclass");

                    // Auto-scroll configuration
                    var scrollSpeed = 15; // Pixels to scroll per interval
                    var scrollZone = 100; // Distance from edge to trigger scroll (in pixels)
                    var scrollInterval;
                    var verticalScrollInterval;
                    var scrollContainer = a(this)[0]; // The horizontal scroll container
                    var currentVerticalContainer = null; // Track which vertical container is being scrolled

                    var dragulaInstance = r ? dragula(n, {
                        moves: function (a, t, n) {
                            return n.classList.contains(r)
                        }
                    }) : dragula(n);

                    // Add drag event listener for auto-scrolling
                    dragulaInstance.on('drag', function (el, source) {
                        // Start checking mouse position for auto-scroll
                        a(document).on('mousemove.dragula-autoscroll', function (e) {
                            if (scrollInterval) {
                                clearInterval(scrollInterval);
                            }
                            if (verticalScrollInterval) {
                                clearInterval(verticalScrollInterval);
                            }

                            var containerRect = scrollContainer.getBoundingClientRect();
                            var mouseX = e.clientX;
                            var mouseY = e.clientY;

                            // === HORIZONTAL SCROLL (for main container) ===
                            // Check if mouse is near left edge
                            if (mouseX < containerRect.left + scrollZone && scrollContainer.scrollLeft > 0) {
                                scrollInterval = setInterval(function () {
                                    scrollContainer.scrollLeft -= scrollSpeed;
                                    if (scrollContainer.scrollLeft <= 0) {
                                        clearInterval(scrollInterval);
                                    }
                                }, 20);
                            }
                            // Check if mouse is near right edge
                            else if (mouseX > containerRect.right - scrollZone) {
                                var maxScroll = scrollContainer.scrollWidth - scrollContainer.clientWidth;
                                if (scrollContainer.scrollLeft < maxScroll) {
                                    scrollInterval = setInterval(function () {
                                        scrollContainer.scrollLeft += scrollSpeed;
                                        if (scrollContainer.scrollLeft >= maxScroll) {
                                            clearInterval(scrollInterval);
                                        }
                                    }, 20);
                                }
                            }
                            // Stop horizontal scrolling if mouse is not near edges
                            else {
                                if (scrollInterval) {
                                    clearInterval(scrollInterval);
                                    scrollInterval = null;
                                }
                            }

                            // === VERTICAL SCROLL (for .main_heighttrack containers) ===
                            // Find which vertical container the mouse is over
                            var verticalContainers = a('.main_heighttrack');
                            currentVerticalContainer = null;

                            verticalContainers.each(function () {
                                var rect = this.getBoundingClientRect();
                                if (mouseX >= rect.left && mouseX <= rect.right &&
                                    mouseY >= rect.top && mouseY <= rect.bottom) {
                                    currentVerticalContainer = this;
                                    return false; // break loop
                                }
                            });

                            if (currentVerticalContainer) {
                                var verticalRect = currentVerticalContainer.getBoundingClientRect();
                                var scrollZoneVertical = 80; // Vertical scroll zone

                                // Check if mouse is near top edge
                                if (mouseY < verticalRect.top + scrollZoneVertical && currentVerticalContainer.scrollTop > 0) {
                                    verticalScrollInterval = setInterval(function () {
                                        currentVerticalContainer.scrollTop -= scrollSpeed;
                                        if (currentVerticalContainer.scrollTop <= 0) {
                                            clearInterval(verticalScrollInterval);
                                        }
                                    }, 20);
                                }
                                // Check if mouse is near bottom edge
                                else if (mouseY > verticalRect.bottom - scrollZoneVertical) {
                                    var maxVerticalScroll = currentVerticalContainer.scrollHeight - currentVerticalContainer.clientHeight;
                                    if (currentVerticalContainer.scrollTop < maxVerticalScroll) {
                                        verticalScrollInterval = setInterval(function () {
                                            currentVerticalContainer.scrollTop += scrollSpeed;
                                            if (currentVerticalContainer.scrollTop >= maxVerticalScroll) {
                                                clearInterval(verticalScrollInterval);
                                            }
                                        }, 20);
                                    }
                                }
                                // Stop vertical scrolling if mouse is not near edges
                                else {
                                    if (verticalScrollInterval) {
                                        clearInterval(verticalScrollInterval);
                                        verticalScrollInterval = null;
                                    }
                                }
                            } else {
                                // Not over any vertical container, stop vertical scrolling
                                if (verticalScrollInterval) {
                                    clearInterval(verticalScrollInterval);
                                    verticalScrollInterval = null;
                                }
                            }
                        });
                    });

                    // Clean up on drop or cancel
                    dragulaInstance.on('drop', function (el, target, source, sibling) {
                        // Clear auto-scroll
                        a(document).off('mousemove.dragula-autoscroll');
                        if (scrollInterval) {
                            clearInterval(scrollInterval);
                            scrollInterval = null;
                        }
                        if (verticalScrollInterval) {
                            clearInterval(verticalScrollInterval);
                            verticalScrollInterval = null;
                        }
                        currentVerticalContainer = null;

                        // Original drop functionality
                        var sort = [];
                        $("#" + target.id + " > div").each(function () {
                            sort[$(this).index()] = $(this).attr('id');
                        });

                        var id = el.id;
                        var old_stage = $("#" + source.id).data('status');
                        var new_stage = $("#" + target.id).data('status');
                        var project_id = '{{ $project->id }}';

                        $("#" + source.id).parent().find('.count').text($("#" + source.id + " > div").length);
                        $("#" + target.id).parent().find('.count').text($("#" + target.id + " > div").length);

                        $.ajax({
                            url: '{{ route('organization.tasks.update.order', [$project->id]) }}',
                            type: 'PATCH',
                            data: {
                                id: id,
                                sort: sort,
                                new_stage: new_stage,
                                old_stage: old_stage,
                                project_id: project_id,
                                "_token": "{{ csrf_token() }}"
                            },
                            success: function (data) {
                            }
                        });
                    });

                    dragulaInstance.on('cancel', function (el, container, source) {
                        // Clear auto-scroll on cancel
                        a(document).off('mousemove.dragula-autoscroll');
                        if (scrollInterval) {
                            clearInterval(scrollInterval);
                            scrollInterval = null;
                        }
                        if (verticalScrollInterval) {
                            clearInterval(verticalScrollInterval);
                            verticalScrollInterval = null;
                        }
                        currentVerticalContainer = null;
                    });
                })
            }, a.Dragula = new t, a.Dragula.Constructor = t
        }(window.jQuery),
            function (a) {
                "use strict";
                a.Dragula.init()
            }(window.jQuery);

        $(document).ready(function () {
            /*Set assign_to Value*/
            $(document).on('click', '.add_usr', function () {
                var ids = [];
                $(this).toggleClass('selected');
                var crr_id = $(this).attr('data-id');
                $('#usr_txt_' + crr_id).html($('#usr_txt_' + crr_id).html() == 'Add' ?
                    '{{ __('Added') }}' : '{{ __('Add') }}');
                if ($('#usr_icon_' + crr_id).hasClass('ti-plus')) {
                    $('#usr_icon_' + crr_id).removeClass('ti-plus');
                    $('#usr_icon_' + crr_id).addClass('ti-check');
                } else {
                    $('#usr_icon_' + crr_id).removeClass('ti-check');
                    $('#usr_icon_' + crr_id).addClass('ti-plus');
                }
                $('.selected').each(function () {
                    ids.push($(this).attr('data-id'));
                });
                $('input[name="assign_to"]').val(ids);
            });

            $(document).on("click", ".del_task", function () {
                var id = $(this);
                $.ajax({
                    url: $(this).attr('data-url'),
                    type: 'DELETE',
                    dataType: 'JSON',
                    data: {
                        "_token": "{{ csrf_token() }}"
                    },
                    success: function (data) {
                        $('#' + data.task_id).remove();
                        show_toastr('{{ __('success') }}',
                            '{{ __('Task Deleted Successfully!') }}');
                    },
                });
            });

            /*For Task Comment*/
            $(document).on('click', '#comment_submit', function (e) {
                var curr = $(this);

                var comment = $.trim($("#form-comment textarea[name='comment']").val());
                if (comment != '') {
                    $.ajax({
                        url: $("#form-comment").data('action'),
                        data: {
                            comment: comment,
                            "_token": "{{ csrf_token() }}"
                        },
                        type: 'POST',
                        success: function (data) {
                            data = JSON.parse(data);
                            var html = "<div class='list-group-item px-0'>" +
                                "                    <div class='row align-items-start'>" +
                                "                        <div class='col-auto'>" +
                                "                            <a href='#' class='avatar avatar_foredittask avatar-sm  ms-2'>" +
                                "                                <img src=" + data.default_img +
                                " alt='' class='avatar-sm rounded border-2 border border-primary ml-3'>" +
                                "                            </a>" +
                                "                        </div>" +
                                "                        <div class='col ml-n2'>" +
                                "                            <p class='d-block h6 text-sm font-weight-light mb-0 text-break'>" +
                                data.comment + "</p>" +
                                "                            <small class='d-block'>" + data
                                    .current_time + "</small>" +
                                "                           </div>" +
                                "                        <div class='col-auto'><div class='action-btn me-4'><a href='#' class='mx-3 btn btn-sm  align-items-center delete-comment bg-danger' data-url='" +
                                data.deleteUrl +
                                "'><i class='ti ti-trash text-white'></i></a></div></div>" +
                                "                    </div>" +
                                "                </div>";

                            $("#comments").prepend(html);
                            $("#form-comment textarea[name='comment']").val('');
                            load_task(curr.closest('.task-id').attr('id'));
                            show_toastr('{{ __('success') }}',
                                '{{ __('Comment Added Successfully!') }}');
                        },
                        error: function (data) {
                            show_toastr('error', '{{ __('Some Thing Is Wrong!') }}');
                        }
                    });
                } else {
                    show_toastr('error', '{{ __('Please write comment!') }}');
                }
            });
            $(document).on("click", ".delete-comment", function () {
                var btn = $(this);

                $.ajax({
                    url: $(this).attr('data-url'),
                    type: 'DELETE',
                    dataType: 'JSON',
                    data: {
                        "_token": "{{ csrf_token() }}"
                    },
                    success: function (data) {
                        load_task(btn.closest('.task-id').attr('id'));
                        show_toastr('{{ __('success') }}',
                            '{{ __('Comment Deleted Successfully!') }}');
                        btn.closest('.list-group-item').remove();
                    },
                    error: function (data) {
                        data = data.responseJSON;
                        if (data.message) {
                            show_toastr('error', data.message);
                        } else {
                            show_toastr('error', '{{ __('Some Thing Is Wrong!') }}');
                        }
                    }
                });
            });

            /*For Task Checklist*/
            $(document).on('click', '#checklist_submit', function () {
                var name = $("#form-checklist input[name=name]").val();
                if (name != '') {
                    $.ajax({
                        url: $("#form-checklist").data('action'),
                        data: {
                            name: name,
                            "_token": "{{ csrf_token() }}"
                        },
                        type: 'POST',
                        success: function (data) {
                            data = JSON.parse(data);
                            console.log('form-checklist', data);
                            load_task($('.task-id').attr('id'));
                            show_toastr('{{ __('success') }}',
                                '{{ __('Checklist Added Successfully!') }}');
                            var html =
                                '<div class="card border shadow-none checklist-member">' +
                                '                    <div class="px-3 py-2 row align-items-center">' +
                                '                        <div class="col">' +
                                '                            <div class="form-check form-check-inline">' +
                                '                                <input type="checkbox" class="form-check-input" id="check-item-' +
                                data.id + '" value="' + data.id + '" data-url="' + data
                                    .updateUrl + '">' +
                                '                                <label class="form-check-label h6 text-sm" for="check-item-' +
                                data.id + '">' + data.name + '</label>' +
                                '                            </div>' +
                                '                        </div>' +
                                '                        <div class="col-auto"> <div class="action-btn  ms-2">' +
                                '                            <a href="#" class="mx-1 btn btn-sm  align-items-center delete-checklist bg-danger" role="button" data-url="' +
                                data.deleteUrl + '">' +
                                '                                <i class="ti ti-trash text-white"></i>' +
                                '                            </a>' +
                                '                        </div></div>' +
                                '                    </div>' +
                                '                </div>'

                            $("#checklist").append(html);
                            $("#form-checklist input[name=name]").val('');
                            $("#form-checklist").collapse('toggle');
                        },
                        error: function (data) {
                            data = data.responseJSON;
                            show_toastr('error', data.message);
                        }
                    });
                } else {
                    show_toastr('error', '{{ __('Please write checklist name!') }}');
                }
            });
            $(document).on("change", "#checklist input[type=checkbox]", function () {
                $.ajax({
                    url: $(this).attr('data-url'),
                    type: 'POST',
                    dataType: 'JSON',
                    data: {
                        "_token": "{{ csrf_token() }}"
                    },
                    success: function (data) {
                        load_task($('.task-id').attr('id'));
                        show_toastr('{{ __('Success') }}',
                            '{{ __('Checklist Updated Successfully!') }}', 'success');
                    },
                    error: function (data) {
                        data = data.responseJSON;
                        if (data.message) {
                            show_toastr('error', data.message);
                        } else {
                            show_toastr('error', '{{ __('Some Thing Is Wrong!') }}');
                        }
                    }
                });
            });
            $(document).on("click", ".delete-checklist", function () {
                var btn = $(this);
                $.ajax({
                    url: $(this).attr('data-url'),
                    type: 'DELETE',
                    dataType: 'JSON',
                    data: {
                        "_token": "{{ csrf_token() }}"
                    },
                    success: function (data) {
                        load_task($('.task-id').attr('id'));
                        show_toastr('{{ __('success') }}',
                            '{{ __('Checklist Deleted Successfully!') }}');
                        btn.closest('.checklist-member').remove();
                    },
                    error: function (data) {
                        data = data.responseJSON;
                        if (data.message) {
                            show_toastr('error', data.message);
                        } else {
                            show_toastr('error', '{{ __('Some Thing Is Wrong!') }}');
                        }
                    }
                });
            });

            /*For Task Attachment*/
            $(document).on('click', '#file_attachment_submit', function () {
                var file_data = $("#task_attachment").prop("files")[0];
                if (file_data != '' && file_data != undefined) {
                    var formData = new FormData();
                    formData.append('file', file_data);
                    formData.append('_token', "{{ csrf_token() }}");
                    $.ajax({
                        url: $("#file_attachment_submit").data('action'),
                        type: 'POST',
                        data: formData,
                        cache: false,
                        processData: false,
                        contentType: false,
                        success: function (data) {
                            $('#task_attachment').val('');
                            $('.attachment_text').html('{{ __('Choose a file…') }}');
                            data = JSON.parse(data);
                            load_task(data.task_id);
                            show_toastr('{{ __('success') }}',
                                '{{ __('File Added Successfully!') }}');

                            var delLink = '';
                            if (data.deleteUrl.length > 0) {
                                delLink =
                                    ' <div class="action-btn bg-danger "><a href="#" class="action-item delete-comment-file" role="button" data-url="' +
                                    data.deleteUrl + '">' +
                                    '                                        <i class="ti ti-trash text-white"></i>' +
                                    '                                    </a></div>';
                            }

                            var html = '<div class="card mb-3 border shadow-none task-file">' +
                                '                    <div class="px-3 py-3">' +
                                '                        <div class="row align-items-center">' +
                                '                            <div class="col ml-n2">' +
                                '                                <h6 class="text-sm mb-0">' +
                                '                                    <a href="#">' + data.name +
                                '</a>' +
                                '                                </h6>' +
                                '                                <p class="card-text small text-muted">' +
                                data.file_size + '</p>' +
                                '                           </div>' +
                                '                            <div class="col-auto"> <div class="action-btn bg-secondary ">' +
                                '                                <a href="{{ asset(Storage::url('tasks')) }}/' +
                                data.file + '" download class="action-item" role="button">' +
                                '                                    <i class="ti ti-download text-white"></i>' +
                                '                                </a>' +
                                '                            </div></div>' +
                                delLink +
                                '                        </div>' +
                                '                    </div>' +
                                '                </div>'

                            $("#comments-file").prepend(html);
                        },
                        error: function (data) {
                            data = data.responseJSON;
                            console.log('error', data);
                            if (data.message) {
                                show_toastr('error', data.errors.file[0]);
                                $('#file-error').text(data.errors.file[0]).show();
                            } else {
                                show_toastr('error', '{{ __('Some Thing Is Wrong!') }}');
                            }
                        }
                    });
                } else {
                    show_toastr('error', '{{ __('Please select file!') }}');
                }
                console.log('not working');
            });
            $(document).on("click", ".delete-comment-file", function () {
                var btn = $(this);
                $.ajax({
                    url: $(this).attr('data-url'),
                    type: 'DELETE',
                    dataType: 'JSON',
                    data: {
                        "_token": "{{ csrf_token() }}"
                    },
                    success: function (data) {
                        load_task(btn.closest('.task-id').attr('id'));
                        show_toastr('{{ __('success') }}',
                            '{{ __('File Deleted Successfully!') }}');
                        btn.closest('.task-file').remove();
                    },
                    error: function (data) {
                        data = data.responseJSON;
                        if (data.message) {
                            show_toastr('error', data.message);
                        } else {
                            show_toastr('error', '{{ __('Some Thing Is Wrong!') }}');
                        }
                    }
                });
            });

            /*For Favorite*/
            $(document).on('click', '#add_favourite', function () {
                $.ajax({
                    url: $(this).attr('data-url'),
                    type: 'POST',
                    data: {
                        "_token": "{{ csrf_token() }}"
                    },
                    success: function (data) {
                        if (data.fav == 1) {
                            $('#add_favourite').addClass('action-favorite');
                        } else if (data.fav == 0) {
                            $('#add_favourite').removeClass('action-favorite');
                        }
                    }
                });
            });

            /*For Complete*/
            $(document).on('change', '#complete_task', function () {
                $.ajax({
                    url: $(this).attr('data-url'),
                    type: 'POST',
                    data: {
                        "_token": "{{ csrf_token() }}"
                    },
                    success: function (data) {
                        if (data.com == 1) {
                            $("#complete_task").prop("checked", true);
                        } else if (data.com == 0) {
                            $("#complete_task").prop("checked", false);
                        }
                        $('#' + data.task).insertBefore($('#task-list-' + data.stage +
                            ' .empty-container'));
                        load_task(data.task);
                    }
                });
            });

            /*Progress Move*/
            $(document).on('change', '#task_progress', function () {
                var progress = $(this).val();
                $('#t_percentage').html(progress);
                $.ajax({
                    url: $(this).attr('data-url'),
                    data: {
                        progress: progress,
                        "_token": "{{ csrf_token() }}"
                    },
                    type: 'POST',
                    success: function (data) {
                        load_task(data.task_id);
                    }
                });
            });
        });

        function load_task(id) {
            $.ajax({
                url: "{{ route('organization.projects.tasks.get', '_task_id') }}".replace('_task_id', id),
                dataType: 'html',
                data: {
                    "_token": "{{ csrf_token() }}"
                },
                success: function (data) {
                    $('#' + id).html('');
                    $('#' + id).html(data);
                }
            });
        }
    </script>
@endpush

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('organization.dashboard') }}">{{ __('Dashboard') }}</a></li>
    <li class="breadcrumb-item"><a href="{{ route('organization.projects.index') }}">{{ __('Project') }}</a></li>
    <li class="breadcrumb-item"><a
            href="{{ route('organization.projects.show', $project->id) }}">{{ ucwords($project->project_name) }}</a>
    </li>
    <li class="breadcrumb-item">{{ __('Task') }}</li>
@endsection

@section('action-btn')
@endsection

@section('content')
    @include('company.layouts.partials.nav')
    <div class="row pt-5">
        <div class="col-sm-12">
            <div class="row kanban-wrapper horizontal-scroll-cards" data-containers='{{ json_encode($stageClass) }}'
                 data-plugin="dragula">
                @foreach ($stages as $stage)
                    @php($tasks = $stage->tasks)
                    <div class="col">
                        <div class="crm-sales-card mb-4">
                            <div class="card-header d-flex align-items-center justify-content-between gap-3">
                                <h4 class="mb-0 d-flex align-items-center gap-3">{{ $stage->name }} <span
                                        class="count_of_task count">{{ $stage->task_count }}</span></h4>
                                <a href="#" data-size="lg"
                                   data-url="{{ route('organization.projects.tasks.create', [$project->id, $stage->id]) }}"
                                   data-ajax-popup="true" data-bs-toggle="tooltip"
                                   title="{{ __('Add Task in ') . $stage->name }}" class="btn btn-sm btn-light-primary">
                                    <i class="ti ti-plus"></i>
                                </a>
                            </div>
                            <div class="sales-item-wrp kanban-box main_heighttrack" id="task-list-{{ $stage->id }}"
                                 data-status="{{ $stage->id }}">
                                @foreach ($tasks as $taskDetail)
                                    <div class="sales-item draggable-item" id="{{ $taskDetail->id }}">
                                        <div class="sales-item-top border-bottom">
                                            <div class="d-flex align-items-center">
                                                <h5 class="mb-0 flex-1">
                                                    {{--                                                    <a href="#" class="dashboard-link task_refid" role="button"--}}
                                                    {{--                                                       data-bs-toggle="offcanvas" data-bs-target="#offcanvasTask"--}}
                                                    {{--                                                       aria-controls="offcanvasTask">{{ $taskDetail->task_id }}</a> --}}

                                                    <a href="{{ route('organization.projects.tasks.showDetail', [$project->id, $taskDetail->id]) }}"
                                                       class="dashboard-link task_refid">{{ $taskDetail->task_id }}</a>
                                                </h5>
                                                <div class="btn-group card-option">
                                                    <button type="button" class="btn p-0 border-0"
                                                            data-bs-toggle="dropdown" aria-haspopup="true"
                                                            aria-expanded="false">
                                                        <i class="ti ti-dots-vertical"></i>
                                                    </button>

                                                    <div
                                                        class="dropdown-menu icon-dropdown icon-dropdown dropdown-menu-end">

                                                        {{--                                                        <a href="#!" data-size="md"--}}
                                                        {{--                                                           data-url="{{ route('organization.projects.tasks.show', [$project->id, $taskDetail->id]) }}"--}}
                                                        {{--                                                           data-ajax-popup="true" class="dropdown-item"--}}
                                                        {{--                                                           data-bs-original-title="{{ __('View') }}">--}}
                                                        {{--                                                            <i class="ti ti-eye"></i>--}}
                                                        {{--                                                            <span>{{ __('View') }}</span>--}}
                                                        {{--                                                        </a>--}}

                                                        <a href="{{ route('organization.projects.tasks.showDetail', [$project->id, $taskDetail->id]) }}"
                                                           class="dropdown-item"
                                                           data-bs-original-title="{{ __('View') }}">
                                                            <i class="ti ti-eye"></i>
                                                            <span>{{ __('View') }}</span>
                                                        </a>

                                                        <a href="#!" data-size="lg"
                                                           data-url="{{ route('organization.projects.tasks.edit', [$project->id, $taskDetail->id]) }}"
                                                           data-ajax-popup="true" class="dropdown-item"
                                                           data-bs-original-title="{{ __('Edit ') . $taskDetail->name }}">
                                                            <i class="ti ti-pencil"></i>
                                                            <span>{{ __('Edit') }}</span>
                                                        </a>

                                                        {!! Form::open(['method' => 'DELETE', 'route' => ['organization.projects.tasks.destroy', [$project->id, $taskDetail->id]]]) !!}
                                                        <a href="#!" class="dropdown-item bs-pass-para">
                                                            <i class="ti ti-trash"></i>
                                                            <span> {{ __('Delete') }} </span>
                                                        </a>
                                                        {!! Form::close() !!}
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="pt-2">
                                                <h5>{{ $taskDetail->name }}</h5>
                                            </div>

                                            <div class="badge-wrp d-flex flex-wrap align-items-center gap-2">
                                                <span class="{{ $taskDetail->getPriorityBadgeClass() }}">
                                                    {{ __( \App\Models\ProjectTask::$priority[strtolower($taskDetail->priority)] ?? ucfirst($taskDetail->priority) ) }}
                                                </span>
                                            </div>

                                            <div class="mt-4">
                                                <div class="progress_bar_box"
                                                     title="{{ \App\Models\ProjectTask::getProgressLabel($taskDetail->progress ?? 0) }}">
                                                    <div class="progress_track">
                                                        <div
                                                            class="{{ \App\Models\ProjectTask::getProgressClass($taskDetail->progress ?? 0) }}"
                                                            style="width: {{ $taskDetail->checklist_progress }}%;"></div>
                                                    </div>
                                                    <div class="progress_value">
                                                        {{ $taskDetail->checklist_progress }}%
                                                        ({{ \App\Models\ProjectTask::getProgressLabel($taskDetail->checklist_progress) }}
                                                        )
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div
                                            class="sales-item-center border-bottom d-flex align-items-center justify-content-between">
                                            <ul class="d-flex flex-wrap align-items-center gap-2 p-0 m-0">
                                                <li class="d-inline-flex align-items-center gap-1 p-1 px-2 border rounded-1"
                                                    data-bs-toggle="tooltip" title="{{ __('Files') }}">
                                                    <i class="fa-solid fa-paperclip"></i>
                                                    {{ count($taskDetail->taskFiles) }}
                                                </li>

                                                <li class="d-inline-flex align-items-center gap-1 p-1 px-2 border rounded-1"
                                                    data-bs-toggle="tooltip" title="{{ __('Comments') }}">
                                                    <i class="f-16 ti ti-message"></i>
                                                    {{ count($taskDetail->comments) }}
                                                </li>

                                                <li class="d-inline-flex align-items-center gap-1 p-1 px-2 border rounded-1"
                                                    data-bs-toggle="tooltip" title="{{ __('Task Checklist') }}">
                                                    <i
                                                        class="f-16 ti ti-list"></i>{{ $taskDetail->countTaskChecklist() }}
                                                </li>
                                            </ul>

                                        </div>
                                        <div
                                            class="sales-item-bottom d-flex align-items-center justify-content-between">

                                            <div class="user-group">
                                                @foreach ($taskDetail->users() as $user)
                                                    <img
                                                        @if (($user->employee->gender ?? null) === GENDER_MALE)
                                                            src="{{ asset('assets/assestsnew/menimg.png') }}"
                                                        alt="Male"
                                                        @elseif (($user->employee->gender ?? null) === GENDER_FEMALE)
                                                            src="{{ asset('assets/assestsnew/femaile-report.svg') }}"
                                                        alt="Female"
                                                        @else
                                                            src="{{ $user->avatar ? asset('/storage/uploads/avatar/' . $user->avatar) : asset('assets/assestsnew/profile.png') }}"
                                                        alt="Default"
                                                        @endif
                                                        class="rounded-circle border shadow-sm me-1"
                                                        width="30" height="30"
                                                        title="{{ $user->name }}">
                                                @endforeach

                                            </div>
                                            @if (!empty($taskDetail->end_date) && $taskDetail->end_date != '0000-00-00')
                                                <span data-bs-toggle="tooltip" title="{{ __('End Date') }}"
                                                      @if (strtotime($taskDetail->end_date) < time()) class="text-primary" @endif>{{ Utility::getDateFormated($taskDetail->end_date) }}</span>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Task Template -->
    <div id="newsletterSection" style="background: #EBF4FF;">
        <div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasTask"
             aria-labelledby="offcanvasTaskLabel">
            <div class="offcanvas-header" style="margin-top: 64px">
                <div class="d-flex w-100 align-items-center">
                    <div class="col-md-6 col-7">
                        <h5 class="mb-0" id="offcanvasTaskLabel">Task Details</h5>
                    </div>
                    <div class="col-md-6 col-5">
                        <div class="d-flex justify-content-end align-items-center mt-0 gap-3">
                            <button type="button" class="trash_ofsetbtn"><i
                                    class="fa-solid fa-trash-can"></i></button>
                            <button type="button" class="share_btnoffset"><i
                                    class="fa-solid fa-share-nodes"></i></button>
                            <button type="button" class="btn-close text-reset p-0 m-0"
                                    data-bs-dismiss="offcanvas" aria-label="Close"></button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="offcanvas-body">
                <div class="d-flex flex-column">
                    <div class="col-12">
                        <div class="row">
                            <div class="col-12 gap-3 bottom_deviders">
                                <h4 class="d-flex align-items-center">Build Website Design for Client 025
                                    <div class="priority-tag priority-medium mx-2 mb-0">Medium</div>
                                </h4>
                                <span class="in_listerspan_select">in list
                                                <select class="px-1 py-1">
                                                    <option>To do</option>
                                                    <option>To do1</option>
                                                    <option>To do2</option>
                                                </select>
                                            </span>
                            </div>
                            <div class="col-12 gap-3 bottom_deviders mt-3">
                                <h4 class="d-flex align-items-center mb-0"><i
                                        class="fa-regular fa-user me-3"></i>Members</h4>
                                <div class="my-2 px-3 sugges_image">
                                    <div class="d-flex align-items-center gap-2">
                                        <img src="{{ asset('assets/assestsnew/roundcli1.svg') }}" alt="">
                                        <img src="{{ asset('assets/assestsnew/roundcli2.svg') }}" alt="">
                                        <button class="add_roundbtnblue" data-bs-toggle="modal"
                                                data-bs-target="#newFieldModal">
                                            <i class="fa-solid fa-plus"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <div class="col-12 gap-3 bottom_deviders mt-3">
                                <div class="d-flex justify-content-between mb-3 align-items-center">
                                    <h4 class="mb-0"><i
                                            class="fa-regular fa-newspaper me-3"></i>Description</h4>
                                    <button class="edit_blues">Edit<i
                                            class="fa-solid fa-pen-to-square ms-2"></i></button>
                                </div>
                                <span style="color: #6D6D6D;">
                                                Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod
                                                tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim
                                                veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea
                                                commodo consequat. Duis aute irure dolor in reprehenderit in voluptate
                                                velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint
                                                occaecat cupidatat non proident, sunt in culpa qui officia deserunt
                                                mollit anim id est laborum.
                                            </span>
                            </div>
                            <div class="col-12 gap-3 bottom_deviders mt-3">
                                <div class="d-flex justify-content-between mb-3 align-items-center">
                                    <h4 class="mb-0"><i
                                            class="fa-solid fa-paperclip me-3"></i>Attachments</h4>
                                    <button class="edit_blues">Add<i
                                            class="fa-solid fa-plus ms-2"></i></button>
                                </div>
                                <div class="d-flex align-items-center mb-3">
                                    <div class="pe-3">
                                        <img src="{{ asset('assets/assestsnew/doucuments_task.svg') }}" alt="">

                                    </div>

                                    <div class="flex-grow-1 main_documentnames">
                                        <h6 class="mb-1">Design Requirements Reports</h6>
                                        <small>Upload 25 Apr 2025, 10:00am</small>
                                    </div>

                                    <div class="dropdown ms-2">
                                        <button type="button" class="btn p-0 border-0" data-bs-toggle="dropdown"
                                                aria-expanded="false">
                                            <i class="ti ti-dots-vertical"></i>
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end">
                                            <li><a class="dropdown-item" href="#!"><i class="ti ti-eye"></i> View</a>
                                            </li>
                                            <li><a class="dropdown-item" href="#!"><i class="ti ti-pencil"></i> Edit</a>
                                            </li>
                                            <li><a class="dropdown-item text-danger" href="#!"><i
                                                        class="ti ti-trash"></i> Delete</a></li>
                                        </ul>
                                    </div>
                                </div>
                                <div class="d-flex align-items-center mb-3">
                                    <div class="pe-3">
                                        <img src="{{ asset('assets/assestsnew/doucuments_task.svg') }}" alt="">

                                    </div>

                                    <div class="flex-grow-1 main_documentnames">
                                        <h6 class="mb-1">Design Requirements Reports</h6>
                                        <small>Upload 25 Apr 2025, 10:00am</small>
                                    </div>

                                    <div class="dropdown ms-2">
                                        <button type="button" class="btn p-0 border-0" data-bs-toggle="dropdown"
                                                aria-expanded="false">
                                            <i class="ti ti-dots-vertical"></i>
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end">
                                            <li><a class="dropdown-item" href="#!"><i class="ti ti-eye"></i> View</a>
                                            </li>
                                            <li><a class="dropdown-item" href="#!"><i class="ti ti-pencil"></i> Edit</a>
                                            </li>
                                            <li><a class="dropdown-item text-danger" href="#!"><i
                                                        class="ti ti-trash"></i> Delete</a></li>
                                        </ul>
                                    </div>
                                </div>

                            </div>
                        </div>
                        <div class="col-12 gap-3 bottom_deviders mt-3">
                            <div class="d-flex justify-content-between mb-3 align-items-center">
                                <h4 class="d-flex align-items-center"><i
                                        class="fas fa-sliders-h me-3"></i></i>Custom Fields</h4>
                                <button class="edit_blues" data-bs-toggle="modal"
                                        data-bs-target="#selectMembersModal">Add<i
                                        class="fa-solid fa-plus ms-2"></i></button>
                            </div>
                            <div class="row my-4">
                                <div class="col-4">

                                    <small class="fw-medium fs-6">Status</small>
                                    <select class="form-select mt-1"
                                            style="background-color: #EAF5FF; width: 100% !important;">
                                        <option value="">High</option>
                                        <option value="">Team 1</option>
                                        <option value="">Team 2</option>
                                        <option value="">Team 3</option>
                                    </select>
                                </div>
                                <div class="col-4">

                                    <small class="fw-medium fs-6">T-Shirt Size</small>
                                    <select class="form-select mt-1"
                                            style="background-color: #EAF5FF; width: 100% !important;">
                                        <option value="">L-32 hrs</option>
                                        <option value="">Team 1</option>
                                        <option value="">Team 2</option>
                                        <option value="">Team 3</option>
                                    </select>
                                </div>
                                <div class="col-4">

                                    <small class="fw-medium fs-6">Actual Time</small>
                                    <select class="form-select mt-1"
                                            style="background-color: #EAF5FF; width: 100% !important;">
                                        <option value="">25 hrs</option>
                                        <option value="">Team 1</option>
                                        <option value="">Team 2</option>
                                        <option value="">Team 3</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="col-12 gap-3 bottom_deviders mt-3">
                            <div class="d-flex justify-content-between mb-3 align-items-center">
                                <h4 class="d-flex align-items-center mb-0"><i
                                        class="fa-solid fa-calendar me-3"></i></i>Dates</h4>
                                <button class="edit_blues" data-bs-toggle="modal"
                                        data-bs-target="#dateModal">Edit<i
                                        class="fa-solid fa-pen-to-square ms-2"></i></button>
                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    <small class="fw-medium fs-6">Start Date</small>
                                    <p class="fw-medium fs-6" style="color: #A2A2A2;">26 Apr 2025</p>
                                </div>
                                <div class="col-md-4">
                                    <small class="fw-medium fs-6">End Date</small>
                                    <p class="fw-medium fs-6" style="color: #A2A2A2;">26 Apr 2025</p>
                                </div>
                            </div>
                        </div>


                        <div class="col-12">
                            <div class="checklist-card my-4">
                                <div class="checklist-header">
                                    <h4><i class="fa-regular fa-square-check"></i> Checklist</h4>
                                    <button class="edit_blues">Add +</button>
                                </div>

                                <div class="checklist-input my-3">
                                    <input type="text" placeholder="Checklist Name">
                                    <button><i class="fa-solid fa-check"></i></button>
                                </div>

                                <div class="checklist-items">
                                    <div class="checklist-item">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="topic1">
                                            <label class="form-check-label" for="topic1">Topic 1</label>
                                        </div>
                                        <span class="delete-btn"><i class="fa-solid fa-trash"></i> Delete</span>
                                    </div>

                                    <div class="checklist-item">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="topic2">
                                            <label class="form-check-label" for="topic2">Topic 2</label>
                                        </div>
                                        <span class="delete-btn"><i class="fa-solid fa-trash"></i> Delete</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-12 gap-3 bottom_deviders mt-3">
                            <div class="d-flex justify-content-between mb-3 align-items-center">
                                <h4 class="mb-0"><i
                                        class="fa-solid fa-message me-3"></i>Comment</h4>
                            </div>
                            <div class="comment-box d-flex align-items-center px-3 py-2">
                                <label class="circle-icon bg-light text-primary m-0" for="fileUpload">
                                    <i class="fas fa-plus"></i>
                                </label>
                                <input type="file" id="fileUpload" class="d-none">

                                <div class="d-flex align-items-center gap-2 ms-2">
                                    <i class="far fa-smile emoji-icon fs-4"
                                       style="cursor: pointer;"></i>
                                    <i class="fas fa-at mention-icon fs-4" style="cursor: pointer;"></i>

                                    <input type="text" class="comment-input flex-grow-1"
                                           placeholder="Add Comments">
                                </div>

                                <div class="ms-auto">
                                    <button class="send-btn">
                                        <i class="fas fa-paper-plane"></i>
                                    </button>
                                </div>
                            </div>

                            <div class="comment-box d-flex align-items-center p-3 mb-2 mt-4">
                                <img src="{{ asset('assets/assestsnew/woman1.png') }}" class="rounded-circle me-2"
                                     alt="avatar">
                                <div class="flex-grow-1">
                                    <p class="mb-1"><strong>User Name</strong> <span
                                            class="text-muted">Lorem ipsum dolor sit amet,
                                                            consectetur adipiscing elit, sed do</span></p>
                                    <p class="text-primary mb-1" style="font-size: 14px;">28 Apr
                                        2025 19:55 pm</p>
                                    <div class="reply-input d-none mt-2">
                                                        <textarea class="form-control" rows="1"
                                                                  placeholder="Write a reply..."></textarea>
                                        <button class="btn btn-sm btn-primary mt-1">Send</button>
                                    </div>
                                </div>
                            </div>
                            <div class="d-flex gap-3">
                                <a href="#"
                                   class="text-muted text-decoration-none small reply-btn">Reply</a>
                            </div>

                            <div class="reply-input d-none mt-2">
                                                <textarea class="form-control" rows="1"
                                                          placeholder="Write a reply..."></textarea>
                                <button class="btn btn-sm btn-primary mt-1">Send</button>
                            </div>

                            <div class="comment-box d-flex align-items-center p-3 mb-2 mt-4">
                                <img src="{{ asset('assets/assestsnew/woman2.png') }}" class="rounded-circle me-2"
                                     alt="avatar">
                                <div class="flex-grow-1">
                                    <p class="mb-1">
                                        <strong>Admin</strong>
                                        <span class="badge bg-light text-primary border me-2"><i
                                                class="fas fa-file-word me-1"></i>Reports</span>
                                        <span class="text-muted">Lorem ipsum dolor sit amet,
                                                            consectetur adipiscing elit, sed do</span>
                                    </p>
                                    <p class="text-primary mb-1" style="font-size: 14px;">30 Apr
                                        2025 10:55 pm</p>


                                    <div class="edit-input d-none mt-2">
                                                        <textarea class="form-control"
                                                                  rows="1">Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do</textarea>
                                        <button class="btn btn-sm btn-success mt-1">Update</button>
                                    </div>
                                </div>
                            </div>


                            <div class="d-flex gap-3">
                                <a href="#"
                                   class="text-decoration-none text-muted small edit-btn">Edit</a>
                                <a href="#"
                                   class="text-decoration-none text-muted small delete-btn">Delete</a>
                            </div>


                            <div class="row my-5">
                                <div class="col-12">
                                    <div class="d-flex justify-content-between mb-3 align-items-center">
                                        <h4 class="mb-0"><i class="fa-solid fa-chart-line me-3"></i>Activity</h4>
                                    </div>

                                    <div class="d-flex align-items-center mb-2 mt-4">
                                        <img src="{{ asset('assets/assestsnew/woman1.png') }}"
                                             class="rounded-circle me-2" alt="avatar">
                                        <div class="flex-grow-1">
                                            <p class="mb-0"><strong>Move Task</strong></p>
                                            <p class="mb-0">Maddy Shan Moved the Task Test from To Do to In Progress</p>
                                            <p class="text-muted mb-0">1 hour ago</p>
                                            <div class="reply-input d-none mt-2">
                                                        <textarea class="form-control" rows="1"
                                                                  placeholder="Write a reply..."></textarea>
                                                <button class="btn btn-sm btn-primary mt-1">Send</button>
                                            </div>
                                        </div>
                                    </div>
                                    <hr>
                                    <div class="d-flex align-items-center mt-4">
                                        <img src="{{ asset('assets/assestsnew/woman1.png') }}"
                                             class="rounded-circle me-2" alt="avatar">
                                        <div class="flex-grow-1">
                                            <p class="mb-0"><strong>Move Task</strong></p>
                                            <p class="mb-0">Maddy Shan Moved the Task Test from To Do to In Progress</p>
                                            <p class="text-muted mb-0">1 hour ago</p>
                                            <div class="reply-input d-none mt-2">
                                                        <textarea class="form-control" rows="1"
                                                                  placeholder="Write a reply..."></textarea>
                                                <button class="btn btn-sm btn-primary mt-1">Send</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Edit Template -->
                <div class="task-container d-none" id="taskContainer">
                    <div class="task-card">
                        <div class="task-stage-item">
                            <div class="task-stage-left">
                                <div class="task-stage-icon">
                                    <img src="./assest/gridmenublue.svg" alt="">
                                </div>
                                <div class="task-stage-title">To Do</div>
                            </div>
                            <div class="task-stage-actions">
                                <button class="task-action-btn task-edit-btn">
                                    <img src="./assest/edit.svg" alt="">
                                </button>
                                <button class="task-action-btn task-delete-btn">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </div>


                        <div class="task-stage-item">
                            <div class="task-stage-left">
                                <div class="task-stage-icon">
                                    <img src="./assest/gridmenublue.svg" alt="">
                                </div>
                                <div class="task-stage-title">In Progress</div>
                            </div>
                            <div class="task-stage-actions">
                                <button class="task-action-btn task-edit-btn">
                                    <img src="./assest/edit.svg" alt="">
                                </button>
                                <button class="task-action-btn task-delete-btn">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </div>


                        <div class="task-stage-item">
                            <div class="task-stage-left">
                                <div class="task-stage-icon">
                                    <img src="./assest/gridmenublue.svg" alt="">
                                </div>
                                <div class="task-stage-title">Review</div>
                            </div>
                            <div class="task-stage-actions">
                                <button class="task-action-btn task-edit-btn">
                                    <img src="./assest/edit.svg" alt="">
                                </button>
                                <button class="task-action-btn task-delete-btn">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </div>


                        <div class="task-stage-item">
                            <div class="task-stage-left">
                                <div class="task-stage-icon">
                                    <img src="./assest/gridmenublue.svg" alt="">
                                </div>
                                <div class="task-stage-title">Done</div>
                            </div>
                            <div class="task-stage-actions">
                                <button class="task-action-btn task-edit-btn">
                                    <img src="./assest/edit.svg" alt="">
                                </button>
                                <button class="task-action-btn task-delete-btn">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </div>


                        <div class="task-stage-item">
                            <div class="task-stage-left">
                                <div class="task-stage-icon">
                                    <img src="./assest/gridmenublue.svg" alt="">
                                </div>
                                <div class="task-stage-title">Trash</div>
                            </div>
                            <div class="task-stage-actions">
                                <button class="task-action-btn task-edit-btn">
                                    <img src="./assest/edit.svg" alt="">
                                </button>
                                <button class="task-action-btn task-delete-btn">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </div>

                        <div class="task-note">
                            <span class="task-note-highlight">Note:</span> You can easily change order of project task
                            stage using drag & drop.
                        </div>

                        <div class="task-buttons">
                            <button class="task-cancel-btn">Cancel</button>
                            <button class="task-save-btn">Save</button>
                        </div>
                    </div>
                </div>


                <!-- Model 1 -->
                <div class="modal fade" id="newFieldModal" tabindex="-1" aria-labelledby="newFieldModalLabel"
                     aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content shadow"
                             style="border-radius: 30px; padding: 1rem; background-color: #fff; box-shadow: 0 10px 30px rgba(0,0,0,0.1);">
                            <div class="modal-header border-0">
                                <h5 class="modal-title w-100 text-center" id="newFieldModalLabel">New Field</h5>
                                <button type="button" class="btn-close position-absolute end-0 me-3"
                                        data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">

                                <!-- Title -->
                                <div class="mb-3 w-100">
                                    <label class="form-label fw-semibold">Title</label>
                                    <input type="text" class="form-control" style="border-radius: 100px;"
                                           placeholder="Add a title…">
                                </div>

                                <!-- Type -->
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Type</label>
                                    <select class="form-select form_hov" style="max-width: 100%;">
                                        <option>Dropdown</option>
                                        <option>Checkbox</option>
                                        <option>Date</option>
                                        <option>Dropdown</option>
                                        <option>Number</option>
                                        <option>Text</option>
                                    </select>
                                </div>

                                <div class="row align-items-end g-2 mb-3">
                                    <div class="col-9">
                                        <label class="form-label fw-semibold">Options</label>
                                        <div class="input-group">
                                            <input type="text" class="form-control" style="border-radius: 100px;"
                                                   placeholder="Add item…">
                                        </div>
                                    </div>
                                    <div class="col-3 d-flex align-items-end">
                                        <button class="btn btn-outline-primary w-100"
                                                style="border-radius: 100px; background-color: #C4DEFF; color: #316FF6;">
                                            Add
                                        </button>
                                    </div>
                                </div>
                                <button class="btn btn-primary w-100 btn-create mt-2" style="border-radius: 100px;">
                                    Create
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Model 2 -->
                <div class="modal fade" id="selectMembersModal" tabindex="-1" aria-labelledby="selectMembersLabel"
                     aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content shadow-lg">
                            <div class="modal-header border-0">
                                <h5 class="modal-title fw-bold" id="selectMembersLabel">Select Members</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                        aria-label="Close"></button>
                            </div>
                            <div class="modal-body px-3 pt-0">
                                <div class="position-relative w-100">
                                    <input type="text" class="form-control rounded-pill ps-4 pe-5"
                                           placeholder="Search Members.."/>
                                    <span class="position-absolute end-0 top-50 translate-middle-y pe-3">
                            <i class="fa fa-search" style="color: #316FF6;"></i>
                        </span>
                                </div>

                                <div class="card_members_list">
                                    <p class="fw-semibold mt-3 fs-5">Card Members</p>
                                    <div class="member-item">
                                        <img src="https://randomuser.me/api/portraits/men/10.jpg" alt="">
                                        <span>Arun Kumar</span>
                                    </div>
                                    <div class="member-item">
                                        <img src="https://randomuser.me/api/portraits/women/20.jpg" alt="">
                                        <span>Anu Sri</span>
                                    </div>


                                    <p class="fw-semibold mt-3 fs-5">Board Members</p>
                                    <div class="member-item">
                                        <img src="https://randomuser.me/api/portraits/men/30.jpg" alt="">
                                        <span>Arun Kumar</span>
                                    </div>
                                    <div class="member-item">
                                        <img src="https://randomuser.me/api/portraits/women/31.jpg" alt="">
                                        <span>Anu Sri</span>
                                    </div>
                                    <div class="member-item">
                                        <img src="https://randomuser.me/api/portraits/men/32.jpg" alt="">
                                        <span>Arun Kumar</span>
                                    </div>
                                    <div class="member-item">
                                        <img src="https://randomuser.me/api/portraits/women/33.jpg" alt="">
                                        <span>Anu Sri</span>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Model 3 -->
                <div class="modal fade" id="dateModal" tabindex="-1" aria-labelledby="dateModalLabel"
                     aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content rounded-4 shadow" style="border: none;">
                            <div class="modal-body p-4">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h5 class="fw-bold mb-0">Dates</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>

                                <div class="p-3 bg-light rounded mb-4 text-center">
                                    <div class="fw-bold">📅 Calendar UI Here</div>
                                    <small class="text-muted">Use Flatpickr or Pikaday for full calendar
                                        functionality</small>
                                </div>

                                <div class="form-check mb-3">
                                    <input class="form-check-input" type="checkbox" id="startDateCheck">
                                    <label class="form-check-label" for="startDateCheck">
                                        Start date
                                    </label>
                                    <input type="date" class="form-control mt-2" disabled id="startDateInput">
                                </div>

                                <div class="form-check mb-3">
                                    <input class="form-check-input" type="checkbox" id="dueDateCheck" checked>
                                    <label class="form-check-label" for="dueDateCheck">
                                        Due date
                                    </label>
                                    <div class="d-flex mt-2 gap-2">
                                        <input type="date" class="form-control" value="2021-04-07">
                                        <input type="time" class="form-control" value="15:00">
                                    </div>
                                </div>

                                <div class="d-grid gap-2 mt-4">
                                    <button class="btn btn-primary rounded-pill">Save</button>
                                    <button class="btn btn-outline-secondary rounded-pill">Remove</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
