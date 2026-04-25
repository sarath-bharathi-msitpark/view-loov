@extends('company.layouts.company')
@section('page-title')
    {{__('Manage Bug Report')}}
@endsection
@section('page-icon')
    {{ asset('assets/assestsnew/project_bug1.svg') }}
@endsection

@push('script-page')

@endpush
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{route('organization.projects.index')}}">{{__('Project')}}</a></li>
    <li class="breadcrumb-item"><a
            href="{{route('organization.projects.show',$project->id)}}">{{ucwords($project->project_name)}}</a></li>
    <li class="breadcrumb-item">{{__('Bug Report')}}</li>
@endsection
@section('action-btn')
    <div class="float-end d-flex">
        <a href="{{ route('organization.task.bug.kanban',$project->id) }}" data-bs-toggle="tooltip"
           title="{{__('Grid View')}}"
           class="rounded_add_btn me-1">
            <i class="ti ti-layout-grid text-primary"></i>
        </a>
        <a href="#" data-size="lg" data-url="{{ route('organization.task.bug.create',$project->id) }}"
           data-ajax-popup="true"
           data-bs-toggle="tooltip" title="{{__('Create New Bug')}}" class="rounded_add_btn">
            <i class="ti ti-plus text-primary"></i>
        </a>
    </div>
@endsection

@section('content')
    @include('company.layouts.partials.nav')

    <div class="row pt-5">
        <div class="col-xl-12">
            <div class="card">
                <div class="card-body table-border-style">
                    <div class="table-responsive">
                        <table class="table attendance-table">
                            <thead>
                            <tr>
                                <th> {{__('Bug Id')}}</th>
                                <th> {{__('Assign To')}}</th>
                                <th> {{__('Bug Title')}}</th>
                                <th> {{__('Start Date')}}</th>
                                <th> {{__('Due Date')}}</th>
                                <th> {{__('Status')}}</th>
                                <th> {{__('Priority')}}</th>
                                <th> {{__('Created By')}}</th>
                                <th width="10%"> {{__('Action')}}</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach ($bugs as $bug)
                                <tr>
                                    <td class="tex_fix">{{ \Auth::user()->bugNumberFormat($bug->bug_id)}}</td>
                                    <td>{{ (!empty($bug->assignTo)?$bug->assignTo->name:'') }}</td>
                                    <td>{{ $bug->title}}</td>
                                    <td>{{ Auth::user()->dateFormat($bug->start_date) }}</td>
                                    <td>{{ Auth::user()->dateFormat($bug->due_date) }}</td>
                                    <td>{{ (!empty($bug->bug_status)?$bug->bug_status->title:'') }}</td>
                                    <td>{{ $bug->priority }}</td>
                                    <td>{{ $bug->createdBy->name }}</td>
                                    <td class="Action" width="10%">
                                        <div class="action-btn me-2">
                                            <a href="#" data-size="lg"
                                               class="copy_com align-items-center"
                                               data-url="{{ route('organization.task.bug.edit',[$project->id,$bug->id]) }}"
                                               data-ajax-popup="true" data-size="xl" data-bs-toggle="tooltip"
                                               title="{{__('Edit')}}" data-title="{{__('Edit Bug')}}">
                                                <i class="fas fa-edit text-primary fs-6" title="Edit"></i>
                                            </a>
                                        </div>

                                        <div class="action-btn ">
                                            {!! Form::open(['method' => 'DELETE', 'route' => ['organization.task.bug.destroy', $project->id,$bug->id]]) !!}
                                            <a href="#"
                                               class="copy_com align-items-center bs-pass-para"
                                               data-bs-toggle="tooltip" title="{{__('Delete')}}"><i
                                                    class="fa-solid fa-trash fs-5 text-danger"
                                                    title="Delete"></i></a>
                                            {!! Form::close() !!}
                                        </div>
                                    </td>
                                </tr>
                            @endforeach

                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <!-- Pagination Links -->
            <div class="row mt-3 justify-content-center">
                <div class="col-12 optional_inputpagi">
                    <div class="row align-items-center justify-content-center">

                        <!-- Left Spacer -->
                        <div class="col-12 col-md-3 mb-3 mb-md-0">
                            <div class="data_table_select">
                                <small>items per page</small>
                                <select>
                                    <option>5</option>
                                    <option>10</option>
                                    <option>20</option>
                                    <option>50</option>
                                </select>
                            </div>
                        </div>

                        <!-- Pagination Numbers -->
                        <div class="col-12 col-md-auto mb-3 mb-md-0">
                            <div class="d-flex justify-content-center">
                                <ul class="paginatio_ulist d-flex align-items-center gap-lg-4 gap-2 m-0 p-0">
                                    {{-- First Page --}}
                                    <li class="disabled">
                                        <a href="#" class="page-link1">&#171;</a>
                                    </li>

                                    {{-- Previous Page --}}
                                    <li class="disabled">
                                        <a href="#" class="page-link1">
                                            <i class="fa-solid fa-chevron-left"></i>
                                        </a>
                                    </li>

                                    {{-- Page Numbers --}}

                                    <li class="active_pagination">
                                        <a href="#"
                                           class="page-link1">1</a>
                                    </li>


                                    {{-- Next Page --}}
                                    <li class="disabled">
                                        <a href="#" class="page-link1">
                                            <i class="fa-solid fa-chevron-right"></i>
                                        </a>
                                    </li>

                                    {{-- Last Page --}}
                                    <li class="disabled">
                                        <a href="#"
                                           class="page-link1">&#187;</a>
                                    </li>
                                </ul>
                            </div>
                        </div>

                        <!-- Page Jump Input -->
                        <div class="col-12 col-lg-3 col-md-4 mb-3 mb-md-0">
                            <div
                                class="d-flex flex-md-row align-items-center justify-content-center justify-content-md-start gap-2">
                                <form
                                    class="d-flex align-items-center gap-2"
                                    style="flex-direction:row !important;">
                                    {{-- Preserve filters --}}

                                    <input type="hidden">


                                    <input type="number" name="page" min="1"
                                           class="form-control form-control-sm" style="width: 80px;"
                                           placeholder="Page">
                                    <button class="btn btn-sm btn-primary" type="button">Go</button>
                                </form>
                                <span class="text-nowrap small text-center text-md-start">of 1 Data</span>
                            </div>
                        </div>

                        <!-- Showing Range -->
                        <div class="col-12 mt-3">
                            <div class="d-flex justify-content-center">
                                <span>to 5 Data</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
