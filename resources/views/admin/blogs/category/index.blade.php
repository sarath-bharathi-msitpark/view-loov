@extends('admin.layouts.admin')
@section('page-title')
    {{__('Manage Blog Category')}}
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
@section('action-btn')
    <div class="float-end mb-5">
        @can('manage blog categories')
            <a href="#" data-url="{{ route('general.blog-categories.create') }}" data-ajax-popup="true" data-bs-toggle="tooltip" title="{{__('Create')}}" title="{{__('Create')}}" data-title="{{__('Create New')}}"  class="btn btn-sm btn-primary">
                <i class="ti ti-plus"></i>
            </a>
        @endcan
    </div>
@endsection

@section('content')
    @include('admin.layouts.partials.nav')

    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body table-border-style">
                    <div class="table-responsive company_order_table">
                        <table class="table datatable">
                            <thead>
                            <tr>
                                <th style="position: unset; background-color: transparent">{{__('Icon') }}</th>
                                <th> {{__('Category')}}</th>
                                <th> {{__('Description')}}</th>
                                <th width="10%"> {{__('Action')}}</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach ($categories as $category)
                                <tr>
                                    @php
                                        $logo = Utility::get_file('uploads/blogs/categories/');
                                    @endphp
                                    <td class="tex_fix" style="position: unset;">
                                        <img src="{{ $logo.$category->icon }}" alt="{{ $category->name }}" class="img-fluid">
                                    </td>
                                    <td class="font-style">
                                        {{ $category->name }}
                                    </td>
                                    <td class="font-style">
                                        {{ $category->short_description }}
                                    </td>

                                    <td class="Action">
                                        <span>
                                            <div class="action-btn me-2">
                                                <a href="#" class="mx-3 btn btn-sm align-items-center bg-info" data-url="{{ route('general.blog-categories.edit',$category->id) }}" data-ajax-popup="true" data-title="{{__('Edit Category')}}" data-bs-toggle="tooltip" title="{{__('Edit')}}" data-original-title="{{__('Edit')}}">
                                                    <i class="ti ti-pencil text-white"></i>
                                                </a>
                                            </div>
                                            <div class="action-btn ">
                                                {!! Form::open(['method' => 'DELETE', 'route' => ['general.blog-categories.destroy', $category->id],'id'=>'delete-form-'.$category->id]) !!}
                                                <a href="#" class="mx-3 btn btn-sm align-items-center bs-pass-para bg-danger" data-bs-toggle="tooltip" title="{{__('Delete')}}" data-original-title="{{__('Delete')}}" data-confirm="{{__('Are You Sure?').'|'.__('This action can not be undone. Do you want to continue?')}}" data-confirm-yes="document.getElementById('delete-form-{{$category->id}}').submit();">
                                                    <i class="ti ti-trash text-white"></i>
                                                </a>
                                                {!! Form::close() !!}
                                            </div>
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection
