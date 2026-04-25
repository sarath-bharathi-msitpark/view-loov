@extends('field_track.layouts.fieldTrack')

@section('page-title')
    {{ __('Track Attendance') }}
@endsection

@section('page-icon')
    {{ asset('assets/assestsnew/field_live_location.svg') }}
@endsection

@push('css-page')
    <link rel="stylesheet" href="{{ asset('assets/tracking/css/tracking_custom.css') }}">
@endpush

@push('script-page')
    <script>
        $('input[name="type"]:radio').on('change', function (e) {
            var type = $(this).val();

            if (type == 'monthly') {
                $('.month').addClass('d-block');
                $('.month').removeClass('d-none');
                $('.date').addClass('d-none');
                $('.date').removeClass('d-block');
            } else {
                $('.date').addClass('d-block');
                $('.date').removeClass('d-none');
                $('.month').addClass('d-none');
                $('.month').removeClass('d-block');
            }
        });

        $('input[name="type"]:radio:checked').trigger('change');

    </script>
    <script>
        document.getElementById('perPage').addEventListener('change', function () {
            document.getElementById('perPageForm').submit();
        });
          $(document).ready(function () {
            $(' #month_input,select[name="branch"],select[name="department"],select[name="designation"], select[name="employee"]').on('change', function () {
                $('#attendanceemployee_filter').submit();
            });
        });
    </script>

    <script>
        const attendanceForm = document.getElementById('attendanceForm');
        const input = $('input[name="date"]');
        let initialDate = input.val();

        // Default to last 7 days
        let start = moment().subtract(6, 'days');
        let end = moment();

        // If user-selected date exists, parse it
        if (initialDate.includes(' to ')) {
            let dates = initialDate.split(' to ');
            start = moment(dates[0], 'YYYY-MM-DD');
            end = moment(dates[1], 'YYYY-MM-DD');
        } else if (initialDate !== '') {
            start = end = moment(initialDate, 'YYYY-MM-DD');
        }

        input.daterangepicker({
            autoUpdateInput: true,
            locale: {
                cancelLabel: 'Clear',
                format: 'YYYY-MM-DD'
            },
            startDate: start,
            endDate: end,
            ranges: {
                'Today': [moment(), moment()],
                'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                'This Month': [moment().startOf('month'), moment().endOf('month')],
                'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
            }
        }, function (start, end) {
            let formattedDate = start.format('YYYY-MM-DD');
            if (start.format('YYYY-MM-DD') !== end.format('YYYY-MM-DD')) {
                formattedDate += ' to ' + end.format('YYYY-MM-DD');
            }
            input.val(formattedDate);
            attendanceForm.submit();
        });

        input.on('cancel.daterangepicker', function () {
            $(this).val('');
            attendanceForm.submit();
        });
    </script>
@endpush


@section('content')
    @include('field_track.layouts.partials.nav')

    <div class="row mt-4">
        <form method="GET" action="{{ route('fieldTrack.attendanceemployee.index') }}"
              class="d-flex justify-content-between" id="attendanceForm">
            <div class="selecters_head">
                <div class="d-flex gap-3">
                    @if(\Auth::user()->type != 'Employee')
                        <select name="employee" id="employee-id" class="form-control select2 mb-3" onchange="this.form.submit()">
                            @foreach($employees as $key => $employee)
                                <option value="{{ $key }}" {{ request('employee') == $key ? 'selected' : '' }}>
                                    {{ $employee }}
                                </option>
                            @endforeach
                        </select>
                    @endif
                </div>
            </div>

            <div class="selecters_head">
                <div class="justify-content-lg-end gap-lg-3 gap-3 d-flex">
                    <input type="text" name="date" class="form-control"
                           value="{{ request('date', now()->toDateString()) }}" autocomplete="off">

                    <button type="submit" name="download" value="excel" class="download_arrbtn">
                        <i class="fas fa-download"></i>
                    </button>

                    <span class="px-0"><a href="{{ route('fieldTrack.attendanceemployee.index') }}"
                                          class="download_arrbtn"><i
                                class="fas fa-redo-alt" style="margin:12px"></i></a>Clear Filter</span>
                </div>
            </div>
        </form>
    </div>

    <div class="row mt-5 px-lg-4 p-md-3 p-1">
        <div class="col-md-12 rounded-3">
            <div class="main-content">
                <div class="attendance-table-outer">
                    <table class="attendance-table">
                        <thead>
                        <tr>
                            <th>{{__('Employee')}}</th>
                            <th>{{__('Date')}}</th>
                            <th width="20%">{{__('Punched In')}}</th>
                            <th width="20%">{{__('Punched Out')}}</th>
                            <th>{{__('Total KM Travelled')}}</th>
                            <th>{{__('Status')}}</th>
                            @if(Gate::check('action attendance'))
                                @if(\Auth::user()->type == "company")
                                    <th>{{__('Action')}}</th>
                                @endif
                            @endif
                        </tr>
                        </thead>

                        <tbody>
                        @forelse ($attendanceEmployee as $attendance)
                            <tr>
                                <td class="tex_fix">
                                    <div class="d-flex align-items-center">
                                    <div class="emp-avatar">
                                        @php
                                            $gender = $attendance['employee']['gender'] ?? null;
                                        @endphp

                                        @if($gender === GENDER_MALE)
                                            <img src="{{ asset('assets/assestsnew/menimg.png') }}" alt="Male">
                                        @else
                                            <img src="{{ asset('assets/assestsnew/femaile-report.svg') }}" alt="Female">
                                        @endif
                                    </div>
                                    <span class="emp-name">{{ $attendance['employee']->user->name ?? 'N/A' }}</span>
                                    </div>
                                </td>
                                <td>{{ \Auth::user()->dateFormat($attendance->date) }}</td>
                                <td class="text-wrap">
                                    {{ ($attendance->clock_in !='00:00:00') ?\Auth::user()->timeFormat($attendance->clock_in):'00:00' }}
                                    <br><b>ODO: </b>{{ $attendance->start_ride }}
                                    <br><b>Location: </b><br><p>{{ $attendance->clock_in_location }}</p>
                                    <br>
                                  @php
                                    $clockInImages = json_decode($attendance->clock_in_images);
                                @endphp

                                @if(is_array($clockInImages))
                                    @foreach($clockInImages as $img)
                                        <a target="_blank" href="{{ \App\Models\Utility::get_file($img) }}">Proof</a>
                                    @endforeach
                                @endif
                                </td>
                                <td class="text-wrap">
                                    {{ ($attendance->clock_out !='00:00:00') ?\Auth::user()->timeFormat( $attendance->clock_out):'00:00' }}
                                    @if($attendance->clock_out !='00:00:00')
                                        <br><b>ODO: </b>{{ $attendance->end_ride }}
                                        <br><b>Location: </b><br><p>{{ $attendance->clock_out_location }}</p>
                                        @if($attendance->clock_out_images != "")
                                            @foreach(json_decode($attendance->clock_out_images) as $img)
                                                <a target="_blank" href="{{\App\Models\Utility::get_file($img) }}">Proof</a>
                                            @endforeach
                                        @endif
                                    @endif
                                </td>
                                <td>{{ max(0, $attendance->total_ride) }}</td>
                                <td>{{ $attendance->status }}</td>
                                @if(Gate::check('action attendance'))
                                    @if(\Auth::user()->type == "company")
                                        <td>
                                            <div class="action-btn me-2">
                                                <a href="#" data-url="{{ URL::to('attendanceemployee/'.$attendance->id.'/edit') }}" data-size="lg" data-ajax-popup="true" data-title="{{__('Edit Attendance')}}" class="mx-3 btn btn-sm align-items-center bg-info" data-bs-toggle="tooltip" title="{{__('Edit')}}" data-original-title="{{__('Edit')}}">
                                                    <i class="ti ti-pencil text-white"></i></a>
                                            </div>
                                        </td>
                                    @endif
                                @endif
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10">
                                    <div class="row justify-content-center text-center">
                                        <img class="w-25" src="{{ asset('assets/assestsnew/no_datasvg.svg') }}"
                                             alt="">
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>


                <div class="row mt-3 justify-content-center">
                    <div class="col-12 optional_inputpagi">
                        <div class="row align-items-start justify-content-center">

                            <!-- Left Spacer -->
                            <div class="col-12 col-md-3 mb-3 mb-md-0">
                                <div class="data_table_select">
                                    <form id="perPageForm" method="GET" action="{{ url()->current() }}"
                                          class="d-flex align-items-center gap-2 m-0">
                                        {{-- Preserve filters --}}
                                        @foreach(request()->except(['page', 'per_page']) as $key => $value)
                                            <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                                        @endforeach

                                        <label for="per_page" class="small mb-0 text-nowrap">Items per page:</label>

                                        <select name="per_page" id="per_page"
                                                class="form-select form-select-sm"
                                                style="width: 90px; min-width: 80px;"
                                                onchange="document.getElementById('perPageForm').submit()">
                                            @foreach([5, 10, 20, 50] as $size)
                                                <option
                                                    value="{{ $size }}" {{ request('per_page', 10) == $size ? 'selected' : '' }}>
                                                    {{ $size }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </form>
                                </div>
                            </div>

                            <!-- Pagination Numbers -->
                            <div class="col-12 col-md-auto mb-3 mb-md-0">
                                <div class="d-flex justify-content-center">
                                    <ul class="paginatio_ulist d-flex align-items-center gap-lg-4 gap-2 m-0 p-0">
                                        {{-- First Page --}}
                                        <li class="{{ $attendanceEmployee->onFirstPage() ? 'disabled' : '' }}">
                                            <a href="{{ $attendanceEmployee->url(1) }}" class="page-link1">&#171;</a>
                                        </li>

                                        {{-- Previous Page --}}
                                        <li class="{{ $attendanceEmployee->onFirstPage() ? 'disabled' : '' }}">
                                            <a href="{{ $attendanceEmployee->previousPageUrl() }}" class="page-link1">
                                                <i class="fa-solid fa-chevron-left"></i>
                                            </a>
                                        </li>

                                        {{-- Page Numbers --}}
                                        @php
                                            $start = max(1, $attendanceEmployee->currentPage() - 2);
                                            $end = min($start + 4, $attendanceEmployee->lastPage());
                                        @endphp
                                        @for ($i = $start; $i <= $end; $i++)
                                            <li class="{{ $attendanceEmployee->currentPage() == $i ? 'active_pagination' : '' }}">
                                                <a href="{{ $attendanceEmployee->url($i) }}" class="page-link1">{{ $i }}</a>
                                            </li>
                                        @endfor

                                        {{-- Next Page --}}
                                        <li class="{{ !$attendanceEmployee->hasMorePages() ? 'disabled' : '' }}">
                                            <a href="{{ $attendanceEmployee->nextPageUrl() }}" class="page-link1">
                                                <i class="fa-solid fa-chevron-right"></i>
                                            </a>
                                        </li>

                                        {{-- Last Page --}}
                                        <li class="{{ !$attendanceEmployee->hasMorePages() ? 'disabled' : '' }}">
                                            <a href="{{ $attendanceEmployee->url($attendanceEmployee->lastPage()) }}" class="page-link1">&#187;</a>
                                        </li>
                                    </ul>
                                </div>
                            </div>

                            <!-- Page Jump Input -->
                            <div class="col-12 col-lg-3 col-md-4 mb-3 mb-md-0">
                                <div
                                    class="d-flex  flex-md-row align-items-center justify-content-center justify-content-md-start gap-2">
                                    <form action="{{ url()->current() }}" method="GET"
                                          class="d-flex align-items-center gap-2"
                                          style="flex-direction:row !important;">
                                        {{-- Preserve filters --}}
                                        @foreach(request()->except('page') as $key => $value)
                                            <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                                        @endforeach

                                        <input type="number" name="page" min="1" max="{{ $attendanceEmployee->lastPage() }}"
                                               class="form-control form-control-sm" style="width: 80px;"
                                               placeholder="Page">
                                        <button class="btn btn-sm btn-primary" type="submit">Go</button>
                                    </form>
                                    <span class="text-nowrap small text-center text-md-start">of {{ $attendanceEmployee->total() }} Data</span>
                                </div>
                            </div>

                            <!-- Showing Range -->
                            <div class="col-12 mt-3">
                                <div class="d-flex justify-content-center">
                                    <span>{{ $attendanceEmployee->firstItem() }} to {{ $attendanceEmployee->lastItem() }} of {{ $attendanceEmployee->total() }} Data</span>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>


        </div>
    </div>
@endsection
