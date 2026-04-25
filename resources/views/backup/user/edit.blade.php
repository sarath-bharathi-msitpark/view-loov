{{ Form::model($user, ['route' => ['users.update', $user->id], 'method' => 'PUT', 'class' => 'needs-validation', 'novalidate']) }}
<div class="modal-body">
    <div class="row">
        <div class="col-md-6">
            <div class="form-group ">
                {{ Form::label('name', __('Name'), ['class' => 'form-label']) }}<x-required></x-required>
                {{ Form::text('name', null, ['class' => 'form-control font-style', 'placeholder' => __('Enter User Name'), 'required' => 'required']) }}
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
                {{ Form::text('email', null, ['class' => 'form-control', 'placeholder' => __('Enter User Email'), 'required' => 'required']) }}
                @error('email')
                    <small class="invalid-email" role="alert">
                        <strong class="text-danger">{{ $message }}</strong>
                    </small>
                @enderror
            </div>
        </div>

        @if (\Auth::user()->type == 'super admin')

            <div class="form-group mb-3">
                <label for="company_name" class="form-label">{{ __('Company Name') }}</label>
                {{ Form::text('company_name', null, ['class' => 'form-control', 'placeholder' => __('Enter company name'), 'required' => 'required']) }}

                @error('company_name')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>

            <div class="form-group mb-3">
                <label for="domain" class="form-label">{{ __('Domain') }}</label>
                {{ Form::url('domain', null, ['class' => 'form-control', 'placeholder' => __('Enter domain'), 'required' => 'required']) }}

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
                    @foreach ($country as $con)
                        <option value="{{ $con->country_code }}"
                            {{ $user->country == $con->country_code ? 'selected' : '' }}>{{ $con->country_name }}
                        </option>
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
                        @foreach ($countries as $country)
                            <option value="{{ $country->country_code }}"
                                {{ $user->country_code == $country->country_code ? 'selected' : '' }}>
                                +{{ $country->country_code }}
                            </option>
                        @endforeach
                    </select>

                    <!-- Mobile number input -->
                    <input id="mobile_no" type="tel" class="form-control" name="mobile_no"
                        value="{{ $user->mobile_no }}" autocomplete="tel"
                        placeholder="{{ __('Enter mobile number') }}" required>

                </div>
            </div>

        @endif
        
        <!--//user start edit-->
        
        <div class="row">
    @php
        $countries = App\Models\Country::get();
    @endphp

    <!-- Mobile Number with Country Code -->
    <div class="col-md-6">
        <div class="form-group mb-3">
            <label for="mobile_no" class="form-label">{{ __('Mobile No') }}</label>
            <div class="input-group">
                <select class="form-select" name="country_code" required>
                    @foreach($countries as $country)
                        <option value="{{ $country->country_code }}" 
                            {{ old('country_code', $user->country_code ?? '') == $country->country_code ? 'selected' : '' }}>
                            +{{ $country->country_code }}
                        </option>
                    @endforeach
                </select>
                <input id="mobile_no" type="tel"
                       class="form-control"
                       name="mobile_no"
                       value="{{ old('mobile_no', $user->mobile_no ?? '') }}"
                       autocomplete="tel"
                       placeholder="{{ __('Enter mobile number') }}" required>
            </div>
        </div>
    </div>

    <!-- Date of Birth -->
    <div class="col-md-6">
        <div class="form-group mb-3">
            <label for="dob" class="form-label">Date of Birth</label>
            <input type="date" class="form-control" id="dob" name="dob"
                   value="{{ old('dob', $user->dob ?? '') }}" required>
        </div>
    </div>

    <!-- Date of Joining -->
    <div class="col-md-6">
        <div class="form-group mb-3">
            <label for="doj" class="form-label">Date of Joining</label>
            <input type="date" class="form-control" id="doj" name="date_of_join"
                   value="{{ old('date_of_join', $user->date_of_join ?? '') }}" required>
        </div>
    </div>

    <!-- Gender -->
    <div class="col-md-6">
        <div class="form-group mb-3">
            <label for="gender" class="form-label">Gender <x-required/></label>
            <select class="form-control" id="gender" name="gender" required>
                <option value="" disabled {{ old('gender', $user->gender ?? '') == '' ? 'selected' : '' }}>Select Gender</option>
                <option value="Male" {{ old('gender', $user->gender ?? '') == 'Male' ? 'selected' : '' }}>Male</option>
                <option value="Female" {{ old('gender', $user->gender ?? '') == 'Female' ? 'selected' : '' }}>Female</option>
                <option value="Other" {{ old('gender', $user->gender ?? '') == 'Other' ? 'selected' : '' }}>Other</option>
            </select>
        </div>
    </div>

    <!-- Designation -->
    <div class="col-md-6">
        <div class="form-group mb-3">
            <label for="designation" class="form-label">Designation <x-required/></label>
            <select class="form-control" id="designation" name="designation" required>
                <option value="" disabled {{ old('designation', $user->designation ?? '') == '' ? 'selected' : '' }}>Select Designation</option>
                <option value="designation1" {{ old('designation', $user->designation ?? '') == 'designation1' ? 'selected' : '' }}>designation1</option>
            </select>
        </div>
    </div>

    <!-- Team -->
    <div class="col-md-6">
        <div class="form-group mb-3">
            <label for="team" class="form-label">Team <x-required/></label>
            <select class="form-control" id="team" name="team" required>
                <option value="" disabled {{ old('team', $user->team ?? '') == '' ? 'selected' : '' }}>Select Team</option>
                <option value="team1" {{ old('team', $user->team ?? '') == 'team1' ? 'selected' : '' }}>team1</option>
                                <option value="team2" {{ old('team', $user->team ?? '') == 'team2' ? 'selected' : '' }}>team2</option>

            </select>
        </div>
    </div>

    <!-- Shift -->
    <div class="col-md-6">
        <div class="form-group mb-3">
            <label for="shift" class="form-label">Shift <x-required/></label>
            <select class="form-control" id="shift" name="shift" required>
                <option value="" disabled {{ old('shift', $user->shift ?? '') == '' ? 'selected' : '' }}>Select Shift</option>
                <option value="shift1" {{ old('shift', $user->shift ?? '') == 'shift1' ? 'selected' : '' }}>shift1</option>
                                <option value="shift2" {{ old('shift', $user->shift ?? '') == 'shift2' ? 'selected' : '' }}>shift2</option>

            </select>
        </div>
    </div>

    <!-- Employee ID -->
    <div class="col-md-6">
        <div class="form-group mb-3">
            <label for="employee_id" class="form-label">Employee ID</label>
            <input type="text" class="form-control" id="employee_id" name="employee_id"
                   value="{{ old('employee_id', $user->employee_id ?? '') }}"
                   placeholder="Enter Employee ID" required>
        </div>
    </div>
</div>

        
<!--user end-->

        @if (\Auth::user()->type != 'super admin')
            <div class="form-group col-md-12">
                {{ Form::label('role', __('User Role'), ['class' => 'form-label']) }}<x-required></x-required>
                {!! Form::select('role', $roles, $user->roles, ['class' => 'form-control select', 'required' => 'required']) !!}
                @error('role')
                    <small class="invalid-role" role="alert">
                        <strong class="text-danger">{{ $message }}</strong>
                    </small>
                @enderror
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
    <input type="button" value="{{ __('Cancel') }}" class="btn  btn-secondary"data-bs-dismiss="modal">
    <input type="submit" value="{{ __('Update') }}" class="btn  btn-primary">
</div>

{{ Form::close() }}
