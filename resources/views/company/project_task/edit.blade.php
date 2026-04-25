{{ Form::model($task, ['route' => ['organization.projects.tasks.update',[$project->id, $task->id]], 'id' => 'edit_task', 'method' => 'POST', 'class'=>'needs-validation', 'novalidate']) }}
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
                <select class="form-control select" name="priority" id="priority" required>
                    @foreach(\App\Models\ProjectTask::$priority as $key => $val)
                        <option
                            value="{{ $key }}" {{ ($key == $task->priority) ? 'selected' : '' }} >{{ __($val) }}</option>
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
        <label class="form-label fs-4 fw-bold">{{__('Task members')}}</label>
        <small class="form-text text-muted mb-2 mt-0">{{__('Below users are assigned in your project.')}}</small>
    </div>
    <div class="list-group list-group-flush mb-4">
        <div class="row g-3">
            @foreach($project->users as $user)
                <div class="col-md-6">
                    <div class="list-group-item px-2 border-0">
                        <div class="row align-items-center">
                            <div class="col-auto ps-md-3 ps-0 pe-0">
                                <a href="#" class="avatar avatar_foredittask avatar-sm rounded-circle">
                                    <img class="wid-40 rounded-circle ml-3 border border-2 border-primary"
                                         data-original-title="{{ $user->name ?? '' }}"
                                         @if (($user->employee->gender ?? null) === GENDER_MALE)
                                             src="{{ asset('assets/assestsnew/menimg.png') }}"
                                         @elseif (($user->employee->gender ?? null) === GENDER_FEMALE)
                                             src="{{ asset('assets/assestsnew/femaile-report.svg') }}"
                                         @elseif(!empty($user->avatar))
                                             src="{{ asset('/storage/uploads/avatar/' . $user->avatar) }}"
                                         @else
                                             src="{{ asset('assets/assestsnew/profile.png') }}"
                                         @endif
                                         alt="{{ $user->name ?? 'User' }}">
                                </a>
                            </div>
                            <div class="col">
                                <p class="d-block h6 text-sm mb-0">{{ $user->name }}</p>
                                <p class="card-text text-sm text-muted mb-0">{{ $user->email }}</p>
                            </div>
                            @php
                                $usrs = explode(',',$task->assign_to);
                            @endphp
                            <div class="col-auto text-end mt-md-0 mt-3 {{ (in_array($user->id,$usrs)) ? '':'' }}"
                                 data-id="{{ $user->id }}">
                                <button type="button" class="btn_task_showedit mr-3">
                            <span class="btn-inner--visible d-flex justify-content-center align-items-center">
                              <i class="text-white ti ti-{{ (in_array($user->id,$usrs)) ? 'check' : 'plus' }} "
                                 id="usr_icon_{{$user->id}}"></i>
                            </span>
                                    <span class="btn-inner--hidden text-white"
                                          id="usr_txt_{{$user->id}}">{{ (in_array($user->id,$usrs)) ? __('Added') : __('Add')}}</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
        {{ Form::hidden('assign_to', $task->assign_to) }}
    </div>
</div>
<div class="modal-footer">
    <input type="button" value="{{__('Cancel')}}" class="btn  btn-secondary" data-bs-dismiss="modal">
    <input type="submit" value="{{__('Update')}}" class="btn  btn-primary">
</div>
{{Form::close()}}

<script>
    // Convert existing user IDs to array
    let assignedUsers = $('input[name="assign_to"]').val()
        ? $('input[name="assign_to"]').val().split(',').map(Number)
        : [];

    // handle click in edit modal
    $(document).on('click', '.btn_task_showedit', function () {
        let id = $(this).closest('[data-id]').data('id');

        if (assignedUsers.includes(id)) {
            assignedUsers = assignedUsers.filter(u => u !== id);
            $('#usr_icon_' + id).removeClass('ti-check').addClass('ti-plus');
            $('#usr_txt_' + id).text('Add');
        } else {
            assignedUsers.push(id);
            $('#usr_icon_' + id).removeClass('ti-plus').addClass('ti-check');
            $('#usr_txt_' + id).text('Added');
        }

        $('input[name="assign_to"]').val(assignedUsers.join(','));
    });
</script>

