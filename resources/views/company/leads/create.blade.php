{{ Form::open(array('url' => 'organization/leads', 'class'=>'needs-validation', 'novalidate')) }}
<div class="modal-body">
    <div class="row">
        <div class="col-6 form-group">
            {{ Form::label('subject', __('Subject'),['class'=>'form-label']) }}<x-required></x-required>
            {{ Form::text('subject', null, array('class' => 'form-control','required'=>'required' , 'placeholder'=>__('Enter Subject'))) }}
        </div>
        <div class="col-6 form-group">
            {{ Form::label('user_id', __('User'),['class'=>'form-label']) }}<x-required></x-required>
            @if (\Auth::user()->hasRole([ROLE_ADMINISTRATOR]))
                {{ Form::select('user_id', $users,null, array('class' => 'form-control select','required'=>'required')) }}
                @if(count($users) == 1)
                    <div class="text-muted text-xs">
                        {{__('Please create new users')}} <a href="{{route('users.index')}}">{{__('here')}}</a>.
                    </div>
                @endif
            @else
                {{ Form::hidden('user_id', Auth::user()->id) }}
                {{ Form::text('user_name', Auth::user()->name, [
                    'class' => 'form-control',
                    'required' => true,
                    'readonly' => true
                ]) }}
            @endif
        </div>
        <div class="col-6 form-group">
            {{ Form::label('name', __('Client Name'),['class'=>'form-label']) }}<x-required></x-required>
            {{ Form::text('name', null, array('class' => 'form-control','required'=>'required' , 'placeholder' => __('Enter Name'))) }}
        </div>
        <div class="col-6 form-group">
            {{ Form::label('email', __('Client Email'),['class'=>'form-label']) }}<x-required></x-required>
            {{ Form::text('email', null, array('class' => 'form-control','required'=>'required' , 'placeholder' => __('Enter email'))) }}
        </div>
        <div class="col-6 form-group">
            <x-mobile label="{{__('Client Phone')}}" name="phone" value="{{old('phone')}}" placeholder="Enter Phone"></x-mobile>
        </div>
        <div class="col-6 form-group">
            {{ Form::label('pipeline_id', __('Pipeline'),['class'=>'form-label']) }}<x-required></x-required>
            {{ Form::select('pipeline_id', $pipelines,null, array('class' => 'form-control select','required'=>'required')) }}
        </div>
        <div class="col-6 form-group">
            {{ Form::label('stage_id', __('Stage'),['class'=>'form-label']) }}<x-required></x-required>
            {{ Form::select('stage_id', [''=>__('Select Stage')],null, array('class' => 'form-control select','required'=>'required')) }}
        </div>
        <div class="col-6 form-group">
            {{ Form::label('sources', __('Sources'),['class'=>'form-label']) }}<x-required></x-required>
            {{ Form::select('sources[]', $sources,null, array('class' => 'form-control select2','id'=>'choices-multiple2','multiple'=>'', 'required' => 'required')) }}
        </div>
        <div class="col-6 form-group">
            {{ Form::label('products', __('Products'),['class'=>'form-label']) }}<x-required></x-required>
            {{ Form::select('products[]', $products,null, array('class' => 'form-control select2', 'required' => 'required', 'placeholder'=>__('Select Product'))) }}
        </div>
        <div class="col-12 form-group">
            {{ Form::label('notes', __('Notes'),['class'=>'form-label']) }}
            {{ Form::textarea('notes',null, array('class' => 'summernote-simple')) }}
        </div>
    </div>
</div>

<div class="modal-footer">
    <input type="button" value="{{__('Cancel')}}" class="btn  btn-secondary" data-bs-dismiss="modal">
    <input type="submit" value="{{__('Create')}}" class="btn  btn-primary">
</div>

{{Form::close()}}

<script>

    $(document).on("change", "select[name=pipeline_id]", function () {
        var currVal = $(this).val();
        console.log('current val ', currVal);
        getStages(currVal);
    });

    function getStages(id) {
        $.ajax({
            url: '{{route('leads.json')}}',
            data: {pipeline_id: id, _token: $('meta[name="csrf-token"]').attr('content')},
            type: 'POST',
            success: function (data) {
                var stage_cnt = Object.keys(data).length;
                $("#stage_id").empty();
                if (stage_cnt > 0) {
                    $.each(data, function (key, data1) {
                        var select = '';
                        $("#stage_id").append('<option value="' + key + '" ' + select + '>' + data1 + '</option>');
                    });
                }
                $("#stage_id").val(stage_id);
                $('#stage_id').select2({
                    placeholder: "{{__('Select Stage')}}"
                });
            }
        })
    }
</script>
