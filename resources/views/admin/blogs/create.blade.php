@extends('admin.layouts.admin')
@section('page-title')
    {{__('Create Blog')}}
@endsection
@section('page-icon')
    {{ asset('assets/assestsnew/FolderSetting.svg') }}
@endsection

@push('css-page')
    <link rel="stylesheet" href="{{asset('css/summernote/summernote-bs4.css')}}">
    <link href="{{asset('css/bootstrap-tagsinput.css')}}" rel="stylesheet"/>

@endpush
@push('script-page')

    <script src="{{asset('js/bootstrap-tagsinput.min.js')}}"></script>
    <script>
        var e = $('[data-toggle="tags"]');
        e.length && e.each(function () {
            $(this).tagsinput({tagClass: "badge badge-primary"})
        });
    </script>
    <script src="{{asset('css/summernote/summernote-bs4.js')}}"></script>
@endpush

@section('content')

    @include('admin.layouts.partials.nav')
    {{Form::open(array('url'=>'general/blogs','method'=>'post', 'class'=>'needs-validation', 'novalidate'))}}
    <div class="row mt-3">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="form-group col-md-6">
                            {{ Form::label('category_id', __('Category'),['class'=>'form-label']) }}<x-required></x-required>
                            {{ Form::select('category_id', $categories,null, array('class' => 'form-control select','required'=>'required')) }}
                
                            <div class=" text-sm mt-1">
                                {{__('Please add category. ')}}<a href="{{route('general.blog-categories.index')}}"><b>{{__('Add Category')}}</b></a>
                            </div>
                        </div>
                        <div class="col-md-6 form-group">
                            {{ Form::label('title', __('Title'),['class'=>'form-label']) }}<x-required></x-required>
                            {{ Form::text('title', '', array('class' => 'form-control','required'=>'required', 'placeholder' => __('Enter'))) }}
                        </div>
                        <div class="form-group col-md-12 editor_pad_remover">
                            {!! Form::label('description', __('Description'),['class'=>'form-label']) !!}<x-required></x-required>
                            <textarea class="form-control summernote-simple-2" name="description" id="exampleFormControlTextarea2" rows="15" placeholder="Enter"></textarea>
                        </div>
                        <div class="form-group col-md-6">
                            {{ Form::label('status', __('Status'), ['class' => 'form-label']) }}<x-required></x-required>
                            {{ Form::select('status', ['1' => 'Active', '0' => 'Inactive'], null, ['class' => 'form-control select', 'required' => 'required']) }}
                        </div>

                        <div class="form-group col-md-6">
                            {{ Form::label('meta_title', __('Meta Title'),['class'=>'form-label']) }}<x-required></x-required>
                            {{ Form::text('meta_title', '', array('class' => 'form-control','required'=>'required' , 'placeholder'=>__('Enter'))) }}
                        </div>
                        <div class="form-group col-md-6">
                            {{ Form::label('meta_description', __('Meta Description'),['class'=>'form-label']) }}<x-required></x-required>
                            {{ Form::text('meta_description', '', array('class' => 'form-control','required'=>'required' , 'placeholder'=>__('Enter'))) }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-12 text-end">
            <div class="form-group">
                <input type="button" value="{{__('Cancel')}}" onclick="location.href = '{{route("general.blogs.index")}}';" class="btn btn-secondary me-1">
                <input type="submit" value="{{__('Create')}}" class="btn btn-primary">
            </div>
        </div>
        {{Form::close()}}
    </div>
@endsection

