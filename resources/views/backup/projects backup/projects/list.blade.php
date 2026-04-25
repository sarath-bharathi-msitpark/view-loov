           
           
           
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


<div class="col-xl-12 mt-3">
    <div class="card">
        <div class="card-body table-border-style">
            <div class="table-responsive">
                <table class="attendance-table table">
                    <thead>
                    <tr>
                        <th>{{__('Project')}}</th>
                        <th>{{__('Status')}}</th>
                        <th>{{__('Users')}}</th>
                        <th>{{__('Completion')}}</th>
                        <th class="text-end">{{__('Action')}}</th>
                    </tr>
                    </thead>
                    <tbody>
                    @if(isset($projects) && !empty($projects) && count($projects) > 0)
                        @foreach ($projects as $key => $project)
                            <tr>
                                <td class="tex_fix">
                                    <div class="d-flex align-items-center">
                                        <img
                                            {{ $project->img_image }} class="wid-40 rounded border-2 border border-primary me-3">
                                        <p class="mb-0"><a href="{{ route('organization.projects.show',$project) }}"
                                                           class="name mb-0 h6 text-sm">{{ $project->project_name }}</a>
                                        </p>
                                    </div>
                                </td>
                                <td class="">
                                    <span
                                        class="status_badge badge bg-{{\App\Models\Project::$status_color[$project->status]}} p-2 px-3 rounded">{{ __(\App\Models\Project::$project_status[$project->status]) }}</span>
                                </td>
                                <td class="">
                                    <div class="avatar-group" id="project_{{ $project->id }}">
                                        @if(isset($project->users) && !empty($project->users) && count($project->users) > 0)
                                            @foreach($project->users as $key => $user)
                                                {{-- @if($key < 3) --}}
                                                <a href="#" class="avatar rounded-circle">
                                                    <img
                                                        @if($user->avatar) src="{{asset('/storage/uploads/avatar/'.$user->avatar)}}"
                                                        @else src="{{asset('/storage/uploads/avatar/avatar.png')}}"
                                                        @endif title="{{ $user->name }}"
                                                        style="height:36px;width:36px;">
                                                </a>
                                                {{-- @else
                                                    @break
                                                @endif --}}
                                            @endforeach
                                            {{-- @if(count($project->users) > 3)
                                                <a href="#" class="avatar rounded-circle avatar-sm">
                                                    <img avatar="+ {{ count($project->users)-3 }}" style="height:36px;width:36px;">
                                                </a>
                                            @endif --}}
                                        @else
                                            {{ __('-') }}
                                        @endif
                                    </div>
                                </td>
                                <td class="text-end">
                                    <h5 class="mb-0 text-success">{{ $project->project_progress($project , $last_task->id)['percentage'] }}</h5>
                                    <div class="progress mb-0">
                                        <div
                                            class="progress-bar bg-{{ $project->project_progress($project , $last_task->id)['color'] }}"
                                            style="width: {{ $project->project_progress($project , $last_task->id)['percentage'] }};"></div>
                                    </div>
                                </td>
                                <td class="text-end">
                                    <span class="d-flex justify-content-end">
                                            <div class="action-btn me-2">
                                                <a href="#" class="copy_com align-items-center"
                                                   data-url="{{ route('organization.invite.project.member.view', $project->id) }}"
                                                   data-ajax-popup="true" data-size="lg" data-bs-toggle="tooltip"
                                                   title="{{__('Invite User')}}" data-title="{{__('Invite User')}}">
                                                    <i class="ti ti-send text-primary"></i>
                                                </a>
                                            </div>

                                            <div class="action-btn me-2">
                                                    <a href="#" class="copy_com align-items-center"
                                                       data-url="{{ URL::to('projects/'.$project->id.'/edit') }}"
                                                       data-ajax-popup="true" data-size="lg" data-bs-toggle="tooltip"
                                                       title="{{__('Edit')}}" data-title="{{__('Edit Project')}}">
                                                        <i class="fas fa-edit text-primary fs-6" title="Edit"></i>
                                                    </a>
                                                </div>

                                            <div class="action-btn ">
                                                    {!! Form::open(['method' => 'DELETE', 'route' => ['organization.projects.user.destroy', [$project->id,$user->id]]]) !!}
                                                    <a href="#"
                                                       class="align-items-center bs-pass-para copy_com"
                                                       data-bs-toggle="tooltip" title="{{__('Delete')}}"><i
                                                            class="fa-solid fa-trash fs-5 text-danger"
                                                            title="Delete"></i></a>
                                                    {!! Form::close() !!}
                                                </div>
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <th scope="col" colspan="7"><h6 class="text-center">{{__('No Projects Found.')}}</h6></th>
                        </tr>
                    @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <!-- Pagination Links -->
    <div class="row my-3 justify-content-center">
        <div class="col-12 optional_inputpagi">
            <div class="row align-items-center justify-content-center">

                <!-- Left Spacer -->
                <div class="col-12 col-md-3 mb-3 mb-md-0">
                    <div class="data_table_select">
                        <small>items per page</small>
                        <select>
                            <option>5</option>
                            <option>10</option>
                            <option>20</option>
                            <option>50</option>
                        </select>
                    </div>
                </div>

                <!-- Pagination Numbers -->
                <div class="col-12 col-md-auto mb-3 mb-md-0">
                    <div class="d-flex justify-content-center">
                        <ul class="paginatio_ulist d-flex align-items-center gap-lg-4 gap-2 m-0 p-0">
                            {{-- First Page --}}
                            <li class="disabled">
                                <a href="#" class="page-link1">&#171;</a>
                            </li>

                            {{-- Previous Page --}}
                            <li class="disabled">
                                <a href="#" class="page-link1">
                                    <i class="fa-solid fa-chevron-left"></i>
                                </a>
                            </li>

                            {{-- Page Numbers --}}

                            <li class="active_pagination">
                                <a href="#"
                                   class="page-link1">1</a>
                            </li>


                            {{-- Next Page --}}
                            <li class="disabled">
                                <a href="#" class="page-link1">
                                    <i class="fa-solid fa-chevron-right"></i>
                                </a>
                            </li>

                            {{-- Last Page --}}
                            <li class="disabled">
                                <a href="#"
                                   class="page-link1">&#187;</a>
                            </li>
                        </ul>
                    </div>
                </div>

                <!-- Page Jump Input -->
                <div class="col-12 col-lg-3 col-md-4 mb-3 mb-md-0">
                    <div
                        class="d-flex flex-md-row align-items-center justify-content-center justify-content-md-start gap-2">
                        <form
                            class="d-flex align-items-center gap-2"
                            style="flex-direction:row !important;">
                            {{-- Preserve filters --}}

                            <input type="hidden">


                            <input type="number" name="page" min="1"
                                   class="form-control form-control-sm" style="width: 80px;"
                                   placeholder="Page">
                            <button class="btn btn-sm btn-primary" type="button">Go</button>
                        </form>
                        <span class="text-nowrap small text-center text-md-start">of 1 Data</span>
                    </div>
                </div>

                <!-- Showing Range -->
                <div class="col-12 mt-3">
                    <div class="d-flex justify-content-center">
                        <span>to 5 Data</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>



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

    $('.next_btn').click(function() {
      owl.trigger('next.owl.carousel');
    });
    $('.prev_btn').click(function() {
      owl.trigger('prev.owl.carousel');
    });
  });
</script>


