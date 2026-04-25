{{ Form::model($project, ['route' => ['organization.projects.update', $project->id], 'method' => 'PUT', 'enctype' => 'multipart/form-data', 'class'=>'needs-validation', 'novalidate']) }}
<div class="modal-body">
    {{-- start for ai module--}}
    @php
        $plan= \App\Models\Utility::getChatGPTSettings();
    @endphp
    @if($plan->chatgpt == 1)
        <div class="text-end">
            <a href="#" data-size="md" class="btn  btn-primary btn-icon btn-sm" data-ajax-popup-over="true"
               data-url="{{ route('organization.generate',['projects']) }}"
               data-bs-placement="top" data-title="{{ __('Generate content with AI') }}">
                <i class="fas fa-robot"></i> <span>{{__('Generate with AI')}}</span>
            </a>
        </div>
    @endif
    {{-- end for ai module--}}
    <div class="row">
        <div class="col-sm-12 col-md-12">
            <div class="form-group">
                {{ Form::label('project_name', __('Project Name'), ['class' => 'form-label']) }}
                <x-required></x-required>
                {{ Form::text('project_name', null, ['class' => 'form-control', 'required' => 'required']) }}
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-6 col-md-6">
            <div class="form-group">
                <label for="teams" class="form-label">Teams</label>
                <x-required></x-required>
                <select name="teams[]" id="teams" class="form-control select2" multiple required>
                    @foreach($teams as $id => $name)
                        <option value="{{ $id }}" {{ in_array($id, $selectedTeams) ? 'selected' : '' }}>
                            {{ $name }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="col-sm-6 col-md-6">
            <div class="form-group">
                <label for="users" class="form-label">Users</label>
                <x-required></x-required>
                <select name="users[]" id="users" class="form-control select2" multiple required>
                    @foreach($users as $id => $name)
                        <option value="{{ $id }}" {{ in_array($id, $selectedUsers) ? 'selected' : '' }}>
                            {{ $name }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="col-sm-6 col-md-6">
            <div class="form-group">
                <label for="on_board_date" class="form-label">On Board Date</label>
                <input
                    type="date"
                    id="on_board_date"
                    name="on_board_date"
                    class="form-control"
                    value="{{ old('on_board_date', optional($project->on_board_date)->format('Y-m-d')) }}"
                >
            </div>
        </div>

        <div class="col-sm-6 col-md-6">
            <div class="form-group">
                <label for="support_start_date" class="form-label">Support Start Date</label>
                <input
                    type="date"
                    id="support_start_date"
                    name="support_start_date"
                    class="form-control"
                    value="{{ old('support_start_date', optional($project->support_start_date)->format('Y-m-d')) }}"
                >
            </div>
        </div>

        <div class="col-sm-6 col-md-6">
            <div class="form-group">
                <label for="support_end_date" class="form-label">Support End Date</label>
                <input
                    type="date"
                    id="support_end_date"
                    name="support_end_date"
                    class="form-control"
                    value="{{ old('support_end_date', optional($project->support_end_date)->format('Y-m-d')) }}"
                >
            </div>
        </div>

        <div class="col-sm-6 col-md-6">
            <div class="form-group">
                <label for="renewal_date" class="form-label">Renewal Date</label>
                <input
                    type="date"
                    id="renewal_date"
                    name="renewal_date"
                    class="form-control"
                    value="{{ old('renewal_date', optional($project->renewal_date)->format('Y-m-d')) }}"
                >
            </div>
        </div>

        {{--        <div class="col-sm-6 col-md-6">--}}
        {{--            <div class="form-group">--}}
        {{--                {{ Form::label('start_date', __('Start Date'), ['class' => 'form-label']) }}--}}
        {{--                {{ Form::date('start_date', null, ['class' => 'form-control']) }}--}}
        {{--            </div>--}}
        {{--        </div>--}}
        {{--        <div class="col-sm-6 col-md-6">--}}
        {{--            <div class="form-group">--}}
        {{--                {{ Form::label('end_date', __('End Date'), ['class' => 'form-label']) }}--}}
        {{--                {{ Form::date('end_date', null, ['class' => 'form-control']) }}--}}
        {{--            </div>--}}
        {{--        </div>--}}

        {{--    </div>--}}
        {{--    <div class="row">--}}
        {{--        <div class="col-sm-6 col-md-6">--}}
        {{--            <div class="form-group">--}}
        {{--                {{ Form::label('client', __('Client'),['class'=>'form-label']) }}--}}
        {{--                <x-required></x-required>--}}
        {{--                {!! Form::select('client', $clients, $project->client_id,array('class' => 'form-control select2','id'=>'choices-multiple1','required'=>'required')) !!}--}}
        {{--            </div>--}}
        {{--        </div>--}}

        {{--    </div>--}}
        {{--    <div class="row">--}}
        {{--        <div class="col-sm-6 col-md-6">--}}
        {{--            <div class="form-group">--}}
        {{--                {{ Form::label('budget', __('Budget'), ['class' => 'form-label']) }}--}}
        {{--                {{ Form::number('budget', null, ['class' => 'form-control']) }}--}}
        {{--            </div>--}}
        {{--        </div>--}}
        {{--        <div class="col-6 col-md-6">--}}
        {{--            <div class="form-group">--}}
        {{--                {{ Form::label('estimated_hrs', __('Estimated Hours'),['class' => 'form-label']) }}--}}
        {{--                {{ Form::number('estimated_hrs', null, ['class' => 'form-control','min'=>'0','maxlength' => '8']) }}--}}
        {{--            </div>--}}
        {{--        </div>--}}
    </div>
    <div class="row">
        <div class="col-sm-12 col-md-12">
            <div class="form-group">
                {{ Form::label('description', __('Description'), ['class' => 'form-label']) }}
                {{ Form::textarea('description', null, ['class' => 'custom_textarea mt-0', 'rows' => '4', 'cols' => '50']) }}
            </div>
        </div>
    </div>
    {{--    <div class="row">--}}
    {{--        <div class="col-sm-12 col-md-12">--}}
    {{--            <div class="form-group">--}}
    {{--                {{ Form::label('tag', __('Tag'), ['class' => 'form-label']) }}--}}
    {{--                {{ Form::text('tag', isset($project->tags) ? $project->tags: '', ['class' => 'form-control', 'data-toggle' => 'tags']) }}--}}
    {{--            </div>--}}
    {{--        </div>--}}
    {{--    </div>--}}
    <div class="row">
        <div class="col-sm-12 col-md-12">
            <div class="form-group">
                {{ Form::label('status', __('Status'), ['class' => 'form-label']) }}
                <select name="status" id="status" class="form-control main-element select2">
                    @foreach(\App\Models\Project::$project_status as $k => $v)
                        <option value="{{$k}}" {{ ($project->status == $k) ? 'selected' : ''}}>{{__($v)}}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>
    {{--    <div class="row">--}}
    {{--        <div class="col-sm-12 col-md-12">--}}
    {{--            {{ Form::label('project_image', __('Project Image'), ['class' => 'form-label']) }}--}}
    {{--            <div class="form-file mb-3">--}}
    {{--                <input type="file" class="form-control file-validate" name="project_image">--}}
    {{--                <p id="" class="file-error text-danger"></p>--}}
    {{--            </div>--}}
    {{--            <img {{$project->img_image}} class="avatar avatar-xl" alt="project-image">--}}
    {{--        </div>--}}

    {{--    </div>--}}
</div>
<div class="modal-footer">
    <input type="button" value="{{__('Cancel')}}" class="btn  btn-secondary" data-bs-dismiss="modal">
    <input type="submit" value="{{__('Update')}}" class="btn  btn-primary">
</div>
{{Form::close()}}

