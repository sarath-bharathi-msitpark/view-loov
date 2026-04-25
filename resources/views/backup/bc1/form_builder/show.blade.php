@extends('company.layouts.company')
@section('page-title')
    {{ $formBuilder->name.__("'s Form Field") }}
@endsection
@section('page-icon')
    {{ asset('assets/assestsnew/form-builder.png') }}
@endsection
@push('script-page')

@endpush
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{route('dashboard')}}">{{__('Dashboard')}}</a></li>
    <li class="breadcrumb-item"><a href="{{route('form_builder.index')}}">{{__('Form Builder')}}</a></li>
    <li class="breadcrumb-item">{{__('Add Field')}}</li>
@endsection
@section('action-btn')
    <div class="float-end">
        <a href="#" data-size="md" data-url="{{ route('form.field.create',$formBuilder->id) }}"
           data-ajax-popup="true" data-bs-toggle="tooltip" title="{{__('Create New Field')}}"
           class="btn btn-sm btn-primary">
            <i class="ti ti-plus"></i>
        </a>
    </div>
@endsection

@section('content')
    <div class="float-end">
        <a href="#" data-size="md" data-url="{{ route('form.field.create',$formBuilder->id) }}"
           data-ajax-popup="true" data-bs-toggle="tooltip" title="{{__('Create New Field')}}"
           class="btn btn-sm btn-primary">
            <i class="ti ti-plus"></i>
        </a>
    </div>

    <div class="row">
        <div class="col-xl-12">
            <div class="card">
                <div class="card-body table-border-style">
                    <div class="table-responsive mt-5">
                        <table class="table datatable">
                            <thead>
                            <tr>
                                <th>{{__('Name')}}</th>
                                <th>{{__('Type')}}</th>
                                <th class="text-end" width="200px">{{__('Action')}}</th>
                            </tr>
                            </thead>
                            <tbody>
                            @if($formBuilder->form_field->count())
                                @foreach ($formBuilder->form_field as $field)
                                    <tr>
                                        <td>{{ $field->name }}</td>
                                        <td>{{ ucfirst($field->type) }}</td>
                                        <td class="text-end">
                                            <div class="action-btn me-2">
                                                <a href="#" class="mx-3 btn btn-sm align-items-center bg-info"
                                                   data-url="{{ route('form.field.edit',[$formBuilder->id,$field->id]) }}"
                                                   data-ajax-popup="true" data-size="md" data-bs-toggle="tooltip"
                                                   title="{{__('Edit')}}" data-title="{{__('Edit Form Field')}}">
                                                    <i class="ti ti-pencil text-white"></i>
                                                </a>
                                            </div>
                                            <div class="action-btn ">
                                                {!! Form::open(['method' => 'DELETE', 'route' => ['form.field.destroy', [$formBuilder->id,$field->id]]]) !!}
                                                <a href="#"
                                                   class="mx-3 btn btn-sm  align-items-center bs-pass-para bg-danger"
                                                   data-bs-toggle="tooltip" title="{{__('Delete')}}"><i
                                                        class="ti ti-trash text-white"></i></a>
                                                {!! Form::close() !!}
                                            </div>

                                        </td>
                                    </tr>
                                @endforeach
                            @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
