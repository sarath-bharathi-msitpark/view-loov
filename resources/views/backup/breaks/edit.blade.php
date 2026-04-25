{{ Form::model($break, ['route' => ['breaks.update', $break->id], 'method' => 'PUT', 'class' => 'needs-validation', 'novalidate']) }}
<div class="modal-body">
    <div class="form-group">
        {{ Form::label('break_name', __('Break Name'), ['class' => 'form-label']) }}
        <x-required></x-required>
        {{ Form::text('break_name', null, ['class' => 'form-control', 'required' => 'required']) }}
    </div>

    <div class="form-group">
        {{ Form::label('maximum_break_time', __('Maximum Break Time (minutes)'), ['class' => 'form-label']) }}
        <x-required></x-required>
        {{ Form::number('maximum_break_time', null, ['class' => 'form-control', 'required' => 'required']) }}
    </div>

    <div class="form-group">
        {{ Form::label('status', __('Status'), ['class' => 'form-label']) }}
        <x-required></x-required>
        {{ Form::select('status', [1 => __('Active'), 0 => __('Inactive')], null, ['class' => 'form-control', 'required' => 'required']) }}
    </div>
</div>
<div class="modal-footer">
    <input type="button" value="{{ __('Cancel') }}" class="btn btn-secondary" data-bs-dismiss="modal">
    <input type="submit" value="{{ __('Update') }}" class="btn btn-primary">
</div>
{{ Form::close() }}
