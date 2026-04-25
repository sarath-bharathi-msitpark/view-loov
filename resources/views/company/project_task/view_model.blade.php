@extends('company.layouts.company')
@section('page-title')
    {{ ucwords($project->project_name) . __("'s Tasks") }}
@endsection

@push('script-page')

    <script>
        function confirmDelete(id) {
            Swal.fire({
                title: 'Are you sure?',
                text: "This action cannot be undone!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Yes, delete it!',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('delete-form-' + id).submit();
                }
            });
        }
    </script>

    <script>
        document.getElementById('shareButton').addEventListener('click', async () => {
            const shareData = {
                title: document.title,
                text: 'Check this out!',
                url: window.location.href
            };

            if (navigator.share) {
                try {
                    await navigator.share(shareData);
                    console.log('Shared successfully');
                } catch (err) {
                    console.error('Error sharing:', err);
                }
            } else {
                try {
                    await navigator.clipboard.writeText(window.location.href);
                    Swal.fire({
                        icon: 'success',
                        title: 'Link copied!',
                        text: 'The page URL has been copied to your clipboard.',
                        timer: 2000,
                        showConfirmButton: false
                    });
                } catch (err) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops!',
                        text: 'Failed to copy the link.',
                    });
                }
            }
        });
    </script>

    {{--Checklist--}}
    <script>
        // --- Prevent Multiple Submissions ---
        let checklistProcessing = false;

        // -------------------------
        // 1️⃣ Add Checklist Item
        // -------------------------
        $(document).on('click', '#addChecklistBtn', function (e) {
            e.preventDefault();

            if (checklistProcessing) return;   // stop duplicates
            checklistProcessing = true;

            let form = $('#checklistForm');
            let url = form.data('action');
            let input = form.find('input[name="name"]');
            let name = input.val().trim();

            if (name === '') {
                checklistProcessing = false;
                return;
            }

            $.ajax({
                url: url,
                type: 'POST',
                dataType: 'json',
                data: form.serialize(),
                success: function (response) {
                    checklistProcessing = false;

                    if (response && response.id) {
                        let item = `
                        <div class="checklist-item d-flex justify-content-between align-items-center" data-id="${response.id}">
                            <div class="form-check">
                                <input class="form-check-input checklist-status" type="checkbox"
                                       id="check-item-${response.id}"
                                       data-url="${response.updateUrl}">
                                <label class="form-check-label" for="check-item-${response.id}">
                                    ${response.name}
                                </label>
                            </div>

                            <span class="btn btn-danger btn-sm text-nowrap delete-checklist text-white"
                                  data-url="${response.deleteUrl}">
                                <i class="fa-solid fa-trash"></i> Delete
                            </span>
                        </div>
                    `;

                        $('#checklistItems').prepend(item);
                        input.val(''); // clear input field
                    } else {
                        alert('Something went wrong while adding the checklist item.');
                    }
                },
                error: function (xhr) {
                    checklistProcessing = false;
                    console.error(xhr.responseText);
                }
            });
        });

        // -------------------------
        // 2️⃣ Add using ENTER key
        // -------------------------
        $(document).on('keypress', 'input[name="name"]', function (e) {
            if (e.which === 13) {
                e.preventDefault();
                $('#addChecklistBtn').click();
            }
        });

        // -------------------------
        // 3️⃣ Toggle Checklist Status
        // -------------------------
        $(document).off('change', '.checklist-status')
            .on('change', '.checklist-status', function () {

                let checkbox = $(this);
                let url = checkbox.data('url');

                $.ajax({
                    url: url,
                    type: 'POST',
                    data: {_token: '{{ csrf_token() }}'},
                    success: function () {
                        console.log('Checklist status updated');
                    },
                    error: function (xhr) {
                        console.error(xhr.responseText);
                    }
                });
            });

        // -------------------------
        // 4️⃣ Delete Checklist Item
        // -------------------------
        $(document).off('click', '.delete-checklist')
            .on('click', '.delete-checklist', function () {

                let item = $(this).closest('.checklist-item');
                let url = $(this).data('url');

                Swal.fire({
                    title: "Are you sure?",
                    text: "This checklist item will be deleted permanently.",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#d33",
                    cancelButtonColor: "#6c757d",
                    confirmButtonText: "Yes, delete it!",
                    cancelButtonText: "Cancel"
                }).then((result) => {

                    if (result.isConfirmed) {

                        $.ajax({
                            url: url,
                            type: 'DELETE',
                            data: {_token: '{{ csrf_token() }}'},
                            success: function () {

                                item.fadeOut(200, function () {
                                    $(this).remove();
                                });

                                Swal.fire({
                                    title: "Deleted!",
                                    text: "The checklist item has been removed.",
                                    icon: "success",
                                    timer: 1500,
                                    showConfirmButton: false
                                });

                            },
                            error: function (xhr) {
                                console.error(xhr.responseText);

                                Swal.fire({
                                    title: "Error!",
                                    text: "Unable to delete the item.",
                                    icon: "error"
                                });
                            }
                        });

                    }
                });
            });

    </script>

    {{--  File Attachment  --}}
    <script>
        $(document).ready(function () {

            /* -----------------------------
                FILE PREVIEW BEFORE UPLOAD
            --------------------------------*/
            $('#task_attachment').on('change', function () {
                let file = this.files[0];
                if (!file) return;

                let url = URL.createObjectURL(file);
                let type = file.type.toLowerCase();

                // Hide all preview items
                $("#imgPreview, #videoPreview, #audioPreview, #docPreview").hide();
                $("#previewContainer").show();

                if (type.startsWith("image/")) {
                    $("#imgPreview").attr("src", url).show();
                } else if (type.startsWith("video/")) {
                    $("#videoPreview").attr("src", url).show();
                } else if (type.startsWith("audio/")) {
                    $("#audioPreview").attr("src", url).show();
                } else if (type === "application/pdf") {
                    $("#docPreview").attr("src", url).show();
                } else {
                    $("#docPreview").attr("src", "https://docs.google.com/viewer?embedded=true&url=" + url).show();
                }
            });

            /* -----------------------------
                CANCEL PREVIEW
            --------------------------------*/
            $('#cancelPreview').on('click', function () {
                $('#previewContainer').hide();
                $('#task_attachment').val('');
                $("#imgPreview, #videoPreview, #audioPreview, #docPreview").hide();
            });


            /* -----------------------------
                AJAX FILE UPLOAD
            --------------------------------*/
            $(document).on('click', '#file_attachment_submit', function (e) {
                e.preventDefault();

                let btn = $(this);
                if (btn.prop('disabled')) return; // avoid double click

                let file = $('#task_attachment')[0].files[0];
                if (!file) {
                    show_toastr("Error", "Please choose a file", "error");
                    return;
                }

                btn.prop('disabled', true); // disable button

                var formData = new FormData();
                formData.append('_token', $('input[name=_token]').val());
                formData.append('file', file);

                $.ajax({
                    url: btn.data('action'),
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,

                    success: function (response) {

                        if (response.is_success && response.data) {

                            let file = response.data;

                            $('#comments-file').prepend(`
                        <div class="d-flex align-items-center mb-3 border-bottom pb-2 task-file" data-id="${file.id}">
                            <div class="pe-3">
                                <img src="/assets/assestsnew/doucuments_task.svg" alt="">
                            </div>

                            <div class="flex-grow-1 main_documentnames">
                                <h6 class="mb-1">${file.name}</h6>
                                <small>Uploaded just now</small>
                            </div>

                            <div class="dropdown ms-2">
                                <button type="button" class="btn p-0 border-0" data-bs-toggle="dropdown">
                                    <i class="ti ti-dots-vertical"></i>
                                </button>

                                <ul class="dropdown-menu dropdown-menu-end">
                                    <li><a class="dropdown-item" href="${file.url}" target="_blank"><i class="ti ti-eye"></i> View</a></li>
                                    <li><a class="dropdown-item" href="${file.url}" download><i class="ti ti-download"></i> Download</a></li>
                                    <li><a class="dropdown-item text-danger delete-comment-file" data-url="${file.deleteUrl}"><i class="ti ti-trash"></i> Delete</a></li>
                                </ul>
                            </div>
                        </div>
                    `);

                            // reset
                            $('#task_attachment').val('');
                            $('#previewContainer').hide();
                            $("#imgPreview, #videoPreview, #audioPreview, #docPreview").hide();

                            show_toastr('Success', 'File uploaded successfully!', 'success');
                        }

                    },

                    error: function () {
                        show_toastr('Error', 'Upload failed!', 'error');
                    },

                    complete: function () {
                        btn.prop('disabled', false); // enable button again
                    }
                });
            });


            /* -----------------------------
                DELETE FILE WITH SWEET ALERT
            --------------------------------*/
            $(document).on('click', '.delete-comment-file', function (e) {
                e.preventDefault();

                let url = $(this).data('url');
                let item = $(this).closest('.task-file');

                Swal.fire({
                    title: "Are you sure?",
                    text: "Delete this file?",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#d33",
                    cancelButtonColor: "#3085d6",
                    confirmButtonText: "Yes, delete it!"
                }).then((result) => {

                    if (result.isConfirmed) {
                        $.ajax({
                            url: url,
                            type: "DELETE",
                            data: {_token: "{{ csrf_token() }}"},

                            success: function () {
                                item.remove();
                                show_toastr("Success", "File deleted!", "success");
                            }
                        });
                    }
                });

            });

        });
    </script>

    {{--  Comments  --}}
    <script>
        $(document).ready(function () {

            $('#commentSubmitBtn').on('click', function () {
                let btn = $(this);

                // Prevent double click
                if (btn.prop('disabled')) return;

                let form = $('#commentForm');
                let url = form.data('action');
                let comment = form.find('input[name="comment"]').val().trim();

                if (comment === '') {
                    show_toastr('error', 'Please write a comment!');
                    return;
                }

                // Disable button while submitting
                btn.prop('disabled', true);

                $.ajax({
                    url: url,
                    method: 'POST',
                    data: form.serialize(),
                    success: function (response) {
                        let comment = typeof response === 'string' ? JSON.parse(response) : response;

                        $('#commentList').prepend(`
        <div class="comment-box d-flex align-items-center p-3 mb-2 border rounded">

            <img src="${comment.avatar_url}"
                class="rounded-circle me-2"
                width="40" height="40" alt="avatar">

            <div class="flex-grow-1">
                <p class="mb-1">
                    <strong>${comment.user_name}</strong>
                    <span class="text-muted">${comment.comment}</span>
                </p>
                <p class="text-primary mb-1 small">${comment.current_time}</p>
            </div>

            <div class="dropdown ms-2">
                <button type="button" class="btn p-0 border-0" data-bs-toggle="dropdown">
                    <i class="ti ti-dots-vertical"></i>
                </button>

                <ul class="dropdown-menu dropdown-menu-end">
                    <li>
                        <a href="#!" class="dropdown-item text-danger delete-comment"
                           data-url="${comment.deleteUrl}">
                            <i class="ti ti-trash"></i> Delete
                        </a>
                    </li>
                </ul>
            </div>

        </div>
    `);

                        // reset input
                        $('#commentForm').find('input[name="comment"]').val('');

                        show_toastr('success', 'Comment Added Successfully!');
                    },

                    error: function () {
                        show_toastr('error', 'Something went wrong!');
                    },

                    complete: function () {
                        // Re-enable button after request finishes
                        btn.prop('disabled', false);
                    }
                });
            });

        });

        // ✅ Delete Comment
        $(document).on('click', '.delete-comment', function (e) {
            e.preventDefault();

            var btn = $(this);
            var url = btn.data('url');

            Swal.fire({
                title: 'Are you sure?',
                text: "Do you really want to delete this comment?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: url,
                        method: 'DELETE',
                        data: {_token: $('meta[name="csrf-token"]').attr('content')},
                        success: function (res) {
                            if (res === "true") {
                                btn.closest('.comment-box').fadeOut(300, function () {
                                    $(this).remove();
                                });

                                Swal.fire({
                                    title: 'Deleted!',
                                    text: 'The comment has been removed.',
                                    icon: 'success',
                                    timer: 1500,
                                    showConfirmButton: false
                                });
                            } else {
                                Swal.fire({
                                    title: 'Error!',
                                    text: 'Failed to delete the comment.',
                                    icon: 'error'
                                });
                            }
                        },
                        error: function () {
                            Swal.fire({
                                title: 'Error!',
                                text: 'Something went wrong on the server.',
                                icon: 'error'
                            });
                        }
                    });
                }
            });
        });
    </script>

    <script>
        $(document).on('change', 'select[name="stage_id"]', function () {
            var select = $(this);
            var taskCard = select.closest('[id^="task-"]');
            var taskId = taskCard.length ? taskCard.attr('id').replace('task-', '') : "{{ $task->id }}";
            var new_stage = select.val();
            var old_stage = select.data('old-stage');
            var project_id = '{{ $project->id }}';

            console.log('Changing stage of task', taskId, 'from', old_stage, 'to', new_stage);

            $.ajax({
                url: '{{ route('organization.tasks.change.stage', [$project->id]) }}',
                type: 'PATCH',
                data: {
                    id: taskId,
                    new_stage: new_stage,
                    old_stage: old_stage,
                    _token: "{{ csrf_token() }}"
                },
                success: function (response) {
                    // console.log(response.message);
                    select.data('old-stage', new_stage); // update stored stage

                    Swal.fire({
                        icon: 'success',
                        title: 'Stage Updated',
                        text: 'The task stage has been updated successfully.',
                        timer: 1500,
                        showConfirmButton: false
                    });
                },
                error: function (xhr) {
                    // console.error('Error:', xhr.responseText);

                    Swal.fire({
                        icon: 'error',
                        title: 'Update Failed',
                        text: 'Something went wrong while updating the task stage.',
                        timer: 2000,
                        showConfirmButton: false
                    });
                }
            });
        });
    </script>

