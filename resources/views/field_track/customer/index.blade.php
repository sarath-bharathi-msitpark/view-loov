@extends('field_track.layouts.fieldTrack')

@section('page-title')
    {{ __('Customers') }}
@endsection

@section('page-icon')
    {{ asset('assets/assestsnew/field_coustomer.svg') }}
@endsection

@section('content')
    @include('field_track.layouts.partials.nav')

    <div class="col-12 entire_box1 mb-5">
        <div class="row g-3 mt-3 align-items-end">
            <form action="{{ route('fieldTrack.customer.index') }}" method="GET" class="row g-3 mt-3 align-items-end"
                  id="filterForm">

                <!-- Search -->
                <div class="col-12 col-lg-12 d-flex flex-wrap align-items-center gap-3">
                    <div class="d-flex w-100 search_maincontain" style="flex-direction: row !important;">
                        <input type="text" name="search" placeholder="Search"
                               value="{{ request('search') }}" class="form-control">
                        <button type="submit" class="btn btn-primary ms-2 p-0"><i
                                class="fa-solid fa-magnifying-glass"></i>
                        </button>
                    </div>
                </div>

                <!-- Country -->
                <div class="col-12 col-sm-6 col-md-6 col-lg-4">
                    <label for="country" class="form-label fw-semibold">Country</label>
                    <select name="country" id="country" class="form-select select2">
                        <option value="">Select Country</option>
                        @foreach($countries as $country)
                            <option
                                value="{{ $country->id }}" {{ request('country') == $country->id ? 'selected' : '' }}>
                                {{ $country->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- State -->
                <div class="col-12 col-sm-6 col-md-6 col-lg-4">
                    <label for="state" class="form-label fw-semibold">State</label>
                    <select name="state" id="state" class="form-select select2">
                        <option value="">Select State</option>
                        @foreach($states as $state)
                            <option value="{{ $state->id }}" {{ request('state') == $state->id ? 'selected' : '' }}>
                                {{ $state->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- City -->
                <div class="col-12 col-sm-6 col-md-6 col-lg-4">
                    <label for="city" class="form-label fw-semibold">City</label>
                    <select name="city" id="city" class="form-select select2">
                        <option value="">Select City</option>
                        @foreach($cities as $city)
                            <option value="{{ $city->id }}" {{ request('city') == $city->id ? 'selected' : '' }}>
                                {{ $city->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Area -->
                <div class="col-12 col-sm-6 col-md-6 col-lg-4">
                    <label for="area" class="form-label fw-semibold">Area</label>
                    <select name="area" id="area" class="form-select select2">
                        <option value="">Select Area</option>
                        @foreach($areas as $area)
                            <option value="{{ $area->id }}" {{ request('area') == $area->id ? 'selected' : '' }}>
                                {{ $area->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Beat -->
                <div class="col-12 col-sm-6 col-md-6 col-lg-4">
                    <label for="beat" class="form-label fw-semibold">Beat</label>
                    <select name="beat" id="beat" class="form-select select2">
                        <option value="">Select Beat</option>
                        @foreach($beats as $beat)
                            <option value="{{ $beat->id }}" {{ request('beat') == $beat->id ? 'selected' : '' }}>
                                {{ $beat->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Filter Button -->
                <div class="col-12 col-sm-6 col-md-6 col-lg-4 d-flex gap-2 align-items-end">
                    <button type="submit"
                            class="btn btn-primary d-flex align-items-center justify-content-center gap-2 px-4">
                        <i class="fas fa-filter"></i> Filter
                    </button>
                    <a href="{{ route('fieldTrack.customer.index') }}"
                       class="btn btn-secondary d-flex align-items-center justify-content-center gap-2 px-4"><i
                            class="fa-solid fa-rotate-right"></i> Clear
                    </a>
                </div>
            </form>
        </div>

        <div class="row mt-5">
            <div class="col-md-12 rounded-3">
                <div class="attendance-table-outer1">
                    <table class="attendance-table">
                        <thead>
                        <tr>
                            <th>S.No</th>
                            <th>Name</th>
                            <th>Email & Contact</th>
                            <th>Address</th>
                            <th class="text-end">Action</th>
                        </tr>
                        </thead>
                        <tbody>
                        @php $i = ($customers->currentPage() - 1) * $customers->perPage() + 1; @endphp
                        @forelse($customers as $customer)
                            <tr>
                                <td class="tex_fix">
                                    <div class="d-flex align-items-center">
                                        <span>{{ $i++ }}</span>
                                    </div>
                                </td>
                                <td class="tex_fix">
                                    <div class="d-flex flex-wrap align-items-center">
                                        <div class="emp-avatar">
                                            @php
                                                $avatar = $customer->avatar ?? null;
                                            @endphp

                                            @if($avatar)
                                                <img src="{{ asset('storage/'.$avatar) }}" alt="Avatar">
                                            @else
                                                <img src="{{ asset('assets/assestsnew/menimg.png') }}" alt="Default">
                                            @endif
                                        </div>

                                        <span class="emp-name tex_fix">{{ $customer->name }}</span>
                                    </div>
                                </td>
                                <td>{{ $customer->email }} <br> {{ $customer->contact }}</td>
                                <td>{{ $customer->billing_address }}</td>
                                <td class="text-end">
                                    <div class="d-flex gap-2 justify-content-end">
                                        <a href="{{ route('fieldTrack.customer.show', $customer->id) }}"
                                           class="btn btn-sm btn-secondary">
                                            <i class="fa-solid fa-eye"></i>
                                        </a>

                                        {{--                                        <a href="{{ route('fieldTrack.customer.delete', $customer->id) }}"--}}
                                        {{--                                           class="btn btn-sm btn-primary">--}}
                                        {{--                                            <i class="fas fa-edit"></i>--}}
                                        {{--                                        </a>--}}

                                        <form action="{{ route('fieldTrack.customer.delete', $customer->id) }}"
                                              method="POST"
                                              class="delete-form d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center">
                                    <div class="row justify-content-center text-center">
                                        <img class="w-25"
                                             src="{{ asset('assets/assestsnew/no_datasvg.svg') }}"
                                             alt="">
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Pagination Links -->
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
                                <li class="{{$customers->onFirstPage() ? 'disabled' : '' }}">
                                    <a href="{{$customers->url(1) }}" class="page-link1">&#171;</a>
                                </li>

                                {{-- Previous Page --}}
                                <li class="{{$customers->onFirstPage() ? 'disabled' : '' }}">
                                    <a href="{{$customers->previousPageUrl() }}" class="page-link1">
                                        <i class="fa-solid fa-chevron-left"></i>
                                    </a>
                                </li>

                                {{-- Page Numbers --}}
                                @php
                                    $start = max(1,$customers->currentPage() - 2);
                                    $end = min($start + 4,$customers->lastPage());
                                @endphp
                                @for ($i = $start; $i <= $end; $i++)
                                    <li class="{{$customers->currentPage() == $i ? 'active_pagination' : '' }}">
                                        <a href="{{$customers->url($i) }}"
                                           class="page-link1">{{ $i }}</a>
                                    </li>
                                @endfor

                                {{-- Next Page --}}
                                <li class="{{ !$customers->hasMorePages() ? 'disabled' : '' }}">
                                    <a href="{{$customers->nextPageUrl() }}" class="page-link1">
                                        <i class="fa-solid fa-chevron-right"></i>
                                    </a>
                                </li>

                                {{-- Last Page --}}
                                <li class="{{ !$customers->hasMorePages() ? 'disabled' : '' }}">
                                    <a href="{{$customers->url($customers->lastPage()) }}"
                                       class="page-link1">&#187;</a>
                                </li>
                            </ul>
                        </div>
                    </div>

                    <!-- Page Jump Input -->
                    <div class="col-12 col-lg-3 col-md-4 mb-3 mb-md-0">
                        <div
                            class="d-flex flex-md-row align-items-center justify-content-center justify-content-md-start gap-2">
                            <form action="{{ url()->current() }}" method="GET"
                                  class="d-flex align-items-center gap-2"
                                  style="flex-direction:row !important;">
                                {{-- Preserve filters --}}
                                @foreach(request()->except('page') as $key => $value)
                                    <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                                @endforeach

                                <input type="number" name="page" min="1"
                                       max="{{$customers->lastPage() }}"
                                       class="form-control form-control-sm" style="width: 80px;"
                                       placeholder="Page">
                                <button class="btn btn-sm btn-primary" type="submit">Go</button>
                            </form>
                            <span
                                class="text-nowrap small text-center text-md-start">of {{$customers->total() }} Data</span>
                        </div>
                    </div>

                    <!-- Showing Range -->
                    <div class="col-12 mt-3">
                        <div class="d-flex justify-content-center">
                            <span>{{$customers->firstItem() }} to {{$customers->lastItem() }} of {{$customers->total() }} Data</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script-page')
    <script>
        $(document).ready(function () {
            $('.select2').select2({
                placeholder: "Select an option",
                allowClear: true
            });

            $('#filterForm').on('submit', function (e) {
                $('.select2').each(function () {
                    if ($(this).val() === "") {
                        $(this).prop('required', false);
                    }
                });
            });
        });
    </script>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const deleteForms = document.querySelectorAll('.delete-form');

            deleteForms.forEach(form => {
                form.addEventListener('submit', function (e) {
                    e.preventDefault();

                    Swal.fire({
                        title: 'Are you sure?',
                        text: "This action cannot be undone!",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#d33',
                        cancelButtonColor: '#6c757d',
                        confirmButtonText: 'Yes, delete it!',
                        cancelButtonText: 'Cancel'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            form.submit();
                        }
                    });
                });
            });
        });
    </script>

@endpush

