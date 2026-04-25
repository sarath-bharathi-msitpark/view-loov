{{ Form::model($project, ['route' => ['organization.projects.update', $project->id], 'method' => 'PUT', 'enctype' => 'multipart/form-data', 'class'=>'needs-validation', 'novalidate']) }}
<div class="modal-body">
    <div class="row">
        <div class="col-sm-8 col-md-8">
            <div class="form-group">
                {{ Form::label('project_name', __('Project Name'), ['class' => 'form-label']) }}
                <x-required></x-required>
                {{ Form::text('project_name', null, ['class' => 'form-control', 'required' => 'required']) }}
            </div>
        </div>

        <div class="col-sm-4 col-md-4">
            <div class="form-group">
                {{ Form::label('project_id', __('Project Code'), ['class' => 'form-label']) }}
                <x-required></x-required>
                {{ Form::text('project_id', null, ['class' => 'form-control', 'required' => 'required']) }}
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-6 col-md-6">
            <div class="form-group select_multiprojects">
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
            <div class="form-group select_multiprojects">
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
                    value="{{ old('on_board_date', $project->on_board_date ? \Carbon\Carbon::parse($project->on_board_date)->format('Y-m-d') : '') }}"
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
                    value="{{ old('support_start_date', $project->support_start_date ? \Carbon\Carbon::parse($project->support_start_date)->format('Y-m-d') : '') }}"
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
                    value="{{ old('support_end_date', $project->support_end_date ? \Carbon\Carbon::parse($project->support_end_date)->format('Y-m-d') : '') }}"
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
                    value="{{ old('renewal_date', $project->renewal_date ? \Carbon\Carbon::parse($project->renewal_date)->format('Y-m-d') : '') }}"
                />
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-12 col-md-12">
            <div class="form-group">
                {{ Form::label('description', __('Description'), ['class' => 'form-label']) }}
                {{ Form::textarea('description', null, ['class' => 'custom_textarea mt-0', 'rows' => '4', 'cols' => '50']) }}
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-6 col-md-6">
            <div class="form-group">
                {{ Form::label('status', __('Status'), ['class' => 'form-label']) }}
                <select name="status" id="status" class="form-control main-element select2">
                    @foreach(\App\Models\Project::$project_status as $k => $v)
                        <option value="{{$k}}" {{ ($project->status == $k) ? 'selected' : ''}}>{{__($v)}}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="col-sm-6 col-md-6">
            {{ Form::label('project_image', __('Project Image'), ['class' => 'form-label']) }}
            <div class="form-file mb-3">
                <input type="file" class="form-control file-validate" name="project_image">
                <p id="" class="file-error text-danger"></p>
            </div>
            <img src="{{ \App\Models\Utility::get_file($project->project_image) }}" class="avatar avatar-xl"
                 alt="project-image">
        </div>

    </div>
</div>
<div class="modal-footer">
    <input type="button" value="{{__('Cancel')}}" class="btn  btn-secondary" data-bs-dismiss="modal">
    <input type="submit" value="{{__('Update')}}" class="btn  btn-primary">
</div>
{{Form::close()}}

