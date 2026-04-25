{{ Form::model($bug, array('route' => array('organization.task.bug.update', $project_id,$bug->id ), 'method' => 'POST', 'class'=>'needs-validation', 'novalidate')) }}
<div class="modal-body">
    <div class="row">
        <div class="form-group col-md-6">
            {{ Form::label('title', __('Title'),['class'=>'form-label']) }}
            <x-required></x-required>
            {{ Form::text('title', null, array('class' => 'form-control','required'=>'required', 'placeholder'=>__('Enter Title'))) }}
        </div>
        <div class="form-group col-md-6">
            {{ Form::label('priority', __('Priority'),['class'=>'form-label']) }}
            <x-required></x-required>
            {!! Form::select('priority', $priority, null,array('class' => 'form-control select','required'=>'required')) !!}
        </div>
        <div class="form-group col-md-6">
            {{ Form::label('start_date', __('Start Date'),['class'=>'form-label']) }}
            <x-required></x-required>
            {{ Form::date('start_date', null, array('class' => 'form-control','required'=>'required')) }}
        </div>
        <div class="form-group col-md-6">
            {{ Form::label('due_date', __('Due Date'),['class'=>'form-label']) }}
            <x-required></x-required>
            {{ Form::date('due_date', null, array('class' => 'form-control','required'=>'required')) }}
        </div>
        <div class="form-group col-md-6">
            {{ Form::label('status', __('Bug Status'),['class'=>'form-label']) }}
            <x-required></x-required>
            {!! Form::select('status', $status, null,array('class' => 'form-control select2','required'=>'required')) !!}
        </div>
        <div class="form-group col-md-6">
            {{ Form::label('assign_to', __('Assigned To'),['class'=>'form-label']) }}
            <x-required></x-required>
            {{ Form::select('assign_to', $users, null,array('class' => 'form-control select2','required'=>'required')) }}
        </div>
    </div>
    <div class="row">
        <div class="form-group col-md-12 editor_pad_remover">
            {{ Form::label('description', __('Description'),['class'=>'form-label']) }}
            <textarea class="form-control summernote-edit" name="description" rows="6">{{ $bug->description }}</textarea>
        </div>
    </div>
</div>
<div class="modal-footer">
    <input type="button" value="{{__('Cancel')}}" class="btn btn-secondary" data-bs-dismiss="modal">
    <input type="submit" value="{{__('Update')}}" class="btn btn-primary" id="bug-edit-submit">
</div>
{{Form::close()}}

<script>
(function () {

    /* ── Summernote init ── */
    if (typeof window.jQuery !== 'undefined' && typeof window.jQuery.fn.summernote !== 'undefined') {
        try {
            window.jQuery('.summernote-edit').summernote({
                height: 180,
                toolbar: [
                    ['style', ['bold', 'italic', 'underline', 'clear']],
                    ['font', ['strikethrough']],
                    ['para', ['ul', 'ol', 'paragraph']],
                    ['insert', ['link']],
                    ['view', ['codeview']]
                ],
                popover: { image: [], link: [], air: [] },
            });
        } catch(e) {}
    }

    /* ── Submit — sync Summernote content before form submit ── */
    var submitBtn = document.getElementById('bug-edit-submit');
    if (submitBtn) {
        submitBtn.addEventListener('click', function () {
            if (typeof window.jQuery !== 'undefined' && typeof window.jQuery.fn.summernote !== 'undefined') {
                try {
                    window.jQuery('.summernote-edit').each(function () {
                        window.jQuery(this).val(window.jQuery(this).summernote('code'));
                    });
                } catch(e) {}
            }
        });
    }

})();
</script>