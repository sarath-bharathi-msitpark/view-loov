@extends('layouts.auth')

@section('page-title')
    {{ __('Forgot Password') }}
@endsection

@php
      $settings = Utility::settings();
@endphp

@push('custom-scripts')
@if ($settings['recaptcha_module'] == 'on')
        {!! NoCaptcha::renderJs() !!}
    @endif
@endpush

@if ($settings['cust_darklayout'] == 'on')
    <style>
        .g-recaptcha {
            filter: invert(1) hue-rotate(180deg) !important;
        }
    </style>
@endif

@php
    $languages = App\Models\Utility::languages();
@endphp
@section('language-bar')
    <div class="lang-dropdown-only-desk">
        <li class="dropdown dash-h-item drp-language">
            <a class="dash-head-link dropdown-toggle btn" href="#" data-bs-toggle="dropdown" aria-expanded="false">
                <span class="drp-text"> {{ $languages[$lang] }}
                </span>
            </a>
            <div class="dropdown-menu dash-h-dropdown dropdown-menu-end">
                @foreach($languages as $code => $language)
                <a href="{{ route('password.request',$code) }}"tabindex="0"
                class="dropdown-item ">
                <span>{{ Str::ucfirst($language) }}</span>
            </a>
                @endforeach
            </div>
        </li>
    </div>
@endsection
@section('content')
{{--
    <div class="card-body">
        <div>
            <h2 class="mb-3 f-w-600">{{ __('Forgot Password') }}</h2>
            
            @if (session('status'))
            <div class="alert alert-primary">
                {{ session('status') }}
            </div>
        @endif
            @if (session('error'))
    <div class="alert alert-danger">
        {{ session('error') }}
    </div>
@endif
        </div>
        <form method="POST" action="{{ route('password.email') }}" class='needs-validation' novalidate>
            @csrf
            <div class="">
                <div class="form-group mb-3">
                    <label for="email" class="form-label">{{ __('E-Mail') }}</label>
                    <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus placeholder="{{__('Enter Email')}}" required='required'>
                    @error('email')
                    <span class="invalid-feedback" role="alert">
                        <small>{{ $message }}</small>
                    </span>
                    @enderror
                </div>

                @if ($settings['recaptcha_module'] == 'on')
                @if (isset($settings['google_recaptcha_version']) && $settings['google_recaptcha_version'] == 'v2-checkbox')
                    <div class="form-group col-lg-12 col-md-12 mt-3">
                        {!! NoCaptcha::display() !!}
                        @error('g-recaptcha-response')
                            <span class="small text-danger" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                @else
                    <div class="form-group col-lg-12 col-md-12 mt-3">
                        <input type="hidden" id="g-recaptcha-response" name="g-recaptcha-response" class="form-control">
                        @error('g-recaptcha-response')
                            <span class="error small text-danger" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                @endif
            @endif

                <div class="d-grid">
                    <button type="submit" class="btn btn-primary btn-block mt-2">{{ __('Send Password Reset Link') }}</button>
                </div>
                <p class="my-4 text-center">{{__("Back to")}} <a href="{{ route('signin' ,$lang) }}" class="text-primary">{{__('Login')}}</a></p>

            </div>
        </form>
    </div>
    --}}
    
    
    
    
    <div class="container-fluid">
        <div class="row h-100 justify-content-center p-3" style="min-height: 100vh;">
            <div class="col-md-6 col-11 main_login_nowhite">
                <div class="row h-100 py-4 align-content-start">
                      @if (session('status'))
            <div class="alert alert-primary">
                {{ session('status') }}
            </div>
        @endif
            @if (session('error'))
    <div class="alert alert-danger">
        {{ session('error') }}
    </div>
@endif
        
        <form method="POST" action="{{ route('password.email') }}" class='needs-validation px-md-4 p-0' novalidate>
            @csrf  
                    <!--<form class="px-4">-->
                        <img style="width: 180px;" src="{{ asset('assets/assestsnew/logo.png') }}" alt="">
                        <h2 class="mt-5 mb-4">Verify Email</h2>
                        <div class="mb-4">
                            <label class="form-label fw-semibold">Enter Email ID<span style="color: red;">*</span></label>
                            <!--<input type="email" class="form-control" style="border-radius: 100px;" placeholder="Enter">-->
                            
                    <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus placeholder="{{__('Enter Email')}}" required='required'  style="border-radius: 100px;">
                    @error('email')
                    <span class="invalid-feedback" role="alert">
                        <small>{{ $message }}</small>
                    </span>
                    @enderror
                            
                            
                            <div class="d-flex">
                                <a href="{{ route('signin') }}" class="ahchor_logs"> <i class="fa-solid fas fa-chevron-left"></i> Back to Login</a>
                            </div>
                        </div>
                        <div class="mb-4">
                            <div class="d-flex mt-5 flex-column align-items-center justify-content-center">
                                <button type="submit" class="btn fw-bold w-100 btn-primary px-4 py-2" style="border-radius: 100px;">Verify Mail</button>
                                
                                
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <div class="col-md-6 col-11 main_login_blues">
                <div class="row justify-content-center align-items-center h-100">
                    <img class="w-75" src="{{ asset('assets/assestsnew/logo_side2.svg') }}" alt="">
                </div>
            </div>
        </div>
    </div>
    
    
    
@endsection

@if (isset($settings['recaptcha_module']) && $settings['recaptcha_module'] == 'on')
    @if (isset($settings['google_recaptcha_version']) && $settings['google_recaptcha_version'] == 'v2-checkbox')
        {!! NoCaptcha::renderJs() !!}
    @else
        <script src="https://www.google.com/recaptcha/api.js?render={{ $settings['google_recaptcha_key'] }}"></script>
        <script>
            $(document).ready(function() {
                grecaptcha.ready(function() {
                    grecaptcha.execute('{{ $settings['google_recaptcha_key'] }}', {
                        action: 'submit'
                    }).then(function(token) {
                        $('#g-recaptcha-response').val(token);
                    });
                });
            });
        </script>
    @endif
@endif




