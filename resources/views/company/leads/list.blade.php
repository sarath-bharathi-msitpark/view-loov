@extends('company.layouts.company')


@section('page-title')
    {{__('Manage Leads')}}
@endsection

@push('css-page')
    <link rel="stylesheet" href="{{asset('css/summernote/summernote-bs4.css')}}">
@endpush
@push('script-page')
    <script src="{{asset('css/summernote/summernote-bs4.js')}}"></script>
    <script>
        $(document).on("change", ".change-pipeline select[name=default_pipeline_id]", function () {
            $('#change-pipeline').submit();
        });
    </script>
@endpush

@section('page-icon')
    {{ asset('assets/assestsnew/Manage_projects.svg') }}
@endsection

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{route('dashboard')}}">{{__('Dashboard')}}</a></li>
    <li class="breadcrumb-item">{{__('Lead')}}</li>
@endsection
@section('action-btn')
    <div class="float-end d-flex topbar_search_section">
        <a href="{{ route('organization.leads.index') }}" data-bs-toggle="tooltip" title="{{__('Grid View')}}" class="rounded_add_btn me-1">
            <i class="ti ti-layout-grid text-primary"></i>
        </a>
        {{--<a href="#" data-size="md"  data-bs-toggle="tooltip" title="{{__('Import')}}" data-url="{{ route('leads.import') }}" data-ajax-popup="true" data-title="{{__('Import Lead CSV file')}}" class="btn btn-sm bg-brown-subtitle me-1">
            <i class="ti ti-file-import"></i>
        </a>
        <a href="{{route('leads.export')}}" data-bs-toggle="tooltip" title="{{__('Export')}}" class="btn btn-sm btn-secondary me-1">
            <i class="ti ti-file-export"></i>
        </a>--}}
        <a href="#" data-size="lg" data-url="{{ route('organization.leads.create') }}" data-ajax-popup="true" data-bs-toggle="tooltip" title="{{__('Create New Lead')}}" data-title="{{__('Create Lead')}}" class="rounded_add_btn">
            <i class="ti ti-plus text-primary"></i>
        </a>
    </div>
@endsection

@section('content')
    @include('company.layouts.partials.nav')
    @if($pipeline)
        <div class="row">
            <div class="col-xl-12">
                <div class="card">
                    <div class="card-body table-border-style">
                        <div class="table-responsive">
                            <table class="table datatable table-background">
                                <thead>
                                <tr>
                                    <th class="">{{__('Lead No')}}</th>
                                    <th class="">{{__('Client Name')}}</th>
                                    <th class="">{{__('Subject')}}</th>
                                    <th class="">{{__('Stage')}}</th>
                                    <th class="">{{__('Product')}}</th>
                                    <th class=" ">{{__('Users')}}</th>
                                    <th class=" icons-width px-4">{{__('Action')}}</th>
                                </tr>
                                </thead>
                                <tbody>
                                @if(count($leads) > 0)
                                    @foreach ($leads as $lead)
                                        @php
                                            $year = \Carbon\Carbon::parse($lead->date)->year;
                                            $pdfName = "Proposal-{$year}-{$lead->lead_no}";
                                        @endphp
                                            
                                        <tr class="bg-white">
                                            <td class=" ">{{ $pdfName }}</td>
                                            <td class=" ">{{ $lead->name }}</td>
                                            <td class=" ">{{ $lead->subject }}</td>
                                            <td class=" ">{{  !empty($lead->stage)?$lead->stage->name:'-' }}</td>
                                            <td class=" ">{{  $lead->products()->first()->name??'-' }}</td>
                                            <td class=" ">
                                                @foreach($lead->users as $user)
                                                    <?php
                                                        $gender = $user->employee?->gender;
                                                        $profile = \App\Models\Utility::get_file($user->avatar);
                                                        $defaultAvatar = asset('assets/assestsnew/menimg.png');
                                                
                                                        $avatar = match ($gender) {
                                                            GENDER_MALE   => asset('assets/assestsnew/menimg.png'),
                                                            GENDER_FEMALE => asset('assets/assestsnew/femaile-report.svg'),
                                                            default       => $user->avatar ? $profile : $defaultAvatar,
                                                        };
                                                    ?>
                                                
                                                    <a href="#" class="btn btn-sm mr-1 p-0 rounded-circle">
                                                        <img
                                                            src="{{ $avatar }}"
                                                            class="rounded-circle border shadow-sm me-1"
                                                            width="30"
                                                            height="30"
                                                            data-toggle="tooltip"
                                                            data-original-title="{{ $user->name }}"
                                                            title="{{ $user->name }}"
                                                            alt="{{ $user->name }}"
                                                        >
                                                    </a>
                                                @endforeach
                                            </td>
                                            @if(Auth::user()->type != 'client')
                                                <td class="Action ">
                                                    <span>
                                                    @can('crm')
                                                            @if($lead->is_active)
                                                                <div  class="action-btn ">
                                                                    <a href="{{ route('organization.leads.downloadPdf', $lead->id) }}"
                                                                       class="mx-3 btn btn-sm align-items-center padd-icons bg-success"
                                                                       target="_blank"
                                                                       data-bs-toggle="tooltip"
                                                                       title="{{ __('Download PDF') }}">
                                                                        <i class="ti ti-download text-white"></i>
                                                                    </a>
                                                                </div>
                                                            @endif
                                                        @endcan
                                                    @can('crm')
                                                            @if($lead->is_active)
                                                                <div class="action-btn ">
                                                                <a href="{{route('organization.leads.show',$lead->id)}}" class="mx-3 btn btn-sm align-items-center bg-warning padd-icons"  data-size="xl" data-bs-toggle="tooltip" title="{{__('View')}}" data-title="{{__('Lead Detail')}}">
                                                                    <i class="ti ti-eye text-white"></i>
                                                                </a>
                                                            </div>
                                                            @endif
                                                        @endcan
                                                        @can('crm')
                                                            <div class="action-btn">
                                                                <a href="#" class="mx-3 btn btn-sm align-items-center bg-info padd-icons" data-url="{{ route('organization.leads.edit',$lead->id) }}" data-ajax-popup="true" data-size="xl" data-bs-toggle="tooltip" title="{{__('Edit')}}" data-title="{{__('Lead Edit')}}">
                                                                    <i class="ti ti-pencil text-white"></i>
                                                                </a>
                                                            </div>
                                                        @endcan
                                                        @can('crm')
                                                            <div class="action-btn ">
                                                                {!! Form::open(['method' => 'DELETE', 'route' => ['organization.leads.destroy', $lead->id],'id'=>'delete-form-'.$lead->id]) !!}
                                                                <a href="#" class="mx-3 btn btn-sm  align-items-center bs-pass-para bg-danger padd-icons" data-bs-toggle="tooltip" title="{{__('Delete')}}" ><i class="ti ti-trash text-white"></i></a>

                                                                {!! Form::close() !!}
                                                             </div>

                                                        @endif
                                                    </span>
                                                </td>
                                            @endif
                                        </tr>
                                    @endforeach
                                @else
                                    <tr class="font-style">
                                        <td colspan="6" class="">{{ __('No data available in table') }}</td>
                                    </tr>
                                @endif

                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

@endsection
