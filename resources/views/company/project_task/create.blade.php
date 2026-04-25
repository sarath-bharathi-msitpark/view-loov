{{ Form::open(['route' => ['organization.projects.tasks.store',$project_id,$stage_id],'id' => 'create_task', 'class'=>'needs-validation', 'novalidate']) }}
<div class="modal-body px-md-3 px-0">
    <div class="row">
        <div class="col-6">
            <div class="form-group">
                {{ Form::label('name', __('Task name'),['class' => 'form-label']) }}
                <x-required></x-required>
                {{ Form::text('name', null, ['class' => 'form-control','required'=>'required', 'placeholder'=>__('Enter Task Name')]) }}
            </div>
        </div>
        <div class="col-6">
            <div class="form-group">
                {{ Form::label('priority', __('Priority'),['class' => 'form-label']) }}
                <x-required></x-required>
                <small class="form-text text-muted mb-2 mt-0">{{__('Set Priority of your task')}}</small>
                <select class="form-control select2" name="priority" id="priority" required>
                    @foreach(\App\Models\ProjectTask::$priority as $key => $val)
                        <option value="{{ $key }}">{{ __($val) }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="col-6">
            <div class="form-group">
                {{ Form::label('start_date', __('Start Date'),['class' => 'form-label']) }}
                {{ Form::date('start_date', null, ['class' => 'form-control']) }}
            </div>
        </div>
        <div class="col-6">
            <div class="form-group">
                {{ Form::label('end_date', __('End Date'),['class' => 'form-label']) }}
                {{ Form::date('end_date', null, ['class' => 'form-control']) }}
            </div>
        </div>
        <div class="col-12">
            <div class="form-group">
                {{ Form::label('description', __('Description'),['class' => 'form-label']) }}
                <small
                    class="form-text text-muted mb-2 mt-0">{{__('This textarea will autosize while you type')}}</small>
                {{ Form::textarea('description', null, ['class' => 'custom_textarea mt-0','rows'=>'1','data-toggle' => 'autosize', 'placeholder'=>__('Enter Description')]) }}
            </div>
        </div>
        <div class="col-6">
            <div class="form-group">
                {{ Form::label('estimated_hrs', __('Estimated Hours'),['class' => 'form-label']) }}
                <x-required></x-required>
                <small
                    class="form-text text-muted mb-2 mt-0">{{__('allocated total ').$hrs['allocated'].__(' hrs in other tasks')}}</small>
                {{ Form::number('estimated_hrs', null, ['class' => 'form-control','required' => 'required','min'=>'0','maxlength' => '8', 'placeholder'=>__('Enter Estimated Hours')]) }}
            </div>
        </div>
    </div>

    <div class="form-group">
        <label class="form-label fs-4 fwbold">{{__('Task members')}}</label>
        <small class="form-text text-muted mb-2 mt-0">{{__('Below users are assigned in your project.')}}</small>
    </div>

    <div class="list-group list-group-flush mb-4">
        <div class="row">
            @foreach($project->users as $user)
                <div class="col-md-6">
                    <div class="list-group-item px-2 border-0">
                        <div class="d-flex flex-wrap px-2 gap-2 align-items-center">
                            <div class="col-auto">
                                <a href="#" class="avatar avatar_foredittask avatar-sm ">
                                    <img class="wid-40 rounded border-2 border border-primary ml-3"
                                         data-original-title="{{ $user->name ?? '' }}"
                                         @if (($user->employee->gender ?? null) === GENDER_MALE)
                                             src="{{ asset('assets/assestsnew/menimg.png') }}"
                                         alt="{{ $user->name }}"
                                         @elseif (($user->employee->gender ?? null) === GENDER_FEMALE)
                                             src="{{ asset('assets/assestsnew/femaile-report.svg') }}"
                                         alt="{{ $user->name }}"
                                         @else
                                             src="{{ $user->avatar ? asset('/storage/uploads/avatar/' . $user->avatar) : asset('assets/assestsnew/profile.png') }}"
                                         alt="{{ $user->name }}"
                                        @endif
                                    />
                                </a>
                            </div>
                            <div class="col">
                                <p class="d-block h6 text-sm mb-0">{{ $user->name }}</p>
                                <p class="card-text text-sm text-muted mb-0">{{ $user->email }}</p>
                            </div>
                            <div class="col-auto text-end add_usr btn_task_showcreate" data-id="{{ $user->id }}">
                                <button type="button" class="mr-3">
                                    <span class="btn-inner--visible d-flex justify-content-center align-items-center">
                                      <i class="ti ti-plus text-white" id="usr_icon_{{$user->id}}"></i>
                                    </span>
                                    <span class="btn-inner--hidden text-white"
                                          id="usr_txt_{{$user->id}}">{{__('')}}</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
        {{ Form::hidden('assign_to', null) }}
    </div>
    @if(isset($settings['google_calendar_enable']) && $settings['google_calendar_enable'] == 'on')
        <div class="form-group col-md-6">
            {{Form::label('synchronize_type',__('Synchronize in Google Calendar ?'),array('class'=>'form-label')) }}
            <div class="form-switch">
                <input type="checkbox" class="form-check-input mt-2" name="synchronize_type" id="switch-shadow"
                       value="google_calender">
                <label class="form-check-label" for="switch-shadow"></label>
            </div>
        </div>
    @endif
</div>
<div class="modal-footer">
    <input type="button" value="{{__('Cancel')}}" class="btn btn-secondary" data-bs-dismiss="modal">
    <input type="submit" value="{{__('Create')}}" class="btn btn-primary">
</div>
{{Form::close()}}

