@extends('company.layouts.company')

@section('page-title')
    {{__('Manage Projects')}}
@endsection

@section('page-icon')
    {{ asset('assets/assestsnew/Manage_projects.svg') }}
@endsection

@push('script-page')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/owl.carousel.min.js"></script>
@endpush

@section('action-btn')
    <div class="float-end d-flex topbar_search_section">
        <div class="custom_search_wrapper">
            <input
                class="search_input_payment"
                type="search"
                name="search"
                placeholder="Search Manager Name"
                aria-label="Search"
            />
            <i class="fa-solid fa-magnifying-glass search_icon_payment"></i>
        </div>

        @if($view == 'grid')
            <a href="{{ route('organization.projects.list','list') }}" data-bs-toggle="tooltip"
               title="{{__('List View')}}"
               class="rounded_add_btn me-1">
                <i class="ti ti-list text-primary"></i>
            </a>
        @else
            <a href="{{ route('organization.projects.index') }}" data-bs-toggle="tooltip" title="{{__('Grid View')}}"
               class="rounded_add_btn me-1">
                <i class="ti ti-layout-grid text-primary"></i>
            </a>
        @endif

        {{------------ Start Filter ----------------}}
        <a href="#" class="rounded_add_btn me-1" role="button" data-bs-toggle="dropdown"
           aria-haspopup="true" aria-expanded="false" data-bs-toggle="tooltip" title="{{ __('Filter') }}"
           data-original-title="{{ __('Filter') }}">
            <i class="ti ti-filter text-primary"></i>
        </a>
        <div class="dropdown-menu  dropdown-steady" id="project_sort">
            <a class="dropdown-item active" href="#" data-val="created_at-desc">
                <i class="ti ti-sort-descending me-2 text-black"></i>{{__('Newest')}}
            </a>
            <a class="dropdown-item" href="#" data-val="created_at-asc">
                <i class="ti ti-sort-ascending me-2 text-black"></i>{{__('Oldest')}}
            </a>

            <a class="dropdown-item" href="#" data-val="project_name-desc">
                <i class="ti ti-sort-descending-letters me-2 text-black"></i>{{__('From Z-A')}}
            </a>
            <a class="dropdown-item" href="#" data-val="project_name-asc">
                <i class="ti ti-sort-ascending-letters me-2 text-black"></i>{{__('From A-Z')}}
            </a>
        </div>

        {{------------ End Filter ----------------}}

        {{------------ Start Status Filter ----------------}}
        <a href="#" class="btn_for_status me-1" role="button"
           data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            <span class="btn-inner--icon">{{__('Status')}}</span>
        </a>
        <div class="dropdown-menu  project-filter-actions dropdown-steady" id="project_status">
            <a class="dropdown-item filter-action filter-show-all pl-4 active" href="#">{{__('Show All')}}</a>
            @foreach(\App\Models\Project::$project_status as $key => $val)
                <a class="dropdown-item filter-action pl-4" href="#" data-val="{{ $key }}">{{__($val)}}</a>
            @endforeach
        </div>
        {{------------ End Status Filter ----------------}}

        @can('project_management')
            <a href="#" data-size="lg" data-url="{{ route('organization.projects.create') }}" data-ajax-popup="true"
               data-bs-toggle="tooltip" title="{{__('Create New Project')}}" data-title="{{__('Create Project')}}"
               class="rounded_add_btn">
                <i class="ti ti-plus text-primary"></i>
            </a>
        @endcan
    </div>
@endsection

@section('content')
    @include('company.layouts.partials.nav')
    <div class="row mt-5" id="project_view"></div>
@endsection

@push('script-page')
    <script>
        $(document).ready(function () {
            var sort = 'created_at-desc';
            var status = '';
            ajaxFilterProjectView('created_at-desc');
            $(".project-filter-actions").on('click', '.filter-action', function (e) {
                if ($(this).hasClass('filter-show-all')) {
                    $('.filter-action').removeClass('active');
                    $(this).addClass('active');
                } else {
                    $('.filter-show-all').removeClass('active');
                    if ($(this).hasClass('active')) {
                        $(this).removeClass('active');
                        $(this).blur();
                    } else {
                        $(this).addClass('active');
                    }
                }

                var filterArray = [];
                var url = $(this).parents('.project-filter-actions').attr('data-url');
                $('div.project-filter-actions').find('.active').each(function () {
                    filterArray.push($(this).attr('data-val'));
                });

                status = filterArray;

                ajaxFilterProjectView(sort, $('#project_keyword').val(), status);
            });

            // when change sorting order
            $('#project_sort').on('click', 'a', function () {
                sort = $(this).attr('data-val');
                ajaxFilterProjectView(sort, $('#project_keyword').val(), status);
                $('#project_sort a').removeClass('active');
                $(this).addClass('active');
            });

            // when searching by project name
            $(document).on('keyup', '#project_keyword', function () {
                ajaxFilterProjectView(sort, $(this).val(), status);
            });

            $(document).on('click', '.invite_usr', function () {
                var project_id = $('#project_id').val();
                var user_id = $(this).attr('data-id');

                $.ajax({
                    url: '{{ route('organization.invite.project.user.member') }}',
                    method: 'POST',
                    dataType: 'json',
                    data: {
                        'project_id': project_id,
                        'user_id': user_id,
                        "_token": "{{ csrf_token() }}"
                    },
                    success: function (data) {
                        if (data.code == '200') {
                            show_toastr(data.status, data.success, 'success')
                            setInterval('location.reload()', 5000);
                        } else if (data.code == '404') {
                            show_toastr(data.status, data.errors, 'error')
                        }
                    }
                });
            });
        });

        var currentRequest = null;

        function ajaxFilterProjectView(project_sort, keyword = '', status = '') {
            var mainEle = $('#project_view');
            var view = '{{$view}}';
            var data = {
                view: view,
                sort: project_sort,
                keyword: keyword,
                status: status,
            }

            currentRequest = $.ajax({
                url: '{{ route('organization.filter.project.view') }}',
                data: data,
                beforeSend: function () {
                    if (currentRequest != null) {
                        currentRequest.abort();
                    }
                },
                success: function (data) {
                    mainEle.html(data.html);
                    $('[id^=fire-modal]').remove();
                    loadConfirm();
                }
            });
        }
    </script>
@endpush
