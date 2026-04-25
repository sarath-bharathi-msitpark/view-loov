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
    <div class="col-12">
        <div class="row mt-5">
            <div class="col-lg-6 selecters_head">
                <h2 class="mb-0" id="sectionHeading">Manage Project Task Stages</h2>
            </div>

            <div class="col-lg-6 selecters_head">
                <div class="row justify-content-lg-end gx-4">
                    <div class="col-auto d-none" id="plusBtnWrapper">
                        <button class="download_arrbtn">
                            <i class="fa-solid fa-plus"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <!-- Edit Template -->
    <div class="task-container">
        <div class="task-card">
            <div class="task-stage-item">
                <div class="task-stage-left">
                    <div class="task-stage-icon">
                        <img src="{{ asset('assets/assestsnew/gridmenublue.svg') }}" alt="">
                    </div>
                    <div class="task-stage-title">To Do</div>
                </div>
                <div class="task-stage-actions">
                    <button class="task-action-btn task-edit-btn">
                        <img src="{{ asset('assets/assestsnew/edit.svg') }}" alt="">
                    </button>
                    <button class="task-action-btn task-delete-btn">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </div>


            <div class="task-stage-item">
                <div class="task-stage-left">
                    <div class="task-stage-icon">
                        <img src="{{ asset('assets/assestsnew/gridmenublue.svg') }}" alt="">
                    </div>
                    <div class="task-stage-title">In Progress</div>
                </div>
                <div class="task-stage-actions">
                    <button class="task-action-btn task-edit-btn">
                        <img src="{{ asset('assets/assestsnew/edit.svg') }}" alt="">
                    </button>
                    <button class="task-action-btn task-delete-btn">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </div>


            <div class="task-stage-item">
                <div class="task-stage-left">
                    <div class="task-stage-icon">
                        <img src="{{ asset('assets/assestsnew/gridmenublue.svg') }}" alt="">
                    </div>
                    <div class="task-stage-title">Review</div>
                </div>
                <div class="task-stage-actions">
                    <button class="task-action-btn task-edit-btn">
                        <img src="{{ asset('assets/assestsnew/edit.svg') }}" alt="">
                    </button>
                    <button class="task-action-btn task-delete-btn">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </div>


            <div class="task-stage-item">
                <div class="task-stage-left">
                    <div class="task-stage-icon">
                        <img src="{{ asset('assets/assestsnew/gridmenublue.svg') }}" alt="">
                    </div>
                    <div class="task-stage-title">Done</div>
                </div>
                <div class="task-stage-actions">
                    <button class="task-action-btn task-edit-btn">
                        <img src="{{ asset('assets/assestsnew/edit.svg') }}" alt="">
                    </button>
                    <button class="task-action-btn task-delete-btn">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </div>


            <div class="task-stage-item">
                <div class="task-stage-left">
                    <div class="task-stage-icon">
                        <img src="{{ asset('assets/assestsnew/gridmenublue.svg') }}" alt="">
                    </div>
                    <div class="task-stage-title">Trash</div>
                </div>
                <div class="task-stage-actions">
                    <button class="task-action-btn task-edit-btn">
                        <img src="{{ asset('assets/assestsnew/edit.svg') }}" alt="">
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
@endsection
