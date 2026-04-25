@extends('company.layouts.company')

@section('page-title')
    {{ __('Task Management') }}
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
        const filterContainer = document.getElementById('filterContainer');


        let isGrid = true;

        toggleBtn.addEventListener('click', () => {
            isGrid = !isGrid;

            // Toggle icon
            toggleIcon.src = isGrid ? '{{ asset('assets/assestsnew/listmenu.svg') }}' : '{{ asset('assets/assestsnew/gridmenu.svg') }}';

            if (isGrid) {
                contentRow.classList.remove('d-none');
                listBox.classList.add('d-none');
                clearFilterContainer.classList.add('d-none');
                selectorContainer.classList.add('d-none');
                filterContainer.classList.add('d-none');
            } else {
                contentRow.classList.add('d-none');
                listBox.classList.remove('d-none');
                clearFilterContainer.classList.remove('d-none');
                selectorContainer.classList.remove('d-none');
                filterContainer.classList.remove('d-none');
            }
        });


    </script>
@endpush

@section('content')
    @include('company.layouts.partials.nav')
    <!-- Task Managment -->
    <div class="col-12" id="taskManagementContainer">
        <div class="row mt-5">
            <div class="col-lg-6 selecters_head">
                <h2 class="mb-0">Tasks</h2>
            </div>

            <div class="col-lg-6 selecters_head">
                <div class="row justify-content-lg-end gx-4">
                    <div id="clearFilterContainer" class="col-auto clear-filter  d-none">
                                    <span>
                                        <button class="download_arrbtn"><i class="fas fa-redo-alt"></i></button>
                                        Clear Filter
                                    </span>
                    </div>
                    <div id="selectorContainer" class="col-auto d-none">
                        <div class="row">
                            <select class="form-select sector-select" style="width: 100px;">
                                <option value="">All Team</option>
                                <option value="">Team 1</option>
                                <option value="">Team 2</option>
                                <option value="">Team 3</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-auto d-none" id="filterContainer">
                                    <span>
                                        <button class="download_arrbtn"><i class="fa-solid fa-filter"></i></button>
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

    <!-- Task Grid View -->
    <div id="contentRow" class="row mt-3 mb-5">
        <div class="col-md-3">
            <div class="task_box">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="tag_1">
                        <p class="mb-0">Medium</p>
                    </div>
                    <div class="">
                        <p class="mb-0 dates">02 Jan 2025</p>
                    </div>
                </div>
                <h6 class="text-black fw-medium mt-3">Website Redesign</h6>

                <div class="d-flex align-items-center">
                    <div class="flex-grow-1 me-2">
                        <div class="custom-progress">
                            <div class="custom-progress-bar" style="width: 20.74%;"></div>
                        </div>
                    </div>
                    <small class="fw-semibold">20.74%</small>
                </div>
                <div class="d-flex gap-3 mt-3">
                    <div class="pin_box">
                        <div class="d-flex align-items-center gap-2">
                            <img src="{{ asset('assets/assestsnew/paperclip.svg') }}" alt="">
                            <p class="mb-0 text-black fw-medium">2</p>
                        </div>
                    </div>
                    <div class="pin_box">
                        <div class="d-flex align-items-center gap-2">
                            <img src="{{ asset('assets/assestsnew/message.svg') }}" alt="">
                            <p class="mb-0 text-black fw-medium">2</p>
                        </div>
                    </div>
                </div>
                <hr>
                <div class="row">

                    <div class="col-6">
                        <div class="d-flex align-items-center gap-2">
                            <img src="{{ asset('assets/assestsnew/woman1.png') }}" alt="Member 1">
                            <img class="imgmove" src="{{ asset('assets/assestsnew/newman1.png') }}" alt="Member 3">
                        </div>
                    </div>

                    <div class="col-6 d-flex justify-content-end">
                        <a class="text-decoration-none"
                           href="{{ route('organization.task_management.taskWiseScreenShot') }}">
                            <div class="screenround">
                                <img src="{{ asset('assets/assestsnew/landscape.svg') }}" alt="">
                            </div>
                        </a>
                    </div>
                </div>

            </div>
        </div>

        <div class="col-md-3 ">
            <div class="task_box">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="tag_1">
                        <p class="mb-0">Medium</p>
                    </div>
                    <div class="">
                        <p class="mb-0 dates">02 Jan 2025</p>
                    </div>
                </div>
                <h6 class="text-black fw-medium mt-3">Website Redesign</h6>

                <div class="d-flex align-items-center">
                    <div class="flex-grow-1 me-2">
                        <div class="custom-progress">
                            <div class="custom-progress-bar" style="width: 68%;"></div>
                        </div>
                    </div>
                    <small class="fw-semibold">68%</small>
                </div>
                <div class="d-flex gap-3 mt-3">
                    <div class="pin_box">
                        <div class="d-flex align-items-center gap-2">
                            <img src="{{ asset('assets/assestsnew/paperclip.svg') }}" alt="">
                            <p class="mb-0 text-black fw-medium">2</p>
                        </div>
                    </div>
                    <div class="pin_box">
                        <div class="d-flex align-items-center gap-2">
                            <img src="{{ asset('assets/assestsnew/message.svg') }}" alt="">
                            <p class="mb-0 text-black fw-medium">2</p>
                        </div>
                    </div>
                </div>
                <hr>
                <div class="row">
                    <div class="col-6">
                        <div class="d-flex align-items-center gap-2">
                            <img src="{{ asset('assets/assestsnew/woman1.png') }}" alt="Member 1">
                            <img class="imgmove" src="{{ asset('assets/assestsnew/newman1.png') }}" alt="Member 3">
                        </div>
                    </div>

                    <div class="col-6 d-flex justify-content-end">
                        <a class="text-decoration-none"
                           href="{{ route('organization.task_management.taskWiseScreenShot') }}">
                            <div class="screenround">
                                <img src="{{ asset('assets/assestsnew/landscape.svg') }}" alt="">
                            </div>
                        </a>
                    </div>
                </div>

            </div>
        </div>

        <div class="col-md-3 ">
            <div class="task_box">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="tag_green">
                        <p class="mb-0">Low</p>
                    </div>
                    <div class="">
                        <p class="mb-0 dates">02 Jan 2025</p>
                    </div>
                </div>
                <h6 class="text-black fw-medium mt-3">Website Redesign</h6>

                <div class="d-flex align-items-center">
                    <div class="flex-grow-1 me-2">
                        <div class="custom-progress">
                            <div class="custom-progress-red" style="width: 10%;"></div>
                        </div>
                    </div>
                    <small class="fw-semibold">10%</small>
                </div>
                <div class="d-flex gap-3 mt-3">
                    <div class="pin_box">
                        <div class="d-flex align-items-center gap-2">
                            <img src="{{ asset('assets/assestsnew/paperclip.svg') }}" alt="">
                            <p class="mb-0 text-black fw-medium">2</p>
                        </div>
                    </div>
                    <div class="pin_box">
                        <div class="d-flex align-items-center gap-2">
                            <img src="{{ asset('assets/assestsnew/message.svg') }}" alt="">
                            <p class="mb-0 text-black fw-medium">2</p>
                        </div>
                    </div>
                </div>
                <hr>
                <div class="row">

                    <div class="col-6">
                        <div class="d-flex align-items-center gap-2">
                            <img src="{{ asset('assets/assestsnew/woman1.png') }}" alt="Member 1">
                            <img class="imgmove" src="{{ asset('assets/assestsnew/newman1.png') }}" alt="Member 3">
                        </div>
                    </div>

                    <div class="col-6 d-flex justify-content-end">
                        <a class="text-decoration-none"
                           href="{{ route('organization.task_management.taskWiseScreenShot') }}">
                            <div class="screenround">
                                <img src="{{ asset('assets/assestsnew/landscape.svg') }}" alt="">
                            </div>
                        </a>
                    </div>
                </div>

            </div>
        </div>

        <div class="col-md-3 ">
            <div class="task_box">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="tag_red">
                        <p class="mb-0">High</p>
                    </div>
                    <div class="">
                        <p class="mb-0 dates">02 Jan 2025</p>
                    </div>
                </div>
                <h6 class="text-black fw-medium mt-3">Website Redesign</h6>

                <div class="d-flex align-items-center">
                    <div class="flex-grow-1 me-2">
                        <div class="custom-progress">
                            <div class="custom-progress-golden" style="width: 40%;"></div>
                        </div>
                    </div>
                    <small class="fw-semibold">40%</small>
                </div>
                <div class="d-flex gap-3 mt-3">
                    <div class="pin_box">
                        <div class="d-flex align-items-center gap-2">
                            <img src="{{ asset('assets/assestsnew/paperclip.svg') }}" alt="">
                            <p class="mb-0 text-black fw-medium">2</p>
                        </div>
                    </div>
                    <div class="pin_box">
                        <div class="d-flex align-items-center gap-2">
                            <img src="{{ asset('assets/assestsnew/message.svg') }}" alt="">
                            <p class="mb-0 text-black fw-medium">2</p>
                        </div>
                    </div>
                </div>
                <hr>
                <div class="row">

                    <div class="col-6">
                        <div class="d-flex align-items-center gap-2">
                            <img src="{{ asset('assets/assestsnew/woman1.png') }}" alt="Member 1">
                            <img class="imgmove" src="{{ asset('assets/assestsnew/newman1.png') }}" alt="Member 3">
                        </div>
                    </div>

                    <div class="col-6 d-flex justify-content-end">
                        <a class="text-decoration-none"
                           href="{{ route('organization.task_management.taskWiseScreenShot') }}">
                            <div class="screenround">
                                <img src="{{ asset('assets/assestsnew/landscape.svg') }}" alt="">
                            </div>
                        </a>
                    </div>
                </div>

            </div>
        </div>

        <div class="col-md-3 ">
            <div class="task_box">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="tag_green">
                        <p class="mb-0">Low</p>
                    </div>
                    <div class="">
                        <p class="mb-0 dates">02 Jan 2025</p>
                    </div>
                </div>
                <h6 class="text-black fw-medium mt-3">Website Redesign</h6>

                <div class="d-flex align-items-center">
                    <div class="flex-grow-1 me-2">
                        <div class="custom-progress">
                            <div class="custom-progress-red" style="width: 10%;"></div>
                        </div>
                    </div>
                    <small class="fw-semibold">10%</small>
                </div>
                <div class="d-flex gap-3 mt-3">
                    <div class="pin_box">
                        <div class="d-flex align-items-center gap-2">
                            <img src="{{ asset('assets/assestsnew/paperclip.svg') }}" alt="">
                            <p class="mb-0 text-black fw-medium">2</p>
                        </div>
                    </div>
                    <div class="pin_box">
                        <div class="d-flex align-items-center gap-2">
                            <img src="{{ asset('assets/assestsnew/message.svg') }}" alt="">
                            <p class="mb-0 text-black fw-medium">2</p>
                        </div>
                    </div>
                </div>
                <hr>
                <div class="row">

                    <div class="col-6">
                        <div class="d-flex align-items-center gap-2">
                            <img src="{{ asset('assets/assestsnew/woman1.png') }}" alt="Member 1">
                            <img class="imgmove" src="{{ asset('assets/assestsnew/newman1.png') }}" alt="Member 3">
                        </div>
                    </div>

                    <div class="col-6 d-flex justify-content-end">
                        <a class="text-decoration-none"
                           href="{{ route('organization.task_management.taskWiseScreenShot') }}">
                            <div class="screenround">
                                <img src="{{ asset('assets/assestsnew/landscape.svg') }}" alt="">
                            </div>
                        </a>
                    </div>
                </div>

            </div>
        </div>


        <div class="col-md-3 ">
            <div class="task_box">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="tag_red">
                        <p class="mb-0">High</p>
                    </div>
                    <div class="">
                        <p class="mb-0 dates">02 Jan 2025</p>
                    </div>
                </div>
                <h6 class="text-black fw-medium mt-3">Website Redesign</h6>

                <div class="d-flex align-items-center">
                    <div class="flex-grow-1 me-2">
                        <div class="custom-progress">
                            <div class="custom-progress-golden" style="width: 40%;"></div>
                        </div>
                    </div>
                    <small class="fw-semibold">40%</small>
                </div>
                <div class="d-flex gap-3 mt-3">
                    <div class="pin_box">
                        <div class="d-flex align-items-center gap-2">
                            <img src="{{ asset('assets/assestsnew/paperclip.svg') }}" alt="">
                            <p class="mb-0 text-black fw-medium">2</p>
                        </div>
                    </div>
                    <div class="pin_box">
                        <div class="d-flex align-items-center gap-2">
                            <img src="{{ asset('assets/assestsnew/message.svg') }}" alt="">
                            <p class="mb-0 text-black fw-medium">2</p>
                        </div>
                    </div>
                </div>
                <hr>
                <div class="row">

                    <div class="col-6">
                        <div class="d-flex align-items-center gap-2">
                            <img src="{{ asset('assets/assestsnew/woman1.png') }}" alt="Member 1">
                            <img class="imgmove" src="{{ asset('assets/assestsnew/newman1.png') }}" alt="Member 3">
                        </div>
                    </div>

                    <div class="col-6 d-flex justify-content-end">
                        <a class="text-decoration-none"
                           href="{{ route('organization.task_management.taskWiseScreenShot') }}">
                            <div class="screenround">
                                <img src="{{ asset('assets/assestsnew/landscape.svg') }}" alt="">
                            </div>
                        </a>
                    </div>
                </div>

            </div>
        </div>

        <div class="col-md-3 ">
            <div class="task_box">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="tag_1">
                        <p class="mb-0">Medium</p>
                    </div>
                    <div class="">
                        <p class="mb-0 dates">02 Jan 2025</p>
                    </div>
                </div>
                <h6 class="text-black fw-medium mt-3">Website Redesign</h6>

                <div class="d-flex align-items-center">
                    <div class="flex-grow-1 me-2">
                        <div class="custom-progress">
                            <div class="custom-progress-bar" style="width: 20.74%;"></div>
                        </div>
                    </div>
                    <small class="fw-semibold">20.74%</small>
                </div>
                <div class="d-flex gap-3 mt-3">
                    <div class="pin_box">
                        <div class="d-flex align-items-center gap-2">
                            <img src="{{ asset('assets/assestsnew/paperclip.svg') }}" alt="">
                            <p class="mb-0 text-black fw-medium">2</p>
                        </div>
                    </div>
                    <div class="pin_box">
                        <div class="d-flex align-items-center gap-2">
                            <img src="{{ asset('assets/assestsnew/message.svg') }}" alt="">
                            <p class="mb-0 text-black fw-medium">2</p>
                        </div>
                    </div>
                </div>
                <hr>
                <div class="row">

                    <div class="col-6">
                        <div class="d-flex align-items-center gap-2">
                            <img src="{{ asset('assets/assestsnew/woman1.png') }}" alt="Member 1">
                            <img class="imgmove" src="{{ asset('assets/assestsnew/newman1.png') }}" alt="Member 3">
                        </div>
                    </div>

                    <div class="col-6 d-flex justify-content-end">
                        <a class="text-decoration-none"
                           href="{{ route('organization.task_management.taskWiseScreenShot') }}">
                            <div class="screenround">
                                <img src="{{ asset('assets/assestsnew/landscape.svg') }}" alt="">
                            </div>
                        </a>
                    </div>
                </div>

            </div>
        </div>

        <div class="col-md-3 ">
            <div class="task_box">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="tag_1">
                        <p class="mb-0">Medium</p>
                    </div>
                    <div class="">
                        <p class="mb-0 dates">02 Jan 2025</p>
                    </div>
                </div>
                <h6 class="text-black fw-medium mt-3">Website Redesign</h6>

                <div class="d-flex align-items-center">
                    <div class="flex-grow-1 me-2">
                        <div class="custom-progress">
                            <div class="custom-progress-bar" style="width: 20.74%;"></div>
                        </div>
                    </div>
                    <small class="fw-semibold">20.74%</small>
                </div>
                <div class="d-flex gap-3 mt-3">
                    <div class="pin_box">
                        <div class="d-flex align-items-center gap-2">
                            <img src="{{ asset('assets/assestsnew/paperclip.svg') }}" alt="">
                            <p class="mb-0 text-black fw-medium">2</p>
                        </div>
                    </div>
                    <div class="pin_box">
                        <div class="d-flex align-items-center gap-2">
                            <img src="{{ asset('assets/assestsnew/message.svg') }}" alt="">
                            <p class="mb-0 text-black fw-medium">2</p>
                        </div>
                    </div>
                </div>
                <hr>
                <div class="row">

                    <div class="col-6">
                        <div class="d-flex align-items-center gap-2">
                            <img src="{{ asset('assets/assestsnew/woman1.png') }}" alt="Member 1">
                            <img class="imgmove" src="{{ asset('assets/assestsnew/newman1.png') }}" alt="Member 3">
                        </div>
                    </div>

                    <div class="col-6 d-flex justify-content-end">
                        <a class="text-decoration-none"
                           href="{{ route('organization.task_management.taskWiseScreenShot') }}">
                            <div class="screenround">
                                <img src="{{ asset('assets/assestsnew/landscape.svg') }}" alt="">
                            </div>
                        </a>
                    </div>
                </div>

            </div>
        </div>
        <div class="col-md-3 ">
            <div class="task_box">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="tag_green">
                        <p class="mb-0">Low</p>
                    </div>
                    <div class="">
                        <p class="mb-0 dates">02 Jan 2025</p>
                    </div>
                </div>
                <h6 class="text-black fw-medium mt-3">Website Redesign</h6>

                <div class="d-flex align-items-center">
                    <div class="flex-grow-1 me-2">
                        <div class="custom-progress">
                            <div class="custom-progress-red" style="width: 10%;"></div>
                        </div>
                    </div>
                    <small class="fw-semibold">10%</small>
                </div>
                <div class="d-flex gap-3 mt-3">
                    <div class="pin_box">
                        <div class="d-flex align-items-center gap-2">
                            <img src="{{ asset('assets/assestsnew/paperclip.svg') }}" alt="">
                            <p class="mb-0 text-black fw-medium">2</p>
                        </div>
                    </div>
                    <div class="pin_box">
                        <div class="d-flex align-items-center gap-2">
                            <img src="{{ asset('assets/assestsnew/message.svg') }}" alt="">
                            <p class="mb-0 text-black fw-medium">2</p>
                        </div>
                    </div>
                </div>
                <hr>
                <div class="row">

                    <div class="col-6">
                        <div class="d-flex align-items-center gap-2">
                            <img src="{{ asset('assets/assestsnew/woman1.png') }}" alt="Member 1">
                            <img class="imgmove" src="{{ asset('assets/assestsnew/newman1.png') }}" alt="Member 3">
                        </div>
                    </div>

                    <div class="col-6 d-flex justify-content-end">
                        <a class="text-decoration-none"
                           href="{{ route('organization.task_management.taskWiseScreenShot') }}">
                            <div class="screenround">
                                <img src="{{ asset('assets/assestsnew/landscape.svg') }}" alt="">
                            </div>
                        </a>
                    </div>
                </div>

            </div>
        </div>

        <div class="col-md-3 ">
            <div class="task_box">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="tag_1">
                        <p class="mb-0">Medium</p>
                    </div>
                    <div class="">
                        <p class="mb-0 dates">02 Jan 2025</p>
                    </div>
                </div>
                <h6 class="text-black fw-medium mt-3">Website Redesign</h6>

                <div class="d-flex align-items-center">
                    <div class="flex-grow-1 me-2">
                        <div class="custom-progress">
                            <div class="custom-progress-bar" style="width: 20.74%;"></div>
                        </div>
                    </div>
                    <small class="fw-semibold">20.74%</small>
                </div>
                <div class="d-flex gap-3 mt-3">
                    <div class="pin_box">
                        <div class="d-flex align-items-center gap-2">
                            <img src="{{ asset('assets/assestsnew/paperclip.svg') }}" alt="">
                            <p class="mb-0 text-black fw-medium">2</p>
                        </div>
                    </div>
                    <div class="pin_box">
                        <div class="d-flex align-items-center gap-2">
                            <img src="{{ asset('assets/assestsnew/message.svg') }}" alt="">
                            <p class="mb-0 text-black fw-medium">2</p>
                        </div>
                    </div>
                </div>
                <hr>
                <div class="row">

                    <div class="col-6">
                        <div class="d-flex align-items-center gap-2">
                            <img src="{{ asset('assets/assestsnew/woman1.png') }}" alt="Member 1">
                            <img class="imgmove" src="{{ asset('assets/assestsnew/newman1.png') }}" alt="Member 3">
                        </div>
                    </div>

                    <div class="col-6 d-flex justify-content-end">
                        <a class="text-decoration-none"
                           href="{{ route('organization.task_management.taskWiseScreenShot') }}">
                            <div class="screenround">
                                <img src="{{ asset('assets/assestsnew/landscape.svg') }}" alt="">
                            </div>
                        </a>
                    </div>
                </div>

            </div>
        </div>

        <div class="col-md-3 ">
            <div class="task_box">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="tag_1">
                        <p class="mb-0">Medium</p>
                    </div>
                    <div class="">
                        <p class="mb-0 dates">02 Jan 2025</p>
                    </div>
                </div>
                <h6 class="text-black fw-medium mt-3">Website Redesign</h6>

                <div class="d-flex align-items-center">
                    <div class="flex-grow-1 me-2">
                        <div class="custom-progress">
                            <div class="custom-progress-bar" style="width: 20.74%;"></div>
                        </div>
                    </div>
                    <small class="fw-semibold">20.74%</small>
                </div>
                <div class="d-flex gap-3 mt-3">
                    <div class="pin_box">
                        <div class="d-flex align-items-center gap-2">
                            <img src="{{ asset('assets/assestsnew/paperclip.svg') }}" alt="">
                            <p class="mb-0 text-black fw-medium">2</p>
                        </div>
                    </div>
                    <div class="pin_box">
                        <div class="d-flex align-items-center gap-2">
                            <img src="{{ asset('assets/assestsnew/message.svg') }}" alt="">
                            <p class="mb-0 text-black fw-medium">2</p>
                        </div>
                    </div>
                </div>
                <hr>
                <div class="row">

                    <div class="col-6">
                        <div class="d-flex align-items-center gap-2">
                            <img src="{{ asset('assets/assestsnew/woman1.png') }}" alt="Member 1">
                            <img class="imgmove" src="{{ asset('assets/assestsnew/newman1.png') }}" alt="Member 3">
                        </div>
                    </div>

                    <div class="col-6 d-flex justify-content-end">
                        <a class="text-decoration-none"
                           href="{{ route('organization.task_management.taskWiseScreenShot') }}">
                            <div class="screenround">
                                <img src="{{ asset('assets/assestsnew/landscape.svg') }}" alt="">
                            </div>
                        </a>
                    </div>
                </div>

            </div>
        </div>

        <div class="col-md-3 ">
            <div class="task_box">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="tag_1">
                        <p class="mb-0">Medium</p>
                    </div>
                    <div class="">
                        <p class="mb-0 dates">02 Jan 2025</p>
                    </div>
                </div>
                <h6 class="text-black fw-medium mt-3">Website Redesign</h6>

                <div class="d-flex align-items-center">
                    <div class="flex-grow-1 me-2">
                        <div class="custom-progress">
                            <div class="custom-progress-bar" style="width: 20.74%;"></div>
                        </div>
                    </div>
                    <small class="fw-semibold">20.74%</small>
                </div>
                <div class="d-flex gap-3 mt-3">
                    <div class="pin_box">
                        <div class="d-flex align-items-center gap-2">
                            <img src="{{ asset('assets/assestsnew/paperclip.svg') }}" alt="">
                            <p class="mb-0 text-black fw-medium">2</p>
                        </div>
                    </div>
                    <div class="pin_box">
                        <div class="d-flex align-items-center gap-2">
                            <img src="{{ asset('assets/assestsnew/message.svg') }}" alt="">
                            <p class="mb-0 text-black fw-medium">2</p>
                        </div>
                    </div>
                </div>
                <hr>
                <div class="row">

                    <div class="col-6">
                        <div class="d-flex align-items-center gap-2">
                            <img src="{{ asset('assets/assestsnew/woman1.png') }}" alt="Member 1">
                            <img class="imgmove" src="{{ asset('assets/assestsnew/newman1.png') }}" alt="Member 3">
                        </div>
                    </div>

                    <div class="col-6 d-flex justify-content-end">
                        <a class="text-decoration-none"
                           href="{{ route('organization.task_management.taskWiseScreenShot') }}">
                            <div class="screenround">
                                <img src="{{ asset('assets/assestsnew/landscape.svg') }}" alt="">
                            </div>
                        </a>
                    </div>
                </div>

            </div>
        </div>
    </div>



    <!-- List view -->
    <div class="list_box d-none">
        <div class="attendance-table-outer">
            <table class="attendance-table ">
                <thead>
                <tr>
                    <th>NAME</th>
                    <th>STAGE</th>
                    <th>PRIORITY</th>
                    <th>END DATE</th>
                    <th>ASSIGNED TO</th>
                    <th>COMPLETION</th>
                    <th></th>
                    <th></th>
                </tr>
                </thead>
                <tbody>
                <tr class="tex_fix">
                    <td class="fw-medium fs-5" style="color: #316FF6;">
                        <a class="project-link text-decoration-none"
                           href="{{ route('organization.task_management.newsLetter') }}">ABC Project 01</a>
                    </td>
                    <td>In Progress</td>
                    <td>
                        <div class="tag_1">
                            <p class="mb-0">Medium</p>
                        </div>
                    </td>
                    <td style="color: #FF1010;">25 Jan 2025</td>
                    <td>
                        <div class="members-avatars">
                            <div class="member-avatar">
                                <img src="{{ asset('assets/assestsnew/woman1.png') }}" alt="Member 1">
                            </div>
                            <div class="member-avatar imgmove">
                                <img src="{{ asset('assets/assestsnew/woman2.png') }}" alt="Member 2">
                            </div>
                            <div class="member-avatar imgmove">
                                <img src="{{ asset('assets/assestsnew/newman1.png') }}" alt="Member 3">
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
                    <td class="d-flex gap-4">
                        <a class="text-decoration-none"
                           href="{{ route('organization.task_management.taskWiseScreenShot') }}">
                            <div class="screenround">
                                <img src="{{ asset('assets/assestsnew/landscape.svg') }}" alt="">
                            </div>
                        </a>
                        <div class="d-flex align-items-center gap-2">
                            <img src="{{ asset('assets/assestsnew/paperclip.svg') }}" alt="">
                            <p class="mb-0 text-black fw-medium">2</p>
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <img src="{{ asset('assets/assestsnew/message.svg') }}" alt="">
                            <p class="mb-0 text-black fw-medium">2</p>
                        </div>
                    </td>
                </tr>

                <tr class="">
                    <td class="fw-medium fs-5" style="color: #316FF6;">
                        <a class="project-link text-decoration-none"
                           href="{{ route('organization.task_management.newsLetter') }}">ABC Project 01</a>
                    </td>
                    <td>In Progress</td>
                    <td>
                        <div class="tag_red">
                            <p class="mb-0">High</p>
                        </div>
                    </td>
                    <td style="color: #FF1010;">25 Jan 2025</td>
                    <td>
                        <div class="members-avatars">
                            <div class="member-avatar">
                                <img src="{{ asset('assets/assestsnew/woman1.png') }}" alt="Member 1">
                            </div>
                            <div class="member-avatar imgmove">
                                <img src="{{ asset('assets/assestsnew/woman2.png') }}" alt="Member 2">
                            </div>
                            <div class="member-avatar imgmove">
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
                    <td class="d-flex gap-4">
                        <a class="text-decoration-none"
                           href="{{ route('organization.task_management.taskWiseScreenShot') }}">
                            <div class="screenround">
                                <img src="{{ asset('assets/assestsnew/landscape.svg') }}" alt="">
                            </div>
                        </a>
                        <div class="d-flex align-items-center gap-2">
                            <img src="{{ asset('assets/assestsnew/paperclip.svg') }}" alt="">
                            <p class="mb-0 text-black fw-medium">2</p>
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <img src="{{ asset('assets/assestsnew/message.svg') }}" alt="">
                            <p class="mb-0 text-black fw-medium">2</p>
                        </div>
                    </td>
                </tr>

                <tr class="tex_fix">
                    <td class="fw-medium fs-5" style="color: #316FF6;">
                        <a class="project-link text-decoration-none"
                           href="{{ route('organization.task_management.newsLetter') }}">ABC Project 01</a>
                    </td>
                    <td>To Do</td>
                    <td>
                        <div class="tag_1">
                            <p class="mb-0">Medium</p>
                        </div>
                    </td>
                    <td style="color: #FF1010;">25 Jan 2025</td>
                    <td>
                        <div class="members-avatars">
                            <div class="member-avatar">
                                <img src="{{ asset('assets/assestsnew/woman1.png') }}" alt="Member 1">
                            </div>
                            <div class="member-avatar imgmove">
                                <img src="{{ asset('assets/assestsnew/woman2.png') }}" alt="Member 2">
                            </div>
                            <div class="member-avatar imgmove">
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
                    <td class="d-flex gap-4">
                        <a class="text-decoration-none"
                           href="{{ route('organization.task_management.taskWiseScreenShot') }}">
                            <div class="screenround">
                                <img src="{{ asset('assets/assestsnew/landscape.svg') }}" alt="">
                            </div>
                        </a>
                        <div class="d-flex align-items-center gap-2">
                            <img src="{{ asset('assets/assestsnew/paperclip.svg') }}" alt="">
                            <p class="mb-0 text-black fw-medium">2</p>
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <img src="{{ asset('assets/assestsnew/message.svg') }}" alt="">
                            <p class="mb-0 text-black fw-medium">2</p>
                        </div>
                    </td>
                </tr>

                <tr class="">
                    <td class="fw-medium fs-5" style="color: #316FF6;">
                        <a class="project-link text-decoration-none"
                           href="{{ route('organization.task_management.newsLetter') }}">ABC Project 01</a>
                    </td>
                    <td>Closed</td>
                    <td>
                        <div class="tag_green">
                            <p class="mb-0">Low</p>
                        </div>
                    </td>
                    <td style="color: #FF1010;">25 Jan 2025</td>
                    <td>
                        <div class="members-avatars">
                            <div class="member-avatar">
                                <img src="{{ asset('assets/assestsnew/woman1.png') }}" alt="Member 1">
                            </div>
                            <div class="member-avatar imgmove">
                                <img src="{{ asset('assets/assestsnew/woman2.png') }}" alt="Member 2">
                            </div>
                            <div class="member-avatar imgmove">
                                <img src="{{ asset('assets/assestsnew/newman1.png') }}" alt="Member 3">
                            </div>
                        </div>
                    </td>
                    <td>
                        <div class="d-flex align-items-center">
                            <div class="flex-grow-1 me-2">
                                <small class="fw-semibold">0%</small>
                                <div class="progress" style="height: 8px;">
                                    <div class="progress-bar" style="width: 0%; background-color: #FF1010;">
                                    </div>
                                </div>
                            </div>

                        </div>
                    </td>
                    <td class="d-flex gap-4">
                        <a class="text-decoration-none"
                           href="{{ route('organization.task_management.taskWiseScreenShot') }}">
                            <div class="screenround">
                                <img src="{{ asset('assets/assestsnew/landscape.svg') }}" alt="">
                            </div>
                        </a>
                        <div class="d-flex align-items-center gap-2">
                            <img src="{{ asset('assets/assestsnew/paperclip.svg') }}" alt="">
                            <p class="mb-0 text-black fw-medium">2</p>
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <img src="{{ asset('assets/assestsnew/message.svg') }}" alt="">
                            <p class="mb-0 text-black fw-medium">2</p>
                        </div>
                    </td>
                </tr>
                <tr class="">
                    <td class="fw-medium fs-5" style="color: #316FF6;">
                        <a class="project-link text-decoration-none"
                           href="{{ route('organization.task_management.newsLetter') }}">ABC Project 01</a>
                    </td>
                    <td>Done</td>
                    <td>
                        <div class="tag_1">
                            <p class="mb-0">Medium</p>
                        </div>
                    </td>
                    <td style="color: #FF1010;">25 Jan 2025</td>
                    <td>
                        <div class="members-avatars">
                            <div class="member-avatar">
                                <img src="{{ asset('assets/assestsnew/woman1.png') }}" alt="Member 1">
                            </div>
                            <div class="member-avatar imgmove">
                                <img src="{{ asset('assets/assestsnew/woman2.png') }}" alt="Member 2">
                            </div>
                            <div class="member-avatar imgmove">
                                <img src="{{ asset('assets/assestsnew/newman1.png') }}" alt="Member 3">
                            </div>
                            <div class="member-avatar imgmove">
                                <img src="{{ asset('assets/assestsnew/newman1.png') }}" alt="Member 3">
                            </div>
                            <div class="member-avatar imgmove">
                                <img src="{{ asset('assets/assestsnew/newman1.png') }}" alt="Member 3">
                            </div>
                            <div class="member-avatar imgmove">
                                <img src="{{ asset('assets/assestsnew/newman1.png') }}" alt="Member 3">
                            </div>
                            <div class="member-avatar imgmove">
                                <img src="{{ asset('assets/assestsnew/newman1.png') }}" alt="Member 3">
                            </div>
                            <div class="member-avatar imgmove">
                                <img src="{{ asset('assets/assestsnew/newman1.png') }}" alt="Member 3">
                            </div>
                            <div class="member-avatar imgmove">
                                <img src="{{ asset('assets/assestsnew/newman1.png') }}" alt="Member 3">
                            </div>
                            <div class="member-avatar imgmove">
                                <img src="{{ asset('assets/assestsnew/newman1.png') }}" alt="Member 3">
                            </div>
                            <div class="member-avatar imgmove">
                                <img src="{{ asset('assets/assestsnew/newman1.png') }}" alt="Member 3">
                            </div>
                        </div>
                    </td>
                    <td>
                        <div class="d-flex align-items-center">
                            <div class="flex-grow-1 me-2">
                                <small class="fw-semibold">0%</small>
                                <div class="progress" style="height: 8px;">
                                    <div class="progress-bar" style="width: 0%; background-color: #FF1010;">
                                    </div>
                                </div>
                            </div>

                        </div>
                    </td>
                    <td class="d-flex gap-4">
                        <a class="text-decoration-none"
                           href="{{ route('organization.task_management.taskWiseScreenShot') }}">
                            <div class="screenround">
                                <img src="{{ asset('assets/assestsnew/landscape.svg') }}" alt="">
                            </div>
                        </a>
                        <div class="d-flex align-items-center gap-2">
                            <img src="{{ asset('assets/assestsnew/paperclip.svg') }}" alt="">
                            <p class="mb-0 text-black fw-medium">2</p>
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <img src="{{ asset('assets/assestsnew/message.svg') }}" alt="">
                            <p class="mb-0 text-black fw-medium">2</p>
                        </div>
                    </td>
                </tr>


                </tbody>
            </table>
        </div>
    </div>

@endsection
