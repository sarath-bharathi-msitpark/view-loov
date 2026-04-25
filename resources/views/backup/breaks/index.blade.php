@extends('layouts.admin')
@section('page-title')
    {{ __('Manage Breaks') }}
@endsection

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></li>
    <li class="breadcrumb-item">{{ __('Breaks') }}</li>
@endsection

@section('content')
<div class="row">
    <!--<div class="col-12">-->
    <!--    @include('layouts.hrm_setup')-->
    <!--</div>-->

    <div class="col-12">
        <div class="my-3 d-flex justify-content-end">
          
                <a href="#" data-url="{{ route('breaks.create') }}" data-ajax-popup="true"
                    data-title="{{ __('Create New Break') }}" data-bs-toggle="tooltip" title="{{ __('Create') }}"
                    class="btn btn-sm btn-primary">
                    <i class="ti ti-plus"></i>
                </a>
        
        </div>

        <div class="card">
            <div class="card-body table-border-style">
                <div class="table-responsive">
                    <table class="table datatable">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>{{ __('Break Name') }}</th>
                                <th>{{ __('Max Break Time (mins)') }}</th>
                                <th>{{ __('Status') }}</th>
                                <th width="200px">{{ __('Action') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($breaks as $break)
                                <tr>
                                    <td>{{ $break->id }}</td>
                                    <td>{{ $break->break_name }}</td>
                                    <td>{{ $break->maximum_break_time }}</td>
                                    <td>
                                        @if($break->status)
                                            <span class="badge bg-success">{{ __('Active') }}</span>
                                        @else
                                            <span class="badge bg-danger">{{ __('Inactive') }}</span>
                                        @endif
                                    </td>
                                    <td class="Action">
                                            <a href="#" class="btn btn-sm bg-info text-white"
                                                data-url="{{ route('breaks.edit', $break->id) }}"
                                                data-ajax-popup="true" data-title="{{ __('Edit Break') }}"
                                                data-bs-toggle="tooltip" title="{{ __('Edit') }}">
                                                <i class="ti ti-pencil"></i>
                                            </a>

                                            {!! Form::open([
                                                'method' => 'DELETE',
                                                'route' => ['breaks.destroy', $break->id],
                                                'id' => 'delete-form-' . $break->id,
                                                'style' => 'display:inline-block;',
                                            ]) !!}
                                            <a href="#" class="btn btn-sm bg-danger text-white"
                                                data-bs-toggle="tooltip"
                                                data-confirm="{{ __('Are You Sure?') . '|' . __('This action can not be undone. Do you want to continue?') }}"
                                                data-confirm-yes="document.getElementById('delete-form-{{ $break->id }}').submit();"
                                                title="{{ __('Delete') }}">
                                                <i class="ti ti-trash"></i>
                                            </a>
                                            {!! Form::close() !!}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5">{{ __('No breaks found.') }}</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection
