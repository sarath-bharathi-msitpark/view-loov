@extends('company.layouts.company')

@section('page-title')
    {{ __('Project Management') }}
@endsection

@section('page-icon')
    {{ asset('assets/assestsnew/play.svg') }}
@endsection

@push('css-page')

@endpush

@push('theme-script')

@endpush

@push('script-page')
    <script>
        const toggleBtn = document.getElementById('toggleViewBtn');
        const contentRow = document.getElementById('contentRow');
        const toggleIcon = document.getElementById('toggleIcon');
        const clearFilterContainer = document.getElementById('clearFilterContainer');
        const selectorContainer = document.getElementById('selectorContainer');
        const listBox = document.querySelector('.list_box');
        const listBox1 = document.querySelector('.list_box1');


        let isGrid = true;

        toggleBtn.addEventListener('click', () => {
            isGrid = !isGrid;

            // Toggle icon
            toggleIcon.src = isGrid ? '.{{ asset('assets/assestsnew/listmenu.svg') }}' : '{{ asset('assets/assestsnew/gridmenu.svg') }}';

            if (isGrid) {
                contentRow.classList.remove('d-none');
                listBox.classList.add('d-none');
                listBox1.classList.add('d-none');
                clearFilterContainer.classList.remove('d-none');
                selectorContainer.classList.remove('d-none');
            } else {
                contentRow.classList.add('d-none');
                listBox.classList.remove('d-none');
                listBox1.classList.remove('d-none');
                clearFilterContainer.classList.add('d-none');
                selectorContainer.classList.add('d-none');
            }
        });
    </script>
@endpush

