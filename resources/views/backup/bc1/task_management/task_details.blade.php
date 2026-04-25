@extends('company.layouts.company')

@section('page-title')
    {{ __('Break') }}
@endsection

@section('page-icon')
    {{ asset('assets/assestsnew/header-logo.svg') }}
@endsection

@push('css-page')
@endpush

@push('theme-script')
@endpush
@push('script-page')


@endpush

@section('content')
    @include('company.layouts.partials.nav')
    <!-- Task Template -->
    <div class="board d-none" id="newsletterSection" style="background: #EBF4FF;">
        <div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasRight"
             aria-labelledby="offcanvasRightLabel">
            <div class="offcanvas-header">
                <div class="d-flex w-100">
                    <div class="col-6">
                        <h5 id="offcanvasRightLabel">Task Details</h5>
                    </div>
                    <div class="col-6">
                        <div class="d-flex justify-content-end align-items-center gap-3">
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
                                <h4 class="d-flex align-items-center"><i
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
                                    <h4 class="d-flex align-items-center"><i
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
                                    <h4 class="d-flex align-items-center"><i
                                            class="fa-solid fa-paperclip me-3"></i>Attachments</h4>
                                    <button class="edit_blues">Add<i
                                            class="fa-solid fa-plus ms-2"></i></button>
                                </div>
                                <div class="d-flex align-items-center mb-3" style="position: relative;">
                                    <div>
                                        <div class="row pe-3 justify-content-center align-items-center">
                                            <img style="width: 80px;"
                                                 src="{{ asset('assets/assestsnew/documents.svg') }}" alt="">
                                        </div>
                                    </div>
                                    <div class="col-9 main_documentnames">
                                        <div class="row">
                                            <h6>Design Requirements Reports</h6>
                                            <small>Upload 25 Apr 2025, 10:00am</small>
                                        </div>
                                    </div>
                                    <div class="card-menu">⋮</div>
                                    <div class="card-dropdown">
                                        <div class="dropdown-item">Edit card</div>
                                        <div class="dropdown-item delete">Delete card</div>
                                    </div>
                                </div>
                                <div class="d-flex align-items-center mb-3" style="position: relative;">
                                    <div>
                                        <div class="row pe-3 justify-content-center align-items-center">
                                            <img style="width: 80px;"
                                                 src="{{ asset('assets/assestsnew/documents.svg') }}" alt="">
                                        </div>
                                    </div>
                                    <div class="col-9 main_documentnames">
                                        <div class="row">
                                            <h6>Design Requirements Reports</h6>
                                            <small>Upload 25 Apr 2025, 10:00am</small>
                                        </div>
                                    </div>
                                    <div class="card-menu">⋮</div>
                                    <div class="card-dropdown">
                                        <div class="dropdown-item">Edit card</div>
                                        <div class="dropdown-item delete">Delete card</div>
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
                                <div class="row">
                                    <div class="col-4">

                                        <small class="fw-medium fs-6">Status</small>
                                        <select class="form-select mt-1" style="background-color: #EAF5FF;">
                                            <option value="">High</option>
                                            <option value="">Team 1</option>
                                            <option value="">Team 2</option>
                                            <option value="">Team 3</option>
                                        </select>
                                    </div>
                                    <div class="col-4">

                                        <small class="fw-medium fs-6">T-Shirt Size</small>
                                        <select class="form-select mt-1" style="background-color: #EAF5FF;">
                                            <option value="">L-32 hrs</option>
                                            <option value="">Team 1</option>
                                            <option value="">Team 2</option>
                                            <option value="">Team 3</option>
                                        </select>
                                    </div>
                                    <div class="col-4">

                                        <small class="fw-medium fs-6">Actual Time</small>
                                        <select class="form-select mt-1">
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
                                    <h4 class="d-flex align-items-center"><i
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

                            <div class="col-12 gap-3 bottom_deviders mt-3">
                                <div class="d-flex justify-content-between mb-3 align-items-center">
                                    <h4 class="d-flex align-items-center"><i
                                            class="fa-solid fa-message me-3"></i>Activity</h4>
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


                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Model 1 -->
    <div class="modal fade" id="newFieldModal" tabindex="-1" aria-labelledby="newFieldModalLabel" aria-hidden="true">
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
                                    style="border-radius: 100px; background-color: #C4DEFF; color: #316FF6;">Add
                            </button>
                        </div>
                    </div>
                    <button class="btn btn-primary w-100 btn-create mt-2" style="border-radius: 100px;">Create</button>
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
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body px-3 pt-0">
                    <div class="position-relative w-100">
                        <input type="text" class="form-control rounded-pill ps-4 pe-5" placeholder="Search Members.."/>
                        <span class="position-absolute end-0 top-50 translate-middle-y pe-3">
                            <i class="fa fa-search" style="color: #316FF6;"></i>
                        </span>
                    </div>

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

    <!-- Model 3 -->
    <div class="modal fade" id="dateModal" tabindex="-1" aria-labelledby="dateModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content rounded-4 shadow" style="border: none;">
                <div class="modal-body p-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="fw-bold mb-0">Dates</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="p-3 bg-light rounded mb-4 text-center">
                        <div class="fw-bold">📅 Calendar UI Here</div>
                        <small class="text-muted">Use Flatpickr or Pikaday for full calendar functionality</small>
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
