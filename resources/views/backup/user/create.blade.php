{{ Form::open(['url' => 'users', 'method' => 'post', 'class'=>'needs-validation', 'novalidate']) }}
<div class="modal-body">
    <div class="row">
        @if (\Auth::user()->type == 'super admin')
            <div class="col-md-6">
                <div class="form-group">
                    {{ Form::label('name', __('Name'), ['class' => 'form-label']) }}<x-required></x-required>
                    {{ Form::text('name', null, ['class' => 'form-control', 'placeholder' => __('Enter Company Name'), 'required' => 'required']) }}
                    @error('name')
                        <small class="invalid-name" role="alert">
                            <strong class="text-danger">{{ $message }}</strong>
                        </small>
                    @enderror
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    {{ Form::label('email', __('Email'), ['class' => 'form-label']) }}<x-required></x-required>
                    {{ Form::email('email', null, ['class' => 'form-control', 'placeholder' => __('Enter Company Email'), 'required' => 'required']) }}
                    @error('email')
                        <small class="invalid-email" role="alert">
                            <strong class="text-danger">{{ $message }}</strong>
                        </small>
                    @enderror
                </div>
            </div>
            
            
              <div class="form-group mb-3">
                        <label for="company_name" class="form-label">{{ __('Company Name') }}</label>
                        <input id="company_name" type="text" 
                            class="form-control @error('company_name') is-invalid @enderror"
                            name="company_name" value="{{ old('company_name') }}" autocomplete="company_name" autofocus
                            placeholder="{{ __('Enter company name') }}" required="required">
                        @error('company_name')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                    
                    <div class="form-group mb-3">
                        <label for="domain" class="form-label">{{ __('Domain') }}</label>
                        <input id="domain" type="url" 
                            class="form-control @error('domain') is-invalid @enderror"
                            name="domain" value="{{ old('domain') }}" autocomplete="domain"
                            placeholder="{{ __('Enter domain') }}" required="required">
                        @error('domain')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                    
                    <div class="form-group mb-3">
                        <label for="country" class="form-label">Country</label>
                        
                        @php
                        
                        $country = App\Models\Country::get();
                        @endphp
                        <select id="country" name="country" class="form-control" required="required">
                              <option value="">Select a country</option>
                            @foreach($country as $con)
                            <option value="{{ $con->country_code }}">{{  $con->country_name }}</option>

                            @endforeach
                        </select>
                        @error('country')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
    
                    
                 <div class="form-group mb-3">
                        <label for="mobile_no" class="form-label">{{ __('Mobile No') }}</label>
                        <div class="input-group">
                            @php
                                $countries = App\Models\Country::get();
                            @endphp
                    
                            <!-- Country code dropdown -->
                            <select class="form-select" name="country_code" required>
                                @foreach($countries as $country)
                                    <option value="{{ $country->country_code }}" 
                                            {{ old('country_code') == $country->country_code ? 'selected' : '' }}>
                                        +{{ $country->country_code }}
                                    </option>
                                @endforeach
                            </select>
                    
                            <!-- Mobile number input -->
                            <input id="mobile_no" type="tel"
                                   class="form-control"
                                   name="mobile_no"
                                   value="{{ old('mobile_no') }}"
                                   autocomplete="tel"
                                   placeholder="{{ __('Enter mobile number') }}" required>
                        </div>
                    </div>


            {!! Form::hidden('role', 'company', null, ['class' => 'form-control select2', 'required' => 'required']) !!}
            <div class="col-md-6 mb-3 form-group mt-4">
                <label for="password_switch">{{ __('Login is enable') }}</label>
                <div class="form-check form-switch custom-switch-v1 float-end">
                    <input type="checkbox" name="password_switch" class="form-check-input input-primary pointer" value="on" id="password_switch">
                    <label class="form-check-label" for="password_switch"></label>
                </div>
            </div>
            <div class="col-md-6 ps_div d-none">
                <div class="form-group">
                    {{ Form::label('password', __('Password'), ['class' => 'form-label']) }}<x-required></x-required>
                    {{ Form::password('password', ['class' => 'form-control', 'placeholder' => __('Enter Company Password'), 'minlength' => '6']) }}
                    @error('password')
                        <small class="invalid-password" role="alert">
                            <strong class="text-danger">{{ $message }}</strong>
                        </small>
                    @enderror
                </div>
            </div>
        @else
            <div class="col-md-6">
                <div class="form-group">
                    {{ Form::label('name', __('Name'), ['class' => 'form-label']) }}<x-required></x-required>
                    {{ Form::text('name', null, ['class' => 'form-control', 'placeholder' => __('Enter User Name'), 'required' => 'required']) }}
                    @error('name')
                        <small class="invalid-name" role="alert">
                            <strong class="text-danger">{{ $message }}</strong>
                        </small>
                    @enderror
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    {{ Form::label('email', __('Email'), ['class' => 'form-label']) }}<x-required></x-required>
                    {{ Form::email('email', null, ['class' => 'form-control', 'placeholder' => __('Enter User Email'), 'required' => 'required']) }}
                    @error('email')
                        <small class="invalid-email" role="alert">
                            <strong class="text-danger">{{ $message }}</strong>
                        </small>
                    @enderror
                </div>
            </div>
            <div class="col-md-6">
                 <div class="form-group mb-3">
                        <label for="mobile_no" class="form-label">{{ __('Mobile No') }}</label>
                        <div class="input-group">
                            @php
                                $countries = App\Models\Country::get();
                            @endphp
                    
                            <!-- Country code dropdown -->
                            <select class="form-select" name="country_code" required>
                                @foreach($countries as $country)
                                    <option value="{{ $country->country_code }}" 
                                            {{ old('country_code') == $country->country_code ? 'selected' : '' }}>
                                        +{{ $country->country_code }}
                                    </option>
                                @endforeach
                            </select>
                    
                            <!-- Mobile number input -->
                            <input id="mobile_no" type="tel"
                                   class="form-control"
                                   name="mobile_no"
                                   value="{{ old('mobile_no') }}"
                                   autocomplete="tel"
                                   placeholder="{{ __('Enter mobile number') }}" required>
                        </div>
                    </div>
              </div>
            
                            
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="form-label" for="dob">Date of Birth</label>
                        <input type="date" class="form-control" id="dob" name="dob" required>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="form-label" for="doj">Date of Joining</label>
                        <input type="date" class="form-control" id="doj" name="doj" required>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="form-label" for="gender">Gender <x-required></x-required></label>
                        <select class="form-control" id="gender" name="gender" required>
                            <option value="" disabled selected>Select Gender</option>
                            <option value="Male">Male</option>
                            <option value="Female">Female</option>
                            <option value="Other">Other</option>
                        </select>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="form-label" for="designation">Designation <x-required></x-required></label>
                        <select class="form-control" id="designation" name="designation" required>
                            <option value="" disabled selected>Select Designation</option>
                            <!-- Add designation options dynamically -->
                             <option value="designation1">designation1</option>
                        </select>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="form-label" for="team">Team <x-required></x-required></label>
                        <select class="form-control" id="team" name="team" required>
                            <option value="" disabled selected>Select Team</option>
                            <!-- Add team options dynamically -->
                             <option value="team1">team1</option>
                        </select>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="form-label" for="shift">Shift <x-required></x-required></label>
                        <select class="form-control" id="shift" name="shift" required>
                            <option value="" disabled selected>Select Shift</option>
                            <!-- Add shift options dynamically -->
                             <option value="shift1">shift1</option>
                        </select>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="form-label" for="employee_id">Employee ID</label>
                        <input type="text" class="form-control" id="employee_id" name="employee_id" placeholder="Enter Employee ID" required>
                    </div>
                </div>

            <div class="form-group col-md-6">
                {{ Form::label('role', __('User Role'), ['class' => 'form-label']) }}<x-required></x-required>
                {!! Form::select('role', $roles, null, ['class' => 'form-control select', 'required' => 'required']) !!}
                @error('role')
                    <small class="invalid-role" role="alert">
                        <strong class="text-danger">{{ $message }}</strong>
                    </small>
                @enderror
            </div>
            <div class="col-md-5 mb-3 form-group mt-4">
                <label for="password_switch">{{ __('Login is enable') }}</label>
                <div class="form-check form-switch custom-switch-v1 float-end">
                    <input type="checkbox" name="password_switch" class="form-check-input input-primary pointer" value="on" id="password_switch">
                    <label class="form-check-label" for="password_switch"></label>
                </div>
            </div>
            <div class="col-md-6 ps_div d-none">
                <div class="form-group">
                    {{ Form::label('password', __('Password'), ['class' => 'form-label']) }}<x-required></x-required>
                    {{ Form::password('password', ['class' => 'form-control', 'placeholder' => __('Enter Company Password'), 'minlength' => '6']) }}
                    @error('password')
                        <small class="invalid-password" role="alert">
                            <strong class="text-danger">{{ $message }}</strong>
                        </small>
                    @enderror
                </div>
            </div>
        @endif
        @if (!$customFields->isEmpty())
            {{-- <div class="col-md-6"> --}}
                {{-- <div class="tab-pane fade show" id="tab-2" role="tabpanel"> --}}
                    @include('customFields.formBuilder')
                {{-- </div> --}}
            {{-- </div> --}}
        @endif
    </div>

</div>

<div class="modal-footer">
    <input type="button" value="{{ __('Cancel') }}" class="btn  btn-secondary" data-bs-dismiss="modal">
    <input type="submit" value="{{ __('Create') }}" class="btn  btn-primary">
</div>

{{ Form::close() }}