@section('content')
    @include('company.layouts.partials.nav')
    <div class="col-12 mb-5">
        <div class="col-12">
            <div class="row mt-5">
                <div class="col-lg-6 selecters_head">
                    <h2 class="mb-0">Manage Project</h2>
                </div>

                <div class="col-lg-6 selecters_head">
                    <div class="row justify-content-lg-end gx-4">
                        <div id="clearFilterContainer" class="col-auto clear-filter">
                                    <span>
                                        <button class="download_arrbtn"><i class="fas fa-redo-alt"></i></button>
                                        Clear Filter
                                    </span>
                        </div>
                        <div id="selectorContainer" class="col-auto d-block">
                            <div class="row">
                                <select class="form-select sector-select" style="width: 100px;">
                                    <option value="">All Team</option>
                                    <option value="">Team 1</option>
                                    <option value="">Team 2</option>
                                    <option value="">Team 3</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-auto">
                                    <span>
                                        <button class="download_arrbtn"><i class="fa-solid fa-filter"></i></button>
                                    </span>
                        </div>
                        <div class="col-auto">
                                    <span>
                                        <button class="download_arrbtn" data-bs-toggle="modal"
                                                data-bs-target="#editTeamModal"><i
                                                class="fa-solid fa-plus"></i></button>
                                    </span>
                        </div>
                        <div class="col-auto">
                            <span>
                                <button id="toggleViewBtn" class="download_arrbtn"
                                        style="background-color: #316FF6;">
                                    <img id="toggleIcon" src="{{ asset('assets/assestsnew/listmenu.svg') }}"
                                         alt="Toggle View">
                                </button>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Add user modal -->
        <div class="modal fade" id="editTeamModal" tabindex="-1" aria-labelledby="editTeamModalLabel"
             aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered ">
                <div class="modal-content rounded-5 shadow p-4">
                    <div class="modal-header border-0">
                        <h5 class="modal-title w-100 text-center fw-semibold fs-4" id="editTeamModalLabel">Edit
                            Team</h5>
                    </div>
                    <div class="modal-body">
                        <form>
                            <div class="">
                                <label for="teamName" class="form-label fw-semibold">Project Name<span
                                        style="color: red;">*</span></label>
                                <input type="text" class="form-control" style="border-radius: 100px; "
                                       id="teamName" placeholder="Enter" required>
                            </div>
                            <div class="row mt-3">
                                <div class="col-6">
                                    <label for="teamEmail" class="form-label fw-semibold">Start Date<span
                                            style="color: red;">*</span></label>
                                    <input type="date" class="form-control dob-input" id="" required
                                           style="border-radius: 100px;">
                                </div>
                                <div class="col-6">
                                    <label for="" class="form-label fw-semibold">End Date</label>
                                    <input type="date" class="form-control dob-input" id="" required
                                           style="border-radius: 100px;">
                                </div>

                            </div>
                            <div class="row mt-3">
                                <div class="col-12">
                                    <div class="custom-input-container">
                                        <label for="projectInput" class="custom-input-label required">Project
                                            Image</label>
                                        <div class="position-relative">
                                            <input type="file" class="custom-input-field" id="projectInput"
                                                   placeholder="Browse">
                                            <button id="projectInput"
                                                    class="custom-input-button">Choose
                                            </button>
                                        </div>
                                    </div>
                                </div>

                            </div>
                            <div class="row mt-3">
                                <div class="col-6">
                                    <label for="" class="form-label fw-semibold">Client<span
                                            style="color: red;">*</span></label>
                                    <select class="form-select" id="" required
                                            style="border-radius: 100px; color: #BCBCBC;">
                                        <option value="">Select Client</option>
                                        <option value="">Select Client 1</option>
                                        <option value="">Select Client 2</option>
                                        <option value="">Select Client 3</option>
                                    </select>
                                </div>
                                <div class="col-6">
                                    <label for="" class="form-label fw-semibold">Users <span
                                            style="color: red;">*</span></label>
                                    <select class="form-select" id="" required
                                            style="border-radius: 100px; color: #BCBCBC;">
                                        <option value="">Select Users</option>
                                        <option value="">Select Users 1</option>
                                        <option value="">Select Users 2</option>
                                        <option value="">Select Users 3</option>
                                    </select>
                                </div>


                            </div>
                            <div class="row mt-3">
                                <div class="col-6">
                                    <label for="" class="form-label fw-semibold">Budget </label>
                                    <select class="form-select" id="" required
                                            style="border-radius: 100px; color: #BCBCBC;">
                                        <option value="">Enter Project Budget</option>
                                        <option value="">Select 1</option>
                                        <option value="">Select 2</option>
                                        <option value="">Select 3</option>
                                    </select>
                                </div>
                                <div class="col-6">
                                    <label for="" class="form-label fw-semibold">Estimated Hours</label>
                                    <select class="form-select" id="" required
                                            style="border-radius: 100px; color: #BCBCBC;">
                                        <option value="">Enter Project Estimated Hours</option>
                                        <option value="">Select 1</option>
                                        <option value="">Select 2</option>
                                        <option value="">Select 3</option>
                                    </select>
                                </div>
                            </div>
                            <div class="row mt-3">
                                <div class="col-12">
                                    <label for="" class="form-label fw-semibold">Description</label>
                                    <textarea class="form-control" id="teamDesc" rows=""
                                              placeholder="Enter Comments" style="border-radius: 20px;"></textarea>
                                </div>
                            </div>

                            <div class="d-flex justify-content-end gap-2 mt-4">
                                <button type="button" class="btn btn-outline-primary px-4"
                                        data-bs-dismiss="modal" style="border-radius: 100px;">Cancel
                                </button>
                                <button type="submit" class="btn btn-primary px-4"
                                        style="border-radius: 100px;">Submit
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div id="contentRow" class="row gx-3 gy-4 mt-3">
            <!-- Repeat this block for each card -->
            <div class="col-12 col-md-6 col-lg-3 p-0">
                <div class="project-card">
                    <!-- Card Header -->
                    <div class="card-header py-3 px-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="d-flex align-items-center">
                                <img src="{{ asset('assets/assestsnew/vwlogo.png') }}" alt="VW Logo" class="me-2"
                                     style="width: 40px; height: 40px;">
                                <div class="project-name fw-semibold text-primary">Project Name</div>
                            </div>
                            <button class="menu-dots border-0 bg-transparent p-0">
                                <i class="fa-solid fa-ellipsis-vertical fs-5"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Status Badge -->
                    <div class="cent_box">
                        <div class="status-badge1">IN PROGRESS</div>
                        <p class="mb-0 px-2" style="color: #7E7E7E;">The goal of this project is to improve
                            operational efficiency within the
                            organization by implementing process automation.</p>
                        <!-- Card Content -->
                        <div class="card-content">
                            <!-- Members Section -->
                            <div class="members-section">
                                <div class="members-label">Members</div>
                                <div class="members-avatars">
                                    <div class="member-avatar">
                                        <img src="{{ asset('assets/assestsnew/woman1.png') }}" alt="Member 1">
                                    </div>
                                    <div class="member-avatar">
                                        <img src="{{ asset('assets/assestsnew/woman2.png') }}" alt="Member 2">
                                    </div>
                                    <div class="member-avatar">
                                        <img src="{{ asset('assets/assestsnew/newman1.png') }}" alt="Member 3">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Date Section -->
                    <div class="date-section">
                        <div class="row">
                            <div class="col-6 start-date">
                                <div class="date-value">25 Apr 2025</div>
                                <div class="date-label">Start Date</div>
                            </div>
                            <div class="col-6 end-date text-end">
                                <div class="date-value">15 May 2025</div>
                                <div class="date-label">End Date</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-6 col-lg-3 p-0">
                <div class="project-card">
                    <!-- Card Header -->
                    <div class="card-header py-3 px-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="d-flex align-items-center">
                                <img src="{{ asset('assets/assestsnew/macdonals.png') }}" alt="VW Logo" class="me-2"
                                     style="width: 40px; height: 40px;">
                                <div class="project-name fw-semibold text-primary">Project Name</div>
                            </div>
                            <button class="menu-dots border-0 bg-transparent p-0">
                                <i class="fa-solid fa-ellipsis-vertical fs-5"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Status Badge -->
                    <div class="cent_box">
                        <div class="status-badge2">ON HOLD</div>
                        <p class="mb-0 px-2" style="color: #7E7E7E;">The goal of this project is to improve
                            operational efficiency within the
                            organization by implementing process automation.</p>
                        <!-- Card Content -->
                        <div class="card-content">
                            <!-- Members Section -->
                            <div class="members-section">
                                <div class="members-label">Members</div>
                                <div class="members-avatars">
                                    <div class="member-avatar">
                                        <img src="{{ asset('assets/assestsnew/woman1.png') }}" alt="Member 1">
                                    </div>
                                    <div class="member-avatar">
                                        <img src="{{ asset('assets/assestsnew/woman2.png') }}" alt="Member 2">
                                    </div>
                                    <div class="member-avatar">
                                        <img src="{{ asset('assets/assestsnew/newman1.png') }}" alt="Member 3">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Date Section -->
                    <div class="date-section">
                        <div class="row">
                            <div class="col-6 start-date">
                                <div class="date-value">25 Apr 2025</div>
                                <div class="date-label">Start Date</div>
                            </div>
                            <div class="col-6 end-date text-end">
                                <div class="date-value">15 May 2025</div>
                                <div class="date-label">End Date</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-6 col-lg-3 p-0">
                <div class="project-card">
                    <!-- Card Header -->
                    <div class="card-header py-3 px-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="d-flex align-items-center">
                                <img src="{{ asset('assets/assestsnew/tesla.png') }}" alt="VW Logo" class="me-2"
                                     style="width: 40px; height: 40px;">
                                <div class="project-name fw-semibold text-primary">Project Name</div>
                            </div>
                            <button class="menu-dots border-0 bg-transparent p-0">
                                <i class="fa-solid fa-ellipsis-vertical fs-5"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Status Badge -->
                    <div class="cent_box">


                        <div class="status-badge3">COMPLETE</div>
                        <p class="mb-0 px-2" style="color: #7E7E7E;">The goal of this project is to improve
                            operational efficiency within the
                            organization by implementing process automation.</p>

                        <!-- Card Content -->
                        <div class="card-content">
                            <!-- Members Section -->
                            <div class="members-section">
                                <div class="members-label">Members</div>
                                <div class="members-avatars">
                                    <div class="member-avatar">
                                        <img src="{{ asset('assets/assestsnew/woman1.png') }}" alt="Member 1">
                                    </div>
                                    <div class="member-avatar">
                                        <img src="{{ asset('assets/assestsnew/woman2.png') }}" alt="Member 2">
                                    </div>
                                    <div class="member-avatar">
                                        <img src="{{ asset('assets/assestsnew/newman1.png') }}" alt="Member 3">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Date Section -->
                    <div class="date-section">
                        <div class="row">
                            <div class="col-6 start-date">
                                <div class="date-value">25 Apr 2025</div>
                                <div class="date-label">Start Date</div>
                            </div>
                            <div class="col-6 end-date text-end">
                                <div class="date-value">15 May 2025</div>
                                <div class="date-label">End Date</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-6 col-lg-3 p-0">
                <div class="project-card">
                    <!-- Card Header -->
                    <div class="card-header py-3 px-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="d-flex align-items-center">
                                <img src="{{ asset('sestsnew/benz.png') }}" alt="VW Logo" class="me-2"
                                     style="width: 40px; height: 40px;">
                                <div class="project-name fw-semibold text-primary">Project Name</div>
                            </div>
                            <button class="menu-dots border-0 bg-transparent p-0">
                                <i class="fa-solid fa-ellipsis-vertical fs-5"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Status Badge -->
                    <div class="cent_box">


                        <div class="status-badge4">CANCELLED</div>
                        <p class="mb-0 px-2" style="color: #7E7E7E;">The goal of this project is to improve
                            operational efficiency within the organization by implementing process automation.
                        </p>
                        <!-- Card Content -->
                        <div class="card-content">
                            <!-- Members Section -->
                            <div class="members-section">
                                <div class="members-label">Members</div>
                                <div class="members-avatars">
                                    <div class="member-avatar">
                                        <img src="{{ asset('assets/assestsnew/woman1.png') }}" alt="Member 1">
                                    </div>
                                    <div class="member-avatar">
                                        <img src="{{ asset('assets/assestsnew/woman2.png') }}" alt="Member 2">
                                    </div>
                                    <div class="member-avatar">
                                        <img src="{{ asset('assets/assestsnew/newman1.png') }}" alt="Member 3">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Date Section -->
                    <div class="date-section">
                        <div class="row">
                            <div class="col-6 start-date">
                                <div class="date-value">25 Apr 2025</div>
                                <div class="date-label">Start Date</div>
                            </div>
                            <div class="col-6 end-date text-end">
                                <div class="date-value">15 May 2025</div>
                                <div class="date-label">End Date</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-6 col-lg-3 p-0">
                <div class="project-card">
                    <!-- Card Header -->
                    <div class="card-header py-3 px-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="d-flex align-items-center">
                                <img src="{{ asset('assets/assestsnew/kodak.png') }}" alt="VW Logo" class="me-2"
                                     style="width: 40px; height: 40px;">
                                <div class="project-name fw-semibold text-primary">Project Name</div>
                            </div>
                            <button class="menu-dots border-0 bg-transparent p-0">
                                <i class="fa-solid fa-ellipsis-vertical fs-5"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Status Badge -->
                    <div class="cent_box">


                        <div class="status-badge2">ON HOLD</div>
                        <p class="mb-0 px-2" style="color: #7E7E7E;">The goal of this project is to improve
                            operational efficiency within the organization by implementing process automation.
                        </p>
                        <!-- Card Content -->
                        <div class="card-content">
                            <!-- Members Section -->
                            <div class="members-section">
                                <div class="members-label">Members</div>
                                <div class="members-avatars">
                                    <div class="member-avatar">
                                        <img src="{{ asset('assets/assestsnew/woman1.png') }}" alt="Member 1">
                                    </div>
                                    <div class="member-avatar">
                                        <img src="{{ asset('assets/assestsnew/woman2.png') }}" alt="Member 2">
                                    </div>
                                    <div class="member-avatar">
                                        <img src="{{ asset('assets/assestsnew/newman1.png') }}" alt="Member 3">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Date Section -->
                    <div class="date-section">
                        <div class="row">
                            <div class="col-6 start-date">
                                <div class="date-value">25 Apr 2025</div>
                                <div class="date-label">Start Date</div>
                            </div>
                            <div class="col-6 end-date text-end">
                                <div class="date-value">15 May 2025</div>
                                <div class="date-label">End Date</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-6 col-lg-3 p-0">
                <div class="project-card">
                    <!-- Card Header -->
                    <div class="card-header py-3 px-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="d-flex align-items-center">
                                <img src="{{ asset('assets/assestsnew/infinityA.png') }}" alt="VW Logo" class="me-2"
                                     style="width: 40px; height: 40px;">
                                <div class="project-name fw-semibold text-primary">Project Name</div>
                            </div>
                            <button class="menu-dots border-0 bg-transparent p-0">
                                <i class="fa-solid fa-ellipsis-vertical fs-5"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Status Badge -->
                    <div class="cent_box">


                        <div class="status-badge4">CANCELLED</div>
                        <p class="mb-0 px-2" style="color: #7E7E7E;">The goal of this project is to improve
                            operational efficiency within the organization by implementing process automation.
                        </p>
                        <!-- Card Content -->
                        <div class="card-content">
                            <!-- Members Section -->
                            <div class="members-section">
                                <div class="members-label">Members</div>
                                <div class="members-avatars">
                                    <div class="member-avatar">
                                        <img src="{{ asset('assets/assestsnew/woman1.png') }}" alt="Member 1">
                                    </div>
                                    <div class="member-avatar">
                                        <img src="{{ asset('assets/assestsnew/woman2.png') }}" alt="Member 2">
                                    </div>
                                    <div class="member-avatar">
                                        <img src="{{ asset('assets/assestsnew/newman1.png') }}" alt="Member 3">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Date Section -->
                    <div class="date-section">
                        <div class="row">
                            <div class="col-6 start-date">
                                <div class="date-value">25 Apr 2025</div>
                                <div class="date-label">Start Date</div>
                            </div>
                            <div class="col-6 end-date text-end">
                                <div class="date-value">15 May 2025</div>
                                <div class="date-label">End Date</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-6 col-lg-3 p-0">
                <div class="project-card">
                    <!-- Card Header -->
                    <div class="card-header py-3 px-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="d-flex align-items-center">
                                <img src="{{ asset('assets/assestsnew/ikea.png') }}" alt="VW Logo" class="me-2"
                                     style="width: 40px; height: 40px;">
                                <div class="project-name fw-semibold text-primary">Project Name</div>
                            </div>
                            <button class="menu-dots border-0 bg-transparent p-0">
                                <i class="fa-solid fa-ellipsis-vertical fs-5"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Status Badge -->
                    <div class="cent_box">


                        <div class="status-badge1">IN PROGRESS</div>
                        <p class="mb-0 px-2" style="color: #7E7E7E;">The goal of this project is to improve
                            operational efficiency within the organization by implementing process automation.
                        </p>
                        <!-- Card Content -->
                        <div class="card-content">
                            <!-- Members Section -->
                            <div class="members-section">
                                <div class="members-label">Members</div>
                                <div class="members-avatars">
                                    <div class="member-avatar">
                                        <img src="{{ asset('assets/assestsnew/woman1.png') }}" alt="Member 1">
                                    </div>
                                    <div class="member-avatar">
                                        <img src="{{ asset('assets/assestsnew/woman2.png') }}" alt="Member 2">
                                    </div>
                                    <div class="member-avatar">
                                        <img src="{{ asset('assets/assestsnew/newman1.png') }}" alt="Member 3">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Date Section -->
                    <div class="date-section">
                        <div class="row">
                            <div class="col-6 start-date">
                                <div class="date-value">25 Apr 2025</div>
                                <div class="date-label">Start Date</div>
                            </div>
                            <div class="col-6 end-date text-end">
                                <div class="date-value">15 May 2025</div>
                                <div class="date-label">End Date</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-6 col-lg-3 p-0">
                <div class="project-card">
                    <!-- Card Header -->
                    <div class="card-header py-3 px-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="d-flex align-items-center">
                                <img src="{{ asset('assets/assestsnew/donat.png') }}" alt="VW Logo" class="me-2"
                                     style="width: 40px; height: 40px;">
                                <div class="project-name fw-semibold text-primary">Project Name</div>
                            </div>
                            <button class="menu-dots border-0 bg-transparent p-0">
                                <i class="fa-solid fa-ellipsis-vertical fs-5"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Status Badge -->
                    <div class="cent_box">


                        <div class="status-badge3">COMPLETE</div>
                        <p class="mb-0 px-2" style="color: #7E7E7E;">The goal of this project is to improve
                            operational efficiency within the organization by implementing process automation.
                        </p>
                        <!-- Card Content -->
                        <div class="card-content">
                            <!-- Members Section -->
                            <div class="members-section">
                                <div class="members-label">Members</div>
                                <div class="members-avatars">
                                    <div class="member-avatar">
                                        <img src="{{ asset('assets/assestsnew/woman1.png') }}" alt="Member 1">
                                    </div>
                                    <div class="member-avatar">
                                        <img src="{{ asset('assets/assestsnew/woman2.png') }}" alt="Member 2">
                                    </div>
                                    <div class="member-avatar">
                                        <img src="{{ asset('assets/assestsnew/newman1.png') }}" alt="Member 3">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Date Section -->
                    <div class="date-section">
                        <div class="row">
                            <div class="col-6 start-date">
                                <div class="date-value">25 Apr 2025</div>
                                <div class="date-label">Start Date</div>
                            </div>
                            <div class="col-6 end-date text-end">
                                <div class="date-value">15 May 2025</div>
                                <div class="date-label">End Date</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>


        <!-- Project Table View -->
        <div class="list_box d-none">
            <div class="row py-3">
                <div class="col-12">
                    <div class="row align-items-end g-3  justify-content-evenly">
                        <div class="col-xl-2 col-lg-3 col-md-4">
                            <label class="form-label fw-semibold">Users <span
                                    class="text-danger">*</span></label>
                            <select class="form-select" id="" required
                                    style="border-radius: 100px; color: #BCBCBC;">
                                <option value="">Select Client</option>
                                <option value="">Select Client 1</option>
                                <option value="">Select Client 2</option>
                                <option value="">Select Client 3</option>
                            </select>
                        </div>

                        <div class="col-xl-2 col-lg-3 col-md-4">
                            <label class="form-label fw-semibold">Status <span
                                    class="text-danger">*</span></label>
                            <select class="form-select" id="" required
                                    style="border-radius: 100px; color: #BCBCBC;">
                                <option value="">Select Client</option>
                                <option value="">Select Client 1</option>
                                <option value="">Select Client 2</option>
                                <option value="">Select Client 3</option>
                            </select>
                        </div>

                        <div class="col-xl-2 col-lg-3 col-md-4">
                            <label class="form-label fw-semibold">Start Date</label>
                            <input type="date" class="form-control dob-input" id="" required
                                   style="border-radius: 100px;">
                        </div>

                        <div class="col-xl-2 col-lg-3 col-md-4">
                            <label class="form-label fw-semibold">End Date </label>
                            <input type="date" class="form-control dob-input" id="" required
                                   style="border-radius: 100px;">
                        </div>

                        <div class="col-xl-2 col-lg-3 col-md-4 d-flex align-items-end gap-5">
                            <button class="btn btn-white"
                                    style="border-radius: 100px; border: 1px solid #D2D2D2;">
                                <i class="fa-solid fa-magnifying-glass"></i>
                            </button>

                            <button class="btn btn-white"
                                    style="border-radius: 100px; border: 1px solid #D2D2D2;">
                                <i class="fa-solid fa-rotate-right"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="list_box1 d-none">
            <div class="attendance-table-outer">
                <table class="attendance-table">
                    <thead>
                    <tr>
                        <th>PROJECTS
                            <i class="fas fa-sort pm-sort-icon"></i>
                        </th>
                        <th>START DATE <i class="fas fa-sort pm-sort-icon"></i></th>
                        <th>DUE DATE <i class="fas fa-sort pm-sort-icon"></i></th>
                        <th>PROJECTS MEMBERS <i class="fas fa-sort pm-sort-icon"></i></th>
                        <th>COMPLETION <i class="fas fa-sort pm-sort-icon"></i></th>
                        <th>STATUS <i class="fas fa-sort pm-sort-icon"></i></th>
                        <th>ACTION <i class="fas fa-sort pm-sort-icon"></i></th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr class="tex_fix">
                        <td class="fw-medium fs-5" style="color: #316FF6;">
                            ABC Project 01
                        </td>
                        <td>02 Jan 2025</td>
                        <td>25 Jan 2025</td>
                        <td>
                            <div class="members-avatars">
                                <div class="member-avatar">
                                    <img src="{{ asset('assets/assestsnew/woman1.png') }}" alt="Member 1">
                                </div>
                                <div class="member-avatar">
                                    <img src="{{ asset('assets/assestsnew/woman2.png') }}" alt="Member 2">
                                </div>
                                <div class="member-avatar">
                                    <img src="{{ asset('sestsnew/newman1.png') }}" alt="Member 3">
                                </div>
                            </div>
                        </td>
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="flex-grow-1 me-2">
                                    <small class="fw-semibold">68%</small>
                                    <div class="progress" style="height: 8px;">
                                        <div class="progress-bar"
                                             style="width: 68%; background-color: #0390AF;"></div>
                                    </div>
                                </div>

                            </div>
                        </td>
                        <td>
                            <div class="status-badge1">IN PROGRESS</div>
                        </td>
                        <td>
                            <div class="text-center d-flex gap-2">
                                <button class="btn p-2 d-flex align-items-center justify-content-center"
                                        style="width: 40px; height: 40px; border-radius: 50%; border: 1px solid #D2D2D2; background-color: #3D948D;">
                                    <img src="{{ asset('assets/assestsnew/eyewhite.svg') }}" alt="View"
                                         style="width: 16px; height: 16px;">
                                </button>
                                <button class="btn p-2 d-flex align-items-center justify-content-center"
                                        style="width: 40px; height: 40px; border-radius: 50%; border: 1px solid #D2D2D2; background-color: #316FF6;">
                                    <img src="{{ asset('assets/assestsnew/edit.svg') }}" alt="View"
                                         style="width: 16px; height: 16px;">
                                </button>
                            </div>
                        </td>
                    </tr>
                    <tr class="">
                        <td class="fw-medium fs-5" style="color: #316FF6;">
                            ABC Project 01
                        </td>
                        <td>02 Jan 2025</td>
                        <td>25 Jan 2025</td>
                        <td>
                            <div class="members-avatars">
                                <div class="member-avatar">
                                    <img src="{{ asset('assets/assestsnew/woman1.png') }}" alt="Member 1">
                                </div>
                                <div class="member-avatar">
                                    <img src="{{ asset('assets/assestsnew/woman2.png') }}" alt="Member 2">
                                </div>
                                <div class="member-avatar">
                                    <img src="{{ asset('assets/assestsnew/newman1.png') }}" alt="Member 3">
                                </div>
                            </div>
                        </td>
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="flex-grow-1 me-2">
                                    <small class="fw-semibold">25%</small>
                                    <div class="progress" style="height: 8px;">
                                        <div class="progress-bar"
                                             style="width: 25%; background-color: #E75212;"></div>
                                    </div>
                                </div>

                            </div>
                        </td>
                        <td>
                            <div class="status-badge2">ON HOLD</div>
                        </td>
                        <td>
                            <div class="text-center d-flex gap-2">
                                <button class="btn p-2 d-flex align-items-center justify-content-center"
                                        style="width: 40px; height: 40px; border-radius: 50%; border: 1px solid #D2D2D2; background-color: #3D948D;">
                                    <img src="{{ asset('assets/assestsnew/eyewhite.svg') }}" alt="View"
                                         style="width: 16px; height: 16px;">
                                </button>
                                <button class="btn p-2 d-flex align-items-center justify-content-center"
                                        style="width: 40px; height: 40px; border-radius: 50%; border: 1px solid #D2D2D2; background-color: #316FF6;">
                                    <img src="{{ asset('assets/assestsnew/edit.svg') }}" alt="View"
                                         style="width: 16px; height: 16px;">
                                </button>
                            </div>
                        </td>
                    </tr>

                    <tr class="tex_fix">
                        <td class="fw-medium fs-5" style="color: #316FF6;">
                            ABC Project 01
                        </td>
                        <td>02 Jan 2025</td>
                        <td>25 Jan 2025</td>
                        <td>
                            <div class="members-avatars">
                                <div class="member-avatar">
                                    <img src="{{ asset('assets/assestsnew/woman1.png') }}" alt="Member 1">
                                </div>
                                <div class="member-avatar">
                                    <img src="{{ asset('assets/assestsnew/woman2.png') }}" alt="Member 2">
                                </div>
                                <div class="member-avatar">
                                    <img src="{{ asset('assets/assestsnew/newman1.png') }}" alt="Member 3">
                                </div>
                            </div>
                        </td>
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="flex-grow-1 me-2">
                                    <small class="fw-semibold">100%</small>
                                    <div class="progress" style="height: 8px;">
                                        <div class="progress-bar"
                                             style="width: 100%; background-color: #08A718;"></div>
                                    </div>
                                </div>

                            </div>
                        </td>
                        <td>
                            <div class="status-badge3">COMPLETE</div>
                        </td>
                        <td>
                            <div class="text-center d-flex gap-2">
                                <button class="btn p-2 d-flex align-items-center justify-content-center"
                                        style="width: 40px; height: 40px; border-radius: 50%; border: 1px solid #D2D2D2; background-color: #3D948D;">
                                    <img src="{{ asset('assets/assestsnew/eyewhite.svg') }}" alt="View"
                                         style="width: 16px; height: 16px;">
                                </button>
                                <button class="btn p-2 d-flex align-items-center justify-content-center"
                                        style="width: 40px; height: 40px; border-radius: 50%; border: 1px solid #D2D2D2; background-color: #316FF6;">
                                    <img src="{{ asset('assets/assestsnew/edit.svg') }}" alt="View"
                                         style="width: 16px; height: 16px;">
                                </button>
                            </div>
                        </td>
                    </tr>

                    <tr class="">
                        <td class="fw-medium fs-5" style="color: #316FF6;">
                            ABC Project 01
                        </td>
                        <td>02 Jan 2025</td>
                        <td>25 Jan 2025</td>
                        <td>
                            <div class="members-avatars">
                                <div class="member-avatar">
                                    <img src="{{ asset('assets/assestsnew/woman1.png') }}" alt="Member 1">
                                </div>
                                <div class="member-avatar">
                                    <img src="{{ asset('assets/assestsnew/woman2.png') }}" alt="Member 2">
                                </div>
                                <div class="member-avatar">
                                    <img src="{{ asset('assets/assestsnew/newman1.png') }}" alt="Member 3">
                                </div>
                            </div>
                        </td>
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="flex-grow-1 me-2">
                                    <small class="fw-semibold">0%</small>
                                    <div class="progress" style="height: 8px;">
                                        <div class="progress-bar"
                                             style="width: 0%; background-color: #FF1010;"></div>
                                    </div>
                                </div>

                            </div>
                        </td>
                        <td>
                            <div class="status-badge4">CANCELLED</div>
                        </td>
                        <td>
                            <div class="text-center d-flex gap-2">
                                <button class="btn p-2 d-flex align-items-center justify-content-center"
                                        style="width: 40px; height: 40px; border-radius: 50%; border: 1px solid #D2D2D2; background-color: #3D948D;">
                                    <img src="{{ asset('assets/assestsnew/eyewhite.svg') }}" alt="View"
                                         style="width: 16px; height: 16px;">
                                </button>
                                <button class="btn p-2 d-flex align-items-center justify-content-center"
                                        style="width: 40px; height: 40px; border-radius: 50%; border: 1px solid #D2D2D2; background-color: #316FF6;">
                                    <img src="{{ asset('assets/assestsnew/edit.svg') }}" alt="View"
                                         style="width: 16px; height: 16px;">
                                </button>
                            </div>
                        </td>
                    </tr>


                    </tbody>
                </table>
            </div>
        </div>

    </div>
@endsection
