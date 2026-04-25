@extends('layouts.auth')
@php
  //  $logo=asset(Storage::url('uploads/logo/'));
    $logo=\App\Models\Utility::get_file('uploads/logo');
 $company_logo=Utility::getValByName('company_logo');
@endphp
@section('page-title')
    {{__('Forgot Password')}}
@endsection

@php
    $languages = App\Models\Utility::languages();
@endphp
@section('content')

    <div class="container-fluid">
        <div class="row h-100 justify-content-center p-3" style="min-height: 100vh;">
            <div class="col-md-6 col-11 main_login_nowhite">
                <div class="row h-100 py-4">
                    
                    @if (session('status'))
                        <div class="alert alert-primary">
                            {{ session('status') }}
                        </div>
                    @endif
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    
                    {{Form::open(array('route'=>'password.update','method'=>'post','id'=>'loginForm', 'class' => 'needs-validation'))}}
                        @csrf  
                            <input type="hidden" name="token" value="{{ $request->route('token') }}">
                            <img style="width: 180px;" src="{{ asset('assets/assestsnew/logo.png') }}" alt="">
                            <h2 class="mt-5 mb-4">Reset Password</h2>
                            <div class="form-group mb-3">
                                {{Form::label('email',__('E-Mail Address'),['class'=>'form-label'])}}
                                {{Form::text('email',null,array('class'=>'form-control' , 'placeholder'=>__('Enter email')))}}
                                @error('email')
                                <span class="invalid-email text-danger" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                @enderror
                            </div>
                            <div class="form-group mb-3">
                                {{Form::label('password',__('Password'),['class'=>'form-label'])}}
                                {{Form::password('password',array('class'=>'form-control' , 'placeholder'=>__('Enter Password')))}}
                                @error('password')
                                <span class="invalid-password text-danger" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                @enderror
                            </div>
                            <div class="form-group mb-3">
                                {{Form::label('password_confirmation',__('Password Confirmation'),['class'=>'form-label'])}}
                                {{Form::password('password_confirmation',array('class'=>'form-control' , 'placeholder'=>__('Enter Confirm Password')))}}
                                @error('password_confirmation')
                                <span class="invalid-password_confirmation text-danger" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                @enderror
                            </div>
                            <div class="d-grid">
                                <div class="d-flex flex-column align-items-center justify-content-center">
                                    <button type="submit" class="btn fw-bold w-100 btn-primary px-4 py-2" id='resetBtn' style="border-radius: 100px;">{{ __('Reset') }}</button>
                                </div>
                            </div>
                        {{Form::close()}}    
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
