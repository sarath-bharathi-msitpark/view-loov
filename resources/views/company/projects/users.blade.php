@foreach($project->users as $user)
    <li class="list-group-item px-0">
        <div class="d-flex flex-wrap align-items-center justify-content-between">
            <div class="col-sm-auto mb-3 mb-sm-0">
                <div class="d-flex align-items-center">
                    @php
                        $avatarPath = \App\Models\Utility::get_file('uploads/avatar/');
                        $gender = $user->employee->gender ?? null;

                        if ($gender === GENDER_MALE) {
                            $src = asset('assets/assestsnew/menimg.png');
                        } elseif ($gender === GENDER_FEMALE) {
                            $src = asset('assets/assestsnew/femaile-report.svg');
                        } else {
                            $src = $user->avatar
                                ? $avatarPath . $user->avatar
                                : $avatarPath . 'avatar.png';
                        }
                    @endphp

                    <img src="{{ $src }}" class="avatar_projectsshow" alt="Male">
                    <div class="div">
                        <h5 class="m-0">{{ $user->name }}</h5>
                        <small class="text-muted">{{ $user->email }}</small>
                    </div>
                </div>
            </div>
            <div class="col-sm-auto text-sm-end d-flex align-items-center">
                @can('project_management')
                    <div class="action-btn" style="width:max-content;">
                        {!! Form::open(['method' => 'DELETE', 'route' => ['organization.projects.user.destroy',  [$project->id,$user->id]]]) !!}
                        <a href="#" class="copy_com  align-items-center "
                           data-bs-toggle="tooltip" title="{{__('Delete')}}"><i class="ti ti-trash text-danger"></i></a>

                        {!! Form::close() !!}
                    </div>
                @endcan
            </div>
        </div>
    </li>
@endforeach
