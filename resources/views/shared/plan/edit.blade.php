    {{Form::model($plan, array('route' => array('general.plans.update', $plan->id), 'method' => 'PUT', 'enctype' => "multipart/form-data", 'class'=>'needs-validation', 'novalidate')) }}
    <div class="modal-body">

        <div class="row">
            <div class="form-group col-md-6">
                {{ Form::label('plan_type', __('Plan Type'), ['class' => 'form-label']) }}<x-required></x-required>
                {{ Form::select('plan_type', ['' => 'Select', 'common' => 'Common Plan', 'company' => 'Company Plan'], $plan->company_id != ""?'company':'common', ['class' => 'form-control select2', 'id' => 'plan_type_selector', 'required']) }}
            </div>
    
            <div class="form-group col-md-6" id="company_id_group">
                {{ Form::label('company_id', __('Company'), ['class' => 'form-label']) }}
                {{ Form::select('company_id', $companies, $plan->company_id ?? null, ['class' => 'form-control select2']) }}
            </div>
            
            <div class="form-group col-md-6 d-none" id="plan_name_group">
                {{ Form::label('name', __('Plan Name'), ['class' => 'form-label']) }}<x-required></x-required>
                {{ Form::text('name', $plan->name ??null, ['class' => 'form-control', 'placeholder' => __('Enter Plan Name')]) }}
            </div>
            @if($plan->price > 0)
                <div class="form-group col-md-6">
                    {{Form::label('price',__('Price Per Person'),['class'=>'form-label'])}}<x-required></x-required>
                    {{Form::number('price',null,array('class'=>'form-control','placeholder'=>__('Plan Price Per Person'),'required'=>'required' ,'step' => '0.01'))}}
                </div>
                <div class="form-group col-md-6">
                    {{ Form::label('tax', __('Tax'), ['class' => 'form-label']) }}
                    {{ Form::number('tax', $plan->tax ?? '', ['class' => 'form-control', 'step' => '0.01', 'placeholder' => __('Enter Tax')]) }}
                </div>
    
                <div class="form-group col-md-6">
                    {{ Form::label('duration', __('Duration'),['class'=>'form-label']) }}<x-required></x-required>
                    {!! Form::select('duration', $arrDuration, null,array('class' => 'form-control select','required'=>'required')) !!}
                </div>
            @endif
            
            <div class="form-group col-md-6">
                {{Form::label('max_users',__('Max Users per Company'),['class'=>'form-label'])}}<x-required></x-required>
                {{Form::number('max_users',null,array('class'=>'form-control','required'=>'required', 'placeholder' => __('Enter maximum number of users'), 'id'=>'max_users'))}}
                <small id="user-warning" class="text-danger" style="display:none;"></small>
            </div>
    
            <div class="form-group col-md-12">
                {{ Form::label('description', __('Short Description'),['class'=>'form-label']) }}
                {!! Form::textarea('description', $plan->description ?? '', ['class'=>'form-control','rows'=>'2', 'placeholder' => __('Enter Short Description')]) !!}
            </div>
            
            @php
                $backupOptions = explode(',', env('BACKUP_DURATION_OPTIONS', '7,15,30,60,90'));
                $defaultBackup = env('BACKUP_DURATION_DEFAULT', 30);
                $selectedBackup = $plan->backup_duration ?? $defaultBackup;
                
                if (!in_array($selectedBackup, $backupOptions)) {
                    $selectedBackup = $backupOptions[0];
                }
            @endphp
            
            <div class="form-group col-md-12">
                {{ Form::label('backup_duration', __('Backup Duration (in days)'), ['class' => 'form-label']) }}<x-required></x-required>
                {!! Form::select('backup_duration', array_combine($backupOptions, $backupOptions), $selectedBackup, ['class' => 'form-control', 'required'=>'required']) !!}
            </div>

            
            <div class="form-group col-md-12">
                <label class="form-label">{{ __('Plan Features') }} <span class="text-danger">*</span></label>
                
                <div id="plan-features-wrapper">
                    
                    @forelse(json_decode($plan->features ?? '[]') as $feature)
                        <div class="input-group mb-2">
                            <input type="text" name="features[]" class="form-control" value="{{ $feature }}" required>
                            <button type="button" class="btn btn-danger remove-feature">&times;</button>
                        </div>
                    @empty
                        <div class="input-group mb-2">
                            <input type="text" name="features[]" class="form-control" placeholder="Enter a feature" required>
                            <button type="button" class="btn btn-danger remove-feature" style="display: none;">&times;</button>
                        </div>
                    @endforelse
                </div>
            
                <button type="button" class="btn btn-sm btn-primary" id="add-feature">+ Add Feature</button>
            </div>


            
            <div class="form-group col-md-12">
                <div class="col-md-12">
                    <div class="form-group" style="overflow: auto;">
                        @if(!empty($permissions))
                            <h6 class="my-3">{{ __('Assign Menu Permission') }}</h6>
                            <table class="table table-striped mb-0">
                                <thead>
                                    <tr>
                                        <th>
                                            <input type="checkbox" 
                                                   class="form-check-input align-middle custom_align_middle" 
                                                   name="staff_checkall" 
                                                   id="staff_checkall" >
                                        </th>
                                        <th>{{ __('Module') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($modules as $module)
                                        @if(in_array($module, (array) $permissions))
                                            @php
                                                $key = array_search($module, $permissions);
                                            @endphp
                                            @if($key !== false)
                                                <tr>
                                                    <td>
                                                        {{ Form::checkbox(
                                                            'permissions[]',
                                                            $key,
                                                            $plan->permissionHas($key),
                                                            [
                                                                'class' => 'form-check-input align-middle ischeck staff_checkall',
                                                                'id' => 'permission' . $key,
                                                                'data-id' => str_replace([' ', '&'], '', $module),
                                                            ]
                                                        ) }}
                                                    </td>
                                                    <td>
                                                        <label 
                                                            for="permission{{ $key }}" 
                                                            class="ischeck staff_checkall" 
                                                            data-id="{{ str_replace([' ', '&', '_'], '', $module) }}"
                                                        >
                                                            {{ ucwords(str_replace([' ', '&', '_'], ' ', $module)) }}
                                                        </label>
                                                    </td>
                                                </tr>
                                            @endif
                                        @endif
                                    @endforeach
                                </tbody>
                            </table>
                        @endif
                    </div>
                </div>
            </div>

        </div>
    </div>

    <div class="modal-footer">
    <input type="button" value="{{__('Cancel')}}" class="btn  btn-secondary" data-bs-dismiss="modal">
    <input type="submit" value="{{__('Update')}}" class="btn  btn-primary" id="submit-btn">
</div>
    {{ Form::close() }}

<script>
    $(document).ready(function () {
        
        $("#staff_checkall").click(function(){
            $('.staff_checkall').not(this).prop('checked', this.checked);
        });
        $(".ischeck").click(function(){
            var ischeck = $(this).data('id');
            $('.isscheck_'+ ischeck).prop('checked', this.checked);
        });
        
        function togglePlanFields() {
            var selectedType = $('#plan_type_selector').val();
            if (selectedType === 'common') {
                $('#company_id_group').addClass('d-none');
                $('#plan_name_group').removeClass('d-none');
                $('#company_id_group select').prop('required', false);
                $('#plan_name_group input').prop('required', true);
            } else {
                $('#company_id_group').removeClass('d-none');
                $('#plan_name_group').addClass('d-none');
                $('#company_id_group select').prop('required', true);
                $('#plan_name_group input').prop('required', false);
            }
        }

        $('#plan_type_selector').on('change', togglePlanFields);
        togglePlanFields();
        
        
        $('#add-feature').click(function () {
            let newInput = `
                <div class="input-group mb-2">
                    <input type="text" name="features[]" class="form-control" placeholder="Enter a feature" required>
                    <button type="button" class="btn btn-danger remove-feature">&times;</button>
                </div>`;
            $('#plan-features-wrapper').append(newInput);
        });
    
        $(document).on('click', '.remove-feature', function () {
            $(this).closest('.input-group').remove();
        });
        

        var activeUsers = {{ $activeUserCount }};
    
        $('#max_users').on('change', function () {
            var maxUsers = parseInt($(this).val());
            var warning = $('#user-warning');
    
            if(!isNaN(maxUsers) && maxUsers < activeUsers){
                var diff = activeUsers - maxUsers;
                warning.text("This company already has " + activeUsers + 
                         " active users. Please remove or inactivate " + diff + 
                         " user(s) before reducing.");
                warning.show();
                $('#submit-btn').prop('disabled', true);
            } else {
                warning.hide();
                $('#submit-btn').prop('disabled', false);
            }
        });
    });
</script>
