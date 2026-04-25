@extends('field_track.layouts.fieldTrack')

@section('page-title')
    {{ __('State') }}
@endsection

@section('page-icon')
    {{ asset('assets/assestsnew/Blue_State.svg') }}
@endsection

@section('content')
    @include('field_track.layouts.partials.nav')

    <div class="col-12 entire_box1 mb-5">
        @include('field_track.location.tab')

        <div class="row mt-4 align-items-center">
            <div class="col-lg-6 col-md-4 d-flex flex-wrap align-items-center gap-3">
                <div class="d-flex w-100 search_maincontain">
                    <form class="d-flex w-100" style="flex-direction: row !important;">
                        <input type="text" name="search" placeholder="Search States" value="{{ request('search') }}"
                               class="form-control">
                        <button type="submit"><i class="fa-solid fa-magnifying-glass"></i></button>
                    </form>
                </div>
            </div>

            <div class="col-lg-6 col-md-8 mt-2 mt-md-0 mt-lg-0 d-flex justify-content-end">
                <button type="button" data-bs-toggle="modal" data-bs-target="#AddStateModal"
                        class="d-flex align-items-center gap-1 adduser_btn">
                    <img src="{{ asset('assets/assestsnew/plus.svg') }}" alt="">
                    <span class="text-white">Add State</span>
                </button>
            </div>
        </div>

        {{-- Add State Modal --}}
        <div class="modal fade" id="AddStateModal" tabindex="-1" aria-labelledby="AddStateModalLabel"
             aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" style="max-width:600px;">
                <div class="modal-content rounded-4 shadow p-4 w-100">
                    <div class="modal-header border-0">
                        <h5 class="modal-title w-100 text-center fw-semibold fs-4">Add State</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <form action="{{ route('fieldTrack.location.states.store') }}" method="POST">
                            @csrf
                            <div class="mb-3">
                                <label for="country_id" class="form-label fw-semibold">Country <span
                                        class="text-danger">*</span></label>
                                <select name="country_id" id="country_id" class="form-select select2" required
                                        style="border-radius: 100px; max-width:none !important;">
                                    <option value="">Select Country</option>
                                    @foreach($countries as $country)
                                        <option
                                            value="{{ $country->id }}" {{ old('country_id', $state->country_id ?? '') == $country->id ? 'selected' : '' }}>
                                            {{ $country->name }}
                                        </option>
                                    @endforeach
                                </select>
                                <div class="text-danger" id="adderror-country_id"></div>
                            </div>
                            <div class="mb-3">
                                <label for="state_name" class="form-label fw-semibold">State Name <span
                                        class="text-danger">*</span></label>
                                <input type="text" name="name" id="state_name" class="form-control"
                                       placeholder="Enter state name" required>
                            </div>
                            <div class="mb-3">
                                <label for="state_code" class="form-label fw-semibold">State Code <span
                                        class="text-danger">*</span></label>
                                <input type="text" name="code" id="state_code" class="form-control"
                                       placeholder="Enter state code" required>
                            </div>
                            <div class="mb-3">
                                <label for="status" class="form-label fw-semibold">Status<span
                                        style="color: red;">*</span></label><br>
                                <select name="status" class="form-select select2" id="edit_state_status" required
                                        style="border-radius: 100px; max-width:none !important;">
                                    <option value="1" selected>Active</option>
                                    <option value="0">Inactive</option>
                                </select>
                                <div class="text-danger" id="adderror-status"></div>
                            </div>
                            <div class="d-flex justify-content-end gap-2 mt-4">
                                <button type="button" class="btn btn-outline-primary" data-bs-dismiss="modal">Cancel
                                </button>
                                <button type="submit" class="btn btn-primary">Save State</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mt-5">
            <div class="col-md-12 rounded-3">
                <div class="attendance-table-outer1">
                    <table class="attendance-table">
                        <thead>
                        <tr>
                            <th>State Name</th>
                            <th>State Code</th>
                            <th>Country</th>
                            <th>Status</th>
                            <th class="text-end">Action</th>
                        </tr>
                        </thead>
                        <tbody>
                        @php $i = ($states->currentPage() - 1) * $states->perPage() + 1; @endphp
                        @forelse($states as $state)
                            <tr>
                                <td>{{ $state->name }}</td>
                                <td>{{ $state->code }}</td>
                                <td>{{ $state->country?->name }}</td>
                                <td>
                                    <div class="form-check form-switch">
                                        <input type="checkbox"
                                               class="form-check-input toggle-status"
                                               data-url="{{ route('fieldTrack.location.states.toggleStatus', $state->id) }}"
                                               {{ $state->status ? 'checked' : '' }}
                                               style="width: 40px; height: 20px;">
                                    </div>
                                </td>
                                <td class="text-end">
                                    <div class="d-flex gap-2 justify-content-end">
                                        <button class="btn btn-sm btn-primary btn-edit-state"
                                                data-id="{{ $state->id }}"
                                                data-name="{{ $state->name }}"
                                                data-code="{{ $state->code }}"
                                                data-country_id="{{ $state->country_id }}"
                                                data-status="{{ $state->status }}">
                                            <i class="fas fa-edit"></i>
                                        </button>

                                        <form action="{{ route('fieldTrack.location.states.destroy', $state->id) }}"
                                              method="POST"
                                              onsubmit="return confirm('Are you sure you want to delete this state?');">
                                            @csrf
                                            @method('DELETE')
                                            <button class="btn btn-sm btn-danger"><i class="fas fa-trash"></i></button>
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
                                <li class="{{$states->onFirstPage() ? 'disabled' : '' }}">
                                    <a href="{{$states->url(1) }}" class="page-link1">&#171;</a>
                                </li>

                                {{-- Previous Page --}}
                                <li class="{{$states->onFirstPage() ? 'disabled' : '' }}">
                                    <a href="{{$states->previousPageUrl() }}" class="page-link1">
                                        <i class="fa-solid fa-chevron-left"></i>
                                    </a>
                                </li>

                                {{-- Page Numbers --}}
                                @php
                                    $start = max(1,$states->currentPage() - 2);
                                    $end = min($start + 4,$states->lastPage());
                                @endphp
                                @for ($i = $start; $i <= $end; $i++)
                                    <li class="{{$states->currentPage() == $i ? 'active_pagination' : '' }}">
                                        <a href="{{$states->url($i) }}"
                                           class="page-link1">{{ $i }}</a>
                                    </li>
                                @endfor

                                {{-- Next Page --}}
                                <li class="{{ !$states->hasMorePages() ? 'disabled' : '' }}">
                                    <a href="{{$states->nextPageUrl() }}" class="page-link1">
                                        <i class="fa-solid fa-chevron-right"></i>
                                    </a>
                                </li>

                                {{-- Last Page --}}
                                <li class="{{ !$states->hasMorePages() ? 'disabled' : '' }}">
                                    <a href="{{$states->url($states->lastPage()) }}"
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
                                       max="{{$states->lastPage() }}"
                                       class="form-control form-control-sm" style="width: 80px;"
                                       placeholder="Page">
                                <button class="btn btn-sm btn-primary" type="submit">Go</button>
                            </form>
                            <span
                                class="text-nowrap small text-center text-md-start">of {{$states->total() }} Data</span>
                        </div>
                    </div>

                    <!-- Showing Range -->
                    <div class="col-12 mt-3">
                        <div class="d-flex justify-content-center">
                            <span>{{$states->firstItem() }} to {{$states->lastItem() }} of {{$states->total() }} Data</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="EditStateModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" style="max-width:600px;">
                <div class="modal-content rounded-4 shadow p-4 w-100">
                    <div class="modal-header border-0">
                        <h5 class="modal-title w-100 text-center fw-semibold fs-4">Edit State</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <form id="editStateForm" method="POST">
                            @csrf
                            @method('PUT')

                            <div class="mb-3">
                                <label for="country_id" class="form-label fw-semibold">Country <span
                                        class="text-danger">*</span></label>
                                <select name="country_id" id="edit_country_id" class="form-select select2" required
                                        style="border-radius: 100px; max-width:none !important;">
                                    <option value="">Select Country</option>
                                    @foreach($countries as $country)
                                        <option
                                            value="{{ $country->id }}" {{ old('country_id', $state->country_id ?? '') == $country->id ? 'selected' : '' }}>
                                            {{ $country->name }}
                                        </option>
                                    @endforeach
                                </select>
                                <div class="text-danger" id="adderror-country_id"></div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-semibold">State Name</label>
                                <input type="text" name="name" id="edit_state_name" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-semibold">State Code</label>
                                <input type="text" name="code" id="edit_state_code" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label for="status" class="form-label fw-semibold">Status<span
                                        style="color: red;">*</span></label><br>
                                <select name="status" class="form-select select2" id="edit_state_status" required
                                        style="border-radius: 100px; max-width:none !important;">
                                    <option value="1" selected>Active</option>
                                    <option value="0">Inactive</option>
                                </select>
                                <div class="text-danger" id="adderror-status"></div>
                            </div>
                            <div class="d-flex justify-content-end gap-2 mt-4">
                                <button type="button" class="btn btn-outline-primary" data-bs-dismiss="modal">Cancel
                                </button>
                                <button type="submit" class="btn btn-primary">Update</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>


    </div>
@endsection

@push('script-page')
    <script>
        $(document).on('change', '.toggle-status', function () {
            let url = $(this).data('url');
            let isChecked = $(this).is(':checked');

            $.ajax({
                url: url,
                type: "PATCH",
                data: {
                    _token: "{{ csrf_token() }}",
                },
                success: function (response) {
                    show_toastr('Error', 'Status updated successfully', 'success');
                },
                error: function () {
                    show_toastr('Error', 'Something went wrong.', 'error');
                }
            });
        });
        $(document).on('click', '.btn-edit-state', function () {
            let id = $(this).data('id');
            let name = $(this).data('name');
            let code = $(this).data('code');
            let status = $(this).data('status');
            let country_id = $(this).data('country_id');

            $('#edit_state_name').val(name);
            $('#edit_state_code').val(code);
            $('#edit_state_status').val(status);
            $('#edit_country_id').val(country_id);

            $('#editStateForm').attr('action', "{{ route('fieldTrack.location.states.update', '') }}/" + id);

            $('#EditStateModal').modal('show');
        });

    </script>
@endpush





