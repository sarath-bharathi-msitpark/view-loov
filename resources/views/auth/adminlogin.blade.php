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
        <div class="row h-100 justify-content-center p-md-3" style="min-height: 70vh;">
            <div class="col-md-6 col-11 main_login_nowhite">
                <div class="row h-100 py-4">

                    <form action="{{ route('auth.login.verify') }}" method="POST" id="loginForm"
                          class="login-form px-md-4 needs-validation" novalidate>
                        @csrf

                        @if (session('status'))
                            <div class="mb-4 font-medium text-lg text-green-600 text-danger">
                                {{ session('status') }}
                            </div>
                        @endif

                        <img style="width: 180px;" src="{{ asset('assets/assestsnew/logo.png') }}" alt="">
                        <h2 class="mt-5 mb-3">Sign In</h2>

                        <!-- Email -->
                        <div class="mb-4">
                            <label class="form-label fw-semibold">Email<span style="color: red;">*</span></label>
                            <input type="text" name="email" class="form-control" placeholder="{{ __('Enter Email') }}"
                                   required style="border-radius: 100px;">
                            @error('email')
                            <span class="error invalid-email text-danger" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                            @enderror
                        </div>

                        <!-- Password -->
                        <div class="mb-4">
                            <label class="form-label fw-semibold">Password<span style="color: red;">*</span></label>
                            <div class="d-flex main_passwordmain" id="inputcontainer-1">
                                <input type="password" name="password" id="input-password" class="form-control"
                                       placeholder="{{ __('Password') }}" required>

                                <button type="button" id="show-password-1" onclick="togglePass(this)"
                                        data-input-id="input-password" data-show-button-id="show-password-1"
                                        data-hide-button-id="hide-password-1">
                                    <i class="fa fa-eye"></i>
                                </button>
                                <button type="button" id="hide-password-1" onclick="togglePass(this)" class="hide-icon"
                                        style="display: none;" data-input-id="input-password"
                                        data-show-button-id="show-password-1" data-hide-button-id="hide-password-1">
                                    <i class="fa fa-eye-slash"></i>
                                </button>
                            </div>
                            @error('password')
                            <span class="error invalid-password text-danger" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                            @enderror

                            @if (Route::has('password.request'))
                                <div class="d-flex justify-content-end">
                                    <a href="{{ route('password.request', $lang) }}" tabindex="0" class="ahchor_logs">
                                        {{ __('Forgot your password?') }}
                                    </a>
                                </div>
                            @endif
                        </div>

                        <!-- Submit Button -->
                        <div class="mb-4">
                            <div class="d-flex mt-5 flex-column align-items-center justify-content-center">
                                <button type="submit" class="btn fw-bold w-100 btn-primary px-4 py-2" id="saveBtn"
                                        style="border-radius: 100px;">Sign In
                                </button>
                                {{--<div class="d-flex w-100 ortext_posi justify-content-center align-items-center">
                                    <!--<span>Or</span>-->
                                </div>
                                <a href="{{ route('auth.google.login') }}" class="btn btn-outline-dark w-100 d-flex align-items-center justify-content-center my-4"
                                   style="border-radius: 100px;">
                                    <img src="https://www.gstatic.com/firebasejs/ui/2.0.0/images/auth/google.svg" alt="Google" width="20" height="20" class="me-2">
                                    Continue with Google
                                </a>
                                <span class="span_loginsign mt-2">
                                    If you don't have account, <a href="{{ route('auth.register') }}">Register</a>
                                </span>--}}
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <div class="col-md-6 col-11 main_login_blues">
                <div class="row justify-content-center align-items-center h-100">
                    <img class="w-75" src="{{ asset('assets/assestsnew/login_side1.svg') }}" alt="">
                </div>
            </div>
        </div>
    </div>
@endsection