@endpush

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('organization.dashboard') }}">{{ __('Dashboard') }}</a></li>
    <li class="breadcrumb-item"><a href="{{ route('organization.projects.index') }}">{{ __('Project') }}</a></li>
    <li class="breadcrumb-item"><a
            href="{{ route('organization.projects.show', $project->id) }}">{{ ucwords($project->project_name) }}</a>
    </li>
    <li class="breadcrumb-item">{{ __('Task') }} - {{ $task->task_id }}</li>
@endsection

@section('action-btn')
@endsection

@section('content')
    @include('company.layouts.partials.nav')

    <div class="col-12" style="margin-top: 64px; background-color:#fff; border-radius:10px">
        <div class="row p-3 p-md-5">
            <div class="col-12">
                <div class="row">
                    <div class="col-md-6 col-7">
                        <h5 class="mb-0" id="offcanvasTaskLabel">Task Details</h5>
                    </div>
                    <div class="col-md-6 col-5">
                        <div class="d-flex justify-content-end align-items-center mt-0 gap-3">
                            {!! Form::open(['method' => 'DELETE', 'route' => ['organization.projects.tasks.destroy', [$project->id, $task->id]], 'id' => 'delete-form-'.$task->id]) !!}
                            <button type="button" class="trash_ofsetbtn" onclick="confirmDelete('{{ $task->id }}')">
                                <i class="fa-solid fa-trash-can"></i>
                            </button>
                            {!! Form::close() !!}

                            <button type="button" class="share_btnoffset" id="shareButton">
                                <i class="fa-solid fa-share-nodes"></i>
                            </button>

                            {{--                            <button type="button"--}}
                            {{--                                    class="fw-normal but_1 text-white d-flex align-items-center gap-2 text-nowrap px-3 py-2"--}}
                            {{--                                    onclick="goBackAndRefresh()">--}}
                            {{--                                <i class="ti ti-arrow-left"></i> Back--}}
                            {{--                            </button>--}}
                            <a href="{{ route('organization.projects.tasks.index', $project->id) }}"
                               class="btn_for_status me-1">
                                Task
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12 gap-3 bottom_deviders">
                <h4 class="d-flex align-items-center">{{ $project->project_name }} - {{ $task->task_id }}
                    <div class="priority-tag priority-medium mx-2 mb-0">
                        <div class="badge-wrp d-flex flex-wrap align-items-center gap-2">
                                        <span class="{{ $task->getPriorityBadgeClass() }}">
                                            {{ __( \App\Models\ProjectTask::$priority[strtolower($task->priority)] ?? ucfirst($task->priority) ) }}
                                        </span>
                        </div>
                    </div>
                </h4>

                <div class="in_listerspan_select">
                    Listed In
                    <select class="px-1 py-1" name="stage_id" data-old-stage="{{ $task->stage_id }}">
                        @foreach ($stages as $stage)
                            <option value="{{ $stage->id }}" {{ $stage->id == $task->stage_id ? 'selected' : '' }}>
                                {{ $stage->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="pt-2">
                    <h4>Estimated Time : {{ $task->estimated_hrs }} Hr</h4>
                </div>

            </div>

            <div class="col-12 gap-3 bottom_deviders mt-3">
                <h4 class="d-flex align-items-center mb-0">
                    <i class="fa-regular fa-user me-3"></i>Project Members
                </h4>

                <div class="my-2 px-3 sugges_image">
                    <div class="d-flex align-items-center flex-wrap gap-3">

                        {{-- Loop through project users --}}
                        @foreach($project->users as $user)
                            <div class="member-item item_showtool position-relative">
                                <img
                                    class="rounded-circle border border-primary"
                                    width="40" height="40"
                                    data-bs-toggle="tooltip"
                                    title="{{ $user->name }}"
                                    @if (($user->employee->gender ?? null) === GENDER_MALE)
                                        src="{{ asset('assets/assestsnew/menimg.png') }}"
                                    alt="{{ $user->name }}"
                                    @elseif (($user->employee->gender ?? null) === GENDER_FEMALE)
                                        src="{{ asset('assets/assestsnew/femaile-report.svg') }}"
                                    alt="{{ $user->name }}"
                                    @else
                                        src="{{ $user->avatar
                        ? asset('/storage/uploads/avatar/' . $user->avatar)
                        : asset('assets/assestsnew/profile.png') }}"
                                    alt="{{ $user->name }}"
                                    @endif
                                >
                            </div>
                        @endforeach

                    </div>
                </div>
            </div>

            <div class="col-12 gap-3 bottom_deviders mt-3">
                <h4 class="d-flex align-items-center mb-0">
                    <i class="fa-regular fa-user me-3"></i>Task Members
                </h4>

                <div class="my-2 px-3 sugges_image">
                    <div class="d-flex align-items-center flex-wrap gap-3">

                        @foreach($assignedUsers as $user)
                            <div class="member-item item_showtool position-relative">
                                <img
                                    class="rounded-circle border border-primary"
                                    width="40" height="40"
                                    data-bs-toggle="tooltip"
                                    title="{{ $user->name }}"
                                    @if (($user->employee->gender ?? null) === GENDER_MALE)
                                        src="{{ asset('assets/assestsnew/menimg.png') }}"
                                    @elseif (($user->employee->gender ?? null) === GENDER_FEMALE)
                                        src="{{ asset('assets/assestsnew/femaile-report.svg') }}"
                                    @else
                                        src="{{ $user->avatar
                            ? asset('/storage/uploads/avatar/' . $user->avatar)
                            : asset('assets/assestsnew/profile.png') }}"
                                    @endif
                                    alt="{{ $user->name }}"
                                >
                            </div>
                        @endforeach

                    </div>
                </div>
            </div>

            <div class="col-12 gap-3 bottom_deviders mt-3">
                <div class="d-flex justify-content-between mb-3 align-items-center">
                    <h4 class="mb-0"><i
                            class="fa-regular fa-newspaper me-3"></i>Description</h4>
                </div>
                <span style="color: #6D6D6D;">{{ $task->description }}</span>
            </div>

            <div class="col-12 gap-3 bottom_deviders mt-3">
                {{-- Header --}}
                <div class="d-flex justify-content-between mb-3 align-items-center">
                    <h4 class="mb-0">
                        <i class="fa-solid fa-paperclip me-3"></i>Attachments
                    </h4>
                    <button class="edit_blues" data-bs-toggle="collapse" data-bs-target="#add_file"
                            aria-expanded="false">
                        Add<i class="fa-solid fa-plus ms-2"></i>
                    </button>
                </div>

                {{-- Upload Form --}}
                <form id="add_file" class="collapse pb-2" enctype="multipart/form-data">
                    @csrf

                    <div class="row align-items-center">

                        <div class="col-10">
                            <input type="file"
                                   name="file"
                                   id="task_attachment"
                                   class="form-control"
                                   required>
                        </div>

                        <div class="col-2">
                            <button class="btn btn-sm btn-primary w-100"
                                    type="button"
                                    id="file_attachment_submit"
                                    data-action="{{ route('organization.comment.store.file', [$task->project_id, $task->id]) }}">
                                <i class="fa-solid fa-check"></i>
                            </button>
                        </div>

                        <!-- Preview Section -->
                        <div class="col-12 mt-3 text-center">
                            <div id="previewContainer" style="display:none;">

                                <img id="imgPreview" style="max-height:120px; display:none;" class="rounded shadow-sm"/>
                                <video id="videoPreview" controls style="max-height:120px; display:none;"></video>
                                <audio id="audioPreview" controls style="display:none;"></audio>
                                <iframe id="docPreview"
                                        style="width:100%; max-height:150px; display:none; border:none;"></iframe>

                                <!-- Cancel preview -->
                                <button id="cancelPreview" type="button" class="btn btn-sm btn-danger mt-2">
                                    Cancel
                                </button>

                            </div>
                        </div>

                    </div>
                </form>

                {{-- File List --}}
                <div id="comments-file" class="mt-3">
                    @foreach($task->taskFiles as $file)
                        <div class="d-flex align-items-center mb-3 border-bottom pb-2 task-file"
                             data-id="{{ $file->id }}">
                            <div class="pe-3">
                                <img src="{{ asset('assets/assestsnew/doucuments_task.svg') }}" alt="">
                            </div>

                            <div class="flex-grow-1 main_documentnames">
                                <h6 class="mb-1">{{ $file->name }}</h6>
                                <small>Uploaded {{ \Carbon\Carbon::parse($file->created_at)->format('d M Y, h:i A') }}</small>
                            </div>

                            <div class="dropdown ms-2">
                                <button type="button" class="btn p-0 border-0" data-bs-toggle="dropdown"
                                        aria-expanded="false">
                                    <i class="ti ti-dots-vertical"></i>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end">
                                    <li>
                                        <a class="dropdown-item" target="_blank"
                                           href="{{ \App\Models\Utility::get_file($file->file) }}">
                                            <i class="ti ti-eye"></i> View
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item"
                                           href="{{ \App\Models\Utility::get_file($file->file) }}" download>
                                            <i class="ti ti-download"></i> Download
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item text-danger delete-comment-file"
                                           href="#"
                                           data-url="{{ route('organization.comment.destroy.file', [$task->project_id, $task->id, $file->id]) }}">
                                            <i class="ti ti-trash"></i> Delete
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="col-12 gap-3 bottom_deviders mt-3">
                <div class="d-flex justify-content-between mb-3 align-items-center">
                    <h4 class="d-flex align-items-center mb-0"><i
                            class="fa-solid fa-calendar me-3"></i></i>Dates</h4>
                </div>
                <div class="row">
                    <div class="col-md-4">
                        <small class="fw-medium fs-6">Start Date</small>
                        <p class="fw-medium fs-6" style="color: #A2A2A2;">{{ $task->start_date }}</p>
                    </div>
                    <div class="col-md-4">
                        <small class="fw-medium fs-6">End Date</small>
                        <p class="fw-medium fs-6" style="color: #A2A2A2;">{{ $task->end_date }}</p>
                    </div>
                </div>
            </div>

            <div class="col-12">
                <div class="checklist-card my-4">
                    <div class="checklist-header d-flex justify-content-between align-items-center">
                        <h4><i class="fa-regular fa-square-check"></i> Checklist</h4>
                    </div>

                    {{-- Add Checklist Input --}}
                    <div class="my-3">
                        <form id="checklistForm"
                              data-action="{{ route('organization.checklist.store', [$task->project_id, $task->id]) }}">
                            @csrf
                            <div class="row g-2 align-items-center">
                                <div class="col-10">
                                    <input type="text" name="name" class="form-control" placeholder="Enter Todo"
                                           required>
                                </div>
                                <div class="col-2">
                                    <button type="button" id="addChecklistBtn" class="btn btn-primary w-100">
                                        <i class="fa-solid fa-check"></i>
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>

                    {{-- Checklist Items --}}
                    <div class="checklist-items" id="checklistItems">
                        @foreach ($task->checklist as $checklist)
                            <div class="checklist-item d-flex justify-content-between align-items-center"
                                 data-id="{{ $checklist->id }}">
                                <div class="form-check">
                                    <input class="form-check-input checklist-status" type="checkbox"
                                           id="check-item-{{ $checklist->id }}"
                                           data-url="{{ route('organization.checklist.update', [$task->project_id, $checklist->id]) }}"
                                        {{ $checklist->status ? 'checked' : '' }}>
                                    <label class="form-check-label" for="check-item-{{ $checklist->id }}">
                                        {{ $checklist->name }}
                                    </label>
                                </div>
                                <span class="btn btn-danger btn-sm text-nowrap delete-checklist"
                                      data-url="{{ route('organization.checklist.destroy', [$task->project_id, $checklist->id]) }}">
    <i class="fa-solid fa-trash"></i> Delete
