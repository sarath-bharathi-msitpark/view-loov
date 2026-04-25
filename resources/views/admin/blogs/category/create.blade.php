{{ Form::open(array('url' => 'general/blog-categories', 'class'=>'needs-validation', 'novalidate', 'enctype'=>'multipart/form-data')) }}
<div class="modal-body">
    <div class="row">
        <div class="form-group col-md-12">
            {{ Form::label('name', __('Category Name'),['class'=>'form-label']) }}<x-required></x-required>
            {{ Form::text('name', '', array('class' => 'form-control','required'=>'required' , 'placeholder'=>__('Enter Category Name'))) }}
        </div>
        <div class="form-group col-md-12">
            {!! Form::label('description', __('Short Description'),['class'=>'form-label']) !!}
            <textarea class="form-control" name="short_description" rows="2" placeholder="Description"></textarea>
        </div>
        <div class="col-md-12 form-group">
            {{Form::label('icon',__('Icon'),['class'=>'form-label'])}}
            <div class="choose-file ">
                <label for="icon" class="form-label">
                    <input type="file" class="form-control file-validate" name="icon" id="icon" data-filename="icon">
                <p id="" class="file-error text-danger"></p>
                    <img id="image" class="mt-3" style="width:25%;"/>
                </label>
            </div>
        </div>
        <div class="form-group  col-md-12">
            {{ Form::label('status', __('Status'), ['class' => 'form-label']) }}<x-required></x-required>
            {{ Form::select('status', [1 => __('Active'), 0 => __('Inactive')], 1, ['class' => 'form-control', 'required' => 'required']) }}
        </div>
        <div class="form-group col-md-12">
            {{ Form::label('meta_title', __('Meta Title'),['class'=>'form-label']) }}<x-required></x-required>
            {{ Form::text('meta_title', '', array('class' => 'form-control','required'=>'required' , 'placeholder'=>__('Enter'))) }}
        </div>
        <div class="form-group col-md-12">
            {{ Form::label('meta_description', __('Meta Description'),['class'=>'form-label']) }}<x-required></x-required>
            {{ Form::text('meta_description', '', array('class' => 'form-control','required'=>'required' , 'placeholder'=>__('Enter'))) }}
        </div>
    </div>
</div>
<div class="modal-footer">
    <input type="button" value="{{__('Cancel')}}" class="btn  btn-secondary" data-bs-dismiss="modal">
    <input type="submit" value="{{__('Create')}}" class="btn  btn-primary">
</div>
{{ Form::close() }}


<script>

    //hide & show chartofaccount

    $(document).on('click', '.cattype', function ()
    {
        var type = $(this).val();
        if (type != 'product & service') {
            $('.account').removeClass('d-none')
            $('.account').addClass('d-block');
        } else {
            $('.account').addClass('d-none')
            $('.account').removeClass('d-block');
        }
    });


    $(document).on('change', '#type', function () {
        var type = $(this).val();

        $.ajax({
            url: '{{route('productServiceCategory.getaccount')}}',
            type: 'POST',
            data: {
                "type": type,
                "_token": "{{ csrf_token() }}",
            },

            success: function (data) {
                $('#chart_account').empty();
                $.each(data.chart_accounts, function (key, value) {
                    $('#chart_account').append('<option value="' + key + '" class="subAccount">' + value + '</option>');
                    $.each(data.sub_accounts, function (subkey, subvalue) {
                        if(key == subvalue.account)
                        {
                            $('#chart_account').append('<option value="' + subvalue.id + '">' + '&nbsp; &nbsp;&nbsp;' + subvalue.name + '</option>');
                        }
                });
                });
            }

        });
    });
</script>

