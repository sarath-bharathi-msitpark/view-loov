@foreach($project->users as $user)
    <li class="list-group-item px-0">
        <div class="d-flex flex-wrap align-items-center justify-content-between">
            <div class="col-sm-auto mb-3 mb-sm-0">
                <div class="d-flex align-items-center">
                    <div class="avatar d-none avatar-sm me-3">
                        @php
                            $avatar = \App\Models\Utility::get_file('uploads/avatar/');

                        @endphp
                        {{--                        <img src="@if($user->avatar) src="{{asset('/storage/uploads/avatar/'.$user->avatar)}}" @else src="{{asset('/storage/uploads/avatar/avatar.png')}}" @endif " alt="kal" class="img-user">--}}
                        <img @if($user->avatar) src="{{$avatar.$user->avatar}}" @else src="{{$avatar. 'avatar.png'}}"
                             @endif  alt="image" class="rounded border-2 border border-primary">

                    </div>
                    <img src="{{ asset('assets/assestsnew/menimg.png') }}" class="avatar_projectsshow" alt="Male">
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
