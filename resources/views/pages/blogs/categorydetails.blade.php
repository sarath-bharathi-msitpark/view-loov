@extends('layouts.auth')
@php
    use App\Models\Utility;
    $logo = \App\Models\Utility::get_file('uploads/logo');
    $settings = Utility::settings();
    $company_logo = $settings['company_logo'] ?? '';
@endphp

@section('page-title')
    {{ __('Documentation') }}
@endsection

@push('css-page')
    <link rel="stylesheet" href="{{ asset('assets/blog/style.css') }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"
        integrity="sha512-..." crossorigin="anonymous" referrerpolicy="no-referrer" />
        
    <style>
        .card{
            border: none !important;
        }
        .custom-login .custom-login-inner{
            max-width: 100% !important;
            padding: 15px 35px;
            min-height: 100vh;
        }
        .primary_box{
            padding: 10px;
        }
        .case_sec_1 {
            border-radius: 15px;
        }
        @media (max-width: 768px) {
            .custom-login .custom-login-inner{
                max-width: 100% !important;
                padding: 15px 15px;
            }
        }
    </style>
@endpush

@section('content')
    <div class="container-fluid">
        <div class="row justify-content-center mb-3">
            <div class="col-12 text-center">
                <img style="width: 180px;" src="{{ asset('assets/assestsnew/logo.png') }}" alt="">
            </div>
        </div>
        <div class="container-fluid">
        <div class="row align-items-center">
            <div class="col-md-12 case_sec_1 py-3">
                <div class="row align-items-center">
                    <div class="col-md-6">
                       <h6 class="mb-0 text-black fw-normal text-lg-start text-md-start text-center">
                           <a class="text-decoration-none text-gray" href="{{ route('blogs.index') }}">Documentation</a> / 
                           <a class="text-decoration-none text-gray" href="{{ route('blogs.category', $blog->category?->slug) }}"><span>{{ $blog->category?->name }}</span> /</a>
                           <a class="text-decoration-none actives" href="">{{ $blog->title }}</a></h6>
                    </div>
                    <div class="col-md-6 d-flex justify-content-md-end justify-content-center mt-2 mt-md-0">
                        <form action="{{ route('blogs.category', $blog->category?->slug) }}" method="GET" class="search-container d-flex align-items-center">
                            <i class="fas fa-search"></i>
                            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search">
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mt-5 justify-content-center">
            <div class="col-11">
                <div class="row justify-content-between">
                    {!! $blog->description !!}
                </div>
            </div>
        </div>
    </div>
    </div>
@endsection
