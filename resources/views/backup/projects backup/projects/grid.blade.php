
@if(isset($projects) && !empty($projects) && count($projects) > 0)
    <div class="col-12">
        <div class="row">

             <div class="col-12">
                <div class="project_slider_section position-relative">
    <div class="owl-carousel project_full_box owl-theme">
        <!-- Card 1 -->
        <div class="item">
            <div class="card border-0 rounded-3 overflow-hidden bg-white mt-0">
                <div class="card-header border-0 d-flex flex-column">
                    <div class="d-flex align-items-center">
                        <img src="{{ asset('assets/assestsnew/project_status5.svg') }}" class="img-fluid me-2" alt="">
                        <h4 class="mb-0 text-truncate">Total Project</h4>
                    </div>
                    <div class="d-flex align-items-center mt-3">
                        <h2>50</h2>
                    </div>
                </div>
            </div>
        </div>

        <div class="item">
            <div class="card border-0 rounded-3 overflow-hidden bg-white mt-0">
                <div class="card-header border-0 d-flex flex-column">
                    <div class="d-flex align-items-center">
                        <img src="{{ asset('assets/assestsnew/project_status4.svg') }}" class="img-fluid me-2" alt="">
                        <h4 class="mb-0 text-truncate">In Progress</h4>
                    </div>
                    <div class="d-flex align-items-center mt-3">
                        <h2>50</h2>
                    </div>
                </div>
            </div>
        </div>

        <div class="item">
            <div class="card border-0 rounded-3 overflow-hidden bg-white mt-0">
                <div class="card-header border-0 d-flex flex-column">
                    <div class="d-flex align-items-center">
                        <img src="{{ asset('assets/assestsnew/project_status3.svg') }}" class="img-fluid me-2" alt="">
                        <h4 class="mb-0 text-truncate">On Hold</h4>
                    </div>
                    <div class="d-flex align-items-center mt-3">
                        <h2>50</h2>
                    </div>
                </div>
            </div>
        </div>

        <div class="item">
            <div class="card border-0 rounded-3 overflow-hidden bg-white mt-0">
                <div class="card-header border-0 d-flex flex-column">
                    <div class="d-flex align-items-center">
                        <img src="{{ asset('assets/assestsnew/project_status2.svg') }}" class="img-fluid me-2" alt="">
                        <h4 class="mb-0 text-truncate">Completed</h4>
                    </div>
                    <div class="d-flex align-items-center mt-3">
                        <h2>50</h2>
                    </div>
                </div>
            </div>
        </div>

        <div class="item">
            <div class="card border-0 rounded-3 overflow-hidden bg-white mt-0">
                <div class="card-header border-0 d-flex flex-column">
                    <div class="d-flex align-items-center">
                        <img src="{{ asset('assets/assestsnew/project_status6.svg') }}" class="img-fluid me-2" alt="">
                        <h4 class="mb-0 text-truncate">Cancelled</h4>
                    </div>
                    <div class="d-flex align-items-center mt-3">
                        <h2>50</h2>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Left/Right Navigation -->
    <button class="project_nav_btn prev_btn">
        <i class="fa-solid fa-chevron-left"></i>
    </button>
    <button class="project_nav_btn next_btn">
        <i class="fa-solid fa-chevron-right"></i>
    </button>
