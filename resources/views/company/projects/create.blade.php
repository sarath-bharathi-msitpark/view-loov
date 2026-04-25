{{ Form::open(['route' => 'organization.projects.store', 'method' => 'post', 'enctype' => 'multipart/form-data', 'class' => 'needs-validation', 'novalidate']) }}
<div class="modal-body">
    <div class="row">
        <div class="col-sm-8 col-md-8">
            <div class="form-group">
                <label for="project_name" class="form-label">Project Name</label>
                <x-required></x-required>
                <input type="text" name="project_name" id="project_name" class="form-control"
                       required placeholder="Enter Project Name">
            </div>
        </div>

        <div class="col-sm-4 col-md-4">
            <div class="form-group">
                <label for="project_id" class="form-label">Project Code</label>
                <x-required></x-required>
                <input type="text" name="project_id" id="project_id" class="form-control"
                       required placeholder="Enter Project Code">
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-6 col-md-6">
            <div class="form-group select_multiprojects">
                <label for="teams" class="form-label">Teams</label>
                <x-required></x-required>
                <select name="teams[]" id="teams" class="form-control main-element select2" multiple required>
                    @foreach($teams as $id => $name)
                        <option value="{{ $id }}">{{ $name }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="col-sm-6 col-md-6">
            <div class="form-group select_multiprojects">
                <label for="users" class="form-label">Users</label>
                <x-required></x-required>
                <select name="users[]" id="users" class="form-control main-element select2" multiple required>
                    @foreach($users as $id => $name)
                        <option value="{{ $id }}">{{ $name }}</option>
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
                    value="{{ date('Y-m-d') }}"
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
                    value="{{ date('Y-m-d') }}"
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
                    value="{{ date('Y-m-d') }}"
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
                    value="{{ date('Y-m-d') }}"
                >
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-12 col-md-12">
            <div class="form-group">
                <label for="description" class="form-label">Description</label>
                <textarea name="description" id="description" class="custom_textarea mt-0" rows="5" cols="50"
                          placeholder="Enter Description"></textarea>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-6 col-md-6">
            <div class="form-group">
                <label for="status" class="form-label">Status</label>
                <select name="status" id="status" class="form-control main-element select2">
                    @foreach(\App\Models\Project::$project_status as $k => $v)
                        <option value="{{ $k }}">{{ __($v) }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="form-group col-sm-6 col-md-6">
            {{ Form::label('project_image', __('Project Image'), ['class' => 'form-label']) }}
            <x-required></x-required>
            <div class="form-file mb-3">
                <input type="file" class="form-control file-validate" name="project_image" required="required">
                <p id="" class="file-error text-danger"></p>
            </div>
        </div>
    </div>
</div>

<div class="modal-footer">
    <input type="button" value="{{__('Cancel')}}" class="btn  btn-secondary" data-bs-dismiss="modal">
    <input type="submit" value="{{__('Create')}}" class="btn  btn-primary">
</div>
{{Form::close()}}
