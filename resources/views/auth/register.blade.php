@extends('layouts.auth')
@section('page-title')
    {{ __('Register') }}
@endsection
@php
    $settings = Utility::settings();
    $default_price = 0;
    $logo = \App\Models\Utility::get_file('uploads/logo');
    $setting = \Modules\LandingPage\Entities\LandingPageSetting::settings();

@endphp
@push('custom-scripts')
    <script>
        $(document).ready(function () {
            function calculateTotal() {
                var maxUsers = parseFloat($('#max_users').val()) || 0;
                var price = parseFloat($('#price').val()) || 0;
                var tax = parseFloat($('#tax').val()) || 0;

                var baseTotal = price * maxUsers;
                var total = baseTotal + (baseTotal * (tax / 100));

                $('#total').val(total.toFixed(2));
                $('#calculated-total').text(total.toFixed(2));
            }

            calculateTotal();

            $('#max_users').on('input', function () {
                calculateTotal();
            });

            $('#plan_id').on('change', function () {
                var selectedOption = $(this).find('option:selected');
                if (selectedOption.length && selectedOption.val() !== '') {
                    var price = selectedOption.data('price') || '';
                    var tax = selectedOption.data('tax') || '';
                    var duration = selectedOption.data('duration') || '';

                    $('#price').val(price);
                    $('#tax').val(tax);
                    $("#duration").val(duration);
                    calculateTotal();
                    $('.subscription').removeClass('d-none');
                } else {
                    $('.subscription').addClass('d-none');
                }
            });

        });
    </script>
@endpush

