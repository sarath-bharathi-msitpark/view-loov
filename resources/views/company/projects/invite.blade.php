<div class="modal-body">
    <div class="col-12">
        <div class="row">
            @if(count($users) > 0)
                @foreach($users as $user)
                    <div class="col-6 mb-4">
                        <div class="list-group-item px-0">
                            <div class="row ">
                                <div class="col-auto">
                                    <img
                                        @php
                                            $gender = $user->employee->gender ?? null;

                                            if ($gender === GENDER_MALE) {
                                                $src = asset('assets/assestsnew/menimg.png');
                                            } elseif ($gender === GENDER_FEMALE) {
                                                $src = asset('assets/assestsnew/femaile-report.svg');
                                            } else {
                                                $src = $user->avatar
                                                    ? asset('/storage/uploads/avatar/' . $user->avatar)
                                                    : asset('/storage/uploads/avatar/avatar.png');
                                            }
                                        @endphp

                                        src="{{ $src }}"
                                        class="wid-40 rounded border-2 border border-primary ml-3"
                                        width="40" height="40"
                                        title="{{ $user->name }}"
                                        alt="{{ $user->name }}"
                                    >
                                </div>
                                <div class="col">
                                    <h6 class="mb-0">{{ $user->name }}</h6>
                                    <p class="mb-0"><span class="text-muted">{{ $user->email }}</p>
                                </div>
                                <div class="col-auto">
                                    <div class="action-btn ms-2 invite_usr" data-id="{{ $user->id }}">
                                        <button type="button" class="rounded_add_btn align-items-center">
                                            <span class="btn-inner--visible">
                                                <i class="ti ti-plus text-primary" id="usr_icon_{{$user->id}}"></i>
                                            </span>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            @else
                <div class="col-12 text-center">
                    <h5>{{__('No User Exist')}}</h5>
                </div>
            @endif
        </div>
    </div>

    {{ Form::hidden('project_id', $project_id,['id'=>'project_id']) }}
</div>

