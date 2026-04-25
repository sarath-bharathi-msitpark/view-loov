@extends('admin.layouts.admin')
@section('page-title')
    {{__('Manage Blogs')}}
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
        @can('manage blogs')
            <a href="{{ route('general.blog-categories.index') }}"title="{{__('Category')}}" data-title="{{__('Category')}}"  class="btn btn-sm btn-primary">
                <i class="ti ti-folder-plus"></i>
            </a>
            <a href="{{ route('general.blogs.create') }}"title="{{__('Create')}}" data-title="{{__('Create New')}}"  class="btn btn-sm btn-primary">
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
                                <th style="position: unset; background-color: transparent">{{__('Title') }}</th>
                                <th> {{__('Category')}}</th>
                                <th> {{__('Status')}}</th>
                                <th width="10%"> {{__('Action')}}</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach ($blogs as $blog)
                                <tr>
                                    <td class="tex_fix"  style="position: unset;">
                                        {{ $blog->title }}
                                    </td>
                                    <td>
                                        {{ $blog->category->name??'' }}
                                    </td>
                                    <td>
                                        @php
                                            $status = $blog->status == 1 ? 'Active' : 'Inactive';
                                        @endphp
                                        {{ $status }}
                                    </td>
                                    <td class="Action">
                                        <span>
                                            <div class="action-btn me-2">
                                                <a href="{{ route('general.blogs.edit',$blog->id) }}" class="mx-3 btn btn-sm align-items-center bg-info" data-title="{{__('Edit')}}" data-bs-toggle="tooltip" title="{{__('Edit')}}" data-original-title="{{__('Edit')}}">
                                                    <i class="ti ti-pencil text-white"></i>
                                                </a>
                                            </div>
                                            <div class="action-btn ">
                                                {!! Form::open(['method' => 'DELETE', 'route' => ['general.blogs.destroy', $blog->id],'id'=>'delete-form-'.$blog->id]) !!}
                                                <a href="#" class="mx-3 btn btn-sm align-items-center bs-pass-para bg-danger" data-bs-toggle="tooltip" title="{{__('Delete')}}" data-original-title="{{__('Delete')}}" data-confirm="{{__('Are You Sure?').'|'.__('This action can not be undone. Do you want to continue?')}}" data-confirm-yes="document.getElementById('delete-form-{{$blog->id}}').submit();">
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