@section('content')
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-12 my-4">
                <div class="row justify-content-center">
                    <img style="width: 220px;" src="{{ asset('assets/assestsnew/logo.png') }}" alt="">
                </div>
            </div>
            <div class="col-md-10 col-11 main_loginwhite">
                <div class="row py-4 justify-content-center">
                    <div class="col-12">
                        <h1 class="mb-5 text-center">Create New Account</h1>
                    </div>

                    <form method="POST" action="{{ route('auth.register.store', ['plan' => $plan]) }}"
                          class="needs-validation px-md-4 p-0">
                        @method('POST')
                        @csrf

                        @if (session('status'))
                            <div class="mb-4 font-medium text-lg text-danger">
                                {{ __('Email SMTP settings are not configured. Please contact your site admin.') }}
                            </div>
                        @endif

                        @if (session('error'))
                            <div class="alert alert-danger">
                                {{ session('error') }}
                            </div>
                        @endif

                        <div class="col-12 mb-4">
                            <label class="form-label fw-semibold">Full Name<span style="color: red;">*</span></label>
                            <input id="name" type="text" class="form-control @error('name') is-invalid @enderror"
                                   name="name" value="{{ session('name')??old('name') }}" autocomplete="name" autofocus
                                   placeholder="{{ __('Enter Name') }}" required style="border-radius: 100px;">
                            @error('name')
                            <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-4">
                                <label class="form-label fw-semibold">Email<span style="color: red;">*</span></label>
                                <input id="email" type="email" class="form-control @error('email') is-invalid @enderror"
                                       name="email" value="{{ session('email')??old('email') }}" autocomplete="email"
                                       placeholder="{{ __('Enter Email') }}" required style="border-radius: 100px;">
                                @error('email')
                                <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-4">
                                <label class="form-label fw-semibold">Password</label>
                                <input id="password" type="password"
                                       class="form-control @error('password') is-invalid @enderror"
                                       name="password" value="{{ session('password')??old('password') }}"
                                       autocomplete="new-password"
                                       placeholder="{{ __('Enter Password') }}" style="border-radius: 100px;"
                                       minlength="8">
                                @error('password')
                                <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                                @enderror
                            </div>
                        </div>

                        <div class="col-12 mb-4">
                            <label class="form-label fw-semibold">Company Name<span style="color: red;">*</span></label>
                            <input id="company_name" type="text"
                                   class="form-control @error('company_name') is-invalid @enderror"
                                   name="company_name" value="{{ old('company_name') }}" autocomplete="organization"
                                   placeholder="{{ __('Enter Company Name') }}" required style="border-radius: 100px;">
                            @error('company_name')
                            <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-4">
                                <label class="form-label fw-semibold">Country<span style="color: red;">*</span></label>
                                @php $country = App\Models\Country::where('created_by',1)->get(); @endphp
                                <select id="country" name="country" class="form-control" required
                                        style="border-radius: 100px;">
                                    <option value="">Select a country</option>
                                    @foreach ($country as $con)
                                        <option value="{{ $con->code }}">{{ $con->name }}</option>
                                    @endforeach
                                </select>
                                @error('country')
                                <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-4">
                                <label class="form-label fw-semibold">Mobile Number<span
                                        style="color: red;">*</span></label>
                                <div class="input-group">
                                    @php $countries = App\Models\Country::where('created_by',1)->get(); @endphp
                                    <select class="form-select" name="country_code" required
                                            style="max-width: 100px !important;">
                                        @foreach ($countries as $country)
                                            <option value="{{ $country->code }}"
                                                {{ old('country_code') == $country->code ? 'selected' : '' }}>
                                                +{{ $country->code }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <input id="mobile_no" type="tel" class="form-control" name="mobile_no"
                                           value="{{ old('mobile_no') }}" autocomplete="tel"
                                           placeholder="{{ __('Enter Mobile Number') }}" required>
                                </div>
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-md-6 mb-4">
                                <label class="form-label fw-semibold">Domain<span style="color: red;">*</span></label>
                                <input id="domain" type="url" class="form-control @error('domain') is-invalid @enderror"
                                       name="domain" value="{{ old('domain') }}" autocomplete="url"
                                       placeholder="https://" required>
                                @error('domain')
                                <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-4">
                                <label class="form-label fw-semibold">Maximum User<span
                                        style="color: red;">*</span></label>
                                <input id="max_users" type="number"
                                       class="form-control @error('max_users') is-invalid @enderror"
                                       name="max_users" value="1" min="1" autocomplete="off"
                                       placeholder="Maximum User" required>
                            </div>
                        </div>

                        <div class="mb-4">
                            <div class="w-100 d-flex align-items-center mt-2">
                                <input type="checkbox" class="checkbox_widths" name="terms" required>
                                <small class="anchoragree">
                                    I agree to the <a href="{{ route('privacy.policy') }}" target="_blank">privacy
                                        policy</a> and
                                    <a href="{{ route('terms') }}" target="_blank">terms & conditions</a>, and to
                                    receive marketing emails, newsletters, and product updates,
                                    with the option to unsubscribe anytime.
                                </small>
                                @error('terms')
                                <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                                @enderror
                            </div>
                            <div class="alert alert-info text-center my-3 subscription d-none"
                                 style="border-radius: 20px;">
                                <strong>Note:</strong> You are registering under a <strong>free trial</strong>. After
                                the trial period ends, the payable amount will be
                                <strong>₹<span
                                        id="calculated-total">{{ ($default_price??0 * 1) + ($default_price??0 * 1 * (18/100)) }}</span></strong>
                                per month, based on the number of users selected.
                            </div>
                        </div>

                        <div class="mb-4">
                            <div class="row justify-content-center">
                                <div class="col-lg-4 mt-3">
                                    <div class="row justify-content-center">
                                        <input type="hidden" name="ref_code" value="{{ $ref }}">
                                        <button type="submit"
                                                class="btn btn-primary px-4 py-2">{{ __('Start Trial') }}</button>
                                        <span class="span_loginsign mt-2">
                                        Existing account? <a href="{{ route('signin') }}">Sign In</a>
                                    </span>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </form>
                </div>
            </div>

            <div class="d-flex links_bottomlog justify-content-center gap-5 my-3">
                <a href="https://wa.me/+919025544235?text=Hi *Loov*, %0D%0A %0D%0AWe have a project for you. Let's chat, are you available?"
                   target="_blank">Help</a>
                <a href="{{ route('privacy.policy') }}" target="_blank">Privacy</a>
                <a href="{{ route('terms') }}" target="_blank">Terms</a>
            </div>
        </div>
@endsection