</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="col-12 gap-3 bottom_deviders mt-3">
                <div class="d-flex justify-content-between mb-3 align-items-center">
                    <h4 class="mb-0"><i class="fa-solid fa-message me-3"></i>Comments</h4>
                </div>

                {{-- ✅ Comment Input Box --}}
                <div class="comment-box d-flex align-items-center px-3 py-2">
                    <div class="d-flex align-items-center gap-2 ms-2 w-100">
                        <form id="commentForm" class="d-flex align-items-center w-100"
                              data-action="{{ route('organization.task.comment.store', [$task->project_id, $task->id]) }}">
                            @csrf
                            <input type="text" name="comment" class="comment-input flex-grow-1 form-control"
                                   placeholder="Add a comment..." required>
                            <button type="button" id="commentSubmitBtn" class="btn btn-primary ms-2">
                                <i class="fas fa-paper-plane"></i>
                            </button>
                        </form>
                    </div>
                </div>

                {{-- ✅ Comment List --}}
                <div id="commentList" class="mt-4">
                    @foreach($task->comments as $comment)
                        @php $user = \App\Models\User::find($comment->user_id); @endphp
                        <div class="comment-box d-flex align-items-center p-3 mb-2 border rounded">
                            {{--                            <img--}}
                            {{--                                src="{{ $user->avatar ? asset('storage/uploads/avatar/'.$user->avatar) : asset('storage/uploads/avatar/avatar.png') }}"--}}
                            {{--                                class="rounded-circle me-2" width="40" height="40" alt="avatar">--}}

                            <img
                                class="rounded-circle me-2"
                                width="40" height="40"
                                @php
                                    $gender = $user->employee->gender ?? null;
                                    $userProfile = \App\Models\Utility::get_file($user->avatar);
                                @endphp

                                @if ($gender === GENDER_MALE)
                                    src="{{ asset('assets/assestsnew/menimg.png') }}"
                                @elseif ($gender === GENDER_FEMALE)
                                    src="{{ asset('assets/assestsnew/femaile-report.svg') }}"
                                @else
                                    src="{{ $user->avatar ? $userProfile : asset('assets/assestsnew/menimg.png') }}"
                                @endif

                                alt="{{ $user->name ?? 'User' }}"
                            >

                            <div class="flex-grow-1">
                                <p class="mb-1">
                                    <strong>{{ $user->name ?? 'User' }}</strong>
                                    <span class="text-muted">{{ $comment->comment }}</span>
                                </p>
                                <p class="text-primary mb-1 small">{{ $comment->created_at->diffForHumans() }}</p>
                            </div>
                            <div class="dropdown ms-2">
                                <button type="button" class="btn p-0 border-0" data-bs-toggle="dropdown"
                                        aria-expanded="false">
                                    <i class="ti ti-dots-vertical"></i>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end">
                                    <li>
                                        <a href="#!" class="dropdown-item text-danger delete-comment"
                                           data-url="{{ route('organization.comment.destroy', [$task->project_id, $task->id, $comment->id]) }}">
                                            <i class="ti ti-trash"></i> Delete
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="row my-5">
                <div class="col-12">
                    <div class="d-flex justify-content-between mb-3 align-items-center">
                        <h4 class="mb-0">
                            <i class="fa-solid fa-chart-line me-3"></i>Activity
                        </h4>
                    </div>

                    @foreach($task->activity_log() as $activity)
                        <div class="d-flex align-items-start mb-3">

                            {{-- Activity Icon (same as your first snippet) --}}
                            <div class="theme-avtar bg-primary badge me-3">
                                <i class="ti {{ $activity->logIcon($activity->log_type) }}"></i>
                            </div>

                            {{-- Activity Content --}}
                            <div class="flex-grow-1">
                                <p class="mb-0">
                                    <strong>{{ __($activity->log_type) }}</strong>
                                </p>
                                <p class="mb-0">{!! $activity->getRemark() !!}</p>
                                <p class="text-muted mb-0">{{ $activity->created_at->diffForHumans() }}</p>

                                {{-- Optional reply box --}}
                                <div class="reply-input d-none mt-2">
                                    <textarea class="form-control" rows="1" placeholder="Write a reply..."></textarea>
                                    <button class="btn btn-sm btn-primary mt-1">Send</button>
                                </div>
                            </div>
                        </div>

                        <hr class="my-3">
                    @endforeach
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
@endsection
