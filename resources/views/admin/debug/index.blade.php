@extends('admin.layouts.admin')
@section('page-title')
    {{__('Debug Mode')}}
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

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        $(document).on('change', '.toggle-debug', function (e) {
            e.preventDefault();

            let checkbox = $(this);
            let userId = checkbox.data('id');
            let isDebug = checkbox.is(':checked') ? 1 : 0;

            checkbox.prop('checked', !checkbox.is(':checked'));

            Swal.fire({
                title: 'Are you sure?',
                text: "Do you want to " + (isDebug ? "ENABLE" : "DISABLE") + " debug mode?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, update it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "{{ url('admin/debug-user-status-update') }}/" + userId,
                        type: "POST",
                        data: {
                            _token: "{{ csrf_token() }}",
                            id: userId,
                            is_debug_mode: isDebug
                        },
                        success: function (res) {
                            checkbox.prop('checked', isDebug);
                            Swal.fire(
                                'Updated!',
                                'Debug mode has been ' + (isDebug ? 'enabled' : 'disabled') + '.',
                                'success'
                            );
                        },
                        error: function (err) {
                            Swal.fire(
                                'Error!',
                                'Something went wrong. Please try again.',
                                'error'
                            );
                        }
                    });
                }
            });
        });
    </script>
@endpush

@section('action-btn')

@endsection

@section('breadcrumb')
    <li class="breadcrumb-item">
        <a href="">{{ __('Dashboard') }}</a>
    </li>
        <li class="breadcrumb-item">{{ __('Companies') }}</li>
   
        <li class="breadcrumb-item">{{ __('Debug') }}</li>
   
@endsection

@section('content')
    @include('admin.layouts.partials.nav')

    <style>
        .switch {
            position: relative;
            display: inline-block;
            width: 46px;
            height: 24px;
        }

        .switch input {
            display: none;
        }

        .slider {
            position: absolute;
            cursor: pointer;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: #ccc;
            transition: .4s;
            border-radius: 24px;
        }

        .slider:before {
            position: absolute;
            content: "";
            height: 18px;
            width: 18px;
            left: 3px;
            bottom: 3px;
            background-color: white;
            transition: .4s;
            border-radius: 50%;
        }

        input:checked + .slider {
            background-color: blue;
        }

        input:checked + .slider:before {
            transform: translateX(22px);
        }
    </style>

    <div class="row mt-5">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body table-border-style">
                    <div class="table-responsive company_order_table">
                        <table class="table datatable">
                            <thead class="text-secondary">
                            <tr>
                                <th style="position: unset; background-color: transparent">{{__('S.NO') }}</th>
                                <th style="position: unset; background-color: transparent">{{__('Name') }}</th>
                                <th> {{__('Email')}}</th>
                                <th> {{__('Contact')}}</th>
                                <th width="10%"> {{__('Action')}}</th>
                            </tr>
                            </thead>
                            <tbody>
                            @forelse($activeUsers as $index => $activeUser)
                                <tr>
                                    <td class="tex_fix" style="position: unset;">
                                        {{ $index + 1 }}
                                    </td>
                                    <td class="tex_fix" style="position: unset;">
                                        {{ $activeUser->name }}
                                    </td>
                                    <td>{{ $activeUser->email }}</td>
                                    <td>{{ $activeUser->mobile_no }}</td>
                                    <td>
                                        <label class="switch">
                                            <input type="checkbox" class="toggle-debug"
                                                   data-id="{{ $activeUser->id }}"
                                                {{ $activeUser->is_debug_mode ? 'checked' : '' }}>
                                            <span class="slider round"></span>
                                        </label>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <th class="text-center" colspan="5">No Data Found</th>
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
