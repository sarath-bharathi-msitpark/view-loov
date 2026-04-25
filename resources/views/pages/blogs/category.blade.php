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
        <div class="row align-items-center">
            <div class="col-md-12 case_sec_1 py-3">
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <h6 class="mb-0 actives fw-normal text-lg-start text-md-start text-center"><a class="text-decoration-none text-gray" href="{{ route('blogs.index') }}">Documentation</a></h6>
                    </div>
                    <div class="col-md-6 d-flex justify-content-md-end justify-content-center mt-2 mt-md-0">
                        <form action="{{ route('blogs.category', $category['slug']) }}" method="GET" class="search-container d-flex align-items-center">
                            <i class="fas fa-search"></i>
                            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search">
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mt-5 justify-content-center">
            <div class="col-12 col-md-11">
                <div class="w-100">
                    <div class="row" id="categoryDetails">
                        <div class="col-12 col-md-6 mb-4 mb-md-0">
                            <div class="accordion-container">
                                <div class="accordion-header">Popular Articles</div>
                                @foreach($blogs as $popularBlog)
                                    <a class="text-decoration-none" href="{{ route('blogs.details', [$popularBlog->category->slug, $popularBlog->slug]) }}">

                                        <div class="mt-3 accordion-item">
                                            <div class="accordion-title">
                                                <img src="{{ asset('assets/assestsnew/news-paper.svg') }}" alt="">
                                                {{ $popularBlog->title }}
                                            </div>
                                            <img class="accordion-arrow" src="{{ asset('assets/assestsnew/arrow-up-right.svg') }}" alt="">
                                        </div>
                                    </a>
                                    <div style="background-color: #BABABA; width: 100%; height: 1px;"></div>
                                @endforeach
                            </div>
                        </div>


                        <div class="col-12 col-md-6 d-flex justify-content-center align-items-start">
                            <div class="box_white w-100">
                                <div
                                    class="text-decoration-none d-flex flex-column justify-content-center align-items-center gap-4">
                                    <div class="card_casestudies w-100">
                                        <div class="d-flex justify-content-start align-items-center gap-3">
                                            <div class="primary_box">
                                                <img src="{{ $category['icon'] }}" alt="{{ $category['name'] }}" class="img-fluid">
                                            </div>
                                            <div>
                                                <h3 class="mb-0 fw-medium fs-md-4 text-black categoryName">{{ $category['name'] }}</h3>
                                                <h6 class="text-black fw-normal fs-6" id="categoryDescription">{{ $category['short_description'] }}</h6>
                                            </div>
                                        </div>
                                    </div>
                                    <img src="{{ asset('assets/assestsnew/feature_dashbord.svg') }}" alt="" class="img-fluid">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
