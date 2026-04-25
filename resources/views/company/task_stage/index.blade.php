@extends('company.layouts.company')
@push('script-page')
    <script src="{{asset('js/jquery-ui.min.js')}}"></script>
    @if(\Auth::user()->type=='company')
        <script>
            $(function () {
                $(".sortable").sortable();
                $(".sortable").disableSelection();
                $(".sortable").sortable({
                    stop: function () {
                        var order = [];
                        $(this).find('li').each(function (index, data) {
                            order[index] = $(data).attr('data-id');
                        });

                        $.ajax({
                            url: "{{route('project-task-stages.order')}}",
                            data: {order: order, _token: $('meta[name="csrf-token"]').attr('content')},
                            type: 'POST',
                            success: function (data) {
                            },
                            error: function (data) {
                                data = data.responseJSON;
                                toastr('Error', data.error, 'error')
                            }
                        })
                    }
                });
            });
        </script>
    @endif
@endpush
@section('page-title')
    {{__('Manage Project Task Stages')}}
@endsection
@section('page-icon')
    {{ asset('assets/assestsnew/project_system_setup.svg') }}
@endsection
@section('action-btn')
    <div class="float-end">

        <a href="#" data-url="{{ route('project-task-stages.create') }}" data-bs-toggle="tooltip"
           title="{{__('Create')}}" class="rounded_add_btn" data-ajax-popup="true"
           data-title="{{__('Create Project Task Stage')}}">
            <i class="ti ti-plus text-primary"></i>
        </a>
    </div>
@endsection

@section('content')
    @include('company.layouts.partials.nav')
    <div class="row justify-content-center">
        <div class="col-sm-12 col-md-10 col-xxl-8">

            <div class="card mt-5 padd_mobilezero">
                <div class="card-body">
                    <div class="tab-content" id="pills-tabContent">
                        @php($i=0)
                        @foreach ($task_stages as $key => $task_stage)

                            <div class="tab-pane fade show  @if($i==0) active @endif" role="tabpanel">
                                <ul class="list-unstyled list-group sortable stage">
                                    @foreach ($task_stages as $task_stage)
                                        <li class="d-flex align-items-center justify-content-between list-group-item mb-3"
                                            data-id="{{$task_stage->id}}">
                                            <h6 class="mb-0">
                                                <i class="me-3 ti ti-arrows-maximize " data-feather="move"></i>
                                                <span>{{$task_stage->name}}</span>
                                            </h6>
                                            <span class="float-end">

                                                    <div class="action-btn me-2">
                                                        <a href="#"
                                                           data-url="{{ URL::to('project-task-stages/'.$task_stage->id.'/edit') }}"
                                                           data-ajax-popup="true" data-bs-toggle="tooltip"
                                                           title="{{__('Edit')}}" data-title="{{__('Edit Bug Status')}}"
                                                           class="copy_com align-items-center">
                                                          <i class="ti ti-pencil text-primary"></i>
                                                      </a>
                                                    </div>

                                                    <div class="action-btn ">
                                                            {!! Form::open(['method' => 'DELETE', 'route' => ['project-task-stages.destroy', $task_stage->id],'id'=>'delete-form-'.$task_stage->id]) !!}
                                                              <a href="#!"
                                                                 class="copy_com align-items-center bs-pass-para"
                                                                 data-bs-toggle="tooltip" title="{{__('Delete')}}"
                                                                 data-original-title="{{__('Delete')}}"
                                                                 data-confirm="Are You Sure?|This action can not be undone. Do you want to continue?"
                                                                 data-confirm-yes="document.getElementById('delete-form-{{$task_stage->id}}').submit();">
                                                                    <i class="ti ti-trash text-danger"></i>
                                                              </a>
                                                            {!! Form::close() !!}
                                                        </div>
                                            </span>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                            @php($i++)
                        @endforeach
                    </div>
                    <p class=" mt-4"><strong>{{__('Note')}}
                            : </strong><b>{{__('You can easily change order of project task stage using drag & drop.')}}</b>
                    </p>

                </div>
            </div>

        </div>
    </div>
@endsection