</div>
            </div>
            
            @foreach ($projects as $key => $project)
                <div class="col-xxl-3 col-md-4 col-sm-6 col-12 d-flex">
                    <div class="card w-100 border-0 rounded-3 overflow-hidden bg-white mt-0">
                        <div class="card-header d-flex align-items-center justify-content-between pb-3 pt-2">
                            <div class="d-flex align-items-center flex-1">
                                {{--                                <img {{ $project->img_image }}--}}
                                {{--                                     class="img-fluid rounded-1 border border-2 border-primary me-2" alt=""--}}
                                {{--                                     width="40" height="40">--}}

                                <img src="{{ asset('assets/assestsnew/Manage_projects.svg') }}"
                                     class="img-fluid rounded-1 border border-2 p-2 border-primary me-2" alt=""
                                     width="36" height="36">
                                <h5 class="mb-0 main_truckspans text-truncate">
                                    <span class="text-dark">#12345</span>
                                    <a class="text-decoration-none text-truncate"
                                       href="{{ route('organization.projects.show', $project) }}">{{ $project->project_name }}</a>
                                </h5>
                            </div>
                            <div class="dropdown">
                                @can('project_management')
                                    <button class="btn p-0 border-0 text-muted" data-bs-toggle="dropdown"
                                            aria-expanded="false">
                                        <i class="ti ti-dots-vertical"></i>
                                    </button>
                                    <ul class="dropdown-menu icon-dropdown dropdown-menu-end">

                                        {{--                                    <li><a class="dropdown-item" data-ajax-popup="true" data-size="md"--}}
                                        {{--                                           data-title="{{ __('Duplicate Project') }}"--}}
                                        {{--                                           data-url="{{ route('organization.project.copy', [$project->id]) }}">--}}
                                        {{--                                            <i class="ti ti-copy"></i> {{ __('Duplicate') }}</a></li>--}}

                                        <li><a class="dropdown-item" href="#!" data-size="lg"
                                               data-url="{{ route('organization.projects.edit', $project->id) }}"
                                               data-ajax-popup="true">
                                                <i class="ti ti-pencil bg-primary text-white"></i> {{ __('Edit') }}</a></li>

                                        <li>
                                            {!! Form::open(['method' => 'DELETE', 'route' => ['organization.projects.destroy', $project->id]]) !!}
                                            <a href="#!" class="dropdown-item text-danger bs-pass-para">
                                                <i class="ti ti-trash bg-primary text-white"></i> {{ __('Delete') }}</a>
                                            {!! Form::close() !!}
                                        </li>
                                    </ul>
                                @endcan
                            </div>
                        </div>
                        <div class="card-body p-3 bg-light">
                        <span
                            class="badge badge_setmainproject bg-light-{{ \App\Models\Project::$status_color[$project->status] }} py-1 px-2 text-uppercase mb-3">{{ __(\App\Models\Project::$project_status[$project->status]) }}</span>
                            <!--@if(!empty($project->description))-->
                            <!--    <p style="min-height:35px" class="text-muted text-sm">{{ \Illuminate\Support\Str::words($project->description, 10, '...') }}</p>-->
                            <!--@else-->
                            <!--    <p style="min-height:35px" class="text-muted text-sm">No description</p>-->
                            <!--@endif-->
                            <div class="d-flex">
                                <span class="fw-bold text-muted w-100">{{ __('Members') }}</span>
                            </div>
                            <div class="d-flex mt-2">
                                @foreach ($project->users->take(3) as $user)
                                    {{--                                    <img--}}
                                    {{--                                        src="{{ $user->avatar ? asset('/storage/uploads/avatar/' . $user->avatar) : asset('/storage/uploads/avatar/avatar.png') }}"--}}
                                    {{--                                        class="rounded-circle border shadow-sm me-1" width="30" height="30"--}}
                                    {{--                                        title="{{ $user->name }}">--}}

                                    @php
                                        $gender = $user->employee->gender ?? null;
                                    @endphp

                                    @if($gender === GENDER_MALE)
                                        <img src="{{ asset('assets/assestsnew/menimg.png') }}"
                                             class="rounded-circle border shadow-sm me-1" width="30" height="30"
                                             title="{{ $user->name }}" alt="Male">
                                    @elseif($gender === GENDER_FEMALE)
                                        <img src="{{ asset('assets/assestsnew/femaile-report.svg') }}"
                                             class="rounded-circle border shadow-sm me-1" width="30" height="30"
                                             title="{{ $user->name }}" alt="Female">
                                    @else
                                        <img
                                            src="{{ $user->avatar ? asset('/storage/uploads/avatar/' . $user->avatar) : asset('assets/assestsnew/profile.png') }}"
                                            class="rounded-circle border shadow-sm me-1" width="30" height="30"
                                            title="{{ $user->name }}" alt="Default">
                                    @endif
                                @endforeach
                            </div>
                            <div class="d-flex mt-3 align-items-center">
                                <div class="progress px-0 w-100">
                                    <div
                                        class="progress-bar myProgressBar"
                                        role="progressbar"
                                        style="width:  30%";>
                                    </div>
                                </div>
                                <small class="ps-3 fw-bold">68%</small>
                            </div>
                        </div>
                        <div class="card-footer border-0 pt-3 d-flex justify-content-between">
                            <div>
                                <h6 class="{{ strtotime($project->on_board_date) < time() ? 'text-danger' : '' }}">
                                    {{ Utility::getDateFormated($project->on_board_date) }}</h6>
                                <span class="text-muted mb-0">{{ __('Onboard Date') }}</span>
                            </div>
                            <div class="text-end">
                                <h6>{{ Utility::getDateFormated($project->renewal_date) }}</h6>
                                <span class="text-muted mb-0">{{ __('Renewal Date') }}</span>
                            </div>
                        </div>
                        <div class="card-footer border-0 pt-3 d-flex justify-content-between">
                            <div>
                                <h6 class="{{ strtotime($project->support_start_date) < time() ? 'text-danger' : '' }}">
                                    {{ Utility::getDateFormated($project->support_start_date) }}</h6>
                                <span class="text-muted mb-0">{{ __('Support Start Date') }}</span>
                            </div>
                            <div class="text-end">
                                <h6>{{ Utility::getDateFormated($project->support_end_date) }}</h6>
                                <span class="text-muted mb-0">{{ __('Support End Date') }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

@else
    <div class="col-xl-12 col-lg-12 col-sm-12">
        <div class="card">
            <div class="card-body">
                <h6 class="text-center mb-0">{{__('No Projects Found.')}}</h6>
            </div>
        </div>
    </div>
@endif




<script>
  $(document).ready(function(){
    var owl = $('.project_full_box');
    owl.owlCarousel({
      loop: true,
      margin: 15,
      dots: false,
      nav: false,
      autoplay: false,
      responsive:{
        0:{ items:1 },
        576:{ items:2 },
        768:{ items:3 },
        992:{ items:4 },
        1200:{ items:5 }
      }
    });

    // Custom Nav Buttons
    $('.next_btn').click(function() {
      owl.trigger('next.owl.carousel');
    });
    $('.prev_btn').click(function() {
      owl.trigger('prev.owl.carousel');
    });
  });
</script>
