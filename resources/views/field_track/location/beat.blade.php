@extends('field_track.layouts.fieldTrack')

@section('page-title')
    {{ __('Beat') }}
@endsection

@section('page-icon')
    {{ asset('assets/assestsnew/Blue_Beats.svg') }}
@endsection

@push('css-page')

@endpush

@section('content')
    @include('field_track.layouts.partials.nav')

    <div class="col-12 entire_box1 mb-5">
        @include('field_track.location.tab')

        <div class="row mt-4 align-items-center">
            <div class="col-lg-6 col-md-4 d-flex flex-wrap align-items-center gap-3">
                <div class="d-flex w-100 search_maincontain">
                    <form class="d-flex w-100" style="flex-direction: row !important;">
                        <input type="text" name="search" placeholder="Search Beats" value="{{ request('search') }}"
                               class="form-control">
                        <button type="submit"><i class="fa-solid fa-magnifying-glass"></i></button>
                    </form>
                </div>
            </div>

            <div class="col-lg-6 col-md-8 mt-2 mt-md-0 mt-lg-0 d-flex justify-content-end">
                <button type="button" data-bs-toggle="modal" data-bs-target="#AddBeatModal"
                        class="d-flex align-items-center gap-1 adduser_btn">
                    <img src="{{ asset('assets/assestsnew/plus.svg') }}" alt="">
                    <span class="text-white">Add Beat</span>
                </button>
            </div>
        </div>

        <div class="modal fade" id="AddBeatModal" tabindex="-1" aria-labelledby="AddBeatModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" style="max-width:600px;">
                <div class="modal-content rounded-4 shadow p-4 w-100">
                    <div class="modal-header border-0">
                        <h5 class="modal-title w-100 text-center fw-semibold fs-4">Add Beat</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <form action="{{ route('fieldTrack.location.beats.store') }}" method="POST">
                            @csrf
                            <div class="mb-3">
                                <label for="country_id" class="form-label fw-semibold">Country <span
                                        class="text-danger">*</span></label>
                                <select name="country_id" id="country_id" class="form-select select2" required
                                        style="border-radius: 100px; max-width:none !important;">
                                    <option value="">Select Country</option>
                                    @foreach($countries as $country)
                                        <option
                                            value="{{ $country->id }}" {{ old('country_id') == $country->id ? 'selected' : '' }}>
                                            {{ $country->name }}
                                        </option>
                                    @endforeach
                                </select>
                                <div class="text-danger" id="adderror-country_id"></div>
                            </div>
                            <div class="mb-3">
                                <label>State <span class="text-danger">*</span></label>
                                <select name="state_id" id="state_id" class="form-select select2" required>
                                    <option value="">Select State</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label>City <span class="text-danger">*</span></label>
                                <select name="city_id" id="city_id" class="form-select select2" required>
                                    <option value="">Select City</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label>Area <span class="text-danger">*</span></label>
                                <select name="area_id" id="area_id" class="form-select select2" required>
                                    <option value="">Select Area</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="beat_name" class="form-label fw-semibold">Beat Name <span
                                        class="text-danger">*</span></label>
                                <input type="text" name="name" id="beat_name" class="form-control"
                                       placeholder="Enter beat name" required>
                            </div>
                            <div class="mb-3">
                                <label for="status" class="form-label fw-semibold">Status<span
                                        style="color: red;">*</span></label><br>
                                <select name="status" class="form-select select2" id="edit_city_status" required
                                        style="border-radius: 100px; max-width:none !important;">
                                    <option value="1" selected>Active</option>
                                    <option value="0">Inactive</option>
                                </select>
                                <div class="text-danger" id="adderror-status"></div>
                            </div>
                            <div class="d-flex justify-content-end gap-2 mt-4">
                                <button type="button" class="btn btn-outline-primary" data-bs-dismiss="modal">Cancel
                                </button>
                                <button type="submit" class="btn btn-primary">Save Beat</button>
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
                            <th>Beat Name</th>
                            <th>Area</th>
                            <th>City</th>
                            <th>Status</th>
                            <th class="text-end">Action</th>
                        </tr>
                        </thead>
                        <tbody>
                        @php $i = ($beats->currentPage() - 1) * $beats->perPage() + 1; @endphp
                        @forelse($beats as $beat)
                            <tr>
                                <td>{{ $beat->name }}</td>
                                <td>{{ $beat->area?->name }}</td>
                                <td>{{ $beat->area?->city?->name }}</td>
                                <td>
                                    <div class="form-check form-switch">
                                        <input type="checkbox"
                                               class="form-check-input toggle-status"
                                               data-url="{{ route('fieldTrack.location.beats.toggleStatus', $beat->id) }}"
                                               {{ $beat->status ? 'checked' : '' }}
                                               style="width: 40px; height: 20px;">
                                    </div>
                                </td>
                                <td class="text-end">
                                    <div class="d-flex gap-2 justify-content-end">
                                        <button class="btn btn-sm btn-primary btn-edit-beat"
                                                data-id="{{ $beat->id }}"
                                                data-name="{{ $beat->name }}"
                                                data-area_id="{{ $beat->area_id }}"
                                                data-city_id="{{ $beat->area?->city_id }}"
                                                data-state_id="{{ $beat->area?->city?->state_id??'' }}"
                                                data-country_id="{{ $beat?->area?->city?->state?->country_id??'' }}"
                                                data-status="{{ $beat->status }}">
                                            <i class="fas fa-edit"></i>
                                        </button>

                                        <form action="{{ route('fieldTrack.location.beats.destroy', $beat->id) }}"
                                              method="POST"
                                              onsubmit="return confirm('Are you sure you want to delete this beat?');">
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
                                <li class="{{$beats->onFirstPage() ? 'disabled' : '' }}">
                                    <a href="{{$beats->url(1) }}" class="page-link1">&#171;</a>
                                </li>

                                {{-- Previous Page --}}
                                <li class="{{$beats->onFirstPage() ? 'disabled' : '' }}">
                                    <a href="{{$beats->previousPageUrl() }}" class="page-link1">
                                        <i class="fa-solid fa-chevron-left"></i>
                                    </a>
                                </li>

                                {{-- Page Numbers --}}
                                @php
                                    $start = max(1,$beats->currentPage() - 2);
                                    $end = min($start + 4,$beats->lastPage());
                                @endphp
                                @for ($i = $start; $i <= $end; $i++)
                                    <li class="{{$beats->currentPage() == $i ? 'active_pagination' : '' }}">
                                        <a href="{{$beats->url($i) }}"
                                           class="page-link1">{{ $i }}</a>
                                    </li>
                                @endfor

                                {{-- Next Page --}}
                                <li class="{{ !$beats->hasMorePages() ? 'disabled' : '' }}">
                                    <a href="{{$beats->nextPageUrl() }}" class="page-link1">
                                        <i class="fa-solid fa-chevron-right"></i>
                                    </a>
                                </li>

                                {{-- Last Page --}}
                                <li class="{{ !$beats->hasMorePages() ? 'disabled' : '' }}">
                                    <a href="{{$beats->url($beats->lastPage()) }}"
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
                                       max="{{$beats->lastPage() }}"
                                       class="form-control form-control-sm" style="width: 80px;"
                                       placeholder="Page">
                                <button class="btn btn-sm btn-primary" type="submit">Go</button>
                            </form>
                            <span
                                class="text-nowrap small text-center text-md-start">of {{$beats->total() }} Data</span>
                        </div>
                    </div>

                    <!-- Showing Range -->
                    <div class="col-12 mt-3">
                        <div class="d-flex justify-content-center">
                            <span>{{$beats->firstItem() }} to {{$beats->lastItem() }} of {{$beats->total() }} Data</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="EditBeatModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" style="max-width:600px;">
                <div class="modal-content rounded-4 shadow p-4 w-100">
                    <div class="modal-header border-0">
                        <h5 class="modal-title w-100 text-center fw-semibold fs-4">Edit Beat</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <form id="editBeatForm" method="POST">
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
                                            value="{{ $country->id }}" {{ old('country_id', $beat->area->city->state->country_id ?? '') == $country->id ? 'selected' : '' }}>
                                            {{ $country->name }}
                                        </option>
                                    @endforeach
                                </select>
                                <div class="text-danger" id="adderror-country_id"></div>
                            </div>
                            <div class="mb-3">
                                <label>State <span class="text-danger">*</span></label>
                                <select name="state_id" id="edit_state_id" class="form-select select2" required>
                                    <option value="">Select State</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label>City <span class="text-danger">*</span></label>
                                <select name="city_id" id="edit_city_id" class="form-select select2" required>
                                    <option value="">Select City</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label>Area <span class="text-danger">*</span></label>
                                <select name="area_id" id="edit_area_id" class="form-select select2" required>
                                    <option value="">Select Area</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Beat Name</label>
                                <input type="text" name="name" id="edit_beat_name" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label for="status" class="form-label fw-semibold">Status<span
                                        style="color: red;">*</span></label><br>
                                <select name="status" class="form-select select2" id="edit_city_status" required
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

        const addStateChoices = new Choices('#state_id', {
            removeItemButton: true,
            shouldSort: false
        });

        const editStateChoices = new Choices('#edit_state_id', {
            removeItemButton: true,
            shouldSort: false
        });

        const addCityChoices = new Choices('#city_id', {
            removeItemButton: true,
            shouldSort: false
        });

        const editCityChoices = new Choices('#edit_city_id', {
            removeItemButton: true,
            shouldSort: false
        });

        const addAreaChoices = new Choices('#area_id', {
            removeItemButton: true,
            shouldSort: false
        });

        const editAreaChoices = new Choices('#edit_area_id', {
            removeItemButton: true,
            shouldSort: false
        });

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

        $(document).on('click', '.btn-edit-beat', function () {
            let id = $(this).data('id');
            let name = $(this).data('name');
            let status = $(this).data('status');
            let country_id = $(this).data('country_id');
            let state_id = $(this).data('state_id');
            let city_id = $(this).data('city_id');
            let area_id = $(this).data('area_id');

            $('#edit_beat_name').val(name);
            $('#edit_city_status').val(status);
            $('#edit_country_id').val(country_id);
            $('#edit_state_id').val(state_id);
            $('#edit_city_id').val(city_id);
            $('#edit_area_id').val(area_id);

            if (country_id) {
                $.get("{{ url('field-track/location/get-states') }}/" + country_id, function (states) {
                    editStateChoices.clearChoices();
                    const newChoices = states.map(state => ({
                        value: state.id,
                        label: state.name,
                        selected: state.id == state_id
                    }));
                    editStateChoices.setChoices(newChoices, 'value', 'label', true);
                });
                if (state_id) {
                    $.get("{{ url('field-track/location/get-cities') }}/" + state_id, function (cities) {
                        editCityChoices.clearChoices();
                        const newChoices = cities.map((city, index) => ({
                            value: city.id,
                            label: city.name,
                            selected: city.id == city_id
                        }));
                        editCityChoices.setChoices(newChoices, 'value', 'label', true);
                    });


                    if (city_id) {
                        $.get("{{ url('field-track/location/get-areas') }}/" + city_id, function (areas) {
                            editAreaChoices.clearChoices();
                            const newChoices = areas.map((area, index) => ({
                                value: area.id,
                                label: area.name,
                                selected: area.id == area_id
                            }));
                            editAreaChoices.setChoices(newChoices, 'value', 'label', true);
                        });
                    }
                }
            }


            $('#editBeatForm').attr('action', "{{ route('fieldTrack.location.beats.update', '') }}/" + id);

            $('#EditBeatModal').modal('show');
        });


        $('#country_id').on('change', function () {
            let country_id = $(this).val();
            if (country_id) {
                $.get("{{ url('field-track/location/get-states') }}/" + country_id, function (states) {
                    // Clear existing choices
                    addStateChoices.clearChoices();
                    addStateChoices.removeActiveItems();

                    // Add new choices
                    const newChoices = states.map((state, index) => ({
                        value: state.id,
                        label: state.name,
                        selected: index === 0
                    }));
                    addStateChoices.setChoices(newChoices, 'value', 'label', true);
                });
            } else {
                addStateChoices.clearChoices();
                addStateChoices.removeActiveItems();
            }
        });

        $('#edit_country_id').on('change', function () {
            let country_id = $(this).val();
            let selectedStateId = null;
            if (country_id) {
                $.get("{{ url('field-track/location/get-states') }}/" + country_id, function (states) {
                    editStateChoices.clearChoices();
                    editStateChoices.removeActiveItems();
                    const newChoices = states.map((state, index) => ({
                        value: state.id,
                        label: state.name,
                        selected: index === 0
                    }));
                    editStateChoices.setChoices(newChoices, 'value', 'label', true);
                });
            } else {
                editStateChoices.clearChoices();
                editStateChoices.removeActiveItems();
            }
        });

        $('#state_id').on('change', function () {
            let state_id = $(this).val();
            if (state_id) {
                $.get("{{ url('field-track/location/get-cities') }}/" + state_id, function (cities) {
                    // Clear existing choices
                    addCityChoices.clearChoices();
                    addCityChoices.removeActiveItems();

                    // Add new choices
                    const newChoices = cities.map((city, index) => ({
                        value: city.id,
                        label: city.name,
                        selected: index === 0
                    }));
                    addCityChoices.setChoices(newChoices, 'value', 'label', true);
                });
            } else {
                addCityChoices.clearChoices();
                addCityChoices.removeActiveItems();
            }
        });

        $('#edit_state_id').on('change', function () {
            let state_id = $(this).val();
            let selectedCityId = '';
            if (state_id) {
                $.get("{{ url('field-track/location/get-cities') }}/" + state_id, function (cities) {
                    editCityChoices.clearChoices();
                    editCityChoices.removeActiveItems();
                    const newChoices = cities.map((city, index) => ({
                        value: city.id,
                        label: city.name,
                        selected: index === 0
                    }));
                    editCityChoices.setChoices(newChoices, 'value', 'label', true);
                });
            } else {
                editCityChoices.clearChoices();
                editCityChoices.removeActiveItems();
            }
        });

        $('#city_id').on('change', function () {
            let city_id = $(this).val();
            if (area_id) {
                $.get("{{ url('field-track/location/get-areas') }}/" + city_id, function (areas) {
                    // Clear existing choices
                    addAreaChoices.clearChoices();
                    addAreaChoices.removeActiveItems();

                    // Add new choices
                    const newChoices = areas.map((area, index) => ({
                        value: area.id,
                        label: area.name,
                        selected: index === 0
                    }));
                    addAreaChoices.setChoices(newChoices, 'value', 'label', true);
                });
            } else {
                addAreaChoices.clearChoices();
                addAreaChoices.removeActiveItems();
            }
        });

        $('#edit_city_id').on('change', function () {
            let city_id = $(this).val();
            if (city_id) {
                $.get("{{ url('field-track/location/get-areas') }}/" + city_id, function (areas) {
                    editAreaChoices.clearChoices();
                    editAreaChoices.removeActiveItems();
                    const newChoices = areas.map((area, index) => ({
                        value: area.id,
                        label: area.name,
                        selected: index === 0
                    }));
                    editAreaChoices.setChoices(newChoices, 'value', 'label', true);
                });
            } else {
                editAreaChoices.clearChoices();
                editAreaChoices.removeActiveItems();
            }
        });


    </script>
@endpush
