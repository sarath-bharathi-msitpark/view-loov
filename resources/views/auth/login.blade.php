@extends('layouts.auth')
@php
    use App\Models\Utility;
    $logo = \App\Models\Utility::get_file('uploads/logo');
    $settings = Utility::settings();
    $company_logo = $settings['company_logo'] ?? '';
@endphp

@section('page-title')
    {{ __('Login') }}
@endsection

@section('content')
    <div class="container-fluid">
        <div class="row h-100 justify-content-center align-items-center" style="min-height: 70vh;">
            <div class="col-md-6 col-11 main_login_nowhite p-0">
                <form method="POST" action="{{ route('auth.login') }}" id="loginForm"
                      class="login-form px-md-4 needs-validation text-center" novalidate>
                    @csrf
                    @if (session('status'))
                        <div class="mb-4 font-medium text-lg text-green-600 text-primary">
                            {{session('status') }}
                        </div>
                    @endif

                    <h5 class="my-4">Work smarter. Stay accountable.<br> Loov helps you manage with ease</h5>

                    <img style="width: 180px;" src="{{ asset('assets/assestsnew/logo.png') }}" alt="">

                    <h2 class="mt-4 mb-1">Sign In</h2>

                    <div class="custom-login-form">
                        <div class="form-group mb-3 text-start">
                            <label class="form-label">{{ __('Email') }}</label>
                            {{ Form::text('email', null, ['class' => 'form-control', 'placeholder' => __('Enter Your Email')]) }}
                            @error('email')
                            <span class="error invalid-email text-danger" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                            @enderror
                        </div>

                        <div class="form-group mb-3 text-start">
                            <label class="form-label">{{ __('Password') }}</label>
                            {{ Form::password('password', ['class' => 'form-control', 'placeholder' => __('Enter Your Password'), 'id' => 'input-password']) }}
                            @error('password')
                            <span class="error invalid-password text-danger" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                            @enderror
                        </div>

                        <div class="form-group mb-2">
                            <div class="d-flex flex-wrap align-items-center justify-content-between">
                                @if (Route::has('password.request'))
                                    <span><a
                                            href="{{ route('password.request', $lang) }}">{{ __('Forgot your password?') }}</a></span>
                                @endif
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <div class="d-flex mt-3 flex-column align-items-center justify-content-center">
                            <button type="submit" class="btn fw-bold w-100 btn-primary px-4 py-2" id="saveBtn"
                                    style="border-radius: 100px;">Sign In
                            </button>
                        </div>
                    </div>

                    <div class="d-flex mb-2 flex-column align-items-center justify-content-center">
                        <div class="d-flex w-100 ortext_posi justify-content-center align-items-center">
                            <span>Or</span>
                        </div>
                    </div>

                    <div class="mb-2">
                        <div class="d-flex mt-3 flex-column align-items-center justify-content-center">
                            <a href="{{ route('auth.google.login') }}"
                               class="google_login_btn d-flex align-items-center justify-content-center my-4"
                               style="border-radius: 100px;">
                                <img src="https://www.gstatic.com/firebasejs/ui/2.0.0/images/auth/google.svg"
                                     alt="Google" width="20" height="20" class="me-2">
                                Continue with Google
                            </a>
                            <span class="span_loginsign mt-2">
                                    If you don't have account, <a href="{{ route('signup') }}">Register</a>
                                </span>
                        </div>
                    </div>

                </form>
            </div>

            <div class="col-md-6 col-11 main_login_blues">
                <div class="row justify-content-center align-items-center h-100">
                    <img class="w-75" src="{{ asset('assets/assestsnew/login_side1.svg') }}" alt="">
                </div>
            </div>
        </div>
    </div>
@endsection
